<div class="container-fluid p-0">
    <!-- Encabezado Principal y Accesos Rápidos -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-grid-1x2-fill text-success me-2"></i>Panel de Control y Analítica
            </h4>
            <p class="text-muted small mb-0">
                Bienvenido, <strong><?= htmlspecialchars($adminNombre) ?></strong> &bull; Santísima Trinidad, Beni &bull; <?= date('d/m/Y') ?>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/reportes" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-none">
                <i class="bi bi-bar-chart-line-fill me-1 text-primary"></i> Reportes Detallados
            </a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/solicitudes" class="btn btn-outline-warning btn-sm rounded-pill px-3 shadow-none position-relative">
                <i class="bi bi-inbox-fill me-1"></i> Solicitudes Web
                <?php if ($kpis['totalSolicitudes'] > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-1"><?= $kpis['totalSolicitudes'] ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/crear" class="btn btn-success btn-sm rounded-pill px-3 shadow-none fw-semibold">
                <i class="bi bi-plus-circle-fill me-1"></i> Nueva Ficha
            </a>
        </div>
    </div>

    <!-- 1. Cuadrícula de Indicadores Clave de Plataforma (KPIs Principales) -->
    <div class="row g-3 mb-4">
        <!-- Visitantes Hoy -->
        <div class="col-6 col-md-4 col-xl">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Visitantes Hoy</span>
                    <div class="dash-metric-icon dash-icon-emerald">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark"><?= number_format($kpis['visitasHoy'] ?? 0) ?></div>
                <div class="dash-metric-sub">
                    <span class="text-success fw-semibold"><i class="bi bi-broadcast"></i> En vivo</span>
                    <span>Navegadores únicos</span>
                </div>
            </div>
        </div>

        <!-- Visitantes del Mes -->
        <div class="col-6 col-md-4 col-xl">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Visitantes del Mes</span>
                    <div class="dash-metric-icon dash-icon-blue">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark"><?= number_format($kpis['visitasMes'] ?? 0) ?></div>
                <div class="dash-metric-sub">
                    <span class="text-primary fw-semibold"><i class="bi bi-graph-up-arrow"></i> Mensual</span>
                    <span>Tráfico acumulado</span>
                </div>
            </div>
        </div>

        <!-- Comercios Afiliados -->
        <div class="col-6 col-md-4 col-xl">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Comercios Afiliados</span>
                    <div class="dash-metric-icon dash-icon-purple">
                        <i class="bi bi-shop-window"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark"><?= number_format($kpis['totalComercios'] ?? 0) ?></div>
                <div class="dash-metric-sub">
                    <span class="text-purple fw-semibold"><i class="bi bi-shield-check"></i> Activos</span>
                    <span><?= $kpis['totalLugares'] ?? 0 ?> fichas en total</span>
                </div>
            </div>
        </div>

        <!-- Atractivos Públicos -->
        <div class="col-6 col-md-4 col-xl">
            <div class="dash-hero-metric">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Atractivos Públicos</span>
                    <div class="dash-metric-icon dash-icon-teal">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                </div>
                <div class="dash-metric-number text-dark"><?= number_format($kpis['totalPublicos'] ?? 0) ?></div>
                <div class="dash-metric-sub">
                    <span class="text-info fw-semibold"><i class="bi bi-pin-map"></i> Turismo</span>
                    <span>Libre acceso</span>
                </div>
            </div>
        </div>

        <!-- Solicitudes Web -->
        <div class="col-12 col-md-4 col-xl">
            <div class="dash-hero-metric <?= ($kpis['totalSolicitudes'] > 0) ? 'border-warning' : '' ?>">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dash-metric-title">Solicitudes Web</span>
                    <div class="dash-metric-icon dash-icon-amber position-relative">
                        <i class="bi bi-inbox-fill"></i>
                        <?php if ($kpis['totalSolicitudes'] > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dash-metric-number <?= ($kpis['totalSolicitudes'] > 0) ? 'text-warning' : 'text-dark' ?>">
                    <?= number_format($kpis['totalSolicitudes'] ?? 0) ?>
                </div>
                <div class="dash-metric-sub">
                    <?php if ($kpis['totalSolicitudes'] > 0): ?>
                        <span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle-fill"></i> Pendientes</span>
                        <a href="<?= htmlspecialchars($baseUrl) ?>/admin/solicitudes" class="text-decoration-none small text-dark fw-bold">Revisar &rarr;</a>
                    <?php else: ?>
                        <span class="text-muted"><i class="bi bi-check2-all text-success"></i> Al día</span>
                        <span>Sin trámites</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 1.1 Comercios por Categoría (Acceso Directo Filtrado) -->
    <?php if (!empty($categoriasComercios)): ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <span class="text-uppercase fw-bold text-muted extra-small" style="letter-spacing: 0.5px;">
                <i class="bi bi-tags-fill me-1 text-success"></i> Comercios Registrados por Rubro
            </span>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="small text-decoration-none text-muted fw-semibold">
                Ver todos <i class="bi bi-chevron-right small"></i>
            </a>
        </div>
        <div class="row g-2">
            <?php 
            $paletteClasses = [
                'dash-icon-emerald', 
                'dash-icon-blue', 
                'dash-icon-purple', 
                'dash-icon-teal', 
                'dash-icon-amber', 
                'dash-icon-rose'
            ];
            foreach ($categoriasComercios as $idx => $cat): 
                $iconClass = !empty($cat['icono']) ? $cat['icono'] : 'bi-tag-fill';
                $colorTheme = $paletteClasses[$idx % count($paletteClasses)];
            ?>
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares?categoria=<?= (int)$cat['id_categoria'] ?>" class="dash-cat-card text-decoration-none d-block">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="dash-cat-card-icon <?= $colorTheme ?>">
                                <i class="bi <?= htmlspecialchars($iconClass) ?>"></i>
                            </div>
                            <span class="dash-cat-card-title text-truncate" title="<?= htmlspecialchars($cat['nombre']) ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <span class="dash-cat-card-count"><?= (int)$cat['cantidad'] ?></span>
                            <span class="extra-small text-muted">negocios</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 2. Alerta de Cobranzas y Renovaciones Pendientes -->
    <?php if ($kpis['comerciosPorVencer'] > 0 || $kpis['comerciosVencidos'] > 0): ?>
        <div class="dashboard-renewal alert alert-warning border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="widget-icon bg-warning text-dark flex-shrink-0 animate-pulse">
                    <i class="bi bi-bell-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Recordatorios de Cobro y Renovación Activos</h6>
                    <p class="small text-secondary mb-0">
                        Tienes <strong><?= $kpis['comerciosPorVencer'] ?> negocio(s) por vencer</strong> en los próximos 15 días y <strong><?= $kpis['comerciosVencidos'] ?> negocio(s)</strong> con suscripción vencida o sin vigencia.
                    </p>
                </div>
            </div>
            <a href="#moduloRecordatorios" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="bi bi-whatsapp me-1"></i> Enviar Recordatorios
            </a>
        </div>
    <?php endif; ?>

    <!-- 3. Sección de Gráficos Analíticos (Chart.js) -->
    <div class="row g-4 mb-4">
        <!-- Gráfico 1: Evolución de Recaudación Mensual -->
        <div class="col-lg-8">
            <div class="admin-card chart-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-people-fill text-success me-2"></i>Visitas al sitio
                        </h6>
                        <small class="text-muted">Visitantes únicos por mes durante los últimos 6 meses</small>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill extra-small">
                        <i class="bi bi-calendar-check me-1"></i> Semestre actual
                    </span>
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="chartVisitasMensuales"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Distribución por Categorías -->
        <div class="col-lg-4">
            <div class="admin-card chart-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-bar-chart-fill text-warning me-2"></i>Comercios por categoría
                        </h6>
                        <small class="text-muted">Cantidad de comercios registrados en cada categoría</small>
                    </div>
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="chartCategoriasComercios"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Módulo Central: Recordatorios de Cobro y Renovación -->
    <div class="dashboard-renewal admin-card mb-4" id="moduloRecordatorios">
        <div class="p-3 p-md-4 border-bottom bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-whatsapp text-success me-2"></i>Recordatorios de Cobro y Renovación de Membresías
                </h5>
                <p class="text-muted small mb-0">
                    Envía avisos de cobro y renovación vía WhatsApp o registra pagos directamente con un solo clic
                </p>
            </div>
            <!-- Filtros rápidos por estado -->
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <button type="button" class="filter-pill-btn active" data-filtro="todos">
                    Todos (<?= count($recordatoriosCobro) ?>)
                </button>
                <button type="button" class="filter-pill-btn" data-filtro="urgente">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Por Vencer (<?= $kpis['comerciosPorVencer'] ?>)
                </button>
                <button type="button" class="filter-pill-btn" data-filtro="vencido">
                    <i class="bi bi-x-circle-fill text-danger me-1"></i> Vencidos (<?= $kpis['comerciosVencidos'] ?>)
                </button>
                <button type="button" class="filter-pill-btn" data-filtro="al_dia">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Al Día (<?= $kpis['comerciosActivos'] ?>)
                </button>
            </div>
        </div>

        <!-- Buscador dentro de la tabla -->
        <div class="p-3 bg-light border-bottom">
            <div class="input-group input-group-sm" style="max-width: 400px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="inputBuscarRecordatorio" class="form-control border-start-0" placeholder="Buscar negocio en la lista...">
            </div>
        </div>

        <!-- Tabla de Recordatorios -->
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0" id="tablaRecordatorios">
                <thead>
                    <tr>
                        <th>Establecimiento</th>
                        <th>Rubro</th>
                        <th>Vencimiento</th>
                        <th>Estado de Cobertura</th>
                        <th>Contacto</th>
                        <th class="text-end">Acciones de Cobro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recordatoriosCobro)): ?>
                        <?php foreach ($recordatoriosCobro as $rec): ?>
                            <tr class="fila-recordatorio" 
                                data-estado="<?= htmlspecialchars($rec['estado']) ?>"
                                data-nombre="<?= htmlspecialchars(mb_strtolower($rec['nombre'], 'UTF-8')) ?>"
                                data-categoria="<?= htmlspecialchars(mb_strtolower($rec['categoria'], 'UTF-8')) ?>">
                                <td>
                                    <strong class="text-dark d-block"><?= htmlspecialchars($rec['nombre']) ?></strong>
                                    <small class="text-muted">Cuota: Bs <?= number_format($rec['monto'], 0) ?>/mes</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($rec['categoria']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark d-block"><?= htmlspecialchars($rec['fecha_vencimiento']) ?></span>
                                    <?php if ($rec['dias_restantes'] !== null): ?>
                                        <small class="text-muted extra-small">
                                            <?= $rec['dias_restantes'] >= 0 ? "Faltan {$rec['dias_restantes']} días" : "Expiró hace " . abs($rec['dias_restantes']) . " días" ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-danger extra-small">Sin abono registrado</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $rec['badge_clase'] ?> rounded-pill px-2 py-1 extra-small">
                                        <?= htmlspecialchars($rec['badge_texto']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($rec['telefono'])): ?>
                                        <span class="small text-dark fw-semibold d-block">
                                            <i class="bi bi-telephone-fill me-1 text-secondary extra-small"></i><?= htmlspecialchars($rec['telefono']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Sin teléfono</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if (!empty($rec['wa_url'])): ?>
                                            <!-- Botón WhatsApp Directo con Mensaje Predeterminado -->
                                            <a href="<?= $rec['wa_url'] ?>" target="_blank" rel="noopener noreferrer" 
                                               class="btn btn-recordatorio-wa btn-sm rounded-pill px-2 py-1 extra-small shadow-none d-flex align-items-center gap-1"
                                               title="Enviar recordatorio cordial por WhatsApp">
                                                <i class="bi bi-whatsapp"></i> Recordar
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($rec['telefono_raw'])): ?>
                                            <!-- Botón Llamar -->
                                            <a href="tel:<?= $rec['telefono_raw'] ?>" 
                                               class="btn btn-outline-dark btn-sm rounded-circle p-1" 
                                               title="Llamar al negocio" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-telephone-outbound extra-small"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Botón Copiar Mensaje -->
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm rounded-circle p-1 btn-copiar-recordatorio" 
                                                data-mensaje="<?= htmlspecialchars($rec['mensaje_recordatorio'], ENT_QUOTES, 'UTF-8') ?>"
                                                title="Copiar texto de recordatorio" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-clipboard extra-small"></i>
                                        </button>

                                        <!-- Botón Registrar Pago / Renovación -->
                                        <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/crear?id_lugar=<?= $rec['id_lugar'] ?>" 
                                           class="btn btn-outline-success btn-sm rounded-pill px-2 py-1 extra-small"
                                           title="Registrar cobro o renovación de este lugar">
                                            <i class="bi bi-cash-stack me-1"></i> Cobrar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-check2-circle fs-3 text-success d-block mb-1"></i>
                                No hay comercios registrados en el sistema.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Tabla de Fichas Recientes y Reglas del Directorio -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card overflow-hidden">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white gap-2">
                    <h6 class="fw-bold text-dark mb-0 text-truncate">
                        <i class="bi bi-clock-history me-1 text-success"></i> Últimas Fichas Registradas
                    </h6>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="btn btn-outline-success btn-sm rounded-pill px-3 text-nowrap flex-shrink-0" style="font-size: 0.78rem;">
                        Ver Directorio Completo <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Lugar</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimosLugares)): ?>
                                <?php foreach ($ultimosLugares as $l): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark d-block"><?= htmlspecialchars($l['nombre']) ?></strong>
                                            <small class="text-muted">Santísima Trinidad, Beni</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-nowrap">
                                                <?= htmlspecialchars($l['categoria']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $l['tipo_lugar'] === 'PUBLICO' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?> rounded-pill text-nowrap">
                                                <?= $l['tipo_lugar'] === 'PUBLICO' ? 'Público Gratuito' : 'Comercio Suscriptor' ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small text-nowrap">
                                            <?= date('d/m/Y', strtotime($l['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No existen lugares registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráfico de visitas recientes -->
        <div class="col-lg-4">
            <div class="admin-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3"><div><h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Visitas recientes</h6><small class="text-muted">Últimos 14 días</small></div><span class="badge bg-success-subtle text-success"><?= array_sum($graficoVisitasDiarias['valores']) ?> visitas</span></div>
                <div style="height: 245px; position:relative"><canvas id="chartVisitasDiarias"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Inicialización de Chart.js y Filtros de Recordatorios -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Ingresos Mensuales (Línea con Gradiente y Barras)
    const ctxIngresos = document.getElementById('chartVisitasMensuales')?.getContext('2d');
    if (ctxIngresos && window.Chart) {
        const labelsIngresos = <?= json_encode($graficoVisitas['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresIngresos = <?= json_encode($graficoVisitas['valores']) ?>;

        const gradient = ctxIngresos.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(42, 157, 143, 0.45)');
        gradient.addColorStop(1, 'rgba(42, 157, 143, 0.02)');

        new Chart(ctxIngresos, {
            type: 'line',
            data: {
                labels: labelsIngresos,
                datasets: [{
                    label: 'Visitantes únicos',
                    data: valoresIngresos,
                    borderColor: '#2A9D8F',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2A9D8F',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0E4D5E',
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 13, weight: 'bold' },
                        callbacks: {
                            label: function(context) {
                                return ' ' + Number(context.parsed.y).toLocaleString('es-BO') + ' visitantes';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(val) {
                                return Number(val).toLocaleString('es-BO');
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    const ctxVisitasDiarias = document.getElementById('chartVisitasDiarias')?.getContext('2d');
    if (ctxVisitasDiarias && window.Chart) {
        new Chart(ctxVisitasDiarias, {
            type: 'bar',
            data: {
                labels: <?= json_encode($graficoVisitasDiarias['labels'], JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{ label: 'Visitas', data: <?= json_encode($graficoVisitasDiarias['valores']) ?>, backgroundColor: 'rgba(42, 157, 143, 0.72)', borderColor: '#2A9D8F', borderWidth: 1, borderRadius: 5 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `${ctx.parsed.y} visitas` } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
        });
    }

    // 2. Gráfico de Categorías (Doughnut)
    const ctxCat = document.getElementById('chartCategoriasComercios')?.getContext('2d');
    if (ctxCat && window.Chart) {
        const labelsCat = <?= json_encode($graficoCategorias['labels'], JSON_UNESCAPED_UNICODE) ?>;
        const valoresCat = <?= json_encode($graficoCategorias['valores']) ?>;

        const paletaColores = [
            '#2A9D8F', // Teal Amazónico (Atractivos)
            '#0d6efd', // Azul (Hoteles)
            '#FF6F3C', // Papaya Tropical (Gastronomía)
            '#6f42c1', // Violeta (Vida Nocturna)
            '#d63384', // Rosa (Comercio)
            '#ffc107', // Amarillo Oro
            '#46B7AA'  // Teal Amaz\u00f3nico claro
        ];

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: labelsCat,
                datasets: [{
                    data: valoresCat,
                    backgroundColor: paletaColores.slice(0, labelsCat.length),
                    borderWidth: 0,
                    borderRadius: 7,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0E4D5E',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.parsed;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${val} comercios (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // 3. Filtros y Búsqueda en la Tabla de Recordatorios de Cobro
    const filterBtns = document.querySelectorAll('.filter-pill-btn');
    const inputBuscar = document.getElementById('inputBuscarRecordatorio');
    const filas = Array.from(document.querySelectorAll('.fila-recordatorio'));

    let filtroEstadoActivo = 'todos';

    function filtrarTablaRecordatorios() {
        const termino = (inputBuscar?.value || '').trim().toLowerCase();

        filas.forEach(fila => {
            const estado = fila.getAttribute('data-estado');
            const nombre = fila.getAttribute('data-nombre') || '';
            const cat = fila.getAttribute('data-categoria') || '';

            // Condición por estado
            let coincideEstado = false;
            if (filtroEstadoActivo === 'todos') {
                coincideEstado = true;
            } else if (filtroEstadoActivo === 'urgente') {
                coincideEstado = (estado === 'URGENTE' || estado === 'POR_VENCER');
            } else if (filtroEstadoActivo === 'vencido') {
                coincideEstado = (estado === 'VENCIDO' || estado === 'SIN_PAGO');
            } else if (filtroEstadoActivo === 'al_dia') {
                coincideEstado = (estado === 'AL_DIA');
            }

            // Condición por texto
            const coincideTexto = !termino || nombre.includes(termino) || cat.includes(termino);

            fila.style.display = (coincideEstado && coincideTexto) ? '' : 'none';
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filtroEstadoActivo = btn.getAttribute('data-filtro') || 'todos';
            filtrarTablaRecordatorios();
        });
    });

    inputBuscar?.addEventListener('input', filtrarTablaRecordatorios);

    // 4. Copiar Texto de Recordatorio al Portapapeles
    document.querySelectorAll('.btn-copiar-recordatorio').forEach(btn => {
        btn.addEventListener('click', async () => {
            const mensaje = btn.getAttribute('data-mensaje') || '';
            if (!mensaje) return;

            try {
                await navigator.clipboard.writeText(mensaje);
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2 text-success extra-small"></i>';
                btn.classList.add('btn-success', 'text-white');
                btn.classList.remove('btn-outline-secondary');

                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success', 'text-white');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            } catch (err) {
                alert('Mensaje de recordatorio:\n\n' + mensaje);
            }
        });
    });
});
</script>
