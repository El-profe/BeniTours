<?php
/**
 * Configuración general del proyecto Trinidad Turismo / BeniTurs.
 * Accesible únicamente desde el backend.
 */

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = ($host === 'localhost' || str_starts_with($host, 'localhost:') || str_contains($host, '127.0.0.1'));

if ($isLocal) {
    // 1. Entorno local (Laragon / XAMPP): detectar la carpeta real del proyecto.
    // Ej.: /benitours/public/index.php => http://localhost/benitours/public
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $localPath = rtrim($scriptDirectory, '/.');
    $envBaseUrl = 'http://' . $host . ($localPath !== '' ? $localPath : '/benitours/public');
} else {
    // 2. Entorno remoto (cPanel, VPS, Cloudflare, etc.)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'))
        ? 'https://' : 'http://';

    $rawBaseUrl = (string)env('BENITURS_BASE_URL', '');
    if ($rawBaseUrl !== '' && !str_contains($rawBaseUrl, 'localhost') && !str_contains($rawBaseUrl, '127.0.0.1')) {
        // Si el usuario escribió http:// en .env pero el sitio carga por https://, forzar https://
        if ($protocol === 'https://' && str_starts_with($rawBaseUrl, 'http://')) {
            $rawBaseUrl = 'https://' . substr($rawBaseUrl, 7);
        }
        $envBaseUrl = $rawBaseUrl;
    } else {
        // Auto-detectar carpeta desde SCRIPT_NAME o REQUEST_URI
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $folder = trim(str_replace('/public', '', $scriptDir), '/');
        $envBaseUrl = $protocol . $host . ($folder !== '' ? '/' . $folder : '');
    }
}

return [
    'app_name'     => 'Trinidad Turismo & Directorio Comercial',
    'app_env'      => (string)env('APP_ENV', $isLocal ? 'local' : 'production'),
    'base_url'     => rtrim($envBaseUrl, '/'),
    'timezone'     => 'America/La_Paz',
    'currency'     => 'Bs',
    'tarifa_base'  => 250.00,
    'version'      => '1.0.0',
    'session_name' => 'TRINI_TURISMO_SESSID'
];
