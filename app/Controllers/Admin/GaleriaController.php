<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\GaleriaFoto;
use App\Models\Lugar;
use App\Models\MapaProvinciaFoto;
use App\Services\ImagenService;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class GaleriaController extends Controller {
    private GaleriaFoto $galeriaModel;
    private Lugar $lugarModel;
    private MapaProvinciaFoto $mapaModel;

    public function __construct() {
        parent::__construct();
        AuthMiddleware::autenticar();
        $this->galeriaModel = new GaleriaFoto();
        $this->lugarModel = new Lugar();
        $this->mapaModel = new MapaProvinciaFoto();
    }

    /**
     * Muestra la lista de fotos de la galería en el panel y las fotos del mapa
     */
    public function index(): void {
        $fotos = $this->galeriaModel->listarTodas();
        // Listar lugares aprobados para el selector opcional de vinculación
        $lugares = $this->lugarModel->listarPublicos(null, null, null);
        // Listar fotos asignadas a las 8 provincias del mapa regional
        $fotosMapa = $this->mapaModel->listarTodas();

        $this->render('admin/galeria/index', [
            'titulo'    => 'Gestión de Galería & Mapa Departamental',
            'fotos'     => $fotos,
            'lugares'   => $lugares,
            'fotosMapa' => $fotosMapa
        ], 'admin');
    }

    /**
     * Actualiza la fotografía de una provincia del mapa regional
     */
    public function actualizarMapaProvincia(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/galeria#tab-mapa");
            exit();
        }

        $codigo = trim($_POST['codigo_provincia'] ?? '');
        $titulo = trim($_POST['titulo_provincia'] ?? '');

        if (empty($codigo)) {
            $_SESSION['flash_error'] = 'Código de provincia no especificado.';
            header("Location: {$this->config['base_url']}/admin/galeria#tab-mapa");
            exit();
        }

        if (empty($_FILES['imagen']['tmp_name'])) {
            $_SESSION['flash_error'] = 'Debes seleccionar una imagen para la provincia.';
            header("Location: {$this->config['base_url']}/admin/galeria#tab-mapa");
            exit();
        }

        try {
            $subida = ImagenService::subir($_FILES['imagen']);
            if (!$subida || empty($subida['nombre_archivo'])) {
                $_SESSION['flash_error'] = 'No se pudo procesar la imagen. Verifica el formato.';
                header("Location: {$this->config['base_url']}/admin/galeria#tab-mapa");
                exit();
            }

            $this->mapaModel->actualizarFoto($codigo, $subida['nombre_archivo'], $titulo ?: null);

            $_SESSION['flash_success'] = '¡Fotografía de la provincia actualizada correctamente!';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Error al subir la imagen: ' . $e->getMessage();
        }

        header("Location: {$this->config['base_url']}/admin/galeria#tab-mapa");
        exit();
    }

    /**
     * Guarda una nueva foto libre o vinculada
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $subtitulo = trim($_POST['subtitulo'] ?? '');
        $creditosAutor = trim($_POST['creditos_autor'] ?? '');
        $urlFuente = trim($_POST['url_fuente'] ?? '');
        $idLugar = !empty($_POST['id_lugar']) ? (int)$_POST['id_lugar'] : null;
        $esDestacada = !empty($_POST['es_destacada']) ? 1 : 0;
        $orden = !empty($_POST['orden']) ? (int)$_POST['orden'] : 1;

        if (empty($titulo)) {
            $_SESSION['flash_error'] = 'Debes ingresar un título o descripción para la fotografía.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        if (empty($_FILES['imagen']['tmp_name'])) {
            $_SESSION['flash_error'] = 'Debes seleccionar un archivo de imagen válido (JPG, PNG o WebP).';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $subida = ImagenService::subir($_FILES['imagen']);
        if (!$subida || empty($subida['nombre_archivo'])) {
            $_SESSION['flash_error'] = 'No se pudo procesar la imagen. Verifica que sea un formato de imagen permitido.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $id = $this->galeriaModel->crear([
            'titulo'         => $titulo,
            'subtitulo'      => $subtitulo,
            'creditos_autor' => $creditosAutor,
            'url_fuente'     => $urlFuente,
            'nombre_archivo' => $subida['nombre_archivo'],
            'id_lugar'       => $idLugar,
            'es_destacada'   => $esDestacada,
            'orden'          => $orden
        ]);

        if ($id > 0) {
            $_SESSION['flash_success'] = '¡Fotografía agregada a la galería con éxito!';
        } else {
            $_SESSION['flash_error'] = 'Hubo un error al registrar la fotografía en la base de datos.';
        }

        header("Location: {$this->config['base_url']}/admin/galeria");
        exit();
    }

    /**
     * Alterna la visibilidad (activo / inactivo)
     */
    public function cambiarEstado(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $id = (int)($_POST['id_galeria'] ?? 0);
        $activo = (int)($_POST['activo'] ?? 1);

        if ($id > 0) {
            $this->galeriaModel->cambiarEstado($id, $activo);
            $_SESSION['flash_success'] = 'Estado de visibilidad actualizado.';
        }

        header("Location: {$this->config['base_url']}/admin/galeria");
        exit();
    }

    /**
     * Marca una foto como la destacada principal del mosaico
     */
    public function destacar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $id = (int)($_POST['id_galeria'] ?? 0);

        if ($id > 0) {
            $this->galeriaModel->establecerDestacada($id);
            $_SESSION['flash_success'] = 'Foto establecida como destacada principal del mosaico.';
        }

        header("Location: {$this->config['base_url']}/admin/galeria");
        exit();
    }

    /**
     * Elimina una foto de la galería
     */
    public function eliminar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido o sesión expirada.';
            header("Location: {$this->config['base_url']}/admin/galeria");
            exit();
        }

        $id = (int)($_POST['id_galeria'] ?? 0);

        if ($id > 0) {
            $nombreArchivo = $this->galeriaModel->eliminar($id);
            $_SESSION['flash_success'] = 'Fotografía eliminada de la galería.';
        }

        header("Location: {$this->config['base_url']}/admin/galeria");
        exit();
    }
}
