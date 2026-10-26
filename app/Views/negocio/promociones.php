<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-0">Promociones y Ofertas</h4>
            <p class="text-muted small mb-0">Tus promociones aparecerán destacadas en tu ficha pública</p>
        </div>
        <button class="btn btn-warning rounded-pill px-4 fw-bold text-dark" id="btnNuevaPromo" data-bs-toggle="modal" data-bs-target="#modalPromocion">
            <i class="bi bi-plus-lg me-1"></i> Nueva Promoción
        </button>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success rounded-4 small mb-4"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-4 small mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php if (!empty($promociones)): ?>
            <?php foreach ($promociones as $p): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="admin-card p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1">
                                <?= htmlspecialchars($p['descuento_texto'] ?: 'Oferta Especial') ?>
                            </span>
                            <span class="badge <?= $p['activo'] ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                                <?= $p['activo'] ? 'Vigente' : 'Pausada' ?>
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($p['titulo']) ?></h5>

                        <?php if (!empty($p['imagen_archivo'])): ?>
                            <div class="mb-3 rounded-3 overflow-hidden position-relative border" style="height: 150px; background-color: #081c15;">
                                <div class="position-absolute top-0 start-0 w-100 h-100" 
                                     style="background-image: url('<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($p['imagen_archivo']) ?>'); background-size: cover; background-position: center; filter: blur(10px) brightness(0.4); transform: scale(1.15);"></div>
                                <img src="<?= htmlspecialchars($baseUrl) ?>/imagen?f=<?= urlencode($p['imagen_archivo']) ?>" 
                                     alt="<?= htmlspecialchars($p['titulo']) ?>"
                                     class="position-relative w-100 h-100 object-fit-contain p-1"
                                     style="z-index: 2;"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>

                        <p class="text-muted small flex-grow-1"><?= htmlspecialchars($p['descripcion']) ?></p>

                        <?php if (!empty($p['tiktok_url']) || !empty($p['facebook_url']) || !empty($p['instagram_url'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <span class="extra-small text-muted fw-semibold">Publicado en:</span>
                                <?php if (!empty($p['tiktok_url'])): ?>
                                    <a href="<?= htmlspecialchars($p['tiktok_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                       class="btn btn-sm btn-dark rounded-circle p-0 d-flex align-items-center justify-content-center shadow-xs" 
                                       style="width: 30px; height: 30px;" title="Ver video en TikTok">
                                        <i class="bi bi-tiktok text-white" style="font-size: 0.8rem;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($p['facebook_url'])): ?>
                                    <a href="<?= htmlspecialchars($p['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                       class="btn btn-sm text-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-xs" 
                                       style="width: 30px; height: 30px; background-color: #1877f2;" title="Ver en Facebook">
                                        <i class="bi bi-facebook" style="font-size: 0.8rem;"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($p['instagram_url'])): ?>
                                    <a href="<?= htmlspecialchars($p['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" 
                                       class="btn btn-sm text-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-xs" 
                                       style="width: 30px; height: 30px; background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);" title="Ver en Instagram">
                                        <i class="bi bi-instagram" style="font-size: 0.8rem;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="border-top pt-2 mt-3 small text-muted d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar3 me-1"></i> Hasta: <?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></span>
                            
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" 
                                        class="btn btn-sm btn-link text-primary text-decoration-none p-0 btn-editar-promo" 
                                        data-promo='<?= htmlspecialchars(json_encode(array_merge($p, [
                                            'imagen_url' => !empty($p['imagen_archivo'])
                                                ? $baseUrl . '/imagen?f=' . rawurlencode($p['imagen_archivo'])
                                                : ''
                                        ])), ENT_QUOTES, 'UTF-8') ?>'
                                        title="Editar promoción">
                                    <i class="bi bi-pencil-square me-1"></i>Editar
                                </button>
                                <span class="text-muted">·</span>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/negocio/promociones/estado" method="POST" class="m-0">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="id_promocion" value="<?= $p['id_promocion'] ?>">
                                    <input type="hidden" name="activo" value="<?= $p['activo'] ? '0' : '1' ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0 <?= $p['activo'] ? 'text-warning' : 'text-success' ?>">
                                        <?= $p['activo'] ? 'Pausar' : 'Activar' ?>
                                    </button>
                                </form>
                                <span class="text-muted">·</span>
                                <form action="<?= htmlspecialchars($baseUrl) ?>/negocio/promociones/eliminar" method="POST" class="m-0" onsubmit="return confirm('¿Eliminar esta promoción definitivamente?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="id_promocion" value="<?= $p['id_promocion'] ?>">
                                    <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-0" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-tag display-5 d-block mb-2"></i>
                No has publicado ninguna promoción todavía. Haz clic en "Nueva Promoción".
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Promoción (Crear / Editar) -->
<div class="modal fade" id="modalPromocion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formPromo" action="<?= htmlspecialchars($baseUrl) ?>/negocio/promociones/guardar" method="POST" enctype="multipart/form-data" data-base-url="<?= htmlspecialchars($baseUrl) ?>" class="modal-content promo-create-modal border-0 shadow-lg">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="id_promocion" id="promoId" value="">
            <input type="hidden" name="quitar_imagen" id="promoQuitarImagen" value="0">

            <div class="modal-header promo-create-header border-0 px-4 px-md-5 py-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="promo-create-header-icon" id="promoHeaderIcon"><i class="bi bi-stars"></i></span>
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="promoModalTitulo">Lanzar Promoción</h5>
                        <p class="mb-0 small opacity-75" id="promoModalSubtitulo">Publica ofertas que atraigan visitantes y aumenten tus ventas.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 p-md-5">
                <div class="promo-form-preview mb-4" aria-live="polite">
                    <div class="promo-form-preview-image" id="promoPreviewImageBox">
                        <img id="promoPreviewImage" alt="Vista previa del flyer" class="d-none">
                        <i class="bi bi-image" id="promoPreviewImageIcon"></i>
                    </div>
                    <div class="promo-form-preview-content">
                        <span class="promo-form-preview-label">Vista previa</span>
                        <span class="promo-form-preview-badge" id="promoPreviewDiscount">Oferta especial</span>
                        <strong id="promoPreviewTitle">Tu próxima promoción</strong>
                        <small id="promoPreviewDescription">Agrega un título y una descripción atractiva.</small>
                    </div>
                </div>

                <div class="promo-form-section">
                    <div class="promo-form-section-title"><span>1</span><div><strong>La oferta</strong><small>Lo primero que verá tu cliente.</small></div></div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small" for="promoTitulo">Título de la promoción <span class="text-danger">*</span></label>
                            <input id="promoTitulo" type="text" name="titulo" class="form-control promo-form-control" maxlength="120" placeholder="Ej. Jueves de Pacú 2x1" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small" for="promoDescuento">Gancho visible</label>
                            <input id="promoDescuento" type="text" name="descuento_texto" class="form-control promo-form-control" maxlength="60" placeholder="Ej. 20% OFF o 2x1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" for="promoDescripcion">Descripción y condiciones <span class="text-danger">*</span></label>
                            <textarea id="promoDescripcion" name="descripcion" rows="3" maxlength="500" class="form-control promo-form-control" placeholder="Ej. Válido solo en consumo en salón de 19:00 a 22:00..." required></textarea>
                            <div class="d-flex justify-content-between form-text"><span>Explica qué incluye la oferta y sus condiciones.</span><span id="promoDescriptionCount">0/500</span></div>
                        </div>
                    </div>
                </div>

                <div class="promo-form-section mt-4">
                    <div class="promo-form-section-title"><span>2</span><div><strong>Redes Sociales de la Oferta</strong><small>Opcional: pega el link directo donde publicaste tu promo para que el turista lo vea.</small></div></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" for="promoTiktokUrl">
                                <i class="bi bi-tiktok text-dark me-1"></i> TikTok (Video / Reel)
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-dark text-white"><i class="bi bi-tiktok"></i></span>
                                <input id="promoTiktokUrl" type="url" name="tiktok_url" class="form-control" maxlength="500" placeholder="https://vm.tiktok.com/...">
                            </div>
                            <small class="text-muted extra-small">Enlace al video promocional.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" for="promoFacebookUrl">
                                <i class="bi bi-facebook text-primary me-1"></i> Facebook (Publicación)
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-primary text-white"><i class="bi bi-facebook"></i></span>
                                <input id="promoFacebookUrl" type="url" name="facebook_url" class="form-control" maxlength="500" placeholder="https://facebook.com/...">
                            </div>
                            <small class="text-muted extra-small">Enlace al post de Facebook.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small" for="promoInstagramUrl">
                                <i class="bi bi-instagram text-danger me-1"></i> Instagram (Reel / Post)
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text text-white" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);"><i class="bi bi-instagram"></i></span>
                                <input id="promoInstagramUrl" type="url" name="instagram_url" class="form-control" maxlength="500" placeholder="https://instagram.com/p/...">
                            </div>
                            <small class="text-muted extra-small">Enlace a la publicación en Instagram.</small>
                        </div>
                    </div>
                </div>

                <div class="promo-form-section mt-4">
                    <div class="promo-form-section-title"><span>3</span><div><strong>Imagen de la oferta</strong><small>Opcional, pero ayuda a captar más miradas.</small></div></div>
                    
                    <!-- Control para imagen existente cuando se edita -->
                    <div id="promoImgActualBox" class="alert alert-light border d-flex align-items-center justify-content-between p-2 mb-3 rounded-3 d-none">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="bi bi-file-earmark-image text-primary fs-5"></i>
                            <span class="small text-truncate" id="promoImgActualNombre">imagen_actual.jpg</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" id="btnQuitarImgActual" title="Quitar imagen actual">
                            <i class="bi bi-x-circle me-1"></i> Quitar imagen
                        </button>
                    </div>

                    <label for="promoFlyer" class="promo-upload-zone" id="promoUploadZone">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <span class="fw-semibold" id="promoUploadLabel">Haz clic para subir un flyer o fotografía</span>
                        <small>JPG, PNG o WEBP · Si no subes una, se usará la foto de portada.</small>
                        <input id="promoFlyer" type="file" name="foto_promocion" accept="image/jpeg,image/png,image/webp" class="visually-hidden">
                    </label>
                </div>

                <div class="promo-form-section mt-4">
                    <div class="promo-form-section-title"><span>4</span><div><strong>Vigencia</strong><small>Define cuándo estará disponible tu oferta.</small></div></div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small" for="promoFechaInicio">Fecha de inicio <span class="text-danger">*</span></label>
                            <input id="promoFechaInicio" type="date" name="fecha_inicio" class="form-control promo-form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small" for="promoFechaFin">Fecha de finalización <span class="text-danger">*</span></label>
                            <input id="promoFechaFin" type="date" name="fecha_fin" class="form-control promo-form-control" value="<?= date('Y-m-d', strtotime('+15 days')) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer promo-modal-footer border-0 px-4 px-md-5 py-3 gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark flex-grow-1 shadow-sm">
                    <i class="bi bi-megaphone-fill me-2" id="promoBtnIcon"></i><span id="promoBtnSubmitText">Publicar promoción</span>
                </button>
            </div>
        </form>
    </div>
</div>
