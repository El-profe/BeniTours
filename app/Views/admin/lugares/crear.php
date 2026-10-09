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
                    <div class="form-section-header border-success-subtle">
                        <div class="form-section-icon bg-success text-white">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h2 class="form-section-title text-success mb-0">Membresía Comercial y Cobro Inicial</h2>
                                <span class="badge bg-success rounded-pill px-3">Alta de Comerciante</span>
                            </div>
                            <p class="form-section-desc">Habilita usuario, contraseña y vigencia pagada en un solo paso.</p>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3 p-2 bg-white rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="crear_cuenta" id="switchCrearCuenta" value="1" checked>
                        <label class="form-check-label fw-bold text-dark small" for="switchCrearCuenta">
                            Aprovisionar cuenta y registrar cobro en el sistema de inmediato
                        </label>
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
                                <span class="badge bg-white text-muted border">Tarifa Base: Bs <?= number_format((float)($tarifaVigente['monto_mensual'] ?? 250.00), 2) ?>/mes</span>
                            </div>

                            <div class="btn-group w-100 p-1 bg-white rounded-4 border mb-3" role="group">
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-lugar active" id="btnModoLugarEstandar" onclick="seleccionarModoPlanLugar('ESTANDAR')">
                                    <i class="bi bi-grid-fill me-1"></i> Planes Predefinidos
                                </button>
                                <button type="button" class="btn btn-sm rounded-pill fw-semibold btn-modo-lugar" id="btnModoLugarPersonalizado" onclick="seleccionarModoPlanLugar('PERSONALIZADO')">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Plan Personalizado (A Medida)
                                </button>
                            </div>

                            <!-- Sección A: Planes Rápidos Predefinidos -->
                            <div id="seccion-lugar-planes-estandar">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-2 text-center border-2 cursor-pointer active" id="cardPlanLugar1" onclick="elegirPlanEstandarLugar(1, 250.00, 1, 0, 'MENSUAL')">
                                            <span class="badge bg-light text-muted extra-small mb-1">Básico</span>
                                            <div class="fw-bold text-dark small">1 Mes</div>
                                            <span class="text-success fw-bold small">Bs 250</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-2 text-center border-2 cursor-pointer" id="cardPlanLugar3" onclick="elegirPlanEstandarLugar(3, 712.50, 1, 37.50, 'MENSUAL')">
                                            <span class="badge bg-info-subtle text-info-emphasis extra-small mb-1">Trimestral</span>
                                            <div class="fw-bold text-dark small">3 Meses</div>
                                            <span class="text-success fw-bold small">Bs 712.50</span>
                                            <small class="text-success fw-semibold extra-small d-block">Ahorra 5%</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-2 text-center border-2 cursor-pointer" id="cardPlanLugar6" onclick="elegirPlanEstandarLugar(6, 1350.00, 1, 150.00, 'MENSUAL')">
                                            <span class="badge bg-primary-subtle text-primary extra-small mb-1">Semestral</span>
                                            <div class="fw-bold text-dark small">6 Meses</div>
                                            <span class="text-success fw-bold small">Bs 1,350</span>
                                            <small class="text-success fw-semibold extra-small d-block">Ahorra 10%</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card card-plan-lugar h-100 p-2 text-center border-2 cursor-pointer" id="cardPlanLugar12" onclick="elegirPlanEstandarLugar(12, 2500.00, 2, 500.00, 'ANUAL')">
                                            <span class="badge bg-warning text-dark fw-bold extra-small mb-1">★ Anual</span>
                                            <div class="fw-bold text-dark small">12 Meses</div>
                                            <span class="text-success fw-bold small">Bs 2,500</span>
                                            <small class="text-success fw-bold extra-small d-block">Ahorra Bs 500</small>
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
                                        <select id="selectReglaLugar" class="form-select form-select-sm" onchange="recalcularPlanPersonalizadoLugar()">
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

                        <!-- Tarjeta Dinámica de Liquidación & Ahorro -->
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-7">
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted extra-small">
                                            <span>Precio regular (<span id="txt-lugar-calc-meses">5</span> meses × Bs <?= number_format((float)($tarifaVigente['monto_mensual'] ?? 250.00), 2) ?>):</span>
                                            <span class="text-decoration-line-through fw-bold" id="txt-lugar-precio-regular">Bs 1,250.00</span>
                                        </div>

                                        <div id="caja-ahorro-lugar" class="alert alert-success py-2 px-3 mb-1 d-flex align-items-center gap-2 rounded-3 border border-success-subtle">
                                            <i class="bi bi-piggy-bank-fill fs-4 text-success flex-shrink-0"></i>
                                            <div>
                                                <div class="fw-bold text-success small" id="txt-lugar-ahorro-banner">
                                                    ¡El cliente se ahorra Bs 125.00 (10.0%)!
                                                </div>
                                                <div class="extra-small text-muted">
                                                    Equivale a pagar <strong class="text-success" id="txt-lugar-costo-mensual">Bs 225.00</strong> por mes.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="extra-small text-muted mt-1">
                                            <i class="bi bi-calendar-range text-primary me-1"></i>
                                            <span id="txt-lugar-proyeccion">Vigencia inicial proyectada...</span>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold extra-small text-dark mb-1">Importe Inicial a Cobrar (Bs) *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-success fw-bold border-end-0">Bs</span>
                                            <input type="number" step="0.01" name="monto" id="inputLugarMonto" class="form-control fw-bold fs-5 text-success border-start-0 text-center" value="1125.00" required oninput="alCambiarMontoLugarManual()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Método de Cobro y Nº Comprobante -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Método de Cobro *</label>
                            <select name="metodo_pago" class="form-select form-select-sm">
                                <option value="EFECTIVO" selected>💵 Efectivo (Cobrado en mano)</option>
                                <option value="QR">📱 QR Simple</option>
                                <option value="TRANSFERENCIA">🏦 Transferencia Bancaria</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Nº Recibo / Talonario</label>
                            <input type="text" name="numero_comprobante" class="form-control form-control-sm" placeholder="Ej. REC-001 (Opcional)">
                        </div>
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
    transition: all 0.2s ease-in-out;
    background: #fff;
    border-color: #dee2e6;
}
.card-plan-lugar:hover {
    border-color: #198754;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.card-plan-lugar.active {
    border-color: #198754 !important;
    background-color: rgba(25, 135, 84, 0.05);
}
.btn-modo-lugar.active {
    background-color: #ffffff !important;
    color: #198754 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
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
    actualizarDesgloseAhorroLugar(1, TARIFA_BASE_LUGAR, 0);
});
</script>
