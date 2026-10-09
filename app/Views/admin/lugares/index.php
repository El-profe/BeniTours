<?php
$escape = static fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
$totalLugares = count($lugares);
$hayFiltros = !empty($buscar) || !empty($tipoActual) || !empty($categoriaActual) || !empty($municipioActual) || $estadoActual !== null || $visibilidadActual !== null;
?>

<div class="container-fluid p-0">
    <!-- Encabezado de Sección y Acción Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-bold fs-7">
                    <i class="bi bi-geo-alt-fill me-1"></i> Catálogo Administrativo
                </span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-1">Directorio de Lugares y Negocios</h2>
            <p class="text-muted small mb-0">Administra atractivos turísticos, comercios suscritos, coordenadas y estados de publicación.</p>
        </div>
        <div>
            <a href="<?= $escape($baseUrl) ?>/admin/lugares/crear" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill fs-5"></i>
                <span>Nueva Ficha</span>
            </a>
        </div>
    </div>

    <!-- Mensajes Flash de Sesión -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 small mb-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= $escape($mensaje) ?>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de Métricas KPI -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="place-kpi-card shadow-sm">
                <div class="place-kpi-icon bg-success-subtle text-success">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                    <div class="place-kpi-val"><?= (int)($estadisticas['total'] ?? 0) ?></div>
                    <div class="place-kpi-lbl">Total Fichas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="place-kpi-card shadow-sm">
                <div class="place-kpi-icon bg-primary-subtle text-primary">
                    <i class="bi bi-shop-window"></i>
                </div>
                <div>
                    <div class="place-kpi-val"><?= (int)($estadisticas['comerciales'] ?? 0) ?></div>
                    <div class="place-kpi-lbl">Comercios</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="place-kpi-card shadow-sm">
                <div class="place-kpi-icon bg-info-subtle text-info">
                    <i class="bi bi-tree-fill"></i>
                </div>
                <div>
                    <div class="place-kpi-val"><?= (int)($estadisticas['publicos'] ?? 0) ?></div>
                    <div class="place-kpi-lbl">Atractivos Públicos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="place-kpi-card shadow-sm">
                <div class="place-kpi-icon bg-success text-white">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                    <div class="place-kpi-val"><?= (int)($estadisticas['visibles'] ?? 0) ?></div>
                    <div class="place-kpi-lbl">Visibles en Web</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Centro de Búsqueda y Filtros Avanzados -->
    <div class="place-filters-card p-3 p-md-4 mb-4">
        <form method="GET" action="<?= $escape($baseUrl) ?>/admin/lugares" id="formFiltrosLugares">
            <!-- Barra Superior: Buscador en Vivo Amplio -->
            <div class="row g-2 align-items-center mb-3">
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="place-search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" 
                               name="buscar" 
                               id="inputBuscadorLugares"
                               class="place-search-input" 
                               placeholder="Buscar por nombre, ID (#15), slug, rubro, teléfono o municipio..." 
                               value="<?= $escape($buscar) ?>"
                               autocomplete="off">
                        <button type="button" 
                                id="btnClearSearch" 
                                class="btn-clear-search" 
                                title="Limpiar búsqueda"
                                style="<?= !empty($buscar) ? 'display: flex;' : '' ?>">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 flex-grow-1 shadow-sm">
                        <i class="bi bi-search me-1"></i> Buscar
                    </button>
                    <?php if ($hayFiltros): ?>
                        <a href="<?= $escape($baseUrl) ?>/admin/lugares" class="btn btn-outline-secondary rounded-pill px-3" title="Restablecer todos los filtros">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fila de Filtros Estructurados -->
            <div class="row g-2 align-items-center">
                <!-- Filtro Condición -->
                <div class="col-6 col-md-3">
                    <select name="tipo" id="filtroTipo" class="form-select place-filter-select">
                        <option value="">Condición: Todas</option>
                        <option value="COMERCIAL" <?= $tipoActual === 'COMERCIAL' ? 'selected' : '' ?>>Comercial</option>
                        <option value="PUBLICO" <?= $tipoActual === 'PUBLICO' ? 'selected' : '' ?>>Público Gratuito</option>
                    </select>
                </div>

                <!-- Filtro Categoría -->
                <div class="col-6 col-md-3">
                    <select name="categoria" id="filtroCategoria" class="form-select place-filter-select">
                        <option value="">Categoría: Todas</option>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= (int)$cat['id_categoria'] ?>" <?= $categoriaActual === (int)$cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= $escape($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtro Visibilidad / Estado -->
                <div class="col-6 col-md-3">
                    <select name="visibilidad" id="filtroVisibilidad" class="form-select place-filter-select">
                        <option value="">Visibilidad: Todas</option>
                        <option value="1" <?= $visibilidadActual === 1 ? 'selected' : '' ?>>Visible en Web</option>
                        <option value="0" <?= $visibilidadActual === 0 ? 'selected' : '' ?>>Oculto (Sin abono / suspendido)</option>
                    </select>
                </div>

                <!-- Filtro Municipio -->
                <div class="col-6 col-md-3">
                    <select name="municipio" id="filtroMunicipio" class="form-select place-filter-select">
                        <option value="">Municipio: Todos</option>
                        <?php if (!empty($municipios)): ?>
                            <?php foreach ($municipios as $mun): ?>
                                <option value="<?= (int)$mun['id_municipio'] ?>" <?= $municipioActual === (int)$mun['id_municipio'] ? 'selected' : '' ?>>
                                    <?= $escape($mun['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Resumen y Contador Dinámico -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top flex-wrap gap-2">
                <div class="small text-muted d-flex align-items-center gap-2">
                    <i class="bi bi-funnel-fill text-success"></i>
                    <span id="contadorTexto">
                        Mostrando <strong id="contadorFiltrados" class="text-dark"><?= $totalLugares ?></strong> de <strong class="text-dark"><?= (int)($estadisticas['total'] ?? $totalLugares) ?></strong> lugares registrados
                    </span>
                    <?php if ($hayFiltros): ?>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2 py-1 ms-1">
                            Filtros aplicados
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?= $escape($baseUrl) ?>/admin/lugares" class="btn btn-link btn-sm text-decoration-none text-muted p-0" id="btnRestablecerEnlace" style="<?= !$hayFiltros ? 'display: none;' : '' ?>">
                        <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla Principal de Fichas -->
    <div class="admin-card overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0" id="tablaLugaresAdmin">
                <thead class="bg-light text-muted">
                    <tr>
                        <th style="width: 55px;" class="text-center">#</th>
                        <th style="min-width: 280px;">Establecimiento / Lugar</th>
                        <th>Categoría</th>
                        <th>Condición</th>
                        <th>Publicación</th>
                        <th>Visibilidad Web</th>
                        <th>Vigencia / Abono</th>
                        <th class="text-end" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyLugares">
                    <?php if (!empty($lugares)): ?>
                        <?php foreach ($lugares as $l): ?>
                            <?php 
                                $idLugar = (int)$l['id_lugar'];
                                $esComercial = ($l['tipo_lugar'] ?? '') === 'COMERCIAL';
                                $esVisible = (bool)($l['es_visible'] ?? false);
                                $estaHabilitado = (bool)($l['habilitado'] ?? false);
                                $tieneVigencia = !empty($l['fecha_vencimiento']);
                                $vencimientoFormateado = $tieneVigencia ? date('d/m/Y', strtotime($l['fecha_vencimiento'])) : null;
                                
                                // Datos para búsqueda en cliente (minúsculas sin tildes para instant search)
                                $searchData = mb_strtolower(
                                    $idLugar . ' ' . 
                                    $l['nombre'] . ' ' . 
                                    ($l['slug'] ?? '') . ' ' . 
                                    ($l['categoria'] ?? '') . ' ' . 
                                    ($l['municipio'] ?? '') . ' ' . 
                                    ($l['telefono_contacto'] ?? '') . ' ' . 
                                    ($l['whatsapp_contacto'] ?? '')
                                );
                            ?>
                            <tr class="lugar-fila" 
                                data-id="<?= $idLugar ?>"
                                data-tipo="<?= $escape($l['tipo_lugar']) ?>"
                                data-categoria="<?= (int)($l['id_categoria'] ?? 0) ?>"
                                data-municipio="<?= (int)$l['id_municipio'] ?>"
                                data-habilitado="<?= $estaHabilitado ? '1' : '0' ?>"
                                data-visible="<?= $esVisible ? '1' : '0' ?>"
                                data-search="<?= $escape($searchData) ?>">
                                
                                <!-- ID -->
                                <td class="text-center text-muted fw-bold small">
                                    #<?= $idLugar ?>
                                </td>

                                <!-- Miniatura + Establecimiento -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($l['imagen'])): ?>
                                            <img src="<?= $escape($baseUrl) ?>/imagen?f=<?= urlencode($l['imagen']) ?>" 
                                                 alt="<?= $escape($l['nombre']) ?>" 
                                                 class="place-table-thumb"
                                                 loading="lazy"
                                                 onerror="this.onerror=null; this.src='<?= $escape($baseUrl) ?>/assets/img/logo2.png';">
                                        <?php else: ?>
                                            <div class="place-table-icon">
                                                <i class="bi <?= $escape($l['categoria_icono'] ?? 'bi-geo-alt') ?>"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                                <span><?= $escape($l['nombre']) ?></span>
                                                <?php if ($esVisible): ?>
                                                    <a href="<?= $escape($baseUrl) ?>/catalogo/detalle?slug=<?= urlencode($l['slug']) ?>" 
                                                       target="_blank" 
                                                       class="text-success text-decoration-none" 
                                                       title="Abrir ficha pública en nueva pestaña">
                                                        <i class="bi bi-box-arrow-up-right small"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                <span class="badge bg-light text-secondary border py-0 px-2 small">
                                                    <i class="bi bi-geo-alt me-1 text-success"></i><?= $escape($l['municipio'] ?? 'Trinidad') ?>
                                                </span>
                                                <code class="small text-muted py-0 px-1 bg-light rounded" style="font-size: 0.72rem;">
                                                    <?= $escape($l['slug']) ?>
                                                </code>
                                                <?php if (!empty($l['whatsapp_contacto'])): ?>
                                                    <a href="https://wa.me/591<?= preg_replace('/\D/', '', $l['whatsapp_contacto']) ?>" 
                                                       target="_blank" 
                                                       class="badge bg-success-subtle text-success text-decoration-none py-0 px-2" 
                                                       title="Enviar mensaje de WhatsApp">
                                                        <i class="bi bi-whatsapp me-1"></i><?= $escape($l['whatsapp_contacto']) ?>
                                                    </a>
                                                <?php elseif (!empty($l['telefono_contacto'])): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary py-0 px-2">
                                                        <i class="bi bi-telephone me-1"></i><?= $escape($l['telefono_contacto']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Categoría -->
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi <?= $escape($l['categoria_icono'] ?? 'bi-tag') ?> text-success"></i>
                                        <span><?= $escape($l['categoria']) ?></span>
                                    </span>
                                </td>

                                <!-- Condición -->
                                <td>
                                    <?php if ($esComercial): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-shop me-1"></i> Comercial
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-tree me-1"></i> Público Gratuito
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Publicación (Habilitado / Deshabilitado) -->
                                <td>
                                    <?php if ($estaHabilitado): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i> Habilitado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-semibold" title="<?= $escape($l['motivo_suspension'] ?? 'Deshabilitado') ?>">
                                            <i class="bi bi-slash-circle-fill me-1"></i> Suspendido
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Visibilidad Web -->
                                <td>
                                    <?php if ($esVisible): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1 fw-semibold shadow-sm">
                                            <i class="bi bi-eye-fill me-1"></i> Visible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-1 fw-semibold">
                                            <i class="bi bi-eye-slash-fill me-1"></i> Oculto
                                        </span>
                                        <?php if ($esComercial && !$tieneVigencia): ?>
                                            <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">(Sin abono activo)</small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Vigencia de Pago -->
                                <td>
                                    <?php if ($esComercial): ?>
                                        <?php if ($tieneVigencia): ?>
                                            <?php 
                                                $estaVencido = strtotime($l['fecha_vencimiento']) < time();
                                            ?>
                                            <div class="small">
                                                <span class="badge <?= $estaVencido ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?> rounded-pill px-2 py-1 fw-bold">
                                                    <?= $estaVencido ? 'Vencido' : 'Al día' ?>
                                                </span>
                                                <small class="d-block text-muted mt-1 font-monospace" style="font-size: 0.72rem;">
                                                    Vence: <?= $vencimientoFormateado ?>
                                                </small>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                Sin pago registrado
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">No aplica (Público)</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Acciones -->
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Enlace Ver Público -->
                                        <?php if ($esVisible): ?>
                                            <a href="<?= $escape($baseUrl) ?>/catalogo/detalle?slug=<?= urlencode($l['slug']) ?>" 
                                               target="_blank" 
                                               class="btn btn-light btn-sm border action-btn-circle" 
                                               title="Ver en el portal público">
                                                <i class="bi bi-arrow-up-right text-success"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Ficha 360 si es comercial -->
                                        <?php if ($esComercial): ?>
                                            <a href="<?= $escape($baseUrl) ?>/admin/comercios/detalle?id=<?= $idLugar ?>" 
                                               class="btn btn-success-subtle btn-sm border border-success-subtle action-btn-circle text-success" 
                                               title="Centro Operativo y Seguimiento 360">
                                                <i class="bi bi-activity"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Botón Editar -->
                                        <a href="<?= $escape($baseUrl) ?>/admin/lugares/editar?id=<?= $idLugar ?>" 
                                           class="btn btn-light btn-sm border action-btn-circle" 
                                           title="Editar Información">
                                            <i class="bi bi-pencil text-dark"></i>
                                        </a>

                                        <!-- Toggle Habilitar / Deshabilitar -->
                                        <form action="<?= $escape($baseUrl) ?>/admin/lugares/cambiar-habilitacion" method="POST" class="d-inline m-0">
                                            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                                            <input type="hidden" name="id_lugar" value="<?= $idLugar ?>">
                                            <input type="hidden" name="habilitado" value="<?= $estaHabilitado ? '0' : '1' ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm action-btn-circle <?= $estaHabilitado ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                                    title="<?= $estaHabilitado ? 'Suspender/Ocultar de la plataforma' : 'Habilitar para publicación' ?>"
                                                    onclick="return confirm('¿Está seguro de <?= $estaHabilitado ? 'suspender' : 'habilitar' ?> este lugar?');">
                                                <i class="bi <?= $estaHabilitado ? 'bi-slash-circle' : 'bi-check-circle' ?>"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="filaSinLugares">
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <div class="place-kpi-icon bg-light text-muted mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No se encontraron lugares registrados</h5>
                                    <p class="small text-muted mb-3">No existen registros que coincidan con los criterios de búsqueda actuales.</p>
                                    <a href="<?= $escape($baseUrl) ?>/admin/lugares" class="btn btn-sm btn-outline-success rounded-pill px-4">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer Filtros
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Fila oculta para estado vacío dinámico en vivo (JS) -->
                    <tr id="filaSinResultadosLive" style="display: none;">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <div class="place-kpi-icon bg-light text-muted mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.8rem;">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Sin coincidencias con la búsqueda</h6>
                                <p class="small text-muted mb-3" id="textoBusquedaFallida">Ningún lugar coincide con el término ingresado.</p>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-4" id="btnRestablecerLive">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ver todos los lugares
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Credenciales Generadas -->
<?php require dirname(__DIR__) . '/parciales/modal_credenciales.php'; ?>

<script src="<?= $escape($baseUrl) ?>/assets/js/admin/credenciales-modal.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/admin/credenciales-modal.js') ?>" defer></script>

<?php if (!empty($credencialesGeneradas)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const datos = <?= json_encode($credencialesGeneradas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        if (window.BeniTursModalCredenciales) {
            window.BeniTursModalCredenciales.mostrar(datos);
        } else {
            setTimeout(function() {
                if (window.BeniTursModalCredenciales) {
                    window.BeniTursModalCredenciales.mostrar(datos);
                }
            }, 300);
        }
    });
</script>
<?php endif; ?>

<!-- Script de Búsqueda y Filtrado Instantáneo en Vivo (Cliente) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputSearch = document.getElementById('inputBuscadorLugares');
    const btnClear = document.getElementById('btnClearSearch');
    const selectTipo = document.getElementById('filtroTipo');
    const selectCat = document.getElementById('filtroCategoria');
    const selectVis = document.getElementById('filtroVisibilidad');
    const selectMun = document.getElementById('filtroMunicipio');
    const tbody = document.getElementById('tbodyLugares');
    const filas = Array.from(tbody.querySelectorAll('tr.lugar-fila'));
    const filaSinResultados = document.getElementById('filaSinResultadosLive');
    const contadorFiltrados = document.getElementById('contadorFiltrados');
    const btnRestablecerLive = document.getElementById('btnRestablecerLive');
    const textoBusquedaFallida = document.getElementById('textoBusquedaFallida');

    // Normalizador para ignorar tildes, mayúsculas y caracteres especiales
    function normalizar(texto) {
        return (texto || '')
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function aplicarFiltrosEnVivo() {
        const query = normalizar(inputSearch.value);
        const valTipo = selectTipo.value;
        const valCat = selectCat.value;
        const valVis = selectVis.value;
        const valMun = selectMun.value;

        // Mostrar / ocultar botón de limpiar (X)
        if (btnClear) {
            btnClear.style.display = inputSearch.value.trim() !== '' ? 'flex' : 'none';
        }

        let visibles = 0;

        filas.forEach(function(fila) {
            const dataSearch = normalizar(fila.getAttribute('data-search'));
            const dataId = fila.getAttribute('data-id');
            const dataTipo = fila.getAttribute('data-tipo');
            const dataCat = fila.getAttribute('data-categoria');
            const dataVis = fila.getAttribute('data-visible');
            const dataMun = fila.getAttribute('data-municipio');

            let coincideTexto = true;
            if (query !== '') {
                const queryId = query.replace(/^#/, '');
                coincideTexto = dataSearch.includes(query) || (queryId !== '' && dataId === queryId);
            }

            const coincideTipo = (valTipo === '' || dataTipo === valTipo);
            const coincideCat  = (valCat === '' || dataCat === valCat);
            const coincideVis  = (valVis === '' || dataVis === valVis);
            const coincideMun  = (valMun === '' || dataMun === valMun);

            if (coincideTexto && coincideTipo && coincideCat && coincideVis && coincideMun) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        if (contadorFiltrados) {
            contadorFiltrados.textContent = visibles;
        }

        if (filaSinResultados) {
            if (visibles === 0 && filas.length > 0) {
                filaSinResultados.style.display = '';
                if (textoBusquedaFallida) {
                    textoBusquedaFallida.textContent = query !== '' 
                        ? 'No se encontraron resultados para "' + inputSearch.value + '".'
                        : 'No hay lugares que coincidan con los filtros seleccionados.';
                }
            } else {
                filaSinResultados.style.display = 'none';
            }
        }
    }

    if (inputSearch) {
        inputSearch.addEventListener('input', aplicarFiltrosEnVivo);
    }

    if (btnClear) {
        btnClear.addEventListener('click', function() {
            inputSearch.value = '';
            inputSearch.focus();
            aplicarFiltrosEnVivo();
        });
    }

    [selectTipo, selectCat, selectVis, selectMun].forEach(function(select) {
        if (select) {
            select.addEventListener('change', aplicarFiltrosEnVivo);
        }
    });

    if (btnRestablecerLive) {
        btnRestablecerLive.addEventListener('click', function() {
            if (inputSearch) inputSearch.value = '';
            if (selectTipo) selectTipo.value = '';
            if (selectCat) selectCat.value = '';
            if (selectVis) selectVis.value = '';
            if (selectMun) selectMun.value = '';
            aplicarFiltrosEnVivo();
        });
    }
});
</script>
