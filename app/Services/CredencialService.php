<?php
namespace App\Services;

use RuntimeException;
use App\Core\Database;

class CredencialService {
    /**
     * Genera una contraseña corta, memorable y fácil de escribir en celular.
     * Formato: "Beni" + 3 dígitos aleatorios (ej: "Beni385", "Beni742").
     * Total: 7 caracteres, 1 mayúscula, números.
     */
    public static function generarClave(): string {
        return 'Beni' . random_int(100, 999);
    }

    /**
     * Genera un nombre de usuario corto, limpio y representativo del comercio.
     * Toma palabras clave del nombre (evitando palabras comunes como restaurante, el, la, etc.)
     * y le concatena 3 dígitos para garantizar unicidad.
     * Total aproximado: 6 a 12 caracteres (ej: "tatu381", "bernardo512", "mentirosa219").
     */
    public static function generarUsuarioCorto(string $nombreComercio, int $idLugar = 0): string {
        // 1. Limpiar acentos y caracteres especiales
        $nombreLimpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombreComercio);
        $nombreLimpio = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $nombreLimpio));

        // 2. Palabras vacías a ignorar para buscar la palabra más representativa
        $stopWords = [
            'restaurante', 'restauran', 'restaurant', 'cafe', 'bar', 'hotel', 'alojamiento',
            'karaoke', 'snack', 'picanteria', 'churrasqueria', 'salteñeria', 'salteneria',
            'comercial', 'tienda', 'el', 'la', 'los', 'las', 'de', 'del', 'y', 'en', 'san', 'santa',
            'don', 'dona', 'doña'
        ];

        $palabras = preg_split('/\s+/', trim($nombreLimpio));
        $palabraElegida = '';

        foreach ($palabras as $p) {
            $p = trim($p);
            if (strlen($p) >= 3 && !in_array($p, $stopWords, true)) {
                $palabraElegida = $p;
                break;
            }
        }

        // Si todas eran stop words o quedó vacía, tomar la primera disponible o "negocio"
        if ($palabraElegida === '') {
            $palabraElegida = !empty($palabras[0]) && strlen($palabras[0]) >= 3 ? $palabras[0] : 'negocio';
        }

        // Limitar a máximo 8 caracteres para mantenerlo súper corto
        $base = substr($palabraElegida, 0, 8);

        // 3. Buscar sufijo único corto
        $db = Database::getConnection();
        for ($intento = 0; $intento < 15; $intento++) {
            $sufijo = (string)random_int(100, 999);
            $candidato = $base . $sufijo;
            $stmt = $db->prepare('SELECT COUNT(*) FROM cuentas_negocio WHERE usuario = ?');
            $stmt->execute([$candidato]);
            if ((int)$stmt->fetchColumn() === 0) {
                return $candidato;
            }
        }

        // Fallback con ID si hubo muchas colisiones
        return $base . ($idLugar > 0 ? $idLugar : random_int(1000, 9999));
    }

    private static function llave(): string {
        $config = require dirname(__DIR__, 2) . '/config/comercial.php';
        if ($config['credential_key'] !== '') {
            $key = base64_decode($config['credential_key'], true);
            if ($key === false || strlen($key) !== 32) throw new RuntimeException('Clave de cifrado inválida.');
            return $key;
        }
        $dir = dirname(__DIR__, 2) . '/storage/private';
        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) throw new RuntimeException('No se pudo preparar el cifrado.');
        $file = fopen($dir . '/credenciales.key', 'c+b');
        if (!$file || !flock($file, LOCK_EX)) throw new RuntimeException('No se pudo abrir la clave de cifrado.');
        try {
            $key = stream_get_contents($file);
            if ($key === '') {
                $key = random_bytes(32);
                if (fwrite($file, $key) !== 32 || !fflush($file)) throw new RuntimeException('No se pudo guardar la clave de cifrado.');
                @chmod($dir . '/credenciales.key', 0600);
            }
            if (strlen($key) !== 32) throw new RuntimeException('Clave de cifrado dañada.');
            return $key;
        } finally { flock($file, LOCK_UN); fclose($file); }
    }

    public static function cifrar(string $texto): string {
        $iv = random_bytes(12);
        $cifrado = openssl_encrypt($texto, 'aes-256-gcm', self::llave(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cifrado === false) throw new RuntimeException('No se pudieron proteger las credenciales.');
        return base64_encode($iv . $tag . $cifrado);
    }

    public static function descifrar(string $texto): string {
        $data = base64_decode($texto, true);
        if ($data === false || strlen($data) < 29) throw new RuntimeException('Credenciales no disponibles.');
        $plano = openssl_decrypt(substr($data, 28), 'aes-256-gcm', self::llave(), OPENSSL_RAW_DATA, substr($data, 0, 12), substr($data, 12, 16));
        if ($plano === false) throw new RuntimeException('No se pudieron recuperar las credenciales.');
        return $plano;
    }
}
