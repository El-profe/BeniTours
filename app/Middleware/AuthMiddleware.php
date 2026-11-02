<?php
namespace App\Middleware;

/**
 * Restringe el acceso a las rutas administrativas únicamente a sesiones autorizadas.
 */
class AuthMiddleware {

    public static function autenticar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['admin_id']) || !self::esAdminActivo((int)$_SESSION['admin_id'])) {
            unset($_SESSION['admin_id'], $_SESSION['admin_nombre'], $_SESSION['admin_email'], $_SESSION['admin_rol']);
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            $baseUrl = rtrim($config['base_url'], '/');
            header("Location: {$baseUrl}/admin/login");
            exit();
        }
    }

    /**
     * Comprueba en base de datos que el administrador siga existiendo y su cuenta esté activa.
     */
    public static function esAdminActivo(int $adminId): bool {
        try {
            $stmt = \App\Core\Database::getConnection()->prepare(
                'SELECT id_administrador FROM administradores WHERE id_administrador = ? AND activo = 1 LIMIT 1'
            );
            $stmt->execute([$adminId]);
            return (bool)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            error_log('Error al verificar sesión de administrador: ' . $e->getMessage());
            return false;
        }
    }
}