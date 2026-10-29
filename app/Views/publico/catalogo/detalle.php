<?php
    $fotos = $fotografias ?? [];
    if (empty($fotos) && !empty($lugar['imagen'])) {
        $fotos = [['nombre_archivo' => $lugar['imagen']]];
    }
    $variasFotos = count($fotos) > 1;
    $queryMaps = urlencode(!empty($mapa) ? $mapa['coordenadas'] : $lugar['nombre'] . ', Trinidad, Beni, Bolivia');
    $esRadiomovil = (int)($lugar['id_categoria'] ?? 0) === 9 || mb_stripos($lugar['nombre'], 'radio') !== false || mb_stripos($lugar['nombre'], 'taxi') !== false;

    // Puntos para mapa interactivo de paradas en la ficha del sindicato
    $puntosMapaDetalle = [];
    if ($esRadiomovil) {
        $waLugar = preg_replace('/\D/', '', $lugar['whatsapp_contacto'] ?? '');
        if (strlen($waLugar) === 8) $waLugar = '591' . $waLugar;
        $telLugar = preg_replace('/[^\d+]/', '', $lugar['telefono_contacto'] ?? '');

        // 1. Central
        if (!empty($lugar['latitud']) && !empty($lugar['longitud'])) {
            $puntosMapaDetalle[] = [
                'id_parada'    => 0,
                'nombre_parada'=> 'Central Principal',
                'tipo_parada'  => 'CENTRAL',
                'direccion'    => $lugar['direccion'] ?: 'Central de Despacho',
                'latitud'      => (float)$lugar['latitud'],
                'longitud'     => (float)$lugar['longitud'],
                'telefono'     => $telLugar,
                'whatsapp'     => $waLugar,
                'es_central'   => true
            ];
        }

        // 2. Paradas Secundarias
        foreach ($paradasTransporte ?? [] as $par) {
            if (!empty($par['latitud']) && !empty($par['longitud'])) {
                $waPar = preg_replace('/\D/', '', $par['whatsapp_directo'] ?: $waLugar);
                if (strlen($waPar) === 8) $waPar = '591' . $waPar;
                $telPar = preg_replace('/[^\d+]/', '', $par['telefono_directo'] ?: $telLugar);
                $puntosMapaDetalle[] = [
                    'id_parada'    => (int)$par['id_parada'],
                    'nombre_parada'=> $par['nombre_parada'],
                    'tipo_parada'  => $par['tipo_parada'],
                    'direccion'    => $par['direccion'] ?: ($par['referencia'] ?: 'Trinidad, Beni'),
                    'latitud'      => (float)$par['latitud'],
                    'longitud'     => (float)$par['longitud'],
                    'telefono'     => $telPar,
                    'whatsapp'     => $waPar,
                    'es_central'   => false
                ];
            }
        }
    }
?>
<div class="container py-3 my-2">
    <!-- Migas de pan de navegación -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($baseUrl) ?>/" class="text-teal text-decoration-none"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($baseUrl) ?>/#explorar" class="text-teal text-decoration-none">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($lugar['nombre']) ?></li>
        </ol>
    </nav>

    <!-- 1. HERO FOTOGRÁFICO PANORÁMICO (Estilo Keliauk Lietuvoje) -->
    <div class="lugar-panoramico-hero mb-0">
        <?php if (!empty($fotos)): ?>
            <div id="carruselLugar" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500" role="region" aria-roledescription="carrusel" aria-label="Fotografías de <?= htmlspecialchars($lugar['nombre']) ?>">
                <?php if ($variasFotos): ?>
                    <div class="carousel-indicators mb-4 pb-2">
                        <?php foreach ($fotos as $indice => $foto): ?>
                            <button type="button" data-bs-target="#carruselLugar" data-bs-slide-to="<?= $indice ?>"<?= $indice === 0 ? ' class="active" aria-current="true"' : '' ?> aria-label="Fotografía <?= $indice + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="carousel-inner">
                    <?php foreach ($fotos as $indice => $foto): ?>
                        <div class="carousel-item<?= $indice === 0 ? ' active' : '' ?>">
                            <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($foto['nombre_archivo']) ?>"
                                 alt="<?= htmlspecialchars($lugar['nombre']) ?> — fotografía <?= $indice + 1 ?> de <?= count($fotos) ?>"
                                 class="d-block w-100 object-fit-cover place-detail-photo"
                                 <?= $indice === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy" decoding="async"' ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($variasFotos): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carruselLugar" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark bg-opacity-50 rounded-circle p-3" aria-hidden="true"></span>
                        <span class="visually-hidden">Fotografía anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carruselLugar" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark bg-opacity-50 rounded-circle p-3" aria-hidden="true"></span>
                        <span class="visually-hidden">Fotografía siguiente</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="p-5 text-center hero-beni text-white detalle-hero-fallback">
                <div class="place-icon-bubble mx-auto mb-3 shadow">
                    <i class="bi <?= htmlspecialchars($lugar['categoria_icono'] ?? 'bi-geo-alt') ?> fs-1 text-teal" aria-hidden="true"></i>
                </div>
                <h3 class="h4 fw-bold mb-1"><?= htmlspecialchars($lugar['nombre']) ?></h3>
                <p class="mb-0 text-white-50">Fotografías no disponibles por el momento</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. CABECERA FLOTANTE CON CURVA ORGÁNICA SUPERPUESTA AL HERO -->
    <header class="lugar-header-flotante p-4 p-md-5 mb-4 position-relative">
        <!-- Barra Superior de Badges y Botones de Acción -->
        <div class="lugar-header-top-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <!-- Badges Informativos (Municipio, Categoría, Estado Operativo, Distancia) -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (!empty($lugar['municipio'])): ?>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold shadow-2xs">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($lugar['municipio']) ?>, Beni
                    </span>
                <?php endif; ?>
                <?php
                    $catIcono = $lugar['categoria_icono'] ?? 'bi-geo-alt';
                    $catIconoClass = str_starts_with($catIcono, 'fa-') ? htmlspecialchars($catIcono) : 'bi ' . htmlspecialchars($catIcono);
                ?>
                <span class="badge bg-light text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold shadow-2xs">
                    <i class="<?= $catIconoClass ?> me-1" aria-hidden="true"></i> <?= htmlspecialchars($lugar['categoria'] ?? 'Destino') ?>
                </span>
                <?php
                    $evaluacionHorario = \App\Services\HorarioHelper::evaluarEstado(
                        $lugar['horario_atencion'] ?? null,
                        $lugar['estado_operativo'] ?? null,
                        $lugar['aviso_temporal_motivo'] ?? null,
                        $lugar['aviso_temporal_reapertura'] ?? null
                    );
                ?>
                <?php if ($evaluacionHorario): ?>
                    <span class="badge <?= $evaluacionHorario['badge'] ?> rounded-pill px-3 py-2 fw-bold shadow-2xs">
                        <?= htmlspecialchars($evaluacionHorario['texto']) ?>
                    </span>
                <?php endif; ?>
                <span id="badgeDistanciaDetalle" class="badge bg-danger text-white shadow-sm rounded-pill px-3 py-2 fw-bold d-none" aria-live="polite"></span>
            </div>
            
            <!-- Botones de Acción (Itinerario / Añadir a mi ruta, Compartir) -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 shadow-none"
                        data-btn-favorito
                        data-id="<?= (int)$lugar['id_lugar'] ?>"
                        data-nombre="<?= htmlspecialchars($lugar['nombre']) ?>"
                        data-slug="<?= htmlspecialchars($lugar['slug']) ?>"
                        data-categoria="<?= htmlspecialchars($lugar['categoria'] ?? '') ?>"
                        data-imagen="<?= htmlspecialchars($lugar['imagen'] ?? '') ?>"
                        data-municipio="<?= htmlspecialchars($lugar['municipio'] ?? '') ?>"
                        data-url="<?= htmlspecialchars($ogMeta['url'] ?? '') ?>">
                    <i class="bi bi-heart"></i>
                    <span class="label-favorito small fw-semibold">Añadir a mi itinerario</span>
                </button>

                <button type="button" class="btn btn-teal-action btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm"
                        data-btn-compartir
                        data-nombre="<?= htmlspecialchars($lugar['nombre']) ?>"
                        data-direccion="<?= htmlspecialchars($lugar['direccion'] ?? '') ?>"
                        data-coordenadas="<?= htmlspecialchars($lugar['coordenadas_gps'] ?? ($lugar['latitud'] && $lugar['longitud'] ? $lugar['latitud'].','.$lugar['longitud'] : '')) ?>"
                        data-url="<?= htmlspecialchars($ogMeta['url'] ?? '') ?>"
                        title="Compartir este lugar">
                    <i class="bi bi-share-fill"></i>
                    <span class="small fw-semibold">Compartir</span>
                </button>
            </div>
        </div>

        <h1 class="lugar-header-titulo mb-2"><?= htmlspecialchars($lugar['nombre']) ?></h1>

        <?php if (!empty($lugar['direccion'])): ?>
            <p class="text-muted mb-0 small">
                <i class="bi bi-pin-map-fill text-teal me-1"></i> <?= htmlspecialchars($lugar['direccion']) ?>
                <?php if (!empty($lugar['referencia_ubicacion'])): ?>
                    <span class="text-secondary ms-1">(&bull; <?= htmlspecialchars($lugar['referencia_ubicacion']) ?>)</span>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </header>

    <!-- 3. CUERPO DE CONTENIDO EN 2 COLUMNAS (Narrativa a la izquierda, Servicios/Mapa a la derecha) -->
    <div class="row g-4 g-lg-5 align-items-start">
        <!-- COLUMNA PRINCIPAL (IZQUIERDA) -->
        <main class="col-lg-7">
            <!-- Aviso de cierre temporal si aplica -->
            <?php if (!empty($lugar['estado_operativo']) && $lugar['estado_operativo'] === 'CERRADO_TEMPORALMENTE'): ?>
                <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 mb-4" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                        <div>
                            <h2 class="h6 fw-bold mb-1">Cerrado Temporalmente</h2>
                            <p class="small mb-0"><?= htmlspecialchars($lugar['aviso_temporal_motivo'] ?: 'Este establecimiento se encuentra cerrado temporalmente.') ?></p>
                            <?php if (!empty($lugar['aviso_temporal_reapertura'])): ?>
                                <p class="extra-small text-muted mt-2 mb-0">Fecha estimada de reapertura: <strong><?= date('d/m/Y', strtotime($lugar['aviso_temporal_reapertura'])) ?></strong></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tarjeta: Descripción -->
            <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 card-detalle-seccion" aria-labelledby="secDescripcion">
                <h2 id="secDescripcion" class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-text-paragraph text-teal"></i> Descripción
                </h2>
                <div class="text-secondary lh-lg detalle-desc-text">
                    <?= htmlspecialchars($lugar['descripcion'] ?? 'No hay una descripción detallada disponible.') ?>
                </div>
            </section>

            <!-- Tarjeta: Horarios de atención -->
            <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 card-detalle-seccion" aria-labelledby="secHorarios">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h2 id="secHorarios" class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-fill text-warning"></i> Horarios de Atención
                    </h2>
                    <?php if ($evaluacionHorario): ?>
                        <span class="badge <?= $evaluacionHorario['badge'] ?> rounded-pill px-3 py-1 extra-small fw-bold">
                            <?= htmlspecialchars($evaluacionHorario['texto']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($lugar['horario_atencion'])): ?>
                    <p class="text-secondary mb-0 detalle-horario-text"><?= htmlspecialchars($lugar['horario_atencion']) ?></p>
                <?php else: ?>
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Horario de atención habitual o sujeto a consulta directa.</p>
                <?php endif; ?>
            </section>

            <!-- Tarjeta: Contacto y Enlaces Directos -->
            <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 card-detalle-seccion" aria-labelledby="secContacto">
                <h2 id="secContacto" class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill text-teal"></i> Contacto y Comunicación Directa
                </h2>
                <p class="small text-muted mb-4">Comunícate directamente con los encargados de este lugar para consultas, reservas o información en tiempo real:</p>

                <div class="d-flex flex-wrap gap-2">
                    <?php
                        $whatsappRaw = preg_replace('/\D/', '', $lugar['whatsapp_contacto'] ?? '');
                        if (strlen($whatsappRaw) === 8) $whatsappRaw = '591' . $whatsappRaw;
                    ?>
                    <?php if (!empty($whatsappRaw)): ?>
                        <?php if ($esRadiomovil): ?>
                            <!-- Para Radiomóviles: Abre Asistente Rápido de Solicitud de Móvil -->
                            <button type="button" class="btn btn-teal-action rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalPedirRadiomovil">
                                <i class="bi bi-taxi-front-fill fs-5 text-warning"></i> Pedir Móvil por WhatsApp
                            </button>
                        <?php else: ?>
                            <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= rawurlencode("Hola, me comunico desde la guía turística BeniTours para consultar sobre {$lugar['nombre']}.") ?>"
                               target="_blank" rel="noopener noreferrer"
                               onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'WHATSAPP')"
                               class="btn btn-teal-action rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-whatsapp fs-5"></i> Contactar por WhatsApp
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php
                        $esEmergenciaSalud = (str_contains(mb_strtolower($lugar['categoria'] ?? '', 'UTF-8'), 'salud') || str_contains(mb_strtolower($lugar['categoria'] ?? '', 'UTF-8'), 'hospital'));
                        $esEmergenciaPolicia = (str_contains(mb_strtolower($lugar['categoria'] ?? '', 'UTF-8'), 'polic') || str_contains(mb_strtolower($lugar['categoria'] ?? '', 'UTF-8'), 'seguridad'));
                        $esEmergencia = $esEmergenciaSalud || $esEmergenciaPolicia;
                    ?>
                    <?php if ($esEmergencia && !empty($lugar['telefono_contacto'])): ?>
                        <a href="tel:<?= preg_replace('/[^\d+]/', '', $lugar['telefono_contacto']) ?>"
                           onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'LLAMADA')"
                           class="btn <?= $esEmergenciaSalud ? 'btn-danger' : 'btn-primary' ?> rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm text-white">
                            <i class="bi bi-telephone-outbound-fill"></i> Llamar a Emergencias (<?= htmlspecialchars($lugar['telefono_contacto']) ?>)
                        </a>
                    <?php elseif (!empty($lugar['telefono_contacto'])): ?>
                        <a href="tel:<?= preg_replace('/[^\d+]/', '', $lugar['telefono_contacto']) ?>"
                           onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'LLAMADA')"
                           class="btn btn-outline-teal rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($lugar['telefono_contacto']) ?>
                        </a>
                    <?php endif; ?>

                    <!-- Botón Compartir Ficha -->
                    <button type="button" id="btnCompartirLugar" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-share-fill"></i> Compartir
                    </button>
                </div>
            </section>

            <!-- Tarjeta: Promociones Activas (Debajo de Contacto) -->
            <?php if (!empty($promociones)): ?>
                <?php $totalPromos = count($promociones); ?>
                <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 card-detalle-seccion" aria-labelledby="tituloPromos">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 id="tituloPromos" class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-warning"></i> Promociones Activas
                        </h2>
                        <?php if ($totalPromos > 1): ?>
                            <span class="badge bg-teal-subtle text-teal rounded-pill extra-small px-2 py-1 fw-semibold">
                                <?= $totalPromos ?> disponibles
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- En modo computadora (desktop d-none d-lg-block): todas las promociones en cuadrícula horizontal -->
                    <div class="d-none d-lg-block">
                        <div class="row g-4">
                            <?php foreach ($promociones as $pr): ?>
                                <?php
                                    $colClass = $totalPromos === 1 ? 'col-12' : ($totalPromos === 2 ? 'col-md-6' : 'col-md-6 col-xl-4');
                                    $textoWaDetalle = rawurlencode("Hola, vi su promoción '{$pr['titulo']}' (" . ($pr['descuento_texto'] ?: 'Oferta') . ") en BeniTours y quisiera aprovecharla en {$lugar['nombre']}.");
                                ?>
                                <div class="<?= $colClass ?>">
                                    <div class="p-3 rounded-4 border bg-warning-subtle border-warning-subtle shadow-2xs h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <?php if (!empty($pr['imagen'])): ?>
                                                <div class="mb-3 rounded-3 overflow-hidden position-relative bg-dark shadow-2xs" style="height: 220px;">
                                                    <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($pr['imagen']) ?>" 
                                                         alt="<?= htmlspecialchars($pr['titulo']) ?>"
                                                         class="w-100 h-100 object-fit-contain" loading="lazy">
                                                    <?php if (!empty($pr['descuento_texto'])): ?>
                                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 extra-small fw-bold position-absolute top-0 end-0 m-2 shadow-sm">
                                                            <?= htmlspecialchars($pr['descuento_texto']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="extra-small text-muted mb-2 text-center">
                                                <i class="bi bi-clock-history text-danger me-1"></i> Vigente hasta: <strong><?= date('d/m/Y', strtotime($pr['fecha_fin'])) ?></strong>
                                            </div>
                                            <?php if (!empty($whatsappRaw)): ?>
                                                <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $textoWaDetalle ?>" 
                                                   target="_blank" rel="noopener noreferrer"
                                                   class="btn btn-teal-action rounded-pill w-100 fw-bold py-2 small d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                                    <i class="bi bi-whatsapp fs-5"></i> Aprovecha la Oferta
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- En modo celular / tablet (< lg): Carrusel manual (data-bs-ride="false" sin auto-play) de a 1 promoción -->
                    <div class="d-lg-none">
                        <?php if ($totalPromos > 1): ?>
                            <div id="carouselPromocionesDetalle" class="carousel slide detalle-promo-carousel position-relative" data-bs-ride="false">
                                <!-- Indicadores -->
                                <div class="carousel-indicators mb-1 position-relative mt-2">
                                    <?php foreach ($promociones as $idxP => $pr): ?>
                                        <button type="button" data-bs-target="#carouselPromocionesDetalle" data-bs-slide-to="<?= $idxP ?>" 
                                                class="<?= $idxP === 0 ? 'active' : '' ?>" 
                                                aria-current="<?= $idxP === 0 ? 'true' : 'false' ?>" 
                                                aria-label="Promoción <?= $idxP + 1 ?>"></button>
                                    <?php endforeach; ?>
                                </div>

                                <div class="carousel-inner rounded-4">
                                    <?php foreach ($promociones as $idxP => $pr): ?>
                                        <?php
                                            $textoWaDetalle = rawurlencode("Hola, vi su promoción '{$pr['titulo']}' (" . ($pr['descuento_texto'] ?: 'Oferta') . ") en BeniTours y quisiera aprovecharla en {$lugar['nombre']}.");
                                        ?>
                                        <div class="carousel-item <?= $idxP === 0 ? 'active' : '' ?>">
                                            <div class="p-3 rounded-4 border bg-warning-subtle border-warning-subtle shadow-2xs">
                                                <?php if (!empty($pr['imagen'])): ?>
                                                    <div class="mb-3 rounded-3 overflow-hidden position-relative bg-dark shadow-2xs" style="height: 220px;">
                                                        <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($pr['imagen']) ?>" 
                                                             alt="<?= htmlspecialchars($pr['titulo']) ?>"
                                                             class="w-100 h-100 object-fit-contain" loading="lazy">
                                                        <?php if (!empty($pr['descuento_texto'])): ?>
                                                            <span class="badge bg-danger text-white rounded-pill px-3 py-1 extra-small fw-bold position-absolute top-0 end-0 m-2 shadow-sm">
                                                                <?= htmlspecialchars($pr['descuento_texto']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="extra-small text-muted mb-2 text-center">
                                                    <i class="bi bi-clock-history text-danger me-1"></i> Vigente hasta: <strong><?= date('d/m/Y', strtotime($pr['fecha_fin'])) ?></strong>
                                                </div>
                                                <?php if (!empty($whatsappRaw)): ?>
                                                    <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $textoWaDetalle ?>" 
                                                       target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-teal-action rounded-pill w-100 fw-bold py-2 small d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                                        <i class="bi bi-whatsapp fs-5"></i> Aprovecha la Oferta
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Controles táctiles / flechas manuales -->
                                <button class="carousel-control-prev promo-det-ctrl" type="button" data-bs-target="#carouselPromocionesDetalle" data-bs-slide="prev" aria-label="Anterior">
                                    <span class="carousel-control-prev-icon rounded-circle bg-dark bg-opacity-75 p-2" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next promo-det-ctrl" type="button" data-bs-target="#carouselPromocionesDetalle" data-bs-slide="next" aria-label="Siguiente">
                                    <span class="carousel-control-next-icon rounded-circle bg-dark bg-opacity-75 p-2" aria-hidden="true"></span>
                                </button>
                            </div>
                        <?php else: ?>
                            <!-- 1 sola promo en móvil -->
                            <?php 
                                $pr = $promociones[0];
                                $textoWaDetalle = rawurlencode("Hola, vi su promoción '{$pr['titulo']}' (" . ($pr['descuento_texto'] ?: 'Oferta') . ") en BeniTours y quisiera aprovecharla en {$lugar['nombre']}.");
                            ?>
                            <div class="p-3 rounded-4 border bg-warning-subtle border-warning-subtle shadow-2xs">
                                <?php if (!empty($pr['imagen'])): ?>
                                    <div class="mb-3 rounded-3 overflow-hidden position-relative bg-dark shadow-2xs" style="height: 220px;">
                                        <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($pr['imagen']) ?>" 
                                             alt="<?= htmlspecialchars($pr['titulo']) ?>"
                                             class="w-100 h-100 object-fit-contain" loading="lazy">
                                        <?php if (!empty($pr['descuento_texto'])): ?>
                                            <span class="badge bg-danger text-white rounded-pill px-3 py-1 extra-small fw-bold position-absolute top-0 end-0 m-2 shadow-sm">
                                                <?= htmlspecialchars($pr['descuento_texto']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="extra-small text-muted mb-2 text-center">
                                    <i class="bi bi-clock-history text-danger me-1"></i> Vigente hasta: <strong><?= date('d/m/Y', strtotime($pr['fecha_fin'])) ?></strong>
                                </div>
                                <?php if (!empty($whatsappRaw)): ?>
                                    <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $textoWaDetalle ?>" 
                                       target="_blank" rel="noopener noreferrer"
                                       class="btn btn-teal-action rounded-pill w-100 fw-bold py-2 small d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                        <i class="bi bi-whatsapp fs-5"></i> Aprovecha la Oferta
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Tarjeta: Menú / Carta de Productos (Debajo de Promociones) -->
            <?php if (!empty($lugar['enlace_menu'])): ?>
                <?php
                    $urlMenu = $lugar['enlace_menu'];
                    $urlEmbedMenu = $urlMenu;
                    $esGoogleDrive = str_contains($urlMenu, 'drive.google.com');
                    if ($esGoogleDrive) {
                        $urlEmbedMenu = preg_replace('/(\/view|\/edit)(\?.*)?$/', '/preview', $urlMenu);
                        if (!str_contains($urlEmbedMenu, '/preview')) {
                            $urlEmbedMenu = rtrim($urlMenu, '/') . '/preview';
                        }
                    }
                    $esImagen = (bool)preg_match('/\.(jpe?g|png|webp|gif)(\?.*)?$/i', $urlMenu);
                ?>
                <section class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 card-detalle-seccion" aria-labelledby="tituloMenu">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 id="tituloMenu" class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-book-half text-warning"></i> Menú / Carta Digital
                        </h2>
                        <a href="<?= htmlspecialchars($lugar['enlace_menu']) ?>" target="_blank" rel="noopener noreferrer" 
                           onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MENU')"
                           class="btn btn-xs btn-outline-teal rounded-pill px-3 py-1 small fw-semibold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Pantalla completa
                        </a>
                    </div>

                    <?php if ($esImagen): ?>
                        <div class="rounded-4 overflow-hidden border text-center bg-light p-2 mb-2">
                            <img src="<?= htmlspecialchars($urlEmbedMenu) ?>" 
                                 alt="Menú de <?= htmlspecialchars($lugar['nombre']) ?>" 
                                 class="img-fluid rounded-3 w-100 detalle-menu-img-preview" 
                                 loading="lazy">
                        </div>
                    <?php elseif ($esGoogleDrive): ?>
                        <div class="menu-drive-card rounded-4 border text-center p-4">
                            <span class="menu-drive-icon"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                            <h3 class="h6 fw-bold text-dark mt-2 mb-1">Menú en Google Drive</h3>
                            <p class="extra-small text-muted mb-3">Toca abajo para abrir el documento oficial con todos los precios:</p>
                            <a href="<?= htmlspecialchars($urlMenu, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
                               onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MENU')"
                               class="btn btn-success btn-sm rounded-pill px-4 fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-2"></i> Abrir carta
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="menu-embed-container rounded-4 overflow-hidden border shadow-2xs detalle-menu-embed-box" style="height: 480px;">
                            <iframe src="<?= htmlspecialchars($urlEmbedMenu, ENT_QUOTES, 'UTF-8') ?>"
                                    title="Menú digital de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    width="100%" height="100%" 
                                    class="border-0"
                                    loading="lazy"
                                    allow="autoplay"
                                    allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>

        <!-- COLUMNA LATERAL (DERECHA): UBICACIÓN, REDES SOCIALES Y TARIFAS -->
        <aside class="col-lg-5 place-detail-sidebar-sticky">
            <!-- Tarjeta: Ubicación / Mapa Oficial -->
            <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 card-detalle-seccion" aria-labelledby="tituloUbicacion">
                <h2 id="tituloUbicacion" class="h5 fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="bi <?= $esRadiomovil ? 'bi-taxi-front-fill text-warning' : 'bi-geo-alt-fill text-danger' ?>"></i>
                    <?= $esRadiomovil ? 'Mapa de Central y Paradas Oficiales' : 'Ubicación y Cómo Llegar' ?>
                </h2>
                <p class="mb-1 text-dark fw-semibold small"><?= htmlspecialchars($lugar['direccion'] ?? '') ?></p>
                <?php if (!empty($lugar['referencia_ubicacion'])): ?>
                    <p class="text-muted extra-small mb-3"><i class="bi bi-info-circle me-1"></i> Referencia: <?= htmlspecialchars($lugar['referencia_ubicacion']) ?></p>
                <?php endif; ?>

                <?php if ($esRadiomovil && !empty($puntosMapaDetalle)): ?>
                    <!-- Mapa Interactivo Leaflet con todas las paradas del Sindicato -->
                    <div class="place-map rounded-4 overflow-hidden border mt-2 mb-3 detalle-mapa-canvas-box">
                        <div id="mapaDetalleSindicatoDesktop" class="detalle-mapa-canvas z-index-1"></div>
                    </div>
                    <div class="extra-small text-muted mb-3">
                        <i class="bi bi-info-circle text-teal me-1"></i> Mostrando <strong><?= count($puntosMapaDetalle) ?> puntos oficiales</strong> (Central y Casetas). Clic en cualquier marcador para detalles.
                    </div>
                <?php elseif (!empty($mapa)): ?>
                    <div class="place-map rounded-4 overflow-hidden border mt-2 mb-3 shadow-2xs">
                        <iframe src="<?= htmlspecialchars($mapa['url'], ENT_QUOTES, 'UTF-8') ?>"
                                title="Mapa de ubicación de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                width="100%" height="280" loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mt-2 mb-3">La ubicación satelital exacta todavía no está disponible.</p>
                <?php endif; ?>

                <div class="d-flex flex-column gap-2 mb-3">
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $queryMaps ?>" target="_blank" rel="noopener noreferrer" 
                       onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MAPA')"
                       class="btn btn-teal-action rounded-pill w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-xs">
                        <i class="bi bi-map-fill"></i> Abrir en Google Maps
                    </a>

                    <button type="button" 
                            class="btn btn-outline-secondary rounded-pill w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            data-btn-compartir
                            data-nombre="<?= htmlspecialchars($lugar['nombre']) ?>"
                            data-direccion="<?= htmlspecialchars($lugar['direccion'] ?? '') ?>"
                            data-coordenadas="<?= htmlspecialchars($lugar['coordenadas_gps'] ?? ($lugar['latitud'] && $lugar['longitud'] ? $lugar['latitud'].','.$lugar['longitud'] : '')) ?>"
                            data-url="<?= htmlspecialchars($ogMeta['url'] ?? '') ?>">
                        <i class="bi bi-share-fill"></i> Compartir Ubicación
                    </button>
                </div>

                <!-- Redes Sociales Oficiales del Lugar (Debajo del Mapa si las configuró: Solo Iconos) -->
                <?php
                    $urlFacebook = !empty($lugar['facebook']) ? $lugar['facebook'] : (!empty($lugar['facebook_url']) ? $lugar['facebook_url'] : '');
                    $urlInstagram = !empty($lugar['instagram']) ? $lugar['instagram'] : (!empty($lugar['instagram_url']) ? $lugar['instagram_url'] : '');
                    $urlTiktok = !empty($lugar['tiktok']) ? $lugar['tiktok'] : (!empty($lugar['tiktok_url']) ? $lugar['tiktok_url'] : '');
                    $urlWeb = !empty($lugar['sitio_web']) ? $lugar['sitio_web'] : '';
                    $tieneRedesSociales = !empty($urlFacebook) || !empty($urlInstagram) || !empty($urlTiktok) || !empty($urlWeb);
                ?>
                <?php if ($tieneRedesSociales): ?>
                    <div class="border-top pt-3 mt-2">
                        <span class="extra-small fw-bold text-muted text-uppercase d-block mb-3 text-center letter-spacing-1">
                            Síguenos en nuestras redes
                        </span>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <?php if (!empty($urlFacebook)): ?>
                                <a href="<?= htmlspecialchars($urlFacebook) ?>" target="_blank" rel="noopener noreferrer"
                                   class="btn text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                   style="width: 44px; height: 44px; background-color: #1877f2;" title="Ver Facebook" aria-label="Facebook">
                                    <i class="bi bi-facebook fs-5"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($urlInstagram)): ?>
                                <a href="<?= htmlspecialchars($urlInstagram) ?>" target="_blank" rel="noopener noreferrer"
                                   class="btn text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                   style="width: 44px; height: 44px; background: linear-gradient(45deg, #f09433, #dc2743, #bc1888);" title="Ver Instagram" aria-label="Instagram">
                                    <i class="bi bi-instagram fs-5"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($urlTiktok)): ?>
                                <a href="<?= htmlspecialchars($urlTiktok) ?>" target="_blank" rel="noopener noreferrer"
                                   class="btn btn-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                   style="width: 44px; height: 44px;" title="Ver TikTok" aria-label="TikTok">
                                    <i class="bi bi-tiktok fs-5"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($urlWeb)): ?>
                                <a href="<?= htmlspecialchars($urlWeb) ?>" target="_blank" rel="noopener noreferrer"
                                   class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center shadow-sm text-dark"
                                   style="width: 44px; height: 44px;" title="Visitar Sitio Web" aria-label="Sitio Web">
                                    <i class="bi bi-globe2 text-teal fs-5"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Tarjeta: Radiomóviles (Tarifas y Paradas Oficiales) -->
            <?php if (!empty($tarifasTransporte) || !empty($paradasTransporte) || !empty($movilesTransporte)): ?>
                <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 card-detalle-seccion" aria-labelledby="tituloRadiomovil">
                    <h2 id="tituloRadiomovil" class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-taxi-front-fill text-warning"></i> Tarifario y Servicios
                    </h2>

                    <!-- Lista de Tarifas de Transporte -->
                    <?php if (!empty($tarifasTransporte)): ?>
                        <div class="mb-3">
                            <strong class="small text-dark d-block mb-2"><i class="bi bi-currency-dollar text-success me-1"></i>Tarifas de Referencia</strong>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($tarifasTransporte as $tar): ?>
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border small">
                                        <div>
                                            <span class="fw-semibold text-dark d-block"><?= htmlspecialchars($tar['tipo_servicio'] ?? 'Servicio') ?></span>
                                            <?php if (!empty($tar['descripcion'])): ?>
                                                <span class="extra-small text-muted"><?= htmlspecialchars($tar['descripcion']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-6">
                                                Bs. <?= number_format((float)($tar['precio_base'] ?? 0), 0) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Lista de Paradas / Casetas Oficiales -->
                    <?php if (!empty($paradasTransporte)): ?>
                        <div class="mb-3">
                            <strong class="small text-dark d-block mb-2">
                                <i class="bi bi-geo-alt-fill me-1 text-danger"></i>Paradas y Casetas Oficiales (<?= count($paradasTransporte) ?>)
                            </strong>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($paradasTransporte as $par): ?>
                                    <?php
                                        $nomP = $par['nombre_parada'] ?? ($par['nombre'] ?? 'Parada');
                                        $tipP = $par['tipo_parada'] ?? ($par['tipo'] ?? 'PARADA');
                                        $dirP = $par['direccion'] ?: ($par['referencia'] ?? 'Trinidad, Beni');
                                        $waP = preg_replace('/\D/', '', $par['whatsapp_directo'] ?: '');
                                        if (strlen($waP) === 8) $waP = '591' . $waP;
                                    ?>
                                    <div class="p-2 px-3 rounded-3 border bg-white small shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-start gap-1">
                                            <strong class="text-dark"><i class="bi bi-pin-map-fill text-danger me-1"></i><?= htmlspecialchars($nomP) ?></strong>
                                            <span class="badge rounded-pill extra-small <?= $tipP === 'TERMINAL' ? 'bg-primary' : ($tipP === 'AEROPUERTO' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                                                <?= htmlspecialchars($tipP) ?>
                                            </span>
                                        </div>
                                        <div class="extra-small text-muted mt-1"><i class="bi bi-geo me-1"></i><?= htmlspecialchars($dirP) ?></div>
                                        <div class="mt-2 pt-1 border-top d-flex justify-content-between align-items-center flex-wrap gap-1">
                                            <?php if (!empty($par['latitud']) && !empty($par['longitud'])): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-xs btn-outline-teal rounded-pill py-0 px-2 extra-small fw-semibold btn-enfocar-parada" data-parada-id="<?= (int)($par['id_parada'] ?? 0) ?>" data-lat="<?= (float)$par['latitud'] ?>" data-lng="<?= (float)$par['longitud'] ?>" data-nombre="<?= htmlspecialchars($nomP) ?>">
                                                        <i class="bi bi-geo-alt-fill me-1"></i>Ver en mapa
                                                    </button>
                                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $par['latitud'] ?>,<?= $par['longitud'] ?>" target="_blank" rel="noopener noreferrer" class="extra-small text-danger text-decoration-none fw-semibold">
                                                        <i class="bi bi-map-fill me-1"></i>Cómo llegar &rarr;
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($waP)): ?>
                                                <a href="https://wa.me/<?= $waP ?>?text=<?= rawurlencode("Hola, me comunico con la caseta '{$nomP}' de {$lugar['nombre']} para consultar por un móvil.") ?>" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-outline-success rounded-pill py-0 px-2 extra-small fw-semibold">
                                                    <i class="bi bi-whatsapp me-1"></i>WhatsApp Caseta
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($baseUrl) ?>/#explorar" class="btn btn-light border w-100 rounded-pill text-muted py-2 mb-3 shadow-2xs">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Volver al Catálogo
            </a>
        </aside>
    </div>
</div>

<!-- MODAL: ASISTENTE DE SOLICITUD DE RADIOMÓVIL -->
<?php if ($esRadiomovil): ?>
    <div class="modal fade" id="modalPedirRadiomovil" tabindex="-1" aria-labelledby="tituloModalRadio" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-teal text-white border-0 py-3">
                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2" id="tituloModalRadio">
                        <i class="bi bi-taxi-front-fill text-warning"></i> Pedir Móvil &bull; <?= htmlspecialchars($lugar['nombre']) ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">
                        Completa tus datos para enviar un mensaje rápido y formateado directamente a la Central de Despacho por WhatsApp:
                    </p>
                    <form id="formPedirRadiomovil" onsubmit="return false;">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark" for="radioTipoServicio">Tipo de Servicio Requerido</label>
                            <select id="radioTipoServicio" class="form-select form-select-sm rounded-3 shadow-none">
                                <?php if (!empty($tarifasTransporte)): ?>
                                    <?php foreach ($tarifasTransporte as $tar): ?>
                                        <option value="<?= htmlspecialchars($tar['tipo_servicio']) ?>">
                                            <?= htmlspecialchars($tar['tipo_servicio']) ?> (Ref. Bs. <?= number_format((float)($tar['precio_base'] ?? 0), 0) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Carrera Urbana">Carrera Urbana Estándar</option>
                                    <option value="Servicio de Encomienda">Envío o Retiro de Encomienda</option>
                                    <option value="Carrera al Aeropuerto">Traslado al Aeropuerto Jorge Henrich</option>
                                    <option value="Carrera a la Terminal">Traslado a la Terminal de Buses</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-dark mb-0" for="radioMiUbicacion">¿Dónde te recogemos?</label>
                                <button type="button" id="btnRadioUsarGps" class="btn btn-link btn-sm p-0 extra-small text-decoration-none text-teal fw-semibold">
                                    <i class="bi bi-cursor-fill me-1"></i> Mi GPS actual
                                </button>
                            </div>
                            <input type="text" id="radioMiUbicacion" class="form-control form-control-sm rounded-3 shadow-none" placeholder="Ej: Av. 6 de Agosto frente al Banco Unión..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark" for="radioDestino">¿Hacia dónde te diriges?</label>
                            <input type="text" id="radioDestino" class="form-control form-control-sm rounded-3 shadow-none" placeholder="Ej: Plaza Principal, Hospital Germán Busch, etc." required>
                        </div>
                        <div class="d-grid gap-2 pt-2">
                            <button type="button" id="btnEnviarPedidoRadio" class="btn btn-teal-action rounded-pill py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
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
<?php endif; ?>

<!-- MODAL: COMPARTIR EN REDES SOCIALES (Escritorio / Fallback) -->
<div class="modal fade" id="modalCompartirLugar" tabindex="-1" aria-labelledby="tituloModalCompartir" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-3 px-3">
                <h5 class="modal-title fs-6 fw-bold text-dark" id="tituloModalCompartir">
                    <i class="bi bi-share-fill text-primary me-2"></i> Compartir ficha
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3">
                <p class="extra-small text-muted mb-3">Comparte este destino o comercio con tus contactos en redes:</p>
                <div class="d-grid gap-2">
                    <a id="btnShareWhatsApp" href="#" target="_blank" rel="noopener noreferrer" 
                       class="btn btn-success text-white rounded-pill fw-semibold py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp fs-5"></i> Enviar por WhatsApp
                    </a>
                    <a id="btnShareFacebook" href="#" target="_blank" rel="noopener noreferrer" 
                       class="btn btn-social-icon--facebook text-white rounded-pill fw-semibold py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-facebook fs-5"></i> Publicar en Facebook
                    </a>
                    <button type="button" id="btnShareCopiar" class="btn btn-light border rounded-pill fw-semibold py-2 text-dark d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-link-45deg fs-5"></i> <span id="txtBtnCopiar">Copiar enlace</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const badgeDistancia = document.getElementById('badgeDistanciaDetalle');
    const coordenadasLugar = <?= !empty($mapa) ? json_encode([
        'lat' => (float)$mapa['latitud'],
        'lng' => (float)$mapa['longitud']
    ]) : 'null' ?>;

    if (badgeDistancia && coordenadasLugar && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((posicion) => {
            const aRad = Math.PI / 180;
            const radioTierraKm = 6371;
            const latUsuario = posicion.coords.latitude;
            const lngUsuario = posicion.coords.longitude;
            const dLat = (coordenadasLugar.lat - latUsuario) * aRad;
            const dLng = (coordenadasLugar.lng - lngUsuario) * aRad;
            const valor = Math.sin(dLat / 2) ** 2
                + Math.cos(latUsuario * aRad) * Math.cos(coordenadasLugar.lat * aRad) * Math.sin(dLng / 2) ** 2;
            const distanciaKm = radioTierraKm * 2 * Math.atan2(Math.sqrt(valor), Math.sqrt(1 - valor));
            const texto = distanciaKm < 1
                ? `A ${Math.round(distanciaKm * 1000)} m`
                : `A ${distanciaKm.toFixed(1)} km`;
            badgeDistancia.innerHTML = `<i class="bi bi-cursor-fill me-1" aria-hidden="true"></i>${texto}`;
            badgeDistancia.classList.remove('d-none');
        }, () => {}, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
    }

    const btnCompartir = document.getElementById('btnCompartirLugar');
    const modalCompartirEl = document.getElementById('modalCompartirLugar');
    const btnShareWhatsApp = document.getElementById('btnShareWhatsApp');
    const btnShareFacebook = document.getElementById('btnShareFacebook');
    const btnShareCopiar = document.getElementById('btnShareCopiar');
    const txtBtnCopiar = document.getElementById('txtBtnCopiar');

    if (btnCompartir) {
        const titulo = <?= json_encode($lugar['nombre'], JSON_UNESCAPED_UNICODE) ?>;
        const textoDesc = <?= json_encode(!empty($lugar['municipio']) ? "Conoce {$lugar['nombre']} en {$lugar['municipio']}, Beni a través de BeniTours:" : "Conoce {$lugar['nombre']} en BeniTours:", JSON_UNESCAPED_UNICODE) ?>;
        const urlActual = window.location.href;

        // Configurar enlaces directos del modal
        if (btnShareWhatsApp) {
            btnShareWhatsApp.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(textoDesc + ' ' + urlActual)}`;
        }
        if (btnShareFacebook) {
            btnShareFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(urlActual)}`;
        }

        btnCompartir.addEventListener('click', async () => {
            // Si el navegador soporta Web Share API (móviles y navegadores modernos)
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: titulo + ' | BeniTours',
                        text: textoDesc,
                        url: urlActual
                    });
                    return;
                } catch (e) {
                    // Si el usuario canceló o falló, no hacemos nada o abrimos modal si no fue AbortError
                    if (e.name === 'AbortError') return;
                }
            }

            // Fallback: abrir modal de compartir en escritorio
            if (modalCompartirEl && window.bootstrap?.Modal) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(modalCompartirEl);
                modal.show();
            } else {
                // Fallback directo a copiar
                navigator.clipboard.writeText(urlActual).then(() => {
                    const original = btnCompartir.innerHTML;
                    btnCompartir.innerHTML = '<i class="bi bi-check-lg text-success"></i> ¡Enlace copiado!';
                    setTimeout(() => {
                        btnCompartir.innerHTML = original;
                    }, 2500);
                });
            }
        });

        btnShareCopiar?.addEventListener('click', () => {
            navigator.clipboard.writeText(urlActual).then(() => {
                if (txtBtnCopiar) {
                    txtBtnCopiar.textContent = '¡Copiado al portapapeles!';
                    setTimeout(() => {
                        txtBtnCopiar.textContent = 'Copiar enlace';
                    }, 2500);
                }
            });
        });
    }

    // Lógica del Asistente para Pedir Radiomóvil
    const formPedirRadio = document.getElementById('formPedirRadiomovil');
    const btnEnviarPedidoRadio = document.getElementById('btnEnviarPedidoRadio');
    const radioServicioSelect = document.getElementById('radioTipoServicio');
    const radioUbicacionInput = document.getElementById('radioMiUbicacion');
    const radioDestinoInput = document.getElementById('radioDestino');
    const btnRadioUsarGps = document.getElementById('btnRadioUsarGps');

    btnRadioUsarGps?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('La geolocalización no está disponible en tu navegador.');
            return;
        }
        btnRadioUsarGps.disabled = true;
        btnRadioUsarGps.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Obteniendo...';
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            radioUbicacionInput.value = `Ubicación GPS: https://maps.google.com/?q=${lat},${lng}`;
            btnRadioUsarGps.disabled = false;
            btnRadioUsarGps.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Ubicación obtenida';
        }, (err) => {
            btnRadioUsarGps.disabled = false;
            btnRadioUsarGps.innerHTML = '<i class="bi bi-cursor-fill me-1"></i> Mi GPS';
            alert('No se pudo obtener tu ubicación actual.');
        });
    });

    btnEnviarPedidoRadio?.addEventListener('click', () => {
        const servicio = radioServicioSelect?.value || 'Carrera Urbana';
        const origen = radioUbicacionInput?.value.trim() || 'Mi ubicación actual';
        const destino = radioDestinoInput?.value.trim() || 'Por definir con el conductor';
        const nombreRadio = <?= json_encode($lugar['nombre'], JSON_UNESCAPED_UNICODE) ?>;
        const waNumero = <?= json_encode($whatsappRaw) ?>;

        const texto = `Hola Central de ${nombreRadio} 🚕\nSolicito un móvil:\n📍 *Punto de recogida:* ${origen}\n🏁 *Destino:* ${destino}\n🛠️ *Tipo de servicio:* ${servicio}\n\n¿Me confirman disponibilidad y tarifa por favor?`;
        
        window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'WHATSAPP');
        window.open(`https://wa.me/${waNumero}?text=${encodeURIComponent(texto)}`, '_blank');
        
        const modalEl = document.getElementById('modalPedirRadiomovil');
        if (modalEl && window.bootstrap?.Modal) {
            window.bootstrap.Modal.getInstance(modalEl)?.hide();
        }
    });

    // =========================================================================
    // MAPA INTERACTIVO DE PARADAS PARA RADIOMÓVILES / SINDICATOS EN FICHA
    // =========================================================================
    const puntosSindicato = <?= json_encode($puntosMapaDetalle ?? [], JSON_UNESCAPED_UNICODE) ?>;
    if (Array.isArray(puntosSindicato) && puntosSindicato.length > 0 && window.L) {
        // Detección de colisiones para que marcadores con coordenadas idénticas se separen sutilmente
        const coordsMap = {};
        const puntosAjustados = puntosSindicato.map(pt => {
            let lat = parseFloat(pt.latitud);
            let lng = parseFloat(pt.longitud);
            const key = `${lat.toFixed(5)},${lng.toFixed(5)}`;
            if (coordsMap[key]) {
                const count = coordsMap[key];
                const angle = (count * 2 * Math.PI) / 4;
                const dist = 0.00035; // ~35 metros de separación visual
                lat += dist * Math.cos(angle);
                lng += dist * Math.sin(angle);
                coordsMap[key]++;
            } else {
                coordsMap[key] = 1;
            }
            return { ...pt, renderLat: lat, renderLng: lng };
        });

        const contenedores = ['mapaDetalleSindicatoDesktop'];
        const mapInstances = {};
        const markersStore = {
            mapaDetalleSindicatoDesktop: {}
        };
        
        contenedores.forEach(cId => {
            const el = document.getElementById(cId);
            if (!el) return;

            const mapInstance = L.map(cId, {
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: true
            }).setView([puntosAjustados[0].renderLat, puntosAjustados[0].renderLng], 14);

            mapInstances[cId] = mapInstance;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(mapInstance);

            const featureGroup = L.featureGroup().addTo(mapInstance);

            puntosAjustados.forEach(pt => {
                const esCentral = pt.es_central;
                let markerBg = '#059669'; // Verde esmeralda por defecto
                let iconClass = 'bi-pin-map-fill';

                if (esCentral) {
                    markerBg = '#f59e0b'; // Ámbar Central
                    iconClass = 'bi-taxi-front-fill';
                } else if (pt.tipo_parada === 'TERMINAL') {
                    markerBg = '#2563eb'; // Azul Terminal
                    iconClass = 'bi-bus-front-fill';
                } else if (pt.tipo_parada === 'AEROPUERTO') {
                    markerBg = '#0284c7'; // Celeste Aeropuerto
                    iconClass = 'bi-airplane-fill';
                } else if (pt.tipo_parada === 'MERCADO') {
                    markerBg = '#7c3aed'; // Morado Mercado
                    iconClass = 'bi-shop';
                }

                const icono = L.divIcon({
                    className: `beniturs-map-marker marker-parada-taxi ${!esCentral ? 'marker-parada-secundaria' : ''}`,
                    html: `<div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;border-radius:50%;background-color:${markerBg};color:#ffffff;box-shadow:0 3px 10px rgba(0,0,0,0.35);border:2.5px solid #ffffff;">
                            <i class="bi ${iconClass}" style="font-size:1.1rem;color:#ffffff;"></i>
                           </div>`,
                    iconSize: [38, 38],
                    iconAnchor: [19, 19],
                    popupAnchor: [0, -22]
                });

                const m = L.marker([pt.renderLat, pt.renderLng], { icon: icono });
                
                const popup = `
                    <div class="beniturs-map-popup p-1" style="min-width: 220px;">
                        <span class="badge ${esCentral ? 'bg-warning text-dark' : 'bg-primary text-white'} extra-small rounded-pill px-2 py-0 fw-bold mb-1">${pt.tipo_parada}</span>
                        <h6 class="fw-bold text-dark mb-1" style="font-size:0.92rem;">${pt.nombre_parada}</h6>
                        <p class="extra-small text-muted mb-2 lh-sm"><i class="bi bi-geo-alt me-1 text-danger"></i>${pt.direccion}</p>
                        <div class="d-grid gap-1 pt-1 border-top">
                            ${pt.whatsapp ? `
                                <a href="https://wa.me/${pt.whatsapp}?text=${encodeURIComponent('Hola, me comunico con ' + pt.nombre_parada + ' para solicitar un servicio.')}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-success rounded-pill fw-bold extra-small py-1 text-center text-white text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i> Contactar Parada
                                </a>
                            ` : ''}
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${pt.latitud},${pt.longitud}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-outline-secondary rounded-pill extra-small py-1 text-center text-decoration-none">
                                <i class="bi bi-navigation-fill me-1"></i> Cómo llegar
                            </a>
                        </div>
                    </div>
                `;
                m.bindPopup(popup, { maxWidth: 260, minWidth: 210, className: 'beniturs-leaflet-popup' });
                featureGroup.addLayer(m);

                // Guardar referencia por ID de parada
                markersStore[cId][pt.id_parada] = m;
            });

            if (featureGroup.getLayers().length > 0) {
                mapInstance.fitBounds(featureGroup.getBounds(), { padding: [45, 45], maxZoom: 16 });
            }

            // Invalidate size on tab or render
            setTimeout(() => mapInstance.invalidateSize(), 500);
        });

        // Interacción: Clic en lista de paradas enfoca el mapa y abre el popup
        document.querySelectorAll('.btn-enfocar-parada').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const idParada = btn.getAttribute('data-parada-id');
                const targetContainerId = 'mapaDetalleSindicatoDesktop';
                const mapInst = mapInstances[targetContainerId];
                const marker = markersStore[targetContainerId]?.[idParada];

                if (mapInst && marker) {
                    const mapEl = document.getElementById(targetContainerId);
                    if (mapEl) {
                        mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    setTimeout(() => {
                        mapInst.setView(marker.getLatLng(), 16, { animate: true });
                        marker.openPopup();
                    }, 350);
                }
            });
        });
    }
});
</script>
