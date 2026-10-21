<?php
$qr = $cobro['qr_url'] ?? '';
$qrValido = is_string($qr) && preg_match('~\A(?:https?://|/(?!/))~i', $qr);
$qrSrc = $qrValido ? (preg_match('~\Ahttps?://~i', $qr) ? $qr : $baseUrl . $qr) : null;
$pagos = $pagos ?? [];
?>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Renovación de Membresía</h1>
            <p class="text-muted small mb-0">Mantén activo y destacado tu negocio en la guía turística y comercial BeniTours.</p>
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

    <?php if ($pagoPendiente): ?>
        <div class="alert alert-warning rounded-4 p-4 mb-4 shadow-sm border-0 bg-warning-subtle text-dark" role="status">
            <div class="d-flex gap-3 align-items-center">
                <div class="bg-warning text-dark p-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Comprobante de Renovación en Verificación</h5>
                    <p class="mb-0 small">Ya has enviado un comprobante de pago que está siendo revisado por la administración. Una vez confirmado, tu vigencia se extenderá automáticamente sin perder ningún día de servicio.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Estado Actual de Cobertura y Pago Oficial QR -->
    <div class="row g-4 mb-4">
        <!-- Tarjeta de Estado y Cobertura -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase">Estado de tu Ficha</span>
                    <?php if ($esVisible): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo en Guía
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-pause-circle-fill me-1"></i> Ficha Pausada
                        </span>
                    <?php endif; ?>
                </div>

                <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($lugar['nombre']) ?></h4>
                <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($lugar['direccion'] ?: 'Trinidad, Beni') ?></p>

                <div class="border-top pt-3 mt-auto">
                    <?php if ($fechaVencimiento): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Vencimiento registrado:</span>
                            <strong class="text-dark"><?= htmlspecialchars(date('d/m/Y', strtotime($fechaVencimiento))) ?></strong>
                        </div>
                        <?php
                            $diasRestantes = (int)(ceil((strtotime($fechaVencimiento) - strtotime(date('Y-m-d'))) / 86400));
                        ?>
                        <?php if ($diasRestantes > 0): ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center text-primary fw-semibold small mb-1">
                                    <i class="bi bi-shield-check me-2 fs-5 text-success"></i>
                                    <span><?= $diasRestantes ?> días de cobertura restante</span>
                                </div>
                                <p class="extra-small text-muted mb-0">Al renovar, los nuevos meses se sumarán automáticamente a partir de tu fecha de vencimiento actual.</p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle">
                                <div class="d-flex align-items-center text-danger fw-bold small mb-1">
                                    <i class="bi bi-exclamation-octagon me-2 fs-5"></i>
                                    <span>Vigencia expirada hace <?= abs($diasRestantes) ?> días</span>
                                </div>
                                <p class="extra-small text-danger-emphasis mb-0">Renueva hoy mismo para reactivar tu negocio en los primeros lugares de las búsquedas.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center">
                            <p class="mb-0 text-muted small">Sin vigencia activa previa registrada.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tarjeta QR Oficial Protagonista (Limpia y sin textos de transferencia redundantes) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 text-center d-flex flex-column justify-content-center align-items-center">
                <span class="text-muted small fw-semibold text-uppercase mb-3">Escanea el Código QR Oficial</span>

                <div class="qr-pago-container p-3 bg-white border border-2 border-primary-subtle rounded-4 shadow-sm d-inline-block mb-3">
                    <?php if ($qrSrc !== null): ?>
                        <img src="<?= htmlspecialchars($qrSrc) ?>" alt="QR Recaudación BeniTours" class="img-fluid qr-imagen-grande rounded-3">
                    <?php else: ?>
                        <div class="qr-placeholder d-flex flex-column align-items-center justify-content-center p-4 bg-light rounded-3 text-muted">
                            <i class="bi bi-qr-code display-4 mb-2"></i>
                            <span class="small">QR en Configuración</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center gap-2 justify-content-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small fw-semibold">
                        <i class="bi bi-phone me-1"></i> Escanea con tu app bancaria (QR Simple)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$pagoPendiente): ?>
    <!-- Formulario de Reporte de Pago -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-credit-card-2-front-fill text-success me-2"></i>Reportar Pago de Renovación</h4>
                <p class="text-muted small mb-0">Selecciona el plan de renovación que abonaste y sube el comprobante.</p>
            </div>
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small fw-semibold">
                <i class="bi bi-shield-lock-fill me-1"></i> Proceso Seguro
            </span>
        </div>

        <form action="<?= htmlspecialchars($baseUrl) ?>/negocio/renovar/guardar" method="POST" enctype="multipart/form-data" id="formRenovacion">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Cuadrícula Completa de Planes Definidos -->
            <label class="form-label small fw-bold text-uppercase text-muted mb-2">1. Selecciona el Plan de Cobertura *</label>
            <div class="row g-3 mb-4">
                <?php foreach ($planes as $indice => $plan): ?>
                    <?php
                    $meses = (int)$plan['meses_duracion'];
                    $esAnual = ($plan['codigo_plan'] === 'ANUAL' || $meses === 12);
                    $badgePlan = match($meses) {
                        1       => ['texto' => 'Básico', 'clase' => 'bg-light text-secondary border'],
                        3       => ['texto' => 'Ahorra 5%', 'clase' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
                        6       => ['texto' => 'Ahorra 10%', 'clase' => 'bg-primary-subtle text-primary border border-primary-subtle'],
                        12      => ['texto' => '★ Más Popular', 'clase' => 'bg-warning text-dark fw-bold border border-warning'],
                        default => ['texto' => "{$meses} Meses", 'clase' => 'bg-light text-dark border']
                    };
                    $activoDefault = ($indice === 0);
                    ?>
                    <div class="col-6 col-lg-3">
                        <label class="card h-100 border-2 p-3 text-center rounded-4 plan-tarjeta-item position-relative <?= $activoDefault ? 'border-primary bg-primary-subtle-glow' : 'border-light-subtle' ?>" style="cursor: pointer;">
                            <input type="radio" name="plan" value="<?= htmlspecialchars($plan['codigo_plan']) ?>" <?= $activoDefault ? 'checked' : '' ?> required class="position-absolute top-0 end-0 m-3 form-check-input plan-radio-selector">
                            
                            <span class="badge <?= $badgePlan['clase'] ?> rounded-pill extra-small mb-2 align-self-center px-2 py-1">
                                <?= htmlspecialchars($badgePlan['texto']) ?>
                            </span>

                            <div class="fw-bold text-dark fs-5 mb-1"><?= $meses ?> <?= $meses === 1 ? 'Mes' : 'Meses' ?></div>
                            <div class="text-success fw-bold fs-4 mb-1">Bs <?= number_format((float)$plan['monto'], 2) ?></div>
                            
                            <small class="text-muted extra-small d-block mt-auto pt-2 border-top">
                                <?= htmlspecialchars($plan['descripcion']) ?>
                            </small>
                        </label>
                    </div>
                <?php endforeach; ?>
                <?php if (!$planes): ?>
                    <div class="col-12">
                        <div class="alert alert-warning">No hay planes configurados en este momento.</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Datos y Subida Exclusiva de Comprobante -->
            <label class="form-label small fw-bold text-uppercase text-muted mb-2">2. Comprobante del Pago</label>
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <label for="numero_comprobante" class="form-label small fw-semibold">Número de Transacción / Operación</label>
                    <input type="text" id="numero_comprobante" name="numero_comprobante" class="form-control form-control-lg rounded-3 fs-6" 
                           placeholder="Ej. OP-839210 o Nº de comprobante" maxlength="100">
                    <small class="text-muted extra-small d-block mt-1">Opcional pero recomendado para acelerar la validación.</small>
                </div>

                <div class="col-md-7">
                    <label class="form-label small fw-semibold d-block">Subir Comprobante de Pago *</label>
                    
                    <!-- Botón Estilizado Exclusivo para Subir Archivo -->
                    <div class="subir-comprobante-box p-3 border-2 border-dashed rounded-4 text-center bg-light position-relative" id="dropAreaComprobante">
                        <input type="file" id="comprobante" name="comprobante" class="input-archivo-oculto" accept="image/jpeg,image/png,image/webp" required>
                        
                        <div id="comprobanteEmptyState" class="py-2">
                            <i class="bi bi-cloud-arrow-up display-6 text-primary mb-2 d-block"></i>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold mb-2 shadow-sm" onclick="document.getElementById('comprobante').click();">
                                <i class="bi bi-paperclip me-1"></i> Seleccionar Comprobante
                            </button>
                            <p class="text-muted extra-small mb-0">Formatos permitidos: JPG, PNG o WEBP (máx. 5 MB). Puedes arrastrar tu archivo aquí.</p>
                        </div>

                        <!-- Preview y confirmación de archivo seleccionado -->
                        <div id="comprobantePreviewState" class="d-none py-2 text-center">
                            <div class="preview-img-wrapper mb-2 position-relative d-inline-block">
                                <img id="previewImg" src="" alt="Vista previa" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 160px; max-width: 100%;">
                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 translate-middle shadow-sm p-1" style="width: 26px; height: 26px; line-height: 1;" onclick="limpiarComprobante();" title="Quitar archivo">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small fw-semibold" id="nombreArchivoTxt">
                                    comprobante.jpg
                                </span>
                            </div>
                            <button type="button" class="btn btn-link text-decoration-none extra-small text-muted mt-1" onclick="document.getElementById('comprobante').click();">
                                Cambiar por otra imagen
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= htmlspecialchars($baseUrl) ?>/negocio/dashboard" class="btn btn-light rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" id="btnEnviarComprobante">
                    <i class="bi bi-cloud-check-fill me-1"></i> Enviar Comprobante para Verificación
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Historial de Pagos y Recibos Oficiales -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Mis Pagos y Recibos Oficiales</h4>
                <p class="text-muted small mb-0">Consulta tus pagos registrados y descarga o imprime tus recibos oficiales para tu contabilidad.</p>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                Total Registros: <strong><?= count($pagos) ?></strong>
            </span>
        </div>

        <?php if (!empty($pagos)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>Recibo / Ref.</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Periodo</th>
                            <th>Vigencia Autorizada</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $p): ?>
                            <?php
                            $reciboCodigo = $p['numero_comprobante'] ?: ('REC-' . date('Y', strtotime($p['created_at'])) . '-' . str_pad((string)$p['id_pago'], 4, '0', STR_PAD_LEFT));
                            $estadoBadge = match($p['estado']) {
                                'CONFIRMADO' => 'bg-success',
                                'PENDIENTE'  => 'bg-warning text-dark',
                                'RECHAZADO'  => 'bg-danger',
                                default      => 'bg-secondary'
                            };
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-dark d-block">#<?= (int)$p['id_pago'] ?></strong>
                                    <code class="extra-small text-muted"><?= htmlspecialchars($reciboCodigo) ?></code>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha_pago_declarada'] ?: $p['created_at']))) ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">Bs <?= number_format((float)$p['monto'], 2) ?></span>
                                </td>
                                <td class="small">
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1">
                                        <?= (int)$p['meses_duracion'] ?> mes(es)
                                    </span>
                                </td>
                                <td class="small">
                                    <?php if (!empty($p['fecha_inicio']) && !empty($p['fecha_vencimiento'])): ?>
                                        <span class="text-success fw-semibold">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha_inicio']))) ?> al <?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha_vencimiento']))) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted italic">En revisión</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $estadoBadge ?> rounded-pill px-3 py-1">
                                        <?= htmlspecialchars($p['estado']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if ($p['estado'] === 'CONFIRMADO'): ?>
                                        <a href="<?= htmlspecialchars($baseUrl) ?>/negocio/recibo?id=<?= (int)$p['id_pago'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" target="_blank">
                                            <i class="bi bi-receipt me-1"></i> Ver Recibo
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted extra-small">Disponible tras confirmación</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-4 mb-2 d-block text-secondary opacity-50"></i>
                <p class="mb-0">Aún no tienes pagos registrados en tu historial.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* QR Responsivo Grande */
.qr-imagen-grande {
    width: 200px;
    height: 200px;
    object-fit: contain;
    display: block;
}
@media (max-width: 576px) {
    .qr-imagen-grande {
        width: 250px;
        height: 250px;
    }
}
.qr-pago-container {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.qr-pago-container:hover {
    transform: scale(1.03);
    box-shadow: 0 10px 25px rgba(42, 157, 143, 0.25) !important;
}

/* Selector de Planes */
.plan-tarjeta-item {
    transition: all 0.2s ease;
    background-color: #fff;
}
.plan-tarjeta-item:hover {
    border-color: #2A9D8F !important;
    background-color: #f0fdf4;
    transform: translateY(-2px);
}
.bg-primary-subtle-glow {
    border-color: #2A9D8F !important;
    background-color: #f4fbf9;
    box-shadow: 0 4px 14px rgba(42, 157, 143, 0.12);
}

/* Área de subida de archivo */
.input-archivo-oculto {
    position: absolute;
    width: 1px;
    height: 1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}
.subir-comprobante-box {
    border-style: dashed !important;
    border-color: #cbd5e1 !important;
    transition: border-color 0.2s, background-color 0.2s;
}
.subir-comprobante-box.dragover {
    border-color: #2A9D8F !important;
    background-color: #e6f7f5 !important;
}
.extra-small {
    font-size: 0.8rem;
}
</style>

<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/shared/compresor-imagen.js?v=<?= filemtime(dirname(__DIR__, 3) . '/public/assets/js/shared/compresor-imagen.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('comprobante');
    const emptyState = document.getElementById('comprobanteEmptyState');
    const previewState = document.getElementById('comprobantePreviewState');
    const previewImg = document.getElementById('previewImg');
    const nombreTxt = document.getElementById('nombreArchivoTxt');
    const dropArea = document.getElementById('dropAreaComprobante');

    if (input && window.CompresorImagen) {
        window.CompresorImagen.vincular(input, { maxDimension: 1280, calidad: 0.82 });
    }

    // Actualización de preview al seleccionar archivo
    if (input) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                nombreTxt.innerText = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    emptyState.classList.add('d-none');
                    previewState.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag and drop para subir comprobante
    if (dropArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.add('dragover');
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.remove('dragover');
            }, false);
        });
        dropArea.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    // Resaltar plan seleccionado
    const planRadios = document.querySelectorAll('.plan-radio-selector');
    planRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.plan-tarjeta-item').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary-subtle-glow');
                card.classList.add('border-light-subtle');
            });
            const parentCard = radio.closest('.plan-tarjeta-item');
            if (parentCard) {
                parentCard.classList.remove('border-light-subtle');
                parentCard.classList.add('border-primary', 'bg-primary-subtle-glow');
            }
        });
    });
});

function limpiarComprobante() {
    const input = document.getElementById('comprobante');
    const emptyState = document.getElementById('comprobanteEmptyState');
    const previewState = document.getElementById('comprobantePreviewState');
    const previewImg = document.getElementById('previewImg');
    
    if (input) input.value = '';
    if (previewImg) previewImg.src = '';
    if (previewState) previewState.classList.add('d-none');
    if (emptyState) emptyState.classList.remove('d-none');
}
</script>
