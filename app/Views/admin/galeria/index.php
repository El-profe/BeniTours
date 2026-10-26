<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-teal-subtle text-teal border border-teal-subtle rounded-pill px-3 py-1 fw-bold fs-7">
                    <i class="bi bi-images me-1"></i> Catálogo Visual
                </span>
                <span class="text-muted small">&bull; Portada y Provincias</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-camera-fill text-teal me-2"></i>Galería de Fotos Turísticas
            </h1>
            <p class="text-muted small mb-0">
                Sube y administra las postales, fotografías libres de paisajes y atractivos que se muestran en el Bento Grid de la portada.
            </p>
        </div>
        <button type="button" class="btn btn-teal rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2 text-white" data-bs-toggle="modal" data-bs-target="#modalSubirFotoGaleria">
            <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
            <span>Subir Nueva Postal</span>
        </button>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-xs mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Pestañas de Navegación: Mapa Departamental (8 Provincias) vs Mosaico Galería -->
    <ul class="nav nav-pills mb-4 gap-2" id="galeriaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-bold px-4 py-2 d-flex align-items-center gap-2 shadow-xs galeria-tab-pill" 
                    id="tab-mapa-btn" 
                    data-bs-toggle="pill" 
                    data-bs-target="#tab-mapa" 
                    type="button" 
                    role="tab">
                <i class="bi bi-map-fill"></i>
                <span>Fotos del Mapa Departamental (8 Provincias)</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-bold px-4 py-2 d-flex align-items-center gap-2 shadow-xs galeria-tab-pill" 
                    id="tab-bento-btn" 
                    data-bs-toggle="pill" 
                    data-bs-target="#tab-bento" 
                    type="button" 
                    role="tab">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>Mosaico Bento de la Portada (<?= count($fotos) ?>)</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="galeriaTabsContent">
        <!-- PESTAÑA 1: FOTOS DEL MAPA DEPARTAMENTAL (8 PROVINCIAS) -->
        <div class="tab-pane fade show active" id="tab-mapa" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-geo-alt-fill me-1 text-teal"></i> Fotografías de Revelación en Hover del Mapa Regional
                        </h6>
                        <small class="text-muted extra-small">
                            Estas son las 8 fotografías auténticas que se descubren al pasar el cursor sobre cada provincia en la portada.
                        </small>
                    </div>
                    <span class="badge bg-teal-subtle text-teal border rounded-pill px-3 py-1 extra-small fw-bold">
                        8 Provincias Oficiales
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <?php foreach ($fotosMapa as $prov): ?>
                            <?php 
                                $imgProv = !empty($prov['imagen']) 
                                    ? (str_starts_with($prov['imagen'], 'hero-') 
                                        ? "{$baseUrl}/assets/img/" . htmlspecialchars($prov['imagen'])
                                        : "{$baseUrl}/imagen?f=" . urlencode($prov['imagen']))
                                    : "{$baseUrl}/assets/img/hero-poster.webp";
                            ?>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card h-100 border rounded-4 overflow-hidden shadow-xs prov-card">
                                    <div class="position-relative" style="height: 140px; background-color: #0f172a;">
                                        <img src="<?= htmlspecialchars($imgProv) ?>" 
                                             alt="<?= htmlspecialchars($prov['nombre_provincia']) ?>" 
                                             class="w-100 h-100 object-fit-cover"
                                             loading="lazy">
                                        <span class="badge bg-dark bg-opacity-75 text-white extra-small rounded-pill position-absolute top-0 start-0 m-2">
                                            <?= htmlspecialchars(strtoupper($prov['codigo'])) ?>
                                        </span>
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($prov['nombre_provincia']) ?></h6>
                                            <p class="text-muted extra-small mb-2 text-truncate" title="<?= htmlspecialchars($prov['titulo'] ?? '') ?>">
                                                <?= htmlspecialchars($prov['titulo'] ?? 'Atractivo turístico') ?>
                                            </p>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-outline-teal btn-sm rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-1 mt-2"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarFotoMapa"
                                                data-codigo="<?= htmlspecialchars($prov['codigo']) ?>"
                                                data-nombre="<?= htmlspecialchars($prov['nombre_provincia']) ?>"
                                                data-titulo="<?= htmlspecialchars($prov['titulo'] ?? '') ?>"
                                                data-foto="<?= htmlspecialchars($imgProv) ?>">
                                            <i class="bi bi-camera me-1"></i> Cambiar Foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 2: MOSAICO BENTO DE LA PORTADA -->
        <div class="tab-pane fade" id="tab-bento" role="tabpanel">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-images me-1 text-teal"></i> Fotos en el Mosaico (<?= count($fotos) ?>)
                    </h6>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 extra-small">
                        Se muestran las primeras 8 activas en la portada
                    </span>
                </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light extra-small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4" style="width: 100px;">Foto</th>
                        <th>Título y Descripción</th>
                        <th>Vínculo Turístico</th>
                        <th class="text-center" style="width: 130px;">Formato</th>
                        <th class="text-center" style="width: 120px;">Estado</th>
                        <th class="text-end pe-4" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fotos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-camera display-4 d-block mb-2 text-secondary"></i>
                                <p class="mb-0">No hay fotos en la galería aún. Sube la primera postal del Beni.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fotos as $f): ?>
                            <?php $imgUrl = "{$baseUrl}/imagen?f=" . urlencode($f['nombre_archivo']) . "&v=" . (int)$f['id_galeria']; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="position-relative rounded-3 overflow-hidden shadow-xs" style="width: 80px; height: 60px; background-color: #f1f5f9;">
                                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Foto" class="w-100 h-100 object-fit-cover" loading="lazy">
                                        <?php if (!empty($f['es_destacada'])): ?>
                                            <span class="position-absolute top-0 start-0 badge bg-warning text-dark extra-small rounded-0 rounded-bottom-end px-1" title="Foto principal grande">
                                                <i class="bi bi-star-fill"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($f['titulo']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($f['subtitulo'] ?: 'Trinidad, Beni') ?></div>
                                    <?php if (!empty($f['creditos_autor'])): ?>
                                        <div class="extra-small text-teal mt-1">
                                            <i class="bi bi-camera-fill me-1"></i>Crédito: 
                                            <?php if (!empty($f['url_fuente'])): ?>
                                                <a href="<?= htmlspecialchars($f['url_fuente']) ?>" target="_blank" rel="noopener noreferrer" class="text-teal text-decoration-underline fw-semibold">
                                                    <?= htmlspecialchars($f['creditos_autor']) ?> <i class="bi bi-box-arrow-up-right extra-small"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="fw-semibold"><?= htmlspecialchars($f['creditos_autor']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($f['id_lugar']) && !empty($f['lugar_nombre'])): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 extra-small">
                                            <i class="bi bi-link-45deg me-1"></i><?= htmlspecialchars($f['lugar_nombre']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 extra-small">
                                            <i class="bi bi-image me-1"></i> Postal Libre
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($f['es_destacada'])): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 extra-small fw-bold">
                                            <i class="bi bi-aspect-ratio-fill me-1"></i> Grande 2x2
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" action="<?= htmlspecialchars($baseUrl) ?>/admin/galeria/destacar" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <input type="hidden" name="id_galeria" value="<?= (int)$f['id_galeria'] ?>">
                                            <button type="submit" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 extra-small text-dark" title="Hacer foto principal grande">
                                                Hacer Grande
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="<?= htmlspecialchars($baseUrl) ?>/admin/galeria/cambiar-estado" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                        <input type="hidden" name="id_galeria" value="<?= (int)$f['id_galeria'] ?>">
                                        <?php if (!empty($f['activo'])): ?>
                                            <input type="hidden" name="activo" value="0">
                                            <button type="submit" class="btn btn-xs btn-success-subtle text-success rounded-pill px-2 py-1 extra-small fw-bold border-0" title="Pulsar para ocultar">
                                                <i class="bi bi-eye-fill me-1"></i> Visible
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="activo" value="1">
                                            <button type="submit" class="btn btn-xs btn-secondary-subtle text-secondary rounded-pill px-2 py-1 extra-small fw-bold border-0" title="Pulsar para mostrar">
                                                <i class="bi bi-eye-slash-fill me-1"></i> Oculto
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="<?= htmlspecialchars($baseUrl) ?>/admin/galeria/eliminar" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta fotografía de la galería?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                        <input type="hidden" name="id_galeria" value="<?= (int)$f['id_galeria'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div> <!-- Cierre #tab-bento -->
</div> <!-- Cierre #galeriaTabsContent -->
</div> <!-- Cierre .container-fluid -->

<!-- Modal para Subir Nueva Postal a la Galería -->
<div class="modal fade" id="modalSubirFotoGaleria" tabindex="-1" aria-labelledby="modalSubirFotoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #0E4D5E, #2A9D8F);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-camera-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalSubirFotoTitulo">Subir Postal a la Galería</h5>
                        <small class="text-white-50">Aparecerá en el Bento Grid de la portada</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/galeria/guardar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-image text-success me-1"></i> Archivo de Imagen *
                        </label>
                        <input type="file" name="imagen" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp" required>
                        <div class="form-text extra-small">Formatos: JPG, PNG o WebP. Recomendado en buena resolución.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-type-h1 text-success me-1"></i> Título de la Postal *
                        </label>
                        <input type="text" name="titulo" class="form-control rounded-pill px-3" placeholder="Ej: Atardecer en Laguna Suárez, Macheteros..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-card-text text-secondary me-1"></i> Subtítulo / Ubicación (Opcional)
                        </label>
                        <input type="text" name="subtitulo" class="form-control rounded-pill px-3" placeholder="Ej: Río Mamoré, Atardecer en el Puerto...">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-person-badge text-success me-1"></i> Autor / Fotógrafo (Opcional)
                            </label>
                            <input type="text" name="creditos_autor" class="form-control rounded-pill px-3" placeholder="Ej: @juanperez_fotos o Fanpage Turística">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-link-45deg text-primary me-1"></i> Enlace Fuente / Red Social (Opcional)
                            </label>
                            <input type="url" name="url_fuente" class="form-control rounded-pill px-3" placeholder="https://instagram.com/... o facebook.com/...">
                        </div>
                        <div class="col-12">
                            <span class="form-text extra-small text-muted">
                                <i class="bi bi-shield-check text-success me-1"></i> Al dar crédito al fotógrafo o página original, proteges la web de reclamos por derechos de autor.
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-link-45deg text-primary me-1"></i> Vincular a un Lugar Turístico (Opcional)
                        </label>
                        <select name="id_lugar" class="form-select rounded-pill px-3 text-dark small">
                            <option value="">-- Postal Libre (Sin vincular a ningún lugar) --</option>
                            <?php foreach ($lugares as $lug): ?>
                                <option value="<?= (int)$lug['id_lugar'] ?>">
                                    <?= htmlspecialchars($lug['nombre']) ?> (<?= htmlspecialchars($lug['categoria_nombre'] ?? 'Turismo') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text extra-small">Si lo vinculas, el botón del visor llevará a la ficha de ese lugar.</div>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="es_destacada" value="1" id="checkEsDestacada">
                        <label class="form-check-label small fw-bold text-dark" for="checkEsDestacada">
                            Marcar como Foto Principal Grande (2x2)
                        </label>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4 fw-bold btn-sm shadow-sm text-white">
                        <i class="bi bi-upload me-1"></i> Guardar Postal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Fotografía de una Provincia del Mapa -->
<div class="modal fade" id="modalEditarFotoMapa" tabindex="-1" aria-labelledby="modalEditarFotoMapaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #0E4D5E, #2A9D8F);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-map-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalEditarFotoMapaTitulo">Cambiar Foto de Provincia</h5>
                        <small class="text-white-50" id="mapaProvinciaNombreTxt">Provincia del Beni</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/galeria/actualizar-mapa" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="codigo_provincia" id="inputCodigoProvincia" value="">

                <div class="modal-body p-4 bg-white">
                    <!-- Vista previa actual -->
                    <div class="mb-3 text-center">
                        <div class="position-relative rounded-4 overflow-hidden shadow-sm mx-auto border" style="max-width: 320px; height: 160px; background-color: #0f172a;">
                            <img src="" id="mapaFotoPreviewImg" alt="Vista previa" class="w-100 h-100 object-fit-cover">
                        </div>
                        <small class="text-muted extra-small d-block mt-2">
                            <i class="bi bi-eye-fill text-success me-1"></i>Fotografía actual revelada al hacer hover
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-cloud-arrow-up-fill text-success me-1"></i> Subir Nueva Fotografía *
                        </label>
                        <input type="file" name="imagen" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp" required>
                        <div class="form-text extra-small">Formato JPG, PNG o WebP. Se adaptará automáticamente a la silueta de la provincia.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="bi bi-type-h1 text-success me-1"></i> Título o Atractivo Representativo
                        </label>
                        <input type="text" name="titulo_provincia" id="inputTituloProvincia" class="form-control rounded-pill px-3" placeholder="Ej: Rurrenabaque y Madidi...">
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4 fw-bold btn-sm shadow-sm text-white">
                        <i class="bi bi-check-circle-fill me-1"></i> Guardar en el Mapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos específicos de la paleta para Galería */
.galeria-tab-pill {
    color: #475569;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}
.galeria-tab-pill:hover {
    color: #0E4D5E;
    background-color: #E2F2F0;
    border-color: #B8E1DA;
}
.galeria-tab-pill.active {
    background-color: #2A9D8F !important;
    color: #ffffff !important;
    border-color: #2A9D8F !important;
    box-shadow: 0 4px 12px rgba(42, 157, 143, 0.3) !important;
}
.btn-teal {
    background-color: #2A9D8F !important;
    border-color: #2A9D8F !important;
    color: #ffffff !important;
    transition: all 0.2s ease;
}
.btn-teal:hover, .btn-teal:focus {
    background-color: #217E73 !important;
    border-color: #217E73 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(42, 157, 143, 0.3) !important;
}
.btn-outline-teal {
    border-color: #2A9D8F !important;
    color: #217E73 !important;
    background-color: #ffffff !important;
    transition: all 0.2s ease;
}
.btn-outline-teal:hover, .btn-outline-teal:focus {
    background-color: #2A9D8F !important;
    border-color: #2A9D8F !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(42, 157, 143, 0.25) !important;
}
.prov-card {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-color: #e2e8f0;
}
.prov-card:hover {
    transform: translateY(-3px);
    border-color: #8BCBC2 !important;
    box-shadow: 0 10px 24px rgba(14, 77, 94, 0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Manejo de tabs según el hash de URL
    if (window.location.hash === '#tab-bento') {
        const tabBentoBtn = document.getElementById('tab-bento-btn');
        if (tabBentoBtn) tabBentoBtn.click();
    } else if (window.location.hash === '#tab-mapa') {
        const tabMapaBtn = document.getElementById('tab-mapa-btn');
        if (tabMapaBtn) tabMapaBtn.click();
    }

    // Modal para editar foto del mapa
    const modalEditarMapa = document.getElementById('modalEditarFotoMapa');
    if (modalEditarMapa) {
        modalEditarMapa.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const codigo = button.getAttribute('data-codigo') || '';
            const nombre = button.getAttribute('data-nombre') || '';
            const titulo = button.getAttribute('data-titulo') || '';
            const foto = button.getAttribute('data-foto') || '';

            document.getElementById('inputCodigoProvincia').value = codigo;
            document.getElementById('mapaProvinciaNombreTxt').textContent = nombre;
            document.getElementById('inputTituloProvincia').value = titulo;
            const imgPreview = document.getElementById('mapaFotoPreviewImg');
            if (imgPreview) {
                imgPreview.src = foto;
            }
        });
    }
});
</script>
