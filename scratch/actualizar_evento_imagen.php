<?php
require __DIR__ . '/../app/bootstrap_env.php';
require __DIR__ . '/../app/Core/Autoloader.php';
App\Core\Autoloader::register();

try {
    $pdo = App\Core\Database::getConnection();
    $stmt = $pdo->query("SELECT id_evento, titulo, imagen_archivo FROM eventos_agenda");
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Eventos en BD:\n";
    foreach ($filas as $f) {
        echo "ID {$f['id_evento']}: {$f['titulo']} -> {$f['imagen_archivo']}\n";
    }

    $afectados = $pdo->exec("UPDATE eventos_agenda SET imagen_archivo = 'EntradaFolklorica2026.jpeg' WHERE imagen_archivo LIKE '%EntradaFolkl%'");
    echo "Filas actualizadas: {$afectados}\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
