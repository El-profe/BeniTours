<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Inicio') ?> | <?= htmlspecialchars($appName ?? 'Trinidad Turismo') ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo2.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo2.png') ?>">
    <!-- SEO y Redes Sociales (Open Graph & Twitter Cards) -->
    <meta name="description" content="<?= htmlspecialchars($ogMeta['description'] ?? 'Directorio turístico y comercial del Beni: descubre atractivos naturales, gastronomía, hoteles y rutas en Trinidad.') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($ogMeta['url'] ?? $baseUrl . '/') ?>">

    <!-- Open Graph (WhatsApp, Facebook, LinkedIn, Telegram) -->
    <meta property="og:site_name" content="BeniTours">
    <meta property="og:locale" content="es_BO">
    <meta property="og:type" content="<?= htmlspecialchars($ogMeta['type'] ?? 'website') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($ogMeta['title'] ?? ($titulo . ' | ' . ($appName ?? 'BeniTours'))) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($ogMeta['description'] ?? 'Directorio turístico y comercial del Beni: descubre atractivos naturales, gastronomía, hoteles y rutas en Trinidad.') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($ogMeta['url'] ?? $baseUrl . '/') ?>">
    <?php if (!empty($ogMeta['image'])): ?>
        <meta property="og:image" content="<?= htmlspecialchars($ogMeta['image']) ?>">
        <meta property="og:image:secure_url" content="<?= htmlspecialchars($ogMeta['image']) ?>">
        <meta property="og:image:alt" content="<?= htmlspecialchars($ogMeta['title'] ?? 'BeniTours') ?>">
        <?php if (!empty($ogMeta['image_type'])): ?>
            <meta property="og:image:type" content="<?= htmlspecialchars($ogMeta['image_type']) ?>">
        <?php endif; ?>
    <?php endif; ?>

    <!-- Twitter Cards (X) -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($ogMeta['title'] ?? ($titulo . ' | ' . ($appName ?? 'BeniTours'))) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($ogMeta['description'] ?? '') ?>">
    <?php if (!empty($ogMeta['image'])): ?>
        <meta name="twitter:image" content="<?= htmlspecialchars($ogMeta['image']) ?>">
    <?php endif; ?>

    <!-- Preconexión y optimización de DNS para recursos externos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="preconnect" href="https://tile.openstreetmap.org">
    <link rel="dns-prefetch" href="https://tile.openstreetmap.org">

    <!-- Tipografía Google Fonts: Poppins (400, 500, 600, 700, 800) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">

    <!-- Bootstrap 5.3.3 CSS (Local) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/css/bootstrap.min.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (Local) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <!-- Font Awesome 6 Free (Iconos Universales) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Leaflet 1.9.4 CSS (Local) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/leaflet/leaflet.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/leaflet/leaflet.css') ?>">
    <!-- AOS (Animate On Scroll) CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/css/app.css?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/css/app.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100 bg-light<?= !empty($heroPantallaCompleta) ? ' page-home' : '' ?><?= !empty($bodyClass) ? ' ' . htmlspecialchars($bodyClass) : '' ?>">

    <!-- Navegación Superior -->
    <nav class="navbar navbar-expand-xl navbar-dark navbar-trinidad sticky-top shadow-sm py-2<?= !empty($heroPantallaCompleta) ? ' navbar-transparente' : ' navbar-scrolled' ?>" id="navbarPrincipal">
        <div class="container-fluid px-2 px-lg-3">
            <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars($baseUrl) ?>/">
                <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo.png') ?>"
                    alt="BeniTours" 
                    class="navbar-logo"
                    height="42"
                    <?php if (!empty($heroPantallaCompleta)): ?>
                        data-logo-principal="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo.png') ?>"
                        data-logo-alterno="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo3.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo3.png') ?>"
                        data-logo-institucional="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo4.png?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/img/logo4.png') ?>"
                    <?php endif; ?>>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-xl-0 fw-medium d-flex align-items-center gap-1 gap-xl-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= htmlspecialchars($baseUrl) ?>/#inicio" data-nav-link="inicio">
                            <i class="bi bi-house-door me-1"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item ms-xl-1 me-xl-1">
                        <a class="nav-link nav-link-festivo fw-bold px-3 py-1 my-1 my-xl-0 d-inline-flex align-items-center gap-1 shadow-sm text-nowrap" href="<?= htmlspecialchars($baseUrl) ?>/#eventos-18-noviembre" data-nav-link="eventos-18-noviembre">
                            <i class="bi bi-calendar-event-fill text-warning me-1"></i> 18 de Noviembre
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/#explorar" data-nav-link="explorar" data-catalogo-todos>
                            <i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Lugares
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/?categoria=hoteles-alojamientos#explorar" data-nav-link="hoteles" data-categoria-slug="hoteles-alojamientos">
                            <i class="bi bi-building me-1" aria-hidden="true"></i> Hoteles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($baseUrl) ?>/#promociones" data-nav-link="promociones">
                            <i class="bi bi-tag-fill me-1" aria-hidden="true"></i> Promociones
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-inline-flex align-items-center gap-1" href="#" id="navbarDropServicios" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-nav-link="servicios">
                            <i class="bi bi-shield-plus text-teal-highlight"></i> Servicios
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark bg-teal-dark shadow-lg rounded-3 border-0 py-1" aria-labelledby="navbarDropServicios">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/?categoria=hospitales-salud#explorar" data-categoria-slug="hospitales-salud">
                                    <i class="bi bi-hospital text-danger fs-6"></i>
                                    <span class="fw-semibold">Salud y Hospitales</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/?categoria=policia-seguridad#explorar" data-categoria-slug="policia-seguridad">
                                    <i class="bi bi-shield-check text-warning fs-6"></i>
                                    <span class="fw-semibold">Seguridad y Policía</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/?categoria=bancos-cajeros#explorar" data-categoria-slug="bancos-cajeros">
                                    <i class="bi bi-cash-coin text-success fs-6"></i>
                                    <span class="fw-semibold">Banca y Cajeros ATM</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary opacity-25 my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/#transporte">
                                    <i class="bi bi-taxi-front-fill text-info fs-6"></i>
                                    <span class="fw-semibold">Transporte y Radiomóviles</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Dropdown Eventos y Cultura -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-inline-flex align-items-center gap-1" href="#" id="navbarDropEventos" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-nav-link="eventos">
                            <i class="bi bi-calendar-event text-teal-highlight"></i> Eventos
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark bg-teal-dark shadow-lg rounded-3 border-0 py-1" aria-labelledby="navbarDropEventos">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/#eventos-18-noviembre">
                                    <i class="bi bi-flag-fill text-warning fs-6"></i>
                                    <div>
                                        <span class="d-block fw-semibold">Eventos y Festividades</span>
                                        <small class="text-white-50 extra-small d-block">Afiches y fiestas cívicas</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/#agenda-festividades">
                                    <i class="bi bi-calendar3-range text-teal-highlight fs-6"></i>
                                    <div>
                                        <span class="d-block fw-semibold">Agenda</span>
                                        <small class="text-white-50 extra-small d-block">Calendario interactivo 2026</small>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-secondary opacity-25 my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= htmlspecialchars($baseUrl) ?>/#galeria">
                                    <i class="bi bi-camera-fill text-info fs-6"></i>
                                    <div>
                                        <span class="d-block fw-semibold">Galería</span>
                                        <small class="text-white-50 extra-small d-block">Postales y fotografías</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalItinerario" title="Ver mi itinerario guardado">
                        <i class="bi bi-heart-fill text-danger"></i>
                        <span class="d-none d-sm-inline">Mi Ruta</span>
                        <span class="badge bg-danger rounded-pill contador-itinerario d-none">0</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Dinámico -->
    <main class="flex-grow-1">
        <?= $content ?>
    </main>

    <!-- Pie de página -->
    <footer class="site-footer mt-5">
        <div class="container site-footer-inner">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-5">
                    <div class="mb-3">
                        <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png" class="site-footer-logo" alt="BeniTours">
                    </div>
                    <p class="text-white-50 small mb-3">
                        Plataforma digital dedicada a promover el turismo, la riqueza natural, la gastronomía típica y los comercios formales de la ciudad de Trinidad y la provincia Cercado en el departamento del Beni.
                    </p>
                    <span class="site-footer-location">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Trinidad &bull; Beni &bull; Bolivia
                    </span>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold text-white text-uppercase small mb-3">Accesos Rápidos</h6>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 site-footer-links">
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/"><i class="bi bi-grid-3x3-gap me-2"></i> Catálogo completo</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/#explorar"><i class="bi bi-compass me-2"></i> Atractivos turísticos</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/#promociones"><i class="bi bi-tag-fill me-2 text-warning"></i> Promociones y Ofertas</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/#unirse"><i class="bi bi-shop me-2"></i> Incorporar negocio</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/negocio/login"><i class="bi bi-box-arrow-in-right me-2 text-info"></i> Portal de comercios</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl) ?>/admin/login"><i class="bi bi-shield-lock me-2"></i> Acceso administrativo</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold text-white text-uppercase small mb-3">Información Local</h6>
                    <p class="small text-white-50 mb-1"><i class="bi bi-clock me-2 text-warning"></i>Zona horaria: America/La_Paz</p>
                    <p class="small text-white-50 mb-1"><i class="bi bi-currency-exchange me-2 text-warning"></i>Moneda: Bolivianos (Bs)</p>
                    <p class="small text-white-50 mb-0"><i class="bi bi-thermometer-sun me-2 text-warning"></i>Clima: Tropical cálido húmedo</p>
                    <nav class="site-footer-social mt-3" aria-label="Redes sociales de BeniTours">
                        <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram de BeniTours" title="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                        <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" aria-label="TikTok de BeniTours" title="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                        <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook de BeniTours" title="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    </nav>
                </div>
            </div>

            <div class="site-footer-bottom mt-4 pt-4 d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                <span>&copy; <?= date('Y') ?> BeniTours · Trinidad, Beni, Bolivia.</span>
                <span><i class="bi bi-heart-fill me-1"></i>Hecho para conectar visitantes y destinos.</span>
            </div>
        </div>
    </footer>

    <?php if (!empty($heroPantallaCompleta) && !empty($eventosDestacados)): ?>
        <!-- Eventos de la agenda del Beni: se muestra al ingresar o recargar la portada si hay eventos vigentes. -->
        <div class="modal fade" id="modalEventosBeni" tabindex="-1" aria-labelledby="modalEventosBeniTitulo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content modal-eventos-beni border-0 overflow-hidden">
                    <div class="modal-header border-0 px-4 py-3">
                        <div>
                            <span class="modal-eventos-eyebrow"><i class="bi bi-stars me-1"></i> Agenda del Beni</span>
                            <h2 class="modal-title h5 fw-bold mb-0 text-white" id="modalEventosBeniTitulo">Eventos destacados <?= date('Y') ?></h2>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar publicidad de eventos"></button>
                    </div>
                    <div class="modal-body p-0 bg-light">
                        <div id="carruselEventosBeni" class="carousel slide" data-bs-interval="false">
                            <div class="carousel-inner">
                                <?php foreach ($eventosDestacados as $indice => $evento): ?>
                                    <?php
                                        $archivo = $evento['imagen_archivo'];
                                        $esSubida = is_file(dirname(__DIR__, 3) . '/public/assets/img/eventos/' . $archivo);
                                        $rutaUrl = $esSubida 
                                            ? "{$baseUrl}/assets/img/eventos/" . rawurlencode($archivo) 
                                            : "{$baseUrl}/assets/img/" . rawurlencode($archivo);
                                        $rutaFisica = $esSubida
                                            ? dirname(__DIR__, 3) . '/public/assets/img/eventos/' . $archivo
                                            : dirname(__DIR__, 3) . '/public/assets/img/' . $archivo;
                                        $versionEvento = is_file($rutaFisica) ? filemtime($rutaFisica) : time();
                                    ?>
                                    <div class="carousel-item <?= $indice === 0 ? 'active' : '' ?>">
                                        <article class="evento-publicidad-card">
                                            <img src="<?= htmlspecialchars($rutaUrl) ?>?v=<?= $versionEvento ?>"
                                                class="evento-publicidad-imagen"
                                                alt="<?= htmlspecialchars($evento['titulo']) ?>">
                                            <div class="evento-publicidad-info">
                                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                                    <div>
                                                        <h3 class="h6 fw-bold text-dark mb-1"><?= htmlspecialchars($evento['titulo']) ?></h3>
                                                        <p class="small text-muted mb-0">
                                                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i><?= htmlspecialchars($evento['subtitulo_lugar'] ?? 'Trinidad · Beni') ?> &bull; 
                                                            <i class="bi bi-calendar-event me-1 text-teal"></i><?= htmlspecialchars($evento['detalle_fechas']) ?>
                                                        </p>
                                                    </div>
                                                    <?php if (!empty($evento['enlace_externo'])): ?>
                                                        <a href="<?= htmlspecialchars($evento['enlace_externo']) ?>" target="_blank" rel="noopener noreferrer" 
                                                           class="btn btn-sm btn-teal-action rounded-pill px-3 py-1 fw-bold mt-1">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i> Más info
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($eventosDestacados) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carruselEventosBeni" data-bs-slide="prev" aria-label="Evento anterior">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carruselEventosBeni" data-bs-slide="next" aria-label="Siguiente evento">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </button>
                                <div class="carousel-indicators position-relative mx-0 mb-0 pt-2 pb-3">
                                    <?php foreach ($eventosDestacados as $indice => $evento): ?>
                                        <button type="button" data-bs-target="#carruselEventosBeni" data-bs-slide-to="<?= $indice ?>" class="<?= $indice === 0 ? 'active' : '' ?>" <?= $indice === 0 ? 'aria-current="true"' : '' ?> aria-label="Ver <?= htmlspecialchars($evento['titulo']) ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-white justify-content-center py-3">
                        <button type="button" class="btn btn-outline-teal rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Explorar Beni</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>



    <!-- Bootstrap Bundle JS (Local) -->
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- Leaflet 1.9.4 JS (Local) -->
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/leaflet/leaflet.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/vendor/leaflet/leaflet.js') ?>"></script>

    <!-- Botón Flotante para Negocios (Esquina Inferior) -->
    <div class="dropdown position-fixed bottom-0 end-0 m-3 m-md-4 floating-negocios-container">
        <button class="btn btn-teal-action rounded-pill fw-bold px-3 py-2 shadow-lg text-white dropdown-toggle d-flex align-items-center gap-2 border border-white border-opacity-25 btn-floating-negocios" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-shop fs-5"></i>
            <span class="d-none d-sm-inline">Negocios</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 border-0 mb-2 py-2 dropdown-menu-floating">
            <li>
                <a class="dropdown-item py-2 px-3 fw-semibold text-dark" href="<?= htmlspecialchars($baseUrl) ?>/solicitar-incorporacion">
                    <i class="bi bi-plus-circle-fill text-teal me-2"></i> Publicar mi Negocio
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <a class="dropdown-item py-2 px-3 small text-secondary" href="<?= htmlspecialchars($baseUrl) ?>/negocio/login">
                    <i class="bi bi-box-arrow-in-right text-teal me-2"></i> Iniciar Sesión de Comercio
                </a>
            </li>
        </ul>
    </div>

    <!-- Botón Flotante para Mi Itinerario -->
    <button type="button" id="btnFlotanteItinerario" class="btn btn-teal-action rounded-pill shadow-lg position-fixed bottom-0 start-0 m-3 m-md-4 py-2 px-3 align-items-center gap-2 d-none floating-itinerario-btn" data-bs-toggle="modal" data-bs-target="#modalItinerario" title="Ver mi ruta planificada">
        <i class="bi bi-heart-fill text-danger fs-5"></i>
        <span class="fw-bold d-none d-sm-inline">Mi Itinerario</span>
        <span class="badge bg-white text-dark rounded-pill contador-itinerario">0</span>
    </button>

    <!-- Modal Mi Itinerario -->
    <div class="modal fade" id="modalItinerario" tabindex="-1" aria-labelledby="modalItinerarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden modal-itinerario-custom">
                <div class="modal-header border-0 modal-header-teal text-white py-3 px-3 px-sm-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-itinerario-icon-badge" title="Ruta Turística">
                            <i class="fa-solid fa-compass"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0 fs-6 fs-sm-5 text-white" id="modalItinerarioLabel">Mi Ruta Turística en el Beni</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3 p-sm-4 bg-light">
                    <div id="itinerarioVacioMensaje" class="text-center py-4 text-muted">
                        <div class="rounded-circle bg-white shadow-xs p-3 d-inline-flex align-items-center justify-content-center mb-2" style="width: 64px; height: 64px;">
                            <i class="bi bi-heart text-danger fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mt-2 mb-1">Tu itinerario está vacío</h6>
                        <p class="small mb-0 text-muted px-2">Explora el catálogo y presiona el corazón (<i class="bi bi-heart text-danger"></i>) en los atractivos, hoteles o restaurantes que deseas visitar para armar tu ruta del día.</p>
                    </div>
                    <div id="listaItinerarioContenido"></div>
                </div>
                <div class="modal-footer border-top bg-white d-flex align-items-center justify-content-between p-3 gap-2 flex-wrap" id="itinerarioFooterAcciones">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" id="btnVaciarItinerario">
                        <i class="bi bi-trash"></i> <span>Vaciar</span>
                    </button>
                    <button type="button" class="btn btn-teal-action btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0 justify-content-center" id="btnCompartirItinerarioWa">
                        <i class="bi bi-whatsapp fs-6"></i> <span>Compartir Ruta por WhatsApp</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Variables Globales y Scripts del Cliente -->
    <script>
        window.APP_CONFIG = {
            baseUrl: '<?= htmlspecialchars($baseUrl) ?>'
        };

        // En la portada rota los logotipos de BeniTours y la institución aliada.
        document.addEventListener('DOMContentLoaded', () => {
            const logo = document.querySelector('.navbar-logo[data-logo-alterno]');
            if (!logo) return;

            const logotipos = [
                { ruta: logo.dataset.logoPrincipal, esAlterno: false, alt: 'BeniTours' },
                { ruta: logo.dataset.logoAlterno, esAlterno: true, alt: 'BeniTours' },
                { ruta: logo.dataset.logoInstitucional, esAlterno: false, alt: 'Plan UABJB 19 Municipios del Beni' }
            ].filter(({ ruta }) => ruta);

            logotipos.forEach(({ ruta }) => {
                const imagen = new Image();
                imagen.src = ruta;
            });

            let indiceLogo = 0;
            window.setInterval(() => {
                logo.classList.add('navbar-logo--cambiando');
                window.setTimeout(() => {
                    indiceLogo = (indiceLogo + 1) % logotipos.length;
                    const siguiente = logotipos[indiceLogo];
                    logo.src = siguiente.ruta;
                    logo.alt = siguiente.alt;
                    logo.classList.toggle('navbar-logo--alterno', siguiente.esAlterno);
                    logo.classList.remove('navbar-logo--cambiando');
                }, 250);
            }, 10000);

        });

        // La publicidad de eventos se presenta cada vez que se abre o recarga la portada.
        document.addEventListener('DOMContentLoaded', () => {
            const modalEventos = document.getElementById('modalEventosBeni');
            if (!modalEventos || !window.bootstrap?.Modal) return;
            window.setTimeout(() => bootstrap.Modal.getOrCreateInstance(modalEventos).show(), 450);
        });
    </script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/shared/http.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/shared/http.js') ?>"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/shared/compresor-imagen.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/shared/compresor-imagen.js') ?>"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/publico/catalogo.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/publico/catalogo.js') ?>"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/publico/solicitudes.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/publico/solicitudes.js') ?>"></script>
    <script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/publico/itinerario.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/publico/itinerario.js') ?>"></script>

    <!-- AOS (Animate On Scroll) JS & Inicialización -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 700,
                    once: true,
                    offset: 100,
                    easing: 'ease-out-cubic'
                });
            }
        });
    </script>
</body>
</html>
