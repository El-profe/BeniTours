<?php
namespace App\Controllers\Negocio;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Lugar;
use App\Models\Fotografia;
use App\Models\Promocion;
use App\Services\ImagenService;
use App\Middleware\NegocioAuthMiddleware;
use App\Middleware\CsrfMiddleware;

class PanelController extends Controller {
    private Lugar $lugarModel;
    private Fotografia $fotografiaModel;
    private Promocion $promocionModel;
    private int $idLugar;

    public function __construct() {
        parent::__construct();
        NegocioAuthMiddleware::autenticar();
        $this->lugarModel       = new Lugar();
        $this->fotografiaModel  = new Fotografia();
        $this->promocionModel   = new Promocion();
        $this->idLugar          = (int)$_SESSION['negocio_lugar_id'];
    }

    public function dashboard(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $promociones = $this->promocionModel->listarPorLugar($this->idLugar);
        
        $db = Database::getConnection();
        $stmtFotos = $db->prepare("SELECT COUNT(*) FROM fotografias WHERE id_lugar = :id");
        $stmtFotos->execute([':id' => $this->idLugar]);
        $totalFotos = (int)$stmtFotos->fetchColumn();
        $pendiente = $db->prepare("SELECT COUNT(*) FROM pagos WHERE id_lugar = ? AND estado = 'PENDIENTE'");
        $pendiente->execute([$this->idLugar]);
        $vencimiento = (new \App\Models\Vigencia())->obtenerUltimoVencimiento($this->idLugar);
        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $hoy = date('Y-m-d');
        $promocionesActivas = array_values(array_filter($promociones, function($p) use ($hoy) {
            return (int)$p['activo'] === 1 && $p['fecha_fin'] >= $hoy;
        }));
        $tienePromoActiva = count($promocionesActivas) > 0;

        // Evaluación de completitud del perfil (Checklist de 5 criterios de 20% cada uno)
        $criteriosSalud = [
            [
                'id' => 'contacto',
                'titulo' => 'Teléfono o WhatsApp de contacto',
                'descripcion' => 'Permite que los turistas te escriban o llamen para consultas y reservas directas.',
                'cumplido' => !empty(trim($lugar['whatsapp_contacto'] ?? '')) || !empty(trim($lugar['telefono_contacto'] ?? '')),
                'enlace' => $this->config['base_url'] . '/negocio/perfil',
                'texto_accion' => 'Añadir teléfono'
            ],
            [
                'id' => 'horario',
                'titulo' => 'Horarios de atención al público',
                'descripcion' => 'Informa a los visitantes tus días y horas de apertura para que te visiten con certeza.',
                'cumplido' => !empty(trim($lugar['horario_atencion'] ?? '')),
                'enlace' => $this->config['base_url'] . '/negocio/perfil',
                'texto_accion' => 'Fijar horarios'
            ],
            [
                'id' => 'ubicacion',
                'titulo' => 'Ubicación GPS fijada en el mapa',
                'descripcion' => 'Permite trazar la ruta en Google Maps y aparecer con el radar de cercanía en Trinidad.',
                'cumplido' => !empty($lugar['latitud']) && !empty($lugar['longitud']),
                'enlace' => $this->config['base_url'] . '/negocio/ubicacion',
                'texto_accion' => 'Fijar en mapa'
            ],
            [
                'id' => 'fotos',
                'titulo' => 'Galería con al menos 3 fotos',
                'descripcion' => "Una ficha con fotos atractivas recibe hasta 4 veces más visitas ({$totalFotos}/3 fotos subidas).",
                'cumplido' => $totalFotos >= 3,
                'enlace' => $this->config['base_url'] . '/negocio/fotos',
                'texto_accion' => 'Subir fotos'
            ],
            [
                'id' => 'promocion',
                'titulo' => 'Al menos una promoción u oferta activa',
                'descripcion' => 'Las ofertas atraen clientes en días clave y te destacan entre los comercios del Beni.',
                'cumplido' => $tienePromoActiva,
                'enlace' => $this->config['base_url'] . '/negocio/promociones',
                'texto_accion' => 'Lanzar promo'
            ],
            [
                'id' => 'menu',
                'titulo' => 'Menú digital o carta de productos',
                'descripcion' => 'Permite que los visitantes consulten tu carta, lista de precios o catálogo online.',
                'cumplido' => !empty(trim($lugar['enlace_menu'] ?? '')),
                'enlace' => $this->config['base_url'] . '/negocio/perfil',
                'texto_accion' => 'Añadir menú'
            ]
        ];

        $cumplidosCount = count(array_filter($criteriosSalud, fn($c) => $c['cumplido']));
        $porcentajeSalud = (int)round(($cumplidosCount / count($criteriosSalud)) * 100);

        // Semáforo de vigencia y cobertura publicitaria
        $diasRestantes = null;
        $estadoVigencia = [
            'badge_clase' => 'bg-secondary',
            'badge_texto' => 'Ficha inactiva',
            'titulo'      => 'Ficha sin cobertura activa',
            'mensaje'     => 'Tu negocio no cuenta con vigencia activa. Renueva para aparecer en la guía turística.',
            'dias'        => 0,
            'progreso'    => 0,
            'color'       => 'secondary'
        ];

        if ($vencimiento) {
            $diasRestantes = (int)(ceil((strtotime($vencimiento) - strtotime($hoy)) / 86400));
            if ($diasRestantes > 15) {
                $estadoVigencia = [
                    'badge_clase' => 'bg-success',
                    'badge_texto' => 'Membresía al día',
                    'titulo'      => '¡Tu negocio está activo y visible para los turistas!',
                    'mensaje'     => "Cuentas con cobertura continua por {$diasRestantes} días más (hasta el " . date('d/m/Y', strtotime($vencimiento)) . ").",
                    'dias'        => $diasRestantes,
                    'progreso'    => min(100, max(15, (int)(($diasRestantes / 30) * 100))),
                    'color'       => 'success'
                ];
            } elseif ($diasRestantes >= 0) {
                $estadoVigencia = [
                    'badge_clase' => 'bg-warning text-dark fw-bold',
                    'badge_texto' => $diasRestantes === 0 ? '¡Vence HOY!' : "Vence en {$diasRestantes}d",
                    'titulo'      => 'Tu membresía comercial vence pronto',
                    'mensaje'     => "Te quedan {$diasRestantes} días de cobertura (vence el " . date('d/m/Y', strtotime($vencimiento)) . "). Renueva con tiempo para no perder visibilidad.",
                    'dias'        => $diasRestantes,
                    'progreso'    => min(100, max(20, (int)(($diasRestantes / 30) * 100))),
                    'color'       => 'warning'
                ];
            } else {
                $diasPasados = abs($diasRestantes);
                $estadoVigencia = [
                    'badge_clase' => 'bg-danger text-white',
                    'badge_texto' => "Vencido hace {$diasPasados}d",
                    'titulo'      => 'Membresía comercial expirada',
                    'mensaje'     => "Tu período venció el " . date('d/m/Y', strtotime($vencimiento)) . ". Tu ficha está pausada en el catálogo. Renueva para reactivarla de inmediato.",
                    'dias'        => $diasRestantes,
                    'progreso'    => 0,
                    'color'       => 'danger'
                ];
            }
        }

        $urlFichaPublica = rtrim($this->config['base_url'], '/') . '/catalogo/detalle?slug=' . urlencode($lugar['slug'] ?? '');

        $this->render('negocio/dashboard', [
            'titulo'             => 'Panel de Control - ' . $lugar['nombre'],
            'lugar'              => $lugar,
            'promociones'        => $promociones,
            'promocionesActivas' => $promocionesActivas,
            'totalFotos'         => $totalFotos,
            'pagoPendiente'      => (bool)$pendiente->fetchColumn(),
            'esVisible'          => \App\Services\PublicacionService::esFichaVisible($this->idLugar),
            'fechaVencimiento'   => $vencimiento,
            'diasRestantes'      => $diasRestantes,
            'criteriosSalud'     => $criteriosSalud,
            'porcentajeSalud'    => $porcentajeSalud,
            'metricasTelemetria' => \App\Services\TelemetriaService::obtenerMetricasLugar($this->idLugar),
            'estadoVigencia'     => $estadoVigencia,
            'urlFichaPublica'    => $urlFichaPublica,
            'mensaje'            => $mensaje,
            'error'              => $error,
            'csrfToken'          => CsrfMiddleware::obtenerToken()
        ], 'negocio');
    }

    public function fotos(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM fotografias WHERE id_lugar = :id ORDER BY es_principal DESC, id_fotografia DESC");
        $stmt->execute([':id' => $this->idLugar]);
        $fotos = $stmt->fetchAll();

        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $this->render('negocio/fotos', [
            'titulo'    => 'Gestión de Fotos - ' . $lugar['nombre'],
            'lugar'     => $lugar,
            'fotos'     => $fotos,
            'mensaje'   => $mensaje,
            'error'     => $error,
            'csrfToken' => CsrfMiddleware::obtenerToken()
        ], 'negocio');
    }

    public function ubicacion(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $datosUbicacion = $_SESSION['negocio_ubicacion_datos'] ?? $lugar;
        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error'], $_SESSION['negocio_ubicacion_datos']);
        $this->render('negocio/ubicacion', [
            'titulo' => 'Ubicación de mi local',
            'lugar' => $lugar,
            'datosUbicacion' => $datosUbicacion,
            'mensaje' => $mensaje,
            'error' => $error,
            'mapaEditable' => true,
            'csrfToken' => CsrfMiddleware::obtenerToken(),
        ], 'negocio');
    }

    public function guardarUbicacion(): void {
        if (!CsrfMiddleware::validarToken(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '')) {
            $_SESSION['negocio_error'] = 'Tu sesión venció. Intenta guardar nuevamente.';
        } else {
            try {
                \App\Services\UbicacionNegocioService::guardar((int)$_SESSION['negocio_id'], $this->idLugar, $_POST);
                unset($_SESSION['negocio_ubicacion_datos']);
                $_SESSION['negocio_flash'] = 'Ubicación guardada. Este punto aparecerá en el mapa cuando tu ficha esté visible.';
            } catch (\InvalidArgumentException $e) {
                $_SESSION['negocio_error'] = $e->getMessage();
            } catch (\Throwable $e) {
                error_log('Error al guardar ubicación del negocio: ' . $e->getMessage());
                $_SESSION['negocio_error'] = 'No pudimos guardar la ubicación. Intenta nuevamente.';
            }
        }
        if (!empty($_SESSION['negocio_error'])) {
            foreach (['direccion', 'referencia_ubicacion', 'coordenadas_gps'] as $campo) {
                $_SESSION['negocio_ubicacion_datos'][$campo] = is_string($_POST[$campo] ?? null) ? $_POST[$campo] : '';
            }
        }
        header("Location: {$this->config['base_url']}/negocio/ubicacion");
        exit();
    }

    public function subirFoto(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['negocio_error'] = 'Token de seguridad inválido.';
            header("Location: {$this->config['base_url']}/negocio/fotos");
            exit();
        }

        $db = Database::getConnection();
        $archivo = null;
        $db->beginTransaction();
        try {
            $lock = $db->prepare('SELECT id_lugar FROM lugares WHERE id_lugar = ? FOR UPDATE');
            $lock->execute([$this->idLugar]);
            if (!$lock->fetchColumn()) throw new \RuntimeException('El negocio ya no está disponible.');
            if (empty($_FILES['fotografia']['tmp_name'])) {
                throw new \RuntimeException('Por favor seleccione una imagen.');
            }

            $archivo = ImagenService::subir($_FILES['fotografia']);
            if ($archivo) {
                $esPrincipal = isset($_POST['es_principal']) ? 1 : 0;
                $this->fotografiaModel->registrar($this->idLugar, $archivo, $esPrincipal);
                $_SESSION['negocio_flash'] = 'Fotografía guardada en tu galería. Su visibilidad depende de la activación de tu ficha.';
            }
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($archivo) ImagenService::eliminarArchivo($archivo['nombre_archivo']);
            $_SESSION['negocio_error'] = $e->getMessage();
        }

        header("Location: {$this->config['base_url']}/negocio/fotos");
        exit();
    }

    public function eliminarFoto(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            header("Location: {$this->config['base_url']}/negocio/fotos");
            exit();
        }

        $idFoto = (int)($_POST['id_fotografia'] ?? 0);
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT * FROM fotografias WHERE id_fotografia = :id AND id_lugar = :id_lugar");
        $stmt->execute([':id' => $idFoto, ':id_lugar' => $this->idLugar]);
        $foto = $stmt->fetch();

        if ($foto) {
            $archivoFisico = ImagenService::getDirectorioStorage() . $foto['nombre_archivo'];
            if (file_exists($archivoFisico)) {
                @unlink($archivoFisico);
            }
            $stmtDel = $db->prepare("DELETE FROM fotografias WHERE id_fotografia = :id");
            $stmtDel->execute([':id' => $idFoto]);
            $_SESSION['negocio_flash'] = 'Fotografía eliminada.';
        }

        header("Location: {$this->config['base_url']}/negocio/fotos");
        exit();
    }

    public function promociones(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $promociones = $this->promocionModel->listarPorLugar($this->idLugar);

        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $this->render('negocio/promociones', [
            'titulo'      => 'Promociones y Ofertas - ' . $lugar['nombre'],
            'lugar'       => $lugar,
            'promociones' => $promociones,
            'mensaje'     => $mensaje,
            'error'       => $error,
            'csrfToken'   => CsrfMiddleware::obtenerToken()
        ], 'negocio');
    }

    public function guardarPromocion(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['negocio_error'] = 'Petición no permitida.';
            header("Location: {$this->config['base_url']}/negocio/promociones");
            exit();
        }

        $idPromo     = (int)($_POST['id_promocion'] ?? 0);
        $titulo      = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $descuento   = trim($_POST['descuento_texto'] ?? '');
        $inicio      = trim($_POST['fecha_inicio'] ?? '');
        $fin         = trim($_POST['fecha_fin'] ?? '');
        $quitarImg   = !empty($_POST['quitar_imagen']);

        if (empty($titulo) || empty($descripcion) || empty($inicio) || empty($fin)) {
            $_SESSION['negocio_error'] = 'Complete todos los campos requeridos (*).';
            header("Location: {$this->config['base_url']}/negocio/promociones");
            exit();
        }

        $imagenArchivo = null;
        $cambioImagen  = false;

        if (!empty($_FILES['foto_promocion']['tmp_name'])) {
            try {
                $archivoSubido = ImagenService::subirPromocion($_FILES['foto_promocion']);
                if ($archivoSubido) {
                    $imagenArchivo = $archivoSubido['nombre_archivo'];
                    $cambioImagen = true;
                }
            } catch (\Throwable $e) {
                $_SESSION['negocio_error'] = 'Error al subir la imagen: ' . $e->getMessage();
                header("Location: {$this->config['base_url']}/negocio/promociones");
                exit();
            }
        } elseif ($quitarImg) {
            $imagenArchivo = null;
            $cambioImagen = true;
        }

        if ($idPromo > 0) {
            $promoExistente = $this->promocionModel->buscarPorId($idPromo, $this->idLugar);
            if (!$promoExistente) {
                $_SESSION['negocio_error'] = 'La promoción que intentas editar no existe o no te pertenece.';
                header("Location: {$this->config['base_url']}/negocio/promociones");
                exit();
            }

            $datosActualizar = [
                'titulo'          => $titulo,
                'descripcion'     => $descripcion,
                'descuento_texto' => $descuento,
                'fecha_inicio'    => $inicio,
                'fecha_fin'       => $fin
            ];

            if ($cambioImagen) {
                $datosActualizar['imagen_archivo'] = $imagenArchivo;
            }

            $this->promocionModel->actualizar($idPromo, $this->idLugar, $datosActualizar);
            $_SESSION['negocio_flash'] = 'Promoción actualizada con éxito.';
        } else {
            $this->promocionModel->crear([
                'id_lugar'        => $this->idLugar,
                'titulo'          => $titulo,
                'descripcion'     => $descripcion,
                'descuento_texto' => $descuento,
                'imagen_archivo'  => $imagenArchivo,
                'fecha_inicio'    => $inicio,
                'fecha_fin'       => $fin
            ]);
            $_SESSION['negocio_flash'] = 'Promoción guardada con éxito. Se mostrará en sus fechas cuando tu ficha esté activa.';
        }

        header("Location: {$this->config['base_url']}/negocio/promociones");
        exit();
    }

    public function estadoPromocion(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            header("Location: {$this->config['base_url']}/negocio/promociones");
            exit();
        }

        $idPromo = (int)($_POST['id_promocion'] ?? 0);
        $activo  = (int)($_POST['activo'] ?? 0);

        $this->promocionModel->cambiarEstado($idPromo, $this->idLugar, $activo);
        $_SESSION['negocio_flash'] = $activo ? 'Promoción activada.' : 'Promoción pausada.';
        header("Location: {$this->config['base_url']}/negocio/promociones");
        exit();
    }

    public function eliminarPromocion(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            header("Location: {$this->config['base_url']}/negocio/promociones");
            exit();
        }

        $idPromo = (int)($_POST['id_promocion'] ?? 0);
        $this->promocionModel->eliminar($idPromo, $this->idLugar);
        $_SESSION['negocio_flash'] = 'Promoción eliminada correctamente.';
        header("Location: {$this->config['base_url']}/negocio/promociones");
        exit();
    }

    public function perfil(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $categoria = (new \App\Models\Categoria())->buscarPorId((int)$lugar['id_categoria']);
        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $this->render('negocio/perfil', [
            'titulo'    => 'Editar Perfil - ' . $lugar['nombre'],
            'lugar'     => $lugar,
            'categoria' => $categoria,
            'mensaje'   => $mensaje,
            'error'     => $error,
            'csrfToken' => CsrfMiddleware::obtenerToken()
        ], 'negocio');
    }

    public function guardarPerfil(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['negocio_error'] = 'Sesión expirada o token inválido.';
            header("Location: {$this->config['base_url']}/negocio/perfil");
            exit();
        }

        $campos = [
            'descripcion' => 10000,
            'direccion' => 255,
            'referencia_ubicacion' => 255,
            'whatsapp_contacto' => 30,
            'telefono_contacto' => 30,
            'email_contacto' => 120,
            'horario_atencion' => 150,
            'enlace_menu' => 255,
        ];
        $datos = [];
        foreach ($campos as $campo => $longitudMaxima) {
            $valor = $_POST[$campo] ?? '';
            if (!is_string($valor)) {
                $_SESSION['negocio_error'] = 'Los datos enviados no son válidos.';
                header("Location: {$this->config['base_url']}/negocio/perfil");
                exit();
            }
            $datos[$campo] = trim($valor);
            if (mb_strlen($datos[$campo]) > $longitudMaxima) {
                $_SESSION['negocio_error'] = 'Uno de los campos supera la longitud permitida.';
                header("Location: {$this->config['base_url']}/negocio/perfil");
                exit();
            }
        }

        if ($datos['descripcion'] === '' || $datos['direccion'] === '' || $datos['whatsapp_contacto'] === '') {
            $_SESSION['negocio_error'] = 'Por favor completa los campos obligatorios: Descripción, Dirección y WhatsApp.';
            header("Location: {$this->config['base_url']}/negocio/perfil");
            exit();
        }

        if ($datos['email_contacto'] !== '' && !filter_var($datos['email_contacto'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['negocio_error'] = 'El correo electrónico ingresado no tiene un formato válido.';
            header("Location: {$this->config['base_url']}/negocio/perfil");
            exit();
        }

        try {
            $this->lugarModel->actualizarPerfilNegocio($this->idLugar, $datos);
            $_SESSION['negocio_flash'] = '¡Datos de tu negocio actualizados correctamente!';
        } catch (\Throwable $e) {
            error_log('Error al actualizar perfil de negocio: ' . $e->getMessage());
            $_SESSION['negocio_error'] = 'No se pudieron guardar los cambios. Intenta nuevamente.';
        }

        header("Location: {$this->config['base_url']}/negocio/perfil");
        exit();
    }

    public function renovar(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $db = Database::getConnection();
        $pendiente = $db->prepare("SELECT COUNT(*) FROM pagos WHERE id_lugar = ? AND estado = 'PENDIENTE'");
        $pendiente->execute([$this->idLugar]);
        $vencimiento = (new \App\Models\Vigencia())->obtenerUltimoVencimiento($this->idLugar);
        $esVisible = \App\Services\PublicacionService::esFichaVisible($this->idLugar);
        $cobro = require dirname(__DIR__, 3) . '/config/comercial.php';

        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $this->render('negocio/renovar', [
            'titulo'           => 'Renovación de Membresía - ' . $lugar['nombre'],
            'lugar'            => $lugar,
            'pagoPendiente'    => (bool)$pendiente->fetchColumn(),
            'planes'          => (new \App\Models\Tarifa())->listarPlanesActivos(),
            'esVisible'        => $esVisible,
            'fechaVencimiento' => $vencimiento,
            'cobro'            => $cobro,
            'mensaje'          => $mensaje,
            'error'            => $error,
            'csrfToken'        => CsrfMiddleware::obtenerToken()
        ], 'negocio');
    }

    public function guardarRenovacion(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['negocio_error'] = 'Sesión expirada o token inválido.';
            header("Location: {$this->config['base_url']}/negocio/renovar");
            exit();
        }

        $plan = trim($_POST['plan'] ?? 'MENSUAL');
        if (!in_array($plan, ['MENSUAL', 'ANUAL'], true)) {
            $_SESSION['negocio_error'] = 'Plan seleccionado inválido.';
            header("Location: {$this->config['base_url']}/negocio/renovar");
            exit();
        }

        $numeroComprobante = trim(is_string($_POST['numero_comprobante'] ?? null) ? $_POST['numero_comprobante'] : '');
        if (mb_strlen($numeroComprobante) > 100) {
            $_SESSION['negocio_error'] = 'El número de comprobante es demasiado largo.';
            header("Location: {$this->config['base_url']}/negocio/renovar");
            exit();
        }

        if (empty($_FILES['comprobante']['name'])) {
            $_SESSION['negocio_error'] = 'Debe adjuntar el archivo del comprobante de pago.';
            header("Location: {$this->config['base_url']}/negocio/renovar");
            exit();
        }

        $nombreArchivo = null;
        $db = Database::getConnection();
        try {
            $nombreArchivo = \App\Services\ComprobanteService::subir($_FILES['comprobante']);
            if (!Database::beginTransaction()) {
                throw new \RuntimeException('No se pudo iniciar el registro de renovación.');
            }
            $lock = $db->prepare('SELECT id_lugar FROM lugares WHERE id_lugar = ? FOR UPDATE');
            $lock->execute([$this->idLugar]);
            if (!$lock->fetchColumn()) throw new \RuntimeException('El negocio ya no está disponible.');
            $pendiente = $db->prepare("SELECT id_pago FROM pagos WHERE id_lugar = ? AND estado = 'PENDIENTE' LIMIT 1 FOR UPDATE");
            $pendiente->execute([$this->idLugar]);
            if ($pendiente->fetchColumn()) {
                throw new \RuntimeException('Ya existe un comprobante de renovación pendiente de verificación.');
            }
            $tarifa = (new \App\Models\Tarifa())->obtenerTarifaVigente($plan);
            if (!$tarifa) {
                throw new \RuntimeException('No hay una tarifa vigente configurada.');
            }

            $idPago = (new \App\Models\Pago())->registrar([
                'id_lugar'             => $this->idLugar,
                'id_tarifa'            => $tarifa['id_tarifa'],
                'monto'                => (float)$tarifa['monto'],
                'meses_duracion'       => (int)$tarifa['meses_duracion'],
                'fecha_pago_declarada' => date('Y-m-d'),
                'numero_comprobante'   => $numeroComprobante,
                'comprobante_archivo'  => $nombreArchivo,
                'metodo_pago'          => 'Transferencia bancaria / QR',
                'observaciones'        => 'Renovación de membresía desde portal comercial (' . $plan . ')',
                'es_registro_inicial'  => 0
            ]);
            $db->commit();
            $_SESSION['negocio_flash'] = "¡Comprobante de renovación enviado con éxito! Pago #{$idPago} en revisión por administración.";
            header("Location: {$this->config['base_url']}/negocio/dashboard");
            exit();
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            if ($nombreArchivo !== null) {
                try { \App\Services\ComprobanteService::eliminar($nombreArchivo); } catch (\Throwable $ignored) {}
            }
            error_log('Error en renovación de negocio: ' . $e->getMessage());
            $_SESSION['negocio_error'] = 'No se pudo registrar la renovación. Intenta nuevamente.';
            header("Location: {$this->config['base_url']}/negocio/renovar");
            exit();
        }
    }

    /* =========================================================================
     * GESTIÓN DE TRANSPORTE Y RADIOMÓVILES (Autogestión por el Sindicato / Central)
     * ========================================================================= */

    public function transporte(): void {
        $this->asegurarOperadorTransporte();
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        $paradas = $this->transporteModel->listarParadasPorLugar($this->idLugar, false);
        $tarifas = $this->transporteModel->listarTarifasPorLugar($this->idLugar, false);
        $moviles = $this->transporteModel->listarMovilesPorLugar($this->idLugar, false);

        $mensaje = $_SESSION['negocio_flash'] ?? null;
        $error   = $_SESSION['negocio_error'] ?? null;
        unset($_SESSION['negocio_flash'], $_SESSION['negocio_error']);

        $this->render('negocio/transporte', [
            'titulo'   => 'Gestión de Radiomóvil & Despacho - ' . $lugar['nombre'],
            'lugar'    => $lugar,
            'paradas'  => $paradas,
            'tarifas'  => $tarifas,
            'moviles'  => $moviles,
            'mensaje'  => $mensaje,
            'error'    => $error
        ], 'negocio');
    }

    public function guardarParada(): void {
        $this->verificarCsrfTransporte('paradas');
        $nombre    = $this->textoPost('nombre');
        $tipo      = $this->textoPost('tipo') ?: 'CENTRAL';
        $direccion = $this->textoPost('direccion');
        $telefono  = $this->textoPost('telefono_contacto');
        $latitudRaw = $this->textoPost('latitud');
        $longitudRaw = $this->textoPost('longitud');
        $latitud   = $latitudRaw !== '' ? (float)$latitudRaw : null;
        $longitud  = $longitudRaw !== '' ? (float)$longitudRaw : null;
        $idParada  = !empty($_POST['id_parada']) ? (int)$_POST['id_parada'] : null;

        if (!in_array($tipo, ['TERMINAL', 'AEROPUERTO', 'CENTRAL', 'PARADA_URBANA'], true)) {
            $this->errorTransporte('El tipo de parada no es valido.', 'paradas');
        }
        if (mb_strlen($nombre) > 150 || mb_strlen($direccion) > 255 || mb_strlen($telefono) > 50) {
            $this->errorTransporte('Uno de los datos excede la longitud permitida.', 'paradas');
        }
        if (($latitudRaw !== '' && (!is_numeric($latitudRaw) || $latitud < -90 || $latitud > 90)) || ($longitudRaw !== '' && (!is_numeric($longitudRaw) || $longitud < -180 || $longitud > 180))) {
            $this->errorTransporte('Ingresa coordenadas GPS validas.', 'paradas');
        }

        if (empty($nombre)) {
            $_SESSION['negocio_error'] = 'El nombre de la parada es obligatorio.';
            header("Location: {$this->config['base_url']}/negocio/transporte?tab=paradas");
            exit();
        }

        try {
            $this->transporteModel->guardarParada([
                'id_parada'         => $idParada,
                'id_lugar'          => $this->idLugar,
                'nombre'            => $nombre,
                'tipo'              => $tipo,
                'direccion'         => $direccion,
                'latitud'           => $latitud,
                'longitud'          => $longitud,
                'telefono_contacto' => $telefono,
                'activo'            => 1
            ]);
            $_SESSION['negocio_flash'] = 'Parada guardada correctamente.';
        } catch (\Throwable $e) {
            error_log('Error al guardar parada de transporte: ' . $e->getMessage());
            $_SESSION['negocio_error'] = 'No se pudo guardar la parada. Inténtalo nuevamente.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=paradas");
        exit();
    }

    public function eliminarParada(): void {
        $this->verificarCsrfTransporte('paradas');
        $idParada = (int)($_POST['id_parada'] ?? 0);
        if ($idParada > 0) {
            $this->transporteModel->eliminarParada($idParada, $this->idLugar);
            $_SESSION['negocio_flash'] = 'Parada eliminada correctamente.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=paradas");
        exit();
    }

    public function guardarTarifa(): void {
        $this->verificarCsrfTransporte('tarifas');
        $origen        = $this->textoPost('origen');
        $destino       = $this->textoPost('destino');
        $tipoServicio  = $this->textoPost('tipo_servicio') ?: 'URBANO_FIJO';
        $precioBaseRaw = $this->textoPost('precio_base');
        $precioNocturnoRaw = $this->textoPost('precio_nocturno');
        $precioBase    = (float)$precioBaseRaw;
        $precioNocturno= $precioNocturnoRaw !== '' ? (float)$precioNocturnoRaw : null;
        $descripcion   = $this->textoPost('descripcion');
        $idTarifa      = !empty($_POST['id_tarifa']) ? (int)$_POST['id_tarifa'] : null;

        if (!in_array($tipoServicio, ['URBANO_FIJO', 'AEROPUERTO_TERMINAL', 'EXPRESO_PROVINCIAL'], true)) {
            $this->errorTransporte('El tipo de servicio no es valido.', 'tarifas');
        }
        if (!is_numeric($precioBaseRaw) || ($precioNocturno !== null && (!is_numeric($precioNocturnoRaw) || $precioNocturno < 0)) || mb_strlen($origen) > 120 || mb_strlen($destino) > 120 || mb_strlen($descripcion) > 255) {
            $this->errorTransporte('Revisa los montos y la longitud de los datos ingresados.', 'tarifas');
        }

        if (empty($origen) || empty($destino) || $precioBase <= 0) {
            $_SESSION['negocio_error'] = 'Origen, destino y precio base son obligatorios.';
            header("Location: {$this->config['base_url']}/negocio/transporte?tab=tarifas");
            exit();
        }

        try {
            $this->transporteModel->guardarTarifa([
                'id_tarifa'       => $idTarifa,
                'id_lugar'        => $this->idLugar,
                'tipo_servicio'   => $tipoServicio,
                'origen'          => $origen,
                'destino'         => $destino,
                'precio_base'     => $precioBase,
                'precio_nocturno' => $precioNocturno,
                'descripcion'     => $descripcion,
                'activo'          => 1
            ]);
            $_SESSION['negocio_flash'] = 'Tarifa guardada correctamente.';
        } catch (\Throwable $e) {
            error_log('Error al guardar tarifa de transporte: ' . $e->getMessage());
            $_SESSION['negocio_error'] = 'No se pudo guardar la tarifa. Inténtalo nuevamente.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=tarifas");
        exit();
    }

    public function eliminarTarifa(): void {
        $this->verificarCsrfTransporte('tarifas');
        $idTarifa = (int)($_POST['id_tarifa'] ?? 0);
        if ($idTarifa > 0) {
            $this->transporteModel->eliminarTarifa($idTarifa, $this->idLugar);
            $_SESSION['negocio_flash'] = 'Tarifa eliminada con éxito.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=tarifas");
        exit();
    }

    public function guardarMovil(): void {
        $this->verificarCsrfTransporte('moviles');
        $numeroMovil    = $this->textoPost('numero_movil');
        $nombreChofer   = $this->textoPost('nombre_chofer');
        $telefonoChofer = $this->textoPost('telefono_chofer');
        $placa          = $this->textoPost('placa');
        $modeloVehiculo = $this->textoPost('modelo_vehiculo');
        $estado         = $this->textoPost('estado') ?: 'DISPONIBLE';
        $idMovil        = !empty($_POST['id_movil']) ? (int)$_POST['id_movil'] : null;

        if (!in_array($estado, ['DISPONIBLE', 'EN_CARRERA', 'FUERA_SERVICIO'], true)) {
            $this->errorTransporte('El estado del movil no es valido.', 'moviles');
        }
        if (mb_strlen($numeroMovil) > 20 || mb_strlen($nombreChofer) > 120 || mb_strlen($telefonoChofer) > 30 || mb_strlen($placa) > 20 || mb_strlen($modeloVehiculo) > 100) {
            $this->errorTransporte('Uno de los datos excede la longitud permitida.', 'moviles');
        }

        if (empty($numeroMovil) || empty($nombreChofer)) {
            $_SESSION['negocio_error'] = 'El número de móvil y el nombre del chofer son obligatorios.';
            header("Location: {$this->config['base_url']}/negocio/transporte?tab=moviles");
            exit();
        }

        try {
            $this->transporteModel->guardarMovil([
                'id_movil'        => $idMovil,
                'id_lugar'        => $this->idLugar,
                'numero_movil'    => $numeroMovil,
                'nombre_chofer'   => $nombreChofer,
                'telefono_chofer' => $telefonoChofer,
                'placa'           => $placa,
                'modelo_vehiculo' => $modeloVehiculo,
                'estado'          => $estado,
                'activo'          => 1
            ]);
            $_SESSION['negocio_flash'] = 'Móvil registrado correctamente.';
        } catch (\Throwable $e) {
            error_log('Error al guardar móvil de transporte: ' . $e->getMessage());
            $_SESSION['negocio_error'] = 'No se pudo guardar el móvil. Inténtalo nuevamente.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=moviles");
        exit();
    }

    public function cambiarEstadoMovil(): void {
        $this->verificarCsrfTransporte('moviles');
        $idMovil = (int)($_POST['id_movil'] ?? 0);
        $estado  = $this->textoPost('estado') ?: 'DISPONIBLE';
        if (!in_array($estado, ['DISPONIBLE', 'EN_CARRERA', 'FUERA_SERVICIO'], true)) {
            $this->errorTransporte('El estado del móvil no es válido.', 'moviles');
        }
        if ($idMovil > 0 && $this->transporteModel->cambiarEstadoMovil($idMovil, $estado, $this->idLugar)) {
            $_SESSION['negocio_flash'] = 'Estado del móvil actualizado.';
        } else {
            $_SESSION['negocio_error'] = 'No se encontró el móvil solicitado.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=moviles");
        exit();
    }

    public function eliminarMovil(): void {
        $this->verificarCsrfTransporte('moviles');
        $idMovil = (int)($_POST['id_movil'] ?? 0);
        if ($idMovil > 0) {
            $this->transporteModel->eliminarMovil($idMovil, $this->idLugar);
            $_SESSION['negocio_flash'] = 'Móvil eliminado de la flota.';
        }
        header("Location: {$this->config['base_url']}/negocio/transporte?tab=moviles");
        exit();
    }

    private function verificarCsrfTransporte(string $tab): void {
        $this->asegurarOperadorTransporte();
        $token = $_POST['csrf_token'] ?? null;
        if (!is_string($token) || !CsrfMiddleware::validarToken($token)) {
            $this->errorTransporte('La sesion del formulario expiro. Recarga la pagina e intentalo nuevamente.', $tab);
        }
    }

    private function errorTransporte(string $mensaje, string $tab): void {
        $_SESSION['negocio_error'] = $mensaje;
        header("Location: {$this->config['base_url']}/negocio/transporte?tab={$tab}");
        exit();
    }

    private function textoPost(string $campo): string {
        $valor = $_POST[$campo] ?? '';
        return is_string($valor) ? trim($valor) : '';
    }

    private function asegurarOperadorTransporte(): void {
        $lugar = $this->lugarModel->buscarPorId($this->idLugar);
        if (empty($lugar['es_operador_transporte']) || ($lugar['estado_verificacion_transporte'] ?? '') !== 'APROBADO') {
            http_response_code(403);
            exit('El panel de transporte solo esta disponible para sindicatos y centrales aprobadas.');
        }
    }

    public function cambiarEstadoOperativo(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['negocio_error'] = 'Sesión expirada o token inválido.';
            header("Location: {$this->config['base_url']}/negocio/dashboard");
            exit();
        }

        $accion = trim($_POST['accion'] ?? '');
        if ($accion === 'ABRIR') {
            $this->lugarModel->actualizarEstadoOperativo($this->idLugar, 'ABIERTO_HABITUAL', null, null);
            $_SESSION['negocio_flash'] = '¡Tu negocio ha reanudado su horario de atención habitual!';
        } elseif ($accion === 'CERRAR') {
            $motivo = trim($_POST['motivo'] ?? '');
            $reapertura = !empty($_POST['reapertura']) ? trim($_POST['reapertura']) : null;
            if (empty($motivo)) {
                $motivo = 'Cerrado temporalmente por mantenimiento o evento.';
            }

            $this->lugarModel->actualizarEstadoOperativo($this->idLugar, 'CERRADO_TEMPORAL', $motivo, $reapertura);
            $_SESSION['negocio_flash'] = 'Estado actualizado: Tu negocio se muestra como "Cerrado temporalmente" con tu aviso.';
        }

        header("Location: {$this->config['base_url']}/negocio/dashboard");
        exit();
    }
}

