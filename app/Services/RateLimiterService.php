<?php
namespace App\Services;

/**
 * Servicio de limitación de tasa (Rate Limiter / Anti-Fuerza Bruta).
 * Protege los endpoints de autenticación contra ataques de diccionario y credential stuffing.
 * Almacena los contadores en archivos aislados dentro de storage/cache/rate_limits/.
 */
class RateLimiterService {
    private static ?string $directorio = null;

    private static function getDirectorio(): string {
        if (self::$directorio === null) {
            self::$directorio = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'rate_limits' . DIRECTORY_SEPARATOR;
            if (!is_dir(self::$directorio)) {
                @mkdir(self::$directorio, 0750, true);
            }
        }
        return self::$directorio;
    }

    /**
     * Genera una clave segura normalizada basada en el identificador (ej: login_admin_IP_USUARIO).
     */
    private static function obtenerRuta(string $clave): string {
        $hash = hash('sha256', $clave);
        return self::getDirectorio() . 'rl_' . $hash . '.json';
    }

    /**
     * Obtiene la IP del cliente considerando posibles proxies reversos o túneles de forma segura.
     */
    public static function obtenerIpCliente(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }

    /**
     * Verifica si una clave ha superado el límite de intentos permitidos.
     * Retorna true si aún está dentro del límite permitido, o false si está bloqueado.
     */
    public static function estaPermitido(string $clave, int $maxIntentos = 5, int $tiempoBloqueoSegundos = 900): bool {
        $ruta = self::obtenerRuta($clave);
        if (!file_exists($ruta)) {
            return true;
        }

        $contenido = @file_get_contents($ruta);
        if ($contenido === false) {
            return true;
        }

        $datos = json_decode($contenido, true);
        if (!is_array($datos)) {
            @unlink($ruta);
            return true;
        }

        $ahora = time();

        // Si el bloqueo ha expirado, reiniciar
        if (isset($datos['bloqueado_hasta']) && $ahora >= $datos['bloqueado_hasta']) {
            @unlink($ruta);
            return true;
        }

        // Si está actualmente bloqueado
        if (isset($datos['bloqueado_hasta']) && $ahora < $datos['bloqueado_hasta']) {
            return false;
        }

        // Si la ventana de tiempo del primer intento ya expiró (más de 15 min), reiniciar
        if (isset($datos['primer_intento']) && ($ahora - $datos['primer_intento']) > $tiempoBloqueoSegundos) {
            @unlink($ruta);
            return true;
        }

        return ($datos['intentos'] ?? 0) < $maxIntentos;
    }

    /**
     * Registra un intento fallido y activa el bloqueo si se alcanza el umbral.
     */
    public static function registrarFallo(string $clave, int $maxIntentos = 5, int $tiempoBloqueoSegundos = 900): void {
        $ruta = self::obtenerRuta($clave);
        $ahora = time();
        $datos = ['intentos' => 0, 'primer_intento' => $ahora, 'bloqueado_hasta' => null];

        if (file_exists($ruta)) {
            $contenido = @file_get_contents($ruta);
            if ($contenido !== false) {
                $actual = json_decode($contenido, true);
                if (is_array($actual)) {
                    $datos = $actual;
                }
            }
        }

        $datos['intentos'] = ($datos['intentos'] ?? 0) + 1;
        if (!isset($datos['primer_intento'])) {
            $datos['primer_intento'] = $ahora;
        }

        if ($datos['intentos'] >= $maxIntentos) {
            $datos['bloqueado_hasta'] = $ahora + $tiempoBloqueoSegundos;
        }

        @file_put_contents($ruta, json_encode($datos), LOCK_EX);
    }

    /**
     * Retorna los segundos restantes de bloqueo.
     */
    public static function segundosRestantes(string $clave): int {
        $ruta = self::obtenerRuta($clave);
        if (!file_exists($ruta)) {
            return 0;
        }

        $contenido = @file_get_contents($ruta);
        if ($contenido === false) {
            return 0;
        }

        $datos = json_decode($contenido, true);
        if (!is_array($datos) || empty($datos['bloqueado_hasta'])) {
            return 0;
        }

        $restante = $datos['bloqueado_hasta'] - time();
        return max(0, $restante);
    }

    /**
     * Limpia los intentos registrados tras un inicio de sesión exitoso.
     */
    public static function limpiar(string $clave): void {
        $ruta = self::obtenerRuta($clave);
        if (file_exists($ruta)) {
            @unlink($ruta);
        }
    }
}
