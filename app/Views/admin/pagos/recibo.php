<?php
/**
 * Vista de Recibo Oficial de Pago - BeniTours
 * Optimizado para pantalla y para impresión en formato Ticket / Recibo estándar (@media print)
 */
$nombreComercio = $pago['nombre_establecimiento'] ?? 'Comercio Afiliado';

// Generar o sanitizar número oficial correlativo
$numComp = trim((string)($pago['numero_comprobante'] ?? ''));
if ($numComp === '' || $numComp === 'SIN-NUMERO') {
    $reciboNumero = 'REC-' . date('Y', strtotime($pago['created_at'])) . '-' . str_pad((string)$pago['id_pago'], 5, '0', STR_PAD_LEFT);
} else {
    $reciboNumero = $numComp;
}

$fechaPago      = date('d/m/Y', strtotime($pago['fecha_pago_declarada'] ?: $pago['created_at']));
$fechaRegistro  = date('d/m/Y H:i', strtotime($pago['created_at']));
$monto          = (float)$pago['monto'];
$meses          = (int)$pago['meses_duracion'];
$metodo         = $pago['metodo_pago'];
$estado         = $pago['estado'];
$contacto       = $pago['whatsapp_contacto'] ?: ($pago['telefono_contacto'] ?: 'No registrado');
$direccion      = $pago['direccion'] ?: 'Trinidad, Beni';

// Corrección de nombre administrativo: siempre BeniTours
$adminNombre    = str_replace('BeniTurs', 'BeniTours', (string)($pago['nombre_admin'] ?? 'Administración BeniTours'));

$fechaInicio = !empty($pago['fecha_inicio']) ? date('d/m/Y', strtotime($pago['fecha_inicio'])) : 'Pendiente de activación';
$fechaFin    = !empty($pago['fecha_vencimiento']) ? date('d/m/Y', strtotime($pago['fecha_vencimiento'])) : '—';

// Formato de WhatsApp
$waNumero = preg_replace('/\D/', '', $contacto);
if (!empty($waNumero) && strlen($waNumero) === 8) {
    $waNumero = '591' . $waNumero;
}
$waMensaje = "🧾 *RECIBO OFICIAL DE PAGO - BENITOURS*\n\n" .
    "Estimado(a) *{$nombreComercio}*,\n" .
    "Te compartimos el comprobante de tu suscripción en el Directorio Turístico *BeniTours*:\n\n" .
    "🔹 *Recibo N°:* {$reciboNumero}\n" .
    "🔹 *Monto:* Bs " . number_format($monto, 2) . "\n" .
    "🔹 *Plan:* {$meses} mes(es) de cobertura\n" .
    "🔹 *Vigencia:* {$fechaInicio} al {$fechaFin}\n" .
    "🔹 *Método:* {$metodo}\n" .
    "🔹 *Estado:* {$estado}\n\n" .
    "¡Tu comercio está visible para turistas y visitantes de Trinidad! 🌴🇧🇴\n" .
    "Gracias por tu preferencia.";
$waUrl = "https://api.whatsapp.com/send?phone=" . urlencode($waNumero) . "&text=" . urlencode($waMensaje);
?>

<div class="container py-4 no-print">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a href="<?= htmlspecialchars($baseUrl) ?>/admin/pagos" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver a Pagos
            </a>
        </div>
        <div class="d-flex gap-2">
            <?php if (!empty($waNumero)): ?>
                <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" class="btn btn-success rounded-pill px-3 fw-bold shadow-sm">
                    <i class="bi bi-whatsapp me-1"></i> Enviar a WhatsApp
                </a>
            <?php endif; ?>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir Recibo
            </button>
        </div>
    </div>
</div>

<!-- Contenedor Imprimible del Recibo -->
<div class="recibo-imprimible-wrapper">
    <div class="recibo-card">
        <!-- Encabezado -->
        <div class="recibo-header">
            <div class="d-flex align-items-center gap-3">
                <div class="recibo-logo">
                    <i class="bi bi-compass-fill"></i>
                </div>
                <div>
                    <h3 class="recibo-titulo">BENITOURS</h3>
                    <p class="recibo-subtitulo">Directorio Turístico & Comercial de Trinidad</p>
                    <p class="recibo-datos-empresa">Trinidad, Beni - Bolivia | info@benitours.bo</p>
                </div>
            </div>
            <div class="recibo-num-box">
                <div class="recibo-num-label">RECIBO DE CAJA</div>
                <div class="recibo-num-valor"><?= htmlspecialchars($reciboNumero) ?></div>
                <div class="recibo-num-fecha">Emisión: <?= htmlspecialchars($fechaPago) ?></div>
            </div>
        </div>

        <hr class="recibo-divider">

        <!-- Datos del Cliente y Ficha -->
        <div class="recibo-seccion-datos">
            <div class="recibo-dato-col">
                <span class="recibo-label">ESTABLECIMIENTO COMERCIAL:</span>
                <strong class="recibo-valor-destacado"><?= htmlspecialchars($nombreComercio) ?></strong>
                <span class="recibo-dato-extra"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($direccion) ?></span>
            </div>
            <div class="recibo-dato-col text-end">
                <span class="recibo-label">CONTACTO / RESPONSABLE:</span>
                <span class="recibo-valor-normal"><i class="bi bi-whatsapp"></i> <?= htmlspecialchars($contacto) ?></span>
                <span class="recibo-dato-extra">Atendido por: <?= htmlspecialchars($adminNombre) ?></span>
            </div>
        </div>

        <!-- Tabla Desglose -->
        <table class="recibo-tabla">
            <thead>
                <tr>
                    <th>DESCRIPCIÓN / CONCEPTO</th>
                    <th class="text-center">PERIODO</th>
                    <th class="text-end">SUBTOTAL (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Suscripción y Difusión en Catálogo BeniTours</strong>
                        <div class="recibo-detalle-menor">
                            Cobertura autorizada: <?= htmlspecialchars($fechaInicio) ?> al <?= htmlspecialchars($fechaFin) ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge-recibo"><?= $meses ?> Mes(es)</span>
                    </td>
                    <td class="text-end fw-bold">
                        <?= number_format($monto, 2) ?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end recibo-total-label">TOTAL CANCELADO:</td>
                    <td class="text-end recibo-total-monto">Bs <?= number_format($monto, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Información de Pago y Estado -->
        <div class="recibo-pie-info">
            <div>
                <span class="recibo-label">FORMA DE PAGO:</span>
                <span class="recibo-metodo-badge"><i class="bi bi-cash-coin me-1"></i><?= htmlspecialchars($metodo) ?></span>
                <span class="ms-2 badge <?= $estado === 'CONFIRMADO' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= htmlspecialchars($estado) ?>
                </span>
            </div>
            <div class="extra-small text-muted text-end">
                Comprobante electrónico válido internamente · BeniTours
            </div>
        </div>

        <?php if (!empty($pago['observaciones'])): ?>
            <div class="recibo-observaciones">
                <small class="text-muted fw-bold">OBSERVACIONES:</small>
                <div class="small text-dark"><?= htmlspecialchars($pago['observaciones']) ?></div>
            </div>
        <?php endif; ?>

        <!-- Sello de Validación Digital y Firmas Físicas -->
        <div class="recibo-seguridad-box my-3 p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-patch-check-fill text-success fs-3"></i>
                <div>
                    <strong class="d-block text-dark small">Comprobante Certificado Digitalmente</strong>
                    <span class="extra-small text-muted">ID Transacción: #<?= (int)$pago['id_pago'] ?> &bull; Emisión Oficial BeniTours &bull; <?= htmlspecialchars($fechaRegistro) ?></span>
                </div>
            </div>
            <div class="text-md-end">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 extra-small fw-bold">
                    <i class="bi bi-shield-lock me-1"></i> Válido sin firma manual para fines de difusión
                </span>
            </div>
        </div>

        <!-- Firmas para Impresión Física -->
        <div class="recibo-firmas">
            <div class="recibo-firma-box">
                <div class="recibo-linea-firma"></div>
                <span>Entregué Conforme (Cliente)</span>
            </div>
            <div class="recibo-firma-box">
                <div class="recibo-linea-firma"></div>
                <span>Recibí Conforme (Cajero / BeniTours)</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos en Pantalla */
.recibo-imprimible-wrapper {
    max-width: 780px;
    margin: 0 auto 40px auto;
}
.recibo-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 35px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
.recibo-logo {
    width: 48px;
    height: 48px;
    background: #2A9D8F;
    color: #fff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}
.recibo-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 15px;
}
.recibo-titulo {
    font-size: 22px;
    font-weight: 800;
    margin: 0;
    color: #0f172a;
    letter-spacing: -0.5px;
}
.recibo-subtitulo {
    margin: 0;
    font-size: 13px;
    font-weight: 600;
    color: #217E73;
}
.recibo-datos-empresa {
    margin: 0;
    font-size: 11px;
    color: #64748b;
}
.recibo-num-box {
    text-align: right;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 10px;
}
.recibo-num-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
}
.recibo-num-valor {
    font-size: 18px;
    font-weight: 800;
    color: #217E73;
    font-family: monospace;
}
.recibo-num-fecha {
    font-size: 11px;
    color: #64748b;
}
.recibo-divider {
    margin: 20px 0;
    border-color: #e2e8f0;
}
.recibo-seccion-datos {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    gap: 20px;
}
.recibo-dato-col {
    display: flex;
    flex-direction: column;
}
.recibo-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    letter-spacing: 0.5px;
}
.recibo-valor-destacado {
    font-size: 17px;
    color: #1e293b;
}
.recibo-valor-normal {
    font-size: 14px;
    color: #334155;
    font-weight: 600;
}
.recibo-dato-extra {
    font-size: 12px;
    color: #64748b;
    margin-top: 3px;
}
.recibo-tabla {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
.recibo-tabla th {
    background: #f1f5f9;
    padding: 10px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    border-bottom: 2px solid #cbd5e1;
}
.recibo-tabla td {
    padding: 14px 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
}
.recibo-detalle-menor {
    font-size: 11px;
    color: #64748b;
    margin-top: 4px;
}
.badge-recibo {
    background: #e2e8f0;
    color: #334155;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.recibo-total-label {
    font-weight: 800;
    font-size: 14px;
    color: #1e293b;
    padding-top: 15px;
}
.recibo-total-monto {
    font-weight: 800;
    font-size: 20px;
    color: #217E73;
    padding-top: 15px;
}
.recibo-pie-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 18px;
}
.recibo-metodo-badge {
    background: #fff;
    border: 1px solid #cbd5e1;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
}
.recibo-observaciones {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 25px;
}
.recibo-firmas {
    display: flex;
    justify-content: space-around;
    margin-top: 50px;
    padding-top: 10px;
}
.recibo-firma-box {
    text-align: center;
    width: 220px;
}
.recibo-linea-firma {
    border-top: 1px dashed #94a3b8;
    margin-bottom: 8px;
}
.recibo-firma-box span {
    font-size: 11px;
    color: #64748b;
}

/* Modo Impresión (@media print) */
@media print {
    body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .no-print, header, nav, footer, .sidebar, .dash-header {
        display: none !important;
    }
    .recibo-imprimible-wrapper {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .recibo-card {
        border: none !important;
        box-shadow: none !important;
        padding: 15px !important;
    }
}
</style>

<?php if (!empty($autoPrint)): ?>
<script>
window.addEventListener('DOMContentLoaded', function() {
    window.print();
});
</script>
<?php endif; ?>
