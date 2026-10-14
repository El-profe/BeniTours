<?php
$totalPeriodo = array_sum(array_column($pagos, 'monto'));
$porCategoria = [];
foreach ($pagos as $pago) {
    $porCategoria[$pago['categoria']] = ($porCategoria[$pago['categoria']] ?? 0) + (float)$pago['monto'];
}
arsort($porCategoria);

$tabActivo = $filtros['tab'] ?? 'finanzas';
?>
<style>
.report-summary { background: linear-gradient(120deg, #0E4D5E, #2A9D8F); color: white; border-radius: 18px; }
.report-summary small { color: #DDF4F1; }
.report-bar { height: 9px; background: #E2F2F0; border-radius: 8px; overflow: hidden; }
.report-bar span { display: block; height: 100%; background: #2A9D8F; }
.report-kpi { color: #ffffff; border-radius: 16px; box-shadow: 0 10px 22px rgba(21, 42, 31, 0.12); transition: transform .2s ease, box-shadow .2s ease; }
.report-kpi:hover { transform: translateY(-3px); box-shadow: 0 15px 28px rgba(21, 42, 31, 0.18); }
.report-kpi-current { background: linear-gradient(135deg, #0E4D5E, #2A9D8F); }
.report-kpi-compare { background: linear-gradient(135deg, #1A6072, #46B7AA); }
.report-kpi-pending { background: linear-gradient(135deg, #C94B23, #FF8A5C); }
.report-kpi-lost { background: linear-gradient(135deg, #ad2139, #df4d64); }
.report-kpi-ticket { background: linear-gradient(135deg, #6940a2, #996bd1); }
.report-kpi-rate { background: linear-gradient(135deg, #087d99, #12abc9); }
.report-risk-card { background: linear-gradient(145deg, #ffffff, #fff5f6); border: 1px solid #f2d6db; border-radius: 18px; box-shadow: 0 4px 16px rgba(111, 30, 46, 0.08); }

/* KPI Cards Modernas estilo SaaS (limpias, con burbujas de icono y sin bordes gruesos toscos) */
.dash-metric-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}
.dash-metric-sub {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.74rem;
    color: #64748b;
    margin-top: 0.25rem;
}
.dash-icon-teal {
    background: rgba(42, 157, 143, 0.14);
    color: #217E73;
}

/* Pestañas */
.nav-reportes .nav-link {
    font-weight: 600;
    color: #495057;
    border-radius: 10px;
    padding: 10px 18px;
    border: 1px solid transparent;
    transition: all .2s ease;
}
.nav-reportes .nav-link.active {
    background-color: #2A9D8F;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(42, 157, 143, 0.25);
}
.nav-reportes .nav-link:hover:not(.active) {
    background-color: #E2F2F0;
    color: #217E73;
}

/* Tarjetas de Pioneros */
.pionero-card {
    border-radius: 16px;
    transition: transform .2s ease, box-shadow .2s ease;
    border: 1px solid rgba(0,0,0,0.06);
}
.pionero-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
.badge-pionero-oro { background: linear-gradient(135deg, #ffd700, #ffae00); color: #4a3500; font-weight: 700; }
.badge-pionero-plata { background: linear-gradient(135deg, #e0e0e0, #bdbdbd); color: #333333; font-weight: 700; }
.badge-pionero-bronce { background: linear-gradient(135deg, #e8a87c, #c38d9e); color: #3d2314; font-weight: 700; }

/* Presets de fechas */
.btn-preset {
    font-size: 0.76rem;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
}
.btn-preset.active {
    background-color: #2A9D8F;
    color: #fff;
    border-color: #2A9D8F;
}

/* Botones selector de período de tráfico */
.btn-periodo-visitas {
    font-size: 0.78rem;
    color: #495057;
    border: none;
    transition: all .2s ease;
}
.btn-periodo-visitas.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
}

/* Modal de Informe Individual */
.modal-header-comercio {
    background: linear-gradient(135deg, #0E4D5E, #2A9D8F);
    color: #ffffff;
}

/* Estilos de Impresión */
@media print {
    #adminSidebar, #adminTopbar, .sidebar-overlay, .report-actions, .report-filters, .nav-reportes, .no-print { display: none !important; }
    #adminContent { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    .tab-pane { display: block !important; opacity: 1 !important; visibility: visible !important; }
    .table-responsive { max-height: none !important; overflow: visible !important; }
    .admin-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; }
    .print-only-header { display: block !important; }
}
.print-only-header { display: none; }
</style>

<div class="container-fluid p-0">
    <!-- Encabezado Membrete Oficial para Impresión -->
    <div class="print-only-header mb-4 p-3 border-bottom text-center">
        <h2 class="fw-bold mb-1">BENITOURS TRINIDAD - GUÍA TURÍSTICA Y COMERCIAL</h2>
        <p class="text-muted small mb-1">Informe Analítico Oficial de Recaudación, Cobertura Comercial y Antigüedad</p>
        <small class="text-muted">Fecha de Emisión: <?= date('d/m/Y H:i') ?> | Sistema de Administración BeniTours</small>
    </div>

    <!-- Encabezado Superior en Pantalla -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Centro de Reportes y Analítica</h3>
            <p class="text-muted small mb-0">Consolidado de ingresos, cobertura de planes, padrón de antigüedad y tráfico turístico</p>
        </div>
        <div class="d-flex gap-2 report-actions flex-wrap">
            <!-- Menú de Exportaciones -->
            <div class="dropdown">
                <button class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar a Excel / CSV
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><h6 class="dropdown-header">Reportes Financieros</h6></li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="financiero" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=financiero">
                            <i class="bi bi-cash-stack text-success me-2"></i>Balance de Ingresos Filtrado
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="vigencias" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=vigencias">
                            <i class="bi bi-clock-history text-primary me-2"></i>Control de Vigencias
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="proyeccion" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=proyeccion">
                            <i class="bi bi-graph-up-arrow text-info me-2"></i>Proyección de Cobranzas (30-90d)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="rendimiento" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=rendimiento">
                            <i class="bi bi-bullseye text-danger me-2"></i>Ranking de Rendimiento y Conversión
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Directorio y Antigüedad</h6></li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="antiguedad" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=antiguedad">
                            <i class="bi bi-award text-warning me-2"></i>Ranking de Comercios por Antigüedad
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="lugares" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=lugares">
                            <i class="bi bi-geo-alt text-info me-2"></i>Padrón Completo de Lugares
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small" data-reporte-enlace data-reporte-accion="exportar" data-reporte-tipo="solicitudes" href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=solicitudes">
                            <i class="bi bi-inbox text-secondary me-2"></i>Histórico de Solicitudes
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Menú de impresión en Modal -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer me-1"></i> Imprimir Informe
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><h6 class="dropdown-header">Reportes Financieros</h6></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('financiero')"><i class="bi bi-cash-stack text-success me-2"></i>Balance de Ingresos Filtrado</a></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('vigencias')"><i class="bi bi-clock-history text-primary me-2"></i>Control de Vigencias</a></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('proyeccion')"><i class="bi bi-graph-up-arrow text-info me-2"></i>Proyección de Cobranzas (30-90d)</a></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('rendimiento')"><i class="bi bi-bullseye text-danger me-2"></i>Ranking de Rendimiento y Conversión</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">Directorio y Antigüedad</h6></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('antiguedad')"><i class="bi bi-award text-warning me-2"></i>Ranking de Comercios por Antigüedad</a></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('lugares')"><i class="bi bi-geo-alt text-info me-2"></i>Padrón Completo de Lugares</a></li>
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="abrirModalImprimir('solicitudes')"><i class="bi bi-inbox text-secondary me-2"></i>Histórico de Solicitudes</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 1. Cuadrícula de KPIs Globales (Diseño Moderno SaaS) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Ingresos Totales</span>
                    <div class="dash-metric-icon dash-icon-emerald">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    Bs <?= number_format($kpis['total_recaudado'], 2) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-success fw-semibold"><i class="bi bi-shield-check"></i> Auditado</span>
                    <span>Abonos confirmados</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Recaudación Mes Actual</span>
                    <div class="dash-metric-icon dash-icon-blue">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    Bs <?= number_format($kpis['total_mes'], 2) ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-primary fw-semibold"><i class="bi bi-graph-up-arrow"></i> Meta:</span>
                    <span>Bs <?= number_format($kpis['proyeccion_mes'], 2) ?></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Comercios al Día</span>
                    <div class="dash-metric-icon dash-icon-purple">
                        <i class="bi bi-shop-window"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= $kpis['comercios_activos'] ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Al día</span>
                    <span>Vigencia activa en catálogo</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-hero-metric <?= ($kpis['comercios_por_vencer'] > 0) ? 'border-warning shadow-xs' : '' ?>">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Por Vencer (7 días)</span>
                    <div class="dash-metric-icon dash-icon-amber">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark">
                    <?= $kpis['comercios_por_vencer'] ?>
                </div>
                <div class="dash-metric-sub">
                    <span class="text-warning-emphasis fw-semibold"><i class="bi bi-clock-history"></i> Renovar</span>
                    <span>Seguimiento de cobro</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Navegación por Pestañas (Tabs) -->
    <ul class="nav nav-pills nav-reportes gap-2 mb-4 p-2 bg-light rounded-4 border no-print" id="tabsReportes" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabActivo === 'finanzas' ? 'active' : '' ?>" id="tab-finanzas-btn" data-bs-toggle="pill" data-bs-target="#tab-finanzas" type="button" role="tab" onclick="cambiarPestana('finanzas')">
                <i class="bi bi-wallet2 me-1"></i> 1. Finanzas y Recaudación
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabActivo === 'vigencias' ? 'active' : '' ?>" id="tab-vigencias-btn" data-bs-toggle="pill" data-bs-target="#tab-vigencias" type="button" role="tab" onclick="cambiarPestana('vigencias')">
                <i class="bi bi-clock-history me-1"></i> 2. Control de Vigencias
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabActivo === 'comercios' ? 'active' : '' ?>" id="tab-comercios-btn" data-bs-toggle="pill" data-bs-target="#tab-comercios" type="button" role="tab" onclick="cambiarPestana('comercios')">
                <i class="bi bi-award-fill me-1 text-warning"></i> 3. Comercios & Antigüedad
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabActivo === 'visitas' ? 'active' : '' ?>" id="tab-visitas-btn" data-bs-toggle="pill" data-bs-target="#tab-visitas" type="button" role="tab" onclick="cambiarPestana('visitas')">
                <i class="bi bi-graph-up me-1"></i> 4. Tráfico Turístico
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $tabActivo === 'rendimiento' ? 'active' : '' ?>" id="tab-rendimiento-btn" data-bs-toggle="pill" data-bs-target="#tab-rendimiento" type="button" role="tab" onclick="cambiarPestana('rendimiento')">
                <i class="bi bi-bullseye me-1 text-danger"></i> 5. Rendimiento & Conversión
            </button>
        </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div id="buscadorReportes" class="input-group input-group-sm mb-3" style="max-width: 360px; display: none;">
        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
        <input type="search" id="buscadorVivo" class="form-control" value="<?= htmlspecialchars($filtros['buscar']) ?>" placeholder="Buscar en esta tabla..." autocomplete="off" aria-label="Buscar en la tabla">
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBuscador" title="Limpiar búsqueda" style="display: <?= !empty($filtros['buscar']) ? '' : 'none' ?>;"><i class="bi bi-x-circle"></i></button>
    </div>

    <div class="tab-content" id="tabsReportesContent">

        <!-- ========================================== -->
        <!-- PESTAÑA 1: FINANZAS Y RECAUDACIÓN          -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= $tabActivo === 'finanzas' ? 'show active' : '' ?>" id="tab-finanzas" role="tabpanel">
            <!-- Tarjetas Secundarias de Analítica Financiera -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-current p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">Mes actual</span>
                        <h5 class="fw-bold mt-2 mb-1">Bs <?= number_format($analiticaFinanciera['mes_actual'], 0) ?></h5>
                        <small class="opacity-75">Ingresos confirmados</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-compare p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">Vs. mes anterior</span>
                        <h5 class="fw-bold mt-2 mb-1">
                            <?php if ($analiticaFinanciera['variacion'] === null): ?>Sin base<?php else: ?><?= $analiticaFinanciera['variacion'] >= 0 ? '+' : '' ?><?= number_format($analiticaFinanciera['variacion'], 1) ?>%<?php endif; ?>
                        </h5>
                        <small class="opacity-75">Anterior: Bs <?= number_format($analiticaFinanciera['mes_anterior'], 0) ?></small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-pending p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">Por validar</span>
                        <h5 class="fw-bold mt-2 mb-1">Bs <?= number_format($analiticaFinanciera['pendiente_monto'], 0) ?></h5>
                        <small class="opacity-75"><?= $analiticaFinanciera['pendiente_cantidad'] ?> pago(s) pendiente(s)</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-lost p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">No concretado</span>
                        <h5 class="fw-bold mt-2 mb-1">Bs <?= number_format($analiticaFinanciera['anulado_monto'], 0) ?></h5>
                        <small class="opacity-75"><?= $analiticaFinanciera['anulado_cantidad'] ?> anulado(s)</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-ticket p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">Ticket promedio</span>
                        <h5 class="fw-bold mt-2 mb-1">Bs <?= number_format($analiticaFinanciera['ticket_promedio'], 0) ?></h5>
                        <small class="opacity-75">Por pago confirmado</small>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="report-kpi report-kpi-rate p-3 h-100">
                        <span class="small text-uppercase fw-bold opacity-75">Conversión</span>
                        <h5 class="fw-bold mt-2 mb-1"><?= number_format($analiticaFinanciera['tasa_confirmacion'], 1) ?>%</h5>
                        <small class="opacity-75">Operaciones confirmadas</small>
                    </div>
                </div>
            </div>

            <!-- Gráficos: Barras de evolución mensual + Dona de categorías -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-success me-2"></i>Comparación mensual de ingresos</h6>
                                <p class="small text-muted mb-0">Facturación confirmada y cantidad de pagos de los últimos 6 meses</p>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Histórico</span>
                        </div>
                        <div style="height: 270px; position: relative;"><canvas id="chartReporteIngresos"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Ingresos por Categoría</h6>
                            <span class="badge bg-light text-muted border">Total: Bs <?= number_format($graficoCategorias['total'], 0) ?></span>
                        </div>
                        <div style="height: 270px; position: relative;">
                            <canvas id="chartCategoriasDona"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros de Reportes (Compactos y Pegados sobre la tabla) -->
            <div class="admin-card p-3 mb-3 report-filters">
                <form id="formFiltrosReportes" onsubmit="event.preventDefault(); filtrarReportePagosEnVivo();">
                    <input type="hidden" name="tab" id="inputTabActivo" value="<?= htmlspecialchars($tabActivo) ?>">
                    <input type="hidden" name="buscar" id="buscarServidor" value="<?= htmlspecialchars($filtros['buscar']) ?>">

                    <!-- Fila Superior: Presets de Rango de Fecha -->
                    <div class="row g-1 align-items-center mb-2">
                        <div class="col-12">
                            <div class="d-flex gap-1 flex-wrap align-items-center">
                                <span class="extra-small text-muted fw-bold me-1">Rangos:</span>
                                <button type="button" onclick="aplicarPreset('hoy')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === 'hoy' ? 'active' : '' ?>">Hoy</button>
                                <button type="button" onclick="aplicarPreset('semana')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === 'semana' ? 'active' : '' ?>">Esta Semana</button>
                                <button type="button" onclick="aplicarPreset('mes')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === 'mes' ? 'active' : '' ?>">Este Mes</button>
                                <button type="button" onclick="aplicarPreset('mes_anterior')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === 'mes_anterior' ? 'active' : '' ?>">Mes Pasado</button>
                                <button type="button" onclick="aplicarPreset('90dias')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === '90dias' ? 'active' : '' ?>">Últimos 90d</button>
                                <button type="button" onclick="aplicarPreset('anio')" class="btn btn-outline-secondary btn-preset <?= $filtros['preset'] === 'anio' ? 'active' : '' ?>">Este Año</button>
                                <button type="button" onclick="aplicarPreset('todo')" class="btn btn-outline-secondary btn-preset <?= (empty($filtros['preset']) || $filtros['preset'] === 'todo') ? 'active' : '' ?>">Todo</button>
                            </div>
                        </div>
                    </div>

                    <!-- Fila Inferior: Rango de Fechas Específico, Categoría, Método de Pago y Estado -->
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-2">
                            <label class="form-label extra-small fw-bold text-muted mb-1">Desde:</label>
                            <input type="date" name="desde" id="fechaDesde" value="<?= htmlspecialchars($filtros['desde']) ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label extra-small fw-bold text-muted mb-1">Hasta:</label>
                            <input type="date" name="hasta" id="fechaHasta" value="<?= htmlspecialchars($filtros['hasta']) ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label extra-small fw-bold text-muted mb-1">Categoría:</label>
                            <select name="id_categoria" id="filtroCategoria" class="form-select form-select-sm">
                                <option value="0">Todas las categorías</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= (int)$cat['id_categoria'] ?>" <?= (int)$filtros['categoria'] === (int)$cat['id_categoria'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label extra-small fw-bold text-muted mb-1">Método de Pago:</label>
                            <select name="metodo" id="filtroMetodo" class="form-select form-select-sm">
                                <option value="">Todos los métodos</option>
                                <?php foreach ($metodosDisponibles as $m): ?>
                                    <option value="<?= htmlspecialchars($m) ?>" <?= $filtros['metodo'] === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label extra-small fw-bold text-muted mb-1">Estado Vigencia:</label>
                            <select name="estado_vigencia" id="filtroEstadoVigencia" class="form-select form-select-sm">
                                <option value="">Todos los estados</option>
                                <option value="VIGENTE" <?= $filtros['estado_vigencia'] === 'VIGENTE' ? 'selected' : '' ?>>Vigente / Activo</option>
                                <option value="POR_VENCER" <?= $filtros['estado_vigencia'] === 'POR_VENCER' ? 'selected' : '' ?>>Por Vencer (≤7d)</option>
                                <option value="VENCIDO" <?= $filtros['estado_vigencia'] === 'VENCIDO' ? 'selected' : '' ?>>Vencido / En Mora</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-1 d-flex gap-1">
                            <button type="button" id="btnResetFiltrosReportes" class="btn btn-light border btn-sm text-muted w-100" title="Limpiar todos los filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="preset" id="inputPreset" value="<?= htmlspecialchars($filtros['preset']) ?>">
                </form>
            </div>

            <!-- Tabla de Transacciones y Abonos Confirmados -->
            <div class="admin-card mb-4 overflow-hidden">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-success me-2"></i>Abonos y Transacciones Confirmadas</h6>
                        <small class="text-muted">Mostrando pagos según el rango y filtros aplicados</small>
                    </div>
                    <div class="reporte-search-slot" data-tab="finanzas"></div>
                    <span class="badge bg-light text-dark border px-3 py-2"><span id="conteoPagos"><?= count($pagos) ?></span> pagos registrados</span>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table admin-table table-hover table-sm align-middle mb-0" id="tablaPagos">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Establecimiento</th>
                                <th>Categoría</th>
                                <th>Fecha Pago</th>
                                <th>Método</th>
                                <th>N° Comprobante</th>
                                <th class="text-end">Importe (Bs)</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pagos)): ?>
                                <?php foreach ($pagos as $p): ?>
                                    <tr class="fila-buscable" 
                                        data-texto="<?= strtolower(htmlspecialchars($p['establecimiento'] . ' ' . $p['categoria'] . ' ' . $p['numero_comprobante'] . ' ' . $p['metodo_pago'])) ?>"
                                        data-categoria="<?= (int)($p['id_categoria'] ?? 0) ?>"
                                        data-fecha="<?= date('Y-m-d', strtotime($p['fecha_reporte'])) ?>"
                                        data-metodo="<?= htmlspecialchars($p['metodo_pago'] ?? '') ?>"
                                        data-estado="<?= htmlspecialchars($p['estado_vigencia'] ?? '') ?>"
                                        data-monto="<?= (float)$p['monto'] ?>">
                                        <td><code class="text-muted small">#<?= (int)$p['id_pago'] ?></code></td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); abrirInformeComercio(<?= (int)$p['id_lugar'] ?>)" class="text-dark fw-bold text-decoration-none hover-primary small d-block">
                                                <?= htmlspecialchars($p['establecimiento']) ?>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark border extra-small"><?= htmlspecialchars($p['categoria']) ?></span></td>
                                        <td class="small text-muted"><?= date('d/m/Y', strtotime($p['fecha_reporte'])) ?></td>
                                        <td class="small"><i class="bi bi-credit-card-2-front text-muted me-1"></i><?= htmlspecialchars($p['metodo_pago']) ?></td>
                                        <td><code class="extra-small text-muted"><?= htmlspecialchars($p['numero_comprobante'] ?: 'S/N') ?></code></td>
                                        <td class="text-end fw-bold text-success small">Bs <?= number_format($p['monto'], 2) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" onclick="abrirInformeComercio(<?= (int)$p['id_lugar'] ?>)" class="btn btn-outline-primary btn-sm py-0 px-2" title="Ver informe y estado de cuenta del comercio">
                                                    <i class="bi bi-file-earmark-person"></i> Ficha
                                                </button>
                                                <?php if (!empty($p['comprobante_archivo'])): ?>
                                                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/comprobante?id=<?= (int)$p['id_pago'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Ver recibo de pago">
                                                        <i class="bi bi-image"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="filaSinPagos">
                                    <td colspan="8" class="text-center py-4 text-muted small">No se encontraron pagos con los filtros seleccionados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PESTAÑA 2: CONTROL DE VIGENCIAS Y COBRO    -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= $tabActivo === 'vigencias' ? 'show active' : '' ?>" id="tab-vigencias" role="tabpanel">
            <!-- Proyección Escalonada de Flujo y Retención de Cartera (SaaS) -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Retención Activa</span>
                            <div class="dash-metric-icon dash-icon-emerald">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($kpis['tasa_retencion'], 1) ?>%</div>
                        <div class="dash-metric-sub">
                            <span class="text-success fw-semibold"><i class="bi bi-check2"></i> Cartera</span>
                            <span>Al día</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Llamadas y Menú</span>
                            <div class="dash-metric-icon dash-icon-blue">
                                <i class="bi bi-telephone-outbound"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($totalesTelemetria['total_telefono'] + $totalesTelemetria['total_menu']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-primary fw-semibold"><i class="bi bi-activity"></i> Contactos</span>
                            <span>Directos</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Promociones</span>
                            <div class="dash-metric-icon dash-icon-rose">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($totalesTelemetria['total_promociones']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-danger fw-semibold"><i class="bi bi-eye"></i> Aperturas</span>
                            <span>Ofertas</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">0 - 30 Días</span>
                            <div class="dash-metric-icon dash-icon-cyan">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark">Bs <?= number_format($kpis['proyeccion_30d_monto'], 0) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-info fw-semibold"><?= $kpis['proyeccion_30d_cant'] ?> renov.</span>
                            <span>Estimadas</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">31 - 60 Días</span>
                            <div class="dash-metric-icon dash-icon-purple">
                                <i class="bi bi-calendar2-range"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark">Bs <?= number_format($kpis['proyeccion_60d_monto'], 0) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-purple fw-semibold"><?= $kpis['proyeccion_60d_cant'] ?> renov.</span>
                            <span>Mediano plazo</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">61 - 90 Días</span>
                            <div class="dash-metric-icon dash-icon-amber">
                                <i class="bi bi-calendar3"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark">Bs <?= number_format($kpis['proyeccion_90d_monto'], 0) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-warning-emphasis fw-semibold"><?= $kpis['proyeccion_90d_cant'] ?> renov.</span>
                            <span>Trimestral</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card mb-4 overflow-hidden">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Monitoreo de Suscripciones y Vencimientos</h6>
                        <small class="text-muted">Estado actual de la membresía comercial de cada establecimiento</small>
                    </div>
                    <div class="reporte-search-slot" data-tab="vigencias"></div>
                    <span class="badge bg-light text-dark border px-3 py-2"><span id="conteoVigencias"><?= count($vigencias) ?></span> fichas activas</span>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table admin-table table-hover table-sm align-middle mb-0" id="tablaVigencias">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Comercio</th>
                                <th>Categoría</th>
                                <th>Fecha Inicio</th>
                                <th>Vencimiento</th>
                                <th>Días Restantes</th>
                                <th>Estado</th>
                                <th class="text-end">Recordatorio & Cobranza</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vigencias)): ?>
                                <?php foreach ($vigencias as $v): ?>
                                    <?php
                                        $telWa = preg_replace('/\D/', '', $v['whatsapp_contacto'] ?: ($v['telefono_contacto'] ?: ''));
                                        if (strlen($telWa) === 8) $telWa = '591' . $telWa;
                                        $msg = "Hola *" . $v['establecimiento'] . "*, le saludamos de BeniTours Trinidad. Le recordamos que su membresía comercial vence el " . date('d/m/Y', strtotime($v['fecha_vencimiento'])) . ". ¿Desea renovar?";
                                        $waLink = !empty($telWa) ? "https://wa.me/{$telWa}?text=" . rawurlencode($msg) : null;
                                    ?>
                                    <tr class="fila-buscable" data-texto="<?= strtolower(htmlspecialchars($v['establecimiento'] . ' ' . $v['categoria'] . ' ' . $v['estado_cobertura'])) ?>">
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); abrirInformeComercio(<?= (int)$v['id_lugar'] ?>)" class="text-dark fw-bold text-decoration-none hover-primary small d-block">
                                                <?= htmlspecialchars($v['establecimiento']) ?>
                                            </a>
                                            <small class="text-muted extra-small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($v['telefono_contacto'] ?: 'S/N') ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-muted border extra-small"><?= htmlspecialchars($v['categoria']) ?></span></td>
                                        <td class="small text-muted"><?= date('d/m/Y', strtotime($v['fecha_inicio'])) ?></td>
                                        <td class="small fw-semibold"><?= date('d/m/Y', strtotime($v['fecha_vencimiento'])) ?></td>
                                        <td class="small fw-semibold <?= $v['dias_restantes'] <= 5 ? 'text-danger' : 'text-dark' ?>">
                                            <?= $v['dias_restantes'] >= 0 ? $v['dias_restantes'] . ' días' : 'Vencido hace ' . abs($v['dias_restantes']) . 'd' ?>
                                        </td>
                                        <td>
                                            <?php if ($v['estado_cobertura'] === 'VIGENTE'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                            <?php elseif ($v['estado_cobertura'] === 'POR_VENCER'): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill extra-small"><i class="bi bi-exclamation-triangle me-1"></i>Por Vencer</span>
                                            <?php elseif ($v['estado_cobertura'] === 'PROGRAMADA'): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill extra-small">Programada</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small"><i class="bi bi-x-circle me-1"></i>Vencido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" onclick="abrirInformeComercio(<?= (int)$v['id_lugar'] ?>)" class="btn btn-outline-primary btn-sm rounded-circle p-1" title="Ver informe del comercio" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-file-earmark-person extra-small"></i>
                                                </button>
                                                <?php if ($waLink): ?>
                                                    <a href="<?= htmlspecialchars($waLink) ?>" target="_blank" rel="noopener noreferrer"
                                                       class="btn btn-outline-success btn-sm rounded-circle p-1"
                                                       title="Enviar recordatorio por WhatsApp" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-whatsapp extra-small"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/crear?id_lugar=<?= $v['id_lugar'] ?>"
                                                   class="btn btn-outline-dark btn-sm rounded-circle p-1"
                                                   title="Registrar cobro" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-cash-stack extra-small"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted small">No se encontraron vigencias registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- PESTAÑA 3: DIRECTORIO DE COMERCIOS & RANKING ANTIGÜEDAD  -->
        <!-- ======================================================== -->
        <div class="tab-pane fade <?= $tabActivo === 'comercios' ? 'show active' : '' ?>" id="tab-comercios" role="tabpanel">
            
            <!-- Encabezado del Módulo de Antigüedad -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-award-fill text-warning me-2"></i>Ranking de Antigüedad y Trayectoria de Comercios</h5>
                    <p class="text-muted small mb-0">Ordenados cronológicamente desde los primeros comercios pioneros que se unieron a BeniTours Trinidad</p>
                </div>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-3 py-2">
                    <?= count($comerciosAntiguedad) ?> Comercios Registrados
                </span>
            </div>

            <!-- Podio de Comercios Pioneros (Top 3) -->
            <?php if (!empty($comerciosAntiguedad)): ?>
                <div class="row g-3 mb-4">
                    <?php 
                    $pioneros = array_slice($comerciosAntiguedad, 0, 3);
                    $medallas = [
                        1 => ['badge' => 'badge-pionero-oro', 'icono' => '🥇', 'titulo' => 'Comercio Fundador #1'],
                        2 => ['badge' => 'badge-pionero-plata', 'icono' => '🥈', 'titulo' => 'Comercio Pionero #2'],
                        3 => ['badge' => 'badge-pionero-bronce', 'icono' => '🥉', 'titulo' => 'Comercio Pionero #3']
                    ];
                    foreach ($pioneros as $p): 
                        $med = $medallas[$p['ranking']] ?? $medallas[3];
                    ?>
                        <div class="col-md-4">
                            <div class="admin-card p-3 p-xl-4 h-100 pionero-card bg-white position-relative overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge <?= $med['badge'] ?> px-2 py-1 rounded-pill">
                                        <?= $med['icono'] ?> <?= $med['titulo'] ?>
                                    </span>
                                    <span class="extra-small text-muted fw-bold"><?= $p['antiguedad_texto'] ?></span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 mt-2"><?= htmlspecialchars($p['establecimiento']) ?></h6>
                                <p class="text-muted extra-small mb-3"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($p['categoria']) ?></p>

                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2 extra-small text-muted">
                                    <span>Registro: <strong><?= date('d/m/Y', strtotime($p['fecha_registro'])) ?></strong></span>
                                    <span>Invertido: <strong class="text-success">Bs <?= number_format($p['total_aportado'], 2) ?></strong></span>
                                </div>

                                <div class="mt-3">
                                    <button type="button" onclick="abrirInformeComercio(<?= (int)$p['id_lugar'] ?>)" class="btn btn-outline-dark btn-sm w-100 rounded-pill py-1">
                                        <i class="bi bi-file-earmark-person me-1"></i> Ver Informe Completo
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Tabla General de Comercios por Antigüedad -->
            <div class="admin-card mb-4 overflow-hidden">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-ol text-primary me-2"></i>Padrón Cronológico de Comercios</h6>
                        <small class="text-muted">Listado completo ordenado por fecha de antigüedad en la plataforma</small>
                    </div>
                    <div class="reporte-search-slot" data-tab="comercios"></div>
                    <span class="badge bg-light text-dark border px-3 py-2"><span id="conteoAntiguedad"><?= count($comerciosAntiguedad) ?></span> establecimientos</span>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table admin-table table-hover table-sm align-middle mb-0" id="tablaAntiguedad">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Establecimiento</th>
                                <th>Categoría</th>
                                <th>Fecha Registro</th>
                                <th>Antigüedad</th>
                                <th class="text-center">Pagos</th>
                                <th class="text-end">Total Aportado (Bs)</th>
                                <th>Estado Membresía</th>
                                <th class="text-center">Informe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($comerciosAntiguedad)): ?>
                                <?php foreach ($comerciosAntiguedad as $c): ?>
                                    <tr class="fila-buscable" data-texto="<?= strtolower(htmlspecialchars($c['establecimiento'] . ' ' . $c['categoria'] . ' ' . $c['estado_suscripcion'])) ?>">
                                        <td>
                                            <?php if ($c['ranking'] === 1): ?>
                                                <span class="badge bg-warning text-dark fw-bold rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                            <?php elseif ($c['ranking'] === 2): ?>
                                                <span class="badge bg-secondary text-white fw-bold rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                            <?php elseif ($c['ranking'] === 3): ?>
                                                <span class="badge bg-dark text-white fw-bold rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                            <?php else: ?>
                                                <span class="text-muted small fw-bold">#<?= $c['ranking'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); abrirInformeComercio(<?= (int)$c['id_lugar'] ?>)" class="text-dark fw-bold text-decoration-none hover-primary small d-block">
                                                <?= htmlspecialchars($c['establecimiento']) ?>
                                            </a>
                                            <small class="text-muted extra-small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['telefono_contacto'] ?: ($c['whatsapp_contacto'] ?: 'S/N')) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border extra-small"><?= htmlspecialchars($c['categoria']) ?></span></td>
                                        <td class="small text-muted"><?= date('d/m/Y', strtotime($c['fecha_registro'])) ?></td>
                                        <td>
                                            <span class="badge bg-light text-primary border extra-small fw-semibold">
                                                <i class="bi bi-hourglass-split me-1"></i><?= htmlspecialchars($c['antiguedad_texto']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center small fw-semibold"><?= (int)$c['total_pagos_confirmados'] ?></td>
                                        <td class="text-end fw-bold text-success small">Bs <?= number_format($c['total_aportado'], 2) ?></td>
                                        <td>
                                            <?php if ($c['estado_suscripcion'] === 'VIGENTE'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small">Al día</span>
                                            <?php elseif ($c['estado_suscripcion'] === 'POR_VENCER'): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill extra-small">Por Vencer</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small">Vencido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" onclick="abrirInformeComercio(<?= (int)$c['id_lugar'] ?>)" class="btn btn-outline-primary btn-sm py-1 px-2 rounded-pill extra-small" title="Ver ficha e historial de pagos de este comercio">
                                                <i class="bi bi-file-earmark-person me-1"></i> Ficha
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted small">No se encontraron comercios en el padrón.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PESTAÑA 4: TRÁFICO TURÍSTICO Y VISITAS     -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= $tabActivo === 'visitas' ? 'show active' : '' ?>" id="tab-visitas" role="tabpanel">
            <!-- 1. Cuadrícula de KPIs de Audiencia Turística (Diseño Moderno SaaS) -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Visitantes Hoy</span>
                            <div class="dash-metric-icon dash-icon-blue">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($analiticaVisitas['resumen']['hoy']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-primary fw-semibold"><i class="bi bi-broadcast"></i> En vivo</span>
                            <span>Usuarios únicos del día</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Últimos 7 Días</span>
                            <div class="dash-metric-icon dash-icon-emerald">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($analiticaVisitas['resumen']['semana']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-success fw-semibold"><i class="bi bi-graph-up-arrow"></i> Semanal</span>
                            <span>Audiencia de la semana</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Este Mes</span>
                            <div class="dash-metric-icon dash-icon-purple">
                                <i class="bi bi-calendar-month-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($analiticaVisitas['resumen']['mes']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-purple fw-semibold"><i class="bi bi-calendar-check"></i> Mes</span>
                            <span>Acumulado <?= date('m/Y') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Total Histórico</span>
                            <div class="dash-metric-icon dash-icon-amber">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($analiticaVisitas['resumen']['total']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-warning-emphasis fw-semibold"><i class="bi bi-globe-americas"></i> Total</span>
                            <span>Visitas registradas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horario Pico y Día Más Concurrido -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="admin-card p-3 h-100 d-flex align-items-center gap-3 bg-white border">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold">Horario de Mayor Tráfico (Hora Pico)</span>
                            <h6 class="fw-bold text-dark mb-0 mt-1"><?= htmlspecialchars($analiticaVisitas['hora_pico']) ?></h6>
                            <small class="text-muted extra-small">Franja horaria con mayor número de consultas turísticas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-card p-3 h-100 d-flex align-items-center gap-3 bg-white border">
                        <div class="rounded-circle bg-success-subtle text-success p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold">Día con Mayor Afluencia</span>
                            <h6 class="fw-bold text-dark mb-0 mt-1"><?= htmlspecialchars($analiticaVisitas['dia_pico']) ?></h6>
                            <small class="text-muted extra-small">Día de la semana preferido por los visitantes</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Cuadrícula de 4 Gráficos Interactivos de Tráfico Turístico -->
            <div class="row g-4 mb-4">
                <!-- Gráfico 1: Visitantes por Día -->
                <div class="col-lg-6">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-calendar-date text-primary me-2"></i>Visitantes por Día (Últimos 14 Días)</h6>
                                <p class="extra-small text-muted mb-0">Evolución diaria y comportamiento de la audiencia turística</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                <?= array_sum($analiticaVisitas['dias']['valores']) ?> visitas
                            </span>
                        </div>
                        <div style="height: 270px; position: relative;">
                            <canvas id="chartVisitasDias"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico 2: Visitantes por Horas -->
                <div class="col-lg-6">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-clock text-info me-2"></i>Distribución por Horas (24 Horas)</h6>
                                <p class="extra-small text-muted mb-0">Franjas horarias del día con mayor actividad en la guía</p>
                            </div>
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1">
                                Pico: <?= htmlspecialchars($analiticaVisitas['hora_pico']) ?>
                            </span>
                        </div>
                        <div style="height: 270px; position: relative;">
                            <canvas id="chartVisitasHoras"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico 3: Visitantes por Semana -->
                <div class="col-lg-6">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-calendar-range text-success me-2"></i>Afluencia por Semana (Últimas 8 Semanas)</h6>
                                <p class="extra-small text-muted mb-0">Consolidado y ritmo semanal de consultas turísticas</p>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                <?= array_sum($analiticaVisitas['semanas']['valores']) ?> visitas
                            </span>
                        </div>
                        <div style="height: 270px; position: relative;">
                            <canvas id="chartVisitasSemanas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico 4: Visitantes por Meses -->
                <div class="col-lg-6">
                    <div class="admin-card p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-calendar3 me-2" style="color: #6f42c1;"></i>Evolución por Meses (Últimos 6 Meses)</h6>
                                <p class="extra-small text-muted mb-0">Crecimiento y estacionalidad turística en Trinidad</p>
                            </div>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                <?= array_sum($analiticaVisitas['meses']['valores']) ?> visitas
                            </span>
                        </div>
                        <div style="height: 270px; position: relative;">
                            <canvas id="chartVisitasMeses"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- PESTAÑA 5: RENDIMIENTO TURÍSTICO Y CONVERSIONES COMERCIALES -->
        <!-- ======================================================== -->
        <div class="tab-pane fade <?= $tabActivo === 'rendimiento' ? 'show active' : '' ?>" id="tab-rendimiento" role="tabpanel">
            <!-- 1. Cuadrícula de KPIs Globales de Conversión (Diseño Moderno SaaS) -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Vistas de Fichas</span>
                            <div class="dash-metric-icon dash-icon-blue">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($totalesTelemetria['total_vistas']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-primary fw-semibold"><i class="bi bi-compass"></i> Interés</span>
                            <span>Destinos del Beni</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">WhatsApp</span>
                            <div class="dash-metric-icon dash-icon-emerald">
                                <i class="bi bi-whatsapp"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($totalesTelemetria['total_whatsapp']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-success fw-semibold"><i class="bi bi-chat-dots-fill"></i> Contactos</span>
                            <span>Consultas directas</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Rutas Maps</span>
                            <div class="dash-metric-icon dash-icon-rose">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= number_format($totalesTelemetria['total_mapa']) ?></div>
                        <div class="dash-metric-sub">
                            <span class="text-danger fw-semibold"><i class="bi bi-signpost-2-fill"></i> Cómo llegar</span>
                            <span>Trazaron ruta</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dash-hero-metric">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="dash-metric-title">Conversión Global</span>
                            <div class="dash-metric-icon dash-icon-amber">
                                <i class="bi bi-pie-chart-fill"></i>
                            </div>
                        </div>
                        <div class="dash-metric-number text-dark"><?= $totalesTelemetria['tasa_conversion_gral'] ?>%</div>
                        <div class="dash-metric-sub">
                            <span class="text-warning-emphasis fw-semibold"><i class="bi bi-funnel-fill"></i> Ratio</span>
                            <span>De visita a contacto</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Tabla del Ranking de Popularidad y Conversiones -->
            <div class="admin-card mb-4 overflow-hidden">
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bullseye text-danger me-2"></i>Ranking de Comercios con Mayor Conversión Turística</h6>
                        <small class="text-muted">Establecimientos que más consultas por WhatsApp y rutas en mapa generan en la plataforma</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-none fw-semibold d-inline-flex align-items-center gap-1" onclick="abrirModalGraficosRendimiento()">
                            <i class="bi bi-bar-chart-line-fill text-danger"></i> Ver Gráficos
                        </button>
                        <span class="badge bg-light text-dark border px-3 py-2"><?= count($rankingRendimiento) ?> comercios analizados</span>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table admin-table table-hover table-sm align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th># Puesto</th>
                                <th>Establecimiento</th>
                                <th>Categoría</th>
                                <th class="text-center">Vistas de Ficha</th>
                                <th class="text-center text-success"><i class="bi bi-whatsapp me-1"></i> WhatsApp</th>
                                <th class="text-center text-danger"><i class="bi bi-geo-alt-fill me-1"></i> Rutas Mapa</th>
                                <th class="text-center text-danger-emphasis"><i class="bi bi-heart-fill me-1"></i> En Favoritos</th>
                                <th class="text-end fw-bold">Total Conversiones</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rankingRendimiento)): ?>
                                <?php $posRanking = 1; foreach ($rankingRendimiento as $r): ?>
                                    <tr>
                                        <td>
                                            <?php if ($posRanking === 1): ?>
                                                <span class="badge bg-warning text-dark fw-bold rounded-pill px-2">🥇 #1</span>
                                            <?php elseif ($posRanking === 2): ?>
                                                <span class="badge bg-secondary text-white fw-bold rounded-pill px-2">🥈 #2</span>
                                            <?php elseif ($posRanking === 3): ?>
                                                <span class="badge bg-danger-subtle text-danger-emphasis fw-bold rounded-pill px-2">🥉 #3</span>
                                            <?php else: ?>
                                                <code class="text-muted small">#<?= $posRanking ?></code>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); abrirInformeComercio(<?= (int)$r['id_lugar'] ?>)" class="text-dark fw-bold text-decoration-none hover-primary small d-block">
                                                <?= htmlspecialchars($r['establecimiento']) ?>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark border extra-small"><?= htmlspecialchars($r['categoria']) ?></span></td>
                                        <td class="text-center small"><?= number_format((int)$r['vistas']) ?></td>
                                        <td class="text-center small fw-semibold text-success"><?= number_format((int)$r['contactos_whatsapp']) ?></td>
                                        <td class="text-center small fw-semibold text-danger"><?= number_format((int)$r['rutas_mapa']) ?></td>
                                        <td class="text-center small text-muted"><?= number_format((int)$r['guardado_favoritos']) ?></td>
                                        <td class="text-end fw-bold text-primary small">
                                            <?= number_format((int)$r['total_conversiones']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" onclick="abrirInformeComercio(<?= (int)$r['id_lugar'] ?>)" class="btn btn-outline-primary btn-sm py-0 px-2" title="Ver ficha y expediente">
                                                    <i class="bi bi-file-earmark-person"></i> Ficha
                                                </button>
                                                <a href="<?= htmlspecialchars($baseUrl) ?>/catalogo/detalle?slug=<?= urlencode($r['slug']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Abrir en catálogo público">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $posRanking++; endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted small">No hay interacciones registradas en este período.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE INFORME INDIVIDUAL DE COMERCIO (ESTADO DE CUENTA)-->
<!-- ======================================================== -->
<div class="modal fade" id="modalInformeComercio" tabindex="-1" aria-labelledby="modalInformeTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header modal-header-comercio py-3">
                <div>
                    <span class="badge bg-white text-success extra-small fw-bold mb-1" id="mCategoria">Comercio</span>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="mNombre">Cargando datos...</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-4" id="modalContenidoComercio">
                <div id="mSpinner" class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted small mt-2">Obteniendo expediente completo del comercio...</p>
                </div>

                <div id="mDetalles" style="display: none;">
                    <!-- Tarjeta de Identificación y Antigüedad -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <div class="p-3 bg-light rounded-3 h-100 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1 text-primary"></i>Datos del Establecimiento</h6>
                                <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>Dirección: <strong class="text-dark" id="mDireccion">--</strong></p>
                                <p class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>Teléfono: <strong class="text-dark" id="mTelefono">--</strong></p>
                                <p class="small text-muted mb-1"><i class="bi bi-whatsapp me-1 text-success"></i>WhatsApp: <strong class="text-dark" id="mWhatsapp">--</strong></p>
                                <p class="small text-muted mb-0"><i class="bi bi-calendar-event me-1"></i>Fecha de Afiliación: <strong class="text-dark" id="mFechaRegistro">--</strong></p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-3 bg-success-subtle rounded-3 h-100 border border-success-subtle">
                                <span class="badge bg-success text-white extra-small mb-1">Trayectoria en BeniTours</span>
                                <h4 class="fw-bold text-success mb-1" id="mAntiguedad">--</h4>
                                <small class="text-muted d-block mb-2">Total aportado acumulado:</small>
                                <h4 class="fw-bold text-dark mb-0">Bs <span id="mTotalAportado">0.00</span></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas de Cobertura Actual -->
                    <div class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-3 border bg-white flex-wrap gap-2">
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold d-block">Estado de Cobertura Actual</span>
                            <span id="mBadgeEstado" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">VIGENTE</span>
                        </div>
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold d-block">Vigencia hasta</span>
                            <strong class="text-dark" id="mVigenciaHasta">--</strong>
                        </div>
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold d-block">Pagos Confirmados</span>
                            <strong class="text-dark" id="mPagosConfirmados">0</strong>
                        </div>
                        <div>
                            <span class="extra-small text-muted text-uppercase fw-bold d-block">Ticket Promedio</span>
                            <strong class="text-dark">Bs <span id="mTicketPromedio">0.00</span></strong>
                        </div>
                    </div>

                    <!-- Tabla de Historial Cronológico de Pagos -->
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-receipt-cutoff me-1 text-success"></i>Historial de Pagos y Comprobantes</h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha Declarada</th>
                                    <th>Importe</th>
                                    <th>Método</th>
                                    <th>Comprobante</th>
                                    <th>Estado</th>
                                    <th>Confirmación</th>
                                </tr>
                            </thead>
                            <tbody id="mCuerpoPagos">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="mBtnExportar" class="btn btn-outline-success btn-sm rounded-pill">
                    <i class="bi bi-file-earmark-excel me-1"></i> Exportar Historial CSV
                </a>
                <button type="button" class="btn btn-primary btn-sm rounded-pill" onclick="imprimirEstadoCuentaModal()">
                    <i class="bi bi-printer me-1"></i> Imprimir Ficha / Estado de Cuenta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE ANÁLISIS GRÁFICO DE CONVERSIONES Y POPULARIDAD  -->
<!-- ======================================================== -->
<div class="modal fade" id="modalGraficosRanking" tabindex="-1" aria-labelledby="modalGraficosRankingTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header py-3" style="background: linear-gradient(135deg, #881337, #e11d48); color: #ffffff;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white text-danger d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 1.15rem;">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalGraficosRankingTitulo">Gráficos de Conversión y Popularidad de Comercios</h5>
                        <small class="text-white-50">Comparativa visual de interacciones turísticas generadas en Trinidad</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <!-- Gráfico 1: Top 10 Comercios por Conversiones Totales -->
                    <div class="col-lg-7">
                        <div class="bg-white p-3 p-md-4 rounded-4 border shadow-xs h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 10 Comercios con Más Conversiones</h6>
                                    <p class="extra-small text-muted mb-0">Suma total de WhatsApp + Rutas en Mapa</p>
                                </div>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Líderes</span>
                            </div>
                            <div style="height: 320px; position: relative;">
                                <canvas id="chartRankingConversiones"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 2: Distribución por Canal de Interacción -->
                    <div class="col-lg-5">
                        <div class="bg-white p-3 p-md-4 rounded-4 border shadow-xs h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Canales de Contacto</h6>
                                    <p class="extra-small text-muted mb-0">Distribución de contactos WhatsApp vs. Rutas Maps</p>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1">Canales</span>
                            </div>
                            <div style="height: 320px; position: relative;">
                                <canvas id="chartCanalesDona"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 3: Comparativa Multivariable (Vistas vs Conversiones vs Favoritos) -->
                    <div class="col-12">
                        <div class="bg-white p-3 p-md-4 rounded-4 border shadow-xs">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-bar-chart-steps text-success me-2"></i>Embudo de Atracción: Vistas vs. Contactos Reales</h6>
                                    <p class="extra-small text-muted mb-0">Compara la visibilidad que recibe la ficha vs cuántos turistas decidieron contactarse</p>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Interacción</span>
                            </div>
                            <div style="height: 300px; position: relative;">
                                <canvas id="chartEmbudoAtraccion"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE VISTA PREVIA Y REPORTE PARA IMPRESIÓN OFICIAL   -->
<!-- ======================================================== -->
<div class="modal fade" id="modalVistaPreviaImpresion" tabindex="-1" aria-labelledby="modalImprimirTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="modal-header py-3" style="background: linear-gradient(135deg, #0E4D5E, #2A9D8F); color: #ffffff;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="bi bi-printer-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalImprimirTitulo">Cargando reporte...</h5>
                        <small class="text-white-50" id="modalImprimirSubtitulo">Vista previa del informe analítico oficial</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body p-4 bg-light" id="modalImprimirAreaDocumento">
                <!-- Spinner de carga -->
                <div id="modalImprimirSpinner" class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Generando informe...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Recopilando datos para el informe oficial...</p>
                </div>

                <!-- Documento Formato Hoja Oficial -->
                <div id="modalImprimirContenido" style="display: none;" class="bg-white p-4 p-md-5 rounded-4 border shadow-sm mx-auto" style="max-width: 950px;">
                    <!-- Membrete Oficial -->
                    <div class="text-center pb-3 mb-4 border-bottom">
                        <h3 class="fw-bold mb-1 text-dark tracking-wide">BENITOURS TRINIDAD</h3>
                        <p class="text-muted small mb-1 fw-semibold text-uppercase" id="docReporteTitulo">INFORME OFICIAL</p>
                        <div class="d-flex justify-content-center gap-3 text-muted extra-small">
                            <span><i class="bi bi-calendar3 me-1"></i>Fecha de Emisión: <strong id="docReporteFecha">--</strong></span>
                            <span>&bull;</span>
                            <span><i class="bi bi-shield-check me-1"></i>Sistema de Administración BeniTours</span>
                        </div>
                    </div>

                    <!-- Tabla de Resultados -->
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered align-middle mb-0" id="docReporteTabla">
                            <thead class="table-light" id="docReporteThead">
                                <!-- Cabeceras dinámicas -->
                            </thead>
                            <tbody id="docReporteTbody">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie del Informe -->
                    <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted extra-small">
                        <span>Total de registros listados: <strong id="docReporteTotal" class="text-dark">0</strong></span>
                        <span>Trinidad, Beni &mdash; Guía Oficial de Turismo y Comercio</span>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer bg-white py-2 justify-content-between">
                <span class="text-muted extra-small d-none d-sm-inline">
                    <i class="bi bi-info-circle me-1"></i>Puedes revisar el informe antes de imprimirlo en papel o guardarlo en PDF.
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-xs" id="btnEjecutarImpresionDoc" onclick="ejecutarImpresionDoc()">
                        <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // El buscador debe funcionar aunque un gráfico no pueda cargarse.
    const buscador = document.getElementById('buscadorVivo');
    const contenedorBuscador = document.getElementById('buscadorReportes');
    const botonLimpiar = document.getElementById('btnLimpiarBuscador');
    const buscarServidor = document.getElementById('buscarServidor');

    // Los filtros se aplican sin recargar la página. Al abrir un CSV o la
    // versión imprimible, generar la URL en ese instante evita exportar datos
    // distintos a los que el administrador tiene seleccionados en pantalla.
    const actualizarUrlReporte = (enlace) => {
        const accion = enlace.dataset.reporteAccion;
        const tipo = enlace.dataset.reporteTipo;
        if (!accion || !tipo) return;

        const params = new URLSearchParams({ tipo });
        const valores = {
            desde: document.getElementById('fechaDesde')?.value || '',
            hasta: document.getElementById('fechaHasta')?.value || '',
            id_categoria: document.getElementById('filtroCategoria')?.value || '0',
            buscar: document.getElementById('buscadorVivo')?.value?.trim() || buscarServidor?.value?.trim() || '',
            metodo: document.getElementById('filtroMetodo')?.value || '',
            estado_vigencia: document.getElementById('filtroEstadoVigencia')?.value || ''
        };

        Object.entries(valores).forEach(([clave, valor]) => {
            if (valor !== '' && valor !== '0') params.set(clave, valor);
        });
        enlace.href = `<?= htmlspecialchars($baseUrl) ?>/admin/reportes/${accion}?${params.toString()}`;
    };

    document.querySelectorAll('[data-reporte-enlace]').forEach((enlace) => {
        enlace.addEventListener('click', () => actualizarUrlReporte(enlace));
    });

    const ubicarBuscador = (tab) => {
        const destino = document.querySelector(`.reporte-search-slot[data-tab="${tab}"]`);
        if (!contenedorBuscador) return;
        if (destino) {
            destino.appendChild(contenedorBuscador);
            contenedorBuscador.style.display = '';
        } else {
            contenedorBuscador.style.display = 'none';
        }
    };

    ubicarBuscador(document.getElementById('inputTabActivo')?.value || 'finanzas');

    document.querySelectorAll('#tabsReportes button[data-bs-toggle="pill"]').forEach((boton) => {
        boton.addEventListener('shown.bs.tab', (evento) => {
            const tab = evento.target.id.replace('tab-', '').replace('-btn', '');
            ubicarBuscador(tab);
        });
    });

    if (buscador) {
        buscador.addEventListener('input', (e) => {
            const termino = e.target.value;
            if (botonLimpiar) botonLimpiar.style.display = termino.trim() !== '' ? '' : 'none';
            if (buscarServidor) buscarServidor.value = termino;
            filtrarTablasEnVivo(termino);
        });
    }

    if (botonLimpiar) botonLimpiar.addEventListener('click', limpiarBuscador);

    // Eventos de cambios en tiempo real para filtros de pagos sin recargar la página
    ['fechaDesde', 'fechaHasta', 'filtroCategoria', 'filtroMetodo', 'filtroEstadoVigencia'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', () => {
                // Al modificar fechas manualmente, desmarcamos los presets rápidos
                if (id === 'fechaDesde' || id === 'fechaHasta') {
                    document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active'));
                    const inputPreset = document.getElementById('inputPreset');
                    if (inputPreset) inputPreset.value = '';
                }
                filtrarTablasEnVivo(buscador?.value || '');
            });
        }
    });

    const btnReset = document.getElementById('btnResetFiltrosReportes');
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            const fDesde = document.getElementById('fechaDesde');
            const fHasta = document.getElementById('fechaHasta');
            const fCat = document.getElementById('filtroCategoria');
            const fMet = document.getElementById('filtroMetodo');
            const fEst = document.getElementById('filtroEstadoVigencia');
            const inPreset = document.getElementById('inputPreset');

            if (fDesde) fDesde.value = '';
            if (fHasta) fHasta.value = '';
            if (fCat) fCat.value = '0';
            if (fMet) fMet.value = '';
            if (fEst) fEst.value = '';
            if (inPreset) inPreset.value = 'todo';

            document.querySelectorAll('.btn-preset').forEach(b => {
                b.classList.remove('active');
                if (b.textContent.trim().toLowerCase() === 'todo') b.classList.add('active');
            });

            filtrarTablasEnVivo(buscador?.value || '');
        });
    }

    try {
        iniciarGraficos();
    } catch (error) {
        console.error('No se pudieron inicializar todos los gráficos del reporte.', error);
    }
});

/**
 * Normaliza cadenas para comparaciones flexibles (minúsculas y sin acentos)
 */
function normalizarTexto(valor) {
    return String(valor || '')
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '');
}

/**
 * Filtra las filas de la tabla de pagos en vivo usando JS (sin recargar la página)
 */
function filtrarReportePagosEnVivo() {
    const query = normalizarTexto(document.getElementById('buscadorVivo')?.value?.trim() || '');
    const fechaDesde = document.getElementById('fechaDesde')?.value || '';
    const fechaHasta = document.getElementById('fechaHasta')?.value || '';
    const categoria = document.getElementById('filtroCategoria')?.value || '0';
    const metodo = document.getElementById('filtroMetodo')?.value || '';
    const estado = document.getElementById('filtroEstadoVigencia')?.value || '';

    const filas = document.querySelectorAll('#tablaPagos tbody tr.fila-buscable');
    let visibles = 0;

    filas.forEach(fila => {
        const textoFila = normalizarTexto(fila.getAttribute('data-texto'));
        const fCat = fila.getAttribute('data-categoria') || '0';
        const fFecha = fila.getAttribute('data-fecha') || '';
        const fMet = fila.getAttribute('data-metodo') || '';
        const fEst = fila.getAttribute('data-estado') || '';

        // Coincidencia de texto (buscador)
        const coincideTexto = query === '' || textoFila.includes(query);

        // Coincidencia de categoría
        const coincideCat = (categoria === '0' || fCat === categoria);

        // Coincidencia de método de pago
        const coincideMet = (metodo === '' || fMet.toUpperCase() === metodo.toUpperCase());

        // Coincidencia de estado de vigencia
        const coincideEst = (estado === '' || fEst.toUpperCase() === estado.toUpperCase());

        // Coincidencia de rango de fechas (YYYY-MM-DD)
        let coincideFecha = true;
        if (fechaDesde && fFecha) {
            if (fFecha < fechaDesde) coincideFecha = false;
        }
        if (fechaHasta && fFecha) {
            if (fFecha > fechaHasta) coincideFecha = false;
        }

        const visible = coincideTexto && coincideCat && coincideMet && coincideEst && coincideFecha;
        fila.style.display = visible ? '' : 'none';

        if (visible) visibles++;
    });

    const badgePagos = document.getElementById('conteoPagos');
    if (badgePagos) badgePagos.textContent = visibles;
}

/**
 * Filtra las filas de todas las tablas en vivo conforme el usuario escribe en el buscador
 */
function filtrarTablasEnVivo(termino) {
    // Primero actualiza la tabla de pagos combinando todos los filtros
    filtrarReportePagosEnVivo();

    // Luego filtra las demás tablas (Vigencias y Antigüedad)
    const query = normalizarTexto(termino.trim());
    let visiblesVigencias = 0;
    let visiblesAntiguedad = 0;

    document.querySelectorAll('#tablaVigencias tbody tr.fila-buscable, #tablaAntiguedad tbody tr.fila-buscable').forEach(fila => {
        const texto = normalizarTexto(fila.getAttribute('data-texto'));
        const coincide = query === '' || texto.includes(query);
        fila.style.display = coincide ? '' : 'none';

        if (coincide) {
            if (fila.closest('#tablaVigencias')) visiblesVigencias++;
            if (fila.closest('#tablaAntiguedad')) visiblesAntiguedad++;
        }
    });

    const badgeVig = document.getElementById('conteoVigencias');
    if (badgeVig) badgeVig.textContent = visiblesVigencias;

    const badgeAnt = document.getElementById('conteoAntiguedad');
    if (badgeAnt) badgeAnt.textContent = visiblesAntiguedad;
}

function limpiarBuscador() {
    const input = document.getElementById('buscadorVivo');
    if (input) input.value = '';
    const boton = document.getElementById('btnLimpiarBuscador');
    if (boton) boton.style.display = 'none';
    const buscarServidor = document.getElementById('buscarServidor');
    if (buscarServidor) buscarServidor.value = '';
    filtrarTablasEnVivo('');
}

/**
 * Aplicación de Presets rápidos de fechas sin recargar la página
 */
function aplicarPreset(preset) {
    const inputPreset = document.getElementById('inputPreset');
    if (inputPreset) inputPreset.value = preset;

    // Resaltar botón activo
    document.querySelectorAll('.btn-preset').forEach(b => {
        b.classList.remove('active');
        const onclickAttr = b.getAttribute('onclick') || '';
        if (onclickAttr.includes("'" + preset + "'")) {
            b.classList.add('active');
        }
    });

    const fDesde = document.getElementById('fechaDesde');
    const fHasta = document.getElementById('fechaHasta');
    if (!fDesde || !fHasta) return;

    const hoy = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const formatYMD = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    if (preset === 'hoy') {
        const hoyStr = formatYMD(hoy);
        fDesde.value = hoyStr;
        fHasta.value = hoyStr;
    } else if (preset === 'semana') {
        // Lunes de la semana actual
        const diaSemana = hoy.getDay(); // 0 Dom, 1 Lun, ...
        const diffAlLunes = diaSemana === 0 ? -6 : 1 - diaSemana;
        const lunes = new Date(hoy);
        lunes.setDate(hoy.getDate() + diffAlLunes);
        fDesde.value = formatYMD(lunes);
        fHasta.value = formatYMD(hoy);
    } else if (preset === 'mes') {
        // Primer día del mes actual hasta hoy
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        fDesde.value = formatYMD(inicioMes);
        fHasta.value = formatYMD(hoy);
    } else if (preset === 'mes_anterior') {
        // Todo el mes calendario anterior
        const inicioMesPasado = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
        const finMesPasado = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        fDesde.value = formatYMD(inicioMesPasado);
        fHasta.value = formatYMD(finMesPasado);
    } else if (preset === '90dias') {
        const hace90 = new Date(hoy);
        hace90.setDate(hoy.getDate() - 90);
        fDesde.value = formatYMD(hace90);
        fHasta.value = formatYMD(hoy);
    } else if (preset === 'anio') {
        const inicioAnio = new Date(hoy.getFullYear(), 0, 1);
        fDesde.value = formatYMD(inicioAnio);
        fHasta.value = formatYMD(hoy);
    } else if (preset === 'todo') {
        fDesde.value = '';
        fHasta.value = '';
    }

    filtrarTablasEnVivo(document.getElementById('buscadorVivo')?.value || '');
}

/**
 * Guarda la pestaña activa en el formulario
 */
function cambiarPestana(tabName) {
    const inputTab = document.getElementById('inputTabActivo');
    if (inputTab) inputTab.value = tabName;
}

/**
 * Carga y abre el modal con el informe individual del comercio
 */
function abrirInformeComercio(idLugar) {
    const modalEl = document.getElementById('modalInformeComercio');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const spinner = document.getElementById('mSpinner');
    const detalles = document.getElementById('mDetalles');
    spinner.style.display = 'block';
    detalles.style.display = 'none';

    fetch('<?= htmlspecialchars($baseUrl) ?>/admin/reportes/comercio?id_lugar=' + idLugar)
        .then(response => {
            if (!response.ok) throw new Error('Error al obtener datos');
            return response.json();
        })
        .then(data => {
            spinner.style.display = 'none';
            detalles.style.display = 'block';

            const c = data.comercio;
            const r = data.resumen_financiero;

            document.getElementById('mNombre').textContent = c.nombre;
            document.getElementById('mCategoria').textContent = c.categoria;
            document.getElementById('mDireccion').textContent = c.direccion;
            document.getElementById('mTelefono').textContent = c.telefono;
            document.getElementById('mWhatsapp').textContent = c.whatsapp;
            document.getElementById('mFechaRegistro').textContent = c.fecha_registro;
            document.getElementById('mAntiguedad').textContent = c.antiguedad_texto;
            document.getElementById('mTotalAportado').textContent = Number(r.total_aportado).toLocaleString('es-BO', { minimumFractionDigits: 2 });
            document.getElementById('mVigenciaHasta').textContent = r.vigencia_hasta;
            document.getElementById('mPagosConfirmados').textContent = r.pagos_confirmados;
            document.getElementById('mTicketPromedio').textContent = Number(r.ticket_promedio).toLocaleString('es-BO', { minimumFractionDigits: 2 });

            // Badge de Cobertura
            const badge = document.getElementById('mBadgeEstado');
            badge.className = 'badge rounded-pill extra-small ';
            if (r.estado_cobertura === 'VIGENTE') {
                badge.className += 'bg-success-subtle text-success border border-success-subtle';
                badge.textContent = 'VIGENTE / AL DÍA';
            } else if (r.estado_cobertura === 'POR_VENCER') {
                badge.className += 'bg-warning-subtle text-warning border border-warning-subtle';
                badge.textContent = 'POR VENCER (' + r.dias_restantes + 'd)';
            } else {
                badge.className += 'bg-danger-subtle text-danger border border-danger-subtle';
                badge.textContent = 'VENCIDO / EN MORA';
            }

            // Historial de Pagos
            const cuerpo = document.getElementById('mCuerpoPagos');
            cuerpo.innerHTML = '';
            if (data.pagos && data.pagos.length > 0) {
                data.pagos.forEach(p => {
                    const tr = document.createElement('tr');
                    const badgeClase = p.estado === 'CONFIRMADO' ? 'bg-success-subtle text-success' : (p.estado === 'PENDIENTE' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger');
                    tr.innerHTML = `
                        <td><code class="text-muted small">#${p.id_pago}</code></td>
                        <td class="small">${p.fecha_pago}</td>
                        <td class="fw-bold text-success small">Bs ${Number(p.monto).toLocaleString('es-BO', { minimumFractionDigits: 2 })}</td>
                        <td class="small">${p.metodo_pago}</td>
                        <td><code class="extra-small">${p.numero_comprobante}</code></td>
                        <td><span class="badge ${badgeClase} rounded-pill extra-small">${p.estado}</span></td>
                        <td class="extra-small text-muted">${p.fecha_confirmacion || 'Pendiente'}</td>
                    `;
                    cuerpo.appendChild(tr);
                });
            } else {
                cuerpo.innerHTML = '<tr><td colspan="7" class="text-center py-3 text-muted small">No hay pagos registrados para este comercio.</td></tr>';
            }

            // Enlace de exportación
            document.getElementById('mBtnExportar').href = '<?= htmlspecialchars($baseUrl) ?>/admin/reportes/exportar?tipo=comercio&id_lugar=' + c.id_lugar;
        })
        .catch(err => {
            spinner.style.display = 'none';
            detalles.style.display = 'block';
            detalles.innerHTML = '<div class="alert alert-danger small">No se pudo cargar la información del comercio.</div>';
        });
}

/**
 * Imprime directamente la ficha individual del comercio en una ventana limpia
 */
function imprimirEstadoCuentaModal() {
    const modalBody = document.getElementById('modalContenidoComercio').innerHTML;
    const nombre = document.getElementById('mNombre').textContent;
    const win = window.open('', '_blank', 'width=800,height=700');
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Estado de Cuenta - ${nombre}</title>
            <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
            <style>
                body { padding: 30px; font-family: sans-serif; }
                .table-sm th, .table-sm td { font-size: 0.82rem; }
            </style>
        </head>
        <body>
            <div class="text-center pb-3 mb-3 border-bottom">
                <h3 class="fw-bold mb-1">BENITOURS TRINIDAD</h3>
                <h5 class="text-muted mb-0">Ficha Individual y Estado de Cuenta de Comercio</h5>
                <small class="text-muted">Fecha de emisión: ${new Date().toLocaleDateString('es-BO')}</small>
            </div>
            ${modalBody}
            <div class="mt-5 pt-4 text-center border-top small text-muted">
                <p class="mb-0">Documento emitido por el Sistema de Administración BeniTours Trinidad.</p>
            </div>
            <script>
                window.onload = function() { window.print(); }
            <\/script>
        </body>
        </html>
    `);
    win.document.close();
}

/**
 * Renderizado de Gráficos Chart.js
 */
function iniciarGraficos() {
    if (!window.Chart) return;

    // 1. Gráfico de Ingresos Mensuales
    const ctxIngresos = document.getElementById('chartReporteIngresos')?.getContext('2d');
    if (ctxIngresos) {
        const etiquetas = <?= json_encode($analiticaFinanciera['grafico']['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const montos = <?= json_encode($analiticaFinanciera['grafico']['montos']) ?>;
        const pagosPorMes = <?= json_encode($analiticaFinanciera['grafico']['pagos']) ?>;

        const gradiente = ctxIngresos.createLinearGradient(0, 0, 0, 270);
        gradiente.addColorStop(0, 'rgba(42, 157, 143, 0.55)');
        gradiente.addColorStop(1, 'rgba(42, 157, 143, 0.05)');

        chartIngresosInstance = new Chart(ctxIngresos, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Ingresos confirmados',
                    data: montos,
                    backgroundColor: gradiente,
                    borderColor: '#2A9D8F',
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0E4D5E',
                        padding: 11,
                        callbacks: {
                            label: (c) => ' Bs ' + Number(c.parsed.y).toLocaleString('es-BO', { minimumFractionDigits: 2 }),
                            afterLabel: (c) => ' ' + pagosPorMes[c.dataIndex] + ' pago(s) confirmado(s)'
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { callback: (v) => 'Bs ' + Number(v).toLocaleString('es-BO') } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Gráfico Dona por Categoría
    const ctxDona = document.getElementById('chartCategoriasDona')?.getContext('2d');
    if (ctxDona) {
        const catLabels = <?= json_encode($graficoCategorias['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const catMontos = <?= json_encode($graficoCategorias['montos']) ?>;
        const catColores = <?= json_encode($graficoCategorias['colores']) ?>;

        chartDonaInstance = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catMontos,
                    backgroundColor: catColores,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: (c) => ' ' + c.label + ': Bs ' + Number(c.parsed).toLocaleString('es-BO', { minimumFractionDigits: 2 })
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 3. Gráficos de Tráfico Turístico (4 Gráficos: Días, Horas, Semanas, Meses)
    iniciarGraficosVisitas();
}

let chartIngresosInstance = null;
let chartDonaInstance = null;
let chartDiasInstance = null;
let chartHorasInstance = null;
let chartSemanasInstance = null;
let chartMesesInstance = null;

function iniciarGraficosVisitas() {
    // 1. Visitantes por Día (Últimos 14 días)
    const ctxDias = document.getElementById('chartVisitasDias')?.getContext('2d');
    if (ctxDias) {
        const labelsDias = <?= json_encode($analiticaVisitas['dias']['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const diasSemana = <?= json_encode($analiticaVisitas['dias']['dias_semana'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresDias = <?= json_encode($analiticaVisitas['dias']['valores']) ?>;

        const gradienteDias = ctxDias.createLinearGradient(0, 0, 0, 270);
        gradienteDias.addColorStop(0, 'rgba(13, 110, 253, 0.45)');
        gradienteDias.addColorStop(1, 'rgba(13, 110, 253, 0.02)');

        chartDiasInstance = new Chart(ctxDias, {
            type: 'line',
            data: {
                labels: labelsDias,
                datasets: [{
                    label: 'Visitantes por día',
                    data: valoresDias,
                    borderColor: '#0d6efd',
                    backgroundColor: gradienteDias,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            title: (items) => {
                                const idx = items[0].dataIndex;
                                return items[0].label + ' (' + (diasSemana[idx] || '') + ')';
                            },
                            label: (c) => ' ' + c.parsed.y + ' visitante(s) único(s)'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { precision: 0 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Distribución por Horas (24 Horas)
    const ctxHoras = document.getElementById('chartVisitasHoras')?.getContext('2d');
    if (ctxHoras) {
        const labelsHoras = <?= json_encode($analiticaVisitas['horas']['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresHoras = <?= json_encode($analiticaVisitas['horas']['valores']) ?>;

        const gradienteHoras = ctxHoras.createLinearGradient(0, 0, 0, 270);
        gradienteHoras.addColorStop(0, 'rgba(13, 202, 240, 0.75)');
        gradienteHoras.addColorStop(1, 'rgba(13, 202, 240, 0.15)');

        chartHorasInstance = new Chart(ctxHoras, {
            type: 'bar',
            data: {
                labels: labelsHoras,
                datasets: [{
                    label: 'Visitas por hora',
                    data: valoresHoras,
                    backgroundColor: gradienteHoras,
                    borderColor: '#0dcaf0',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 22
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            title: (items) => 'Franja Horaria: ' + items[0].label,
                            label: (c) => ' ' + c.parsed.y + ' visita(s)'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 12
                        }
                    }
                }
            }
        });
    }

    // 3. Afluencia por Semanas (Últimas 8 Semanas)
    const ctxSemanas = document.getElementById('chartVisitasSemanas')?.getContext('2d');
    if (ctxSemanas) {
        const labelsSemanas = <?= json_encode($analiticaVisitas['semanas']['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const rangosSemanas = <?= json_encode($analiticaVisitas['semanas']['rangos'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresSemanas = <?= json_encode($analiticaVisitas['semanas']['valores']) ?>;

        const gradienteSemanas = ctxSemanas.createLinearGradient(0, 0, 0, 270);
        gradienteSemanas.addColorStop(0, 'rgba(42, 157, 143, 0.75)');
        gradienteSemanas.addColorStop(1, 'rgba(42, 157, 143, 0.15)');

        chartSemanasInstance = new Chart(ctxSemanas, {
            type: 'bar',
            data: {
                labels: labelsSemanas,
                datasets: [{
                    label: 'Visitantes por semana',
                    data: valoresSemanas,
                    backgroundColor: gradienteSemanas,
                    borderColor: '#2A9D8F',
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            title: (items) => {
                                const idx = items[0].dataIndex;
                                return items[0].label + ' (' + (rangosSemanas[idx] || '') + ')';
                            },
                            label: (c) => ' ' + c.parsed.y + ' visitante(s) semanal(es)'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { precision: 0 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 4. Evolución por Meses (Últimos 6 Meses)
    const ctxMeses = document.getElementById('chartVisitasMeses')?.getContext('2d');
    if (ctxMeses) {
        const labelsMeses = <?= json_encode($analiticaVisitas['meses']['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresMeses = <?= json_encode($analiticaVisitas['meses']['valores']) ?>;

        const gradienteMeses = ctxMeses.createLinearGradient(0, 0, 0, 270);
        gradienteMeses.addColorStop(0, 'rgba(111, 66, 193, 0.75)');
        gradienteMeses.addColorStop(1, 'rgba(111, 66, 193, 0.15)');

        chartMesesInstance = new Chart(ctxMeses, {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Visitantes por mes',
                    data: valoresMeses,
                    backgroundColor: gradienteMeses,
                    borderColor: '#6f42c1',
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            title: (items) => 'Mes: ' + items[0].label,
                            label: (c) => ' ' + c.parsed.y + ' visitante(s) total'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { precision: 0 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
}

// Variables globales para instancias de gráficos del Modal de Rendimiento
let chartRankingConvInstance = null;
let chartCanalesDonaInstance = null;
let chartEmbudoInstance = null;

/**
 * Abre el modal con los gráficos visuales de la tabla de Rendimiento y Conversión
 */
function abrirModalGraficosRendimiento() {
    const modalEl = document.getElementById('modalGraficosRanking');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Inicializar gráficos del Modal cuando este se muestre en pantalla
document.addEventListener('DOMContentLoaded', () => {
    const modalRankingEl = document.getElementById('modalGraficosRanking');
    if (modalRankingEl) {
        modalRankingEl.addEventListener('shown.bs.modal', () => {
            renderizarGraficosModalRanking();
        });
    }

    const tabVisitasBtn = document.getElementById('tab-visitas-btn');
    if (tabVisitasBtn) {
        tabVisitasBtn.addEventListener('shown.bs.tab', () => {
            if (chartDiasInstance) chartDiasInstance.resize();
            if (chartHorasInstance) chartHorasInstance.resize();
            if (chartSemanasInstance) chartSemanasInstance.resize();
            if (chartMesesInstance) chartMesesInstance.resize();
        });
    }

    const tabFinanzasBtn = document.getElementById('tab-finanzas-btn');
    if (tabFinanzasBtn) {
        tabFinanzasBtn.addEventListener('shown.bs.tab', () => {
            if (chartIngresosInstance) chartIngresosInstance.resize();
            if (chartDonaInstance) chartDonaInstance.resize();
        });
    }
});

/**
 * Construye los gráficos interactivos dentro del Modal usando los datos del ranking
 */
function renderizarGraficosModalRanking() {
    if (!window.Chart) return;

    <?php
    $datosRankingJson = [];
    if (!empty($rankingRendimiento)) {
        foreach ($rankingRendimiento as $rk) {
            $datosRankingJson[] = [
                'nombre' => $rk['establecimiento'],
                'categoria' => $rk['categoria'],
                'vistas' => (int)$rk['vistas'],
                'whatsapp' => (int)$rk['contactos_whatsapp'],
                'mapa' => (int)$rk['rutas_mapa'],
                'favoritos' => (int)$rk['guardado_favoritos'],
                'total_conv' => (int)$rk['total_conversiones']
            ];
        }
    }
    ?>
    const datosRanking = <?= json_encode($datosRankingJson, JSON_UNESCAPED_UNICODE) ?>;
    const totalesCanales = {
        whatsapp: <?= (int)$totalesTelemetria['total_whatsapp'] ?>,
        mapa: <?= (int)$totalesTelemetria['total_mapa'] ?>,
        favoritos: <?= (int)$totalesTelemetria['total_favoritos'] ?>
    };

    // 1. Gráfico de Barras Horizontales: Top 10 Comercios por Conversiones
    const ctxConv = document.getElementById('chartRankingConversiones')?.getContext('2d');
    if (ctxConv) {
        if (chartRankingConvInstance) chartRankingConvInstance.destroy();

        // Tomar hasta los 10 primeros
        const top10 = datosRanking.slice(0, 10);
        const labelsTop = top10.map(d => d.nombre.length > 20 ? d.nombre.substring(0, 18) + '...' : d.nombre);
        const dataWhatsapp = top10.map(d => d.whatsapp);
        const dataMapa = top10.map(d => d.mapa);

        chartRankingConvInstance = new Chart(ctxConv, {
            type: 'bar',
            data: {
                labels: labelsTop,
                datasets: [
                    {
                        label: 'WhatsApp',
                        data: dataWhatsapp,
                        backgroundColor: '#2A9D8F',
                        borderRadius: 6
                    },
                    {
                        label: 'Rutas Maps',
                        data: dataMapa,
                        backgroundColor: '#e11d48',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11, weight: 'bold' } } },
                    tooltip: {
                        backgroundColor: '#0E4D5E',
                        padding: 10,
                        callbacks: {
                            footer: (items) => {
                                let total = 0;
                                items.forEach(i => total += i.parsed.x);
                                return 'Total: ' + total + ' conversiones';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Dona: Distribución por Canal de Interacción
    const ctxCanales = document.getElementById('chartCanalesDona')?.getContext('2d');
    if (ctxCanales) {
        if (chartCanalesDonaInstance) chartCanalesDonaInstance.destroy();

        const labelsCanales = ['WhatsApp Directo', 'Rutas en Google Maps', 'En Favoritos'];
        const dataCanales = [totalesCanales.whatsapp, totalesCanales.mapa, totalesCanales.favoritos];

        chartCanalesDonaInstance = new Chart(ctxCanales, {
            type: 'doughnut',
            data: {
                labels: labelsCanales,
                datasets: [{
                    data: dataCanales,
                    backgroundColor: ['#2A9D8F', '#e11d48', '#FF6F3C'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 14, font: { size: 11 } } },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        callbacks: {
                            label: (c) => ' ' + c.label + ': ' + c.raw + ' interacciones'
                        }
                    }
                },
                cutout: '62%'
            }
        });
    }

    // 3. Gráfico de Barras Comparativo: Visibilidad vs Contactos (Top 8)
    const ctxEmbudo = document.getElementById('chartEmbudoAtraccion')?.getContext('2d');
    if (ctxEmbudo) {
        if (chartEmbudoInstance) chartEmbudoInstance.destroy();

        const top8 = datosRanking.slice(0, 8);
        const labelsEmb = top8.map(d => d.nombre.length > 18 ? d.nombre.substring(0, 16) + '...' : d.nombre);
        const dataVistas = top8.map(d => d.vistas);
        const dataTotalConv = top8.map(d => d.total_conv);

        chartEmbudoInstance = new Chart(ctxEmbudo, {
            type: 'bar',
            data: {
                labels: labelsEmb,
                datasets: [
                    {
                        label: 'Vistas de Ficha',
                        data: dataVistas,
                        backgroundColor: 'rgba(249, 115, 22, 0.82)',
                        borderColor: '#ea580c',
                        borderWidth: 1.5,
                        borderRadius: 6
                    },
                    {
                        label: 'Contactos Reales (Conv.)',
                        data: dataTotalConv,
                        backgroundColor: 'rgba(42, 157, 143, 0.88)',
                        borderColor: '#0E4D5E',
                        borderWidth: 1.5,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        backgroundColor: '#0E4D5E',
                        padding: 10,
                        callbacks: {
                            afterBody: (items) => {
                                const idx = items[0].dataIndex;
                                const v = dataVistas[idx] || 0;
                                const c = dataTotalConv[idx] || 0;
                                const tasa = v > 0 ? ((c / v) * 100).toFixed(1) : '0.0';
                                return 'Tasa de conversión: ' + tasa + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10.5 } }
                    }
                }
            }
        });
    }
}

/**
 * Abre el informe oficial dentro de un Modal en la misma página (sin recargas ni cambiar de pantalla)
 */
function abrirModalImprimir(tipo) {
    const modalEl = document.getElementById('modalVistaPreviaImpresion');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const spinner = document.getElementById('modalImprimirSpinner');
    const contenido = document.getElementById('modalImprimirContenido');
    const tituloModal = document.getElementById('modalImprimirTitulo');

    if (spinner) spinner.style.display = 'block';
    if (contenido) contenido.style.display = 'none';
    if (tituloModal) tituloModal.textContent = 'Generando informe...';

    // Parámetros actuales de los filtros
    const params = new URLSearchParams({
        tipo: tipo,
        format: 'json'
    });

    const valores = {
        desde: document.getElementById('fechaDesde')?.value || '',
        hasta: document.getElementById('fechaHasta')?.value || '',
        id_categoria: document.getElementById('filtroCategoria')?.value || '0',
        buscar: document.getElementById('buscadorVivo')?.value?.trim() || '',
        metodo: document.getElementById('filtroMetodo')?.value || '',
        estado_vigencia: document.getElementById('filtroEstadoVigencia')?.value || ''
    };

    Object.entries(valores).forEach(([clave, valor]) => {
        if (valor !== '' && valor !== '0') params.set(clave, valor);
    });

    fetch(`<?= htmlspecialchars($baseUrl) ?>/admin/reportes/imprimir?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => {
        if (!res.ok) throw new Error('Error al cargar datos del reporte (' + res.status + ')');
        return res.json();
    })
    .then(data => {
        if (spinner) spinner.style.display = 'none';
        if (contenido) contenido.style.display = 'block';

        if (tituloModal) tituloModal.textContent = data.titulo || 'Informe Oficial';
        const docTitulo = document.getElementById('docReporteTitulo');
        if (docTitulo) docTitulo.textContent = data.titulo || 'INFORME OFICIAL';

        const docFecha = document.getElementById('docReporteFecha');
        if (docFecha) docFecha.textContent = data.fecha || '';

        const docTotal = document.getElementById('docReporteTotal');
        if (docTotal) docTotal.textContent = data.total || (data.filas ? data.filas.length : 0);

        // Cabeceras de tabla
        const thead = document.getElementById('docReporteThead');
        if (thead) {
            let thHtml = '<tr>';
            (data.cabeceras || []).forEach(c => {
                thHtml += `<th class="small py-2">${escapeHtml(c)}</th>`;
            });
            thHtml += '</tr>';
            thead.innerHTML = thHtml;
        }

        // Filas de tabla
        const tbody = document.getElementById('docReporteTbody');
        if (tbody) {
            let trHtml = '';
            if (data.filas && data.filas.length > 0) {
                data.filas.forEach(fila => {
                    trHtml += '<tr>';
                    fila.forEach((val, idx) => {
                        const esPrimero = idx === 0;
                        const esMonto = String(val).startsWith('Bs ') || /^[0-9]+(\.[0-9]{2})?$/.test(String(val).replace(/,/g, ''));
                        const align = esMonto ? 'text-end' : (esPrimero ? 'text-center' : '');
                        trHtml += `<td class="small py-1 ${align}">${escapeHtml(val)}</td>`;
                    });
                    trHtml += '</tr>';
                });
            } else {
                const totalCols = data.cabeceras ? data.cabeceras.length : 1;
                trHtml = `<tr><td colspan="${totalCols}" class="text-center py-4 text-muted small">No se encontraron registros para este informe.</td></tr>`;
            }
            tbody.innerHTML = trHtml;
        }
    })
    .catch(err => {
        if (spinner) spinner.style.display = 'none';
        if (contenido) {
            contenido.style.display = 'block';
            contenido.innerHTML = `<div class="alert alert-danger py-3 small mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>${err.message || 'Error al obtener el reporte.'}</div>`;
        }
    });
}

/**
 * Escapa cadenas HTML para prevenir inyecciones
 */
function escapeHtml(texto) {
    if (texto === null || texto === undefined) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Ejecuta la impresión directamente desde la misma pantalla usando un iframe oculto
 * sin abrir una segunda ventana ni dejar una página en blanco abierta.
 */
function ejecutarImpresionDoc() {
    const docHtml = document.getElementById('modalImprimirContenido')?.innerHTML;
    const titulo = document.getElementById('docReporteTitulo')?.textContent || 'Informe';
    if (!docHtml) return;

    // Buscar o crear iframe oculto para impresión silenciosa
    let printIframe = document.getElementById('iframeImpresionOculto');
    if (!printIframe) {
        printIframe = document.createElement('iframe');
        printIframe.id = 'iframeImpresionOculto';
        printIframe.style.position = 'fixed';
        printIframe.style.right = '0';
        printIframe.style.bottom = '0';
        printIframe.style.width = '0';
        printIframe.style.height = '0';
        printIframe.style.border = '0';
        printIframe.style.visibility = 'hidden';
        document.body.appendChild(printIframe);
    }

    const doc = printIframe.contentWindow.document;
    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>${escapeHtml(titulo)} - BeniTours</title>
            <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/css/bootstrap.min.css">
            <style>
                @page { size: auto; margin: 15mm 10mm; }
                body { margin: 0; padding: 15px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #212529; background: #fff; }
                .table-sm th, .table-sm td { font-size: 0.8rem; padding: 0.35rem 0.5rem; }
                thead { display: table-header-group; }
                tr { break-inside: avoid; }
                .table-bordered { border-color: #dee2e6 !important; }
            </style>
        </head>
        <body>
            ${docHtml}
        </body>
        </html>
    `);
    doc.close();

    // Esperar a que rendericen estilos e invocar impresión directa sin popups visibles
    setTimeout(() => {
        printIframe.contentWindow.focus();
        printIframe.contentWindow.print();
    }, 250);
}
</script>
