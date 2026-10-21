<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="bi bi-cash-stack text-success me-2"></i>Registrar Pago Comercial
                        </h4>
                        <small class="text-muted">El pago se guardará en estado PENDIENTE para su verificación administrativa</small>
                    </div>
                    <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver a Pagos
                    </a>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!$tarifa): ?>
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> No se encontró una tarifa vigente en el sistema. Se utilizará la tarifa base de referencia (Bs 250.00/mes).
                    </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($baseUrl) ?>/admin/pagos/guardar" method="POST" enctype="multipart/form-data" id="formPagoComercial">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="id_tarifa" id="id_tarifa" value="<?= (int)($tarifa['id_tarifa'] ?? 1) ?>">
                    <input type="hidden" name="meses_duracion" id="meses_duracion" value="1">
                    <input type="hidden" name="tipo_plan_modalidad" id="tipo_plan_modalidad" value="ESTANDAR">

                    <div class="row g-3">
                        <!-- 1. Buscador Inteligente de Comercio con Tarjetas Interactivas -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="bi bi-shop me-1 text-primary"></i>Buscar Comercio Afiliado *
                                </label>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 extra-small">
                                    <?= count($comercios) ?> comercios en Trinidad
                                </span>
                            </div>

                            <!-- Input oculto para el envío del formulario -->
                            <input type="hidden" name="id_lugar" id="pago-lugar" value="<?= $idLugarSeleccionado ?: '' ?>" required>

                            <!-- Bloque de Búsqueda Activa -->
                            <div id="bloqueBuscadorComercio">
                                <div class="input-group input-group-lg shadow-xs rounded-4 overflow-hidden border border-primary-subtle">
                                    <span class="input-group-text bg-white border-0 text-primary ps-3">
                                        <i class="bi bi-search fs-5"></i>
                                    </span>
                                    <input type="text" id="inputBuscarComercio" class="form-control border-0 fs-6 ps-2" 
                                           placeholder="Escribe el nombre del comercio (ej. Framon, El Cuadril, etc.)..." 
                                           autocomplete="off">
                                    <button class="btn btn-white border-0 text-muted pe-3" type="button" id="btnLimpiarBusqueda" title="Limpiar búsqueda" style="display: none;">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                </div>
                                <div class="form-text extra-small text-muted mt-1 ps-1">
                                    Escribe una o varias letras para ver las tarjetas con su foto y fecha de vencimiento.
                                </div>
                            </div>

                            <!-- Tarjeta de Comercio Seleccionado (Se muestra cuando se elige uno) -->
                            <div id="tarjetaComercioFijado" class="p-3 rounded-4 bg-white border border-success shadow-xs d-none">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img id="fijadoFoto" src="" alt="Comercio" class="rounded-3 border shadow-2xs" style="width: 58px; height: 58px; object-fit: cover;">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h5 class="fw-bold text-dark mb-0" id="fijadoNombre">Nombre</h5>
                                                <span class="badge rounded-pill px-2 py-1 extra-small" id="fijadoBadgeEstado">Estado</span>
                                            </div>
                                            <div class="extra-small text-muted mt-1" id="fijadoDetalles">
                                                <i class="bi bi-geo-alt me-1"></i>Trinidad, Beni
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnCambiarComercio">
                                            <i class="bi bi-arrow-repeat me-1"></i> Cambiar Comercio
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenedor Dinámico de Tarjetas de Sugerencias -->
                            <div id="contenedorTarjetasComercios" class="row g-2 mt-1" style="max-height: 380px; overflow-y: auto;">
                                <!-- Se llenan dinámicamente con JavaScript -->
                            </div>

                            <!-- Tarjeta Contextual de Vigencia del Comercio Seleccionado -->
                            <div id="info-vigencia-comercio" class="mt-2" style="display: none;"></div>
                        </div>

                        <!-- 2. Modalidad de Contratación: Planes Estándar vs Plan Personalizado -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="bi bi-calendar-check me-1 text-success"></i>Modalidad de Cobertura y Plan *
                                </label>
                                <span class="badge bg-light text-muted border">Tarifa Base: Bs <?= number_format((float)($tarifa['monto_mensual'] ?? 250.00), 2) ?> / mes</span>
                            </div>

                            <!-- Botonera de Selección de Modo -->
                            <div class="btn-group w-100 p-1 bg-light rounded-4 border mb-3" role="group">
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-plan active" id="btnModoEstandar" onclick="seleccionarModoPlan('ESTANDAR')">
                                    <i class="bi bi-grid-fill me-1"></i> Planes Predefinidos
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-plan" id="btnModoPersonalizado" onclick="seleccionarModoPlan('PERSONALIZADO')">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Plan Personalizado (A Medida)
                                </button>
                            </div>

                            <!-- Sección A: Planes Rápidos Predefinidos -->
                            <div id="seccion-planes-estandar">
                                <div class="row g-2">
                                    <!-- Plan 1 Mes -->
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan h-100 p-3 text-center border-2 cursor-pointer active" id="cardPlan1" onclick="elegirPlanEstandar(1, 250.00, 1, 0)">
                                            <span class="badge bg-light text-muted extra-small mb-1">Básico</span>
                                            <h6 class="fw-bold text-dark mb-1">1 Mes</h6>
                                            <span class="text-success fw-bold fs-6">Bs 250</span>
                                            <small class="text-muted extra-small d-block mt-1">Precio regular</small>
                                        </div>
                                    </div>
                                    <!-- Plan 3 Meses -->
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan h-100 p-3 text-center border-2 cursor-pointer" id="cardPlan3" onclick="elegirPlanEstandar(3, 712.50, 1, 37.50)">
                                            <span class="badge bg-info-subtle text-info-emphasis extra-small mb-1">Trimestral</span>
                                            <h6 class="fw-bold text-dark mb-1">3 Meses</h6>
                                            <span class="text-success fw-bold fs-6">Bs 712.50</span>
                                            <small class="text-success fw-semibold extra-small d-block mt-1"><i class="bi bi-tag-fill me-1"></i>Ahorra Bs 37.50 (5%)</small>
                                        </div>
                                    </div>
                                    <!-- Plan 6 Meses -->
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan h-100 p-3 text-center border-2 cursor-pointer" id="cardPlan6" onclick="elegirPlanEstandar(6, 1350.00, 1, 150.00)">
                                            <span class="badge bg-primary-subtle text-primary extra-small mb-1">Semestral</span>
                                            <h6 class="fw-bold text-dark mb-1">6 Meses</h6>
                                            <span class="text-success fw-bold fs-6">Bs 1,350</span>
                                            <small class="text-success fw-semibold extra-small d-block mt-1"><i class="bi bi-tag-fill me-1"></i>Ahorra Bs 150 (10%)</small>
                                        </div>
                                    </div>
                                    <!-- Plan 12 Meses (Anual) -->
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan h-100 p-3 text-center border-2 cursor-pointer position-relative" id="cardPlan12" onclick="elegirPlanEstandar(12, 2500.00, 2, 500.00)">
                                            <span class="badge bg-warning text-dark fw-bold extra-small mb-1">★ Más Popular</span>
                                            <h6 class="fw-bold text-dark mb-1">12 Meses</h6>
                                            <span class="text-success fw-bold fs-6">Bs 2,500</span>
                                            <small class="text-success fw-bold extra-small d-block mt-1"><i class="bi bi-gift-fill me-1"></i>Ahorra Bs 500 (2m gratis)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección B: Apartado de Plan Personalizado (Dinámico) -->
                            <div id="seccion-plan-personalizado" class="p-3 p-md-4 rounded-4 border border-2 border-warning-subtle bg-warning-subtle bg-opacity-10" style="display: none;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-sliders me-1"></i> Configuración a Medida
                                    </span>
                                    <span class="small text-muted">Ajuste los meses pactados con el cliente y elija la política de descuento</span>
                                </div>

                                <div class="row g-3 align-items-center">
                                    <!-- Selector de Cantidad de Meses -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-dark mb-1">
                                            Cantidad de Meses contratados:
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <button type="button" class="btn btn-outline-secondary px-3" onclick="cambiarMesesRelativo(-1)" title="Restar 1 mes">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <input type="number" id="inputMesesPersonalizado" min="1" max="120" value="5" class="form-control text-center fw-bold fs-4 text-primary" oninput="recalcularPlanPersonalizado()">
                                            <button type="button" class="btn btn-outline-secondary px-3" onclick="cambiarMesesRelativo(1)" title="Sumar 1 mes">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Accesos directos a duraciones comunes -->
                                        <div class="d-flex gap-1 flex-wrap mt-2">
                                            <span class="extra-small text-muted align-self-center me-1">Rápidos:</span>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMeses(2)">2m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMeses(4)">4m</button>
                                            <button type="button" class="btn btn-warning btn-sm border border-warning py-0 px-2 extra-small fw-bold" onclick="fijarMeses(5)">5 meses</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMeses(7)">7m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMeses(8)">8m</button>
                                            <button type="button" class="btn btn-light btn-sm border py-0 px-2 extra-small" onclick="fijarMeses(10)">10m</button>
                                        </div>
                                    </div>

                                    <!-- Selector de Descuento / Ahorro Aplicado -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-dark mb-1">
                                            Descuento o Beneficio de Ahorro:
                                        </label>
                                        <select id="selectTipoDescuento" class="form-select form-select-lg fs-6" onchange="cambiarReglaDescuento()">
                                            <option value="0">Sin descuento (Tarifa regular 100%)</option>
                                            <option value="5">5% de Descuento comercial</option>
                                            <option value="10" selected>10% de Descuento preferencial (Sugerido)</option>
                                            <option value="15">15% de Descuento fidelidad</option>
                                            <option value="20">20% de Descuento especial</option>
                                            <option value="mes_gratis">1 Mes de regalo (Descuenta 1 mensualidad)</option>
                                            <option value="manual">Fijar importe acordado manualmente en Bs</option>
                                        </select>
                                        <small class="text-muted extra-small d-block mt-1">El ahorro se calcula comparando contra la tarifa regular mensual.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Panel Dinámico de Liquidación, Desglose de Ahorro y Proyección de Vigencia -->
                        <div class="col-12 mt-3">
                            <div class="card rounded-4 border p-3 p-md-4 bg-light shadow-xs">
                                <div class="row g-3 align-items-center">
                                    <!-- A la izquierda: Desglose numérico y tarjeta de ahorro -->
                                    <div class="col-lg-7">
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted small">
                                            <span>Precio regular sin descuento (<span id="txt-calc-meses">5</span> meses × Bs <?= number_format((float)($tarifa['monto_mensual'] ?? 250.00), 2) ?>):</span>
                                            <span class="fw-semibold text-decoration-line-through" id="txt-precio-regular">Bs 1,250.00</span>
                                        </div>

                                        <!-- Tarjeta Destacada de Ahorro para el Cliente -->
                                        <div id="caja-ahorro-destacada" class="p-3 my-2 rounded-3 border d-flex align-items-center gap-3 bg-white">
                                            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                                <i class="bi bi-piggy-bank-fill fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-success fs-5">
                                                    ¡El cliente se está ahorrando <span id="txt-ahorro-monto">Bs 125.00</span>!
                                                </div>
                                                <div class="extra-small text-muted mt-0">
                                                    Representa un <strong class="text-dark"><span id="txt-ahorro-porcentaje">10.0%</span> de ahorro</strong> sobre el costo mensual regular. 
                                                    Equivale a pagar solo <strong class="text-success"><span id="txt-costo-mensual">Bs 225.00</span></strong> por mes.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Proyección de Vigencia -->
                                        <div class="d-flex align-items-center gap-2 mt-2 extra-small text-muted">
                                            <i class="bi bi-calendar2-range text-primary"></i>
                                            <span id="txt-proyeccion-vigencia">Proyectando fechas de vigencia...</span>
                                        </div>
                                    </div>

                                    <!-- A la derecha: Importe Final a Cobrar (editable) -->
                                    <div class="col-lg-5">
                                        <div class="p-3 bg-white rounded-3 border text-center">
                                            <label class="form-label fw-bold extra-small text-muted text-uppercase mb-1">
                                                Importe Final a Cobrar (Bs) *
                                            </label>
                                            <div class="input-group input-group-lg justify-content-center">
                                                <span class="input-group-text bg-light text-success fw-bold border-end-0">Bs</span>
                                                <input type="number" step="0.01" name="monto" id="inputMontoFinal" 
                                                       class="form-control text-center fw-bold fs-3 text-success border-start-0" 
                                                       value="1125.00" required oninput="alCambiarMontoManualmente()">
                                            </div>
                                            <small class="text-muted extra-small d-block mt-1">Monto acordado para el recibo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Fecha Declarada y Selector de Método de Pago -->
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold small text-dark">
                                <i class="bi bi-calendar-event me-1 text-muted"></i>Fecha Declarada del Abono *
                            </label>
                            <input type="date" name="fecha_pago_declarada" id="inputFechaDeclarada" class="form-control" value="<?= date('Y-m-d') ?>" required onchange="actualizarProyeccionVigencia()">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold small text-dark">
                                <i class="bi bi-credit-card-2-front me-1 text-muted"></i>Canal de Cobro Seleccionado *
                            </label>
                            <input type="hidden" name="metodo_pago" id="inputMetodoPago" value="Efectivo en oficina">
                            <div class="btn-group w-100 p-1 bg-light rounded-4 border" role="group">
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-canal-pago active" id="btnCanalEfectivo" onclick="seleccionarCanalPago('EFECTIVO')">
                                    <i class="bi bi-cash-stack text-success me-1"></i> Cobro en Efectivo
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-canal-pago" id="btnCanalQR" onclick="seleccionarCanalPago('QR')">
                                    <i class="bi bi-qr-code-scan text-primary me-1"></i> Pago por QR / Banco
                                </button>
                            </div>
                        </div>

                        <!-- Panel A: Flujo Cobro en Efectivo (Recibo Inmediato, WhatsApp e Impresión) -->
                        <div class="col-12" id="panelFlujoEfectivo">
                            <div class="p-3 rounded-4 border border-success-subtle bg-success-subtle bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success text-white px-2 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-receipt me-1"></i> Recibo de Caja BeniTours
                                        </span>
                                        <span class="small fw-semibold text-dark">Emisión y comprobante para el cliente</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="confirmar_inmediato" id="checkConfirmarInmediato" value="1" checked>
                                        <label class="form-check-label small fw-bold text-success" for="checkConfirmarInmediato">
                                            Confirmar y activar vigencia de inmediato
                                        </label>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3">
                                    El recibo se autocompletará con los datos del comercio seleccionado. Podrás imprimirlo o enviárselo directamente a su WhatsApp en 1 clic.
                                </p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-semibold" onclick="abrirModalPrevisualizarRecibo()">
                                        <i class="bi bi-eye me-1"></i> Vista previa del recibo
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" onclick="enviarWhatsAppBorrador()">
                                        <i class="bi bi-whatsapp me-1"></i> Probar envío a WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Panel B: Flujo Pago por QR / Transferencia (QR responsivo + Botones limpios) -->
                        <div class="col-12" id="panelFlujoQR" style="display: none;">
                            <div class="p-3 p-md-4 rounded-4 border border-primary-subtle bg-white shadow-xs">
                                <div class="row align-items-center g-3">
                                    <!-- QR Oficial (Lateral en PC, Arriba y Grande en Móvil) -->
                                    <div class="col-12 col-md-auto text-center" id="columnaQrCobro">
                                        <?php 
                                            $qrPath = $cobro['qr_url'] ?? '/assets/img/QR-Prueba.jpeg';
                                            $qrFullPath = $baseUrl . $qrPath;
                                        ?>
                                        <div class="bg-light p-2 d-inline-block rounded-4 shadow-2xs border">
                                            <img src="<?= htmlspecialchars($qrFullPath) ?>" id="imgQrCobro" alt="QR Oficial BeniTours" class="img-fluid rounded-3 qr-pago-responsive">
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
                                                <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-xs" id="btnSubirArchivoPago">
                                                    <i class="bi bi-folder2-open me-1"></i> Subir Archivo
                                                </button>
                                                <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-xs" id="btnTomarFotoPago">
                                                    <i class="bi bi-camera-fill me-1"></i> Sacar Foto
                                                </button>
                                                <button type="button" class="btn btn-outline-danger rounded-pill px-2 py-2 d-none shadow-xs" id="btnQuitarFoto" title="Quitar comprobante">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Inputs file ocultos -->
                                            <input type="file" id="inputCamaraComprobante" name="comprobante_archivo" accept="image/*" capture="environment" class="d-none">
                                            <input type="file" id="inputArchivoComprobante" accept="image/jpeg,image/png,image/webp" class="d-none">

                                            <!-- Nombre de archivo seleccionado -->
                                            <div id="nombreArchivoPago" class="extra-small text-truncate text-secondary fw-semibold mb-2 text-center text-md-start" style="max-width: 100%;"></div>

                                            <!-- Previsualización de la foto capturada -->
                                            <div id="contenedorPreviewFoto" class="d-none text-center text-md-start">
                                                <img id="previewFotoImg" src="" alt="Comprobante capturado" class="rounded-3 border shadow-sm" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                            </div>
                                            <div id="placeholderFotoBox" class="p-2 p-md-3 text-muted border border-dashed rounded-3 bg-light extra-small text-center text-md-start">
                                                <i class="bi bi-receipt text-secondary me-1"></i> Sin comprobante adjuntado aún
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Comprobante Secuencial y Notas -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">
                                <i class="bi bi-receipt me-1 text-muted"></i>N° de Recibo o Comprobante
                            </label>
                            <input type="text" name="numero_comprobante" id="inputNumeroComprobante" class="form-control" placeholder="Automático (Ej. REC-<?= date('Y') ?>-XXXX)">
                            <small class="text-muted extra-small">Si lo dejas en blanco, el sistema generará uno automáticamente.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">
                                <i class="bi bi-chat-left-text me-1 text-muted"></i>Observaciones o Notas Internas
                            </label>
                            <input type="text" name="observaciones" id="inputObservaciones" class="form-control" placeholder="Detalle comercial, cajero o notas adicionales...">
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small" id="txtNotaGuardado">
                                <i class="bi bi-info-circle me-1"></i>Cobro en efectivo: Se registrará y se emitirá el recibo listo para imprimir o enviar.
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button <?= !$tarifa ? 'disabled' : '' ?> type="submit" name="emitir_recibo" value="1" class="btn btn-outline-success btn-lg rounded-pill px-4 fw-bold shadow-sm" id="btnGuardarEImprimir">
                                <i class="bi bi-printer me-2"></i>Guardar e Imprimir Recibo
                            </button>
                            <button <?= !$tarifa ? 'disabled' : '' ?> type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm" id="btnGuardarPrincipal">
                                <i class="bi bi-check-circle me-2"></i>Guardar Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.card-plan {
    transition: all 0.2s ease-in-out;
    background: #fff;
    border-color: #dee2e6;
}
.card-plan:hover {
    border-color: #2A9D8F;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.card-plan.active {
    border-color: #2A9D8F !important;
    background-color: rgba(42, 157, 143, 0.04);
}
.btn-modo-plan.active {
    background-color: #ffffff !important;
    color: #217E73 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.qr-pago-responsive {
    width: 170px;
    height: 170px;
    object-fit: contain;
}
@media (max-width: 767.98px) {
    .qr-pago-responsive {
        width: 240px;
        height: 240px;
    }
}
.card-resultado-comercio {
    transition: all 0.2s ease-in-out;
    border-color: #e2e8f0;
}
.card-resultado-comercio:hover {
    border-color: #0d9488 !important;
    background-color: #f0fdfa !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.12);
}
</style>

<script>
const TARIFA_BASE_MENSUAL = <?= (float)($tarifa['monto_mensual'] ?? 250.00) ?>;
const BASE_URL_APP = <?= json_encode($baseUrl) ?>;
const COMERCIOS_DATA = <?= json_encode($comercios, JSON_UNESCAPED_UNICODE) ?>;
let modoActual = 'ESTANDAR'; // 'ESTANDAR' o 'PERSONALIZADO'
let mesesSeleccionados = 1;
let reglaDescuentoManual = false;
let comercioSeleccionadoObj = null;

// Placeholder SVG institucional limpio en base64 para evitar errores 404 o bucles onerror
const PLACEHOLDER_COMERCIO_SVG = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20fill%3D%22none%22%3E%3Crect%20width%3D%2260%22%20height%3D%2260%22%20rx%3D%2212%22%20fill%3D%22%23F1F5F9%22%2F%3E%3Cpath%20d%3D%22M18%2038V28M42%2038V28M16%2028H44M20%2028L23%2020H37L40%2028M26%2038V32H34V38%22%20stroke%3D%22%230D9488%22%20stroke-width%3D%222.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E";

function resolverFotoComercio(nombreArchivo) {
    if (!nombreArchivo) return PLACEHOLDER_COMERCIO_SVG;
    return `${BASE_URL_APP}/imagen?f=${encodeURIComponent(nombreArchivo)}`;
}

document.addEventListener('DOMContentLoaded', () => {
    const inputOcultoLugar = document.getElementById('pago-lugar');
    const inputBuscar = document.getElementById('inputBuscarComercio');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
    const contenedorTarjetas = document.getElementById('contenedorTarjetasComercios');
    const bloqueBuscador = document.getElementById('bloqueBuscadorComercio');
    const tarjetaFijado = document.getElementById('tarjetaComercioFijado');
    const btnCambiar = document.getElementById('btnCambiarComercio');

    // Inicializar si ya viene un id_lugar preseleccionado por URL
    const idInicial = parseInt(inputOcultoLugar?.value || 0, 10);
    if (idInicial > 0) {
        const encontrado = COMERCIOS_DATA.find(c => parseInt(c.id_lugar, 10) === idInicial);
        if (encontrado) {
            seleccionarComercioCard(encontrado);
        }
    }

    // Buscador en vivo de comercios
    inputBuscar?.addEventListener('input', function() {
        const termino = this.value.trim().toLowerCase();
        btnLimpiar.style.display = termino ? 'inline-block' : 'none';

        if (!termino) {
            contenedorTarjetas.innerHTML = '';
            return;
        }

        const filtrados = COMERCIOS_DATA.filter(c => {
            const nombre = (c.nombre || '').toLowerCase();
            const direccion = (c.direccion || '').toLowerCase();
            return nombre.includes(termino) || direccion.includes(termino);
        });

        renderizarTarjetasComercio(filtrados);
    });

    btnLimpiar?.addEventListener('click', function() {
        inputBuscar.value = '';
        this.style.display = 'none';
        contenedorTarjetas.innerHTML = '';
        inputBuscar.focus();
    });

    btnCambiar?.addEventListener('click', function() {
        deseleccionarComercio();
    });

    // Plan inicial estándar
    elegirPlanEstandar(1, TARIFA_BASE_MENSUAL, 1, 0);
});

/**
 * Renderiza las tarjetas de resultados de búsqueda
 */
function renderizarTarjetasComercio(comercios) {
    const contenedor = document.getElementById('contenedorTarjetasComercios');
    if (!contenedor) return;

    if (!comercios || comercios.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12">
                <div class="p-3 text-center text-muted bg-light rounded-4 border extra-small">
                    <i class="bi bi-search fs-4 d-block mb-1 text-secondary"></i>
                    No se encontró ningún comercio con ese nombre.
                </div>
            </div>
        `;
        return;
    }

    const hoy = new Date();
    hoy.setHours(0,0,0,0);

    let html = '';
    comercios.slice(0, 10).forEach(c => {
        const imgUrl = resolverFotoComercio(c.imagen);
        let badgeEstado = '';
        let textoVencimiento = '';

        if (c.ultimo_vencimiento) {
            const dVenc = new Date(c.ultimo_vencimiento + 'T00:00:00');
            const fmt = dVenc.toLocaleDateString('es-BO');
            if (dVenc >= hoy) {
                badgeEstado = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small"><i class="bi bi-shield-check me-1"></i>Vence: ${fmt}</span>`;
            } else {
                badgeEstado = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small"><i class="bi bi-exclamation-triangle me-1"></i>Venció: ${fmt}</span>`;
            }
            textoVencimiento = fmt;
        } else {
            badgeEstado = `<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill extra-small"><i class="bi bi-star me-1"></i>Primer periodo</span>`;
        }

        html += `
            <div class="col-12 col-md-6">
                <div class="card p-3 rounded-4 border shadow-2xs h-100 card-resultado-comercio cursor-pointer bg-white" onclick="seleccionarComercioPorId(${c.id_lugar})">
                    <div class="d-flex align-items-center gap-3">
                        <img src="${imgUrl}" alt="${c.nombre}" class="rounded-3 border flex-shrink-0 shadow-2xs" style="width: 54px; height: 54px; object-fit: cover;" onerror="this.onerror=null; this.src=PLACEHOLDER_COMERCIO_SVG;">
                        <div class="flex-grow-1 text-truncate">
                            <h6 class="fw-bold text-dark mb-1 text-truncate">${c.nombre}</h6>
                            <div class="mb-1">${badgeEstado}</div>
                            <small class="text-muted extra-small text-truncate d-block">
                                <i class="bi bi-geo-alt me-1"></i>${c.direccion || 'Trinidad, Beni'}
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold shadow-xs">
                                <i class="bi bi-check2"></i> Elegir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
}

/**
 * Selecciona un comercio por su ID
 */
function seleccionarComercioPorId(id) {
    const c = COMERCIOS_DATA.find(item => parseInt(item.id_lugar, 10) === parseInt(id, 10));
    if (c) {
        seleccionarComercioCard(c);
    }
}

/**
 * Fija el comercio seleccionado y actualiza la interfaz
 */
function seleccionarComercioCard(c) {
    comercioSeleccionadoObj = c;

    const inputOculto = document.getElementById('pago-lugar');
    const bloqueBuscador = document.getElementById('bloqueBuscadorComercio');
    const tarjetaFijado = document.getElementById('tarjetaComercioFijado');
    const contenedorTarjetas = document.getElementById('contenedorTarjetasComercios');
    const fijadoFoto = document.getElementById('fijadoFoto');
    const fijadoNombre = document.getElementById('fijadoNombre');
    const fijadoBadgeEstado = document.getElementById('fijadoBadgeEstado');
    const fijadoDetalles = document.getElementById('fijadoDetalles');

    if (inputOculto) inputOculto.value = c.id_lugar;

    // Actualizar datos de la tarjeta fija
    const imgUrl = resolverFotoComercio(c.imagen);
    if (fijadoFoto) {
        fijadoFoto.src = imgUrl;
        fijadoFoto.onerror = () => { 
            fijadoFoto.onerror = null; 
            fijadoFoto.src = PLACEHOLDER_COMERCIO_SVG; 
        };
    }
    if (fijadoNombre) fijadoNombre.textContent = c.nombre;

    const hoy = new Date();
    hoy.setHours(0,0,0,0);

    if (fijadoBadgeEstado) {
        if (c.ultimo_vencimiento) {
            const dVenc = new Date(c.ultimo_vencimiento + 'T00:00:00');
            const fmt = dVenc.toLocaleDateString('es-BO');
            if (dVenc >= hoy) {
                fijadoBadgeEstado.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small';
                fijadoBadgeEstado.innerHTML = `<i class="bi bi-shield-check me-1"></i>Vence: ${fmt}`;
            } else {
                fijadoBadgeEstado.className = 'badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small';
                fijadoBadgeEstado.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>Venció: ${fmt}`;
            }
        } else {
            fijadoBadgeEstado.className = 'badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill extra-small';
            fijadoBadgeEstado.innerHTML = `<i class="bi bi-star me-1"></i>Primer periodo`;
        }
    }

    if (fijadoDetalles) {
        fijadoDetalles.innerHTML = `<i class="bi bi-geo-alt me-1"></i>${c.direccion || 'Trinidad, Beni'}` + (c.whatsapp_contacto ? ` &bull; <i class="bi bi-whatsapp text-success me-1"></i>${c.whatsapp_contacto}` : '');
    }

    // Cambiar visibilidad
    bloqueBuscador?.classList.add('d-none');
    tarjetaFijado?.classList.remove('d-none');
    if (contenedorTarjetas) contenedorTarjetas.innerHTML = '';

    // Mostrar banner de vigencia y proyectar
    mostrarBannerVigencia(c);
}

/**
 * Permite cambiar el comercio seleccionado
 */
function deseleccionarComercio() {
    comercioSeleccionadoObj = null;

    const inputOculto = document.getElementById('pago-lugar');
    const bloqueBuscador = document.getElementById('bloqueBuscadorComercio');
    const tarjetaFijado = document.getElementById('tarjetaComercioFijado');
    const inputBuscar = document.getElementById('inputBuscarComercio');
    const contenedorInfo = document.getElementById('info-vigencia-comercio');

    if (inputOculto) inputOculto.value = '';
    bloqueBuscador?.classList.remove('d-none');
    tarjetaFijado?.classList.add('d-none');
    if (contenedorInfo) {
        contenedorInfo.style.display = 'none';
        contenedorInfo.innerHTML = '';
    }
    if (inputBuscar) {
        inputBuscar.value = '';
        inputBuscar.focus();
    }
    actualizarProyeccionVigencia();
}

/**
 * Muestra el banner informativo de vigencia para el comercio seleccionado
 */
function mostrarBannerVigencia(c) {
    const contenedorInfo = document.getElementById('info-vigencia-comercio');
    if (!contenedorInfo) return;

    if (!c) {
        contenedorInfo.style.display = 'none';
        contenedorInfo.innerHTML = '';
        actualizarProyeccionVigencia();
        return;
    }

    if (c.ultimo_vencimiento) {
        const dVenc = new Date(c.ultimo_vencimiento + 'T00:00:00');
        const hoy = new Date();
        hoy.setHours(0,0,0,0);
        const fmt = dVenc.toLocaleDateString('es-BO');

        if (dVenc >= hoy) {
            contenedorInfo.innerHTML = `
                <div class="alert alert-success py-2 px-3 mb-0 small rounded-4 d-flex align-items-center gap-2 border shadow-2xs">
                    <i class="bi bi-shield-check fs-5 text-success"></i>
                    <div>
                        <strong>Membresía activa al día:</strong> Vence el <strong>${fmt}</strong>. 
                        Este pago sumará los meses contratados directamente a partir de esa fecha sin perder días.
                    </div>
                </div>
            `;
        } else {
            contenedorInfo.innerHTML = `
                <div class="alert alert-warning py-2 px-3 mb-0 small rounded-4 d-flex align-items-center gap-2 border shadow-2xs">
                    <i class="bi bi-clock-history fs-5 text-warning-emphasis"></i>
                    <div>
                        <strong>Membresía vencida el ${fmt}:</strong> 
                        Este pago reactivará la publicación a partir de la fecha declarada del abono.
                    </div>
                </div>
            `;
        }
    } else {
        contenedorInfo.innerHTML = `
            <div class="alert alert-info py-2 px-3 mb-0 small rounded-4 d-flex align-items-center gap-2 border shadow-2xs">
                <i class="bi bi-star fs-5 text-info"></i>
                <div>
                    <strong>Primer periodo de membresía:</strong> La cobertura iniciará a partir de la fecha de pago declarada.
                </div>
            </div>
        `;
    }
    contenedorInfo.style.display = 'block';
    actualizarProyeccionVigencia();
}

/**
 * Alterna entre Planes Predefinidos y Plan Personalizado
 */
function seleccionarModoPlan(modo) {
    modoActual = modo;
    document.getElementById('tipo_plan_modalidad').value = modo;

    const btnEst = document.getElementById('btnModoEstandar');
    const btnPer = document.getElementById('btnModoPersonalizado');
    const secEst = document.getElementById('seccion-planes-estandar');
    const secPer = document.getElementById('seccion-plan-personalizado');

    if (modo === 'ESTANDAR') {
        btnEst.classList.add('active');
        btnPer.classList.remove('active');
        secEst.style.display = 'block';
        secPer.style.display = 'none';
        elegirPlanEstandar(1, TARIFA_BASE_MENSUAL, 1, 0);
    } else {
        btnPer.classList.add('active');
        btnEst.classList.remove('active');
        secEst.style.display = 'none';
        secPer.style.display = 'block';
        document.querySelectorAll('.card-plan').forEach(c => c.classList.remove('active'));
        
        // Cargar por defecto 5 meses como en la solicitud del usuario
        fijarMeses(5);
    }
}

/**
 * Selecciona uno de los planes estándar rápidos
 */
function elegirPlanEstandar(meses, monto, idTarifa, ahorro) {
    mesesSeleccionados = meses;
    reglaDescuentoManual = false;

    // Resaltar tarjeta seleccionada
    document.querySelectorAll('.card-plan').forEach(c => c.classList.remove('active'));
    document.getElementById('cardPlan' + meses)?.classList.add('active');

    // Asignar campos del formulario
    document.getElementById('meses_duracion').value = meses;
    document.getElementById('id_tarifa').value = idTarifa;
    document.getElementById('inputMontoFinal').value = Number(monto).toFixed(2);

    actualizarDesgloseAhorro(meses, monto, ahorro);
    actualizarProyeccionVigencia();
}

/**
 * Modifica la cantidad de meses en el plan personalizado
 */
function cambiarMesesRelativo(delta) {
    const input = document.getElementById('inputMesesPersonalizado');
    let val = parseInt(input.value || 1, 10) + delta;
    if (val < 1) val = 1;
    if (val > 120) val = 120;
    input.value = val;
    recalcularPlanPersonalizado();
}

function fijarMeses(meses) {
    const input = document.getElementById('inputMesesPersonalizado');
    input.value = meses;
    recalcularPlanPersonalizado();
}

function cambiarReglaDescuento() {
    reglaDescuentoManual = false;
    recalcularPlanPersonalizado();
}

/**
 * Calcula dinámicamente el precio regular, descuento, ahorro y nuevo importe en el Plan Personalizado
 */
function recalcularPlanPersonalizado() {
    const inputMeses = document.getElementById('inputMesesPersonalizado');
    let meses = parseInt(inputMeses.value || 1, 10);
    if (isNaN(meses) || meses < 1) meses = 1;
    if (meses > 120) meses = 120;
    mesesSeleccionados = meses;

    document.getElementById('meses_duracion').value = meses;

    const regla = document.getElementById('selectTipoDescuento').value;
    const precioRegular = meses * TARIFA_BASE_MENSUAL;
    let montoFinal = precioRegular;
    let ahorro = 0;

    if (regla === 'manual') {
        // En modo manual respetamos lo que el usuario tipee en inputMontoFinal
        const inputMonto = document.getElementById('inputMontoFinal');
        montoFinal = parseFloat(inputMonto.value || 0);
        ahorro = Math.max(0, precioRegular - montoFinal);
    } else if (regla === 'mes_gratis') {
        // Descuenta exactamente 1 mes regular
        ahorro = TARIFA_BASE_MENSUAL;
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

    document.getElementById('inputMontoFinal').value = Number(montoFinal).toFixed(2);
    actualizarDesgloseAhorro(meses, montoFinal, ahorro);
    actualizarProyeccionVigencia();
}

/**
 * Cuando el administrador edita manualmente el campo de Bs en modo personalizado
 */
function alCambiarMontoManualmente() {
    if (modoActual === 'PERSONALIZADO') {
        document.getElementById('selectTipoDescuento').value = 'manual';
    }
    const meses = mesesSeleccionados;
    const precioRegular = meses * TARIFA_BASE_MENSUAL;
    const montoFinal = parseFloat(document.getElementById('inputMontoFinal').value || 0);
    const ahorro = Math.max(0, precioRegular - montoFinal);

    actualizarDesgloseAhorro(meses, montoFinal, ahorro);
}

/**
 * Actualiza los elementos visuales de la tarjeta de Ahorro y Liquidación
 */
function actualizarDesgloseAhorro(meses, monto, ahorro) {
    const precioRegular = meses * TARIFA_BASE_MENSUAL;
    const ahorroReal = Math.max(0, ahorro);
    const porcentaje = precioRegular > 0 ? ((ahorroReal / precioRegular) * 100) : 0;
    const costoMensual = meses > 0 ? (monto / meses) : monto;

    document.getElementById('txt-calc-meses').textContent = meses;
    document.getElementById('txt-precio-regular').textContent = 'Bs ' + precioRegular.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('txt-ahorro-monto').textContent = 'Bs ' + ahorroReal.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('txt-ahorro-porcentaje').textContent = porcentaje.toFixed(1) + '%';
    document.getElementById('txt-costo-mensual').textContent = 'Bs ' + costoMensual.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const caja = document.getElementById('caja-ahorro-destacada');
    if (ahorroReal > 0) {
        caja.className = 'p-3 my-2 rounded-3 border d-flex align-items-center gap-3 bg-white border-success shadow-xs';
        caja.innerHTML = `
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="bi bi-piggy-bank-fill fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-success fs-5">
                    ¡El cliente se está ahorrando Bs ${ahorroReal.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}!
                </div>
                <div class="extra-small text-muted mt-0">
                    Ahorro del <strong class="text-dark">${porcentaje.toFixed(1)}%</strong> sobre la tarifa mensual regular. 
                    Equivale a pagar solo <strong class="text-success">Bs ${costoMensual.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong> por mes.
                </div>
            </div>
        `;
    } else {
        caja.className = 'p-3 my-2 rounded-3 border d-flex align-items-center gap-3 bg-white border-secondary-subtle';
        caja.innerHTML = `
            <div class="rounded-circle bg-light text-muted p-2 d-flex align-items-center justify-content-center flex-shrink-0 border" style="width: 44px; height: 44px;">
                <i class="bi bi-receipt fs-4 text-muted"></i>
            </div>
            <div>
                <div class="fw-bold text-dark fs-6">
                    Tarifa regular estándar (Bs 250.00 / mes)
                </div>
                <div class="extra-small text-muted">
                    No se ha aplicado ningún descuento promocional en este plan.
                </div>
            </div>
        `;
    }
}

/**
 * Calcula y proyecta las fechas de inicio y vencimiento en tiempo real
 */
function actualizarProyeccionVigencia() {
    const fechaDeclaradaStr = document.getElementById('inputFechaDeclarada')?.value || new Date().toISOString().slice(0, 10);
    const txtProyeccion = document.getElementById('txt-proyeccion-vigencia');

    const vencimientoRaw = comercioSeleccionadoObj?.ultimo_vencimiento;
    const hoyStr = new Date().toISOString().slice(0, 10);

    let fechaInicio = fechaDeclaradaStr;
    let esRenovacion = false;

    if (vencimientoRaw && vencimientoRaw > hoyStr) {
        fechaInicio = vencimientoRaw;
        esRenovacion = true;
    }

    const dInicio = new Date(fechaInicio + 'T00:00:00');
    if (isNaN(dInicio.getTime())) {
        txtProyeccion.textContent = 'Fecha no válida';
        return;
    }

    // Sumar meses
    const dFin = new Date(dInicio);
    dFin.setMonth(dFin.getMonth() + mesesSeleccionados);

    const fmtInicio = dInicio.toLocaleDateString('es-BO');
    const fmtFin = dFin.toLocaleDateString('es-BO');

    if (esRenovacion) {
        txtProyeccion.innerHTML = `Renovación anticipada: <strong>${fmtInicio}</strong> &rarr; <strong>${fmtFin}</strong> (${mesesSeleccionados} mes(es) adicionados)`;
    } else {
        txtProyeccion.innerHTML = `Vigencia proyectada: <strong>${fmtInicio}</strong> &rarr; <strong>${fmtFin}</strong> (${mesesSeleccionados} mes(es) de cobertura)`;
    }
}

/**
 * Selección interactiva de Canal de Cobro: Efectivo vs QR / Transferencia
 */
let canalCobroActual = 'EFECTIVO';

function seleccionarCanalPago(canal) {
    canalCobroActual = canal;
    const btnEf = document.getElementById('btnCanalEfectivo');
    const btnQr = document.getElementById('btnCanalQR');
    const panelEf = document.getElementById('panelFlujoEfectivo');
    const panelQr = document.getElementById('panelFlujoQR');
    const inputMetodo = document.getElementById('inputMetodoPago');
    const txtNota = document.getElementById('txtNotaGuardado');
    const btnImprimir = document.getElementById('btnGuardarEImprimir');

    if (canal === 'EFECTIVO') {
        btnEf.classList.add('active', 'btn-success', 'text-white');
        btnEf.classList.remove('btn-light', 'text-dark');
        btnQr.classList.remove('active', 'btn-primary', 'text-white');
        btnQr.classList.add('btn-light', 'text-dark');

        panelEf.style.display = 'block';
        panelQr.style.display = 'none';
        inputMetodo.value = 'Efectivo en oficina';
        txtNota.innerHTML = '<i class="bi bi-info-circle me-1"></i>Cobro en efectivo: Se registrará y se emitirá el recibo listo para imprimir o enviar.';
        btnImprimir.style.display = 'inline-block';
    } else {
        btnQr.classList.add('active', 'btn-primary', 'text-white');
        btnQr.classList.remove('btn-light', 'text-dark');
        btnEf.classList.remove('active', 'btn-success', 'text-white');
        btnEf.classList.add('btn-light', 'text-dark');

        panelEf.style.display = 'none';
        panelQr.style.display = 'block';
        inputMetodo.value = 'Transferencia bancaria / QR';
        txtNota.innerHTML = '<i class="bi bi-info-circle me-1"></i>Pago por QR: Asegúrese de capturar o adjuntar el comprobante emitido por el cliente.';
        btnImprimir.style.display = 'none';
    }
}

/**
 * Manejo de Captura de Foto del Comprobante
 */
document.addEventListener('DOMContentLoaded', () => {
    const btnSubir = document.getElementById('btnSubirArchivoPago');
    const btnTomarFoto = document.getElementById('btnTomarFotoPago');
    const btnQuitar = document.getElementById('btnQuitarFoto');
    const inputArchivo = document.getElementById('inputArchivoComprobante');
    const inputCamara = document.getElementById('inputCamaraComprobante');

    btnSubir?.addEventListener('click', () => inputArchivo?.click());
    btnTomarFoto?.addEventListener('click', () => inputCamara?.click());
    btnQuitar?.addEventListener('click', () => quitarFotoComprobante());

    inputArchivo?.addEventListener('change', function() {
        procesarFotoComprobante(this);
    });

    inputCamara?.addEventListener('change', function() {
        procesarFotoComprobante(this);
    });
});

function procesarFotoComprobante(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    
    // Si viene del input de galería, asignarlo también al input principal que viaja en el formulario
    if (input.id === 'inputArchivoComprobante') {
        const camaraInput = document.getElementById('inputCamaraComprobante');
        if (window.DataTransfer && camaraInput) {
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                camaraInput.files = dt.files;
            } catch (e) {
                console.warn('DataTransfer no soportado', e);
            }
        }
    }

    const nombreArchivo = document.getElementById('nombreArchivoPago');
    if (nombreArchivo) {
        nombreArchivo.textContent = '📎 ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImg = document.getElementById('previewFotoImg');
        const contenedorPreview = document.getElementById('contenedorPreviewFoto');
        const placeholder = document.getElementById('placeholderFotoBox');
        const btnQuitar = document.getElementById('btnQuitarFoto');

        if (previewImg) previewImg.src = e.target.result;
        if (contenedorPreview) contenedorPreview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
        if (btnQuitar) btnQuitar.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

function quitarFotoComprobante() {
    const inputCamara = document.getElementById('inputCamaraComprobante');
    const inputArchivo = document.getElementById('inputArchivoComprobante');
    const previewImg = document.getElementById('previewFotoImg');
    const contenedorPreview = document.getElementById('contenedorPreviewFoto');
    const placeholder = document.getElementById('placeholderFotoBox');
    const btnQuitar = document.getElementById('btnQuitarFoto');
    const nombreArchivo = document.getElementById('nombreArchivoPago');

    if (inputCamara) inputCamara.value = '';
    if (inputArchivo) inputArchivo.value = '';
    if (previewImg) previewImg.src = '';
    if (contenedorPreview) contenedorPreview.classList.add('d-none');
    if (placeholder) placeholder.classList.remove('d-none');
    if (btnQuitar) btnQuitar.classList.add('d-none');
    if (nombreArchivo) nombreArchivo.textContent = '';
}

/**
 * Generación de datos autocompletados del Recibo
 */
function obtenerDatosReciboActual() {
    const nombreComercio = comercioSeleccionadoObj ? (comercioSeleccionadoObj.nombre || 'Comercio') : 'Comercio no seleccionado';
    const whatsapp = comercioSeleccionadoObj?.whatsapp_contacto || '';
    const telefono = comercioSeleccionadoObj?.telefono_contacto || '';
    const direccion = comercioSeleccionadoObj?.direccion || 'Trinidad, Beni';
    const monto = parseFloat(document.getElementById('inputMontoFinal')?.value || 0).toFixed(2);
    const meses = mesesSeleccionados;
    const fecha = document.getElementById('inputFechaDeclarada')?.value || new Date().toISOString().slice(0, 10);
    const numComp = document.getElementById('inputNumeroComprobante')?.value.trim() || ('REC-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000));
    const canal = canalCobroActual;

    return {
        nombreComercio,
        whatsapp,
        telefono,
        direccion,
        monto,
        meses,
        fecha,
        numComp,
        canal
    };
}

/**
 * Abrir Modal de Previsualización de Recibo
 */
function abrirModalPrevisualizarRecibo() {
    const d = obtenerDatosReciboActual();
    if (!document.getElementById('pago-lugar').value) {
        alert('Por favor selecciona primero un establecimiento comercial.');
        return;
    }

    document.getElementById('reciboModalComercio').textContent = d.nombreComercio;
    document.getElementById('reciboModalNumero').textContent = d.numComp;
    document.getElementById('reciboModalFecha').textContent = d.fecha;
    document.getElementById('reciboModalMonto').textContent = 'Bs ' + Number(d.monto).toLocaleString('es-BO', { minimumFractionDigits: 2 });
    document.getElementById('reciboModalMeses').textContent = d.meses + ' mes(es) de suscripción comercial';
    document.getElementById('reciboModalCanal').textContent = d.canal;
    document.getElementById('reciboModalContacto').textContent = d.whatsapp || d.telefono || 'Sin teléfono registrado';

    const modal = new bootstrap.Modal(document.getElementById('modalPreviewRecibo'));
    modal.show();
}

/**
 * Enviar Borrador de Recibo por WhatsApp
 */
function enviarWhatsAppBorrador() {
    const d = obtenerDatosReciboActual();
    if (!document.getElementById('pago-lugar').value) {
        alert('Por favor selecciona primero un establecimiento comercial.');
        return;
    }

    let numero = d.whatsapp || d.telefono;
    if (!numero) {
        numero = prompt('El comercio no tiene teléfono registrado. Ingrese el número de WhatsApp con código de país (ej. 59178901234):');
    }
    if (!numero) return;

    numero = numero.replace(/\D/g, '');
    if (!numero.startsWith('591') && numero.length === 8) {
        numero = '591' + numero;
    }

    const mensaje = `🧾 *COMPROBANTE DE PAGO - BENITOURS*\n\n` +
        `Estimado(a) *${d.nombreComercio}*,\n` +
        `Confirmamos la recepción de tu pago por concepto de suscripción comercial en el directorio turístico *BeniTours*:\n\n` +
        `🔹 *Recibo N°:* ${d.numComp}\n` +
        `🔹 *Monto:* Bs ${d.monto}\n` +
        `🔹 *Cobertura:* ${d.meses} mes(es)\n` +
        `🔹 *Método:* ${d.canal}\n` +
        `🔹 *Fecha:* ${d.fecha}\n\n` +
        `¡Gracias por impulsar el turismo y comercio en Trinidad! 🌴🇧🇴\n` +
        `Equipo BeniTours`;

    const url = `https://api.whatsapp.com/send?phone=${encodeURIComponent(numero)}&text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
}
</script>

<!-- Modal de Vista Previa de Recibo -->
<div class="modal fade" id="modalPreviewRecibo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-receipt-cutoff text-success me-2"></i>Vista Previa de Recibo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 border rounded-3 bg-light">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-success mb-0">DIRECTORIO BENITOURS</h6>
                        <small class="text-muted extra-small">Trinidad - Beni, Bolivia</small>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">N° Recibo:</span>
                        <strong class="font-monospace text-dark" id="reciboModalNumero">REC-001</strong>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">Establecimiento:</span>
                        <strong class="text-dark" id="reciboModalComercio">—</strong>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">Fecha:</span>
                        <span class="text-dark" id="reciboModalFecha">—</span>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">Concepto:</span>
                        <span class="text-dark" id="reciboModalMeses">—</span>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">Método:</span>
                        <span class="text-dark" id="reciboModalCanal">—</span>
                    </div>
                    <div class="d-flex justify-content-between small border-bottom pb-2 mb-2">
                        <span class="text-muted">Contacto:</span>
                        <span class="text-dark" id="reciboModalContacto">—</span>
                    </div>
                    <div class="d-flex justify-content-between fs-5 fw-bold text-success pt-2">
                        <span>Total Pagado:</span>
                        <span id="reciboModalMonto">Bs 0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="enviarWhatsAppBorrador()">
                    <i class="bi bi-whatsapp me-1"></i> Enviar por WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>
