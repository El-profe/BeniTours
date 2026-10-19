<?php
namespace App\Controllers\Publico;

use App\Core\Controller;
use App\Models\Lugar;
use App\Models\Categoria;
use App\Models\Municipio;
use App\Models\Promocion;
use App\Models\Fotografia;
use App\Services\MapaService;
use App\Services\AnaliticaVisitasService;

class CatalogoController extends Controller {
    private Lugar $lugarModel;
    private Categoria $categoriaModel;
    private Municipio $municipioModel;

    public function __construct() {
        parent::__construct();
        $this->lugarModel = new Lugar();
        $this->categoriaModel = new Categoria();
        $this->municipioModel = new Municipio();
    }

    public function index(): void {
        AnaliticaVisitasService::registrarVisita();
        $categorias = $this->categoriaModel->listarTodasActivas();
        $municipios = $this->municipioModel->listarActivos();

        $categoriaSeleccionada = null;
        $slugCategoria = is_string($_GET['categoria'] ?? null) ? $_GET['categoria'] : '';
        foreach ($categorias as $categoria) {
            if ($categoria['slug'] === $slugCategoria) {
                $categoriaSeleccionada = (int)$categoria['id_categoria'];
                break;
            }
        }

        $idMunicipio = !empty($_GET['municipio']) ? (int)$_GET['municipio'] : null;
        $lugares = $this->lugarModel->listarPublicos($categoriaSeleccionada, null, $idMunicipio);
        $promocionesVigentes = (new Promocion())->listarTodasVigentesPublicas();
        $eventosDestacados = (new \App\Models\Evento())->listarVigentesPublicos();

        $ogMeta = [
            'title'       => 'BeniTours - Directorio Turístico y Comercial del Beni',
            'description' => 'Descubre los mejores destinos, atractivos naturales, gastronomía amazónica, hospedaje y comercios locales en Trinidad y las principales rutas del Beni.',
            'image'       => "{$this->config['base_url']}/assets/img/hero-poster.webp",
            'url'         => "{$this->config['base_url']}/",
            'type'        => 'website',
            'image_type'  => 'image/webp'
        ];

        $this->render('publico/catalogo/index', [
            'titulo'                => 'BeniTours - Directorio Turístico y Comercial del Beni',
            'heroPantallaCompleta'  => true,
            'categorias'            => $categorias,
            'municipios'            => $municipios,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'municipioSeleccionado' => $idMunicipio,
            'lugares'               => $lugares,
            'promocionesVigentes'   => $promocionesVigentes,
            'eventosDestacados'     => $eventosDestacados,
            'ogMeta'                => $ogMeta
        ], 'publico');
    }

    public function detalle(): void {
        AnaliticaVisitasService::registrarVisita();
        $slug = trim($_GET['slug'] ?? '');
        if (empty($slug)) {
            header("Location: {$this->config['base_url']}/");
            exit();
        }

        $lugar = $this->lugarModel->buscarPublicoPorSlug($slug);
        if (!$lugar) {
            http_response_code(404);
            $appName = $this->config['app_name'];
            $baseUrl = $this->config['base_url'];
            require dirname(__DIR__, 2) . '/Views/errors/404.php';
            exit();
        }

        // Registrar telemetría de vista de ficha comercial o turística
        \App\Services\TelemetriaService::registrarEvento((int)$lugar['id_lugar'], 'VISTA');

        $promocionModel = new Promocion();
        $promociones = $promocionModel->listarVigentesPublicas((int)$lugar['id_lugar']);
        $fotografias = (new Fotografia())->listarPorLugar((int)$lugar['id_lugar']);

        $imgUrl = !empty($lugar['imagen'])
            ? "{$this->config['base_url']}/imagen?f=" . urlencode($lugar['imagen'])
            : "{$this->config['base_url']}/assets/img/hero-poster.webp";

        $descRaw = trim(strip_tags($lugar['descripcion'] ?? ''));
        if (!empty($descRaw)) {
            $descOg = mb_substr($descRaw, 0, 160, 'UTF-8');
            if (mb_strlen($descRaw, 'UTF-8') > 160) {
                $descOg .= '...';
            }
        } else {
            $descOg = "Conoce {$lugar['nombre']} ({$lugar['categoria']}) en {$lugar['municipio']}, Beni. Encuentra ubicación, fotos, contacto directo y promociones en BeniTours.";
        }

        $tituloOg = !empty($lugar['municipio'])
            ? "{$lugar['nombre']} ({$lugar['municipio']}) | BeniTours"
            : "{$lugar['nombre']} | BeniTours";

        $ogMeta = [
            'title'       => $tituloOg,
            'description' => $descOg,
            'image'       => $imgUrl,
            'url'         => "{$this->config['base_url']}/catalogo/detalle?slug=" . urlencode($lugar['slug']),
            'type'        => 'place'
        ];

        $this->render('publico/catalogo/detalle', [
            'titulo'            => $lugar['nombre'],
            'lugar'             => $lugar,
            'fotografias'       => $fotografias,
            'mapa'              => MapaService::desdeCoordenadas($lugar['coordenadas_gps'] ?? null),
            'promociones'       => $promociones,
            'ogMeta'            => $ogMeta
        ], 'publico');
    }

    /**
     * Endpoint API para búsquedas y filtros en vivo vía Fetch.
     */
    public function apiBuscar(): void {
        $idCategoria = !empty($_GET['categoria']) ? (int)$_GET['categoria'] : null;
        $idMunicipio = !empty($_GET['municipio']) ? (int)$_GET['municipio'] : null;
        $termino = !empty($_GET['q']) ? trim($_GET['q']) : null;

        $resultados = $this->lugarModel->listarPublicos($idCategoria, $termino, $idMunicipio);
        foreach ($resultados as &$resultado) {
            $resultado['estado_horario'] = \App\Services\HorarioHelper::evaluarEstado(
                $resultado['horario_atencion'] ?? null,
                $resultado['estado_operativo'] ?? null,
                $resultado['aviso_temporal_motivo'] ?? null,
                $resultado['aviso_temporal_reapertura'] ?? null
            );
        }
        unset($resultado);

        $this->json([
            'success'      => true,
            'total'        => count($resultados),
            'resultados'   => $resultados
        ]);
    }

    /**
     * Endpoint API para registrar eventos de telemetría (WhatsApp, Mapa, Favoritos, etc.)
     */
    public function apiRegistrarEvento(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $idLugar = (int)($input['id_lugar'] ?? ($_POST['id_lugar'] ?? 0));
        $evento = (string)($input['tipo_evento'] ?? ($_POST['tipo_evento'] ?? ''));

        if ($idLugar > 0 && !empty($evento)) {
            $exito = \App\Services\TelemetriaService::registrarEvento($idLugar, $evento);
            $this->json(['success' => $exito]);
            return;
        }

        http_response_code(400);
        $this->json(['success' => false, 'error' => 'Parámetros no válidos.']);
    }
}
