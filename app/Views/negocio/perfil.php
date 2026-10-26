<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Editar Perfil del Negocio</h1>
            <p class="text-muted small mb-0">Mantén actualizados tus datos de contacto, horarios y descripción para los turistas.</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl) ?>/negocio/dashboard" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <form action="<?= htmlspecialchars($baseUrl) ?>/negocio/perfil/guardar" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Datos no editables directamente por el usuario (Identidad) -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-4 border">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Nombre del Establecimiento</label>
                    <input type="text" class="form-control bg-white" value="<?= htmlspecialchars($lugar['nombre']) ?>" disabled readonly>
                    <small class="text-muted extra-small">Para cambiar el nombre o razón social, contacta a la administración.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Categoría</label>
                    <input type="text" class="form-control bg-white" value="<?= htmlspecialchars($categoria['nombre'] ?? 'Comercial') ?>" disabled readonly>
                    <small class="text-muted extra-small">Categoría asignada en tu suscripción.</small>
                </div>
            </div>

            <!-- Canales de Contacto Directo -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-dots text-success me-2"></i>Canales de Contacto Directo</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="whatsapp_contacto" class="form-label small fw-semibold">Número de WhatsApp (Consultas y Reservas) *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success-subtle text-success border-end-0"><i class="bi bi-whatsapp"></i></span>
                        <input type="text" id="whatsapp_contacto" name="whatsapp_contacto" class="form-control" 
                               value="<?= htmlspecialchars($lugar['whatsapp_contacto'] ?? '') ?>" 
                               placeholder="Ej. 70000000 o +591 70000000" maxlength="30" required>
                    </div>
                    <small class="text-muted extra-small">Se usará en el botón directo de WhatsApp de tu ficha pública.</small>
                </div>

                <div class="col-md-6">
                    <label for="telefono_contacto" class="form-label small fw-semibold">Teléfono Fijo o Móvil de Llamadas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" id="telefono_contacto" name="telefono_contacto" class="form-control" 
                               value="<?= htmlspecialchars($lugar['telefono_contacto'] ?? '') ?>" 
                               placeholder="Ej. 34621234" maxlength="30">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="email_contacto" class="form-label small fw-semibold">Correo Electrónico Comercial</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" id="email_contacto" name="email_contacto" class="form-control" 
                               value="<?= htmlspecialchars($lugar['email_contacto'] ?? '') ?>" 
                               placeholder="contacto@negocio.com" maxlength="120">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="horario_atencion" class="form-label small fw-semibold">Horarios de Atención al Público</label>
                    <div class="input-group">
                        <span class="input-group-text bg-warning-subtle text-warning border-end-0"><i class="bi bi-clock-fill"></i></span>
                        <input type="text" id="horario_atencion" name="horario_atencion" class="form-control" 
                               value="<?= htmlspecialchars($lugar['horario_atencion'] ?? '') ?>" 
                               placeholder="Ej. Lunes a Sábado: 08:00 a 22:00" maxlength="200">
                    </div>
                </div>

                <div class="col-12">
                    <label for="enlace_menu" class="form-label small fw-semibold">Menú Digital / Carta de Productos (Enlace Web o PDF)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-info-subtle text-info border-end-0"><i class="bi bi-book-half"></i></span>
                        <input type="url" id="enlace_menu" name="enlace_menu" class="form-control" 
                               value="<?= htmlspecialchars($lugar['enlace_menu'] ?? '') ?>" 
                               placeholder="Ej. https://mi-menu-digital.com o enlace público a PDF en Google Drive" maxlength="255">
                    </div>
                    <small class="text-muted extra-small">Si agregas este enlace, los turistas verán un botón destacado para consultar tu menú o carta en tu ficha pública.</small>
                </div>
            </div>

            <!-- Redes Sociales Oficiales -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-share text-primary me-2"></i>Redes Sociales Oficiales</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="tiktok_url" class="form-label small fw-semibold">
                        <i class="bi bi-tiktok text-dark me-1"></i> Perfil de TikTok
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-white border-end-0"><i class="bi bi-tiktok"></i></span>
                        <input type="url" id="tiktok_url" name="tiktok_url" class="form-control" 
                               value="<?= htmlspecialchars($lugar['tiktok_url'] ?? '') ?>" 
                               placeholder="https://www.tiktok.com/@tunegocio" maxlength="255">
                    </div>
                    <small class="text-muted extra-small">Enlace a tu perfil de TikTok para ver tus videos virales.</small>
                </div>

                <div class="col-md-4">
                    <label for="facebook_url" class="form-label small fw-semibold">
                        <i class="bi bi-facebook text-primary me-1"></i> Página de Facebook
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white border-end-0"><i class="bi bi-facebook"></i></span>
                        <input type="url" id="facebook_url" name="facebook_url" class="form-control" 
                               value="<?= htmlspecialchars($lugar['facebook_url'] ?? '') ?>" 
                               placeholder="https://facebook.com/tunegocio" maxlength="255">
                    </div>
                    <small class="text-muted extra-small">Enlace a tu Fanpage para ver publicaciones y menú del día.</small>
                </div>

                <div class="col-md-4">
                    <label for="instagram_url" class="form-label small fw-semibold">
                        <i class="bi bi-instagram text-danger me-1"></i> Cuenta de Instagram
                    </label>
                    <div class="input-group">
                        <span class="input-group-text text-white border-end-0" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);"><i class="bi bi-instagram"></i></span>
                        <input type="url" id="instagram_url" name="instagram_url" class="form-control" 
                               value="<?= htmlspecialchars($lugar['instagram_url'] ?? '') ?>" 
                               placeholder="https://instagram.com/tunegocio" maxlength="255">
                    </div>
                    <small class="text-muted extra-small">Enlace a tu feed para mostrar las mejores fotos de tus platos y ambiente.</small>
                </div>
            </div>

            <!-- Ubicación y Descripción -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-card-text text-primary me-2"></i>Información del Establecimiento</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="direccion" class="form-label small fw-semibold">Dirección Física *</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" 
                           value="<?= htmlspecialchars($lugar['direccion'] ?? '') ?>" 
                           placeholder="Ej. Calle Cipriano Barace entre La Paz y Sucre" maxlength="255" required>
                </div>

                <div class="col-md-6">
                    <label for="referencia_ubicacion" class="form-label small fw-semibold">Punto de Referencia</label>
                    <input type="text" id="referencia_ubicacion" name="referencia_ubicacion" class="form-control" 
                           value="<?= htmlspecialchars($lugar['referencia_ubicacion'] ?? '') ?>" 
                           placeholder="Ej. Frente a la plaza principal, lado del banco" maxlength="255">
                </div>

                <div class="col-12">
                    <label for="descripcion" class="form-label small fw-semibold">Descripción del Establecimiento y Servicios *</label>
                    <textarea id="descripcion" name="descripcion" rows="5" class="form-control" 
                              maxlength="2000" required><?= htmlspecialchars($lugar['descripcion'] ?? '') ?></textarea>
                    <small class="text-muted extra-small">Describe tus especialidades, menú, comodidades, formas de pago, etc. Máximo 2.000 caracteres.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= htmlspecialchars($baseUrl) ?>/negocio/dashboard" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow-sm">
                    <i class="bi bi-save2 me-1"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Zona de Gestión de Estado y Baja Voluntaria -->
    <div class="card border border-danger-subtle shadow-sm rounded-4 p-4 p-md-4 mt-4 bg-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 fs-4 bg-danger-subtle text-danger flex-shrink-0">
                    <i class="bi bi-shield-x"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Dar de baja mi establecimiento</h5>
                    <p class="text-muted small mb-0">
                        Si has cerrado tu local permanentemente o prefieres no figurar más en BeniTours, puedes solicitar la baja voluntaria.
                    </p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBajaComercio">
                    <i class="bi bi-x-circle me-1"></i> Dar de baja mi negocio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación Segura: Baja Voluntaria -->
<div class="modal fade" id="modalBajaComercio" tabindex="-1" aria-labelledby="modalBajaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= htmlspecialchars($baseUrl) ?>/negocio/baja-voluntaria" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="modal-header bg-danger text-white border-0 py-3 px-4">
                    <h5 class="modal-title fw-bold" id="modalBajaTitulo">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Baja Voluntaria
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Atención:</strong> Al confirmar la baja, tu establecimiento se ocultará de inmediato del catálogo general, buscador y mapa de BeniTours. Tus fotos y datos no se eliminarán; si en el futuro decides reabrir, podrás solicitar la reactivación a la administración.
                    </div>

                    <div class="mb-3">
                        <label for="motivo_baja" class="form-label fw-bold text-dark small">¿Cuál es el motivo principal de la baja? <span class="text-danger">*</span></label>
                        <select name="motivo_baja" id="motivo_baja" class="form-select rounded-3" required>
                            <option value="" disabled selected>Selecciona un motivo...</option>
                            <option value="Cierre definitivo del establecimiento">Cierre definitivo del establecimiento</option>
                            <option value="Cambio de rubro comercial">Cambio de rubro comercial</option>
                            <option value="Traslado a otra ciudad o departamento">Traslado a otra ciudad o departamento</option>
                            <option value="Motivos económicos / presupuesto">Motivos económicos / presupuesto</option>
                            <option value="Baja temporal prolongada">Baja temporal prolongada</option>
                            <option value="Otro motivo">Otro motivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comentario_baja" class="form-label fw-bold text-dark small">Comentario adicional (Opcional)</label>
                        <textarea name="comentario_baja" id="comentario_baja" rows="2" class="form-control rounded-3" 
                                  placeholder="¿Tienes alguna sugerencia para BeniTours o detalle adicional?" maxlength="500"></textarea>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label for="password_confirm" class="form-label fw-bold text-danger small">
                            <i class="bi bi-lock-fill me-1"></i>Ingresa tu contraseña actual para confirmar: <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control rounded-3" 
                               placeholder="Contraseña de tu cuenta" autocomplete="current-password" required>
                        <div class="form-text extra-small">Medida de seguridad para evitar bajas accidentales.</div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-trash3-fill me-1"></i> Sí, confirmar baja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
