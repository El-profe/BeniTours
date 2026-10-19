<div class="container-fluid p-0">
    <!-- Encabezado Superior -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1 fw-bold border" style="background-color: #E2F2F0; color: #0E4D5E; border-color: #B8E1DA !important;">
                    <i class="bi bi-calendar-event-fill me-1 text-teal"></i> Agenda Cultural & Promocional
                </span>
                <span class="text-muted small">BeniTours &bull; Difusión Departamental</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Eventos Destacados del Beni</h1>
            <small class="text-muted">Administra los eventos, ferias y festividades que se muestran automáticamente en el pop-up de la portada.</small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-nuevo-evento rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnNuevoEvento">
                <i class="bi bi-plus-circle-fill me-1"></i> Nuevo Evento
            </button>
        </div>
    </div>

    <!-- Mensajes Flash -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert" style="background-color: #DDF4F1; color: #0A3D4A;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span class="fw-semibold"><?= htmlspecialchars($mensaje) ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert" style="background-color: #f8d7da; color: #842029;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span class="fw-semibold"><?= htmlspecialchars($error) ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Eventos -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">
                <i class="bi bi-stars text-warning me-1"></i> Cartelera de Eventos (<?= count($eventos) ?>)
            </span>
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Los eventos expirados se ocultan automáticamente en la portada.</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light extra-small text-uppercase text-secondary">
                    <tr>
                        <th style="width: 50px;" class="text-center">Ord.</th>
                        <th style="width: 100px;">Afiche</th>
                        <th>Evento / Festividad</th>
                        <th>Lugar & Fechas Mostradas</th>
                        <th>Vigencia de Publicación</th>
                        <th class="text-center">Estado</th>
                        <th style="width: 130px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eventos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                No hay eventos registrados en la agenda. Haz clic en <strong>"Nuevo Evento"</strong> para comenzar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($eventos as $ev): ?>
                            <?php
                                // Determinar ruta de la imagen: si está en eventos/ o en img/
                                $archivo = $ev['imagen_archivo'];
                                $rutaUrl = is_file(dirname(__DIR__, 4) . '/public/assets/img/eventos/' . $archivo)
                                    ? "{$baseUrl}/assets/img/eventos/" . rawurlencode($archivo)
                                    : "{$baseUrl}/assets/img/" . rawurlencode($archivo);

                                $badgeEstado = match($ev['estado_vigencia']) {
                                    'VIGENTE'    => 'bg-success text-white',
                                    'PROGRAMADO' => 'bg-info text-dark',
                                    'EXPIRADO'   => 'bg-secondary text-white',
                                    'PAUSADO'    => 'bg-warning text-dark',
                                    default      => 'bg-light text-dark'
                                };
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= (int)$ev['orden'] ?></td>
                                <td>
                                    <div class="rounded-3 overflow-hidden border shadow-sm" style="width: 75px; height: 95px; background: #f8f9fa;">
                                        <img src="<?= htmlspecialchars($rutaUrl) ?>" 
                                             alt="<?= htmlspecialchars($ev['titulo']) ?>" 
                                             class="w-100 h-100 object-fit-cover" 
                                             style="cursor: pointer;"
                                             onclick="window.open('<?= htmlspecialchars($rutaUrl) ?>', '_blank')">
                                    </div>
                                </td>
                                <td>
                                    <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($ev['titulo']) ?></h6>
                                    <?php if (!empty($ev['enlace_externo'])): ?>
                                        <a href="<?= htmlspecialchars($ev['enlace_externo']) ?>" target="_blank" rel="noopener noreferrer" class="extra-small text-teal text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Enlace oficial / Más info
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($ev['subtitulo_lugar']) ?>
                                    </div>
                                    <div class="extra-small text-muted">
                                        <i class="bi bi-calendar-event me-1 text-teal"></i><?= htmlspecialchars($ev['detalle_fechas']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="extra-small text-dark mb-1">
                                        <strong>Desde:</strong> <?= date('d/m/Y', strtotime($ev['fecha_inicio'])) ?>
                                    </div>
                                    <div class="extra-small text-dark">
                                        <strong>Hasta:</strong> <?= date('d/m/Y', strtotime($ev['fecha_fin'])) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $badgeEstado ?> rounded-pill px-3 py-1 extra-small fw-bold">
                                        <?= htmlspecialchars($ev['estado_vigencia']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 btn-editar-evento p-2 px-2" 
                                                data-evento='<?= json_encode($ev, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'
                                                data-img-url="<?= htmlspecialchars($rutaUrl) ?>"
                                                title="Editar evento">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>

                                        <!-- Alternar Estado -->
                                        <form action="<?= htmlspecialchars($baseUrl) ?>/admin/eventos/estado" method="POST" class="d-inline m-0">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="id_evento" value="<?= (int)$ev['id_evento'] ?>">
                                            <input type="hidden" name="activo" value="<?= (int)$ev['activo'] ? 0 : 1 ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-<?= (int)$ev['activo'] ? 'warning' : 'success' ?> rounded-3 p-2 px-2" 
                                                    title="<?= (int)$ev['activo'] ? 'Pausar evento' : 'Activar evento' ?>">
                                                <i class="bi <?= (int)$ev['activo'] ? 'bi-pause-fill' : 'bi-play-fill' ?> fs-6"></i>
                                            </button>
                                        </form>

                                        <!-- Eliminar -->
                                        <form action="<?= htmlspecialchars($baseUrl) ?>/admin/eventos/eliminar" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Seguro que deseas eliminar este evento de la cartelera?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="id_evento" value="<?= (int)$ev['id_evento'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 p-2 px-2" title="Eliminar evento">
                                                <i class="bi bi-trash fs-6"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Estilos para el botón Nuevo Evento y hover */
.btn-nuevo-evento {
    background-color: #0d9488 !important;
    color: #ffffff !important;
    border: none;
    transition: all 0.25s ease-in-out;
}
.btn-nuevo-evento:hover, .btn-nuevo-evento:focus {
    background-color: #0f766e !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35) !important;
}

/* Área de subida y vista previa del afiche */
.afiche-upload-box {
    border: 2px dashed #cbd5e1;
    background-color: #f8fafc;
    border-radius: 1rem;
    transition: border-color 0.2s ease;
}
.afiche-upload-box:hover {
    border-color: #0d9488;
}
.afiche-preview-img {
    max-height: 290px;
    width: 100%;
    object-fit: contain;
    border-radius: 0.75rem;
    background-color: #1e293b;
}
</style>

<!-- Modal Crear / Editar Evento -->
<div class="modal fade" id="modalEventoForm" tabindex="-1" aria-labelledby="modalEventoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/eventos/guardar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id_evento" id="formIdEvento" value="0">

                <div class="modal-header bg-teal text-white border-0 py-3 px-4">
                    <h5 class="modal-title fw-bold" id="modalEventoTitulo">
                        <i class="bi bi-calendar-plus me-2"></i>Nuevo Evento / Afiche
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Columna Izquierda: Información del Evento -->
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bi bi-info-circle-fill text-teal fs-5"></i>
                                <span class="fw-bold text-dark">Información del Evento</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="formTitulo" class="form-label fw-bold text-dark small">Título del Evento <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" id="formTitulo" class="form-control rounded-3" 
                                           placeholder="Ej. FexpoBeni 2026, Entrada Folklórica, Festival del Pescado..." required>
                                </div>

                                <div class="col-md-6">
                                    <label for="formSubtitulo" class="form-label fw-bold text-dark small">Lugar / Sede</label>
                                    <input type="text" name="subtitulo_lugar" id="formSubtitulo" class="form-control rounded-3" 
                                           placeholder="Ej. Trinidad · Beni, Rurrenabaque, San Ignacio..." value="Trinidad · Beni">
                                </div>

                                <div class="col-md-6">
                                    <label for="formDetalleFechas" class="form-label fw-bold text-dark small">Texto descriptivo de fechas <span class="text-danger">*</span></label>
                                    <input type="text" name="detalle_fechas" id="formDetalleFechas" class="form-control rounded-3" 
                                           placeholder="Ej. 14 al 18 de noviembre de 2026" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="formFechaInicio" class="form-label fw-bold text-dark small">Publicar desde (Fecha Inicio) <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_inicio" id="formFechaInicio" class="form-control rounded-3" required value="<?= date('Y-m-d') ?>">
                                    <div class="form-text extra-small">Inicio de visibilidad en portada.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="formFechaFin" class="form-label fw-bold text-dark small">Expiración automática (Fecha Fin) <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_fin" id="formFechaFin" class="form-control rounded-3" required>
                                    <div class="form-text extra-small">Se ocultará automáticamente tras esta fecha.</div>
                                </div>

                                <div class="col-12">
                                    <label for="formEnlace" class="form-label fw-bold text-dark small">Enlace Externo (Opcional)</label>
                                    <input type="url" name="enlace_externo" id="formEnlace" class="form-control rounded-3" 
                                           placeholder="https://facebook.com/evento-oficial o página web">
                                    <div class="form-text extra-small">Botón de enlace directo que aparecerá en el afiche.</div>
                                </div>

                                <div class="col-md-4">
                                    <label for="formOrden" class="form-label fw-bold text-dark small">Orden de visualización</label>
                                    <input type="number" name="orden" id="formOrden" class="form-control rounded-3" value="1" min="1">
                                </div>

                                <div class="col-md-8 d-flex align-items-center pt-md-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo" id="formActivo" value="1" checked>
                                        <label class="form-check-label fw-bold text-dark small" for="formActivo">Evento Activo en la cartelera</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha: Imagen / Afiche y Vista Previa -->
                        <div class="col-lg-5 ps-lg-4 border-start-lg d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bi bi-image-fill text-teal fs-5"></i>
                                <span class="fw-bold text-dark">Afiche o Fotografía</span>
                            </div>

                            <div class="mb-3">
                                <label for="formImagen" class="form-label fw-bold text-dark small">Seleccionar Archivo <span class="text-danger" id="reqImagenAsterisco">*</span></label>
                                <input type="file" name="imagen" id="formImagen" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text extra-small">Formatos: JPG, PNG, WebP (Máx. 8 MB). Se recomienda orientación vertical / afiche.</div>
                            </div>

                            <!-- Caja de Previsualización Centrada -->
                            <div class="afiche-upload-box p-3 text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
                                <div class="d-none w-100" id="previewContenedor">
                                    <span class="extra-small text-muted d-block mb-2 fw-semibold">Vista Previa del Afiche:</span>
                                    <img src="" id="imgPreview" alt="Previsualización" class="afiche-preview-img shadow-sm border">
                                </div>
                                <div id="previewPlaceholder" class="text-muted py-4">
                                    <i class="bi bi-card-image display-4 text-secondary opacity-50 d-block mb-2"></i>
                                    <span class="small">El afiche seleccionado se previsualizará aquí.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4 fw-bold">
                        <i class="bi bi-save me-1"></i> Guardar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('modalEventoForm');
    const modalInstance = modalEl ? new bootstrap.Modal(modalEl) : null;
    const btnNuevo = document.getElementById('btnNuevoEvento');
    const formId = document.getElementById('formIdEvento');
    const formTitulo = document.getElementById('formTitulo');
    const formSubtitulo = document.getElementById('formSubtitulo');
    const formDetalleFechas = document.getElementById('formDetalleFechas');
    const formFechaInicio = document.getElementById('formFechaInicio');
    const formFechaFin = document.getElementById('formFechaFin');
    const formEnlace = document.getElementById('formEnlace');
    const formOrden = document.getElementById('formOrden');
    const formActivo = document.getElementById('formActivo');
    const formImagen = document.getElementById('formImagen');
    const reqAsterisco = document.getElementById('reqImagenAsterisco');
    const previewContenedor = document.getElementById('previewContenedor');
    const imgPreview = document.getElementById('imgPreview');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const modalTitulo = document.getElementById('modalEventoTitulo');

    function mostrarPreview(url) {
        if (url) {
            imgPreview.src = url;
            previewContenedor?.classList.remove('d-none');
            previewPlaceholder?.classList.add('d-none');
        } else {
            imgPreview.src = '';
            previewContenedor?.classList.add('d-none');
            previewPlaceholder?.classList.remove('d-none');
        }
    }

    // Botón Nuevo Evento
    btnNuevo?.addEventListener('click', () => {
        formId.value = '0';
        formTitulo.value = '';
        formSubtitulo.value = 'Trinidad · Beni';
        formDetalleFechas.value = '';
        formFechaInicio.value = '<?= date('Y-m-d') ?>';
        formFechaFin.value = '';
        formEnlace.value = '';
        formOrden.value = '1';
        formActivo.checked = true;
        formImagen.value = '';
        formImagen.required = true;
        reqAsterisco.classList.remove('d-none');
        mostrarPreview(null);
        modalTitulo.innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Nuevo Evento / Afiche';
        modalInstance?.show();
    });

    // Botones Editar Evento
    document.querySelectorAll('.btn-editar-evento').forEach(btn => {
        btn.addEventListener('click', () => {
            const dataStr = btn.getAttribute('data-evento');
            const imgUrl = btn.getAttribute('data-img-url');
            if (!dataStr) return;

            try {
                const ev = JSON.parse(dataStr);
                formId.value = ev.id_evento;
                formTitulo.value = ev.titulo || '';
                formSubtitulo.value = ev.subtitulo_lugar || 'Trinidad · Beni';
                formDetalleFechas.value = ev.detalle_fechas || '';
                formFechaInicio.value = ev.fecha_inicio || '';
                formFechaFin.value = ev.fecha_fin || '';
                formEnlace.value = ev.enlace_externo || '';
                formOrden.value = ev.orden || 1;
                formActivo.checked = parseInt(ev.activo) === 1;
                formImagen.value = '';
                formImagen.required = false;
                reqAsterisco.classList.add('d-none');

                mostrarPreview(imgUrl || null);

                modalTitulo.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Evento';
                modalInstance?.show();
            } catch (e) {
                console.error('Error al abrir evento para editar', e);
            }
        });
    });

    // Vista previa de imagen al seleccionarla
    formImagen?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                mostrarPreview(e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            mostrarPreview(null);
        }
    });
});
</script>
