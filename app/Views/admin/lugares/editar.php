<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<?php
$coordGpsValor = trim($lugar['coordenadas_gps'] ?? '');
if (empty($coordGpsValor) && !empty($lugar['latitud']) && !empty($lugar['longitud'])) {
    $coordGpsValor = $lugar['latitud'] . ', ' . $lugar['longitud'];
}
$initLat = !empty($lugar['latitud']) ? (float)$lugar['latitud'] : -14.8333;
$initLng = !empty($lugar['longitud']) ? (float)$lugar['longitud'] : -64.9000;
if (!empty($coordGpsValor)) {
    $partesGps = explode(',', $coordGpsValor);
    if (count($partesGps) === 2 && is_numeric(trim($partesGps[0])) && is_numeric(trim($partesGps[1]))) {
        $initLat = (float)trim($partesGps[0]);
        $initLng = (float)trim($partesGps[1]);
    }
}
$tieneCoordsIniciales = (!empty($lugar['latitud']) || !empty($coordGpsValor));
?>

<div class="container-fluid p-0">
    <!-- Encabezado Superior -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">#<?= (int)$lugar['id_lugar'] ?></span>
                <?php if ($lugar['tipo_lugar'] === 'COMERCIAL'): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                        <i class="bi bi-shop me-1"></i> Ficha Comercial
                    </span>
                <?php else: ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                        <i class="bi bi-tree me-1"></i> Ficha Pública
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0"><?= htmlspecialchars($lugar['nombre']) ?></h1>
            <small class="text-muted">Edición de contenido, clasificación turística y galería de fotos.</small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($lugar['slug'])): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>/catalogo/detalle?slug=<?= urlencode($lugar['slug']) ?>" 
                   target="_blank" 
                   rel="noopener"
                   class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Ver en Catálogo</span>
                </a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Volver al Listado
            </a>
        </div>
    </div>

    <!-- Alertas Flash -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i><?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario Principal de Edición -->
    <form id="formEditarFicha" action="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/actualizar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" id="csrfTokenGlobal" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="id_lugar" id="idLugarGlobal" value="<?= (int)$lugar['id_lugar'] ?>">

        <div class="row g-4">
            <!-- COLUMNA PRINCIPAL (Galería y Descripción) -->
            <div class="col-lg-8">
                <!-- Tarjeta 1: Galería Multimedia (Requisito Principal del Usuario) -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4" id="seccion-galeria">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <h4 class="h5 fw-bold text-dark mb-0">
                                <i class="bi bi-images text-success me-2"></i>Galería de Fotografías
                            </h4>
                            <small class="text-muted">Gestiona las imágenes de esta ficha: elimina las que ya no uses, define la portada o sube nuevas.</small>
                        </div>
                        <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold" id="conteoFotosGaleria">
                            <?= count($fotografias) ?> foto(s) registrada(s)
                        </span>
                    </div>

                    <!-- Cuadrícula de Fotografías Actuales -->
                    <div class="row g-3 mb-4" id="gridFotografiasActuales">
                        <?php if (!empty($fotografias)): ?>
                            <?php foreach ($fotografias as $foto): ?>
                                <?php $esPortada = (int)$foto['es_principal'] === 1; ?>
                                <div class="col-6 col-sm-4 col-md-3 item-foto-galeria" id="card-foto-<?= (int)$foto['id_fotografia'] ?>">
                                    <div class="card h-100 border rounded-4 overflow-hidden shadow-sm position-relative foto-card-inner">
                                        <!-- Imagen -->
                                        <div class="position-relative foto-thumbnail-container" style="height: 140px; background-color: #092611;">
                                            <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($foto['nombre_archivo']) ?>" 
                                                 alt="Fotografía de <?= htmlspecialchars($lugar['nombre']) ?>" 
                                                 class="w-100 h-100 object-fit-cover"
                                                 loading="lazy">
                                            
                                            <!-- Badge de Portada -->
                                            <div class="badge-portada-wrapper position-absolute top-0 start-0 m-2">
                                                <?php if ($esPortada): ?>
                                                    <span class="badge bg-success text-white shadow-sm rounded-pill px-2 py-1 extra-small fw-bold">
                                                        <i class="bi bi-star-fill text-warning me-1"></i> Portada
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Botón de Eliminar (Acción Rápida) -->
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm rounded-circle p-1 position-absolute top-0 end-0 m-2 shadow btn-eliminar-foto-ajax" 
                                                    data-id-foto="<?= (int)$foto['id_fotografia'] ?>"
                                                    title="Eliminar esta fotografía"
                                                    style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-trash3-fill extra-small"></i>
                                            </button>
                                        </div>

                                        <!-- Pie de Tarjeta de Foto con Acciones -->
                                        <div class="p-2 text-center bg-white border-top">
                                            <?php if (!$esPortada): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-warning text-dark btn-sm rounded-pill w-100 py-1 extra-small fw-semibold btn-establecer-portada-ajax"
                                                        data-id-foto="<?= (int)$foto['id_fotografia'] ?>"
                                                        title="Hacer foto principal de la ficha">
                                                    <i class="bi bi-star me-1 text-warning"></i> Portada
                                                </button>
                                            <?php else: ?>
                                                <span class="text-success extra-small fw-bold d-block py-1">
                                                    <i class="bi bi-check2-circle me-1"></i> Foto Principal
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12" id="estadoVacioGaleria">
                                <div class="text-center p-4 bg-light rounded-4 border border-dashed">
                                    <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center p-3 text-secondary mb-2" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Sin Fotografías Registradas</h6>
                                    <p class="text-muted small mb-0">Esta ficha aún no cuenta con imágenes. Puedes seleccionar y subir varias a continuación.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Zona de Carga para Aumentar Más Imágenes -->
                    <div class="p-3 bg-light rounded-4 border">
                        <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
                            <i class="bi bi-cloud-arrow-up-fill text-success fs-5"></i>
                            <span>Añadir Más Fotografías a la Galería</span>
                        </label>
                        
                        <div class="dropzone-area p-4 text-center rounded-3 bg-white border border-2 border-dashed mb-2" id="dropzoneFotos" style="cursor: pointer;">
                            <i class="bi bi-images display-6 text-success d-block mb-2"></i>
                            <strong class="d-block text-dark">Haz clic aquí para seleccionar imágenes</strong>
                            <small class="text-muted d-block mb-1">Puedes seleccionar varias fotos al mismo tiempo manteniendo presionada la tecla Ctrl / Shift.</small>
                            <span class="badge bg-light text-secondary border px-2 py-1 extra-small">JPG, PNG o WEBP &bull; Máximo 5 MB por imagen</span>
                            <input type="file" 
                                   id="inputFotografiasNuevas" 
                                   name="fotografias[]" 
                                   multiple 
                                   class="d-none" 
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        <!-- Previsualización de Fotos Nuevas Seleccionadas -->
                        <div id="previewFotosNuevasContainer" class="d-none mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="extra-small fw-bold text-uppercase text-muted" id="conteoNuevasFotos">Imágenes listas para subir</span>
                                <button type="button" class="btn btn-link text-danger text-decoration-none extra-small p-0" id="btnLimpiarNuevasFotos">
                                    <i class="bi bi-x-circle me-1"></i> Descartar selección
                                </button>
                            </div>
                            <div class="row g-2" id="gridPreviewNuevasFotos"></div>
                            <small class="text-muted extra-small d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Estas nuevas fotografías se subirán y guardarán al presionar el botón <strong>Guardar Cambios</strong>.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2: Descripción y Contenido Turístico -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h4 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-card-text text-success me-2"></i>Descripción & Atractivos
                    </h4>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small text-dark">Descripción Completa *</label>
                        <textarea id="descripcion" 
                                  name="descripcion" 
                                  rows="6" 
                                  class="form-control" 
                                  placeholder="Describe las características principales, platillos, servicios o atractivos del lugar..."
                                  required><?= htmlspecialchars($lugar['descripcion']) ?></textarea>
                        <small class="text-muted extra-small">Explica qué hace único a este lugar para los turistas y visitantes.</small>
                    </div>
                </div>

                <!-- Tarjeta: Ubicación Geográfica & Mapa Interactivo -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mt-4" id="seccion-ubicacion">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div>
                            <h4 class="h5 fw-bold text-dark mb-0">
                                <i class="bi bi-geo-alt-fill text-success me-2"></i>Ubicación & GPS
                            </h4>
                            <small class="text-muted extra-small">Dirección física y localización exacta con pin interactivo en el mapa.</small>
                        </div>
                        <div class="location-mode-tabs" role="tablist">
                            <button type="button" class="active" data-location-mode="mapa">
                                <i class="bi bi-map-fill me-1"></i> Marcar en el Mapa
                            </button>
                            <button type="button" data-location-mode="manual">
                                <i class="bi bi-keyboard me-1"></i> Coordenadas Manuales
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="direccion" class="form-label fw-semibold small text-dark">Dirección Física Exacta *</label>
                            <input type="text" 
                                   id="direccion" 
                                   name="direccion" 
                                   class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($lugar['direccion']) ?>" 
                                   required
                                   placeholder="Ej. Calle 18 de Noviembre Nº 420 entre La Paz y Sucre">
                        </div>
                        <div class="col-md-5">
                            <label for="referencia_ubicacion" class="form-label fw-semibold small text-dark">Puntos de Referencia para Llegar</label>
                            <input type="text" 
                                   id="referencia_ubicacion" 
                                   name="referencia_ubicacion" 
                                   class="form-control rounded-3" 
                                   value="<?= htmlspecialchars($lugar['referencia_ubicacion'] ?? '') ?>"
                                   placeholder="Ej. A dos cuadras de la plaza principal, frente al Banco Unión">
                        </div>

                        <!-- Selector GPS Mapa / Manual -->
                        <div class="col-12 mt-2">
                            <div id="ubicacionMapa">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <span class="small fw-semibold text-dark">
                                        <i class="bi bi-pin-map-fill text-danger me-1"></i>Pin en el Mapa
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 extra-small fw-semibold" id="btnCapturarGpsAdmin">
                                            <i class="bi bi-crosshair me-1"></i> Mi GPS actual
                                        </button>
                                        <span class="badge bg-light text-dark border extra-small font-monospace" id="badgeEstadoCoords">
                                            <?= !empty($coordGpsValor) ? htmlspecialchars($coordGpsValor) : 'Sin coordenadas' ?>
                                        </span>
                                    </div>
                                </div>
                                <div id="mapaEditarFicha" 
                                     class="location-picker-map shadow-sm" 
                                     data-lat="<?= $initLat ?>" 
                                     data-lng="<?= $initLng ?>"
                                     data-has-coords="<?= $tieneCoordsIniciales ? '1' : '0' ?>"
                                     style="height: 330px; border-radius: 14px; border: 1px solid #cfe0d5;"></div>
                                <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                    <small id="estadoMapaEditarFicha" class="text-muted extra-small">
                                        <i class="bi bi-info-circle me-1 text-teal"></i>Haz clic en el mapa o arrastra el pin rojo para afinar la ubicación.
                                    </small>
                                    <a href="https://www.google.com/maps?q=<?= $initLat ?>,<?= $initLng ?>" target="_blank" rel="noopener" id="linkVerGoogleMaps" class="extra-small text-decoration-none fw-semibold text-teal <?= empty($coordGpsValor) ? 'd-none' : '' ?>">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Abrir en Google Maps
                                    </a>
                                </div>
                            </div>

                            <div id="ubicacionManual" class="d-none mt-2">
                                <label for="coordenadas_gps" class="form-label fw-semibold small text-dark mb-1">Coordenadas GPS (Latitud, Longitud)</label>
                                <input type="text" 
                                       id="coordenadas_gps" 
                                       name="coordenadas_gps" 
                                       class="form-control font-monospace rounded-3" 
                                       value="<?= htmlspecialchars($coordGpsValor) ?>"
                                       placeholder="-14.833300, -64.900000">
                                <small class="text-muted extra-small">Formato requerido: latitud, longitud separadas por coma (ej. -14.833300, -64.900000).</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Especial: Paradas Oficiales del Sindicato / Transporte -->
                <?php if ((int)$lugar['id_categoria'] === 9): ?>
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mt-4" id="seccion-paradas">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                            <div>
                                <h4 class="h5 fw-bold text-dark mb-0">
                                    <i class="bi bi-geo-alt-fill text-warning me-2"></i>Paradas y Casetas Oficiales
                                </h4>
                                <small class="text-muted extra-small">Registra todas las paradas físicas fijas que tiene este sindicato en la ciudad.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-teal-action rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAgregarParadaAdmin">
                                <i class="bi bi-plus-circle me-1"></i> Añadir Parada
                            </button>
                        </div>

                        <?php if (!empty($paradas)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle extra-small mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre de Parada</th>
                                            <th>Tipo</th>
                                            <th>Dirección / Ref.</th>
                                            <th>Coordenadas GPS</th>
                                            <th class="text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paradas as $p): ?>
                                            <tr>
                                                <td class="fw-bold text-dark">
                                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($p['nombre_parada']) ?>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill extra-small <?= $p['tipo_parada'] === 'TERMINAL' ? 'bg-primary' : ($p['tipo_parada'] === 'AEROPUERTO' ? 'bg-info text-dark' : 'bg-secondary') ?>">
                                                        <?= htmlspecialchars($p['tipo_parada']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted">
                                                    <?= htmlspecialchars($p['direccion'] ?: 'Sin dirección') ?>
                                                    <?php if (!empty($p['referencia'])): ?>
                                                        <br><small class="text-secondary">(<?= htmlspecialchars($p['referencia']) ?>)</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($p['latitud']) && !empty($p['longitud'])): ?>
                                                        <a href="https://www.google.com/maps?q=<?= $p['latitud'] ?>,<?= $p['longitud'] ?>" target="_blank" class="text-teal text-decoration-none fw-semibold">
                                                            <i class="bi bi-pin-map-fill me-1"></i><?= number_format((float)$p['latitud'], 4) ?>, <?= number_format((float)$p['longitud'], 4) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted fst-italic">Sin GPS</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <form method="POST" action="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/eliminar-parada" class="d-inline" onsubmit="return confirm('¿Eliminar esta parada del sindicato?');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                        <input type="hidden" name="id_parada" value="<?= (int)$p['id_parada'] ?>">
                                                        <input type="hidden" name="id_lugar" value="<?= (int)$lugar['id_lugar'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-xs rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Eliminar parada">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border text-center py-3 mb-0">
                                <i class="bi bi-geo-alt fs-3 text-muted d-block mb-1"></i>
                                <span class="small text-muted">Aún no hay paradas adicionales registradas para este sindicato. Solo se mostrará la ubicación principal.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- COLUMNA LATERAL (Clasificación, Contacto y Ubicación) -->
            <div class="col-lg-4">
                <!-- Tarjeta 3: Datos de Identidad y Clasificación -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h4 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-bookmark-star text-success me-2"></i>Clasificación
                    </h4>

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold small text-dark">Nombre del Establecimiento *</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               class="form-control fw-bold" 
                               value="<?= htmlspecialchars($lugar['nombre']) ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_lugar" class="form-label fw-semibold small text-dark">Tipo de Ficha *</label>
                        <select id="tipo_lugar" name="tipo_lugar" class="form-select" required>
                            <option value="COMERCIAL" <?= $lugar['tipo_lugar'] === 'COMERCIAL' ? 'selected' : '' ?>>Comercial (De Pago)</option>
                            <option value="PUBLICO" <?= $lugar['tipo_lugar'] === 'PUBLICO' ? 'selected' : '' ?>>Público (Gratuito / Institucional)</option>
                        </select>
                        <small class="text-muted extra-small">Determina las reglas de membresía y visibilidad.</small>
                    </div>

                    <div class="mb-3">
                        <label for="id_municipio" class="form-label fw-semibold small text-dark">Municipio del Beni *</label>
                        <select id="id_municipio" name="id_municipio" class="form-select" required>
                            <?php foreach ($municipios as $m): ?>
                                <option value="<?= (int)$m['id_municipio'] ?>" <?= ((int)($lugar['id_municipio'] ?? 1) === (int)$m['id_municipio']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['provincia']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_categoria" class="form-label fw-semibold small text-dark">Categoría Turística *</label>
                        <select id="id_categoria" name="id_categoria" class="form-select" required>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= (int)$c['id_categoria'] ?>" <?= (int)$lugar['id_categoria'] === (int)$c['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="horario_atencion" class="form-label fw-semibold small text-dark">Horario de Atención</label>
                        <input type="text" 
                               id="horario_atencion" 
                               name="horario_atencion" 
                               class="form-control" 
                               value="<?= htmlspecialchars($lugar['horario_atencion'] ?? '') ?>"
                               placeholder="Ej. Lun - Sáb: 08:00 - 22:00">
                    </div>
                </div>

                <!-- Tarjeta 4: Canales de Contacto Directo -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h4 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-telephone text-success me-2"></i>Canales de Contacto
                    </h4>

                    <div class="mb-3">
                        <label for="whatsapp_contacto" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-whatsapp text-success me-1"></i> WhatsApp
                        </label>
                        <input type="text" 
                               id="whatsapp_contacto" 
                               name="whatsapp_contacto" 
                               class="form-control" 
                               value="<?= htmlspecialchars($lugar['whatsapp_contacto'] ?? '') ?>"
                               placeholder="Ej. 76891234 o +591 76891234">
                        <small class="text-muted extra-small">Los turistas podrán chatear con el local con 1 clic.</small>
                    </div>

                    <div class="mb-3">
                        <label for="telefono_contacto" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-telephone me-1 text-primary"></i> Teléfono / Celular
                        </label>
                        <input type="text" 
                               id="telefono_contacto" 
                               name="telefono_contacto" 
                               class="form-control" 
                               value="<?= htmlspecialchars($lugar['telefono_contacto'] ?? '') ?>"
                               placeholder="Ej. 34621234 o 71234567">
                    </div>

                    <div class="mb-3">
                        <label for="email_contacto" class="form-label fw-semibold small text-dark">
                            <i class="bi bi-envelope me-1 text-secondary"></i> Correo Electrónico
                        </label>
                        <input type="email" 
                               id="email_contacto" 
                               name="email_contacto" 
                               class="form-control" 
                               value="<?= htmlspecialchars($lugar['email_contacto'] ?? '') ?>" 
                               placeholder="contacto@negocio.com">
                    </div>

                    <!-- Redes Sociales Oficiales -->
                    <div class="pt-3 border-top">
                        <label class="form-label fw-bold small text-dark mb-2">
                            <i class="bi bi-share text-primary me-1"></i> Redes Sociales Oficiales
                        </label>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-tiktok"></i></span>
                                <input type="url" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($lugar['tiktok_url'] ?? '') ?>" placeholder="https://tiktok.com/@...">
                            </div>
                            <small class="text-muted extra-small">TikTok</small>
                        </div>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-facebook"></i></span>
                                <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($lugar['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
                            </div>
                            <small class="text-muted extra-small">Facebook</small>
                        </div>
                        <div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-white" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);"><i class="bi bi-instagram"></i></span>
                                <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($lugar['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
                            </div>
                            <small class="text-muted extra-small">Instagram</small>
                        </div>
                    </div>
                </div>


                <!-- Tarjeta 5.5: Portal Comercial y Credenciales de Acceso (Nivel 4) -->
                <?php if ($lugar['tipo_lugar'] === 'COMERCIAL'): ?>
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4" id="tarjetaCredencialesComercio">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h4 class="h5 fw-bold text-dark mb-0">
                                <i class="bi bi-shield-lock-fill text-success me-2"></i>Acceso Comercial
                            </h4>
                            <?php if ($cuentaNegocio): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Cuenta Activa
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Sin Cuenta
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($cuentaNegocio): ?>
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block extra-small text-uppercase fw-bold">Usuario:</span>
                                        <code class="fs-6 fw-bold text-success" id="cuentaUsuarioDisplay"><?= htmlspecialchars($cuentaNegocio['usuario']) ?></code>
                                    </div>
                                    <div class="col-6 text-end">
                                        <span class="text-muted d-block extra-small text-uppercase fw-bold">Cobertura:</span>
                                        <span class="fw-bold text-dark"><?= $ultimoVencimiento ? date('d/m/Y', strtotime($ultimoVencimiento)) : 'Sin vigencia' ?></span>
                                    </div>
                                    <div class="col-12 mt-2 pt-2 border-top">
                                        <span class="text-muted extra-small">Último acceso: <?= $cuentaNegocio['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($cuentaNegocio['ultimo_acceso'])) : 'Aún no ha iniciado sesión' ?></span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" 
                                    class="btn btn-warning rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" 
                                    id="btnRestablecerClaveEditar" 
                                    data-id-lugar="<?= (int)$lugar['id_lugar'] ?>">
                                <i class="bi bi-key-fill fs-5"></i>
                                <span>Restablecer y Enviar Clave</span>
                            </button>
                            <small class="text-muted extra-small d-block text-center mt-2">
                                Genera una nueva contraseña temporal y te permite enviársela por WhatsApp o imprimir su recibo oficial.
                            </small>
                        <?php else: ?>
                            <div class="alert alert-warning border-0 rounded-3 small mb-3">
                                <i class="bi bi-info-circle me-1"></i> Esta ficha comercial aún no cuenta con usuario de acceso para gestionar sus fotos y datos.
                            </div>
                            <button type="button" 
                                    class="btn btn-success rounded-pill w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalAprovisionarCuentaDirecta">
                                <i class="bi bi-person-plus-fill fs-5"></i>
                                <span>Aprovisionar Cuenta y Plan</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Tarjeta 6: Barra de Acciones y Guardado -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-top" style="top: 20px; z-index: 10;">
                    <span class="text-muted extra-small fw-bold text-uppercase d-block mb-3">Acciones de Guardado</span>

                    <button type="submit" class="btn btn-success rounded-pill w-100 py-2 fw-bold shadow-sm mb-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check2-circle fs-5"></i>
                        <span>Guardar Cambios</span>
                    </button>

                    <button type="submit" name="guardar_y_continuar" value="1" class="btn btn-outline-success rounded-pill w-100 py-2 fw-semibold mb-3">
                        <i class="bi bi-save me-1"></i> Guardar y Seguir Editando
                    </button>

                    <div class="border-top pt-3 text-center">
                        <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="btn btn-light rounded-pill px-4 btn-sm text-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal para Confirmar Eliminación de Foto -->
<div class="modal fade" id="modalConfirmarEliminarFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-trash3-fill fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">¿Eliminar fotografía?</h5>
                <p class="text-muted small mb-4">Esta imagen se borrará permanentemente del servidor y ya no se mostrará en el catálogo.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-semibold" id="btnConfirmarBorradoFoto">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprovisionar Cuenta y Plan Directo (Nivel 4) -->
<div class="modal fade" id="modalAprovisionarCuentaDirecta" tabindex="-1" aria-labelledby="tituloModalAprovDirecta" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 shadow" id="formAprovisionarDirecta">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="id_lugar" value="<?= (int)$lugar['id_lugar'] ?>">
            <div class="modal-header bg-success text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="tituloModalAprovDirecta">Aprovisionar Cuenta Comercial</h5>
                        <small class="text-white-50">Crear portal de autoservicio y suscripción</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Se generará el usuario, contraseña inicial y vigencia para <strong><?= htmlspecialchars($lugar['nombre']) ?></strong>.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Plan o Tarifa *</label>
                    <select name="codigo_plan" class="form-select" required>
                        <?php if (!empty($tarifas)): ?>
                            <?php foreach ($tarifas as $tar): ?>
                                <option value="<?= htmlspecialchars($tar['codigo_plan']) ?>">
                                    <?= htmlspecialchars($tar['nombre']) ?> · Bs <?= number_format($tar['monto'], 2) ?> (<?= (int)$tar['meses_duracion'] ?> mes<?= (int)$tar['meses_duracion'] > 1 ? 'es' : '' ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="MENSUAL">Plan Mensual (Bs. 50.00)</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Método de Pago *</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="EFECTIVO" selected>💵 Efectivo (Cobrado en mano)</option>
                        <option value="QR">📱 QR Simple</option>
                        <option value="TRANSFERENCIA">🏦 Transferencia Bancaria</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nº de Recibo / Talonario (opcional)</label>
                    <input type="text" name="numero_comprobante" class="form-control" placeholder="Ej. REC-00124">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Aprovisionar y Generar Clave
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Registrar Nueva Parada (Sindicato de Transporte) -->
<div class="modal fade" id="modalAgregarParadaAdmin" tabindex="-1" aria-labelledby="tituloModalParada" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/guardar-parada" method="POST" class="modal-content rounded-4 border-0 shadow">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="id_lugar" value="<?= (int)$lugar['id_lugar'] ?>">
            
            <div class="modal-header bg-warning text-dark py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="tituloModalParada">Registrar Parada Oficial</h5>
                        <small class="text-dark text-opacity-75">Asociada a: <?= htmlspecialchars($lugar['nombre']) ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold small text-dark">Nombre de la Parada o Caseta *</label>
                        <input type="text" name="nombre_parada" id="paradaNombreInput" class="form-control rounded-pill px-3" placeholder="Ej: Caseta Terminal de Buses, Parada Aeropuerto" required>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-dark">Tipo de Parada *</label>
                        <select name="tipo_parada" id="paradaTipoSelect" class="form-select rounded-pill px-3">
                            <option value="PARADA_URBANA" selected>Parada Urbana</option>
                            <option value="TERMINAL">Terminal de Buses</option>
                            <option value="AEROPUERTO">Aeropuerto</option>
                            <option value="CENTRAL">Central / Oficina Principal</option>
                            <option value="MERCADO">Mercado / Feria</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold small text-dark mb-0">
                                <i class="bi bi-pin-map-fill text-danger me-1"></i>Ubicar en el Mapa de Trinidad (Haz clic o arrastra el marcador)
                            </label>
                            <div class="d-flex gap-1 align-items-center">
                                <span class="badge bg-secondary-subtle text-secondary extra-small" id="badgeParadaCoords"></span>
                            </div>
                        </div>
                        <div id="mapaParadaPicker" style="height: 240px; border-radius: 12px; border: 1px solid #ced4da; z-index: 1;"></div>
                        <div class="d-flex justify-content-between align-items-center mt-1 flex-wrap gap-1">
                            <small class="text-muted extra-small">Puntos frecuentes en Trinidad:</small>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary py-0 px-2 extra-small" id="btnPresetTerminal">Terminal Buses</button>
                                <button type="button" class="btn btn-outline-secondary py-0 px-2 extra-small" id="btnPresetAeropuerto">Aeropuerto</button>
                                <button type="button" class="btn btn-outline-secondary py-0 px-2 extra-small" id="btnPresetMercado">Mdo. Pompeya</button>
                                <button type="button" class="btn btn-outline-secondary py-0 px-2 extra-small" id="btnPresetPlaza">Plaza Principal</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Latitud GPS</label>
                        <input type="number" step="0.00000001" name="latitud" id="paradaLatInput" class="form-control rounded-pill px-3" placeholder="-14.8325" value="-14.835100" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Longitud GPS</label>
                        <input type="number" step="0.00000001" name="longitud" id="paradaLngInput" class="form-control rounded-pill px-3" placeholder="-64.9012" value="-64.903800" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Dirección de la Parada</label>
                        <input type="text" name="direccion" id="paradaDireccionInput" class="form-control rounded-pill px-3" placeholder="Ej: Av. Panamericana esq. Circunvalación">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Punto de Referencia</label>
                        <input type="text" name="referencia" class="form-control rounded-pill px-3" placeholder="Ej: Caseta blanca frente a la puerta 2">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Teléfono Directo</label>
                        <input type="text" name="telefono_directo" class="form-control rounded-pill px-3" placeholder="Ej: 34621234">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">WhatsApp Directo</label>
                        <input type="text" name="whatsapp_directo" class="form-control rounded-pill px-3" placeholder="Ej: 71234567">
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-save me-1"></i> Guardar Parada
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Credenciales Generadas (Compartido) -->
<?php require dirname(__DIR__) . '/parciales/modal_credenciales.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/admin/credenciales-modal.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/admin/credenciales-modal.js') ?>" defer></script>
<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/shared/compresor-imagen.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/shared/compresor-imagen.js') ?>"></script>

<!-- Script Interactivo para Gestión Dinámica de Fotos y Previsualizaciones -->
<script>

document.addEventListener('DOMContentLoaded', () => {
    const baseUrl = '<?= htmlspecialchars($baseUrl) ?>';
    const csrfToken = document.getElementById('csrfTokenGlobal').value;
    const idLugar = document.getElementById('idLugarGlobal').value;

    // 1. Manejo de Subida Múltiple y Previsualización Local
    const dropzone = document.getElementById('dropzoneFotos');
    const inputFotos = document.getElementById('inputFotografiasNuevas');
    const previewContainer = document.getElementById('previewFotosNuevasContainer');
    const previewGrid = document.getElementById('gridPreviewNuevasFotos');
    const conteoNuevasFotos = document.getElementById('conteoNuevasFotos');
    const btnLimpiar = document.getElementById('btnLimpiarNuevasFotos');

    if (dropzone && inputFotos) {
        dropzone.addEventListener('click', () => inputFotos.click());

        // Arrastrar y soltar
        ['dragenter', 'dragover'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-success', 'bg-light');
            });
        });

        ['dragleave', 'drop'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-success', 'bg-light');
            });
        });

        dropzone.addEventListener('drop', async (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                let files = e.dataTransfer.files;
                if (window.CompresorImagen) {
                    conteoNuevasFotos.textContent = 'Optimizando fotos en el navegador...';
                    const res = await Promise.all(Array.from(files).map(f => window.CompresorImagen.comprimir(f, { maxDimension: 1280, calidad: 0.82 })));
                    if (typeof DataTransfer !== 'undefined') {
                        const dt = new DataTransfer();
                        res.forEach(r => dt.items.add(r.file));
                        inputFotos.files = dt.files;
                    }
                } else {
                    inputFotos.files = files;
                }
                mostrarPrevisualizaciones(inputFotos.files);
            }
        });

        inputFotos.addEventListener('change', async () => {
            if (window.CompresorImagen && inputFotos.files.length > 0) {
                previewContainer.classList.remove('d-none');
                conteoNuevasFotos.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-success"></span> Optimizando fotos en el navegador...';
                const res = await Promise.all(Array.from(inputFotos.files).map(f => window.CompresorImagen.comprimir(f, { maxDimension: 1280, calidad: 0.82 })));
                if (typeof DataTransfer !== 'undefined') {
                    const dt = new DataTransfer();
                    res.forEach(r => dt.items.add(r.file));
                    inputFotos.files = dt.files;
                }
            }
            mostrarPrevisualizaciones(inputFotos.files);
        });

        btnLimpiar?.addEventListener('click', () => {
            inputFotos.value = '';
            previewGrid.innerHTML = '';
            previewContainer.classList.add('d-none');
        });
    }

    function mostrarPrevisualizaciones(files) {
        if (!files || files.length === 0) {
            previewContainer.classList.add('d-none');
            previewGrid.innerHTML = '';
            return;
        }

        previewGrid.innerHTML = '';
        conteoNuevasFotos.textContent = `${files.length} fotografía(s) seleccionada(s) para añadir`;
        previewContainer.classList.remove('d-none');

        Array.from(files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const col = document.createElement('div');
            col.className = 'col-4 col-sm-3 col-md-2';
            
            const card = document.createElement('div');
            card.className = 'card h-100 border rounded-3 overflow-hidden shadow-sm position-relative';
            
            const img = document.createElement('img');
            img.className = 'w-100 object-fit-cover';
            img.style.height = '85px';
            
            const reader = new FileReader();
            reader.onload = (e) => { img.src = e.target.result; };
            reader.readAsDataURL(file);

            const badge = document.createElement('span');
            badge.className = 'badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-1 extra-small';
            badge.textContent = Math.round(file.size / 1024) + ' KB';

            card.appendChild(img);
            card.appendChild(badge);
            col.appendChild(card);
            previewGrid.appendChild(col);
        });
    }

    // 2. Eliminación de Fotografías Existentes (AJAX con Modal)
    let fotoIdAEliminar = null;
    const modalEl = document.getElementById('modalConfirmarEliminarFoto');
    const modalConfirmacion = modalEl && window.bootstrap ? new window.bootstrap.Modal(modalEl) : null;
    const btnConfirmarBorrado = document.getElementById('btnConfirmarBorradoFoto');

    document.querySelectorAll('.btn-eliminar-foto-ajax').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            fotoIdAEliminar = btn.getAttribute('data-id-foto');
            if (modalConfirmacion) {
                modalConfirmacion.show();
            } else if (confirm('¿Estás seguro de eliminar esta fotografía de la ficha?')) {
                ejecutarEliminacion(fotoIdAEliminar);
            }
        });
    });

    btnConfirmarBorrado?.addEventListener('click', () => {
        if (fotoIdAEliminar) {
            ejecutarEliminacion(fotoIdAEliminar);
            modalConfirmacion?.hide();
        }
    });

    function ejecutarEliminacion(idFoto) {
        const cardFoto = document.getElementById(`card-foto-${idFoto}`);
        if (cardFoto) {
            cardFoto.style.opacity = '0.5';
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('id_fotografia', idFoto);
        formData.append('id_lugar', idLugar);

        fetch(`${baseUrl}/admin/lugares/eliminar-foto`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (cardFoto) {
                    cardFoto.style.transition = 'all 0.3s ease';
                    cardFoto.style.transform = 'scale(0.8)';
                    cardFoto.style.opacity = '0';
                    setTimeout(() => {
                        cardFoto.remove();
                        actualizarConteoFotos();
                    }, 300);
                }
            } else {
                alert(data.message || 'Error al eliminar la fotografía.');
                if (cardFoto) cardFoto.style.opacity = '1';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            // Fallback: Si falla la petición AJAX, enviar formulario tradicional
            enviarFormularioDirecto(`${baseUrl}/admin/lugares/eliminar-foto`, {
                csrf_token: csrfToken,
                id_fotografia: idFoto,
                id_lugar: idLugar
            });
        });
    }

    // 3. Establecer Fotografía como Portada Principal (AJAX)
    document.querySelectorAll('.btn-establecer-portada-ajax').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const idFoto = btn.getAttribute('data-id-foto');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('id_fotografia', idFoto);
            formData.append('id_lugar', idLugar);

            fetch(`${baseUrl}/admin/lugares/establecer-portada`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Recargar suavemente para reflejar la nueva portada en todos los badges
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al cambiar portada.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-star me-1 text-warning"></i> Portada';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                enviarFormularioDirecto(`${baseUrl}/admin/lugares/establecer-portada`, {
                    csrf_token: csrfToken,
                    id_fotografia: idFoto,
                    id_lugar: idLugar
                });
            });
        });
    });

    function actualizarConteoFotos() {
        const totalRestantes = document.querySelectorAll('.item-foto-galeria').length;
        const badgeConteo = document.getElementById('conteoFotosGaleria');
        if (badgeConteo) {
            badgeConteo.textContent = `${totalRestantes} foto(s) registrada(s)`;
        }
        if (totalRestantes === 0) {
            const grid = document.getElementById('gridFotografiasActuales');
            if (grid) {
                grid.innerHTML = `
                    <div class="col-12" id="estadoVacioGaleria">
                        <div class="text-center p-4 bg-light rounded-4 border border-dashed">
                            <div class="rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center p-3 text-secondary mb-2" style="width: 60px; height: 60px;">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Sin Fotografías Registradas</h6>
                            <p class="text-muted small mb-0">Esta ficha aún no cuenta con imágenes. Puedes seleccionar y subir varias a continuación.</p>
                        </div>
                    </div>
                `;
            }
        }
    }

    function enviarFormularioDirecto(url, params) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        for (const key in params) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    }

    // 4. Inicialización del Mapa Interactivo Leaflet y Selector GPS
    const contenedorMapa = document.getElementById('mapaEditarFicha');
    const inputGps = document.getElementById('coordenadas_gps');
    const badgeCoords = document.getElementById('badgeEstadoCoords');
    const estadoMapa = document.getElementById('estadoMapaEditarFicha');
    const linkGoogleMaps = document.getElementById('linkVerGoogleMaps');
    const btnGps = document.getElementById('btnCapturarGpsAdmin');
    const panelMapa = document.getElementById('ubicacionMapa');
    const panelManual = document.getElementById('ubicacionManual');
    const botonesModoUbicacion = document.querySelectorAll('[data-location-mode]');

    let mapaEditar = null;
    let marcadorEditar = null;

    const actualizarUbicacionUI = (lat, lng, actualizarInput = true) => {
        const latNum = Number(lat);
        const lngNum = Number(lng);
        const coordsStr = `${latNum.toFixed(6)}, ${lngNum.toFixed(6)}`;

        if (actualizarInput && inputGps) {
            inputGps.value = coordsStr;
        }
        if (badgeCoords) {
            badgeCoords.textContent = coordsStr;
        }
        if (estadoMapa) {
            estadoMapa.innerHTML = `<i class="bi bi-check-circle-fill me-1 text-teal"></i>Ubicación fijada: <strong>${coordsStr}</strong>`;
        }
        if (linkGoogleMaps) {
            linkGoogleMaps.href = `https://www.google.com/maps?q=${coordsStr}`;
            linkGoogleMaps.classList.remove('d-none');
        }
    };

    const colocarMarcadorEditar = (lat, lng, actualizarInput = true) => {
        if (!mapaEditar || !window.L) return;
        const latNum = Number(lat);
        const lngNum = Number(lng);

        if (marcadorEditar) {
            marcadorEditar.setLatLng([latNum, lngNum]);
        } else {
            marcadorEditar = L.marker([latNum, lngNum], { draggable: true }).addTo(mapaEditar);
            marcadorEditar.on('dragend', () => {
                const pos = marcadorEditar.getLatLng();
                colocarMarcadorEditar(pos.lat, pos.lng, true);
            });
        }
        actualizarUbicacionUI(latNum, lngNum, actualizarInput);
    };

    const iniciarMapaEditar = () => {
        if (mapaEditar || !contenedorMapa || !window.L) return;

        const initLat = parseFloat(contenedorMapa.dataset.lat) || -14.8333;
        const initLng = parseFloat(contenedorMapa.dataset.lng) || -64.9000;
        const hasCoords = contenedorMapa.dataset.hasCoords === '1';

        mapaEditar = L.map('mapaEditarFicha', {
            zoomControl: true
        }).setView([initLat, initLng], hasCoords ? 16 : 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapaEditar);

        if (hasCoords || (inputGps && inputGps.value.trim())) {
            colocarMarcadorEditar(initLat, initLng, false);
        }

        mapaEditar.on('click', (e) => {
            colocarMarcadorEditar(e.latlng.lat, e.latlng.lng, true);
        });

        setTimeout(() => mapaEditar?.invalidateSize(), 250);
    };

    // Tabs Modo Mapa vs Modo Manual
    botonesModoUbicacion.forEach((btn) => {
        btn.addEventListener('click', () => {
            const modo = btn.dataset.locationMode;
            botonesModoUbicacion.forEach((b) => b.classList.toggle('active', b === btn));
            if (modo === 'mapa') {
                panelMapa?.classList.remove('d-none');
                panelManual?.classList.add('d-none');
                iniciarMapaEditar();
                setTimeout(() => mapaEditar?.invalidateSize(), 150);
            } else {
                panelMapa?.classList.add('d-none');
                panelManual?.classList.remove('d-none');
            }
        });
    });

    // Escuchar cambios en el input de coordenadas manuales
    inputGps?.addEventListener('change', () => {
        const match = inputGps.value.match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
        if (match) {
            const nLat = Number(match[1]);
            const nLng = Number(match[2]);
            if (mapaEditar) {
                mapaEditar.setView([nLat, nLng], 15);
                colocarMarcadorEditar(nLat, nLng, false);
            }
            actualizarUbicacionUI(nLat, nLng, false);
        }
    });

    // Captura GPS Geolocation
    btnGps?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalización GPS.');
            return;
        }

        const originalBtnHtml = btnGps.innerHTML;
        btnGps.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Obteniendo GPS...';
        btnGps.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                if (!mapaEditar) {
                    iniciarMapaEditar();
                }
                if (mapaEditar) {
                    mapaEditar.setView([lat, lng], 16);
                    colocarMarcadorEditar(lat, lng, true);
                } else {
                    actualizarUbicacionUI(lat, lng, true);
                }
                btnGps.innerHTML = '<i class="bi bi-check2 text-success me-1"></i> ¡GPS capturado!';
                setTimeout(() => {
                    btnGps.innerHTML = originalBtnHtml;
                    btnGps.disabled = false;
                }, 3000);
            },
            (err) => {
                alert('No se pudo obtener tu ubicación GPS: ' + err.message);
                btnGps.innerHTML = originalBtnHtml;
                btnGps.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Iniciar el mapa automáticamente al cargar
    if (panelMapa && !panelMapa.classList.contains('d-none')) {
        iniciarMapaEditar();
    }

    // 5. Restablecimiento de Contraseña para Comercios (Nivel 4)
    const btnRestablecer = document.getElementById('btnRestablecerClaveEditar');
    if (btnRestablecer) {
        btnRestablecer.addEventListener('click', async () => {
            if (!confirm('¿Deseas restablecer la clave de acceso de este comercio? Se creará una nueva clave temporal de inmediato.')) {
                return;
            }

            const idLugar = btnRestablecer.dataset.idLugar;
            const originalHtml = btnRestablecer.innerHTML;
            btnRestablecer.disabled = true;
            btnRestablecer.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generando clave...';

            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('id_lugar', idLugar);

                const res = await fetch(`${baseUrl}/admin/lugares/restablecer-clave`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await res.json();
                if (data.success && data.data) {
                    if (window.BeniTursModalCredenciales) {
                        window.BeniTursModalCredenciales.mostrar(data.data);
                    }
                } else {
                    alert(data.message || 'Error al restablecer la contraseña.');
                }
            } catch (e) {
                alert('Error en la comunicación con el servidor: ' + e.message);
            } finally {
                btnRestablecer.disabled = false;
                btnRestablecer.innerHTML = originalHtml;
            }
        });
    }

    // 6. Aprovisionamiento Directo de Cuenta y Plan Comercial (Nivel 4)
    const formAprovDirecta = document.getElementById('formAprovisionarDirecta');
    if (formAprovDirecta) {
        formAprovDirecta.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = formAprovDirecta.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Aprovisionando...';

            try {
                const formData = new FormData(formAprovDirecta);
                const res = await fetch(`${baseUrl}/admin/lugares/crear-cuenta`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await res.json();
                if (data.success && data.data) {
                    const modalAprovEl = document.getElementById('modalAprovisionarCuentaDirecta');
                    if (modalAprovEl && window.bootstrap) {
                        bootstrap.Modal.getInstance(modalAprovEl)?.hide();
                    }
                    if (window.BeniTursModalCredenciales) {
                        window.BeniTursModalCredenciales.mostrar(data.data);
                    }
                    const modalCredEl = document.getElementById('modalCredencialesGeneradas');
                    modalCredEl?.addEventListener('hidden.bs.modal', () => {
                        window.location.reload();
                    }, { once: true });
                } else {
                    alert(data.message || 'Error al aprovisionar la cuenta.');
                }
            } catch (e) {
                alert('Error al comunicarse con el servidor: ' + e.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }

    // 7. Mapa Interactivo para Selección de Coordenadas de Parada
    const modalAgregarParada = document.getElementById('modalAgregarParadaAdmin');
    const inputParadaLat = document.getElementById('paradaLatInput');
    const inputParadaLng = document.getElementById('paradaLngInput');
    const badgeParadaCoords = document.getElementById('badgeParadaCoords');
    const paradaNombreInput = document.getElementById('paradaNombreInput');
    const paradaTipoSelect = document.getElementById('paradaTipoSelect');
    const paradaDireccionInput = document.getElementById('paradaDireccionInput');
    let mapPicker = null;
    let pickerMarker = null;

    const actualizarBadgeCoords = (lat, lng) => {
        if (badgeParadaCoords) {
            badgeParadaCoords.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    };

    const fijarPuntoParada = (lat, lng, nombre = null, tipo = null, dir = null) => {
        if (inputParadaLat) inputParadaLat.value = lat.toFixed(6);
        if (inputParadaLng) inputParadaLng.value = lng.toFixed(6);
        actualizarBadgeCoords(lat, lng);

        if (nombre && paradaNombreInput && !paradaNombreInput.value.trim()) {
            paradaNombreInput.value = nombre;
        }
        if (tipo && paradaTipoSelect) {
            paradaTipoSelect.value = tipo;
        }
        if (dir && paradaDireccionInput && !paradaDireccionInput.value.trim()) {
            paradaDireccionInput.value = dir;
        }

        if (mapPicker && pickerMarker) {
            pickerMarker.setLatLng([lat, lng]);
            mapPicker.setView([lat, lng], 15);
        }
    };

    if (modalAgregarParada) {
        modalAgregarParada.addEventListener('shown.bs.modal', () => {
            const initialLat = parseFloat(inputParadaLat?.value) || -14.835100;
            const initialLng = parseFloat(inputParadaLng?.value) || -64.903800;
            actualizarBadgeCoords(initialLat, initialLng);

            if (!mapPicker && window.L) {
                mapPicker = L.map('mapaParadaPicker', {
                    zoomControl: true,
                    attributionControl: false
                }).setView([initialLat, initialLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapPicker);

                pickerMarker = L.marker([initialLat, initialLng], {
                    draggable: true
                }).addTo(mapPicker);

                pickerMarker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    if (inputParadaLat) inputParadaLat.value = pos.lat.toFixed(6);
                    if (inputParadaLng) inputParadaLng.value = pos.lng.toFixed(6);
                    actualizarBadgeCoords(pos.lat, pos.lng);
                });

                mapPicker.on('click', (e) => {
                    fijarPuntoParada(e.latlng.lat, e.latlng.lng);
                });
            } else if (mapPicker) {
                mapPicker.invalidateSize();
                fijarPuntoParada(initialLat, initialLng);
            }
        });

        // Botones de presets rápidos
        document.getElementById('btnPresetTerminal')?.addEventListener('click', () => {
            fijarPuntoParada(-14.835100, -64.903800, 'Caseta Terminal de Buses', 'TERMINAL', 'Av. Panamericana esq. Circunvalación');
        });
        document.getElementById('btnPresetAeropuerto')?.addEventListener('click', () => {
            fijarPuntoParada(-14.819700, -64.918000, 'Caseta Aeropuerto Jorge Henrich', 'AEROPUERTO', 'Av. Aeropuerto (Caseta Principal)');
        });
        document.getElementById('btnPresetMercado')?.addEventListener('click', () => {
            fijarPuntoParada(-14.830200, -64.898500, 'Parada Mercado Pompeya', 'MERCADO', 'C. Cochabamba esq. Sucre');
        });
        document.getElementById('btnPresetPlaza')?.addEventListener('click', () => {
            fijarPuntoParada(-14.841064, -64.901175, 'Caseta Plaza Principal', 'CENTRAL', 'Plaza José Ballivián');
        });
    }
});
</script>