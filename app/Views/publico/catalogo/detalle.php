<?php
    $fotos = $fotografias ?? [];
    if (empty($fotos) && !empty($lugar['imagen'])) {
        $fotos = [['nombre_archivo' => $lugar['imagen']]];
    }
    $variasFotos = count($fotos) > 1;
    $queryMaps = urlencode(!empty($mapa) ? $mapa['coordenadas'] : $lugar['nombre'] . ', Trinidad, Beni, Bolivia');
?>
<div class="container py-4 my-2">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($baseUrl) ?>/" class="text-teal text-decoration-none"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= htmlspecialchars($baseUrl) ?>/#explorar" class="text-teal text-decoration-none">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($lugar['nombre']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 align-items-start">
        <!-- Fotografías y ubicación -->
        <div class="col-lg-7 place-detail-media-column">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <?php if (!empty($fotos)): ?>
                    <div id="carruselLugar" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500" role="region" aria-roledescription="carrusel" aria-label="Fotografías de <?= htmlspecialchars($lugar['nombre']) ?>">
                        <?php if ($variasFotos): ?>
                            <div class="carousel-indicators">
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
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                                <span class="visually-hidden">Fotografía anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carruselLugar" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                                <span class="visually-hidden">Fotografía siguiente</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="p-5 text-center hero-beni text-white">
                        <div class="place-icon-bubble mx-auto mb-3 shadow">
                            <i class="bi <?= htmlspecialchars($lugar['categoria_icono'] ?? 'bi-geo-alt') ?> fs-1 text-teal" aria-hidden="true"></i>
                        </div>
                        <p class="mb-0">Fotografías no disponibles por el momento</p>
                    </div>
                <?php endif; ?>
            </div>

            <section class="card border-0 shadow-sm rounded-4 p-4 bg-white d-none d-lg-block" aria-labelledby="tituloUbicacion">
                <h2 id="tituloUbicacion" class="h5 fw-bold text-dark mb-3"><i class="bi bi-geo-alt me-2 text-danger" aria-hidden="true"></i>Ubicación</h2>
                <p class="mb-1 text-dark fw-semibold"><?= htmlspecialchars($lugar['direccion'] ?? '') ?></p>
                <?php if (!empty($lugar['referencia_ubicacion'])): ?>
                    <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1" aria-hidden="true"></i> Referencia: <?= htmlspecialchars($lugar['referencia_ubicacion']) ?></p>
                <?php endif; ?>
                <?php if (!empty($mapa)): ?>
                    <div class="place-map rounded-4 overflow-hidden border mt-3 mb-3">
                        <iframe src="<?= htmlspecialchars($mapa['url'], ENT_QUOTES, 'UTF-8') ?>"
                                title="Mapa de ubicación de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                width="100%" height="300" loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mt-3 mb-2">La ubicación exacta en el mapa todavía no está disponible.</p>
                <?php endif; ?>
                <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $queryMaps ?>" target="_blank" rel="noopener noreferrer" 
                   onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MAPA')"
                   class="btn btn-outline-teal rounded-pill px-4 mt-2 align-self-start">
                    <i class="bi bi-map-fill me-2" aria-hidden="true"></i> Cómo llegar
                </a>
            </section>

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
                <!-- Menú / Carta de Productos debajo del mapa -->
                <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mt-4 d-none d-lg-block" aria-labelledby="tituloMenu">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h2 id="tituloMenu" class="h5 fw-bold text-dark mb-0">
                            <i class="bi bi-book-half me-2 text-warning" aria-hidden="true"></i>Menú / Carta de Productos
                        </h2>
                        <a href="<?= htmlspecialchars($lugar['enlace_menu']) ?>" target="_blank" rel="noopener noreferrer" 
                           onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MENU')"
                           class="btn btn-sm btn-outline-teal rounded-pill px-3 fw-semibold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Abrir en pantalla completa
                        </a>
                    </div>

                    <?php if ($esImagen): ?>
                        <div class="rounded-4 overflow-hidden border text-center bg-light p-2">
                            <img src="<?= htmlspecialchars($urlEmbedMenu) ?>" 
                                 alt="Menú de <?= htmlspecialchars($lugar['nombre']) ?>" 
                                 class="img-fluid rounded-3 w-100" 
                                 style="max-height: 750px; object-fit: contain;" 
                                 loading="lazy">
                        </div>
                    <?php elseif ($esGoogleDrive): ?>
                        <div class="menu-drive-card rounded-4 border text-center p-4 p-md-5">
                            <span class="menu-drive-icon"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                            <h3 class="h6 fw-bold text-dark mt-3 mb-2">Menú disponible en Google Drive</h3>
                            <p class="small text-muted mb-3 mx-auto">Ábrelo en una nueva pestaña para verlo completo, sin errores de carga en tu celular.</p>
                            <a href="<?= htmlspecialchars($urlMenu, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
                               onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MENU')"
                               class="btn btn-success rounded-pill px-4 fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Abrir menú
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="menu-embed-container rounded-4 overflow-hidden border shadow-sm" style="height: 560px; background-color: #f8f9fa;">
                            <iframe src="<?= htmlspecialchars($urlEmbedMenu, ENT_QUOTES, 'UTF-8') ?>"
                                    title="Menú digital de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    width="100%" height="100%" 
                                    style="border: 0;"
                                    loading="lazy"
                                    allow="autoplay"
                                    allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>

        <!-- Información del lugar -->
        <div class="col-lg-5 place-detail-info-column">
            <section class="card border-0 shadow-sm rounded-4 p-4 p-xl-5 bg-white place-detail-info" aria-labelledby="nombreLugar">
                <h1 id="nombreLugar" class="h2 fw-bold text-dark mb-3"><?= htmlspecialchars($lugar['nombre']) ?></h1>
                <?php
                    $estadoHorario = \App\Services\HorarioHelper::evaluarEstado($lugar['horario_atencion'] ?? null);
                ?>
                <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
                    <span class="badge bg-light text-success border border-success-subtle rounded-pill px-3 py-2">
                        <i class="bi <?= htmlspecialchars($lugar['categoria_icono'] ?? 'bi-geo-alt') ?> me-1" aria-hidden="true"></i> <?= htmlspecialchars($lugar['categoria'] ?? '') ?>
                    </span>
                    <?php if ($estadoHorario): ?>
                        <span class="badge <?= $estadoHorario['badge'] ?> rounded-pill px-3 py-2">
                            <?= htmlspecialchars($estadoHorario['texto']) ?>
                        </span>
                    <?php endif; ?>
                    <div class="place-detail-actions ms-auto d-inline-flex align-items-center gap-2">
                        <span id="badgeDistanciaDetalle" class="badge bg-danger text-white shadow-sm rounded-pill px-2 py-1 fw-bold d-none" style="font-size: 0.72rem;" aria-live="polite"></span>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-none"
                            data-btn-favorito
                            data-id="<?= (int)$lugar['id_lugar'] ?>"
                            data-nombre="<?= htmlspecialchars($lugar['nombre']) ?>"
                            data-slug="<?= htmlspecialchars($lugar['slug']) ?>"
                            data-categoria="<?= htmlspecialchars($lugar['categoria'] ?? '') ?>"
                            data-imagen="<?= htmlspecialchars($lugar['imagen'] ?? '') ?>"
                            data-municipio="<?= htmlspecialchars($lugar['municipio'] ?? '') ?>"
                            data-url="<?= htmlspecialchars($ogMeta['url'] ?? '') ?>">
                        <i class="bi bi-heart"></i>
                        <span class="label-favorito small fw-semibold">Añadir a mi ruta</span>
                    </button>
                    </div>
                </div>
                <h2 class="h5 fw-bold text-dark mb-3">Descripción</h2>
                <p class="text-secondary mb-4" style="white-space: pre-line;"><?= htmlspecialchars($lugar['descripcion'] ?? '') ?></p>
                <div class="border-top pt-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h5 fw-bold text-dark mb-0"><i class="bi bi-clock me-2 text-warning" aria-hidden="true"></i>Horarios de atención</h2>
                        <?php
                            $evaluacionHorario = \App\Services\HorarioHelper::evaluarEstado(
                                $lugar['horario_atencion'] ?? null,
                                $lugar['estado_operativo'] ?? null,
                                $lugar['aviso_temporal_motivo'] ?? null,
                                $lugar['aviso_temporal_reapertura'] ?? null
                            );
                        ?>
                        <?php if ($evaluacionHorario): ?>
                            <span class="badge <?= $evaluacionHorario['badge'] ?> rounded-pill px-3 py-1 extra-small fw-bold">
                                <?= htmlspecialchars($evaluacionHorario['texto']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-secondary mb-0" style="white-space: pre-line;"><?= htmlspecialchars(($lugar['horario_atencion'] ?? '') ?: 'Sin horario específico / Abierto al público') ?></p>
                </div>

                <?php if (($lugar['estado_operativo'] ?? '') === 'CERRADO_TEMPORAL'): ?>
                    <!-- Alerta Destacada: Aviso Especial de Cierre Temporal del Negocio -->
                    <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm border-0 border-start border-danger border-4" role="alert">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="rounded-circle bg-danger text-white p-2 fs-5 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-door-closed-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-danger mb-1">Aviso importante del establecimiento:</h6>
                                <p class="mb-1 small text-dark fw-semibold">
                                    "<?= htmlspecialchars($lugar['aviso_temporal_motivo'] ?: 'Este local se encuentra temporalmente cerrado por mantenimiento o evento.') ?>"
                                </p>
                                <?php if (!empty($lugar['aviso_temporal_reapertura'])): ?>
                                    <div class="extra-small text-danger fw-bold mt-1">
                                        <i class="bi bi-calendar-event me-1"></i> Reapertura estimada: <?= date('d/m/Y', strtotime($lugar['aviso_temporal_reapertura'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                    $whatsappRaw = preg_replace('/\D/', '', $lugar['whatsapp_contacto'] ?? '');
                    if (strlen($whatsappRaw) === 8) {
                        $whatsappRaw = '591' . $whatsappRaw;
                    }
                    $esCerradoTemporal = ($lugar['estado_operativo'] ?? '') === 'CERRADO_TEMPORAL';
                    $textoWaConsulta = $esCerradoTemporal
                        ? ("Hola, vi en BeniTours que " . $lugar['nombre'] . " tiene un aviso de cierre temporal. Quisiera consultarles sobre su reapertura o reservas.")
                        : ("Hola, vi la información de " . $lugar['nombre'] . " en BeniTours y quisiera hacer una consulta.");
                    $mensajeWa = rawurlencode($textoWaConsulta);
                    $telefonoRaw = preg_replace('/[^\d+]/', '', $lugar['telefono_contacto'] ?? '');
                ?>
                <div class="border-top pt-4 mb-4">
                    <h2 class="h5 fw-bold text-dark mb-3"><i class="bi bi-chat-dots me-2 text-teal" aria-hidden="true"></i>Contacto Directo</h2>
                    <div class="d-grid gap-2">
                        <?php if (!empty($whatsappRaw)): ?>
                            <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $mensajeWa ?>" target="_blank" rel="noopener noreferrer" 
                                onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'WHATSAPP')"
                                class="btn btn-teal-action rounded-pill py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-whatsapp fs-5"></i> Contactar por WhatsApp
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($telefonoRaw)): ?>
                            <a href="tel:<?= $telefonoRaw ?>" 
                                onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'TELEFONO')"
                                class="btn btn-outline-dark rounded-pill py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-telephone-fill"></i> Llamar: <?= htmlspecialchars($lugar['telefono_contacto']) ?>
                            </a>
                        <?php endif; ?>
                        <?php if (empty($whatsappRaw) && empty($telefonoRaw)): ?>
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Puedes visitar este lugar directamente en la dirección indicada.</p>
                        <?php endif; ?>
                        <button type="button" class="btn btn-light rounded-pill py-2 text-secondary fw-semibold border d-flex align-items-center justify-content-center gap-2 mt-1" id="btnCompartirLugar">
                            <i class="bi bi-share-fill text-primary"></i> Compartir este lugar
                        </button>
                    </div>
                </div>

                <!-- Modal para compartir en Redes Sociales (Escritorio / Fallback) -->
                <div class="modal fade" id="modalCompartirLugar" tabindex="-1" aria-labelledby="tituloModalCompartir" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header border-0 pb-0 pt-3 px-3">
                                <h5 class="modal-title fs-6 fw-bold text-dark" id="tituloModalCompartir">
                                    <i class="bi bi-share-fill text-primary me-2"></i>Compartir ficha
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
                                       class="btn btn-primary rounded-pill fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" style="background-color: #1877f2; border-color: #1877f2;">
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

                <?php if (!empty($promociones)): ?>
                    <div class="border-top pt-4 mb-4 d-none d-lg-block">
                        <h2 class="h5 fw-bold text-dark mb-3"><i class="bi bi-tag-fill text-warning me-2" aria-hidden="true"></i>Promociones activas</h2>
                        <?php foreach ($promociones as $pr): ?>
                            <div class="p-3 rounded-4 border bg-warning-subtle border-warning-subtle mb-3 shadow-sm">
                                <?php if (!empty($pr['imagen'])): ?>
                                    <div class="mb-2 rounded-3 overflow-hidden" style="max-height: 160px;">
                                        <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($pr['imagen']) ?>" 
                                             alt="<?= htmlspecialchars($pr['titulo']) ?>"
                                             class="w-100 object-fit-cover" style="height: 140px;" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <strong class="text-dark d-block"><?= htmlspecialchars($pr['titulo']) ?></strong>
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1 extra-small fw-bold"><?= htmlspecialchars($pr['descuento_texto'] ?: 'Oferta') ?></span>
                                </div>
                                <p class="text-secondary small mb-2"><?= htmlspecialchars($pr['descripcion']) ?></p>
                                <div class="extra-small text-muted mb-2">
                                    <i class="bi bi-clock-history text-danger me-1"></i> Hasta: <?= date('d/m/Y', strtotime($pr['fecha_fin'])) ?>
                                </div>
                                <?php
                                    $textoWaDetalle = rawurlencode("Hola, vi su promoción '{$pr['titulo']}' (" . ($pr['descuento_texto'] ?: 'Oferta') . ") en BeniTours y quisiera aprovecharla en {$lugar['nombre']}.");
                                ?>
                                <?php if (!empty($whatsappRaw)): ?>
                                    <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $textoWaDetalle ?>" 
                                       target="_blank" rel="noopener noreferrer"
                                       class="btn btn-sm btn-teal-action rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-1 shadow-sm mt-1">
                                        <i class="bi bi-whatsapp"></i> Pedir / Aprovechar Oferta
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- SECCIÓN DE RADIOMÓVIL Y TRANSPORTE (Debajo de Promociones - Desktop) -->
                <?php if (!empty($tarifasTransporte) || !empty($paradasTransporte) || !empty($movilesTransporte)): ?>
                    <div class="border-top pt-4 mb-4 d-none d-lg-block">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 fw-bold text-dark mb-0">
                                <i class="bi bi-taxi-front-fill text-primary me-2" aria-hidden="true"></i>Servicio de Radiomóvil
                            </h2>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 extra-small fw-bold">
                                Tarifario Oficial
                            </span>
                        </div>

                        <!-- Botón Rápido Pedir Móvil -->
                        <?php if (!empty($whatsappRaw)): ?>
                            <?php 
                                $txtPedirMovil = rawurlencode("Hola, me comunico con la central de {$lugar['nombre']}. Necesito solicitar un radiomóvil en mi ubicación actual hacia: ");
                            ?>
                            <div class="mb-3">
                                <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $txtPedirMovil ?>" target="_blank" rel="noopener noreferrer"
                                   onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'WHATSAPP')"
                                   class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp fs-5"></i> Solicitar Móvil a la Central
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($movilesTransporte)): ?>
                            <div class="mb-3">
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-taxi-front-fill me-1 text-primary"></i>Moviles activos</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($movilesTransporte as $movil): ?>
                                        <?php $disponible = ($movil['estado'] ?? '') === 'DISPONIBLE'; ?>
                                        <span class="badge rounded-pill px-3 py-2 <?= $disponible ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($movil['numero_movil']) ?>: <?= $disponible ? 'Disponible' : htmlspecialchars(str_replace('_', ' ', $movil['estado'])) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Tarifario -->
                        <?php if (!empty($tarifasTransporte)): ?>
                            <div class="mb-3">
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-cash-coin me-1 text-success"></i>Tarifas y Expresos</strong>
                                <div class="list-group list-group-flush rounded-4 border overflow-hidden">
                                    <?php foreach ($tarifasTransporte as $tar): ?>
                                        <div class="list-group-item p-2 px-3 bg-light-subtle d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="extra-small fw-bold text-dark">
                                                    <?= htmlspecialchars($tar['origen']) ?> &rarr; <span class="text-primary"><?= htmlspecialchars($tar['destino']) ?></span>
                                                </div>
                                                <div class="extra-small text-muted">
                                                    <?= htmlspecialchars($tar['tipo_servicio'] === 'EXPRESO_PROVINCIAL' ? 'Expreso Provincial' : ($tar['tipo_servicio'] === 'AEROPUERTO_TERMINAL' ? 'Aeropuerto/Terminal' : 'Urbano Fijo')) ?>
                                                    <?php if (!empty($tar['descripcion'])): ?> &bull; <?= htmlspecialchars($tar['descripcion']) ?><?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success text-white rounded-pill px-2 py-1 small fw-bold">
                                                    Bs <?= number_format($tar['precio_base'], 2) ?>
                                                </span>
                                                <?php if (!empty($tar['precio_nocturno'])): ?>
                                                    <div class="extra-small text-muted" title="Precio nocturno">Noc: Bs <?= number_format($tar['precio_nocturno'], 2) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Paradas Oficiales -->
                        <?php if (!empty($paradasTransporte)): ?>
                            <div>
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-geo-alt-fill me-1 text-danger"></i>Paradas y Casetas Oficiales</strong>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($paradasTransporte as $par): ?>
                                        <div class="p-2 px-3 rounded-3 border bg-white small">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <strong class="text-dark"><?= htmlspecialchars($par['nombre']) ?></strong>
                                                <span class="badge rounded-pill extra-small <?= $par['tipo'] === 'TERMINAL' ? 'bg-primary' : ($par['tipo'] === 'AEROPUERTO' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                                                    <?= htmlspecialchars($par['tipo']) ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($par['direccion'])): ?>
                                                <div class="extra-small text-muted"><i class="bi bi-geo me-1"></i><?= htmlspecialchars($par['direccion']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($par['latitud']) && !empty($par['longitud'])): ?>
                                                <div class="mt-1">
                                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $par['latitud'] ?>,<?= $par['longitud'] ?>" target="_blank" rel="noopener noreferrer" class="extra-small text-danger text-decoration-none fw-semibold">
                                                        <i class="bi bi-map-fill me-1"></i>Ver ubicación en mapa &rarr;
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($baseUrl) ?>/#explorar" class="btn btn-light btn-sm w-100 rounded-pill text-muted d-none d-lg-block">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Volver al Directorio
                </a>
            </section>

            <!-- Orden exclusivo para celular: información, mapa, menú y promociones. -->
            <div class="d-lg-none mt-4">
                <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" aria-labelledby="tituloUbicacionMovil">
                    <h2 id="tituloUbicacionMovil" class="h5 fw-bold text-dark mb-3"><i class="bi bi-geo-alt me-2 text-danger" aria-hidden="true"></i>Ubicación</h2>
                    <p class="mb-1 text-dark fw-semibold"><?= htmlspecialchars($lugar['direccion'] ?? '') ?></p>
                    <?php if (!empty($lugar['referencia_ubicacion'])): ?>
                        <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Referencia: <?= htmlspecialchars($lugar['referencia_ubicacion']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($mapa)): ?>
                        <div class="place-map rounded-4 overflow-hidden border mt-3 mb-3">
                            <iframe src="<?= htmlspecialchars($mapa['url'], ENT_QUOTES, 'UTF-8') ?>"
                                    title="Mapa de ubicación de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    width="100%" height="300" loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mt-3 mb-2">La ubicación exacta en el mapa todavía no está disponible.</p>
                    <?php endif; ?>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $queryMaps ?>" target="_blank" rel="noopener noreferrer"
                       onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MAPA')"
                       class="btn btn-outline-teal rounded-pill px-4 mt-2">
                        <i class="bi bi-map-fill me-2" aria-hidden="true"></i>Cómo llegar
                    </a>
                </section>

                <?php if (!empty($lugar['enlace_menu'])): ?>
                    <?php
                        $urlMenuMovil = $lugar['enlace_menu'];
                        $esGoogleDriveMovil = str_contains($urlMenuMovil, 'drive.google.com');
                        $esImagenMovil = (bool)preg_match('/\.(jpe?g|png|webp|gif)(\?.*)?$/i', $urlMenuMovil);
                    ?>
                    <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" aria-labelledby="tituloMenuMovil">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h2 id="tituloMenuMovil" class="h5 fw-bold text-dark mb-0"><i class="bi bi-book-half me-2 text-warning" aria-hidden="true"></i>Menú / Carta</h2>
                            <a href="<?= htmlspecialchars($urlMenuMovil, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
                               onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'MENU')"
                               class="btn btn-sm btn-outline-teal rounded-pill px-3 fw-semibold">Abrir menú</a>
                        </div>
                        <?php if ($esImagenMovil): ?>
                            <div class="rounded-4 overflow-hidden border text-center bg-light p-2"><img src="<?= htmlspecialchars($urlMenuMovil) ?>" alt="Menú de <?= htmlspecialchars($lugar['nombre']) ?>" class="img-fluid rounded-3 w-100" loading="lazy"></div>
                        <?php elseif ($esGoogleDriveMovil): ?>
                            <div class="menu-drive-card rounded-4 border text-center p-4"><span class="menu-drive-icon"><i class="bi bi-file-earmark-pdf-fill"></i></span><p class="small text-muted mt-3 mb-0">Ábrelo en una nueva pestaña para verlo completo.</p></div>
                        <?php else: ?>
                            <div class="menu-embed-container rounded-4 overflow-hidden border" style="height: 460px;"><iframe src="<?= htmlspecialchars($urlMenuMovil, ENT_QUOTES, 'UTF-8') ?>" title="Menú digital de <?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>" width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen></iframe></div>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($promociones)): ?>
                    <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" aria-labelledby="tituloPromosMovil">
                        <h2 id="tituloPromosMovil" class="h5 fw-bold text-dark mb-3"><i class="bi bi-tag-fill text-warning me-2" aria-hidden="true"></i>Promociones activas</h2>
                        <?php foreach ($promociones as $pr): ?>
                            <div class="p-3 rounded-4 border bg-warning-subtle border-warning-subtle mb-3 shadow-sm">
                                <?php if (!empty($pr['imagen'])): ?><div class="mb-2 rounded-3 overflow-hidden" style="max-height:160px;"><img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($pr['imagen']) ?>" alt="<?= htmlspecialchars($pr['titulo']) ?>" class="w-100 object-fit-cover" style="height:140px;" loading="lazy"></div><?php endif; ?>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1"><strong class="text-dark d-block"><?= htmlspecialchars($pr['titulo']) ?></strong><span class="badge bg-danger text-white rounded-pill px-2 py-1 extra-small fw-bold"><?= htmlspecialchars($pr['descuento_texto'] ?: 'Oferta') ?></span></div>
                                <p class="text-secondary small mb-2"><?= htmlspecialchars($pr['descripcion']) ?></p>
                                <div class="extra-small text-muted mb-2"><i class="bi bi-clock-history text-danger me-1"></i>Hasta: <?= date('d/m/Y', strtotime($pr['fecha_fin'])) ?></div>
                                <?php if (!empty($whatsappRaw)): ?>
                                    <?php $textoWaMovil = rawurlencode("Hola, vi su promoción '{$pr['titulo']}' (" . ($pr['descuento_texto'] ?: 'Oferta') . ") en BeniTours y quisiera aprovecharla en {$lugar['nombre']}."); ?>
                                    <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $textoWaMovil ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill w-100 fw-bold"><i class="bi bi-whatsapp me-1"></i>Pedir / Aprovechar oferta</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <!-- SECCIÓN DE RADIOMÓVIL Y TRANSPORTE (Debajo de Promociones - Móvil) -->
                <?php if (!empty($tarifasTransporte) || !empty($paradasTransporte) || !empty($movilesTransporte)): ?>
                    <section class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" aria-labelledby="tituloTransporteMovil">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 id="tituloTransporteMovil" class="h5 fw-bold text-dark mb-0">
                                <i class="bi bi-taxi-front-fill text-primary me-2" aria-hidden="true"></i>Servicio de Radiomóvil
                            </h2>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 extra-small fw-bold">
                                Oficial
                            </span>
                        </div>

                        <?php if (!empty($whatsappRaw)): ?>
                            <?php 
                                $txtPedirMovilMovil = rawurlencode("Hola, me comunico con la central de {$lugar['nombre']}. Necesito solicitar un radiomóvil en mi ubicación actual hacia: ");
                            ?>
                            <div class="mb-3">
                                <a href="https://wa.me/<?= $whatsappRaw ?>?text=<?= $txtPedirMovilMovil ?>" target="_blank" rel="noopener noreferrer"
                                   onclick="window.BeniTursItinerario?.registrarEvento(<?= (int)$lugar['id_lugar'] ?>, 'WHATSAPP')"
                                   class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp fs-5"></i> Pedir Móvil por WhatsApp
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($movilesTransporte)): ?>
                            <div class="mb-3">
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-taxi-front-fill me-1 text-primary"></i>Moviles activos</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($movilesTransporte as $movil): ?>
                                        <?php $disponible = ($movil['estado'] ?? '') === 'DISPONIBLE'; ?>
                                        <span class="badge rounded-pill px-3 py-2 <?= $disponible ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($movil['numero_movil']) ?>: <?= $disponible ? 'Disponible' : htmlspecialchars(str_replace('_', ' ', $movil['estado'])) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tarifasTransporte)): ?>
                            <div class="mb-3">
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-cash-coin me-1 text-success"></i>Tarifas y Expresos</strong>
                                <div class="list-group list-group-flush rounded-4 border overflow-hidden">
                                    <?php foreach ($tarifasTransporte as $tar): ?>
                                        <div class="list-group-item p-2 px-3 bg-light-subtle d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="extra-small fw-bold text-dark">
                                                    <?= htmlspecialchars($tar['origen']) ?> &rarr; <span class="text-primary"><?= htmlspecialchars($tar['destino']) ?></span>
                                                </div>
                                                <div class="extra-small text-muted">
                                                    <?= htmlspecialchars($tar['tipo_servicio'] === 'EXPRESO_PROVINCIAL' ? 'Expreso' : ($tar['tipo_servicio'] === 'AEROPUERTO_TERMINAL' ? 'Aeropuerto/Terminal' : 'Urbano')) ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success text-white rounded-pill px-2 py-1 small fw-bold">
                                                    Bs <?= number_format($tar['precio_base'], 2) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($paradasTransporte)): ?>
                            <div>
                                <strong class="small text-dark d-block mb-2"><i class="bi bi-geo-alt-fill me-1 text-danger"></i>Paradas Oficiales</strong>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($paradasTransporte as $par): ?>
                                        <div class="p-2 px-3 rounded-3 border bg-light small">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <strong class="text-dark"><?= htmlspecialchars($par['nombre']) ?></strong>
                                                <span class="badge rounded-pill extra-small <?= $par['tipo'] === 'TERMINAL' ? 'bg-primary' : ($par['tipo'] === 'AEROPUERTO' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                                                    <?= htmlspecialchars($par['tipo']) ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($par['latitud']) && !empty($par['longitud'])): ?>
                                                <div class="mt-1">
                                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $par['latitud'] ?>,<?= $par['longitud'] ?>" target="_blank" rel="noopener noreferrer" class="extra-small text-danger text-decoration-none fw-semibold">
                                                        <i class="bi bi-map-fill me-1"></i>Ver en mapa &rarr;
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($baseUrl) ?>/#explorar" class="btn btn-light btn-sm w-100 rounded-pill text-muted mb-2"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver al Directorio</a>
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
});
</script>
