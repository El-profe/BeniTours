<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

<div class="container-fluid p-0">
    <!-- Encabezado Hero Superior -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                    <i class="bi bi-shield-check me-1"></i> Publicación Segura
                </span>
                <span class="text-muted small">&bull; Nueva Ficha en Directorio</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Crear Nueva Ficha Turística o Comercial</h1>
            <p class="text-muted small mb-0">Configura la identidad, categoría, galería y geolocalización del establecimiento.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/lugares" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Volver al Directorio
            </a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <!-- Formulario Principal -->
    <form id="formCrearFicha" action="<?= htmlspecialchars($baseUrl) ?>/admin/lugares/guardar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="foto_portada" id="fotoPortada" value="0">

        <div class="row g-4">
            <!-- COLUMNA PRINCIPAL DE DATOS (8 Columnas) -->
            <div class="col-lg-8">
                
                <!-- SECCIÓN 1: Identidad y Tipo de Ficha -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-card-heading"></i>
                        </div>
                        <div>
                            <h2 class="form-section-title">Identidad del Establecimiento</h2>
                            <p class="form-section-desc">Define el nombre oficial, municipio de origen y modalidad de ficha.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Nombre del Lugar -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-dark">Nombre del Establecimiento o Atractivo *</label>
                            <input type="text" name="nombre" id="inputNombreLugar" class="form-control form-control-lg fs-6 rounded-3" placeholder="Ej. Restaurante El Tamarindo / Museo Ictícola" required autocomplete="off">
                        </div>

                        <!-- Municipio -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Municipio del Beni *</label>
                            <select name="id_municipio" id="selectMunicipio" class="form-select form-select-lg fs-6 rounded-3" required>
                                <?php foreach ($municipios as $m): ?>
                                    <option value="<?= $m['id_municipio'] ?>" <?= ((int)$m['id_municipio'] === 1) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['provincia']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Selector de Tipo: Público vs Comercial -->
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold small text-dark d-block">Tipo de Ficha en Plataforma *</label>
                            <div class="type-choice-container">
                                <label class="type-choice-card">
                                    <input type="radio" name="tipo_lugar" value="PUBLICO" checked>
                                    <div class="type-choice-icon">
                                        <i class="bi bi-tree-fill"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark small">Público / Municipal</strong>
                                        <small class="text-muted extra-small">Plaza, museo, laguna o atractivo libre</small>
                                    </div>
                                </label>
                                <label class="type-choice-card">
                                    <input type="radio" name="tipo_lugar" value="COMERCIAL">
                                    <div class="type-choice-icon">
                                        <i class="bi bi-shop-window"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark small">Comercial / Negocio</strong>
                                        <small class="text-muted extra-small">Hotel, restaurante, café o comercio afiliado</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 1.1: Aprovisionamiento Comercial (Condicional si es Comercial) -->
                <div class="form-section-card border-success-subtle bg-success-subtle bg-opacity-10 d-none" id="seccionAprovisionamientoComercial">
                    <div class="form-section-header border-success-subtle pb-3 mb-3">
                        <div class="form-section-icon bg-success text-white shadow-xs">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h2 class="form-section-title text-success mb-0 fw-bold">Membresía Comercial y Cobro Inicial</h2>
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-semibold">Alta Directa de Comerciante</span>
                            </div>
                            <p class="form-section-desc mb-0">Genera credenciales de acceso, cobra el primer periodo y activa la visibilidad en un solo paso.</p>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3 p-3 bg-white rounded-4 border shadow-xs d-flex align-items-center justify-content-between">
                        <div>
                            <label class="form-check-label fw-bold text-dark mb-0 d-block" for="switchCrearCuenta">
                                Aprovisionar cuenta comercial y registrar cobro de inmediato
                            </label>
                            <small class="text-muted extra-small">Si está activo, se emitirá el recibo, credenciales automáticas y la vigencia inicial.</small>
                        </div>
                        <input class="form-check-input fs-4 ms-3" type="checkbox" role="switch" name="crear_cuenta" id="switchCrearCuenta" value="1" checked>
                    </div>

                    <div class="row g-3" id="camposAprovisionamiento">
                        <!-- Campos ocultos de plan y monto -->
                        <input type="hidden" name="tipo_plan_modalidad" id="lugar_tipo_plan_modalidad" value="ESTANDAR">
                        <input type="hidden" name="codigo_plan" id="lugar_codigo_plan" value="MENSUAL">
                        <input type="hidden" name="meses_duracion" id="lugar_meses_duracion" value="1">
                        <input type="hidden" name="id_tarifa" id="lugar_id_tarifa" value="<?= (int)($tarifaVigente['id_tarifa'] ?? 1) ?>">

                        <!-- Botonera de Selección de Modo de Plan -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="bi bi-calendar-check text-success me-1"></i>Plan de Membresía & Periodo Inicial
                                </label>
                                <span class="badge bg-white text-muted border rounded-pill px-3 py-1">Tarifa Base: Bs <?= number_format((float)($tarifaVigente['monto_mensual'] ?? 250.00), 2) ?>/mes</span>
                            </div>

                            <div class="btn-group w-100 p-1 bg-white rounded-4 border mb-3 shadow-xs" role="group">
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-lugar active py-2" id="btnModoLugarEstandar" onclick="seleccionarModoPlanLugar('ESTANDAR')">
                                    <i class="bi bi-grid-fill me-1"></i> Planes Predefinidos
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-lugar py-2" id="btnModoLugarPersonalizado" onclick="seleccionarModoPlanLugar('PERSONALIZADO')">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Plan Personalizado (A Medida)
                                </button>
                            </div>

                            <!-- Sección A: Planes Rápidos Predefinidos -->
                            <div id="seccion-lugar-planes-estandar">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-3 text-center rounded-4 cursor-pointer active" id="cardPlanLugar1" onclick="elegirPlanEstandarLugar(1, 250.00, 1, 0, 'MENSUAL')">
                                            <span class="badge bg-light text-secondary rounded-pill extra-small mb-2 fw-semibold">Básico</span>
                                            <div class="fw-bold text-dark fs-6 mb-1">1 Mes</div>
                                            <div class="text-success fw-bold fs-5">Bs 250</div>
                                            <small class="text-muted extra-small d-block mt-1">Tarifa mensual</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-3 text-center rounded-4 cursor-pointer" id="cardPlanLugar3" onclick="elegirPlanEstandarLugar(3, 712.50, 1, 37.50, 'MENSUAL')">
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill extra-small mb-2 fw-semibold">Trimestral</span>
                                            <div class="fw-bold text-dark fs-6 mb-1">3 Meses</div>
                                            <div class="text-success fw-bold fs-5">Bs 712.50</div>
                                            <span class="badge bg-success-subtle text-success rounded-pill extra-small mt-1 fw-bold">Ahorra 5%</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-3 text-center rounded-4 cursor-pointer" id="cardPlanLugar6" onclick="elegirPlanEstandarLugar(6, 1350.00, 1, 150.00, 'MENSUAL')">
                                            <span class="badge bg-primary-subtle text-primary rounded-pill extra-small mb-2 fw-semibold">Semestral</span>
                                            <div class="fw-bold text-dark fs-6 mb-1">6 Meses</div>
                                            <div class="text-success fw-bold fs-5">Bs 1,350</div>
                                            <span class="badge bg-success-subtle text-success rounded-pill extra-small mt-1 fw-bold">Ahorra 10%</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-3 text-center rounded-4 cursor-pointer border-warning" id="cardPlanLugar12" onclick="elegirPlanEstandarLugar(12, 2500.00, 2, 500.00, 'ANUAL')">
                                            <span class="badge bg-warning text-dark rounded-pill extra-small mb-2 fw-bold">★ Recomendado</span>
                                            <div class="fw-bold text-dark fs-6 mb-1">12 Meses</div>
                                            <div class="text-success fw-bold fs-5">Bs 2,500</div>
                                            <span class="badge bg-warning text-dark rounded-pill extra-small mt-1 fw-bold">Ahorra Bs 500</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección B: Plan Personalizado (Meses libres + Descuento) -->
                            <div id="seccion-lugar-plan-personalizado" class="p-3 rounded-4 border border-2 border-warning-subtle bg-white shadow-xs" style="display: none;">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold extra-small text-dark mb-1">Cantidad de Meses contratados:</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cambiarMesesRelativoLugar(-1)"><i class="bi bi-dash-lg"></i></button>
                                            <input type="number" id="inputMesesLugar" min="1" max="120" value="5" class="form-control form-control-sm text-center fw-bold text-primary fs-5" oninput="recalcularPlanPersonalizadoLugar()">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cambiarMesesRelativoLugar(1)"><i class="bi bi-plus-lg"></i></button>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap mt-1">
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMesesLugar(2)">2m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMesesLugar(4)">4m</button>
                                            <button type="button" class="btn btn-warning btn-sm border border-warning py-0 px-2 extra-small fw-bold" onclick="fijarMesesLugar(5)">5 meses</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMesesLugar(7)">7m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMesesLugar(8)">8m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMesesLugar(10)">10m</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold extra-small text-dark mb-1">Descuento o Beneficio de Ahorro:</label>
                                        <select id="selectReglaLugar" class="form-select form-select-sm rounded-3" onchange="recalcularPlanPersonalizadoLugar()">
                                            <option value="0">Sin descuento (Tarifa regular 100%)</option>
                                            <option value="5">5% de Descuento comercial</option>
                                            <option value="10" selected>10% de Descuento preferencial (Sugerido)</option>
                                            <option value="15">15% de Descuento fidelidad</option>
                                            <option value="20">20% de Descuento especial</option>
                                            <option value="mes_gratis">1 Mes de regalo (Descuenta 1 mes)</option>
                                            <option value="manual">Fijar importe pactado manualmente en Bs</option>
                                        </select>
                                        <small class="text-muted extra-small">Calcula el ahorro exacto en tiempo real.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta Dinámica de Liquidación & Cobro POS -->
                        <div class="col-12">
                            <div class="p-3 p-md-4 bg-white rounded-4 border shadow-sm">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7">
                                        <div class="d-flex justify-content-between align-items-center mb-2 text-muted extra-small">
                                            <span>Precio regular (<span id="txt-lugar-calc-meses">1</span> mes(es) × Bs <?= number_format((float)($tarifaVigente['monto_mensual'] ?? 250.00), 2) ?>):</span>
                                            <span class="text-decoration-line-through fw-bold" id="txt-lugar-precio-regular">Bs 250.00</span>
                                        </div>

                                        <div id="caja-ahorro-lugar" class="alert alert-light py-2 px-3 mb-2 d-flex align-items-center gap-2 rounded-3 border">
                                            <i class="bi bi-piggy-bank-fill fs-4 text-success flex-shrink-0"></i>
                                            <div>
                                                <div class="fw-bold text-success small" id="txt-lugar-ahorro-banner">
                                                    Tarifa regular estándar (Bs 250.00/mes)
                                                </div>
                                                <div class="extra-small text-muted">
                                                    Equivale a pagar <strong class="text-success" id="txt-lugar-costo-mensual">Bs 250.00</strong> por mes.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="extra-small text-muted">
                                            <i class="bi bi-calendar-range text-primary me-1"></i>
                                            <span id="txt-lugar-proyeccion">Vigencia inicial proyectada...</span>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="p-3 bg-light rounded-4 border text-center">
                                            <label class="form-label fw-bold extra-small text-uppercase text-secondary mb-1 d-block">
                                                Importe Inicial a Cobrar
                                            </label>
                                            <div class="input-group input-group-lg justify-content-center">
                                                <span class="input-group-text bg-white text-success fw-bold border-end-0 rounded-start-pill">Bs</span>
                                                <input type="number" step="0.01" name="monto" id="inputLugarMonto" class="form-control fw-bold fs-4 text-success border-start-0 text-center rounded-end-pill bg-white" value="250.00" required oninput="alCambiarMontoLugarManual()">
                                            </div>
                                            <small class="text-muted extra-small d-block mt-1">Monto que quedará registrado en el recibo de alta.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Método de Cobro y Comprobante / Recibo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">
                                <i class="bi bi-wallet2 text-success me-1"></i>Método de Cobro *
                            </label>
                            <select name="metodo_pago" id="selectMetodoPagoLugar" class="form-select rounded-3">
                                <option value="EFECTIVO" selected>💵 Efectivo (Cobrado en mano)</option>
                                <option value="QR">📱 QR Simple</option>
                                <option value="TRANSFERENCIA">🏦 Transferencia Bancaria</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">
                                <i class="bi bi-hash text-secondary me-1"></i>Nº Recibo / Talonario / Ref.
                            </label>
                            <input type="text" name="numero_comprobante" class="form-control rounded-3" placeholder="Ej. REC-001 o Nº Transacción (Opcional)">
                        </div>

                        <?php
                            $qrPathLugar = !empty($cobro['qr_url']) ? $cobro['qr_url'] : '/assets/img/QR-Prueba.jpeg';
                            $qrFullPathLugar = $baseUrl . $qrPathLugar;
                        ?>

                        <!-- Contenedor Unificado: Código QR y Carga de Comprobante (Para QR y Transferencia) -->
                        <div class="col-12" id="contenedorComprobantePago" style="display: none;">
                            <div class="p-3 p-md-4 rounded-4 bg-white border border-primary-subtle shadow-xs">
                                
                                <!-- Inputs file ocultos (para archivo y cámara directa) -->
                                <input type="file" name="comprobante_pago" id="inputComprobantePago" class="d-none" accept="image/jpeg,image/png,image/webp">
                                <input type="file" id="inputComprobanteCamara" class="d-none" accept="image/*" capture="environment">

                                <div class="row align-items-center g-3">
                                    
                                    <!-- QR Oficial BeniTours (Visible en modo QR) -->
                                    <div class="col-12 col-md-auto text-center" id="columnaQrMembresia">
                                        <div class="bg-light p-2 rounded-4 border d-inline-block shadow-2xs">
                                            <img src="<?= htmlspecialchars($qrFullPathLugar) ?>" 
                                                 alt="QR Oficial BeniTours" 
                                                 class="img-fluid rounded-3 qr-img-responsive" 
                                                 id="imgQrMembresia">
                                        </div>
                                        <?php if (!empty($cobro['banco'])): ?>
                                            <div class="mt-1 extra-small text-muted fw-semibold d-block">
                                                <?= htmlspecialchars($cobro['banco']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Acciones de Comprobante: Botones y Previsualización -->
                                    <div class="col-12 col-md">
                                        <div class="d-flex flex-column justify-content-center h-100">
                                            <!-- Botones Subir Archivo y Sacar Foto -->
                                            <div class="d-flex flex-wrap gap-2 mb-2 justify-content-center justify-content-md-start">
                                                <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-xs" id="btnSubirComprobanteArchivo">
                                                    <i class="bi bi-folder2-open me-1"></i> Subir Archivo
                                                </button>
                                                <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-xs" id="btnTomarFotoComprobante">
                                                    <i class="bi bi-camera-fill me-1"></i> Sacar Foto
                                                </button>
                                                <button type="button" class="btn btn-outline-danger rounded-pill px-2 py-2 d-none shadow-xs" id="btnEliminarComprobante" title="Quitar comprobante">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Nombre de archivo seleccionado -->
                                            <div id="nombreArchivoComprobante" class="extra-small text-truncate text-secondary fw-semibold mb-2 text-center text-md-start" style="max-width: 100%;"></div>

                                            <!-- Vista Previa / Estado del Comprobante -->
                                            <div id="previewComprobanteBox" class="d-none text-center text-md-start">
                                                <img src="" id="imgPreviewComprobante" alt="Comprobante" class="rounded-3 border shadow-sm" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                            </div>
                                            <div id="placeholderComprobanteBox" class="p-2 p-md-3 text-muted border border-dashed rounded-3 bg-light extra-small text-center text-md-start">
                                                <i class="bi bi-receipt text-secondary me-1"></i> Sin comprobante adjuntado aún
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Estilos inline responsivos para el QR -->
                        <style>
                            .qr-img-responsive {
                                width: 170px;
                                height: 170px;
                                object-fit: contain;
                            }
                            @media (max-width: 767.98px) {
                                .qr-img-responsive {
                                    width: 240px;
                                    height: 240px;
                                }
                            }
                        </style>
                    </div>
                </div>

                <!-- SECCIÓN 2: Categoría & Detalles del Servicio -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <div>
                            <h2 class="form-section-title">Clasificación & Descripción</h2>
                            <p class="form-section-desc">Selecciona la categoría temática y detalla qué ofrece este lugar.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Selector de Categorías Compacto -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark d-block">Categoría del Catálogo *</label>
                            <div class="category-pill-grid">
                                <?php foreach ($categorias as $indice => $c): ?>
                                    <label class="category-pill-card">
                                        <input type="radio" name="id_categoria" value="<?= (int)$c['id_categoria'] ?>" data-nombre-cat="<?= htmlspecialchars($c['nombre']) ?>" <?= $indice === 0 ? 'checked' : '' ?> required>
                                        <i class="bi <?= htmlspecialchars($c['icono'] ?? 'bi-tag') ?> category-pill-icon"></i>
                                        <span class="category-pill-name" title="<?= htmlspecialchars($c['nombre']) ?>">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Horario de Atención -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Horario de Atención</label>
                            <input type="text" name="horario_atencion" class="form-control rounded-3" placeholder="Ej. Lunes a Sábado, 08:00 a 22:00">
                        </div>

                        <!-- Descripción Completa -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-dark">Descripción Completa *</label>
                            <textarea name="descripcion" id="inputDescripcionLugar" rows="3" class="form-control rounded-3" placeholder="Describe los atractivos, historia, platos típicos o servicios especiales que brinda el lugar..." required></textarea>
                            <small class="text-muted extra-small">Una buena descripción mejora la visibilidad en las búsquedas turísticas.</small>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: Galería Fotográfica y Portada -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-images"></i>
                        </div>
                        <div>
                            <h2 class="form-section-title">Galería Fotográfica & Portada</h2>
                            <p class="form-section-desc">Sube hasta 6 fotos de alta calidad. La primera imagen o la que marques con estrella será la portada.</p>
                        </div>
                    </div>

                    <label for="fotografias" class="dropzone-modern">
                        <div class="dropzone-modern-icon">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                        <strong class="text-dark fs-6">Haz clic aquí para seleccionar fotografías</strong>
                        <small class="text-muted">Formatos admitidos: JPG, PNG o WEBP &bull; Máx. 5 MB cada una &bull; Hasta 6 imágenes</small>
                    </label>
                    <input id="fotografias" type="file" name="fotografias[]" class="d-none" accept="image/jpeg,image/png,image/webp" multiple>

                    <!-- Previsualización interactiva con selección de portada -->
                    <div id="previsualizacionFotos" class="image-preview-grid mt-3" aria-live="polite"></div>
                </div>

                <!-- SECCIÓN 4: Ubicación y Contacto -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <div class="form-section-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h2 class="form-section-title">Ubicación & Canales de Contacto</h2>
                            <p class="form-section-desc">Dirección física, geolocalización satelital y números de atención al visitante.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-dark">Dirección Física Exacta *</label>
                            <input type="text" name="direccion" id="inputDireccion" class="form-control rounded-3" placeholder="Ej. Calle Cipriano Barace #120 entre La Paz y Sucre" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-dark">Punto de Referencia</label>
                            <input type="text" name="referencia_ubicacion" class="form-control rounded-3" placeholder="Ej. A dos cuadras de la Plaza Mariscal José Ballivián">
                        </div>

                        <!-- Selector GPS Mapa / Manual -->
                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label fw-bold small text-dark mb-0">Geolocalización GPS</label>
                                <div class="location-mode-tabs" role="tablist">
                                    <button type="button" class="active" data-location-mode="manual">
                                        <i class="bi bi-keyboard me-1"></i> Coordenadas Manuales
                                    </button>
                                    <button type="button" data-location-mode="mapa">
                                        <i class="bi bi-map-fill me-1"></i> Marcar en el Mapa
                                    </button>
                                </div>
                            </div>

                            <div id="ubicacionManual" class="mt-2">
                                <input type="text" id="coordenadasGps" name="coordenadas_gps" class="form-control font-monospace rounded-3" placeholder="Ej. -14.8333, -64.9000">
                                <small class="text-muted extra-small">Formato requerido: latitud, longitud (separadas por coma).</small>
                            </div>

                            <div id="ubicacionMapa" class="d-none mt-2">
                                <div id="mapaNuevaFicha" class="location-picker-map shadow-sm"></div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small id="estadoMapaNuevaFicha" class="text-muted fw-semibold">Haz clic en el mapa o arrastra el pin para fijar la posición.</small>
                                    <span class="badge bg-light text-dark border">Trinidad, Beni</span>
                                </div>
                            </div>
                        </div>

                        <!-- Canales de Comunicación -->
                        <div class="col-md-4 mt-3">
                            <label class="form-label fw-bold small text-dark"><i class="bi bi-whatsapp text-success me-1"></i>WhatsApp</label>
                            <input type="text" name="whatsapp_contacto" class="form-control rounded-3" placeholder="Ej. 59170000000">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label fw-bold small text-dark"><i class="bi bi-telephone-fill text-primary me-1"></i>Teléfono Fijo</label>
                            <input type="text" name="telefono_contacto" class="form-control rounded-3" placeholder="Ej. 34620000">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label fw-bold small text-dark"><i class="bi bi-envelope-fill text-secondary me-1"></i>Correo Electrónico</label>
                            <input type="email" name="email_contacto" class="form-control rounded-3" placeholder="contacto@ejemplo.bo">
                        </div>
                    </div>
                </div>

            </div>

            <!-- COLUMNA LATERAL (Sidebar Sticky - 4 Columnas) -->
            <div class="col-lg-4">
                <div class="sticky-sidebar-box">

                    <!-- Tarjeta de Guardado Rápido -->
                    <div class="form-section-card p-3 mb-3">
                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                            <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                            <span>Guardar y Publicar Ficha</span>
                        </button>
                        <div class="text-center">
                            <small class="text-muted extra-small">
                                <i class="bi bi-shield-check text-success me-1"></i>La ficha se registrará habilitada en el catálogo.
                            </small>
                        </div>
                    </div>

                    <!-- Miniatura Live Preview -->
                    <div class="live-preview-card mb-3">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark"><i class="bi bi-eye-fill text-success me-1"></i> Vista Previa en Vivo</span>
                            <span class="badge bg-light text-muted border extra-small">Catálogo Público</span>
                        </div>
                        <div class="live-preview-img-box" id="previewImgBox">
                            <div class="text-center text-muted small p-3" id="previewPlaceholderIcon">
                                <i class="bi bi-image fs-1 d-block mb-1 opacity-50"></i>
                                <span>Sin fotografía de portada</span>
                            </div>
                            <img id="previewImgElement" src="" alt="Portada" style="display: none;">
                            <span class="live-preview-badge-type bg-primary text-white" id="previewTypeBadge">
                                <i class="bi bi-tree me-1"></i>Público
                            </span>
                            <span class="live-preview-badge-cat" id="previewCatBadge">
                                General
                            </span>
                        </div>
                        <div class="live-preview-body">
                            <h3 class="live-preview-title" id="previewTitle">Nombre del Establecimiento</h3>
                            <div class="live-preview-meta mb-2">
                                <i class="bi bi-geo-alt text-danger"></i>
                                <span id="previewMunicipio">Trinidad</span> &bull; <span id="previewDireccion">Dirección pendiente</span>
                            </div>
                            <p class="text-muted extra-small mb-0 text-truncate" id="previewDesc">
                                Descripción preliminar de la ficha turística...
                            </p>
                        </div>
                    </div>

                    <!-- Tips de Calidad para el Administrador -->
                    <div class="card border-0 rounded-4 bg-light p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-lightbulb-fill text-warning fs-5"></i>
                            <h4 class="h6 fw-bold text-dark mb-0">Recomendaciones de Calidad</h4>
                        </div>
                        <ul class="text-muted extra-small ps-3 mb-0 d-flex flex-column gap-2">
                            <li><strong>Fotos Horizontales:</strong> Procura fotos nítidas con buena luz natural para la portada.</li>
                            <li><strong>Contacto Claro:</strong> Si es comercio, un número con WhatsApp activo multiplica las consultas.</li>
                            <li><strong>GPS Preciso:</strong> Haz clic en el mapa para que turistas lleguen guiados por Google Maps.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/shared/compresor-imagen.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/shared/compresor-imagen.js') ?>"></script>
<script src="<?= htmlspecialchars($baseUrl) ?>/assets/js/admin/crear-lugar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/public/assets/js/admin/crear-lugar.js') ?>" defer></script>

<style>
.card-plan-lugar {
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    background: #ffffff;
    border: 2px solid #e2e8f0;
    cursor: pointer;
}
.card-plan-lugar:hover {
    border-color: #0d9488;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(13, 148, 136, 0.08);
}
.card-plan-lugar.active {
    border-color: #0d9488 !important;
    background-color: #f0fdfa !important;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.25), 0 8px 18px rgba(13, 148, 136, 0.12) !important;
    transform: translateY(-2px);
}
.btn-modo-lugar.active {
    background-color: #ffffff !important;
    color: #0f766e !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.qr-membresia-panel {
    background: linear-gradient(135deg, #f0fdfa, #e6fffa);
    border: 1px solid #99f6e4;
}
</style>

<script>
const TARIFA_BASE_LUGAR = <?= (float)($tarifaVigente['monto_mensual'] ?? 250.00) ?>;
let modoPlanLugar = 'ESTANDAR';
let mesesPlanLugar = 1;

function seleccionarModoPlanLugar(modo) {
    modoPlanLugar = modo;
    document.getElementById('lugar_tipo_plan_modalidad').value = modo;

    const btnEst = document.getElementById('btnModoLugarEstandar');
    const btnPer = document.getElementById('btnModoLugarPersonalizado');
    const secEst = document.getElementById('seccion-lugar-planes-estandar');
    const secPer = document.getElementById('seccion-lugar-plan-personalizado');

    if (modo === 'ESTANDAR') {
        btnEst.classList.add('active');
        btnPer.classList.remove('active');
        secEst.style.display = 'block';
        secPer.style.display = 'none';
        elegirPlanEstandarLugar(1, TARIFA_BASE_LUGAR, 1, 0, 'MENSUAL');
    } else {
        btnPer.classList.add('active');
        btnEst.classList.remove('active');
        secEst.style.display = 'none';
        secPer.style.display = 'block';
        document.querySelectorAll('.card-plan-lugar').forEach(c => c.classList.remove('active'));
        document.getElementById('lugar_codigo_plan').value = 'PERSONALIZADO';
        fijarMesesLugar(5);
    }
}

function elegirPlanEstandarLugar(meses, monto, idTarifa, ahorro, codigoPlan) {
    mesesPlanLugar = meses;
    document.querySelectorAll('.card-plan-lugar').forEach(c => c.classList.remove('active'));
    document.getElementById('cardPlanLugar' + meses)?.classList.add('active');

    document.getElementById('lugar_meses_duracion').value = meses;
    document.getElementById('lugar_id_tarifa').value = idTarifa;
    document.getElementById('lugar_codigo_plan').value = codigoPlan;
    document.getElementById('inputLugarMonto').value = Number(monto).toFixed(2);

    actualizarDesgloseAhorroLugar(meses, monto, ahorro);
}

function cambiarMesesRelativoLugar(delta) {
    const input = document.getElementById('inputMesesLugar');
    let val = parseInt(input.value || 1, 10) + delta;
    if (val < 1) val = 1;
    if (val > 120) val = 120;
    input.value = val;
    recalcularPlanPersonalizadoLugar();
}

function fijarMesesLugar(meses) {
    const input = document.getElementById('inputMesesLugar');
    input.value = meses;
    recalcularPlanPersonalizadoLugar();
}

function recalcularPlanPersonalizadoLugar() {
    const inputMeses = document.getElementById('inputMesesLugar');
    let meses = parseInt(inputMeses.value || 1, 10);
    if (isNaN(meses) || meses < 1) meses = 1;
    if (meses > 120) meses = 120;
    mesesPlanLugar = meses;

    document.getElementById('lugar_meses_duracion').value = meses;
    document.getElementById('lugar_codigo_plan').value = 'PERSONALIZADO';

    const regla = document.getElementById('selectReglaLugar').value;
    const precioRegular = meses * TARIFA_BASE_LUGAR;
    let montoFinal = precioRegular;
    let ahorro = 0;

    if (regla === 'manual') {
        montoFinal = parseFloat(document.getElementById('inputLugarMonto').value || 0);
        ahorro = Math.max(0, precioRegular - montoFinal);
    } else if (regla === 'mes_gratis') {
        ahorro = TARIFA_BASE_LUGAR;
        montoFinal = Math.max(0, precioRegular - ahorro);
    } else {
        const pct = parseFloat(regla);
        if (pct > 0) {
            ahorro = precioRegular * (pct / 100);
            montoFinal = precioRegular - ahorro;
        } else {
            ahorro = 0;
            montoFinal = precioRegular;
        }
    }

    document.getElementById('inputLugarMonto').value = Number(montoFinal).toFixed(2);
    actualizarDesgloseAhorroLugar(meses, montoFinal, ahorro);
}

function alCambiarMontoLugarManual() {
    if (modoPlanLugar === 'PERSONALIZADO') {
        document.getElementById('selectReglaLugar').value = 'manual';
    }
    const meses = mesesPlanLugar;
    const precioRegular = meses * TARIFA_BASE_LUGAR;
    const montoFinal = parseFloat(document.getElementById('inputLugarMonto').value || 0);
    const ahorro = Math.max(0, precioRegular - montoFinal);
    actualizarDesgloseAhorroLugar(meses, montoFinal, ahorro);
}

function actualizarDesgloseAhorroLugar(meses, monto, ahorro) {
    const precioRegular = meses * TARIFA_BASE_LUGAR;
    const ahorroReal = Math.max(0, ahorro);
    const porcentaje = precioRegular > 0 ? ((ahorroReal / precioRegular) * 100) : 0;
    const costoMensual = meses > 0 ? (monto / meses) : monto;

    document.getElementById('txt-lugar-calc-meses').textContent = meses;
    document.getElementById('txt-lugar-precio-regular').textContent = 'Bs ' + precioRegular.toLocaleString('es-BO', { minimumFractionDigits: 2 });
    document.getElementById('txt-lugar-costo-mensual').textContent = 'Bs ' + costoMensual.toLocaleString('es-BO', { minimumFractionDigits: 2 });

    const caja = document.getElementById('caja-ahorro-lugar');
    const banner = document.getElementById('txt-lugar-ahorro-banner');
    if (ahorroReal > 0) {
        caja.className = 'alert alert-success py-2 px-3 mb-1 d-flex align-items-center gap-2 rounded-3 border border-success-subtle';
        banner.innerHTML = `¡El cliente se ahorra Bs ${ahorroReal.toLocaleString('es-BO', { minimumFractionDigits: 2 })} (${porcentaje.toFixed(1)}%)!`;
    } else {
        caja.className = 'alert alert-light py-2 px-3 mb-1 d-flex align-items-center gap-2 rounded-3 border';
        banner.innerHTML = `Tarifa regular estándar (Bs ${TARIFA_BASE_LUGAR.toFixed(2)}/mes)`;
    }

    // Proyección de vigencia desde hoy
    const hoy = new Date();
    const fin = new Date(hoy);
    fin.setMonth(fin.getMonth() + meses);
    document.getElementById('txt-lugar-proyeccion').innerHTML = `Vigencia inicial: <strong>${hoy.toLocaleDateString('es-BO')}</strong> &rarr; <strong>${fin.toLocaleDateString('es-BO')}</strong> (${meses} mes(es) de cobertura)`;
}

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar siempre con Plan Básico de 1 Mes y Bs 250.00
    elegirPlanEstandarLugar(1, TARIFA_BASE_LUGAR, 1, 0, 'MENSUAL');

    const metodoPago = document.getElementById('selectMetodoPagoLugar');
    const columnaQr = document.getElementById('columnaQrMembresia');
    const contenedorComprobante = document.getElementById('contenedorComprobantePago');
    const inputComprobante = document.getElementById('inputComprobantePago');
    const previewComprobanteBox = document.getElementById('previewComprobanteBox');
    const placeholderComprobanteBox = document.getElementById('placeholderComprobanteBox');
    const imgPreviewComprobante = document.getElementById('imgPreviewComprobante');

    const actualizarMetodoPagoUI = () => {
        const metodo = metodoPago?.value || 'EFECTIVO';
        // Mostrar contenedor si es QR o Transferencia Bancaria
        if (contenedorComprobante) {
            contenedorComprobante.style.display = (metodo === 'QR' || metodo === 'TRANSFERENCIA') ? 'block' : 'none';
        }
        // El QR solo es necesario si el método es QR
        if (columnaQr) {
            columnaQr.style.display = (metodo === 'QR') ? 'block' : 'none';
        }
    };

    metodoPago?.addEventListener('change', actualizarMetodoPagoUI);
    actualizarMetodoPagoUI();

    // Elementos para gestión de comprobante de pago
    const btnSubirArchivo = document.getElementById('btnSubirComprobanteArchivo');
    const btnTomarFoto = document.getElementById('btnTomarFotoComprobante');
    const btnEliminar = document.getElementById('btnEliminarComprobante');
    const inputCamara = document.getElementById('inputComprobanteCamara');
    const nombreArchivo = document.getElementById('nombreArchivoComprobante');

    const mostrarPreviewComprobante = (file) => {
        if (!file) {
            limpiarComprobante();
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            if (imgPreviewComprobante) imgPreviewComprobante.src = e.target.result;
            previewComprobanteBox?.classList.remove('d-none');
            placeholderComprobanteBox?.classList.add('d-none');
            btnEliminar?.classList.remove('d-none');
            if (nombreArchivo) {
                nombreArchivo.textContent = '📎 ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            }
        };
        reader.readAsDataURL(file);
    };

    const limpiarComprobante = () => {
        if (inputComprobante) inputComprobante.value = '';
        if (inputCamara) inputCamara.value = '';
        if (imgPreviewComprobante) imgPreviewComprobante.src = '';
        previewComprobanteBox?.classList.add('d-none');
        placeholderComprobanteBox?.classList.remove('d-none');
        btnEliminar?.classList.add('d-none');
        if (nombreArchivo) nombreArchivo.textContent = '';
    };

    btnSubirArchivo?.addEventListener('click', () => {
        inputComprobante?.click();
    });

    btnTomarFoto?.addEventListener('click', () => {
        inputCamara?.click();
    });

    btnEliminar?.addEventListener('click', () => {
        limpiarComprobante();
    });

    // Evento al seleccionar archivo desde el explorador / galería
    inputComprobante?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            mostrarPreviewComprobante(file);
        } else {
            limpiarComprobante();
        }
    });

    // Evento al tomar foto con la cámara (transfiere el archivo al input principal del formulario)
    inputCamara?.addEventListener('change', function() {
        const file = this.files[0];
        if (file && inputComprobante) {
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                inputComprobante.files = dt.files;
            } catch (err) {
                console.warn('DataTransfer no soportado, manteniendo archivo de cámara', err);
            }
            mostrarPreviewComprobante(file);
        }
    });
});
</script>
