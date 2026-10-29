<?php
require __DIR__ . '/../app/bootstrap_env.php';
require __DIR__ . '/../app/Core/Autoloader.php';
App\Core\Autoloader::register();

$config = App\Services\TelegramService::configuracion();
$token = $config['telegram_token'];

$ch = curl_init("https://api.telegram.org/bot{$token}/getWebhookInfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
echo "Webhook Info:\n" . $res . "\n";
