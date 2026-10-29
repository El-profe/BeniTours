<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Evento;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class EventoController extends Controller {
    private Evento $eventoModel;

    public function __construct() {
        parent::__construct();
        AuthMiddleware::autenticar();
        $this->eventoModel = new Evento();
    }

    public function index(): void {
        $eventos = $this->eventoModel->listarTodos();
        $fraternidades = (new \App\Models\Fraternidad())->listarAdmin();
        $mensaje = $_SESSION['admin_flash'] ?? null;
        $error   = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_flash'], $_SESSION['admin_error']);

        $this->render('admin/eventos/index', [
            'titulo'         => 'Agenda de Eventos y Entrada Universitaria',
            'eventos'        => $eventos,
            'fraternidades'  => $fraternidades,
            'mensaje'        => $mensaje,
            'error'          => $error,
            'csrfToken'      => CsrfMiddleware::obtenerToken()
        ], 'admin');
    }

    /**
     * Gestión de Fraternidades para la Entrada Folklórica Universitaria
     */
    public function guardarFraternidad(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Token de seguridad inválido.';
            header("Location: {$this->config['base_url']}/admin/eventos#tabFraternidadesAdmin");
            exit();
        }

        $id = (int)($_POST['id_fraternidad'] ?? 0);
        $orden = max(1, (int)($_POST['orden'] ?? 1));
        $hora = trim($_POST['hora_ingreso'] ?? '14:00');
        $nombre = trim($_POST['nombre_fraternidad'] ?? '');
        $danza = trim($_POST['danza'] ?? '');
        $universidad = trim($_POST['universidad'] ?? '');
        $depto = trim($_POST['departamento'] ?? 'Beni');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (empty($nombre) || empty($danza) || empty($universidad)) {
            $_SESSION['admin_error'] = 'Por favor ingresa el nombre de la fraternidad, danza y universidad.';
            header("Location: {$this->config['base_url']}/admin/eventos#tabFraternidadesAdmin");
            exit();
        }

        $fratModel = new \App\Models\Fraternidad();
        $datos = [
            'orden'              => $orden,
            'hora_ingreso'       => $hora,
            'nombre_fraternidad' => $nombre,
            'danza'              => $danza,
            'universidad'        => $universidad,
            'departamento'       => $depto,
            'activo'             => $activo
        ];

        if ($id > 0) {
            $fratModel->actualizar($id, $datos);
            $_SESSION['admin_flash'] = 'Fraternidad actualizada exitosamente.';
        } else {
            $fratModel->crear($datos);
            $_SESSION['admin_flash'] = 'Fraternidad agregada al rol de la entrada.';
        }

        header("Location: {$this->config['base_url']}/admin/eventos#tabFraternidadesAdmin");
        exit();
    }

    public function eliminarFraternidad(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Token de seguridad inválido.';
            header("Location: {$this->config['base_url']}/admin/eventos#tabFraternidadesAdmin");
            exit();
        }

        $id = (int)($_POST['id_fraternidad'] ?? 0);
        if ($id > 0) {
            (new \App\Models\Fraternidad())->eliminar($id);
            $_SESSION['admin_flash'] = 'Fraternidad eliminada del rol.';
        }

        header("Location: {$this->config['base_url']}/admin/eventos#tabFraternidadesAdmin");
        exit();
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        $id = (int)($_POST['id_evento'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $subtitulo = trim($_POST['subtitulo_lugar'] ?? 'Trinidad · Beni');
        $detalleFechas = trim($_POST['detalle_fechas'] ?? '');
        $enlace = trim($_POST['enlace_externo'] ?? '');
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
        $fechaFin = trim($_POST['fecha_fin'] ?? '');
        $orden = max(1, (int)($_POST['orden'] ?? 1));
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (empty($titulo) || empty($detalleFechas) || empty($fechaInicio) || empty($fechaFin)) {
            $_SESSION['admin_error'] = 'Por favor completa todos los campos obligatorios (Título, Fechas y Vigencia).';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        if ($fechaFin < $fechaInicio) {
            $_SESSION['admin_error'] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        $nombreArchivo = null;

        // Procesar subida de afiche / imagen si se adjuntó
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $archivoTmp = $_FILES['imagen']['tmp_name'];
            $nombreOriginal = $_FILES['imagen']['name'];
            $tamano = $_FILES['imagen']['size'];

            // Validar peso (máximo 8MB)
            if ($tamano > 8 * 1024 * 1024) {
                $_SESSION['admin_error'] = 'La imagen es muy pesada. El tamaño máximo permitido es 8 MB.';
                header("Location: {$this->config['base_url']}/admin/eventos");
                exit();
            }

            // Validar tipo de imagen
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $archivoTmp);
            finfo_close($finfo);

            $mimesPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($mimesPermitidos[$mime])) {
                $_SESSION['admin_error'] = 'Formato de imagen no permitido. Usa JPG, PNG o WebP.';
                header("Location: {$this->config['base_url']}/admin/eventos");
                exit();
            }

            $ext = $mimesPermitidos[$mime];
            $nombreArchivo = 'evento_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
            $destino = dirname(__DIR__, 3) . '/public/assets/img/eventos/' . $nombreArchivo;

            if (!move_uploaded_file($archivoTmp, $destino)) {
                $_SESSION['admin_error'] = 'No se pudo guardar la imagen del evento en el servidor.';
                header("Location: {$this->config['base_url']}/admin/eventos");
                exit();
            }
        } elseif ($id === 0) {
            $_SESSION['admin_error'] = 'Debes subir una imagen o afiche para el nuevo evento.';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        $datos = [
            'titulo'          => $titulo,
            'subtitulo_lugar' => !empty($subtitulo) ? $subtitulo : 'Trinidad · Beni',
            'detalle_fechas'  => $detalleFechas,
            'enlace_externo'  => !empty($enlace) ? $enlace : null,
            'fecha_inicio'    => $fechaInicio,
            'fecha_fin'       => $fechaFin,
            'activo'          => $activo,
            'orden'           => $orden
        ];

        if ($nombreArchivo !== null) {
            $datos['imagen_archivo'] = $nombreArchivo;
        }

        try {
            if ($id > 0) {
                $this->eventoModel->actualizar($id, $datos);
                $_SESSION['admin_flash'] = "Evento '{$titulo}' actualizado correctamente.";
            } else {
                $this->eventoModel->crear($datos);
                $_SESSION['admin_flash'] = "Nuevo evento '{$titulo}' añadido a la agenda.";
            }
        } catch (\Throwable $e) {
            error_log('Error al guardar evento: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Ocurrió un error al guardar en la base de datos.';
        }

        header("Location: {$this->config['base_url']}/admin/eventos");
        exit();
    }

    public function cambiarEstado(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Token CSRF inválido.';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        $id = (int)($_POST['id_evento'] ?? 0);
        $activo = (int)($_POST['activo'] ?? 0);

        if ($id > 0) {
            $this->eventoModel->cambiarEstado($id, $activo);
            $_SESSION['admin_flash'] = $activo ? 'Evento activado en la agenda.' : 'Evento pausado temporalmente.';
        }

        header("Location: {$this->config['base_url']}/admin/eventos");
        exit();
    }

    public function eliminar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Token CSRF inválido.';
            header("Location: {$this->config['base_url']}/admin/eventos");
            exit();
        }

        $id = (int)($_POST['id_evento'] ?? 0);
        if ($id > 0) {
            $this->eventoModel->eliminar($id);
            $_SESSION['admin_flash'] = 'Evento eliminado de la agenda.';
        }

        header("Location: {$this->config['base_url']}/admin/eventos");
        exit();
    }
}
