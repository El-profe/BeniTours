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
        $todasCategorias = $this->categoriaModel->listarTodasActivas();
        // Filtrar transporte para que no se mezcle con las categorías turísticas generales
        $categorias = array_values(array_filter($todasCategorias, function ($c) {
            return (int)$c['id_categoria'] !== 9 && ($c['slug'] ?? '') !== 'transporte-radiomoviles';
        }));
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
        if ($categoriaSeleccionada === null) {
            // Excluir transporte del listado general de atractivos/destinos turísticos
            $lugares = array_values(array_filter($lugares, fn($l) => (int)$l['id_categoria'] !== 9));
        }

        // Obtener fotos secundarias (hasta 4) para el efecto hover interactivo en tarjetas
        $idsLugares = array_map(fn($l) => (int)$l['id_lugar'], $lugares);
        $fotosPorLugar = [];
        if (!empty($idsLugares)) {
            $inClause = implode(',', $idsLugares);
            $db = \App\Core\Database::getConnection();
            $stmtFotos = $db->query("SELECT id_fotografia, id_lugar, nombre_archivo, es_principal 
                                     FROM fotografias 
                                     WHERE id_lugar IN ({$inClause}) 
                                     ORDER BY id_lugar, es_principal DESC, id_fotografia ASC");
            $todasFotos = $stmtFotos->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($todasFotos as $tf) {
                $idL = (int)$tf['id_lugar'];
                if (!isset($fotosPorLugar[$idL])) {
                    $fotosPorLugar[$idL] = [];
                }
                if (count($fotosPorLugar[$idL]) < 4) {
                    $fotosPorLugar[$idL][] = $tf['nombre_archivo'];
                }
            }
        }
        foreach ($lugares as &$lugItem) {
            $lugItem['fotos_galeria'] = $fotosPorLugar[(int)$lugItem['id_lugar']] ?? [];
        }
        unset($lugItem);
        $promocionesVigentes = (new Promocion())->listarTodasVigentesPublicas();
        $eventosDestacados = (new \App\Models\Evento())->listarVigentesPublicos();
        $fraternidadesEntrada = (new \App\Models\Fraternidad())->listarTodas();
        $rutasRecomendadas = \App\Services\RutaTuristicaService::obtenerRutasRecomendadas($idMunicipio);
        $radiomoviles = $this->lugarModel->listarPublicos(9, null, $idMunicipio ?: 1);
        $paradasTransporte = (new \App\Models\LugarParada())->listarTodasActivas();

        // Galería destacada de postales turísticas y fotos libres
        $galeriaFotos = (new \App\Models\GaleriaFoto())->listarActivas(8);
        $fotosMapa = (new \App\Models\MapaProvinciaFoto())->listarTodasIndexadas();

        $ogMeta = [
            'title'       => 'BeniTours - Directorio Turístico y Comercial del Beni',
            'description' => 'Descubre los mejores destinos, atractivos naturales, gastronomía amazónica, hospedaje y comercios locales en Trinidad y las principales rutas del Beni.',
            'image'       => "{$this->config['base_url']}/assets/img/hero-poster.webp",
            'url'         => "{$this->config['base_url']}/",
            'type'        => 'website',
            'image_type'  => 'image/webp'
        ];

        // Agenda y Cronograma Oficial Completo 58° Aniversario UAB & Fiestas de Noviembre (Páginas 1, 2 y 3)
        $agendaUAB = [
            // --- PÁGINA 1 ---
            [
                'fecha_corta' => '03/11',
                'hora'        => '10:00',
                'titulo'      => 'Acto Académico del VIII Aniversario del Programa de Cultura Física y Recreación',
                'organiza'    => 'Programa Cultura Física y Recreación',
                'lugar'       => 'Salón Bicentenario',
                'tipo'        => 'ACADÉMICO',
                'categoria'   => 'academico',
                'icono'       => 'bi-mortarboard-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '03/11 al 06/11',
                'hora'        => '18:00',
                'titulo'      => 'II Versión de la Feria Exposición de artes denominada "Alas alrededor"',
                'organiza'    => 'IUA, Cultura y Deporte, MCPL',
                'lugar'       => 'Galería de Artes "Juan Carlos Aguirre Muñoz"',
                'tipo'        => 'CULTURA Y ARTE',
                'categoria'   => 'cultura',
                'icono'       => 'bi-palette-fill',
                'badge'       => 'bg-teal-subtle text-teal-dark border border-teal-subtle'
            ],
            [
                'fecha_corta' => '06/11',
                'hora'        => '08:00 a 12:00',
                'titulo'      => 'Feria Jurídica "El derecho al servicio de la sociedad"',
                'organiza'    => 'Facultad de Ciencias Jurídicas, Políticas y Sociales',
                'lugar'       => 'Plaza Principal de Trinidad "Gral. José Ballivián"',
                'tipo'        => 'FERIA SOCIAL',
                'categoria'   => 'ferias',
                'icono'       => 'bi-briefcase-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '06/11',
                'hora'        => '16:00 a 21:00',
                'titulo'      => 'Feria Productiva, Científica, de emprendimiento, proyecto y socialización de carreras',
                'organiza'    => 'Facultad de Ciencias Económicas (FCE)',
                'lugar'       => 'Plaza Principal de Trinidad "Gral. José Ballivián"',
                'tipo'        => 'EMPRENDIMIENTO',
                'categoria'   => 'ferias',
                'icono'       => 'bi-lightbulb-fill',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '07/11',
                'hora'        => '12:00',
                'titulo'      => 'Festival Barace 2026: Churrasco, Historia y Tradición (En apoyo a la fundación un boliviano contra el cáncer)',
                'organiza'    => 'Kaliman asador y La Fundación un boliviano contra el cáncer',
                'lugar'       => 'Campo Ferial FEXPOBENI',
                'tipo'        => 'FESTIVAL SOLIDARIO',
                'categoria'   => 'festivales',
                'icono'       => 'bi-fire',
                'badge'       => 'bg-danger text-white'
            ],
            [
                'fecha_corta' => '09/11',
                'hora'        => '19:00',
                'titulo'      => 'Sesión de Honor y Conmemoración de la Facultad de Derecho',
                'organiza'    => 'Facultad de Ciencias Jurídicas y Políticas',
                'lugar'       => 'Sala de Juicios Orales',
                'tipo'        => 'PROTOCOLO',
                'categoria'   => 'academico',
                'icono'       => 'bi-award-fill',
                'badge'       => 'bg-teal-subtle text-teal-dark border border-teal-subtle'
            ],
            [
                'fecha_corta' => '10/11',
                'hora'        => '07:00',
                'titulo'      => 'Ofrenda Floral al Héroe Nacional Pedro Ignacio Muiba en conmemoración de la Gesta Libertaria de 1810',
                'organiza'    => 'Gobernación del Beni',
                'lugar'       => 'Monumento Pedro Ignacio Muiba',
                'tipo'        => 'ACTO CÍVICO',
                'categoria'   => 'civicos',
                'icono'       => 'bi-flower1',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '12/11',
                'hora'        => '19:00',
                'titulo'      => '"Arte con Identidad: Contribución Cultural al Frontis del Instituto de Artes"',
                'organiza'    => 'Instituto Universitario de Artes (IUA)',
                'lugar'       => 'Frontis del Instituto Universitario de Artes',
                'tipo'        => 'CULTURA Y ARTE',
                'categoria'   => 'cultura',
                'icono'       => 'bi-brush-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '13/11',
                'hora'        => '19:00',
                'titulo'      => 'Elección de la Ñusta y Moperita Universitaria 2026',
                'organiza'    => 'FUL - Dirección de Bienestar Estudiantil',
                'lugar'       => 'Cancha Vieja Platillo Volador',
                'tipo'        => 'FOLKLORE Y BELLEZA',
                'categoria'   => 'folklore',
                'icono'       => 'bi-gem',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '13/11',
                'hora'        => '20:00',
                'titulo'      => 'Remate de Ganado Cabaña Taj Mahal & El Álamo',
                'organiza'    => 'Cabaña Taj Mahal & El Álamo',
                'lugar'       => 'Centro de Remate Guillermo Tineo Leigue',
                'tipo'        => 'REMATE GANADERO',
                'categoria'   => 'remates',
                'icono'       => 'bi-tag-fill',
                'badge'       => 'bg-teal-dark text-white'
            ],

            // --- PÁGINA 2 ---
            [
                'fecha_corta' => '14/11',
                'hora'        => '09:00',
                'titulo'      => 'XIII Entrada Folklórica Nacional 2026 y XXII Entrada Folklórica Universitaria Trinidad 2026',
                'organiza'    => 'Dirección de Bienestar Estudiantil – FUL',
                'lugar'       => 'Concentración Av. Prof. Rogers Becerra Casanovas (Cambódromo y esquina Japón)',
                'tipo'        => 'ENTRADA FOLKLÓRICA',
                'categoria'   => 'folklore',
                'icono'       => 'bi-music-note-beamed',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '14/11',
                'hora'        => '20:00',
                'titulo'      => 'Apertura Oficial FEXPOBENI 2026',
                'organiza'    => 'FEXPOBENI y Rectorado UAB',
                'lugar'       => 'Campo Ferial FEXPOBENI',
                'tipo'        => 'FERIA INTERNACIONAL',
                'categoria'   => 'fexpobeni',
                'icono'       => 'bi-shop-window',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '14/11',
                'hora'        => '20:00',
                'titulo'      => 'Remate de Ganado Lechero de Élite',
                'organiza'    => 'ASOCEBU y CERMA',
                'lugar'       => 'Centro de Remate Guillermo Tineo Leigue',
                'tipo'        => 'REMATE GANADERO',
                'categoria'   => 'remates',
                'icono'       => 'bi-award-fill',
                'badge'       => 'bg-teal-dark text-white'
            ],
            [
                'fecha_corta' => '15/11',
                'hora'        => 'Por confirmar',
                'titulo'      => 'Inauguración Oficial FEXPOBENI 2026',
                'organiza'    => 'FEXPOBENI y Rectorado UAB',
                'lugar'       => 'Escenario Principal Campo Ferial',
                'tipo'        => 'INAUGURACIÓN FERIA',
                'categoria'   => 'fexpobeni',
                'icono'       => 'bi-megaphone-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '15/11',
                'hora'        => '20:00',
                'titulo'      => 'Remate de Ganado Cabaña Taj Mahal',
                'organiza'    => 'Cabaña Taj Mahal',
                'lugar'       => 'Club Social 18 de Noviembre',
                'tipo'        => 'REMATE GANADERO',
                'categoria'   => 'remates',
                'icono'       => 'bi-tag-fill',
                'badge'       => 'bg-teal-dark text-white'
            ],
            [
                'fecha_corta' => '16/11 al 18/11',
                'hora'        => 'Jornada completa',
                'titulo'      => 'Congreso Nacional de Universidades',
                'organiza'    => 'Comité Ejecutivo de la Universidad Boliviana (CEUB) y UAB',
                'lugar'       => 'Sede de Docentes UAB',
                'tipo'        => 'CONGRESO ACADÉMICO',
                'categoria'   => 'academico',
                'icono'       => 'bi-building-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '16/11 al 18/11',
                'hora'        => '20:00',
                'titulo'      => 'Remate de Ganado Cabaña Mucarzel',
                'organiza'    => 'Cabaña Mucarzel',
                'lugar'       => 'Centro de Remate Guillermo Tineo Leigue',
                'tipo'        => 'REMATE GANADERO',
                'categoria'   => 'remates',
                'icono'       => 'bi-tag-fill',
                'badge'       => 'bg-teal-dark text-white'
            ],
            [
                'fecha_corta' => '17/11',
                'hora'        => '08:30',
                'titulo'      => 'Acto de Colación de Grado y Toma de Juramento Profesional',
                'organiza'    => 'Rectorado y Secretaría General UAB',
                'lugar'       => 'Salón Bicentenario',
                'tipo'        => 'GRADUACIÓN',
                'categoria'   => 'academico',
                'icono'       => 'bi-mortarboard-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '17/11',
                'hora'        => '19:00',
                'titulo'      => 'Acto Central de Celebración del LIX Aniversario de la Universidad Autónoma del Beni "José Ballivián"',
                'organiza'    => 'Rectorado y DICOM UAB',
                'lugar'       => 'Salón Bicentenario',
                'tipo'        => 'ANIVERSARIO OFICIAL',
                'categoria'   => 'civicos',
                'icono'       => 'bi-star-fill',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '17/11',
                'hora'        => '20:00',
                'titulo'      => 'Remate de Ganado Cabaña Sausalito',
                'organiza'    => 'Cabaña Sausalito',
                'lugar'       => 'Centro de Remate Guillermo Tineo Leigue',
                'tipo'        => 'REMATE GANADERO',
                'categoria'   => 'remates',
                'icono'       => 'bi-tag-fill',
                'badge'       => 'bg-teal-dark text-white'
            ],
            [
                'fecha_corta' => '17/11',
                'hora'        => '23:00',
                'titulo'      => 'Serenata al Beni por sus 184 Años de Creación',
                'organiza'    => 'Rectorado UAB y Campo Ferial FexpoBeni',
                'lugar'       => 'Escenario Principal Campo Ferial',
                'tipo'        => 'FESTIVAL & SERENATA',
                'categoria'   => 'festivales',
                'icono'       => 'bi-music-note-list',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '18/11',
                'hora'        => '07:00',
                'titulo'      => 'Ofrenda Floral al pie del monumento del Mariscal José Ballivián',
                'organiza'    => 'Rectorado UAB',
                'lugar'       => 'Plaza Mariscal José Ballivián',
                'tipo'        => 'ACTO PROTOCOLAR',
                'categoria'   => 'civicos',
                'icono'       => 'bi-flower1',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '18/11',
                'hora'        => '08:00',
                'titulo'      => 'Acto y Desfile Cívico en conmemoración a la Efeméride Departamental del Beni',
                'organiza'    => 'Gobernación del Beni',
                'lugar'       => 'Concentración: Frontis Edificio Central UAB',
                'tipo'        => 'DESFILE DE HONOR',
                'categoria'   => 'civicos',
                'icono'       => 'bi-flag-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '18/11',
                'hora'        => '23:00',
                'titulo'      => 'Clausura Oficial FEXPOBENI 2026',
                'organiza'    => 'FEXPOBENI y Rectorado UAB',
                'lugar'       => 'Campo Ferial FEXPOBENI',
                'tipo'        => 'CLAUSURA FERIA',
                'categoria'   => 'fexpobeni',
                'icono'       => 'bi-award',
                'badge'       => 'bg-teal-subtle text-teal-dark border border-teal-subtle'
            ],

            // --- PÁGINA 3 ---
            [
                'fecha_corta' => '19/11',
                'hora'        => '10:30',
                'titulo'      => 'Acto de Celebración por el XLVIII Aniversario de Canal 11 (RED RUBI)',
                'organiza'    => 'DICOM UAB',
                'lugar'       => 'Salón de Postgrado UABJB',
                'tipo'        => 'ANIVERSARIO TELEVISIÓN',
                'categoria'   => 'academico',
                'icono'       => 'bi-tv-fill',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '20/11',
                'hora'        => '08:00 a 15:00',
                'titulo'      => 'Mini Feria de Libros por los "56 Años de Aniversario de la Biblioteca Central"',
                'organiza'    => 'Biblioteca Central UAB',
                'lugar'       => 'Atrio exterior del Campus Aniversario "Dr. Luis Adolfo Siles Salinas"',
                'tipo'        => 'FERIA DEL LIBRO',
                'categoria'   => 'cultura',
                'icono'       => 'bi-book-half',
                'badge'       => 'bg-warning text-dark'
            ],
            [
                'fecha_corta' => '20/11',
                'hora'        => '10:00',
                'titulo'      => 'Entrega del Modelo de Diseño Curricular',
                'organiza'    => 'Planificación Académica UAB',
                'lugar'       => 'Salón Bicentenario',
                'tipo'        => 'ACADÉMICO INSTITUCIONAL',
                'categoria'   => 'academico',
                'icono'       => 'bi-journal-check',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '25/11',
                'hora'        => '08:00 a 13:00',
                'titulo'      => 'Día de Campo por el XI Aniversario de los Laboratorios de la UABJB (NUBROMS)',
                'organiza'    => 'Laboratorios UAB',
                'lugar'       => 'Ambientes de los Laboratorios Campus Hernán Melgar Justiniano',
                'tipo'        => 'CIENCIA & CAMPO',
                'categoria'   => 'academico',
                'icono'       => 'bi-moisture',
                'badge'       => 'bg-teal'
            ],
            [
                'fecha_corta' => '29/11',
                'hora'        => '09:00',
                'titulo'      => 'Acto de Inauguración y Emplazamiento del Monumento a Oscar Velarde y Festival de Música',
                'organiza'    => 'Consejo Departamental de Culturas del Beni',
                'lugar'       => 'Teatro al aire libre "Oscar Velarde Vejarano"',
                'tipo'        => 'FESTIVAL CULTURAL',
                'categoria'   => 'cultura',
                'icono'       => 'bi-music-player-fill',
                'badge'       => 'bg-warning text-dark'
            ]
        ];

        $this->render('publico/catalogo/index', [
            'titulo'                => 'BeniTours - Directorio Turístico y Comercial del Beni',
            'heroPantallaCompleta'  => true,
            'categorias'            => $categorias,
            'municipios'            => $municipios,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'municipioSeleccionado' => $idMunicipio,
            'lugares'               => $lugares,
            'fotosPorLugar'         => $fotosPorLugar,
            'promocionesVigentes'   => $promocionesVigentes,
            'eventosDestacados'     => $eventosDestacados,
            'agendaUAB'             => $agendaUAB,
            'fraternidadesEntrada'  => $fraternidadesEntrada,
            'rutasRecomendadas'     => $rutasRecomendadas,
            'radiomoviles'          => $radiomoviles,
            'paradasTransporte'     => $paradasTransporte,
            'galeriaFotos'          => $galeriaFotos,
            'fotosMapa'             => $fotosMapa,
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

        // Si es lugar de transporte o sindicato, cargar sus paradas y casetas oficiales
        $paradasTransporte = [];
        if ((int)($lugar['id_categoria'] ?? 0) === 9 || mb_stripos($lugar['nombre'], 'radio') !== false || mb_stripos($lugar['nombre'], 'taxi') !== false) {
            $paradasTransporte = (new \App\Models\LugarParada())->listarPorLugar((int)$lugar['id_lugar']);
        }

        $this->render('publico/catalogo/detalle', [
            'titulo'            => $lugar['nombre'],
            'lugar'             => $lugar,
            'fotografias'       => $fotografias,
            'mapa'              => MapaService::desdeCoordenadas($lugar['coordenadas_gps'] ?? null),
            'promociones'       => $promociones,
            'paradasTransporte' => $paradasTransporte,
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
        if ($idCategoria === null) {
            // Excluir transporte del listado general de atractivos/destinos turísticos
            $resultados = array_values(array_filter($resultados, fn($l) => (int)$l['id_categoria'] !== 9));
        }
        // Obtener fotos secundarias (hasta 4) para el efecto hover interactivo en tarjetas
        $idsLugares = array_map(fn($l) => (int)$l['id_lugar'], $resultados);
        $fotosPorLugar = [];
        if (!empty($idsLugares)) {
            $inClause = implode(',', $idsLugares);
            $db = \App\Core\Database::getConnection();
            $stmtFotos = $db->query("SELECT id_fotografia, id_lugar, nombre_archivo, es_principal 
                                     FROM fotografias 
                                     WHERE id_lugar IN ({$inClause}) 
                                     ORDER BY id_lugar, es_principal DESC, id_fotografia ASC");
            $todasFotos = $stmtFotos->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($todasFotos as $tf) {
                $idL = (int)$tf['id_lugar'];
                if (!isset($fotosPorLugar[$idL])) {
                    $fotosPorLugar[$idL] = [];
                }
                if (count($fotosPorLugar[$idL]) < 4) {
                    $fotosPorLugar[$idL][] = $tf['nombre_archivo'];
                }
            }
        }

        foreach ($resultados as &$resultado) {
            $resultado['fotos_galeria'] = $fotosPorLugar[(int)$resultado['id_lugar']] ?? [];
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
     * Endpoint API para consultar rutas turísticas recomendadas optimizadas
     */
    public function apiRutasRecomendadas(): void {
        $idMunicipio = !empty($_GET['municipio']) ? (int)$_GET['municipio'] : null;
        $userLat = isset($_GET['lat']) && is_numeric($_GET['lat']) ? (float)$_GET['lat'] : null;
        $userLng = isset($_GET['lng']) && is_numeric($_GET['lng']) ? (float)$_GET['lng'] : null;

        $rutas = \App\Services\RutaTuristicaService::obtenerRutasRecomendadas($idMunicipio, $userLat, $userLng);

        $this->json([
            'success' => true,
            'rutas'   => $rutas
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
