<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

require dirname(__DIR__) . '/app/bootstrap_env.php';
require dirname(__DIR__) . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();

$config = App\Services\TelegramService::configuracion();
$errores = [];
if (empty($config['telegram_activo'])) $errores[] = 'TELEGRAM_ACTIVO debe ser true.';
if (preg_match('/\A\d+:[A-Za-z0-9_-]+\z/D', (string)($config['telegram_token'] ?? '')) !== 1) {
    $errores[] = 'TELEGRAM_BOT_TOKEN no tiene un formato válido.';
}
if (preg_match('/\A-?[1-9]\d*\z/D', (string)($config['admin_chat_id'] ?? '')) !== 1) {
    $errores[] = 'TELEGRAM_ADMIN_CHAT_ID no es válido.';
}
if (preg_match('/\A[A-Za-z0-9_-]{16,256}\z/D', (string)($config['webhook_secret'] ?? '')) !== 1) {
    $errores[] = 'TELEGRAM_WEBHOOK_SECRET debe tener entre 16 y 256 caracteres seguros.';
}
if (!extension_loaded('curl')) $errores[] = 'La extensión PHP cURL no está habilitada.';

if ($errores) {
    fwrite(STDERR, "Telegram no está listo:\n- " . implode("\n- ", $errores) . "\n");
    exit(1);
}

try {
    $bot = App\Services\TelegramService::verificarConexion() ?: 'sin usuario';
    echo "Conexión con Telegram correcta: @{$bot}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "No se pudo conectar con Telegram. Revisa el token, la salida HTTPS del hosting y cURL.\n");
    exit(1);
}
