<?php
require_once __DIR__ . '/../app/bootstrap_env.php';
require_once __DIR__ . '/../app/Core/Autoloader.php';
\App\Core\Autoloader::register();

$db = \App\Core\Database::getConnection();

$stmt = $db->query("SELECT l.id_lugar, l.nombre, l.tipo_lugar, l.horario_atencion, l.telefono_contacto, c.nombre as categoria, c.icono as cat_icono, c.slug as cat_slug 
                    FROM lugares l 
                    JOIN categorias c ON l.id_categoria = c.id_categoria 
                    WHERE c.slug IN ('hospitales-salud', 'policia-seguridad', 'bancos-cajeros') 
                    ORDER BY c.id_categoria, l.id_lugar");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "LUGARES DE UTILIDAD PÚBLICA REGISTRADOS (" . count($res) . " lugares):\n";
foreach ($res as $r) {
    echo "ID: {$r['id_lugar']} | [{$r['categoria']}] {$r['nombre']} | Horario: {$r['horario_atencion']} | Tel: {$r['telefono_contacto']}\n";
}
