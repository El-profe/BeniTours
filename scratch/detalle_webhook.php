<?php
require __DIR__ . '/../app/bootstrap_env.php';
require __DIR__ . '/../app/Core/Autoloader.php';
App\Core\Autoloader::register();

$config = App\Services\TelegramService::configuracion();
$token = $config['telegram_token'];

// Ver detalles completos de webhookInfo
$ch = curl_init("https://api.telegram.org/bot{$token}/getWebhookInfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$data = json_decode($res, true);
print_r($data);
