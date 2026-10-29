<?php
require __DIR__ . '/../app/bootstrap_env.php';
require __DIR__ . '/../app/Core/Autoloader.php';
App\Core\Autoloader::register();

$url = 'https://bit-ideas.com/benitourss/telegram_webhook.php';
echo "Registrando webhook en Telegram a: {$url} ...\n";

try {
    App\Services\TelegramService::registrarWebhook($url);
    echo "¡Webhook registrado exitosamente en Telegram!\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$config = App\Services\TelegramService::configuracion();
$token = $config['telegram_token'];

$ch = curl_init("https://api.telegram.org/bot{$token}/getWebhookInfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
echo "Estado actual del Webhook:\n" . $res . "\n";
