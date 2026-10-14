<?php
namespace App\Services;

/**
 * Valida formatos, pesos y almacena las imágenes en storage/uploads/lugares/
 */
class ImagenService {
    private static ?string $directorio = null;
    private static ?string $directorioPromociones = null;

    public static function getDirectorioStorage(): string {
        $dir = self::$directorio ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'lugares' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /** Almacenamiento exclusivo para flyers e imágenes de promociones. */
    public static function getDirectorioPromociones(): string {
        $dir = self::$directorioPromociones ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'promociones' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /** Devuelve una ruta solo para nombres internos permitidos. */
    public static function getRutaArchivo(string $nombre): ?string {
        if (!preg_match('/\A(lugar|promo)_[a-f0-9]{16}_[0-9]+\.(jpg|png|webp)\z/D', $nombre)) {
            return null;
        }
        if (str_starts_with($nombre, 'promo_')) {
            return self::getDirectorioPromociones() . $nombre;
        }

        $rutaActual = self::getDirectorioStorage() . $nombre;
        if (is_file($rutaActual)) {
            return $rutaActual;
        }

        // Compatibilidad con las fotografias existentes antes de reorganizar el almacenamiento.
        $rutaRespaldo = dirname(rtrim(self::getDirectorioStorage(), DIRECTORY_SEPARATOR))
            . DIRECTORY_SEPARATOR . 'lugares_backup_raw' . DIRECTORY_SEPARATOR . $nombre;

        return is_file($rutaRespaldo) ? $rutaRespaldo : $rutaActual;
    }

    public static function eliminarArchivo(string $nombre): bool {
        $ruta = self::getRutaArchivo($nombre);
        if ($ruta === null) {
            throw new \InvalidArgumentException('Nombre de fotografía inválido.');
        }
        if (is_link($ruta)) throw new \RuntimeException('Archivo no permitido.');
        return !is_file($ruta) || unlink($ruta);
    }

    /**
     * Procesa la subida de un archivo físico proveniente de $_FILES
     */
    public static function subir(array $file): ?array {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // 1. Validar tamaño (máximo 5 MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new \RuntimeException('La imagen excede el tamaño máximo permitido de 5 MB.');
        }

        // 2. Validar tipo MIME real del archivo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $mimesPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $mimesPermitidos)) {
            throw new \RuntimeException('Formato de imagen no admitido. Use JPG, PNG o WEBP.');
        }

        $extension = $mimesPermitidos[$mime];
        $nombreServidor = 'lugar_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destino = self::getDirectorioStorage() . $nombreServidor;

        if (!self::optimizarYGuardar($file['tmp_name'], $destino, $mime)) {
            throw new \RuntimeException('No se pudo guardar la fotografía en el servidor.');
        }

        return [
            'nombre_archivo'  => $nombreServidor,
            'nombre_original' => basename($file['name']),
            'mime_type'       => $mime,
            'tamano_bytes'    => (int)filesize($destino)
        ];
    }

    /** Procesa y guarda un flyer en el directorio exclusivo de promociones. */
    public static function subirPromocion(array $file): ?array {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new \RuntimeException('La imagen excede el tamaÃ±o mÃ¡ximo permitido de 5 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $mimesPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!array_key_exists($mime, $mimesPermitidos)) {
            throw new \RuntimeException('Formato de imagen no admitido. Use JPG, PNG o WEBP.');
        }

        $nombreServidor = 'promo_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $mimesPermitidos[$mime];
        $destino = self::getDirectorioPromociones() . $nombreServidor;
        if (!self::optimizarYGuardar($file['tmp_name'], $destino, $mime)) {
            throw new \RuntimeException('No se pudo guardar la imagen de la promociÃ³n.');
        }

        return [
            'nombre_archivo'  => $nombreServidor,
            'nombre_original' => basename($file['name']),
            'mime_type'       => $mime,
            'tamano_bytes'    => (int)filesize($destino)
        ];
    }

    /**
     * Redimensiona (máx 1200px) y comprime la imagen usando PHP GD antes de almacenarla
     */
    private static function optimizarYGuardar(string $origen, string $destino, string $mime, int $maxDim = 1200, int $calidad = 82): bool {
        if (!extension_loaded('gd')) {
            return move_uploaded_file($origen, $destino);
        }

        $info = @getimagesize($origen);
        if (!$info) {
            return move_uploaded_file($origen, $destino);
        }

        [$anchoOrig, $altoOrig] = $info;

        $srcImg = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($origen),
            'image/png'  => @imagecreatefrompng($origen),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($origen) : null,
            default      => null
        };

        if (!$srcImg) {
            return move_uploaded_file($origen, $destino);
        }

        $nuevoAncho = $anchoOrig;
        $nuevoAlto = $altoOrig;
        if ($anchoOrig > $maxDim || $altoOrig > $maxDim) {
            if ($anchoOrig >= $altoOrig) {
                $nuevoAncho = $maxDim;
                $nuevoAlto = (int)round(($altoOrig * $maxDim) / $anchoOrig);
            } else {
                $nuevoAlto = $maxDim;
                $nuevoAncho = (int)round(($anchoOrig * $maxDim) / $altoOrig);
            }
        }

        $dstImg = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            $trans = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
            imagefilledrectangle($dstImg, 0, 0, $nuevoAncho, $nuevoAlto, $trans);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOrig, $altoOrig);
        imagedestroy($srcImg);

        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($dstImg, $destino, $calidad),
            'image/png'  => imagepng($dstImg, $destino, 8),
            'image/webp' => function_exists('imagewebp') ? imagewebp($dstImg, $destino, $calidad) : false,
            default      => false
        };

        imagedestroy($dstImg);
        return $ok;
    }
}
