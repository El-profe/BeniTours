<?php $escape = static fn($valor) => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); ?>
<?php
$conteosSolicitud = ['PENDIENTE' => 0, 'ACEPTADA' => 0, 'RECHAZADA' => 0];
foreach ($solicitudes as $solicitud) {
    if (isset($conteosSolicitud[$solicitud['estado']])) $conteosSolicitud[$solicitud['estado']]++;
}
$totalSolicitudes = count($solicitudes);
$hayBusqueda = trim((string)$buscar) !== '';
?>
<div class="container-fluid p-0">
    <!-- 1. Encabezado y Métricas de Solicitudes -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                    <i class="bi bi-inbox-fill me-1"></i> Cuarentena Web
                </span>
                <span class="text-muted small">&bull; Trámites de Nuevos Comercios</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Bandeja de Solicitudes de Afiliación</h1>
            <p class="text-muted small mb-0">Verifica comprobantes de pago, datos de negocio y activa nuevas cuentas comerciales.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= $escape($baseUrl) ?>/admin/lugares/crear" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Nueva Ficha Directa
            </a>
            <a href="<?= $escape($baseUrl) ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-grid-1x2 me-1"></i> Panel
            </a>
        </div>
    </div>

    <!-- 1.1 Tarjetas de Estado (KPIs) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="dash-hero-metric <?= ($conteosSolicitud['PENDIENTE'] > 0) ? 'border-warning shadow-xs' : '' ?>">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Por Revisar</span>
                    <div class="dash-metric-icon dash-icon-amber position-relative">
                        <i class="bi bi-hourglass-split"></i>
                        <?php if ($conteosSolicitud['PENDIENTE'] > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dash-metric-number <?= ($conteosSolicitud['PENDIENTE'] > 0) ? 'text-warning' : 'text-dark' ?>">
                    <?= number_format($conteosSolicitud['PENDIENTE']) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-warning fw-semibold"><i class="bi bi-bell-fill"></i> Pendientes</span>
                    <span>Requieren acción</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Aceptadas</span>
                    <div class="dash-metric-icon dash-icon-emerald">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= number_format($conteosSolicitud['ACEPTADA']) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Aprobadas</span>
                    <span>Cuentas creadas</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Rechazadas</span>
                    <div class="dash-metric-icon dash-icon-rose">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= number_format($conteosSolicitud['RECHAZADA']) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-danger fw-semibold"><i class="bi bi-slash-circle"></i> Descartadas</span>
                    <span>Sin vigencia</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Total Solicitudes</span>
                    <div class="dash-metric-icon dash-icon-blue">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= number_format($totalSolicitudes) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-primary fw-semibold"><i class="bi bi-archive"></i> Historial</span>
                    <span>Registros totales</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Barra de Búsqueda y Filtros en Pastillas -->
    <div class="card border-0 rounded-4 shadow-xs bg-white p-3 p-md-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            
            <!-- Buscador Global en Tiempo Real -->
            <form method="get" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 480px;" role="search" id="formBuscarSolicitudes">
                <input type="hidden" name="estado" id="filtroEstadoActualSolicitudes" value="<?= $escape($filtroActual) ?>">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-pill">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" 
                           id="inputBuscadorSolicitudes"
                           name="buscar" 
                           value="<?= $escape($buscar ?? '') ?>" 
                           class="form-control bg-light border-start-0 border-end-0 py-2 ps-1 shadow-none" 
                           placeholder="Buscar por establecimiento, solicitante, teléfono, categoría..." 
                           aria-label="Buscar solicitudes"
                           autocomplete="off">
                    <button type="button" 
                            id="btnClearSearchSolicitudes" 
                            class="btn bg-light border-start-0 border-end-0 text-muted px-2 shadow-none" 
                            title="Limpiar búsqueda" 
                            style="display: <?= !empty($buscar) ? 'block' : 'none' ?>;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    <button class="btn btn-success px-4 fw-semibold rounded-end-pill shadow-none" type="submit">
                        Buscar
                    </button>
                </div>
                <?php if ($hayBusqueda || $filtroActual !== ''): ?>
                    <a class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;" href="<?= $escape($baseUrl) ?>/admin/solicitudes" title="Limpiar todos los filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                <?php endif; ?>
            </form>

            <!-- Filtros por Estado en Pastillas (Pills) -->
            <div class="d-flex align-items-center gap-1 flex-wrap">
                <?php 
                $opcionesFiltro = [
                    ''          => ['label' => 'Todas', 'badge' => $totalSolicitudes],
                    'PENDIENTE' => ['label' => 'Pendientes', 'badge' => $conteosSolicitud['PENDIENTE']],
                    'ACEPTADA'  => ['label' => 'Aceptadas', 'badge' => $conteosSolicitud['ACEPTADA']],
                    'RECHAZADA' => ['label' => 'Rechazadas', 'badge' => $conteosSolicitud['RECHAZADA']]
                ];
                foreach ($opcionesFiltro as $estado => $cfg): 
                    $consultaFiltro = http_build_query(array_filter([
                        'estado' => $estado,
                        'buscar' => $buscar
                    ], static fn($v) => $v !== ''));
                    $enlace = $baseUrl . '/admin/solicitudes' . ($consultaFiltro ? '?' . $consultaFiltro : '');
                    $esActivo = ($filtroActual === $estado);
                ?>
                    <a href="<?= $escape($enlace) ?>" class="filter-pill-btn text-decoration-none <?= $esActivo ? 'active' : '' ?>">
                        <?= $cfg['label'] ?>
                        <span class="badge rounded-pill ms-1 <?= $esActivo ? 'bg-light text-dark' : 'bg-secondary-subtle text-secondary' ?>">
                            <?= (int)$cfg['badge'] ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- Alertas Flash -->
    <?php if ($mensaje): ?>
        <div class="alert alert-success rounded-4 shadow-sm mb-3 d-flex align-items-center gap-2" role="status">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><?= $escape($mensaje) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger rounded-4 shadow-sm mb-3 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= $escape($error) ?></div>
        </div>
    <?php endif; ?>

    <div id="solicitudAdminMensaje" role="status" aria-live="polite" tabindex="-1"></div>

    <!-- 3. Tabla SaaS de Solicitudes -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table solicitudes-table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Establecimiento & Rubro</th>
                        <th>Solicitante & Contacto</th>
                        <th>Plan & Monto</th>
                        <th>Comprobante</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitudes as $s): ?>
                    <?php 
                    $searchTerms = mb_strtolower(implode(' ', array_filter([
                        '#' . $s['id_solicitud'],
                        $s['id_solicitud'],
                        $s['nombre_establecimiento'],
                        $s['categoria'] ?? '',
                        $s['municipio'] ?? '',
                        $s['nombre_solicitante'],
                        $s['telefono_contacto'],
                        $s['email_contacto'] ?? '',
                        $s['plan_solicitado'] ?? '',
                        $s['numero_comprobante'] ?? '',
                        $s['estado'] ?? ''
                    ])), 'UTF-8');
                    ?>
                    <tr class="solicitud-row" 
                        data-solicitud="<?= (int)$s['id_solicitud'] ?>"
                        data-estado="<?= $escape($s['estado']) ?>"
                        data-search="<?= $escape($searchTerms) ?>">
                        <!-- Establecimiento -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="solicitud-avatar">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-light text-muted border extra-small">#<?= (int)$s['id_solicitud'] ?></span>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill extra-small">
                                            <i class="bi bi-geo-alt me-1"></i><?= $escape($s['municipio'] ?? 'Trinidad') ?>
                                        </span>
                                    </div>
                                    <strong class="d-block text-dark fs-6"><?= $escape($s['nombre_establecimiento']) ?></strong>
                                    <small class="text-muted"><i class="bi bi-tag text-success me-1"></i><?= $escape($s['categoria']) ?></small>
                                </div>
                            </div>
                        </td>

                        <!-- Solicitante -->
                        <td>
                            <strong class="text-dark small d-block"><?= $escape($s['nombre_solicitante']) ?></strong>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <a href="tel:<?= $escape($s['telefono_contacto']) ?>" class="badge bg-light text-dark border text-decoration-none">
                                    <i class="bi bi-telephone me-1 text-primary"></i><?= $escape($s['telefono_contacto']) ?>
                                </a>
                                <?php if (!empty($s['email_contacto'])): ?>
                                    <span class="text-muted extra-small text-truncate" style="max-width: 140px;" title="<?= $escape($s['email_contacto']) ?>">
                                        <i class="bi bi-envelope me-1"></i><?= $escape($s['email_contacto']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Plan & Monto -->
                        <td>
                            <div class="fw-bold text-success small">
                                Bs <?= number_format((float)($s['monto_declarado'] ?? ($s['plan_solicitado'] === 'ANUAL' ? 2500 : 250)), 2) ?>
                            </div>
                            <small class="badge bg-light text-dark border extra-small">
                                <?= $escape($s['tarifa_nombre'] ?? ($s['plan_solicitado'] === 'ANUAL' ? 'Plan Anual' : 'Plan Mensual')) ?>
                            </small>
                            <div class="extra-small text-muted mt-1">
                                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?>
                            </div>
                        </td>

                        <!-- Comprobante / Método -->
                        <td>
                            <?php if ($s['comprobante_archivo']): ?>
                                <button type="button" class="comprobante-preview-btn" data-bs-toggle="modal" data-bs-target="#comprobante<?= (int)$s['id_solicitud'] ?>">
                                    <img src="<?= $escape($baseUrl) ?>/admin/solicitudes/comprobante?id=<?= (int)$s['id_solicitud'] ?>" class="comprobante-thumb-mini" alt="Thumb">
                                    <span>Ver Recibo</span>
                                </button>
                                <?php if (!empty($s['numero_comprobante'])): ?>
                                    <small class="d-block text-muted extra-small mt-1 font-monospace">N° <?= $escape($s['numero_comprobante']) ?></small>
                                <?php endif; ?>
                            <?php elseif (($s['metodo_pago'] ?? '') === 'EFECTIVO'): ?>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-cash-stack me-1 text-success"></i> Efectivo
                                </span>
                                <small class="d-block text-muted extra-small mt-1">Cobro directo</small>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border extra-small">Sin archivo</span>
                            <?php endif; ?>
                        </td>

                        <!-- Estado -->
                        <td data-estado>
                            <?php 
                            $badgeClass = match($s['estado']) {
                                'ACEPTADA'  => 'bg-success-subtle text-success border border-success-subtle',
                                'RECHAZADA' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                default     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
                            };
                            $badgeIcon = match($s['estado']) {
                                'ACEPTADA'  => 'bi-check-circle-fill',
                                'RECHAZADA' => 'bi-x-circle-fill',
                                default     => 'bi-hourglass-split'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1 fw-bold small">
                                <i class="bi <?= $badgeIcon ?> me-1"></i><?= $escape($s['estado']) ?>
                            </span>
                            <?php if ($s['id_lugar_creado']): ?>
                                <small class="d-block text-muted extra-small mt-1">
                                    <a href="<?= $escape($baseUrl) ?>/admin/lugares/editar?id=<?= (int)$s['id_lugar_creado'] ?>" class="text-decoration-none text-muted">
                                        Ficha #<?= (int)$s['id_lugar_creado'] ?> &rarr;
                                    </a>
                                </small>
                            <?php endif; ?>
                        </td>

                        <!-- Acciones -->
                        <td class="text-end pe-4" data-acciones>
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Botón Detalle -->
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#detalle<?= (int)$s['id_solicitud'] ?>" title="Ver datos completos">
                                    <i class="bi bi-info-circle me-1"></i> Detalle
                                </button>

                                <?php if ($s['estado'] === 'PENDIENTE' && $s['comprobante_archivo']): ?>
                                    <form action="<?= $escape($baseUrl) ?>/admin/solicitudes/aprovisionar" method="post" class="d-inline form-aprovisionar" data-solicitud-accion="aprovisionar">
                                        <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                        <input type="hidden" name="id_solicitud" value="<?= (int)$s['id_solicitud'] ?>">
                                        <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-none" type="submit" title="Aprobar y crear cuenta">
                                            <i class="bi bi-check2-circle me-1"></i> Aprobar
                                        </button>
                                    </form>
                                <?php elseif ($s['estado'] === 'PENDIENTE'): ?>
                                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-none btn-cobro-efectivo" 
                                            type="button" 
                                            data-id="<?= (int)$s['id_solicitud'] ?>" 
                                            data-nombre="<?= $escape($s['nombre_establecimiento']) ?>" 
                                            data-monto="<?= $escape($s['monto_declarado']) ?>" 
                                            data-plan="<?= $escape($s['plan_solicitado']) ?>">
                                        <i class="bi bi-cash-stack me-1"></i> Cobrar y Activar
                                    </button>
                                <?php endif; ?>

                                <?php if ($s['estado'] === 'PENDIENTE'): ?>
                                    <form action="<?= $escape($baseUrl) ?>/admin/solicitudes/resolver" method="post" class="d-inline" data-solicitud-accion="rechazar">
                                        <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                        <input type="hidden" name="id_solicitud" value="<?= (int)$s['id_solicitud'] ?>">
                                        <input type="hidden" name="estado" value="RECHAZADA">
                                        <button class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-none" style="width: 32px; height: 32px;" type="submit" title="Rechazar solicitud">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($s['estado'] === 'ACEPTADA' && $s['id_cuenta_creada']): ?>
                                    <?php try { $whatsapp = \App\Services\SolicitudService::enlaceBienvenida($s); } catch (\Throwable $e) { $whatsapp = null; } ?>
                                    <?php if ($whatsapp): ?>
                                        <a href="<?= $escape($whatsapp) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-none" title="Enviar credenciales por WhatsApp">
                                            <i class="bi bi-whatsapp"></i> <span>WhatsApp</span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Fila de Sin Resultados en Vivo (JavaScript) -->
                <tr id="filaSinResultadosSolicitudes" style="display: none;">
                    <td colspan="6" class="text-center py-5">
                        <div class="admin-empty-state p-4">
                            <i class="bi bi-search fs-1 text-muted mb-2 d-block opacity-50"></i>
                            <strong class="text-dark fs-6 d-block" id="textoBusquedaFallidaSolicitudes">No se encontraron solicitudes</strong>
                            <span class="text-muted small">Intenta buscar por otro término o limpia el buscador.</span>
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="btnRestablecerLiveSolicitudes">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar búsqueda
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>

                <?php if (!$solicitudes): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="admin-empty-state p-4">
                                <i class="bi bi-inbox fs-1 text-muted mb-2 d-block opacity-50"></i>
                                <strong class="text-dark fs-6 d-block">No se encontraron solicitudes</strong>
                                <span class="text-muted small">No hay trámites que coincidan con la búsqueda o filtro aplicado.</span>
                                <div class="mt-3">
                                    <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="<?= $escape($baseUrl) ?>/admin/solicitudes">
                                        Restablecer filtros
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales de Comprobante y Detalle -->
<?php foreach ($solicitudes as $s): ?>
    <?php if ($s['comprobante_archivo']): ?>
    <div class="modal fade" id="comprobante<?= (int)$s['id_solicitud'] ?>" tabindex="-1" aria-labelledby="tituloComprobante<?= (int)$s['id_solicitud'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 id="tituloComprobante<?= (int)$s['id_solicitud'] ?>" class="modal-title fw-bold text-dark mb-0">
                            <i class="bi bi-receipt text-success me-2"></i>Comprobante de Pago #<?= (int)$s['id_solicitud'] ?>
                        </h5>
                        <small class="text-muted"><?= $escape($s['nombre_establecimiento']) ?></small>
                    </div>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 text-center bg-light">
                    <img loading="lazy" class="img-fluid rounded-3 shadow-sm border" style="max-height: 480px; object-fit: contain;" alt="Comprobante bancario" src="<?= $escape($baseUrl) ?>/admin/solicitudes/comprobante?id=<?= (int)$s['id_solicitud'] ?>">
                    <?php if (!empty($s['numero_comprobante'])): ?>
                        <div class="mt-3 p-2 bg-white rounded-3 border d-inline-block">
                            <span class="text-muted small">N° Comprobante:</span>
                            <strong class="text-dark ms-1 font-monospace"><?= $escape($s['numero_comprobante']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-top bg-white">
                    <a href="<?= $escape($baseUrl) ?>/admin/solicitudes/comprobante?id=<?= (int)$s['id_solicitud'] ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Imagen Completa
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal Detalle del Establecimiento -->
    <div class="modal fade" id="detalle<?= (int)$s['id_solicitud'] ?>" tabindex="-1" aria-labelledby="tituloDetalle<?= (int)$s['id_solicitud'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 id="tituloDetalle<?= (int)$s['id_solicitud'] ?>" class="modal-title fw-bold text-dark mb-0">
                            <?= $escape($s['nombre_establecimiento']) ?>
                        </h5>
                        <span class="badge bg-light text-muted border extra-small">Solicitud #<?= (int)$s['id_solicitud'] ?> &bull; <?= $escape($s['categoria']) ?></span>
                    </div>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-dark small text-uppercase mb-2"><i class="bi bi-card-text text-success me-1"></i> Descripción del Lugar</h6>
                    <p class="small text-muted mb-3 bg-light p-3 rounded-3" style="white-space: pre-line"><?= $escape($s['descripcion']) ?></p>

                    <h6 class="fw-bold text-dark small text-uppercase mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Datos del Establecimiento</h6>
                    <ul class="list-unstyled small mb-3 d-flex flex-column gap-2">
                        <li><strong>Municipio:</strong> <?= $escape($s['municipio'] ?? 'Trinidad') ?></li>
                        <li><strong>Dirección:</strong> <?= $escape($s['direccion']) ?></li>
                        <li><strong>Horarios:</strong> <?= $escape($s['horarios']) ?></li>
                        <li><strong>Solicitante:</strong> <?= $escape($s['nombre_solicitante']) ?></li>
                        <li><strong>Teléfono / WhatsApp:</strong> <?= $escape($s['telefono_contacto']) ?></li>
                        <li><strong>Correo Electrónico:</strong> <?= $escape($s['email_contacto']) ?></li>
                        <li><strong>Usuario en el Sistema:</strong> <span class="badge bg-light text-dark border"><?= $escape($s['usuario_asignado'] ?: 'Se generará al aprobar') ?></span></li>
                    </ul>

                    <?php if ($s['estado'] === 'PENDIENTE'): ?>
                        <div class="border-top pt-3 mt-3">
                            <h6 class="fw-bold text-danger small text-uppercase mb-2"><i class="bi bi-slash-circle me-1"></i> Descartar o Rechazar</h6>
                            <form action="<?= $escape($baseUrl) ?>/admin/solicitudes/resolver" method="post" data-solicitud-accion="rechazar">
                                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                <input type="hidden" name="id_solicitud" value="<?= (int)$s['id_solicitud'] ?>">
                                <input type="hidden" name="estado" value="RECHAZADA">
                                <textarea name="observaciones_admin" class="form-control form-control-sm mb-2 rounded-3" placeholder="Motivo u observaciones del rechazo..." maxlength="2000"></textarea>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" type="submit">Confirmar Rechazo</button>
                            </form>
                        </div>
                    <?php elseif (!empty($s['observaciones_admin'])): ?>
                        <div class="border-top pt-3 mt-3">
                            <strong class="small text-muted d-block mb-1">Observaciones registradas:</strong>
                            <p class="small text-danger bg-danger-subtle p-2 rounded-3 mb-0"><?= $escape($s['observaciones_admin']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal de Credenciales Generadas (Nivel 1) -->
<?php require dirname(__DIR__) . '/parciales/modal_credenciales.php'; ?>


<!-- Modal para Confirmar Cobro en Efectivo -->
<div class="modal fade" id="modalConfirmarCobroEfectivo" tabindex="-1" aria-labelledby="tituloModalCobro" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content rounded-4 border-0 shadow" id="formCobroEfectivoModal">
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <input type="hidden" name="id_solicitud" id="cobroEfectivoIdSolicitud" value="">
            <input type="hidden" name="metodo_pago" value="EFECTIVO">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="tituloModalCobro"><i class="bi bi-cash-stack text-success me-2"></i>Registrar Cobro en Efectivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Vas a confirmar que recibiste el pago en efectivo de este comerciante y se activará su cuenta de inmediato.</p>
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <strong class="d-block text-dark fs-6" id="cobroEfectivoNombre">-</strong>
                    <small class="text-muted" id="cobroEfectivoPlan">-</small>
                </div>
                <div class="mb-3">
                    <label for="cobroEfectivoMonto" class="form-label fw-bold small">Monto Recibido en Mano (Bs) *</label>
                    <input type="text" class="form-control form-control-lg fw-bold text-success" id="cobroEfectivoMonto" readonly>
                </div>
                <div class="mb-3">
                    <label for="cobroEfectivoRecibo" class="form-label fw-bold small">N° de Recibo o Talonario Físico (opcional)</label>
                    <input type="text" name="numero_recibo" class="form-control" id="cobroEfectivoRecibo" placeholder="Ej. REC-00124" maxlength="50">
                    <small class="text-muted">Si le entregaste un recibo en papel, anota aquí el número correlativo.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnConfirmarCobroEfectivo">
                    <i class="bi bi-check2-circle me-1"></i> Cobrar y Generar Credenciales
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= $escape($baseUrl) ?>/assets/js/admin/credenciales-modal.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/admin/credenciales-modal.js') ?>" defer></script>
<script src="<?= $escape($baseUrl) ?>/assets/js/admin/solicitudes.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/admin/solicitudes.js') ?>" defer></script>
