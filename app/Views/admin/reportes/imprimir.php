<style>
@media print {
    #adminSidebar, #adminTopbar, .sidebar-overlay { display: none !important; }
    #adminContent { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    .print-report { box-shadow: none !important; border: 0 !important; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; }
}
</style>
<div class="container-fluid py-4 print-report">
    <header class="text-center border-bottom pb-3 mb-4">
        <h2 class="fw-bold mb-1">BENITOURS TRINIDAD</h2>
        <p class="mb-1 text-muted"><?= htmlspecialchars($titulo) ?></p>
        <small class="text-muted">Fecha de emisión: <?= date('d/m/Y H:i') ?></small>
    </header>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr><?php foreach ($cabeceras as $cabecera): ?><th><?= htmlspecialchars($cabecera) ?></th><?php endforeach; ?></tr></thead>
            <tbody>
                <?php if (!$filas): ?><tr><td colspan="<?= count($cabeceras) ?>" class="text-center py-4 text-muted">No se encontraron registros para este informe.</td></tr><?php endif; ?>
                <?php foreach ($filas as $fila): ?><tr><?php foreach ($fila as $valor): ?><td><?= htmlspecialchars((string)($valor ?? '')) ?></td><?php endforeach; ?></tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>window.addEventListener('load', () => window.print());</script>
