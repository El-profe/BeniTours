<?php
namespace App\Controllers\Publico;

use App\Core\Controller;
use App\Models\Fotografia;
use App\Services\PublicacionService;
use App\Services\ImagenService;

class ImagenController extends Controller {

    /**
     * Sirve la fotografía comprobando permisos de visualización pública o admin
     */
    public function ver(): void {
        $nombreArchivo = basename($_GET['f'] ?? '');
        if (empty($nombreArchivo)) {
            http_response_code(404);
            exit();
        }

        $idLugar = null;
        $mimeType = null;

        $fotoModel = new Fotografia();
        $foto = $fotoModel->obtenerPorNombreArchivo($nombreArchivo);

        if ($foto) {
            $idLugar = (int)$foto['id_lugar'];
            $mimeType = $foto['mime_type'];
        } else {
            // Comprobación si la imagen pertenece a una promoción
            $db = \App\Core\Database::getConnection();
            $stmtPromo = $db->prepare('SELECT id_lugar FROM promociones WHERE imagen_archivo = ? LIMIT 1');
            $stmtPromo->execute([$nombreArchivo]);
            $promoIdLugar = $stmtPromo->fetchColumn();

            if ($promoIdLugar) {
                $idLugar = (int)$promoIdLugar;
            }
        }

        if (!$idLugar) {
            http_response_code(404);
            exit();
        }

        // Comprobación de seguridad: Permitir si es Admin logueado O si la ficha es visible O si es el comercio propietario
        $esAdmin = !empty($_SESSION['admin_id']);
        if ($esAdmin) {
            $admin = \App\Core\Database::getConnection()->prepare('SELECT id_administrador FROM administradores WHERE id_administrador = ? AND activo = 1');
            $admin->execute([(int)$_SESSION['admin_id']]);
            $esAdmin = (bool)$admin->fetchColumn();
        }
        $esVisible = PublicacionService::esFichaVisible($idLugar);
        $esPropietario = (int)($_SESSION['negocio_lugar_id'] ?? 0) === $idLugar
            && \App\Middleware\NegocioAuthMiddleware::esCuentaActualValida();

        if (!$esAdmin && !$esVisible && !$esPropietario) {
            http_response_code(403);
            exit();
        }

        $rutaFisica = ImagenService::getRutaArchivo($nombreArchivo);
        if ($rutaFisica === null || !is_file($rutaFisica)) {
            http_response_code(404);
            exit();
        }

        if (empty($mimeType)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $rutaFisica) ?: 'image/jpeg';
            finfo_close($finfo);
        }

        $mtime = filemtime($rutaFisica);
        $etag = '"' . md5($nombreArchivo . $mtime) . '"';
        $lastModified = gmdate('D, d M Y H:i:s ', $mtime) . 'GMT';

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($rutaFisica));
        header('Cache-Control: public, max-age=2592000, stale-while-revalidate=86400');
        header('ETag: ' . $etag);
        header('Last-Modified: ' . $lastModified);
        header('X-Content-Type-Options: nosniff');

        // Soporte de caché del navegador: HTTP 304 Not Modified
        $ifNoneMatch = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : null;
        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;

        if (($ifNoneMatch !== null && $ifNoneMatch === $etag) ||
            ($ifModifiedSince !== false && $ifModifiedSince >= $mtime)) {
            http_response_code(304);
            exit();
        }

        readfile($rutaFisica);
        exit();
    }
}
