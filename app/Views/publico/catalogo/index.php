<!-- Hero Principal con Buscador Central y Rediseño Premium Glassmorphism -->
<section class="hero-trinidad text-white position-relative" id="inicio">
    <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="<?= htmlspecialchars($baseUrl) ?>/assets/img/hero-poster.webp" aria-hidden="true">
        <source src="<?= htmlspecialchars($baseUrl) ?>/assets/video/video.mp4" type="video/mp4">
    </video>
    <!-- Capa de Degradado Cinematográfico para Máxima Legibilidad -->
    <div class="hero-overlay-cinematic" aria-hidden="true"></div>

<div class="container hero-content py-5">
    <div class="row justify-content-center text-center">
        <div class="col-xl-9 col-lg-10">
            
            <!-- Badge: Visión departamental con sede en la capital -->
            <div class="d-inline-flex align-items-center gap-2 mb-3">
                <span class="badge hero-badge-glass fw-bold px-3 py-2 rounded-pill text-uppercase shadow-sm">
                    <i class="bi bi-compass-fill text-warning me-1"></i> Portal Turístico del Beni · Trinidad
                </span>
            </div>

            <!-- Título Principal con Relieve y Contraste Óptimo -->
            <h1 class="display-4 fw-extrabold mb-3 text-white hero-title-cinematic">
                Descubre los mejores destinos del <span class="text-teal-gradient">Beni</span>
            </h1>

            <!-- Descripción clara y legible -->
            <p class="lead hero-lead-cinematic hero-lead-bounded mb-4 mx-auto">
                Explora atractivos naturales, gastronomía amazónica, hospedaje y comercios locales en Trinidad y las principales rutas del departamento.
            </p>

            <!-- Barra de Búsqueda Principal Premium Glassmorphism -->
            <div class="search-box-hero-premium hero-search-box-bounded mx-auto p-2 rounded-pill mb-3">
                <div class="d-flex align-items-center">
                    <span class="ps-3 text-teal-dark"><i class="bi bi-search fs-5"></i></span>
                    <input type="text" id="inputBuscarHero" class="form-control border-0 shadow-none px-3 py-2 bg-transparent hero-input-text" 
                           placeholder="¿Qué buscas en Trinidad o el Beni? (ej. Laguna Suárez, majadito, hotel...)">
                    <button id="btnLimpiarFiltros" class="btn btn-light rounded-pill btn-sm me-1 text-muted d-none" title="Limpiar búsqueda">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <button type="button" class="btn btn-hero-buscar rounded-pill px-4 py-2 fw-bold text-white shadow-sm d-none d-sm-inline-flex align-items-center gap-1" onclick="document.getElementById('inputBuscarHero').focus()">
                        <span>Buscar</span>
                        <i class="bi bi-arrow-right-short fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Micro-filtros Rápidos Interactivas (Píldoras) -->
            <div class="hero-quick-pills d-flex flex-wrap justify-content-center gap-2 mb-4">
                <button type="button" class="btn hero-pill-btn" onclick="aplicarFiltroRapidoHero('Laguna Suárez')">
                    <i class="fa-solid fa-water text-info me-1"></i> Laguna Suárez
                </button>
                <button type="button" class="btn hero-pill-btn" onclick="aplicarFiltroRapidoHero('Gastronomía')">
                    <i class="fa-solid fa-utensils text-warning me-1"></i> Dónde Comer
                </button>
                <button type="button" class="btn hero-pill-btn" onclick="aplicarFiltroRapidoHero('Hotel')">
                    <i class="fa-solid fa-hotel text-primary me-1"></i> Hoteles
                </button>
                <button type="button" class="btn hero-pill-btn" onclick="aplicarFiltroRapidoHero('Puerto Ballivián')">
                    <i class="fa-solid fa-ship text-success me-1"></i> Puerto Ballivián
                </button>
            </div>

            <!-- 3 Insignias de Confianza con "100% del Beni" -->
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-4 pt-2">
                <div class="hero-stat-badge">
                    <i class="bi bi-stars text-warning fs-5"></i>
                    <span class="small fw-semibold">+50 Destinos Únicos</span>
                </div>
                <div class="hero-stat-divider d-none d-sm-block"></div>
                <div class="hero-stat-badge">
                    <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                    <span class="small fw-semibold">Mapa GPS en Vivo</span>
                </div>
                <div class="hero-stat-divider d-none d-sm-block"></div>
                <div class="hero-stat-badge1">
                    <i class="fa-solid fa-heart text-danger fs-5"></i>
                    <span class="small fw-bold">100% del Beni</span>
                </div>
            </div>

        </div>
    </div>
</div>
</section>

<!-- DIVISOR ORGÁNICO: OLA AMAZÓNICA Y BRÚJULA FLOTANTE -->
<div class="section-divider-wrapper position-relative">
    <!-- Ola Fluida SVG -->
    <div class="wave-divider">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 90" preserveAspectRatio="none">
            <path fill="#f7faf9" fill-opacity="1" d="M0,32L48,42.7C96,53,192,75,288,74.7C384,75,480,53,576,42.7C672,32,768,32,864,42.7C960,53,1056,75,1152,69.3C1248,64,1344,32,1392,16L1440,0L1440,90L1392,90C1344,90,1248,90,1152,90C1056,90,960,90,864,90C768,90,672,90,576,90C480,90,384,90,288,90C192,90,96,90,48,90L0,90Z"></path>
        </svg>
    </div>
</div>

<!-- Catálogo y Filtros de Categorías Multicolor -->
<section class="container py-4" id="explorar">
    <!-- Encabezado Centrado -->
    <div class="text-center mb-3">
        <h2 class="fw-bold text-dark mb-1">Explorar el Beni y sus Destinos</h2>
        <p class="text-muted small mb-0">Selecciona tu municipio, rubro favorito o explora el mapa interactivo</p>
    </div>

    <!-- Filtro de Municipios del Beni -->
    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
        <div class="municipio-filter align-items-center rounded-pill px-3 py-1">
            <span class="municipio-filter-icon"><i class="bi bi-geo-alt-fill"></i></span>
            <span class="municipio-filter-label small fw-semibold me-2 d-none d-sm-inline">Municipio:</span>
            <select id="selectMunicipio" class="form-select form-select-sm border-0 shadow-none py-1 ps-1 pe-4 bg-transparent fw-bold" aria-label="Seleccionar municipio">
                <option value="">Todo el Departamento del Beni</option>
                <?php foreach ($municipios as $m): ?>
                    <option value="<?= (int)$m['id_municipio'] ?>"
                            data-lat="<?= htmlspecialchars((string)($m['latitud_defecto'] ?? '')) ?>"
                            data-lng="<?= htmlspecialchars((string)($m['longitud_defecto'] ?? '')) ?>"
                            <?= ($municipioSeleccionado === (int)$m['id_municipio']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['provincia']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Píldoras de Categoría + Botón GPS Cerca de Mí -->
    <div class="category-filter-container d-flex flex-wrap justify-content-center align-items-center gap-2 gap-md-3 mb-4 py-2">
        <!-- 1. Botón Todos -->
        <button type="button" class="chip-filter-icon cat-color-all<?= $categoriaSeleccionada === null ? ' active' : '' ?>" data-categoria="" title="Todos los lugares" aria-label="Todos los lugares">
            <i class="bi bi-grid-fill"></i>
        </button>

        <!-- 2. BOTÓN GPS: Cerca de Mí (Radio de 1 km) -->
        <button type="button" id="btnCercaDeMi" class="chip-filter-icon cat-color-geo" title="Lugares a menos de 1 km de mi ubicación" aria-label="Lugares a menos de 1 km">
            <i class="bi bi-geo-alt-fill"></i>
        </button>

        <!-- 3. Categorías Dinámicas de la BD -->
        <?php
        $categoriaColorMap = [
            'gastronomia'                  => 'cat-color-gastro', // Naranja Ámbar
            'hoteles-alojamientos'         => 'cat-color-hotel',  // Azul Cielo
            'vida-nocturna'                => 'cat-color-night',  // Violeta Neón
            'atractivos-turisticos'        => 'cat-color-nature', // Verde Esmeralda
            'balnearios-recreacion'        => 'cat-color-water',  // Turquesa / Cian
            'museos-y-patrimonio-cultural' => 'cat-color-gold',   // Dorado / Ámbar
            'comercio-artesanias'          => 'cat-color-shop',   // Rosa Coral
        ];
        foreach ($categorias as $c):
            $catSlug = $c['slug'] ?? '';
            $currentClass = $categoriaColorMap[$catSlug] ?? 'cat-color-nature';
            if (!isset($categoriaColorMap[$catSlug])) {
                $nom = mb_strtolower($c['nombre'] ?? '', 'UTF-8');
                if (str_contains($nom, 'museo') || str_contains($nom, 'cultur')) $currentClass = 'cat-color-gold';
                elseif (str_contains($nom, 'bar') || str_contains($nom, 'nocturn')) $currentClass = 'cat-color-night';
                elseif (str_contains($nom, 'gastro') || str_contains($nom, 'restauran')) $currentClass = 'cat-color-gastro';
                elseif (str_contains($nom, 'hotel') || str_contains($nom, 'alojam')) $currentClass = 'cat-color-hotel';
                elseif (str_contains($nom, 'balneari') || str_contains($nom, 'agua')) $currentClass = 'cat-color-water';
                elseif (str_contains($nom, 'comercio') || str_contains($nom, 'tienda')) $currentClass = 'cat-color-shop';
            }
        ?>
            <?php $iconoChip = str_starts_with($c['icono'] ?? '', 'fa-') ? htmlspecialchars($c['icono']) : 'bi ' . htmlspecialchars($c['icono'] ?? 'bi-tag'); ?>
            <button type="button" class="chip-filter-icon <?= $currentClass ?><?= $categoriaSeleccionada === (int)$c['id_categoria'] ? ' active' : '' ?>"
                    data-categoria="<?= $c['id_categoria'] ?>"
                    title="<?= htmlspecialchars($c['nombre']) ?>"
                    aria-label="<?= htmlspecialchars($c['nombre']) ?>">
                <i class="<?= $iconoChip ?>"></i>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Alerta de estado de geolocalización (Flotante y compacta) -->
    <div id="alertaGeolocalizacion" class="alert alert-info alert-dismissible fade d-none rounded-4 small mb-4 border-0 shadow-sm mx-auto text-center hero-alert-bounded" role="alert">
        <span id="alertaGeoTexto"></span>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
    </div>

    <!-- Barra de Herramientas: Contador y Selector de Modo de Vista -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 px-1">
        <div class="d-flex align-items-center gap-2">
            <span id="contadorResultados" class="badge bg-teal-subtle text-teal border border-teal-subtle px-3 py-2 rounded-pill fw-semibold small">
                <?= count($lugares) ?> lugares encontrados
            </span>
        </div>
        <!-- Selector de Modo de Vista (Cuadrícula / Mapa) -->
        <div class="btn-group p-1 bg-white border rounded-pill shadow-sm view-switcher-group" role="group" aria-label="Modo de visualización">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold active" id="btnVistaGrid" title="Ver en cuadrícula de tarjetas">
                <i class="bi bi-grid-fill me-1"></i> Cuadrícula
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-secondary" id="btnVistaMapa" title="Ver mapa interactivo de Trinidad">
                <i class="bi bi-map-fill me-1"></i> Mapa Trinidad
            </button>
        </div>
    </div>

    <!-- Contenedor del Mapa Interactivo de Trinidad -->
    <div id="mapaCatalogoWrapper" class="d-none mb-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative map-catalog-card">
            <!-- Selector de Rutas Turísticas Inteligentes (Algoritmo de Popularidad y Proximidad) -->
            <div class="bg-light border-bottom p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge hero-badge-teal rounded-pill px-3 py-1 fw-bold text-uppercase extra-small">
                            <i class="bi bi-compass-fill me-1"></i> Rutas Recomendadas
                        </span>
                        <span class="small fw-bold text-dark">Circuitos Inteligentes de Trinidad:</span>
                    </div>
                    <div class="extra-small text-muted">
                        <i class="bi bi-stars text-warning me-1"></i> Basado en los destinos más votados por la comunidad
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2" id="selectorRutasTuristicas" role="tablist">
                    <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-semibold btn-filtro-ruta active shadow-xs" data-ruta-id="todos">
                        <i class="bi bi-geo-alt-fill me-1 text-teal"></i> Todos los Lugares
                    </button>
                    <?php if (!empty($rutasRecomendadas)): ?>
                        <?php foreach ($rutasRecomendadas as $ruta): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-white border rounded-pill px-3 py-1 fw-semibold btn-filtro-ruta shadow-xs"
                                    data-ruta-id="<?= htmlspecialchars($ruta['id']) ?>"
                                    title="<?= htmlspecialchars($ruta['subtitulo']) ?>">
                                <i class="bi <?= htmlspecialchars($ruta['icono']) ?> me-1" style="color: <?= htmlspecialchars($ruta['color']) ?>;"></i>
                                <?= htmlspecialchars($ruta['titulo']) ?>
                                <span class="badge bg-light text-dark rounded-pill ms-1 extra-small border"><?= count($ruta['paradas']) ?> paradas</span>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Panel informativo de la ruta activa -->
                <div id="panelInfoRutaActiva" class="mt-3 p-3 rounded-3 bg-white border border-teal-subtle shadow-xs d-none">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span id="rutaInfoBadge" class="badge rounded-pill px-2 py-1 extra-small fw-bold text-white bg-teal">Ruta Activa</span>
                                <h6 id="rutaInfoTitulo" class="fw-bold text-dark mb-0">Ruta Turística</h6>
                            </div>
                            <p id="rutaInfoSubtitulo" class="extra-small text-muted mb-0">Recorrido continuo optimizado</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2 extra-small text-dark">
                                <span><i class="bi bi-flag-fill text-success"></i> <strong id="rutaInfoParadas">0</strong> paradas</span>
                                <span>·</span>
                                <span><i class="bi bi-signpost-split text-info"></i> <strong id="rutaInfoDistancia">0 km</strong></span>
                                <span>·</span>
                                <span><i class="bi bi-clock-history text-warning"></i> <strong id="rutaInfoTiempo">0 min</strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a id="rutaBtnGoogleMaps" href="#" target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-sm btn-outline-teal rounded-pill px-3 py-1 extra-small fw-semibold shadow-xs">
                                    <i class="bi bi-google me-1"></i> Abrir en Google Maps
                                </a>
                                <button type="button" id="rutaBtnVolverGeneral" class="btn btn-sm btn-light border rounded-pill px-2 py-1 extra-small" title="Ver todos los lugares">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de control del mapa -->
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-teal rounded-pill px-3 py-1 fw-semibold small">
                        <i class="bi bi-geo-alt-fill me-1"></i> Mapa General de Trinidad, Beni
                    </span>
                    <span id="mapaConteoLugares" class="badge bg-light text-secondary border rounded-pill px-2 py-1 extra-small"></span>
                    <span id="mapaBadgeRadio" class="badge bg-danger text-white rounded-pill px-2 py-1 extra-small d-none shadow-sm">
                        <i class="bi bi-broadcast-pin me-1"></i> Radio: 1 km
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnMapaCentrarTrinidad" class="btn btn-sm btn-outline-teal rounded-pill px-3 py-1 shadow-none" title="Centrar en el centro de Trinidad">
                        <i class="bi bi-crosshair me-1"></i> Centrar Trinidad
                    </button>
                    <button type="button" id="btnMapaMiUbicacion" class="btn btn-sm btn-teal-action rounded-pill px-3 py-1 shadow-none" title="Ver mi ubicación GPS y lugares en 1 km">
                        <i class="bi bi-cursor-fill me-1"></i> Mi Ubicación (1 km)
                    </button>
                </div>
            </div>
            <!-- Canvas del mapa Leaflet -->
            <div id="mapaCatalogo" class="catalogo-mapa-canvas z-index-1"></div>
            <!-- Leyenda rápida inferior -->
            <div class="card-footer bg-white py-2 px-3 border-top small text-muted d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="fw-semibold text-dark"><i class="bi bi-palette-fill me-1 text-success"></i> Categorías:</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-nat"></span> Atractivos y Naturaleza</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-gas"></span> Gastronomía</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-hot"></span> Hoteles</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-noc"></span> Vida Nocturna</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-mus"></span> Museos y Cultura</span>
                    <span class="d-inline-flex align-items-center gap-1"><span class="badge-dot badge-dot-bal"></span> Balnearios</span>
                </div>
                <div class="extra-small text-secondary">
                    <i class="bi bi-info-circle me-1 text-warning"></i> Clic en cualquier marcador para ver ficha o navegar con GPS
                </div>
            </div>
        </div>
    </div>

    <!-- Cuadrícula de Fichas de Lugares -->
    <div class="row g-4" id="gridLugaresCatalogo">
        <?php if (!empty($lugares)): ?>
            <?php foreach ($lugares as $indiceLugar => $l): ?>
                <?php
                    $icono = ($l['categoria_icono'] ?? '') ?: 'bi-geo-alt';
                    $iconoClass = str_starts_with($icono, 'fa-') ? htmlspecialchars($icono) : 'bi ' . htmlspecialchars($icono);
                    $descripcion = $l['descripcion'] ?? '';
                    if (mb_strlen($descripcion, 'UTF-8') > 140) {
                        $descripcion = mb_substr($descripcion, 0, 140, 'UTF-8') . '...';
                    }
                    $estadoHorario = \App\Services\HorarioHelper::evaluarEstado(
                        $l['horario_atencion'] ?? null,
                        $l['estado_operativo'] ?? null,
                        $l['aviso_temporal_motivo'] ?? null,
                        $l['aviso_temporal_reapertura'] ?? null
                    );
                    $urlFicha = $baseUrl . '/catalogo/detalle?slug=' . urlencode($l['slug']);
                ?>
                <div class="col-md-6 col-lg-4 item-lugar tarjeta-lugar-col<?= ($indiceLugar >= 6) ? ' d-none tarjeta-oculta-fila' : '' ?>"
                     data-id="<?= (int)$l['id_lugar'] ?>"
                     data-nombre="<?= htmlspecialchars(mb_strtolower($l['nombre'], 'UTF-8')) ?>"
                     data-categoria="<?= (int)$l['id_categoria'] ?>"
                     data-municipio="<?= (int)($l['id_municipio'] ?? 1) ?>"
                     data-coordenadas="<?= htmlspecialchars($l['coordenadas_gps'] ?? '') ?>">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative place-card place-card-editorial">
                        
                        <!-- Contenedor de Imagen Inmersiva -->
                        <div class="place-card-media-wrapper position-relative">
                            <?php if (!empty($l['imagen'])): ?>
                                <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($l['imagen']) ?>"
                                     alt="<?= htmlspecialchars($l['nombre']) ?>"
                                     class="w-100 h-100 object-fit-cover place-card-img"
                                     loading="lazy"
                                     decoding="async">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white detalle-hero-fallback bg-teal-gradient">
                                    <div class="place-icon-bubble shadow-sm mb-2">
                                        <i class="<?= $iconoClass ?> fs-2 text-teal"></i>
                                    </div>
                                    <span class="small text-white-50"><?= htmlspecialchars($l['categoria'] ?? 'Lugar de Interés') ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Badges Superiores Flotantes: Categoría y Municipio -->
                            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1 z-index-4">
                                <span class="badge bg-dark bg-opacity-75 text-white rounded-pill fw-semibold shadow-sm extra-small">
                                    <i class="<?= $iconoClass ?> me-1 text-warning"></i> <?= htmlspecialchars($l['categoria'] ?? '') ?>
                                </span>
                                <?php if (!empty($l['municipio'])): ?>
                                    <span class="badge bg-dark bg-opacity-60 text-white-50 rounded-pill fw-normal shadow-sm extra-small">
                                        <i class="bi bi-geo-alt text-warning"></i> <?= htmlspecialchars($l['municipio']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Badge de Promoción si existe -->
                            <?php if (!empty($l['promo_activa_texto'])): ?>
                                <span class="badge bg-danger text-white position-absolute top-0 end-0 m-3 rounded-pill fw-bold shadow-sm extra-small z-index-5">
                                    <i class="bi bi-tag-fill me-1"></i> Promo
                                </span>
                            <?php endif; ?>

                            <!-- Badge de Distancia GPS Dinámico -->
                            <div class="badge-distancia-container distance-badge-position position-absolute end-0 top-0 mt-5 me-3 z-index-4"></div>
                        </div>

                        <!-- Panel Flotante Curvado Estilo Editorial (Inspirado en la Referencia) -->
                        <div class="place-card-panel-editorial position-relative p-3 p-sm-4 d-flex flex-column justify-content-between">
                            <!-- Título del Lugar (con enlace que se subraya) -->
                            <div class="place-card-header-info mb-2">
                                <h5 class="fw-bold text-dark mb-1 place-card-title-link">
                                    <a href="<?= htmlspecialchars($urlFicha) ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= htmlspecialchars($l['nombre']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted extra-small mb-0 line-clamp-2 place-card-desc-preview">
                                    <?= htmlspecialchars($descripcion) ?>
                                </p>
                            </div>

                            <!-- Barra de Acciones Revelables en Hover (❤️ Favorito, ℹ️ Info/Horario, Redes, ➔ Botón Flecha) -->
                            <div class="place-card-hover-actions d-flex align-items-center justify-content-between pt-2">
                                <div class="d-flex align-items-center gap-2 position-relative z-index-6">
                                    <!-- Botón de Favorito / Guardar en itinerario -->
                                    <button type="button" 
                                            class="btn btn-white bg-white rounded-circle shadow-xs place-btn-icon" 
                                            data-btn-favorito
                                            data-id="<?= (int)$l['id_lugar'] ?>"
                                            data-nombre="<?= htmlspecialchars($l['nombre']) ?>"
                                            data-slug="<?= htmlspecialchars($l['slug']) ?>"
                                            data-categoria="<?= htmlspecialchars($l['categoria'] ?? '') ?>"
                                            data-imagen="<?= htmlspecialchars($l['imagen'] ?? '') ?>"
                                            data-municipio="<?= htmlspecialchars($l['municipio'] ?? '') ?>"
                                            data-url="<?= htmlspecialchars($urlFicha) ?>"
                                            title="Guardar en mi itinerario"
                                            aria-label="Guardar en mi itinerario">
                                        <i class="bi bi-heart text-danger fs-6"></i>
                                    </button>

                                    <!-- Estado de Horario / Info -->
                                    <?php if ($estadoHorario): ?>
                                        <span class="badge <?= $estadoHorario['badge'] ?> rounded-pill extra-small shadow-2xs" title="<?= htmlspecialchars($l['horario_atencion'] ?? '') ?>">
                                            <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($estadoHorario['texto']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border rounded-pill extra-small" title="Información disponible">
                                            <i class="bi bi-info-circle text-teal"></i> Info
                                        </span>
                                    <?php endif; ?>

                                    <!-- Redes Sociales Compactas si tiene -->
                                    <?php if (!empty($l['tiktok_url']) || !empty($l['facebook_url']) || !empty($l['instagram_url'])): ?>
                                        <div class="d-flex align-items-center gap-1 ms-1">
                                            <?php if (!empty($l['tiktok_url'])): ?>
                                                <a href="<?= htmlspecialchars($l['tiktok_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                   class="btn-social-icon btn-social-icon-sm btn-social-icon--tiktok" 
                                                   title="Ver en TikTok" onclick="event.stopPropagation();">
                                                    <i class="bi bi-tiktok"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($l['facebook_url'])): ?>
                                                <a href="<?= htmlspecialchars($l['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                   class="btn-social-icon btn-social-icon-sm btn-social-icon--facebook" 
                                                   title="Ver en Facebook" onclick="event.stopPropagation();">
                                                    <i class="bi bi-facebook"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($l['instagram_url'])): ?>
                                                <a href="<?= htmlspecialchars($l['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                   class="btn-social-icon btn-social-icon-sm btn-social-icon--instagram" 
                                                   title="Ver en Instagram" onclick="event.stopPropagation();">
                                                    <i class="bi bi-instagram"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Botón Circular Verde con Flecha (Inspirado en la Referencia) -->
                                <a href="<?= htmlspecialchars($urlFicha) ?>" 
                                   class="btn-place-arrow-circle d-flex align-items-center justify-content-center text-white shadow-sm position-relative z-index-6" 
                                   title="Ver todos los detalles">
                                    <i class="bi bi-arrow-right fs-5"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-compass display-4 d-block mb-2"></i>
                No hay lugares disponibles en este momento.
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón Centrado de Ver Más / Ver Menos Lugares (2 filas = 6 tarjetas) -->
    <div id="contenedorVerMasLugares" class="text-center mt-4 mb-3<?= count($lugares) <= 6 ? ' d-none' : '' ?>">
        <button type="button" id="btnVerMasLugares" class="btn btn-outline-teal btn-ver-mas-catalogo rounded-pill px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-chevron-down me-2" id="iconoVerMasLugares"></i>
            <span id="textoVerMasLugares">Ver más lugares (<?= max(0, count($lugares) - 6) ?> restantes)</span>
        </button>
    </div>
    <!-- Datos iniciales de lugares en formato JSON para el mapa interactivo -->
    <script id="lugaresInicialesJson" type="application/json">
        <?= json_encode($lugares, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <!-- Datos de Rutas Inteligentes en formato JSON -->
    <script id="rutasInicialesJson" type="application/json">
        <?= json_encode($rutasRecomendadas ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>
    </script>
</section>

<!-- Sección Carrusel de Promociones y Ofertas Imperdibles -->
<section class="seccion-promociones py-5 border-bottom position-relative" id="promociones">

    <!-- Video de Fondo en Promociones -->
    <video class="promo-bg-video" autoplay muted loop playsinline preload="metadata" poster="<?= htmlspecialchars($baseUrl) ?>/assets/img/hero-poster.webp" aria-hidden="true">
        <source src="<?= htmlspecialchars($baseUrl) ?>/assets/video/video.mp4" type="video/mp4">
    </video>
    <div class="promo-bg-overlay" aria-hidden="true"></div>

    <div class="container py-2">
        <div class="text-center mb-4">
            <div>
                <span class="badge text-white px-3 py-2 rounded-pill fw-bold text-uppercase small mb-2 shadow-sm hero-badge-glass">
                    <i class="bi bi-fire me-1 text-teal-highlight"></i> Ahorra en tu viaje
                </span>
                <h2 class="fw-bold text-white mb-1 text-shadow-hero">Promociones y Ofertas Activas</h2>
                <p class="text-white-50 small mb-0">Descubre descuentos especiales y combos exclusivos de nuestros comercios aliados</p>
            </div>
        </div>

        <?php if (!empty($promocionesVigentes)): ?>
            <div id="carruselPromociones" class="promo-carousel" role="region" aria-roledescription="carrusel" aria-label="Promociones activas" data-promo-carousel>
                <?php if (count($promocionesVigentes) > 1): ?>
                    <div class="promo-carousel-controls" aria-label="Controles del carrusel">
                        <button class="btn btn-outline-dark rounded-circle shadow-sm promo-carousel-control promo-carousel-control-prev" type="button" data-promo-direction="prev" aria-label="Promoción anterior">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-outline-dark rounded-circle shadow-sm promo-carousel-control promo-carousel-control-next" type="button" data-promo-direction="next" aria-label="Promoción siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="promo-carousel-stage">
                    <?php foreach ($promocionesVigentes as $indicePromo => $pr): ?>
                                    <?php
                                        $fotoPromo = !empty($pr['imagen'])
                                            ? "{$baseUrl}/imagen?f=" . urlencode($pr['imagen'])
                                            : null;
                                        $urlFichaPromo = "{$baseUrl}/catalogo/detalle?slug=" . urlencode($pr['lugar_slug']);
                                        $waPromoRaw = preg_replace('/\D/', '', $pr['whatsapp_contacto'] ?? '');
                                        if (strlen($waPromoRaw) === 8) {
                                            $waPromoRaw = '591' . $waPromoRaw;
                                        }
                                        $textoWaPromo = rawurlencode("Hola, vi su promoción '{$pr['titulo']}'" . (!empty($pr['descuento_texto']) ? " ({$pr['descuento_texto']})" : "") . " en BeniTours y quisiera aprovecharla en {$pr['lugar_nombre']}.");
                                        $fechaFinFmt = !empty($pr['fecha_fin']) ? date('d/m/Y', strtotime($pr['fecha_fin'])) : '';

                                        $promoPayload = json_encode([
                                            'id_promocion'       => (int)$pr['id_promocion'],
                                            'id_lugar'           => (int)$pr['id_lugar'],
                                            'titulo'             => $pr['titulo'],
                                            'descripcion'        => $pr['descripcion'],
                                            'descuento_texto'    => $pr['descuento_texto'] ?? '',
                                            'imagen'             => $fotoPromo,
                                            'lugar_nombre'       => $pr['lugar_nombre'],
                                            'lugar_slug'         => $pr['lugar_slug'],
                                            'url_ficha'          => $urlFichaPromo,
                                            'lugar_direccion'    => $pr['lugar_direccion'] ?? '',
                                            'lugar_referencia'   => $pr['lugar_referencia'] ?? '',
                                            'municipio_nombre'   => $pr['municipio_nombre'] ?? 'Trinidad',
                                            'categoria_nombre'   => $pr['categoria_nombre'] ?? '',
                                            'categoria_icono'    => $pr['categoria_icono'] ?? 'bi-shop',
                                            'latitud'            => $pr['latitud'] ?? null,
                                            'longitud'           => $pr['longitud'] ?? null,
                                            'tiktok_url'         => $pr['tiktok_url'] ?? null,
                                            'facebook_url'       => $pr['facebook_url'] ?? null,
                                            'instagram_url'      => $pr['instagram_url'] ?? null,
                                            'fecha_fin_fmt'      => $fechaFinFmt,
                                            'wa_contacto'        => $waPromoRaw,
                                            'wa_texto'           => $textoWaPromo
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                                    ?>
                                    <article class="promo-carousel-card" 
                                             data-promo-card 
                                             data-promo-data='<?= htmlspecialchars($promoPayload, ENT_QUOTES, 'UTF-8') ?>'
                                             aria-roledescription="diapositiva" 
                                             aria-label="Promoción <?= $indicePromo + 1 ?> de <?= count($promocionesVigentes) ?>">
                                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-promocion position-relative">
                                            <!-- Cabecera de la tarjeta con imagen completa o diseño distintivo -->
                                            <?php if ($fotoPromo): ?>
                                                <div class="position-relative promo-card-img-container overflow-hidden promo-card-banner" 
                                                     data-promo-trigger="modal"
                                                     title="Ver detalles completos de la promoción">
                                                    <!-- Fondo desenfocado con los tonos del afiche -->
                                                    <div class="promo-card-blur-bg" 
                                                         style="background-image: url('<?= htmlspecialchars($fotoPromo) ?>');"></div>
                                                    <!-- Imagen principal ajustada completa sin recortes -->
                                                    <img src="<?= htmlspecialchars($fotoPromo) ?>"
                                                         alt="<?= htmlspecialchars($pr['lugar_nombre']) ?>"
                                                         class="position-relative w-100 h-100 object-fit-contain p-1 z-index-2"
                                                         loading="lazy"
                                                         decoding="async">
                                                    <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 rounded-pill extra-small shadow-sm z-index-3">
                                                        <i class="bi <?= htmlspecialchars($pr['categoria_icono'] ?? 'bi-shop') ?> me-1"></i> <?= htmlspecialchars($pr['categoria_nombre'] ?? '') ?>
                                                    </span>
                                                    <?php if (!empty($pr['descuento_texto'])): ?>
                                                        <span class="badge bg-danger text-white position-absolute top-0 end-0 m-3 rounded-pill fw-bold shadow promo-discount-tag fs-6 z-index-3">
                                                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($pr['descuento_texto']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="p-4 text-white position-relative bg-success bg-gradient cursor-pointer h-min-140" data-promo-trigger="modal">
                                                    <span class="badge bg-dark bg-opacity-50 text-white rounded-pill extra-small">
                                                        <i class="bi <?= htmlspecialchars($pr['categoria_icono'] ?? 'bi-shop') ?> me-1"></i> <?= htmlspecialchars($pr['categoria_nombre'] ?? '') ?>
                                                    </span>
                                                    <?php if (!empty($pr['descuento_texto'])): ?>
                                                        <span class="badge bg-danger text-white position-absolute top-0 end-0 m-3 rounded-pill fw-bold shadow promo-discount-tag fs-6">
                                                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($pr['descuento_texto']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <div class="mt-2">
                                                        <h5 class="fw-bold mb-0 text-white"><?= htmlspecialchars($pr['lugar_nombre']) ?></h5>
                                                        <small class="text-white-50"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($pr['municipio_nombre'] ?? 'Trinidad') ?></small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Cuerpo de la tarjeta -->
                                            <div class="card-body p-3 p-sm-4 d-flex flex-column">
                                                <?php if ($fotoPromo): ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="fw-bold text-dark mb-0 text-truncate" title="<?= htmlspecialchars($pr['lugar_nombre']) ?>">
                                                            <?= htmlspecialchars($pr['lugar_nombre']) ?>
                                                        </h6>
                                                        <small class="text-muted extra-small"><i class="bi bi-geo-alt text-warning"></i> <?= htmlspecialchars($pr['municipio_nombre'] ?? 'Trinidad') ?></small>
                                                    </div>
                                                <?php endif; ?>

                                                <h5 class="fw-bold text-teal mb-2 text-truncate" title="<?= htmlspecialchars($pr['titulo']) ?>">
                                                    <?= htmlspecialchars($pr['titulo']) ?>
                                                </h5>
                                                <p class="text-secondary small flex-grow-1 mb-3 line-clamp-3">
                                                    <?= htmlspecialchars($pr['descripcion']) ?>
                                                </p>

                                                <?php if ($fechaFinFmt): ?>
                                                    <div class="extra-small text-muted mb-3 d-flex align-items-center gap-1">
                                                        <i class="bi bi-clock-history text-danger"></i>
                                                        <span>Válido hasta el <strong><?= $fechaFinFmt ?></strong></span>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Acciones -->
                                                <div class="d-flex align-items-center gap-2 pt-2 border-top mt-auto">
                                                    <button type="button" 
                                                            class="btn btn-outline-teal btn-sm rounded-pill px-3 fw-semibold flex-grow-1 d-flex align-items-center justify-content-center gap-1"
                                                            data-promo-trigger="modal">
                                                        <i class="bi bi-eye"></i>
                                                        <span>Ver</span>
                                                    </button>
                                                    <?php if (!empty($waPromoRaw)): ?>
                                                        <a href="https://wa.me/<?= $waPromoRaw ?>?text=<?= $textoWaPromo ?>" 
                                                            target="_blank" rel="noopener noreferrer"
                                                            onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$pr['id_lugar'] ?>, 'WHATSAPP')"
                                                            class="btn btn-teal-action btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-sm">
                                                            <i class="bi bi-whatsapp"></i>
                                                            <span>Aprovechar</span>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($pr['tiktok_url']) || !empty($pr['facebook_url']) || !empty($pr['instagram_url'])): ?>
                                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                            <?php if (!empty($pr['tiktok_url'])): ?>
                                                                <a href="<?= htmlspecialchars($pr['tiktok_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                                   class="btn-social-icon btn-social-icon-md btn-social-icon--tiktok shadow-xs" 
                                                                   title="Ver video en TikTok" onclick="event.stopPropagation();">
                                                                    <i class="bi bi-tiktok"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($pr['facebook_url'])): ?>
                                                                <a href="<?= htmlspecialchars($pr['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                                   class="btn-social-icon btn-social-icon-md btn-social-icon--facebook shadow-xs" 
                                                                   title="Ver en Facebook" onclick="event.stopPropagation();">
                                                                    <i class="bi bi-facebook"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($pr['instagram_url'])): ?>
                                                                <a href="<?= htmlspecialchars($pr['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                                                   class="btn-social-icon btn-social-icon-md btn-social-icon--instagram shadow-xs" 
                                                                   title="Ver en Instagram" onclick="event.stopPropagation();">
                                                                    <i class="bi bi-instagram"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($promocionesVigentes) > 1): ?>
                    <div class="promo-carousel-indicators" aria-label="Elegir promoción">
                        <?php foreach ($promocionesVigentes as $indicePromo => $promocion): ?>
                            <button type="button" data-promo-to="<?= $indicePromo ?>" <?= $indicePromo === 0 ? 'class="is-active" aria-current="true"' : '' ?> aria-label="Mostrar promoción <?= $indicePromo + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="p-4 rounded-4 bg-white border text-center mx-auto shadow-sm max-w-600">
                <i class="bi bi-tag-fill text-warning fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-dark">Próximamente más Promociones</h5>
                <p class="text-muted small mb-3">Nuestros comercios aliados renuevan ofertas continuamente. Visita el catálogo para explorar todos los lugares disponibles.</p>
                <a href="#explorar" class="btn btn-outline-teal btn-sm rounded-pill px-4 fw-semibold">
                    <i class="bi bi-compass me-1"></i> Explorar Catálogo
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Guía del Viajero -->
<section class="bg-white py-5 border-bottom" id="guia-trinidad">
    <div class="container py-3 position-relative guia-trinidad-container">
        <!-- Encabezado con Animales Amazónicos: Perezoso y Tucán -->
        <div class="row align-items-center mb-5 position-relative">
            <!-- Perezoso Colgado de la Rama (Izquierda) -->
            <div class="col-lg-2 col-md-3 d-none d-md-flex justify-content-center justify-content-lg-start mb-3 mb-lg-0">
                <div class="perezoso-flotante-box position-relative" title="Perezoso Beniano (Bradypus) - Calma y naturaleza de la selva beniana">
                    <svg viewBox="0 0 160 140" width="135" height="118" fill="none" xmlns="http://www.w3.org/2000/svg" class="perezoso-svg">
                        <defs>
                            <linearGradient id="gradPerezosoCuerpo" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#a8896c" />
                                <stop offset="50%" stop-color="#8c6d53" />
                                <stop offset="100%" stop-color="#6b4f3a" />
                            </linearGradient>
                            <linearGradient id="gradPerezosoCara" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#fdfbf7" />
                                <stop offset="100%" stop-color="#f3ede2" />
                            </linearGradient>
                            <filter id="sombraPerezoso" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#022c22" flood-opacity="0.25" />
                            </filter>
                        </defs>
                        <!-- Rama de la que cuelga -->
                        <path d="M5 25 C50 20 110 26 155 22" stroke="#78350f" stroke-width="7" stroke-linecap="round" />
                        <path d="M12 24 C55 20 105 24 150 22" stroke="#92400e" stroke-width="2.5" stroke-linecap="round" opacity="0.6" />
                        <!-- Hoja selvática en la rama -->
                        <path d="M30 22 C22 12 10 14 8 20 C14 26 24 26 30 22 Z" fill="#22c55e" />
                        <path d="M26 22 C32 10 45 10 46 16 C40 24 30 24 26 22 Z" fill="#16a34a" />

                        <!-- Brazo Izquierdo sujetando la rama -->
                        <path d="M48 65 C44 42 46 26 54 22 C56 22 58 26 56 36 C54 48 56 60 56 68 Z" fill="#785942" />
                        <!-- Garras curvas aferradas -->
                        <path d="M51 22 C50 16 54 15 56 18" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M55 22 C55 16 59 15 60 18" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M59 23 C60 17 64 16 64 19" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />

                        <!-- Brazo Derecho sujetando la rama -->
                        <path d="M112 65 C116 42 114 26 106 22 C104 22 102 26 104 36 C106 48 104 60 104 68 Z" fill="#6b4f3a" />
                        <!-- Garras derechas -->
                        <path d="M101 22 C100 16 104 15 105 18" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M105 22 C105 16 109 15 110 18" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M109 23 C110 17 114 16 114 19" stroke="#334155" stroke-width="2.5" stroke-linecap="round" />

                        <!-- Cuerpo del perezoso panzón suspendido -->
                        <g class="perezoso-cuerpo-hamaca">
                            <path d="M46 65 C40 88 56 122 80 122 C104 122 120 88 114 65 C108 52 52 52 46 65 Z" fill="url(#gradPerezosoCuerpo)" filter="url(#sombraPerezoso)" />
                            <!-- Pelaje de textura en pecho -->
                            <path d="M60 75 C68 82 92 82 100 75 C94 92 66 92 60 75 Z" fill="#5c4028" opacity="0.4" />
                            
                            <!-- Patitas traseras recogidas cómodamente -->
                            <path d="M58 114 C52 118 48 126 54 130 C62 132 66 124 64 116 Z" fill="#6b4f3a" />
                            <path d="M102 114 C108 118 112 126 106 130 C98 132 94 124 96 116 Z" fill="#5c4028" />

                            <!-- Cabecita tierna y sonriente -->
                            <g class="perezoso-cabecita">
                                <circle cx="80" cy="62" r="23" fill="#8c6d53" />
                                <!-- Máscara facial clara -->
                                <path d="M64 62 C64 50 72 46 80 46 C88 46 96 50 96 62 C96 72 88 76 80 76 C72 76 64 72 64 62 Z" fill="url(#gradPerezosoCara)" />
                                <!-- Franjas oscuras características alrededor de los ojos -->
                                <path d="M67 58 C66 54 74 54 76 58 C74 62 67 62 67 58 Z" fill="#5c4028" />
                                <path d="M93 58 C94 54 86 54 84 58 C86 62 93 62 93 58 Z" fill="#5c4028" />
                                <!-- Ojos amigables y relajados -->
                                <circle cx="72" cy="58" r="2" fill="#1e293b" />
                                <circle cx="88" cy="58" r="2" fill="#1e293b" />
                                <circle cx="72.6" cy="57.4" r="0.7" fill="#ffffff" />
                                <circle cx="88.6" cy="57.4" r="0.7" fill="#ffffff" />
                                <!-- Nariz oscura -->
                                <ellipse cx="80" cy="64" rx="4.5" ry="3.2" fill="#1e293b" />
                                <!-- Sonrisa pacífica -->
                                <path d="M75 69 C78 72 82 72 85 69" stroke="#1e293b" stroke-width="1.6" stroke-linecap="round" />
                            </g>
                        </g>
                    </svg>
                    <div class="text-teal fw-bold text-center extra-small mt-1 text-uppercase font-xs-7 tracking-wide-05">
                        <i class="fa-solid fa-leaf text-success me-1"></i> Sin Prisa en Trinidad
                    </div>
                </div>
            </div>

            <!-- Título y Descripción Central -->
            <div class="col-lg-8 col-md-6 text-center">
                <span class="badge hero-badge-teal px-3 py-2 rounded-pill fw-bold text-uppercase small mb-2 shadow-xs">
                    <i class="fa-solid fa-compass me-1"></i> Consejos Útiles
                </span>
                <h2 class="fw-bold text-dark display-6 mb-2">¿Llegando por primera vez a Trinidad?</h2>
                <p class="text-muted small mx-auto mb-0 section-intro-bounded-sm">
                    Información práctica para moverte por la ciudad con total tranquilidad y disfrutar al máximo de la calidez, la fauna y la hospitalidad beniana.
                </p>
            </div>

            <!-- Tucán Amazónico Posado en Rama con Gran Pico (Derecha) -->
            <div class="col-lg-2 col-md-3 d-none d-md-flex justify-content-center justify-content-lg-end mb-3 mb-lg-0">
                <div class="tucan-flotante-box position-relative" title="Tucán Toco Amazónico (Ramphastos toco) - Emblema de la selva y pampas del Beni">
                    <svg viewBox="0 0 160 140" width="140" height="122" fill="none" xmlns="http://www.w3.org/2000/svg" class="tucan-svg">
                        <defs>
                            <!-- Pico arcoíris/amarillo naranja toco -->
                            <linearGradient id="gradPicoTucan" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#ea580c" />
                                <stop offset="35%" stop-color="#f59e0b" />
                                <stop offset="70%" stop-color="#facc15" />
                                <stop offset="100%" stop-color="#fbbf24" />
                            </linearGradient>
                            <linearGradient id="gradCuerpoNegro" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#1e293b" />
                                <stop offset="60%" stop-color="#0f172a" />
                                <stop offset="100%" stop-color="#020617" />
                            </linearGradient>
                            <filter id="sombraTucan" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#022c22" flood-opacity="0.25" />
                            </filter>
                        </defs>

                        <!-- Rama tropical de apoyo -->
                        <path d="M15 116 C60 114 110 118 155 112" stroke="#78350f" stroke-width="6.5" stroke-linecap="round" />
                        <path d="M25 114 C70 112 115 116 150 112" stroke="#92400e" stroke-width="2" stroke-linecap="round" opacity="0.6" />
                        <!-- Hoja de palmera verde -->
                        <path d="M35 114 C25 106 18 114 26 122 C34 122 38 118 35 114 Z" fill="#16a34a" />
                        <path d="M48 114 C40 102 32 108 38 118 C44 118 48 116 48 114 Z" fill="#22c55e" />

                        <!-- Cola negra con base roja brillante -->
                        <path d="M106 98 C112 112 118 128 122 136 C116 128 108 114 102 102 Z" fill="#ef4444" />
                        <path d="M108 100 C114 116 122 132 126 138 C118 126 110 112 104 102 Z" fill="#0f172a" />

                        <!-- Cuerpo del tucán negro carbón -->
                        <path d="M72 65 C82 62 106 72 108 98 C108 106 100 112 90 112 C78 112 72 102 70 88 C68 76 70 68 72 65 Z" fill="url(#gradCuerpoNegro)" filter="url(#sombraTucan)" />
                        
                        <!-- Babero / Cuello blanco puro característico -->
                        <path d="M62 48 C68 44 80 44 86 52 C88 64 84 76 72 78 C64 78 58 70 60 58 C60 52 61 50 62 48 Z" fill="#ffffff" />
                        <!-- Borde rojo vivo en el límite del babero -->
                        <path d="M65 74 C72 78 78 77 82 72 C80 75 74 77 68 75 Z" fill="#ef4444" />

                        <!-- Cabeza y corona negra -->
                        <g class="tucan-cabeza-animada">
                            <path d="M52 38 C56 30 70 28 78 36 C82 42 80 50 76 54 C68 56 58 54 54 48 Z" fill="url(#gradCuerpoNegro)" />
                            
                            <!-- Área ocular azul celeste/turquesa y naranja -->
                            <path d="M60 38 C64 36 70 38 72 43 C72 47 68 50 64 50 C60 50 58 46 58 42 Z" fill="#38bdf8" />
                            <circle cx="65" cy="43" r="3.2" fill="#0284c7" />
                            <circle cx="65.5" cy="42.5" r="1.8" fill="#0f172a" />
                            <circle cx="66.2" cy="42" r="0.8" fill="#ffffff" />

                            <!-- Pico Toco Gigante Curvo en Degradado Oro-Naranja con Punta Negra -->
                            <path d="M54 36 C42 35 24 40 10 52 C6 56 8 60 14 60 C26 59 44 56 56 52 C58 48 58 40 54 36 Z" fill="url(#gradPicoTucan)" filter="url(#sombraTucan)" />
                            <!-- Detalle de mandíbula inferior del pico -->
                            <path d="M54 48 C42 52 26 56 14 60 C18 63 26 63 36 61 C46 59 52 55 56 52 Z" fill="#f59e0b" />
                            <!-- Mancha negra grande en la punta del pico del Tucán Toco -->
                            <path d="M10 52 C8 54 9 58 14 60 C20 59 22 55 20 51 C16 51 12 51 10 52 Z" fill="#0f172a" />
                            <!-- Línea de separación negra entre las valvas del pico -->
                            <path d="M14 59 C28 58 44 55 56 50" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
                            <!-- Base roja del pico junto a la cara -->
                            <path d="M55 36 C57 40 58 46 56 52" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" />
                        </g>

                        <!-- Patitas grises agarrando la rama -->
                        <path d="M78 110 C77 113 76 116 75 118" stroke="#475569" stroke-width="3.5" stroke-linecap="round" />
                        <path d="M86 110 C86 113 86 116 85 118" stroke="#475569" stroke-width="3.5" stroke-linecap="round" />
                        <path d="M72 118 C75 120 78 119 79 117" stroke="#1e293b" stroke-width="2" stroke-linecap="round" />
                        <path d="M83 118 C86 120 89 119 90 117" stroke="#1e293b" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <div class="text-teal fw-bold text-center extra-small mt-1 text-uppercase font-xs-7 tracking-wide-05">
                        <i class="fa-solid fa-feather text-warning me-1"></i> Tucán Amazónico
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center guide-card guide-card-transport">
                    <div class="icon-circle mx-auto mb-3">
                        <i class="bi bi-car-front-fill fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Transporte Urbano & Taxis</h5>
                    <p class="text-muted small mb-0">
                        Para moverte por la ciudad cuentas con mototaxis para tramos cortos o radiomóviles oficiales con central 24h para traslados seguros, paseos a La Loma Suárez y viajes con equipaje.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center guide-card guide-card-food">
                    <div class="icon-circle mx-auto mb-3">
                        <i class="bi bi-cup-hot-fill fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Sabores Imperdibles</h5>
                    <p class="text-muted small mb-0">
                        No te vayas de Trinidad sin probar el pacú frito de río, el majadito de charque con plátano maduro, el keoperí al horno y los masacos de yuca o plátano.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light rounded-4 p-4 text-center guide-card guide-card-sunset">
                    <div class="icon-circle mx-auto mb-3">
                        <i class="bi bi-water fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Atardeceres Benianos</h5>
                    <p class="text-muted small mb-0">
                        La Laguna Suárez (a 5 km del centro) y Puerto Ballivián sobre el río Ibare son los mejores puntos para contemplar la caída del sol con un refresco tradicional de somó.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Radiomóviles y Transporte Turístico en Trinidad -->
<section class="seccion-transporte-radiomovil py-5 border-bottom position-relative overflow-hidden" id="transporte">

    <!-- Video de Fondo en Transporte -->
    <video class="transporte-bg-video" autoplay muted loop playsinline preload="metadata" poster="<?= htmlspecialchars($baseUrl) ?>/assets/img/hero-poster.webp" aria-hidden="true">
        <source src="<?= htmlspecialchars($baseUrl) ?>/assets/video/video.mp4" type="video/mp4">
    </video>
    <div class="transporte-bg-overlay" aria-hidden="true"></div>

    <div class="container py-2 position-relative z-index-2">
        <div class="text-center mb-4">
            <span class="badge text-white px-3 py-2 rounded-pill fw-bold text-uppercase small mb-2 shadow-sm hero-badge-glass">
                <i class="bi bi-taxi-front-fill me-1 text-teal-highlight"></i> Movilidad Segura en Trinidad
            </span>
            <h2 class="fw-bold text-white mb-1 text-shadow-sm">Radiomóviles y Taxis Oficiales</h2>
            <p class="text-white-50 small mx-auto mb-0 section-intro-bounded">
                Asociaciones oficiales con paradas fijas y central de despacho 24 horas. Carreras urbanas en todo Trinidad, paseos turísticos a La Loma Suárez, Laguna Suárez y expresos a todo el departamento.
            </p>
        </div>

        <?php if (!empty($radiomoviles)): ?>
            <div class="row g-4 align-items-stretch">
                <?php
                    // Precomputar puntos de mapa para tener el conteo exacto y los puntos por sindicato
                    $puntosMapa = [];
                    $paradasPorSindicato = [];

                    // 1. Ubicación principal de cada sindicato
                    foreach ($radiomoviles as $rm) {
                        $idL = (int)$rm['id_lugar'];
                        $paradasPorSindicato[$idL] = [];
                        if (!empty($rm['latitud']) && !empty($rm['longitud'])) {
                            $wa = preg_replace('/\D/', '', $rm['whatsapp_contacto'] ?? '');
                            if (strlen($wa) === 8) $wa = '591' . $wa;
                            $tel = preg_replace('/[^\d+]/', '', $rm['telefono_contacto'] ?? '');
                            $pt = [
                                'id_lugar'        => $idL,
                                'id_parada'       => 0,
                                'nombre_sindicato'=> $rm['nombre'],
                                'nombre_parada'   => 'Central Principal',
                                'tipo_parada'     => 'CENTRAL',
                                'latitud'         => (float)$rm['latitud'],
                                'longitud'        => (float)$rm['longitud'],
                                'direccion'       => $rm['direccion'] ?? 'Trinidad, Beni',
                                'referencia'      => $rm['referencia_ubicacion'] ?? '',
                                'telefono'        => $tel,
                                'whatsapp'        => $wa,
                                'slug'            => $rm['slug'] ?? '',
                                'url_ficha'       => $baseUrl . '/catalogo/detalle?slug=' . urlencode($rm['slug'] ?? '')
                            ];
                            $puntosMapa[] = $pt;
                            $paradasPorSindicato[$idL][] = $pt;
                        }
                    }

                    // 2. Paradas secundarias registradas en lugares_paradas
                    foreach ($paradasTransporte ?? [] as $par) {
                        $idL = (int)$par['id_lugar'];
                        if (!empty($par['latitud']) && !empty($par['longitud'])) {
                            $wa = preg_replace('/\D/', '', $par['whatsapp_directo'] ?: ($par['sindicato_whatsapp'] ?? ''));
                            if (strlen($wa) === 8) $wa = '591' . $wa;
                            $tel = preg_replace('/[^\d+]/', '', $par['telefono_directo'] ?: ($par['sindicato_telefono'] ?? ''));
                            $pt = [
                                'id_lugar'        => $idL,
                                'id_parada'       => (int)$par['id_parada'],
                                'nombre_sindicato'=> $par['sindicato_nombre'],
                                'nombre_parada'   => $par['nombre_parada'],
                                'tipo_parada'     => $par['tipo_parada'],
                                'latitud'         => (float)$par['latitud'],
                                'longitud'        => (float)$par['longitud'],
                                'direccion'       => $par['direccion'] ?: ($par['referencia'] ?: 'Trinidad, Beni'),
                                'referencia'      => $par['referencia'] ?? '',
                                'telefono'        => $tel,
                                'whatsapp'        => $wa,
                                'slug'            => $par['sindicato_slug'] ?? '',
                                'url_ficha'       => $baseUrl . '/catalogo/detalle?slug=' . urlencode($par['sindicato_slug'] ?? '')
                            ];
                            $puntosMapa[] = $pt;
                            if (!isset($paradasPorSindicato[$idL])) {
                                $paradasPorSindicato[$idL] = [];
                            }
                            $paradasPorSindicato[$idL][] = $pt;
                        }
                    }

                    // Paleta institucional distinguible por sindicato
                    $coloresSindicatos = [
                        ['bg' => '#f59e0b', 'text' => '#111827', 'border' => '#d97706', 'nombre' => 'Amarillo'],
                        ['bg' => '#0284c7', 'text' => '#ffffff', 'border' => '#0369a1', 'nombre' => 'Azul'],
                        ['bg' => '#10b981', 'text' => '#ffffff', 'border' => '#047857', 'nombre' => 'Verde'],
                        ['bg' => '#ea580c', 'text' => '#ffffff', 'border' => '#c2410c', 'nombre' => 'Naranja'],
                        ['bg' => '#8b5cf6', 'text' => '#ffffff', 'border' => '#6d28d9', 'nombre' => 'Violeta'],
                        ['bg' => '#e11d48', 'text' => '#ffffff', 'border' => '#be123c', 'nombre' => 'Rojo']
                    ];
                ?>

                <!-- Columna: Tarjetas de Asociaciones (order-2 en móvil para quedar debajo del mapa, order-lg-1 en desktop) -->
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <span class="extra-small fw-bold text-white text-opacity-75 text-uppercase letter-spacing-1">
                            <i class="bi bi-shield-check text-warning me-1"></i> <?= count($radiomoviles) ?> Asociaciones Habilitadas
                        </span>
                        <span class="extra-small text-white text-opacity-75 d-lg-none">
                            <i class="bi bi-arrow-left-right text-warning me-1"></i> Desliza para ver más
                        </span>
                    </div>

                    <!-- Contenedor con scroll vertical en PC y swipe horizontal en Móvil -->
                    <div class="transporte-lista-scrollable">
                        <?php foreach ($radiomoviles as $idxRm => $rm): ?>
                            <?php
                                $idLugarRm = (int)$rm['id_lugar'];
                                $colorInfo = $coloresSindicatos[$idxRm % count($coloresSindicatos)];
                                $waRaw = preg_replace('/\D/', '', $rm['whatsapp_contacto'] ?? '');
                                if (strlen($waRaw) === 8) {
                                    $waRaw = '591' . $waRaw;
                                }
                                $telRaw = preg_replace('/[^\d+]/', '', $rm['telefono_contacto'] ?? '');
                                $urlFicha = $baseUrl . '/catalogo/detalle?slug=' . urlencode($rm['slug']);
                                $latRm = !empty($rm['latitud']) ? (float)$rm['latitud'] : null;
                                $lngRm = !empty($rm['longitud']) ? (float)$rm['longitud'] : null;
                                $listaPuntosLugar = $paradasPorSindicato[$idLugarRm] ?? [];
                                $totalParadasLugar = count($listaPuntosLugar);
                            ?>
                            <div class="card border rounded-4 shadow-sm card-transporte-app bg-white overflow-hidden" 
                                 id="card-radio-<?= $idLugarRm ?>"
                                 data-id="<?= $idLugarRm ?>"
                                 data-lat="<?= $latRm ?? '' ?>"
                                 data-lng="<?= $lngRm ?? '' ?>"
                                 style="border-left: 5px solid <?= $colorInfo['bg'] ?> !important;">

                                <!-- Barra superior: badge de estado y parada fija -->
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill px-2 py-1 extra-small fw-bold" style="background-color: <?= $colorInfo['bg'] ?>; color: <?= $colorInfo['text'] ?>;">
                                            <i class="bi bi-geo-alt-fill me-1"></i><?= $totalParadasLugar ?> <?= $totalParadasLugar === 1 ? 'Parada' : 'Paradas' ?>
                                        </span>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 extra-small fw-semibold">
                                            <i class="bi bi-circle-fill me-1" style="font-size:0.45rem;vertical-align:middle;"></i> 24h
                                        </span>
                                    </div>
                                    <?php if ($latRm && $lngRm): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-link text-teal p-0 text-decoration-none extra-small fw-bold btn-ver-parada-mapa"
                                                data-lat="<?= $latRm ?>"
                                                data-lng="<?= $lngRm ?>"
                                                data-id="<?= $idLugarRm ?>"
                                                data-parada-id="0"
                                                title="Ubicar central en el mapa">
                                            <i class="bi bi-crosshair me-1 text-danger"></i> Central en mapa
                                        </button>
                                    <?php else: ?>
                                        <span class="extra-small text-muted fw-medium">
                                            <i class="bi bi-patch-check-fill text-teal me-1"></i> Oficial
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <!-- Identidad: Ícono con color distintivo + Nombre + Parada -->
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="transporte-app-avatar rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="background-color: <?= $colorInfo['bg'] ?>; color: <?= $colorInfo['text'] ?>; border: 2px solid <?= $colorInfo['border'] ?>; width: 42px; height: 42px; flex-shrink: 0;">
                                            <i class="bi bi-taxi-front-fill fs-5"></i>
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <h6 class="fw-bold mb-0 text-truncate lh-sm text-trini-dark"><?= htmlspecialchars($rm['nombre']) ?></h6>
                                            <p class="extra-small text-muted mb-0 text-truncate">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($rm['direccion'] ?: 'Central Trinidad') ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Fila Informativa de Confianza y Cobertura -->
                                    <div class="transporte-app-info-row rounded-3 py-2 px-3 mb-2 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-1 extra-small text-teal fw-semibold">
                                            <i class="bi bi-patch-check-fill fs-6"></i>
                                            <span>Asociación Oficial Registrada</span>
                                        </div>
                                        <span class="badge badge-teal-subtle rounded-pill extra-small px-2 py-1">
                                            <i class="bi bi-shield-lock me-1"></i> Viaje Seguro
                                        </span>
                                    </div>

                                    <!-- Chips de Paradas y Casetas de este Sindicato -->
                                    <?php if (!empty($listaPuntosLugar)): ?>
                                        <div class="mb-2">
                                            <div class="extra-small text-muted fw-bold mb-1 d-flex align-items-center gap-1">
                                                <i class="bi bi-signpost-split text-teal"></i> Paradas oficiales habilitadas:
                                            </div>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($listaPuntosLugar as $pto): ?>
                                                    <button type="button" 
                                                            class="btn btn-xs rounded-pill extra-small py-0 px-2 btn-ver-parada-mapa d-inline-flex align-items-center gap-1 shadow-2xs"
                                                            style="font-size: 0.72rem; border: 1px solid <?= $colorInfo['border'] ?>; background-color: #f8fafc; color: #1e293b;"
                                                            data-lat="<?= $pto['latitud'] ?>"
                                                            data-lng="<?= $pto['longitud'] ?>"
                                                            data-id="<?= $idLugarRm ?>"
                                                            data-parada-id="<?= $pto['id_parada'] ?>"
                                                            title="Ver <?= htmlspecialchars($pto['nombre_parada']) ?> en el mapa">
                                                        <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: <?= $colorInfo['bg'] ?>;"></span>
                                                        <strong><?= htmlspecialchars($pto['nombre_parada']) ?></strong>
                                                        <i class="bi bi-geo-alt text-muted" style="font-size: 0.68rem;"></i>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Destinos y Servicios en micro-etiquetas estilizadas -->
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        <span class="transporte-tag"><i class="bi bi-signpost-2-fill me-1 text-teal"></i>Carrera Urbana</span>
                                        <span class="transporte-tag"><i class="bi bi-tree-fill me-1 text-teal"></i>Loma Suárez</span>
                                        <span class="transporte-tag"><i class="bi bi-water me-1 text-teal"></i>Laguna Suárez</span>
                                        <span class="transporte-tag"><i class="bi bi-compass-fill me-1 text-secondary"></i>Expresos</span>
                                    </div>

                                    <!-- Botones de Acción con Paleta BeniTours -->
                                    <div class="mt-auto d-flex gap-2">
                                        <?php if (!empty($waRaw)): ?>
                                            <button type="button" 
                                                    class="btn btn-teal-action rounded-pill fw-semibold flex-grow-1 d-flex align-items-center justify-content-center gap-2 py-2 btn-pedir-radio shadow-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPedirRadiomovilIndex"
                                                    data-nombre="<?= htmlspecialchars($rm['nombre']) ?>"
                                                    data-wa="<?= htmlspecialchars($waRaw) ?>"
                                                    data-id="<?= $idLugarRm ?>">
                                                <i class="bi bi-whatsapp fs-5 text-white"></i> Pedir Móvil
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($telRaw)): ?>
                                            <a href="tel:<?= $telRaw ?>" 
                                               onclick="window.BeniTursItinerario?.registrarEvento(<?= $idLugarRm ?>, 'TELEFONO')"
                                               class="btn btn-outline-teal rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0 size-40"
                                               title="Llamar a central telefónica">
                                                <i class="bi bi-telephone-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= $urlFicha ?>" 
                                           class="btn btn-outline-teal rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0 size-40"
                                           title="Ver información y paradas completas">
                                            <i class="bi bi-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Mapa Interactivo de Paradas Fijas (order-1 en móvil para quedar arriba, order-lg-2 en desktop) -->
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="card border rounded-4 shadow-sm overflow-hidden h-100 bg-white d-flex flex-column transporte-mapa-card">
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light bg-opacity-75">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center size-34">
                                    <i class="bi bi-geo-alt-fill text-warning fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Mapa de Paradas Fijas</h6>
                                    <span class="extra-small text-muted">Centrales y casetas habilitadas en Trinidad</span>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill extra-small px-3 py-1 fw-bold">
                                <?= count($puntosMapa) ?> Paradas activas
                            </span>
                        </div>

                        <!-- Contenedor del Mapa Leaflet -->
                        <div class="position-relative flex-grow-1 transporte-mapa-canvas-box">
                            <div id="mapaTransporte" class="transporte-mapa-canvas z-index-1"></div>
                        </div>

                        <!-- Leyenda de colores interactiva por Sindicato -->
                        <div class="p-2 px-3 bg-white border-top">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="extra-small fw-bold text-muted"><i class="bi bi-palette-fill me-1"></i>Sindicatos:</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($radiomoviles as $idxRm => $rm): ?>
                                        <?php $c = $coloresSindicatos[$idxRm % count($coloresSindicatos)]; ?>
                                        <span class="badge rounded-pill extra-small px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs border"
                                              style="background-color: <?= $c['bg'] ?>; color: <?= $c['text'] ?>; border-color: <?= $c['border'] ?> !important;">
                                            <i class="bi bi-taxi-front-fill" style="font-size: 0.65rem;"></i>
                                            <?= htmlspecialchars($rm['nombre']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light border-top extra-small text-muted d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-info-circle me-1 text-teal"></i> Toca un marcador para pedir móvil directo a esa parada.</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 extra-small" id="btnRecentrarMapaTransporte">
                                <i class="bi bi-arrows-fullscreen me-1"></i> Ver todo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-taxi-front display-4 d-block mb-2"></i>
                <p>Próximamente más asociaciones de radiomóvil afiliadas.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Payload de Paradas Fijas de Radiomóviles para Leaflet (Centrales + Paradas Secundarias) -->
<script>
    window.BeniToursRadiomoviles = <?= json_encode($puntosMapa ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Modal Asistente para Pedir Radiomóvil en la Portada -->
<div class="modal fade" id="modalPedirRadiomovilIndex" tabindex="-1" aria-labelledby="modalPedirRadioIndexTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-warning bg-opacity-25 border-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center size-38">
                        <i class="bi bi-taxi-front-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fs-6 fw-bold text-dark mb-0" id="modalPedirRadioIndexTitulo">
                            Pedir Radiomóvil a la Central
                        </h5>
                        <p class="extra-small text-muted mb-0" id="modalPedirRadioIndexNombre">Central de Radiomóvil</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formPedirRadiomovilIndex" onsubmit="return false;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> ¿Dónde te recogemos?
                        </label>
                        <div class="input-group">
                            <input type="text" id="radioIndexMiUbicacion" class="form-control rounded-start-pill ps-3 text-dark small" placeholder="Ej: Hotel Don Bernardo, Plaza Principal...">
                            <button type="button" id="btnRadioIndexUsarGps" class="btn btn-outline-teal rounded-end-pill px-3 extra-small fw-semibold" title="Insertar mi posición GPS actual">
                                <i class="bi bi-cursor-fill me-1"></i> Mi GPS
                            </button>
                        </div>
                        <div class="form-text extra-small">Indica tu hotel, calle o presiona "Mi GPS".</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-pin-map-fill text-teal me-1"></i> ¿A dónde deseas ir? (Destino)
                        </label>
                        <input type="text" id="radioIndexDestino" class="form-control rounded-pill px-3 text-dark small" placeholder="Ej: La Loma Suárez, Aeropuerto, Restaurante...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-tag-fill text-warning me-1"></i> Tipo de Servicio
                        </label>
                        <select id="radioIndexTipoServicio" class="form-select rounded-pill px-3 text-dark small">
                            <option value="Carrera Urbana">Carrera Urbana (Centro y Circunvalación)</option>
                            <option value="Carrera Periférica (Fuera de circunvalación)">Carrera Periférica (Barrios fuera de circunvalación)</option>
                            <option value="Paseo Turístico a La Loma Suárez">Paseo Turístico: La Loma Suárez</option>
                            <option value="Paseo Turístico a Laguna Suárez">Paseo Turístico: Laguna Suárez</option>
                            <option value="Paseo Turístico a Puerto Ballivián">Paseo Turístico: Puerto Ballivián</option>
                            <option value="Expreso Interprovincial (Viaje especial)">Expreso Interprovincial (A otra localidad o provincia)</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="btnEnviarPedidoRadioIndex" class="btn btn-teal-action rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-whatsapp fs-5"></i> Enviar Pedido a la Central por WhatsApp
                        </button>
                        <button type="button" class="btn btn-light rounded-pill py-2 text-secondary extra-small" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle de Promoción -->
<div class="modal fade" id="modalDetallePromocion" tabindex="-1" aria-labelledby="modalPromoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="promo-modal-header-badges">
                    <span class="badge badge-teal rounded-pill px-3 py-2 fw-semibold" id="modalPromoCategoria">
                        <i class="bi bi-tag-fill me-1" id="modalPromoCatIcono"></i>
                        <span id="modalPromoCatTexto">Promoción</span>
                    </span>
                    <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold d-none" id="modalPromoDescuentoTag">
                        <i class="bi bi-fire me-1"></i> <span id="modalPromoDescuentoTexto"></span>
                    </span>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row g-4 align-items-stretch">
                    <!-- Columna Izquierda: Foto completa sin recortes -->
                    <div class="col-lg-6 d-flex flex-column">
                        <div class="promo-modal-image-box rounded-4 overflow-hidden position-relative shadow-sm flex-grow-1 d-flex align-items-center justify-content-center modal-promo-preview-box">
                            <!-- Fondo desenfocado -->
                            <div id="modalPromoImgBackdrop" class="modal-promo-blur-bg"></div>
                            <!-- Imagen principal completa sin recortes -->
                            <img id="modalPromoImg" src="" alt="Promoción"
                                 class="position-relative w-100 h-100 object-fit-contain p-2 z-index-2 max-h-500"
                                 loading="lazy">
                            <!-- Placeholder si no hay imagen -->
                            <div id="modalPromoNoImg" class="text-white text-center p-4 position-relative d-none z-index-2">
                                <i class="bi bi-tags display-1 text-teal mb-3 d-block"></i>
                                <h4 class="fw-bold" id="modalPromoPlaceholderNombre"></h4>
                                <p class="text-white-50 small mb-0">Oferta especial exclusiva en BeniTours</p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Título, Lugar, Descripción, Botones y Mapa -->
                    <div class="col-lg-6 d-flex flex-column">
                        <!-- Título y Lugar -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-muted small fw-semibold">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><span id="modalPromoLugarMunicipio">Trinidad</span>
                                </span>
                            </div>
                            <h3 class="fw-bold text-teal mb-1" id="modalPromoTitulo"></h3>
                            <h5 class="fw-semibold text-dark mb-2" id="modalPromoLugarNombre"></h5>
                            
                            <!-- Vigencia -->
                            <div id="modalPromoVigenciaContainer" class="small text-muted mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-clock-history text-danger"></i>
                                <span>Válido hasta: <strong class="text-dark" id="modalPromoFechaFin"></strong></span>
                            </div>
                            
                            <!-- Descripción completa -->
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <h6 class="fw-bold text-dark mb-1 small text-uppercase tracking-wider">
                                    <i class="bi bi-info-circle text-teal me-1"></i> Descripción de la oferta:
                                </h6>
                                <p class="text-secondary small mb-0 lh-base detalle-desc-text" id="modalPromoDescripcion"></p>
                            </div>
                        </div>

                        <!-- Mini Mapa de Ubicación -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small text-uppercase">
                                    <i class="bi bi-map-fill text-teal me-1"></i> Ubicación del Comercio
                                </span>
                                <small class="text-muted extra-small text-truncate max-w-250" id="modalPromoLugarDireccion"></small>
                            </div>
                            <!-- Contenedor Mapa Leaflet -->
                            <div id="modalPromoMapa" class="rounded-3 border shadow-sm modal-promo-map-canvas z-index-1"></div>
                            <div id="modalPromoMapaFallback" class="alert alert-light border small text-muted text-center py-2 mb-0 d-none">
                                <i class="bi bi-geo-alt me-1"></i> Dirección: <span id="modalPromoLugarDireccionAlt"></span>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-auto pt-3 border-top d-flex flex-wrap align-items-center gap-2">
                            <!-- WhatsApp Principal -->
                            <a id="modalPromoBtnWa" href="#" target="_blank" rel="noopener noreferrer"
                               class="btn btn-teal-action fw-bold rounded-pill px-3 py-2 flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="bi bi-whatsapp fs-5"></i>
                                <span>Aprovechar en WhatsApp</span>
                            </a>

                            <!-- Redes Sociales de la Promo (Solo Íconos Circulares) -->
                            <div id="modalPromoRedesContainer" class="d-flex align-items-center gap-1 d-none">
                                <a id="modalPromoBtnTiktok" href="#" target="_blank" rel="noopener noreferrer"
                                   class="btn-social-icon btn-social-icon-lg btn-social-icon--tiktok shadow-xs d-none" 
                                   title="Ver video en TikTok">
                                    <i class="bi bi-tiktok"></i>
                                </a>
                                <a id="modalPromoBtnFacebook" href="#" target="_blank" rel="noopener noreferrer"
                                   class="btn-social-icon btn-social-icon-lg btn-social-icon--facebook shadow-xs d-none" 
                                   title="Ver en Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a id="modalPromoBtnInstagram" href="#" target="_blank" rel="noopener noreferrer"
                                   class="btn-social-icon btn-social-icon-lg btn-social-icon--instagram shadow-xs d-none" 
                                   title="Ver en Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            </div>

                            <!-- Ver Ficha Completa -->
                            <a id="modalPromoBtnFicha" href="#"
                               class="btn btn-outline-teal fw-semibold rounded-pill px-3 py-2 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                <span>Ver Ficha Completa</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>

                            <!-- Cómo llegar (Google Maps) -->
                            <a id="modalPromoBtnComoLlegar" href="#" target="_blank" rel="noopener noreferrer"
                               class="btn btn-outline-secondary fw-semibold rounded-pill px-3 py-2 d-flex align-items-center justify-content-center gap-1"
                               title="Abrir indicaciones en Google Maps">
                                <i class="bi bi-compass"></i>
                                <span>Cómo llegar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<!-- Sección Galería de Fotos Turísticas (Bento Grid) -->
<?php if (!empty($galeriaFotos)): ?>
<section class="seccion-galeria-turismo py-5 bg-light border-bottom position-relative" id="galeria">
    <div class="container py-2">
        <div class="text-center mb-4">
            <span class="badge hero-badge-teal px-3 py-2 rounded-pill fw-bold text-uppercase small mb-2 shadow-xs">
                <i class="bi bi-camera-fill me-1"></i> Postales de Trinidad y el Beni
            </span>
            <h2 class="fw-bold text-dark mb-1">Galería Visual Turística</h2>
            <p class="text-muted small mx-auto mb-0 section-intro-bounded">
                Descubre los paisajes amazónicos, la cultura viva, la gastronomía tradicional y los atractivos que te esperan en el corazón del Beni.
            </p>
        </div>

        <!-- Mosaico Bento Grid -->
        <div class="bento-gallery-grid">
            <?php foreach ($galeriaFotos as $idx => $gf): ?>
                <?php
                    $fotoUrl = "{$baseUrl}/imagen?f=" . urlencode($gf['nombre_archivo']) . "&v=" . (int)$gf['id_galeria'];
                    $fichaUrl = !empty($gf['lugar_slug']) ? "{$baseUrl}/catalogo/detalle?slug=" . urlencode($gf['lugar_slug']) : '';
                    $isFeatured = !empty($gf['es_destacada']);
                    $categoriaTxt = !empty($gf['subtitulo']) ? $gf['subtitulo'] : (!empty($gf['categoria_nombre']) ? $gf['categoria_nombre'] : 'Postal del Beni');
                ?>
                <div class="bento-gallery-item <?= $isFeatured ? 'bento-item-featured' : '' ?>"
                     data-bs-toggle="modal"
                     data-bs-target="#modalLightboxGaleria"
                     data-foto="<?= htmlspecialchars($fotoUrl) ?>"
                     data-titulo="<?= htmlspecialchars($gf['titulo']) ?>"
                     data-categoria="<?= htmlspecialchars($categoriaTxt) ?>"
                     data-credito="<?= htmlspecialchars($gf['creditos_autor'] ?? '') ?>"
                     data-fuente="<?= htmlspecialchars($gf['url_fuente'] ?? '') ?>"
                     data-url="<?= htmlspecialchars($fichaUrl) ?>">
                    <img src="<?= htmlspecialchars($fotoUrl) ?>" 
                         alt="<?= htmlspecialchars($gf['titulo']) ?>" 
                         loading="lazy" 
                         class="bento-gallery-img">
                    <div class="bento-gallery-overlay">
                        <span class="badge bg-dark bg-opacity-75 text-white extra-small rounded-pill px-2 py-1 mb-1">
                            <?= htmlspecialchars($categoriaTxt) ?>
                        </span>
                        <h6 class="text-white fw-bold mb-0 text-truncate"><?= htmlspecialchars($gf['titulo']) ?></h6>
                        <?php if (!empty($gf['creditos_autor'])): ?>
                            <span class="extra-small text-white text-opacity-75 d-block text-truncate">
                                <i class="bi bi-camera-fill text-warning me-1"></i><?= htmlspecialchars($gf['creditos_autor']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="extra-small text-white-50"><i class="bi bi-arrows-fullscreen me-1"></i> Ver en grande</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Modal Lightbox a Pantalla Completa para la Galería -->
<div class="modal fade" id="modalLightboxGaleria" tabindex="-1" aria-labelledby="modalLightboxTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg bg-teal-dark">
            <div class="modal-header border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-teal rounded-pill px-3 py-1 extra-small fw-semibold" id="lightboxCatBadge">
                        Turismo
                    </span>
                    <h5 class="modal-title fw-bold text-white mb-0 mt-1" id="lightboxTituloLugar">Lugar Turístico</h5>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0 position-relative text-center d-flex align-items-center justify-content-center modal-lightbox-viewport">
                <img id="lightboxImg" src="" alt="Foto turística" class="img-fluid object-fit-contain modal-lightbox-img">
            </div>
            <div class="modal-footer border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 modal-lightbox-footer">
                <div class="d-flex flex-column">
                    <span class="extra-small text-white-50"><i class="bi bi-geo-alt-fill text-warning me-1"></i> Trinidad, Beni</span>
                    <div id="lightboxCreditoContainer" class="extra-small text-white text-opacity-75 d-none">
                        <i class="bi bi-camera-fill text-warning me-1"></i>Foto: <span id="lightboxCreditoNombre" class="fw-semibold text-white"></span>
                        <a href="#" id="lightboxFuenteLink" target="_blank" rel="noopener noreferrer" class="ms-1 text-warning text-decoration-underline d-none">
                            Ver publicación <i class="bi bi-box-arrow-up-right extra-small"></i>
                        </a>
                    </div>
                </div>
                <a href="#" id="lightboxBtnFicha" class="btn btn-teal-action rounded-pill px-4 py-2 fw-semibold btn-sm shadow-sm">
                    <i class="bi bi-arrow-up-right me-1"></i> Ver Lugar Completo
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ==========================================================================
     MAPA REGIONAL INTERACTIVO DEL BENI (UN DEPARTAMENTO, CUATRO MARAVILLAS)
     ========================================================================== -->
<section class="seccion-mapa-regional-beni py-5 position-relative text-white overflow-hidden" id="regiones-beni">
    <div class="container py-4 position-relative z-index-2">
        <div class="row align-items-center gy-5">
            <!-- Columna Izquierda: Información Editorial y Llamado a la Acción -->
            <div class="col-lg-5 text-center text-lg-start">
                <span class="badge hero-badge-glass fw-bold px-3 py-2 rounded-pill text-uppercase mb-3 shadow-sm">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> Mapa Departamental
                </span>
                <h2 class="display-5 fw-extrabold mb-3 text-white titulo-mapa-regional">
                    Descubre el Beni, <br class="d-none d-lg-block">
                    <span class="text-teal-gradient">Tierra de Encanto</span>
                </h2>
                <p class="lead text-white-50 mb-4 fs-6 max-w-480">
                    Explora la riqueza natural, cultural e histórica de nuestro departamento a través de sus provincias. Conoce sus atractivos turísticos, tradiciones, ríos y reservas naturales en un solo lugar.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start mb-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold btn-sm d-inline-flex align-items-center gap-2 btn-explorar-mapa" onclick="document.getElementById('galeria').scrollIntoView({ behavior: 'smooth' });">
                        <span>Ver Galería Turística</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <!-- Mini Leyenda Rápida -->
                <div class="p-3 rounded-4 bg-dark bg-opacity-40 border border-white border-opacity-10 max-w-440">
                    <div class="extra-small text-white-50 text-uppercase fw-bold mb-2">
                        <i class="bi bi-hand-index-thumb me-1 text-warning"></i> Pasa el mouse o presiona sobre cada provincia para explorar:
                    </div>
                    <div class="d-flex flex-wrap gap-2 extra-small text-white-50">
                        <span><i class="bi bi-check2-circle text-success me-1"></i> 8 Provincias</span>
                        <span class="mx-1">•</span>
                        <span><i class="bi bi-camera-fill text-warning me-1"></i> Fotografías reales</span>
                        <span class="mx-1">•</span>
                        <span><i class="bi bi-compass-fill text-info me-1"></i> Destinos turísticos</span>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Mapa SVG Interactivo con Revelación Fotográfica -->
            <div class="col-lg-7 text-center">
                <div class="mapa-beni-wrapper mx-auto position-relative">
                    <!-- Mapa Geográfico Oficial y Estilizado del Departamento del Beni -->
                    <svg viewBox="0 0 520 520" class="mapa-beni-svg w-100 h-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <!-- Patrones fotográficos dinámicos administrables para cada una de las 8 provincias del Beni -->
                            <?php
                                $obtenerUrlFotoMapa = function($codigo, $fallbackImg) use ($fotosMapa, $baseUrl) {
                                    $foto = $fotosMapa[$codigo]['imagen'] ?? null;
                                    if (!empty($foto)) {
                                        if (str_starts_with($foto, 'hero-')) {
                                            return "{$baseUrl}/assets/img/{$foto}";
                                        }
                                        return "{$baseUrl}/imagen?f=" . urlencode($foto);
                                    }
                                    return $fallbackImg;
                                };

                                $imgVacaDiez = $obtenerUrlFotoMapa('vacadiez', "{$baseUrl}/imagen?f=lugar_083829094a198208_1792908099.jpg");
                                $imgBallivian = $obtenerUrlFotoMapa('ballivian', "{$baseUrl}/assets/img/hero-poster.webp");
                                $imgYacuma = $obtenerUrlFotoMapa('yacuma', "{$baseUrl}/imagen?f=lugar_93f0c8ef051a2dda_1790312766.png");
                                $imgMamore = $obtenerUrlFotoMapa('mamore', "{$baseUrl}/imagen?f=lugar_2cf4c818d0c03cd8_1790319478.jpg");
                                $imgItenez = $obtenerUrlFotoMapa('itenez', "{$baseUrl}/imagen?f=lugar_2cf4c818d0c03cd8_1790319478.jpg");
                                $imgCercado = $obtenerUrlFotoMapa('cercado', "{$baseUrl}/imagen?f=lugar_9998d4fdbb4f31c5_1791967750.jpg");
                                $imgMoxos = $obtenerUrlFotoMapa('moxos', "{$baseUrl}/imagen?f=lugar_8570b95699c5b12b_1790310877.png");
                                $imgMarban = $obtenerUrlFotoMapa('marban', "{$baseUrl}/imagen?f=lugar_8570b95699c5b12b_1790310877.png");
                            ?>
                            <!-- 1. Vaca Díez (Riberalta / Guayaramerín) -->
                            <pattern id="patronVacaDiez" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgVacaDiez) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 2. Gral. José Ballivián (Rurrenabaque / Pampas / Pilón Lajas) -->
                            <pattern id="patronBallivian" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgBallivian) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 3. Yacuma (Santa Rosa de Yacuma / Bufeos) -->
                            <pattern id="patronYacuma" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgYacuma) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 4. Mamoré (San Joaquín / San Ramón / Puerto Siles) -->
                            <pattern id="patronMamore" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMamore) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 5. Iténez (Magdalena / Baures / Río Iténez / Versalles) -->
                            <pattern id="patronItenez" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgItenez) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 6. Cercado (Trinidad Capital / Laguna Suárez / Loma Suárez) -->
                            <pattern id="patronCercado" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgCercado) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 7. Moxos (San Ignacio de Moxos / Laguna Isireri) -->
                            <pattern id="patronMoxos" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMoxos) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>
                            <!-- 8. Marbán (Loreto / San Andrés / Santuario de Loreto) -->
                            <pattern id="patronMarban" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <image href="<?= htmlspecialchars($imgMarban) ?>" x="0" y="0" width="1" height="1" preserveAspectRatio="xMidYMid slice" />
                            </pattern>

                            <!-- Sombra suave para el mapa completo -->
                            <filter id="sombraMapaBeni" x="-10%" y="-10%" width="120%" height="120%">
                                <feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#021417" flood-opacity="0.55" />
                            </filter>
                        </defs>

                        <!-- MAPA GEOGRÁFICO DEPARTAMENTO DEL BENI (8 PROVINCIAS ENCAJADAS) -->
                        <g filter="url(#sombraMapaBeni)">
                            
                            <!-- CAPA 1: CUERPO Y FORMAS DE LAS 8 PROVINCIAS -->
                            <g class="capa-cuerpos-provincias">
                                <!-- 1. Antonio Vaca Díez -->
                                <g class="region-beni-grupo" data-provincia="vacadiez" data-region="norte" tabindex="0" role="button" aria-label="Provincia Vaca Díez: Riberalta y Guayaramerín">
                                    <path class="region-path region-vacadiez" 
                                          d="M 189 12 L 195 12 L 197 14 L 198 29 L 202 34 L 202 43 L 208 52 L 208 61 L 200 76 L 200 80 L 204 86 L 204 99 L 210 101 L 213 116 L 220 116 L 222 118 L 225 138 L 219 140 L 161 140 L 155 142 L 131 143 L 111 143 L 102 141 L 84 141 L 75 143 L 75 139 L 70 132 L 72 129 L 77 128 L 79 120 L 84 120 L 89 117 L 92 103 L 95 98 L 102 93 L 108 94 L 111 91 L 122 91 L 122 89 L 126 88 L 127 81 L 132 77 L 134 71 L 138 69 L 138 66 L 142 62 L 144 56 L 143 48 L 146 45 L 154 45 L 154 47 L 157 47 L 157 40 L 166 39 L 170 36 L 170 33 L 165 30 L 166 24 L 179 23 L 189 12 Z" 
                                          fill="#c1bba4" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 2. Gral. José Ballivián -->
                                <g class="region-beni-grupo" data-provincia="ballivian" data-region="pampas" tabindex="0" role="button" aria-label="Provincia Gral. Ballivián: Rurrenabaque y San Borja">
                                    <path class="region-path region-ballivian" 
                                          d="M 84 141 L 125 143 L 122 151 L 125 168 L 118 179 L 113 183 L 104 206 L 104 284 L 106 285 L 106 302 L 104 303 L 102 314 L 102 345 L 104 347 L 104 367 L 102 368 L 102 428 L 87 442 L 78 444 L 72 432 L 68 430 L 66 423 L 61 418 L 59 407 L 50 395 L 47 387 L 33 381 L 31 376 L 33 362 L 29 356 L 21 351 L 21 348 L 26 341 L 24 336 L 20 333 L 20 329 L 26 325 L 25 320 L 23 319 L 23 313 L 25 311 L 23 292 L 26 289 L 29 289 L 27 280 L 32 276 L 32 272 L 38 266 L 36 251 L 43 245 L 45 238 L 52 234 L 56 225 L 61 223 L 66 211 L 70 207 L 71 193 L 69 188 L 72 182 L 70 181 L 69 174 L 74 170 L 73 152 L 76 148 L 75 143 L 84 141 Z" 
                                          fill="#f2e415" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 3. Yacuma -->
                                <g class="region-beni-grupo" data-provincia="yacuma" data-region="pampas" tabindex="0" role="button" aria-label="Provincia Yacuma: Santa Rosa del Yacuma">
                                    <path class="region-path region-yacuma" 
                                          d="M 202 139 L 219 140 L 221 145 L 220 151 L 214 154 L 216 163 L 214 174 L 219 184 L 223 187 L 222 196 L 226 200 L 226 212 L 224 213 L 225 220 L 222 229 L 213 230 L 214 238 L 210 246 L 210 257 L 206 265 L 205 279 L 216 292 L 218 299 L 223 305 L 223 311 L 215 314 L 210 313 L 197 316 L 186 347 L 178 355 L 170 359 L 171 366 L 169 372 L 171 373 L 171 376 L 167 380 L 166 389 L 158 396 L 158 399 L 154 404 L 142 404 L 141 406 L 134 407 L 119 420 L 107 427 L 102 427 L 102 368 L 104 367 L 102 314 L 104 303 L 106 302 L 106 285 L 104 284 L 104 206 L 113 183 L 118 179 L 125 168 L 122 151 L 125 143 L 131 142 L 202 139 Z" 
                                          fill="#ef66e6" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 4. Mamoré -->
                                <g class="region-beni-grupo" data-provincia="mamore" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Mamoré: San Joaquín">
                                    <path class="region-path region-mamore" 
                                          d="M 224 138 L 232 139 L 241 144 L 242 149 L 247 149 L 252 155 L 264 158 L 266 162 L 268 162 L 269 171 L 280 179 L 277 186 L 285 209 L 283 211 L 283 219 L 286 223 L 286 230 L 280 239 L 279 248 L 276 251 L 275 312 L 267 312 L 239 303 L 221 303 L 216 292 L 205 279 L 206 265 L 210 257 L 210 246 L 214 238 L 213 230 L 221 230 L 225 220 L 224 213 L 226 212 L 226 200 L 222 196 L 223 187 L 219 184 L 214 174 L 214 169 L 216 168 L 214 154 L 221 149 L 219 139 L 224 138 Z" 
                                          fill="#f2efbc" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 5. Iténez -->
                                <g class="region-beni-grupo" data-provincia="itenez" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Iténez: Magdalena, Baures y Versalles">
                                    <path class="region-path region-itenez" 
                                          d="M 323 173 L 339 177 L 344 184 L 348 185 L 354 191 L 363 193 L 368 191 L 376 192 L 382 203 L 389 207 L 393 207 L 403 221 L 413 220 L 419 225 L 427 226 L 430 229 L 440 228 L 442 230 L 451 230 L 455 234 L 455 239 L 461 248 L 469 250 L 475 257 L 475 262 L 480 262 L 482 264 L 483 262 L 490 262 L 493 259 L 497 259 L 499 266 L 492 271 L 481 273 L 474 278 L 470 278 L 459 285 L 446 288 L 422 299 L 387 310 L 386 312 L 366 317 L 362 321 L 358 321 L 356 324 L 349 326 L 339 327 L 333 323 L 275 312 L 276 251 L 279 248 L 280 239 L 286 230 L 286 223 L 283 219 L 283 211 L 285 209 L 277 183 L 279 180 L 285 180 L 286 178 L 292 178 L 294 181 L 310 180 L 323 173 Z" 
                                          fill="#e194bc" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 6. Cercado -->
                                <g class="region-beni-grupo" data-provincia="cercado" data-region="cercado" tabindex="0" role="button" aria-label="Provincia Cercado: Ciudad de Trinidad">
                                    <path class="region-path region-cercado" 
                                          d="M 224 302 L 239 303 L 267 312 L 277 312 L 280 314 L 295 315 L 306 319 L 333 323 L 339 326 L 340 330 L 319 352 L 317 352 L 314 359 L 302 361 L 294 366 L 282 368 L 278 371 L 258 374 L 238 383 L 231 383 L 229 382 L 229 374 L 225 362 L 230 352 L 233 335 L 231 333 L 231 326 L 227 322 L 227 313 L 222 310 L 222 303 L 224 302 Z" 
                                          fill="#57e261" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 7. Moxos -->
                                <g class="region-beni-grupo" data-provincia="moxos" data-region="misiones" tabindex="0" role="button" aria-label="Provincia Moxos: San Ignacio de Moxos">
                                    <path class="region-path region-moxos" 
                                          d="M 222 311 L 226 311 L 227 313 L 227 322 L 231 326 L 233 340 L 228 358 L 225 362 L 229 374 L 229 383 L 225 393 L 215 402 L 212 410 L 213 416 L 216 417 L 219 422 L 220 432 L 217 441 L 210 448 L 206 448 L 208 454 L 206 459 L 208 493 L 203 499 L 195 502 L 156 503 L 153 500 L 137 499 L 136 496 L 120 483 L 115 472 L 107 464 L 82 455 L 79 450 L 79 445 L 87 442 L 95 436 L 102 427 L 107 427 L 119 420 L 132 408 L 141 406 L 142 404 L 154 404 L 157 401 L 158 396 L 166 389 L 167 380 L 171 376 L 171 373 L 169 372 L 171 366 L 170 359 L 178 355 L 186 347 L 197 316 L 220 313 L 222 311 Z" 
                                          fill="#56e4dd" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>

                                <!-- 8. Marbán -->
                                <g class="region-beni-grupo" data-provincia="marban" data-region="cercado" tabindex="0" role="button" aria-label="Provincia Marbán: Loreto y San Andrés">
                                    <path class="region-path region-marban" 
                                          d="M 308 359 L 314 359 L 314 370 L 312 372 L 314 378 L 313 391 L 320 400 L 322 411 L 333 427 L 342 434 L 349 436 L 350 440 L 353 442 L 356 454 L 353 456 L 344 456 L 343 454 L 298 449 L 285 451 L 280 450 L 279 448 L 237 447 L 230 450 L 225 456 L 225 467 L 222 471 L 223 481 L 221 488 L 212 493 L 208 493 L 208 473 L 206 471 L 206 459 L 208 454 L 206 448 L 210 448 L 217 441 L 220 432 L 219 422 L 216 417 L 213 416 L 212 410 L 215 402 L 225 393 L 228 383 L 238 383 L 252 376 L 278 371 L 308 359 Z" 
                                          fill="#9eeea4" 
                                          stroke="#1e293b" 
                                          stroke-width="2" />
                                </g>
                            </g>

                            <!-- CAPA 2 (OVERLAY SUPERIOR ABSOLUTO): PINES Y ETIQUETAS FLOTANTES -->
                            <!-- Renderizado siempre después de todas las provincias para que jamás queden cubiertas -->
                            <g class="capa-overlays-provincias pointer-events-none">
                                <!-- 1. Vaca Díez -->
                                <g class="provincia-overlay" data-overlay="vacadiez">
                                    <g class="region-pin-btn" transform="translate(163, 95)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(85, 120)">
                                        <rect x="0" y="0" width="155" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="77" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Vaca Díez (Riberalta) ➔</text>
                                    </g>
                                </g>

                                <!-- 2. Gral. Ballivián -->
                                <g class="provincia-overlay" data-overlay="ballivian">
                                    <g class="region-pin-btn" transform="translate(76, 295)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(10, 325)">
                                        <rect x="0" y="0" width="155" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="77" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Gral. Ballivián (Pampas) ➔</text>
                                    </g>
                                </g>

                                <!-- 3. Yacuma -->
                                <g class="provincia-overlay" data-overlay="yacuma">
                                    <g class="region-pin-btn" transform="translate(154, 255)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(85, 285)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Yacuma & Santa Rosa ➔</text>
                                    </g>
                                </g>

                                <!-- 4. Mamoré -->
                                <g class="provincia-overlay" data-overlay="mamore">
                                    <g class="region-pin-btn" transform="translate(246, 225)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(180, 255)">
                                        <rect x="0" y="0" width="135" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="67" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Provincia Mamoré ➔</text>
                                    </g>
                                </g>

                                <!-- 5. Iténez -->
                                <g class="provincia-overlay" data-overlay="itenez">
                                    <g class="region-pin-btn" transform="translate(365, 252)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(295, 280)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Iténez & Magdalena ➔</text>
                                    </g>
                                </g>

                                <!-- 6. Cercado -->
                                <g class="provincia-overlay" data-overlay="cercado">
                                    <g class="region-pin-btn" transform="translate(267, 342)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(200, 372)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Cercado (Trinidad) ➔</text>
                                    </g>
                                </g>

                                <!-- 7. Moxos -->
                                <g class="provincia-overlay" data-overlay="moxos">
                                    <g class="region-pin-btn" transform="translate(165, 435)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(95, 465)">
                                        <rect x="0" y="0" width="145" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="72" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Moxos (San Ignacio) ➔</text>
                                    </g>
                                </g>

                                <!-- 8. Marbán -->
                                <g class="provincia-overlay" data-overlay="marban">
                                    <g class="region-pin-btn" transform="translate(275, 420)">
                                        <circle cx="0" cy="0" r="12" fill="#ffffff" class="pin-bg" />
                                        <text x="0" y="4" text-anchor="middle" font-size="14" font-weight="bold" fill="#0f3b43" class="pin-icon">+</text>
                                    </g>
                                    <g class="region-tag-label" transform="translate(205, 450)">
                                        <rect x="0" y="0" width="140" height="26" rx="13" fill="#ffffff" fill-opacity="0.95" class="tag-rect" />
                                        <text x="70" y="17" text-anchor="middle" font-size="10" font-weight="bold" fill="#0f172a">Marbán (Loreto) ➔</text>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tigre Amazónico / Jaguar Beniano (Panthera onca) Vigilante en la Esquina -->
    <div class="tigre-mapa-box d-none d-md-block" title="Tigre Amazónico / Jaguar (Panthera onca) - El rey de la fauna y las reservas del Beni">
        <svg viewBox="0 0 180 140" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Pelaje dorado/ámbar del Jaguar -->
                <linearGradient id="gradPielTigre" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#f59e0b" />
                    <stop offset="60%" stop-color="#d97706" />
                    <stop offset="100%" stop-color="#b45309" />
                </linearGradient>
                <!-- Hocico y mentón blanco crema -->
                <linearGradient id="gradHocicoTigre" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#fef3c7" />
                </linearGradient>
                <!-- Ojos ámbar / esmeralda brillante -->
                <linearGradient id="gradOjoTigre" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fef08a" />
                    <stop offset="60%" stop-color="#a3e635" />
                    <stop offset="100%" stop-color="#65a30d" />
                </linearGradient>
            </defs>

            <!-- Hojas de selva de fondo en la esquina -->
            <g opacity="0.75">
                <path d="M 120 140 C 135 105 160 85 180 80 C 180 115 160 135 120 140 Z" fill="#047857" />
                <path d="M 100 140 C 120 115 140 100 165 98 C 160 125 135 138 100 140 Z" fill="#10b981" opacity="0.6" />
            </g>

            <!-- Cola curvada y animada con anillos oscuros -->
            <g class="tigre-cola-animada">
                <path d="M 32 96 C 18 90 8 75 14 58 C 18 48 28 46 32 52 C 34 56 30 64 24 68 C 19 72 22 84 34 90 Z" fill="url(#gradPielTigre)" stroke="#78350f" stroke-width="0.8" />
                <!-- Manchas de la cola -->
                <circle cx="16" cy="62" r="3" fill="#1e1b18" />
                <circle cx="21" cy="74" r="3.2" fill="#1e1b18" />
                <circle cx="26" cy="85" r="3.5" fill="#1e1b18" />
                <!-- Punta oscura -->
                <path d="M 28 48 C 32 50 33 54 30 56 C 26 58 20 54 22 50 Z" fill="#1e1b18" />
            </g>

            <!-- Cuerpo del Jaguar en reposo felino -->
            <g class="tigre-cuerpo">
                <path d="M 30 96 C 30 76 48 65 72 65 C 102 65 120 74 135 90 C 142 98 145 112 144 125 C 138 128 50 130 30 125 C 24 116 28 104 30 96 Z" fill="url(#gradPielTigre)" />
                <!-- Rosetas / Manchas características del Jaguar en el lomo -->
                <g fill="#1e1b18">
                    <!-- Roseta 1 -->
                    <circle cx="52" cy="78" r="4.5" fill="none" stroke="#1e1b18" stroke-width="2.2" />
                    <circle cx="52" cy="78" r="1.5" />
                    <!-- Roseta 2 -->
                    <circle cx="74" cy="75" r="5" fill="none" stroke="#1e1b18" stroke-width="2.5" />
                    <circle cx="74" cy="75" r="1.8" />
                    <!-- Roseta 3 -->
                    <circle cx="95" cy="80" r="4.8" fill="none" stroke="#1e1b18" stroke-width="2.3" />
                    <circle cx="95" cy="80" r="1.6" />
                    <!-- Roseta 4 -->
                    <circle cx="62" cy="94" r="5.2" fill="none" stroke="#1e1b18" stroke-width="2.4" />
                    <circle cx="62" cy="94" r="1.8" />
                    <!-- Roseta 5 -->
                    <circle cx="86" cy="96" r="4.6" fill="none" stroke="#1e1b18" stroke-width="2.2" />
                    <circle cx="86" cy="96" r="1.6" />
                </g>
                <!-- Pata delantera visible descansando -->
                <path d="M 112 102 C 118 102 124 108 125 118 C 125 126 120 128 110 128 C 102 128 98 124 99 116 C 100 106 106 102 112 102 Z" fill="url(#gradPielTigre)" />
                <path d="M 125 116 C 132 116 138 120 138 128 C 135 130 115 130 112 128 C 112 124 118 116 125 116 Z" fill="#fef3c7" />
                <!-- Líneas de garras retráctiles -->
                <path d="M 122 123 L 122 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
                <path d="M 128 123 L 128 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
                <path d="M 134 123 L 134 128" stroke="#78350f" stroke-width="1.2" stroke-linecap="round" />
            </g>

            <!-- Cabeza majestuosa del Tigre Amazónico / Jaguar (Con animación sutil de mirada) -->
            <g class="tigre-cabeza-animada">
                <!-- Oreja Izquierda -->
                <path d="M 104 42 C 100 30 110 24 116 30 C 120 34 118 42 114 45 Z" fill="url(#gradPielTigre)" />
                <path d="M 106 40 C 104 33 110 28 113 32 C 115 35 114 40 111 42 Z" fill="#1e1b18" />
                <circle cx="110" cy="35" r="1.5" fill="#ffffff" opacity="0.9" />

                <!-- Oreja Derecha -->
                <path d="M 148 44 C 154 32 164 28 168 34 C 170 39 166 47 160 48 Z" fill="url(#gradPielTigre)" />
                <path d="M 152 42 C 156 34 162 32 164 36 C 165 40 162 45 158 46 Z" fill="#1e1b18" />
                <circle cx="160" cy="38" r="1.5" fill="#ffffff" opacity="0.9" />

                <!-- Cráneo y Mandíbula poderosa del felino -->
                <path d="M 108 52 C 104 68 112 90 134 94 C 156 94 168 76 166 54 C 164 42 148 38 135 38 C 122 38 110 44 108 52 Z" fill="url(#gradPielTigre)" />

                <!-- Marcas faciales y rosetas en la frente -->
                <path d="M 130 43 C 132 40 138 40 140 43" stroke="#1e1b18" stroke-width="2.2" stroke-linecap="round" />
                <path d="M 126 48 C 129 46 133 46 135 48" stroke="#1e1b18" stroke-width="2" stroke-linecap="round" />
                <path d="M 137 48 C 139 46 143 46 146 48" stroke="#1e1b18" stroke-width="2" stroke-linecap="round" />
                <circle cx="121" cy="52" r="2.2" fill="#1e1b18" />
                <circle cx="151" cy="53" r="2.2" fill="#1e1b18" />

                <!-- Ojos hipnóticos verdes-dorados de jaguar con parpadeo perfecto en su propio eje -->
                <!-- Ojo Izquierdo -->
                <g class="tigre-ojo-izq">
                    <ellipse cx="125" cy="58" rx="5.5" ry="4" fill="url(#gradOjoTigre)" stroke="#1e1b18" stroke-width="1.8" />
                    <ellipse cx="125" cy="58" rx="2" ry="3.5" fill="#0f172a" />
                    <circle cx="126" cy="56.5" r="0.9" fill="#ffffff" />
                    <path d="M 119 55 C 123 54 128 54 131 56" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                </g>

                <!-- Ojo Derecho -->
                <g class="tigre-ojo-der">
                    <ellipse cx="147" cy="59" rx="5.5" ry="4" fill="url(#gradOjoTigre)" stroke="#1e1b18" stroke-width="1.8" />
                    <ellipse cx="147" cy="59" rx="2" ry="3.5" fill="#0f172a" />
                    <circle cx="148" cy="57.5" r="0.9" fill="#ffffff" />
                    <path d="M 141 56 C 145 54 150 54 153 56" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                </g>

                <!-- Hocico blanco y mofletes de fiera -->
                <path d="M 128 66 C 122 72 120 84 135 84 C 150 84 150 72 144 66 C 140 62 132 62 128 66 Z" fill="url(#gradHocicoTigre)" />
                <!-- Nariz rosada oscura felina -->
                <path d="M 132 68 C 134 66 138 66 140 68 C 141 71 138 74 136 74 C 134 74 131 71 132 68 Z" fill="#be185d" />
                <!-- División del labio -->
                <path d="M 136 74 L 136 79" stroke="#1e1b18" stroke-width="1.6" stroke-linecap="round" />
                <path d="M 131 78 C 133 80 139 80 141 78" stroke="#1e1b18" stroke-width="1.4" stroke-linecap="round" />
                
                <!-- Puntos de bigotes -->
                <circle cx="128" cy="74" r="1" fill="#1e1b18" />
                <circle cx="126" cy="77" r="1" fill="#1e1b18" />
                <circle cx="130" cy="77" r="1" fill="#1e1b18" />
                <circle cx="144" cy="74" r="1" fill="#1e1b18" />
                <circle cx="142" cy="77" r="1" fill="#1e1b18" />
                <circle cx="146" cy="77" r="1" fill="#1e1b18" />

                <!-- Bigotes blancos finos y elegantes -->
                <path d="M 124 75 C 114 74 104 77 96 82" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 125 78 C 116 80 107 85 100 92" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 148 76 C 158 75 168 78 176 83" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
                <path d="M 147 79 C 156 82 165 87 172 94" stroke="#ffffff" stroke-width="1" stroke-linecap="round" opacity="0.85" />
            </g>

            <!-- Hojas de selva decorativas en primer plano -->
            <path d="M 140 140 C 150 120 170 115 180 118 C 178 135 160 140 140 140 Z" fill="#10b981" />
            <path d="M 155 140 C 162 128 175 125 180 128" stroke="#34d399" stroke-width="1" stroke-linecap="round" />
        </svg>
    </div>
</section>


<!-- Banner CTA Rediseñado con Identidad Amazónica y Paraba Beniana -->
<section class="container py-5" id="unirse">
    <div class="card border-0 rounded-5 p-4 p-md-5 hero-cta text-white shadow-lg overflow-hidden position-relative banner-negocio-amazonia">
        <!-- Paraba Beniana Animada (Vuelo Periódico) -->
        <div class="paraba-voladora-wrapper" aria-hidden="true">
            <svg class="paraba-svg" viewBox="0 0 160 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Ala trasera -->
                <g class="paraba-ala-trasera">
                    <path d="M52 28 C58 12 75 4 92 6 C84 14 74 24 58 32 Z" fill="#0284c7" />
                    <path d="M58 32 C70 24 82 14 92 6 C88 18 78 28 64 35 Z" fill="#0369a1" />
                </g>
                <!-- Cola larga y elegante -->
                <g class="paraba-cola">
                    <path d="M82 45 C108 55 138 68 155 78 C135 70 105 58 78 48 Z" fill="#0284c7" />
                    <path d="M82 46 C105 57 132 72 148 82 C130 72 100 59 78 49 Z" fill="#0d9488" />
                    <path d="M80 47 C98 56 120 68 135 76 C122 68 96 57 76 49 Z" fill="#f59e0b" />
                </g>
                <!-- Cuerpo y vientre amarillo dorado -->
                <g class="paraba-cuerpo">
                    <path d="M38 32 C45 38 58 46 80 46 C68 49 52 48 40 42 C34 38 32 34 38 32 Z" fill="#f59e0b" />
                    <path d="M42 34 C50 40 62 46 78 47 C66 49 54 48 44 42 Z" fill="#fbbf24" />
                    <path d="M30 26 C42 24 60 28 82 45 C65 42 48 35 34 28 Z" fill="#0369a1" />
                </g>
                <!-- Cabeza, mejilla y pico -->
                <g class="paraba-cabeza">
                    <path d="M30 26 C24 24 20 28 18 31 C22 34 28 35 34 32 C35 29 33 27 30 26 Z" fill="#0284c7" />
                    <path d="M22 28 C20 29 19 31 21 32 C23 33 26 33 27 31 C27 29 25 28 22 28 Z" fill="#ffffff" />
                    <circle cx="23" cy="30" r="1.5" fill="#0f172a" />
                    <path d="M22 33 C25 35 29 35 31 34 C28 36 24 36 22 33 Z" fill="#0d9488" />
                    <path d="M18 29 C14 29 11 31 10 34 C12 35 15 35 17 33 C18 32 18 30 18 29 Z" fill="#1e293b" />
                    <path d="M14 34 C12 35 11 36 12 37 C14 37 16 36 17 34 Z" fill="#0f172a" />
                </g>
                <!-- Ala delantera principal -->
                <g class="paraba-ala-delantera">
                    <path d="M46 30 C56 10 78 -2 98 0 C86 12 72 26 52 38 Z" fill="#0284c7" />
                    <path d="M52 34 C64 22 78 12 94 4 C84 16 70 28 54 40 Z" fill="#0369a1" />
                    <path d="M48 32 C58 24 70 18 82 12 C74 22 62 30 50 36 Z" fill="#f59e0b" />
                </g>
            </svg>
        </div>

        <!-- Silueta de Palmeras Tropicales Decorativa de Fondo -->
        <div class="palmera-silueta-bg" aria-hidden="true"></div>

        <div class="row align-items-center gy-4 position-relative z-index-2">
            <div class="col-lg-8 text-center text-lg-start">
                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill text-uppercase mb-3 shadow-xs">
                    <i class="fa-solid fa-briefcase me-1"></i> Directorio Comercial
                </span>
                <h2 class="display-6 fw-bold mb-2">¿Tienes un restaurante, hotel, bar o negocio en Trinidad?</h2>
                <p class="lead text-white-50 mb-3">
                    Conecta con cientos de turistas y clientes locales que exploran la ciudad todos los días. Aumenta tus ventas y recibe contactos directos a tu WhatsApp.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Presencia en Mapa GPS
                    </span>
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-whatsapp text-success me-1"></i> WhatsApp de tu Negocio
                    </span>
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1 extra-small">
                        <i class="bi bi-camera-fill text-warning me-1"></i> Fotos y Menú Digital
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end d-flex flex-column align-items-center align-items-lg-end justify-content-center">
                <!-- Mascota Paraba Barba Azul Beniana Posada en Rama (Versión Grande, Elegante y Animada) -->
                <div class="paraba-emblema-box mb-2 text-center text-lg-end position-relative z-index-5" title="Paraba Barba Azul (Ara glaucogularis) - Orgullo y Ave Emblema del Beni">
                    <svg class="paraba-emblema-svg" viewBox="0 0 170 145" width="180" height="152" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <!-- Degradados vibrantes para plumaje azul -->
                            <linearGradient id="gradPlumasAzulVibrante" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#38bdf8" />
                                <stop offset="35%" stop-color="#0284c7" />
                                <stop offset="85%" stop-color="#1d4ed8" />
                                <stop offset="100%" stop-color="#0f172a" />
                            </linearGradient>
                            <!-- Degradado oro beniano para pecho y vientre -->
                            <linearGradient id="gradPechoOroRico" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#fef08a" />
                                <stop offset="30%" stop-color="#fbbf24" />
                                <stop offset="85%" stop-color="#f59e0b" />
                                <stop offset="100%" stop-color="#d97706" />
                            </linearGradient>
                            <!-- Degradado turquesa para la barba -->
                            <linearGradient id="gradBarbaTurquesa" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#2dd4bf" />
                                <stop offset="100%" stop-color="#0d9488" />
                            </linearGradient>
                            <!-- Sombra de la rama y el ave -->
                            <filter id="sombraParabaPosada" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#02151b" flood-opacity="0.45" />
                            </filter>
                        </defs>

                        <!-- Rama tropical de madera amazónica con hojas selváticas -->
                        <g class="paraba-rama-soporte" filter="url(#sombraParabaPosada)">
                            <!-- Rama principal curvada -->
                            <path d="M12 118 C45 114 90 120 162 115" stroke="#78350f" stroke-width="6.5" stroke-linecap="round" />
                            <path d="M18 116 C55 113 95 118 158 114" stroke="#92400e" stroke-width="2.5" stroke-linecap="round" opacity="0.6" />
                            
                            <!-- Brotes y hojas tropicales verdes -->
                            <path d="M105 118 C118 110 134 108 146 110" stroke="#15803d" stroke-width="3.5" stroke-linecap="round" />
                            <path d="M118 110 C128 102 142 104 144 112 C136 116 124 115 118 110 Z" fill="#22c55e" />
                            <path d="M132 108 C144 98 158 102 160 110 C150 114 138 113 132 108 Z" fill="#16a34a" />
                            <path d="M120 110 C130 106 140 108 144 112" stroke="#86efac" stroke-width="1" stroke-linecap="round" />
                        </g>
                        
                        <!-- Cola larga y elegante en cascada -->
                        <g class="paraba-cola-posada">
                            <path d="M68 94 C78 114 92 134 100 144 C88 128 72 108 62 96 Z" fill="url(#gradPlumasAzulVibrante)" />
                            <path d="M64 94 C72 112 84 130 90 138 C82 124 70 106 60 96 Z" fill="#0d9488" />
                            <path d="M60 94 C66 106 74 118 80 128 C74 116 64 104 56 94 Z" fill="#f59e0b" />
                            <path d="M62 98 C66 116 75 136 78 142 C72 126 62 110 58 100 Z" fill="#ef4444" opacity="0.85" />
                        </g>
                        
                        <!-- Cuerpo y vientre amarillo oro beniano voluptuoso -->
                        <path d="M50 52 C62 52 82 62 82 86 C82 96 70 100 56 100 C44 100 38 88 38 74 C38 60 44 52 50 52 Z" fill="url(#gradPechoOroRico)" />
                        <path d="M54 58 C64 58 76 68 76 84 C76 92 68 96 58 96 C50 96 46 88 46 76 C46 66 50 58 54 58 Z" fill="#fef08a" opacity="0.45" />

                        <!-- Ala plegada con capas de plumas y animación de aleteo suave -->
                        <g class="paraba-ala-posada-animada">
                            <!-- Capa exterior azul brillante -->
                            <path d="M54 52 C78 54 98 68 100 90 C90 98 76 100 60 96 C54 84 52 68 54 52 Z" fill="url(#gradPlumasAzulVibrante)" />
                            <!-- Plumas secundarias en azul cobalto y turquesa -->
                            <path d="M58 56 C74 60 88 72 90 86 C84 92 72 94 60 90 Z" fill="#0284c7" />
                            <path d="M60 62 C72 66 82 74 84 84 C78 88 68 90 60 86 Z" fill="#0369a1" />
                            <path d="M62 68 C70 72 78 78 80 84 C74 86 68 87 62 84 Z" fill="#1e40af" />
                            <!-- Detalles dorados en el borde superior del hombro -->
                            <path d="M54 52 C60 52 68 56 70 60 C64 60 58 56 54 52 Z" fill="#f59e0b" />
                        </g>

                        <!-- Barba azul/turquesa icónica (Ara glaucogularis) -->
                        <path d="M48 54 C40 58 35 62 33 67 C35 72 43 71 48 68 Z" fill="url(#gradBarbaTurquesa)" />
                        
                        <!-- Cabeza y corona azul cobalto -->
                        <path d="M48 54 C40 48 40 37 48 30 C56 22 68 25 71 32 C74 40 68 49 58 54 Z" fill="url(#gradPlumasAzulVibrante)" />
                        
                        <!-- Rostro blanco con dibujo facial y ojo expresivo -->
                        <path d="M46 32 C49 29 57 29 59 33 C61 37 58 43 52 43 C46 43 45 37 46 32 Z" fill="#ffffff" />
                        <circle cx="53" cy="35" r="2.8" fill="#0f172a" />
                        <circle cx="54" cy="34" r="1" fill="#ffffff" />
                        <!-- Rayitas faciales del plumaje -->
                        <path d="M48 37 C50 38 53 38 55 37" stroke="#cbd5e1" stroke-width="0.7" stroke-linecap="round" />
                        <path d="M48 40 C50 41 53 41 55 40" stroke="#cbd5e1" stroke-width="0.7" stroke-linecap="round" />

                        <!-- Pico curvo negro característico -->
                        <path d="M46 34 C38 36 32 42 30 49 C34 50 42 49 46 43 C47 40 47 37 46 34 Z" fill="#1e293b" />
                        <path d="M40 47 C35 48 33 51 34 53 C38 53 42 51 44 48 Z" fill="#0f172a" />

                        <!-- Patitas firmes sujetando la rama -->
                        <path d="M54 98 C53 104 51 108 50 118" stroke="#334155" stroke-width="4.5" stroke-linecap="round" />
                        <path d="M62 98 C62 104 62 108 61 118" stroke="#334155" stroke-width="4.5" stroke-linecap="round" />
                        <!-- Uñas aferradas a la corteza -->
                        <path d="M47 118 C50 120 54 119 55 117" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M58 118 C61 120 65 119 66 117" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                    <div class="text-warning fw-bold text-uppercase mt-1 font-xs-76 tracking-wide-06">
                        <i class="fa-solid fa-feather-pointed me-1"></i> Paraba Barba Azul
                    </div>
                </div>

                <a href="<?= htmlspecialchars($baseUrl) ?>/solicitar-incorporacion" class="btn btn-warning btn-cta-negocio btn-lg rounded-pill fw-bold px-4 py-3 shadow-sm">
                    <i class="bi bi-send-check-fill me-1"></i> Solicitar Publicación
                </a>
            </div>
        </div>
    </div>
</section>
