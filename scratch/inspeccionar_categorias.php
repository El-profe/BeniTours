<?php
require_once __DIR__ . '/../app/bootstrap_env.php';
require_once __DIR__ . '/../app/Core/Autoloader.php';
\App\Core\Autoloader::register();

$db = \App\Core\Database::getConnection();
$stmt = $db->query('SHOW COLUMNS FROM lugares');
echo "COLUMNAS EN LUGARES:\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}
