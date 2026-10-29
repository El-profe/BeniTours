<?php
$url = 'https://bit-ideas.com/benitourss/telegram_webhook.php';
$secret = getenv('TELEGRAM_WEBHOOK_SECRET') ?: '';

if ($secret === '') {
    fwrite(STDERR, "Configura TELEGRAM_WEBHOOK_SECRET antes de ejecutar este script.\n");
    exit(1);
}

$payload = json_encode([
    'update_id' => 123456789,
    'message' => [
        'message_id' => 999,
        'from' => [
            'id' => 5823966432,
            'is_bot' => false,
            'first_name' => 'Admin'
        ],
        'chat' => [
            'id' => 5823966432,
            'type' => 'private'
        ],
        'date' => time(),
        'text' => '/resumen'
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Telegram-Bot-Api-Secret-Token: ' . $secret
]);
curl_setopt($ch, CURLOPT_HEADER, true);

$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $httpCode\n";
echo "Response:\n" . $res . "\n";
