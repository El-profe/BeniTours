<?php
/**
 * Inicialización del entorno de la aplicación BeniTours / Trinidad Turismo.
 */

// 1. Carga del entorno compartida por la aplicación, CLI y webhook de Telegram.
require_once __DIR__ . '/bootstrap_env.php';

// 2. Configuración de errores y manejador global según el entorno
$appEnv = env('APP_ENV', 'local');
$logDir = dirname(__DIR__) . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0750, true);
}

if ($appEnv === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('log_errors', '1');
    ini_set('error_log', $logDir . '/app_error.log');

    // Manejador global de excepciones no capturadas para prevenir fuga de información técnica
    set_exception_handler(function (\Throwable $e) use ($logDir) {
        $fecha = date('Y-m-d H:i:s');
        $mensaje = "[{$fecha}] Excepción no capturada: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
        @file_put_contents($logDir . '/app_error.log', $mensaje, FILE_APPEND | LOCK_EX);

        http_response_code(500);
        if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Ha ocurrido un error interno. Intente nuevamente más tarde.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error del Servidor</title><style>body{font-family:sans-serif;background:#f8fafc;color:#334155;text-align:center;padding:10vh 20px;}h1{color:#dc2626;}p{font-size:1.1rem;}</style></head><body><h1>500 - Error del Servidor</h1><p>Lo sentimos, ha ocurrido un problema temporal. Por favor regrese más tarde.</p><p><a href="/">Volver al inicio</a></p></body></html>';
        }
        exit();
    });
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// 3. Cargar configuración base
$config = require dirname(__DIR__) . '/config/app.php';

// 4. Establecer zona horaria oficial (Trinidad, Beni, Bolivia)
date_default_timezone_set($config['timezone'] ?? 'America/La_Paz');

// 5. Inicializar el autocargador de clases
require_once __DIR__ . '/Core/Autoloader.php';
\App\Core\Autoloader::register();

// 6. Cabeceras HTTP de seguridad emitidas de forma defensiva desde PHP
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)');
    header('Cross-Origin-Opener-Policy: same-origin-allow-popups');

    $csp = "default-src 'self'; "
        . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net; "
        . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com; "
        . "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
        . "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com https://*.google.com https://*.googleapis.com https://*.gstatic.com; "
        . "connect-src 'self' https://*.tile.openstreetmap.org https://unpkg.com https://api.telegram.org; "
        . "frame-ancestors 'self'; "
        . "object-src 'none'; "
        . "base-uri 'self';";

    header("Content-Security-Policy: {$csp}");

    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// 7. Configurar e iniciar sesión segura con control de expiración por inactividad
if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name'] ?? 'TRINI_TURISMO_SESSID');

    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure'   => $isHttps,
        'use_strict_mode' => 1
    ]);

    // Expiración por inactividad (Idle Timeout): 45 minutos (2700 segundos)
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 2700)) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

// 8. Inicializar Enrutador y registrar rutas
$router = new \App\Core\Router();

require_once dirname(__DIR__) . '/routes/web.php';
require_once dirname(__DIR__) . '/routes/api.php';

return $router;
