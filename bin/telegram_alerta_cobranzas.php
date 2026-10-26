<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/app/bootstrap_env.php';
require dirname(__DIR__) . '/app/Core/Autoloader.php';
App\Core\Autoloader::register();
date_default_timezone_set('America/La_Paz');

if (!App\Services\TelegramService::configurado()) {
    fwrite(STDERR, "Telegram no está configurado. Verifique TELEGRAM_BOT_TOKEN y TELEGRAM_ADMIN_CHAT_ID.\n");
    exit(1);
}

echo "Ejecutando alerta matutina de cobranzas y vencimientos...\n";
try {
    App\Services\TelegramService::enviarAlertaMatutinaDiaria();
    echo "Proceso de alerta matutina ejecutado correctamente.\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Error al enviar alerta: " . $e->getMessage() . "\n");
    exit(1);
}
