<div class="container-fluid p-0">
    <!-- Encabezado Principal y Acceso Rápido -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                    <i class="bi bi-wallet2 me-1"></i> Finanzas & Membresías
                </span>
                <span class="text-muted small">&bull; Conciliación y Cobertura Comercial</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Control de Pagos y Vigencias</h1>
            <p class="text-muted small mb-0">Auditoría manual de transferencias bancarias, cobros en efectivo y activación de meses.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/crear" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Registrar Pago
            </a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-grid-1x2 me-1"></i> Panel
            </a>
        </div>
    </div>

    <!-- Alertas de Publicaciones Próximas a Vencer (RF-29) -->
    <?php if (!empty($proximosVencimientos)): ?>
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 p-3 d-flex align-items-center gap-3">
            <div class="widget-icon bg-warning text-dark flex-shrink-0">
                <i class="bi bi-bell-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark">Publicaciones comerciales próximas a vencer (Siguientes 5 días):</h6>
                <ul class="mb-0 small ps-3 text-secondary">
                    <?php foreach ($proximosVencimientos as $pv): ?>
                        <li>
                            <strong><?= htmlspecialchars($pv['nombre']) ?></strong> vence el 
                            <strong><?= date('d/m/Y', strtotime($pv['fecha_vencimiento'])) ?></strong> 
                            (Quedan <span class="badge bg-warning text-dark"><?= $pv['dias_restantes'] ?> días</span>).
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Alertas Flash -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 small mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 small mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-x-octagon-fill me-2 fs-5"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Resumen Financiero y Comercial (KPIs SaaS Limpios) -->
    <div class="row g-3 mb-4">
        <!-- Ingresos Confirmados -->
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Ingresos Confirmados</span>
                    <div class="dash-metric-icon dash-icon-emerald">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    Bs <?= number_format($estadisticas['totalConfirmado'], 2) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Auditado</span>
                    <span>Histórico real acumulado</span>
                </div>
            </div>
        </div>

        <!-- Generado este Mes -->
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Generado este Mes</span>
                    <div class="dash-metric-icon dash-icon-blue">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    Bs <?= number_format($estadisticas['recaudadoMes'], 2) ?>
                </div>
                <div class="dash-metric-sub">
                    <?php if ($estadisticas['variacionMensual'] !== null): ?>
                        <span class="<?= $estadisticas['variacionMensual'] >= 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                            <i class="bi bi-graph-<?= $estadisticas['variacionMensual'] >= 0 ? 'up' : 'down' ?>-arrow"></i>
                            <?= $estadisticas['variacionMensual'] >= 0 ? '+' : '' ?><?= number_format($estadisticas['variacionMensual'], 1) ?>%
                        </span>
                        <span>vs. mes anterior</span>
                    <?php else: ?>
                        <span class="text-muted">Mes base</span>
                        <span>Primer periodo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Por Validar -->
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric <?= ($estadisticas['pendienteCantidad'] > 0) ? 'border-warning shadow-xs' : '' ?>">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Por Validar</span>
                    <div class="dash-metric-icon dash-icon-amber position-relative">
                        <i class="bi bi-hourglass-split"></i>
                        <?php if ($estadisticas['pendienteCantidad'] > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dash-metric-number <?= ($estadisticas['pendienteCantidad'] > 0) ? 'text-warning' : 'text-dark' ?>">
                    Bs <?= number_format($estadisticas['pendienteMonto'], 2) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> <?= $estadisticas['pendienteCantidad'] ?> cobro(s)</span>
                    <span>Pendientes de conciliar</span>
                </div>
            </div>
        </div>

        <!-- Comercios Vigentes -->
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Comercios Vigentes</span>
                    <div class="dash-metric-icon dash-icon-purple">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= number_format($estadisticas['comerciosVigentes']) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-purple fw-semibold"><i class="bi bi-shop"></i> Activos</span>
                    <span>Con cobertura al día</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico Financiero y Banner de Prioridad -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-xs bg-white p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3 flex-wrap">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-bar-chart-line-fill text-success me-2"></i>Evolución de Ingresos Mensuales</h5>
                        <p class="small text-muted mb-0">Total recaudado de cobros confirmados durante los últimos seis meses.</p>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                        <i class="bi bi-check-all me-1"></i>Solo pagos confirmados
                    </span>
                </div>
                <div style="height: 275px; position: relative;"><canvas id="chartIngresosPagos"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-xs bg-white h-100 p-4 d-flex flex-column justify-content-between border-start border-success border-4">
                <div>
                    <span class="small text-uppercase fw-bold text-success"><i class="bi bi-shield-lock me-1"></i>Prioridad Operativa</span>
                    <h5 class="fw-bold text-dark mt-2">Revisión de Comprobantes</h5>
                    <p class="text-muted small mb-3">Cada pago confirmado activa o extiende la cobertura de visualización del negocio en el catálogo municipal.</p>
                    
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                        <span class="dash-metric-icon dash-icon-amber fs-4"><?= $estadisticas['pendienteCantidad'] ?></span>
                        <div>
                            <strong class="d-block text-dark small">Cobros por verificar</strong>
                            <small class="text-muted extra-small">Aguardan confirmación de depósito o efectivo</small>
                        </div>
                    </div>
                </div>

                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos?estado=PENDIENTE" class="btn btn-success rounded-pill w-100 mt-4 fw-bold shadow-sm">
                    Revisar Pagos Pendientes <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Barra de Búsqueda Mejorada y Filtros en Pastillas -->
    <div class="card border-0 rounded-4 shadow-xs bg-white p-3 p-md-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            
            <!-- Buscador Potenciado en Tiempo Real -->
            <form method="get" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 520px;" role="search" id="formBuscarPagos">
                <input type="hidden" name="estado" id="filtroEstadoActualPagos" value="<?= htmlspecialchars($filtroActual) ?>">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted ps-3 rounded-start-pill">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" 
                           id="inputBuscadorPagos"
                           name="buscar" 
                           value="<?= htmlspecialchars($buscar ?? '') ?>" 
                           class="form-control bg-light border-start-0 border-end-0 py-2 ps-1 shadow-none" 
                           placeholder="Buscar por comercio, recibo, método, monto o notas..." 
                           aria-label="Buscar pagos"
                           autocomplete="off">
                    <button type="button" 
                            id="btnClearSearchPagos" 
                            class="btn bg-light border-start-0 border-end-0 text-muted px-2 shadow-none" 
                            title="Limpiar búsqueda" 
                            style="display: <?= !empty($buscar) ? 'block' : 'none' ?>;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    <button class="btn btn-success px-4 fw-semibold rounded-end-pill shadow-none" type="submit">
                        Buscar
                    </button>
                </div>
                <button type="button" 
                        class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                        id="btnResetFiltrosPagos" 
                        style="width: 38px; height: 38px; display: <?= (!empty($buscar) || !empty($filtroActual)) ? 'flex' : 'none' ?>;" 
                        title="Limpiar búsqueda y filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </form>

            <!-- Filtros de Estado en Pastillas (Pills) -->
            <div class="d-flex align-items-center gap-1 flex-wrap" id="contenedorFiltrosEstadoPagos">
                <button type="button" class="filter-pill-btn <?= empty($filtroActual) ? 'active' : '' ?>" data-filtro-estado="">
                    Todos los Pagos
                </button>
                <button type="button" class="filter-pill-btn <?= $filtroActual === 'PENDIENTE' ? 'active' : '' ?>" data-filtro-estado="PENDIENTE">
                    Pendientes
                    <?php if ($estadisticas['pendienteCantidad'] > 0): ?>
                        <span class="badge rounded-pill ms-1 bg-warning text-dark"><?= $estadisticas['pendienteCantidad'] ?></span>
                    <?php endif; ?>
                </button>
                <button type="button" class="filter-pill-btn <?= $filtroActual === 'CONFIRMADO' ? 'active' : '' ?>" data-filtro-estado="CONFIRMADO">
                    Confirmados
                </button>
                <button type="button" class="filter-pill-btn <?= $filtroActual === 'ANULADO' ? 'active' : '' ?>" data-filtro-estado="ANULADO">
                    Anulados
                </button>
            </div>

        </div>
    </div>

    <!-- Tabla Principal de Pagos -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table solicitudes-table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 80px;">Pago #</th>
                        <th>Establecimiento Comercial</th>
                        <th>Importe & Cobertura</th>
                        <th>Fecha Declarada</th>
                        <th>Método & Comprobante</th>
                        <th>Estado</th>
                        <th>Vigencia Otorgada</th>
                        <th class="text-end pe-4" style="width: 170px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pagos)): ?>
                        <?php foreach ($pagos as $p): ?>
                            <?php 
                            $searchTermsPago = mb_strtolower(implode(' ', array_filter([
                                '#' . $p['id_pago'],
                                $p['id_pago'],
                                $p['nombre_establecimiento'],
                                (string)$p['monto'],
                                'bs ' . $p['monto'],
                                $p['numero_comprobante'] ?? '',
                                $p['metodo_pago'] ?? '',
                                $p['estado'] ?? '',
                                $p['observaciones'] ?? '',
                                (string)($p['meses_duracion'] ?? '') . ' meses'
                            ])), 'UTF-8');
                            ?>
                            <tr class="pago-row" 
                                data-pago="<?= (int)$p['id_pago'] ?>"
                                data-estado="<?= htmlspecialchars($p['estado']) ?>"
                                data-search="<?= htmlspecialchars($searchTermsPago) ?>">
                                <td class="ps-4 text-muted fw-bold font-monospace">
                                    #<?= $p['id_pago'] ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="solicitud-avatar" style="width: 36px; height: 36px; font-size: 1rem;">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark d-block"><?= htmlspecialchars($p['nombre_establecimiento']) ?></strong>
                                            <?php if (!empty($p['observaciones'])): ?>
                                                <small class="text-muted extra-small d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($p['observaciones']) ?>">
                                                    <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($p['observaciones']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success fs-6">
                                        Bs <?= number_format($p['monto'], 2) ?>
                                    </div>
                                    <span class="badge bg-light text-muted border extra-small">
                                        <?= (int)$p['meses_duracion'] ?> mes(es) de plan
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($p['fecha_pago_declarada'])) ?>
                                </td>
                                <td>
                                    <span class="small d-block text-dark fw-semibold"><?= htmlspecialchars($p['metodo_pago']) ?></span>
                                    <?php if (!empty($p['comprobante_archivo'])): ?>
                                        <button type="button" class="comprobante-preview-btn mt-1" data-bs-toggle="modal" data-bs-target="#comprobantePago<?= (int)$p['id_pago'] ?>">
                                            <img src="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/comprobante?id=<?= (int)$p['id_pago'] ?>" class="comprobante-thumb-mini" alt="Recibo">
                                            <span>Ver Comprobante</span>
                                        </button>
                                        <?php if (!empty($p['numero_comprobante'])): ?>
                                            <small class="d-block text-muted extra-small font-monospace mt-1">N° <?= htmlspecialchars($p['numero_comprobante']) ?></small>
                                        <?php endif; ?>
                                    <?php elseif (!empty($p['numero_comprobante'])): ?>
                                        <code class="extra-small text-muted font-monospace">N° <?= htmlspecialchars($p['numero_comprobante']) ?></code>
                                    <?php else: ?>
                                        <small class="text-muted extra-small">Sin comprobante físico</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['estado'] === 'PENDIENTE'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                        </span>
                                    <?php elseif ($p['estado'] === 'CONFIRMADO'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-check-circle-fill me-1"></i>Confirmado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-x-circle-fill me-1"></i>Anulado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($p['fecha_inicio'])): ?>
                                        <div class="small fw-semibold text-success">
                                            <?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?> &rarr; <?= date('d/m/Y', strtotime($p['fecha_vencimiento'])) ?>
                                        </div>
                                        <span class="badge bg-light text-muted border extra-small" style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($p['tipo_periodo']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted extra-small">Sin vigencia asignada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($p['estado'] === 'PENDIENTE'): ?>
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <!-- Formulario Confirmar Pago (Transaccional) -->
                                            <form action="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/confirmar" method="POST" class="d-inline m-0">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="id_pago" value="<?= $p['id_pago'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-none" title="Confirmar abono y activar ficha">
                                                    <i class="bi bi-check2 me-1"></i> Confirmar
                                                </button>
                                            </form>

                                            <!-- Botón Anular -->
                                            <?php if (!empty($p['es_registro_inicial'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#rechazarRegistro<?= (int)$p['id_pago'] ?>" title="Eliminar registro">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center shadow-none" data-bs-toggle="modal" data-bs-target="#modalAnular<?= $p['id_pago'] ?>" title="Anular comprobante" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Modal Anulación -->
                                        <div class="modal fade text-start" id="modalAnular<?= $p['id_pago'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <form action="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/anular" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                        <input type="hidden" name="id_pago" value="<?= $p['id_pago'] ?>">
                                                        <div class="modal-body p-4 text-center">
                                                            <i class="bi bi-exclamation-circle text-danger display-5 mb-2 d-block"></i>
                                                            <h6 class="fw-bold mb-2">¿Anular Pago #<?= $p['id_pago'] ?>?</h6>
                                                            <textarea name="motivo_anulacion" rows="2" class="form-control form-control-sm mb-3 rounded-3" placeholder="Motivo (ej. Fondos no acreditados)" required></textarea>
                                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill w-100 fw-bold">Confirmar Anulación</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-inline-flex gap-2 align-items-center justify-content-end">
                                            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/recibo?id=<?= (int)$p['id_pago'] ?>" 
                                               class="btn-recibo-action text-decoration-none" 
                                               target="_blank"
                                               title="Ver e Imprimir Recibo Oficial">
                                                <i class="bi bi-receipt"></i>
                                                <span>Recibo</span>
                                            </a>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small fw-semibold">
                                                <i class="bi bi-check2"></i> Procesado
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Fila de Sin Resultados en Tiempo Real (JavaScript) -->
                        <tr id="filaSinResultadosPagos" style="display: none;">
                            <td colspan="8" class="text-center py-5">
                                <div class="admin-empty-state p-4">
                                    <i class="bi bi-search fs-1 text-muted mb-2 d-block opacity-50"></i>
                                    <strong class="text-dark fs-6 d-block" id="textoBusquedaFallidaPagos">No se encontraron pagos</strong>
                                    <span class="text-muted small">Intenta buscar por otro término o limpia el buscador.</span>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="btnRestablecerLivePagos">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar búsqueda
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="admin-empty-state p-4">
                                    <i class="bi bi-cash-coin fs-1 text-muted mb-2 d-block opacity-50"></i>
                                    <strong class="text-dark fs-6 d-block">No se encontraron pagos</strong>
                                    <span class="text-muted small">No hay pagos que coincidan con la búsqueda o filtro aplicado.</span>
                                    <div class="mt-3">
                                        <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos">
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

<?php foreach ($pagos as $p): ?>
    <?php if (!empty($p['comprobante_archivo'])): ?>
    <div class="modal fade" id="comprobantePago<?= (int)$p['id_pago'] ?>" tabindex="-1" aria-labelledby="tituloPago<?= (int)$p['id_pago'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 id="tituloPago<?= (int)$p['id_pago'] ?>" class="modal-title fw-bold text-dark mb-0">
                            <i class="bi bi-receipt text-success me-2"></i>Comprobante del Pago #<?= (int)$p['id_pago'] ?>
                        </h5>
                        <small class="text-muted"><?= htmlspecialchars($p['nombre_establecimiento']) ?></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center p-4 bg-light">
                    <img class="img-fluid rounded-3 shadow-sm border" style="max-height: 480px; object-fit: contain;" loading="lazy" alt="Comprobante bancario" src="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/comprobante?id=<?= (int)$p['id_pago'] ?>">
                    <?php if (!empty($p['numero_comprobante'])): ?>
                        <div class="mt-3 p-2 bg-white rounded-3 border d-inline-block">
                            <span class="text-muted small">N° Transacción / Recibo:</span>
                            <strong class="text-dark ms-1 font-monospace"><?= htmlspecialchars($p['numero_comprobante']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-top bg-white">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/comprobante?id=<?= (int)$p['id_pago'] ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Imagen Completa
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($p['estado'] === 'PENDIENTE' && !empty($p['es_registro_inicial'])): ?>
    <div class="modal fade" id="rechazarRegistro<?= (int)$p['id_pago'] ?>" tabindex="-1" aria-labelledby="tituloRechazar<?= (int)$p['id_pago'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 id="tituloRechazar<?= (int)$p['id_pago'] ?>" class="modal-title fw-bold text-danger">Eliminar registro falso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">Se eliminarán permanentemente <strong><?= htmlspecialchars($p['nombre_establecimiento']) ?></strong>, su cuenta, fotos y pagos pendientes. El usuario perderá el acceso.</p>
                    <form action="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/rechazar-eliminar" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="id_pago" value="<?= (int)$p['id_pago'] ?>">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Eliminar definitivamente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const contexto = document.getElementById('chartIngresosPagos')?.getContext('2d');
    if (contexto && window.Chart) {
        const etiquetas = <?= json_encode($graficoIngresos['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const ingresos = <?= json_encode($graficoIngresos['valores']) ?>;
        const pagosMes = <?= json_encode($graficoIngresos['pagos']) ?>;
        const gradiente = contexto.createLinearGradient(0, 0, 0, 260);
        gradiente.addColorStop(0, 'rgba(42, 157, 143, 0.48)');
        gradiente.addColorStop(1, 'rgba(42, 157, 143, 0.03)');

        new Chart(contexto, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Ingresos confirmados',
                    data: ingresos,
                    backgroundColor: gradiente,
                    borderColor: '#2A9D8F',
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 54
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#092611',
                        padding: 11,
                        callbacks: {
                            label: (context) => ' Bs ' + Number(context.parsed.y).toLocaleString('es-BO', { minimumFractionDigits: 2 }),
                            afterLabel: (context) => ' ' + pagosMes[context.dataIndex] + ' pago(s) confirmado(s)'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
                        ticks: { callback: (valor) => 'Bs ' + Number(valor).toLocaleString('es-BO') }
                    }
                },
            }
        });
    }

    // ==========================================
    // BUSCADOR EN TIEMPO REAL (LIVE SEARCH)
    // ==========================================
    // ==========================================
    // BUSCADOR Y FILTRADO EN TIEMPO REAL (SIN RECARGAR PÁGINA)
    // ==========================================
    const formBuscarPagos = document.getElementById('formBuscarPagos');
    const inputSearchPagos = document.getElementById('inputBuscadorPagos');
    const btnClearPagos = document.getElementById('btnClearSearchPagos');
    const btnResetFiltrosPagos = document.getElementById('btnResetFiltrosPagos');
    const btnRestablecerLivePagos = document.getElementById('btnRestablecerLivePagos');
    const filaSinResultadosPagos = document.getElementById('filaSinResultadosPagos');
    const textoBusquedaFallidaPagos = document.getElementById('textoBusquedaFallidaPagos');
    const filasPagos = Array.from(document.querySelectorAll('tr.pago-row'));
    const botonesFiltroEstado = Array.from(document.querySelectorAll('#contenedorFiltrosEstadoPagos [data-filtro-estado]'));
    
    let estadoSeleccionado = (new URLSearchParams(window.location.search)).get('estado') || '';

    function aplicarFiltrosPagos() {
        const query = (inputSearchPagos ? inputSearchPagos.value : '').trim().toLowerCase();

        // Visibilidad botón de limpieza en el input
        if (btnClearPagos) {
            btnClearPagos.style.display = query !== '' ? 'block' : 'none';
        }

        // Visibilidad botón reset global
        if (btnResetFiltrosPagos) {
            btnResetFiltrosPagos.style.display = (query !== '' || estadoSeleccionado !== '') ? 'flex' : 'none';
        }

        let visibles = 0;

        filasPagos.forEach(fila => {
            const dataSearch = fila.getAttribute('data-search') || '';
            const dataId = fila.getAttribute('data-pago') || '';
            const dataEstado = fila.getAttribute('data-estado') || '';

            // 1. Filtro por texto
            let coincideTexto = true;
            if (query !== '') {
                const queryLimpia = query.replace(/^#/, '');
                coincideTexto = dataSearch.includes(query) || (queryLimpia !== '' && dataId === queryLimpia);
            }

            // 2. Filtro por estado
            let coincideEstado = true;
            if (estadoSeleccionado !== '') {
                coincideEstado = (dataEstado === estadoSeleccionado);
            }

            if (coincideTexto && coincideEstado) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        // Mostrar / Ocultar mensaje sin resultados
        if (filaSinResultadosPagos) {
            if (visibles === 0 && filasPagos.length > 0) {
                filaSinResultadosPagos.style.display = '';
                if (textoBusquedaFallidaPagos) {
                    let mensaje = 'No se encontraron pagos';
                    if (query !== '' && estadoSeleccionado !== '') {
                        mensaje = `No se encontraron pagos con estado "${estadoSeleccionado}" para "${inputSearchPagos.value}".`;
                    } else if (query !== '') {
                        mensaje = `No se encontraron pagos para "${inputSearchPagos.value}".`;
                    } else if (estadoSeleccionado !== '') {
                        mensaje = `No hay pagos con estado "${estadoSeleccionado}".`;
                    }
                    textoBusquedaFallidaPagos.textContent = mensaje;
                }
            } else {
                filaSinResultadosPagos.style.display = 'none';
            }
        }

        // Actualizar URL sin recargar la página (para poder copiar o compartir si se desea)
        const params = new URLSearchParams();
        if (estadoSeleccionado) params.set('estado', estadoSeleccionado);
        if (query) params.set('buscar', inputSearchPagos.value.trim());
        const nuevoQuery = params.toString();
        const nuevaUrl = window.location.pathname + (nuevoQuery ? '?' + nuevoQuery : '');
        window.history.replaceState({}, '', nuevaUrl);
    }

    // Evitar que el formulario recargue la página
    if (formBuscarPagos) {
        formBuscarPagos.addEventListener('submit', (e) => {
            e.preventDefault();
            aplicarFiltrosPagos();
        });
    }

    // Búsqueda en vivo al teclear
    if (inputSearchPagos) {
        inputSearchPagos.addEventListener('input', aplicarFiltrosPagos);
    }

    // Limpiar texto de búsqueda
    if (btnClearPagos) {
        btnClearPagos.addEventListener('click', () => {
            if (inputSearchPagos) {
                inputSearchPagos.value = '';
                inputSearchPagos.focus();
                aplicarFiltrosPagos();
            }
        });
    }

    // Interacción con los botones de pastilla (Pills)
    botonesFiltroEstado.forEach(btn => {
        btn.addEventListener('click', () => {
            botonesFiltroEstado.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            estadoSeleccionado = btn.getAttribute('data-filtro-estado') || '';
            aplicarFiltrosPagos();
        });
    });

    // Botón de reset global (recargar/limpiar todo)
    if (btnResetFiltrosPagos) {
        btnResetFiltrosPagos.addEventListener('click', () => {
            if (inputSearchPagos) inputSearchPagos.value = '';
            estadoSeleccionado = '';
            botonesFiltroEstado.forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-filtro-estado') === '');
            });
            aplicarFiltrosPagos();
        });
    }

    // Botón restablecer dentro del estado vacío
    if (btnRestablecerLivePagos) {
        btnRestablecerLivePagos.addEventListener('click', () => {
            if (inputSearchPagos) inputSearchPagos.value = '';
            estadoSeleccionado = '';
            botonesFiltroEstado.forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-filtro-estado') === '');
            });
            aplicarFiltrosPagos();
        });
    }

    // Ejecutar filtro inicial si viene con parámetros
    if (inputSearchPagos && inputSearchPagos.value.trim() !== '') {
        aplicarFiltrosPagos();
    }
});
</script>
