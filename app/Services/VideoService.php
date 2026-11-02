<?php
namespace App\Services;

/**
 * Servicio para validación, compresión y optimización de videos para la web y dispositivos móviles.
 */
class VideoService {

    /**
     * Comprueba si la herramienta ffmpeg está disponible en el servidor.
     */
    public static function esFfmpegDisponible(): bool {
        // En Windows/Linux comprobar mediante comando de versión
        $comando = DIRECTORY_SEPARATOR === '\\' ? 'where ffmpeg 2>NUL' : 'which ffmpeg 2>/dev/null';
        $salida = @shell_exec($comando);
        return !empty($salida);
    }

    /**
     * Comprime y optimiza un video MP4 para streaming web y móviles:
     * - Remueve la pista de audio innecesaria en fondos silenciados (ahorra espacio).
     * - Aplica códec H.264 Web estándar compatible con el 100% de celulares.
     * - Escala inteligentemente manteniendo proporción (máx 960px de ancho).
     * - Aplica flag '+faststart' para mover los metadatos al inicio del archivo (inicio instantáneo).
     *
     * @return array [
     *    'exito' => bool,
     *    'tamano_original' => int,
     *    'tamano_final' => int,
     *    'comprimido_con_ffmpeg' => bool,
     *    'error' => ?string
     * ]
     */
    public static function optimizarYGuardar(string $origenTmp, string $destinoFinal): array {
        $tamanoOriginal = file_exists($origenTmp) ? (int)filesize($origenTmp) : 0;
        $dirDestino = dirname($destinoFinal);
        if (!is_dir($dirDestino)) {
            @mkdir($dirDestino, 0755, true);
        }

        if (!self::esFfmpegDisponible()) {
            // Fallback transparente si ffmpeg no está en el servidor
            $movido = is_uploaded_file($origenTmp) ? move_uploaded_file($origenTmp, $destinoFinal) : copy($origenTmp, $destinoFinal);
            return [
                'exito' => $movido,
                'tamano_original' => $tamanoOriginal,
                'tamano_final' => file_exists($destinoFinal) ? (int)filesize($destinoFinal) : 0,
                'comprimido_con_ffmpeg' => false,
                'error' => $movido ? null : 'No se pudo mover el archivo de video al destino.'
            ];
        }

        // Archivo temporal para compresión segura antes de reemplazar el destino
        $archivoTempOpt = $dirDestino . DIRECTORY_SEPARATOR . 'opt_' . bin2hex(random_bytes(6)) . '.mp4';

        // Escapar rutas para la consola según el sistema operativo
        $cmdOrigen = escapeshellarg($origenTmp);
        $cmdDestino = escapeshellarg($archivoTempOpt);

        // Parámetros optimizados para video de fondo web y celular:
        // -an: sin audio
        // -vcodec libx264: máxima compatibilidad universal
        // -crf 28: excelente balance de calidad y compresión
        // -preset faster: codificación ágil en servidor
        // -vf "scale='min(960,iw)':-2": escala a max 960px manteniendo proporción par
        // -pix_fmt yuv420p: requerido para reproducción en navegadores iOS/Android
        // -movflags +faststart: streaming inmediato en el primer segundo
        $comando = "ffmpeg -y -i {$cmdOrigen} -an -vcodec libx264 -crf 28 -preset faster -b:v 500k -maxrate 700k -bufsize 1200k -vf \"scale='min(960,iw)':-2\" -pix_fmt yuv420p -movflags +faststart {$cmdDestino} 2>&1";

        @exec($comando, $salida, $codigoRetorno);

        if ($codigoRetorno === 0 && file_exists($archivoTempOpt) && filesize($archivoTempOpt) > 1024) {
            // Reemplazar destino de forma atómica
            if (file_exists($destinoFinal)) {
                @unlink($destinoFinal);
            }
            @rename($archivoTempOpt, $destinoFinal);

            $tamanoFinal = file_exists($destinoFinal) ? (int)filesize($destinoFinal) : 0;
            return [
                'exito' => true,
                'tamano_original' => $tamanoOriginal,
                'tamano_final' => $tamanoFinal,
                'comprimido_con_ffmpeg' => true,
                'error' => null
            ];
        }

        // En caso de que ffmpeg falle durante la ejecución, usar archivo original como respaldo
        if (file_exists($archivoTempOpt)) {
            @unlink($archivoTempOpt);
        }

        $movido = is_uploaded_file($origenTmp) ? move_uploaded_file($origenTmp, $destinoFinal) : copy($origenTmp, $destinoFinal);
        return [
            'exito' => $movido,
            'tamano_original' => $tamanoOriginal,
            'tamano_final' => file_exists($destinoFinal) ? (int)filesize($destinoFinal) : 0,
            'comprimido_con_ffmpeg' => false,
            'error' => $movido ? null : 'Fallo en la optimización y en el respaldo del video.'
        ];
    }
}
