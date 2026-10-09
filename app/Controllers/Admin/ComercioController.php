<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Models\Lugar;
use App\Models\OperacionAdmin;
use App\Services\TelemetriaService;
use PDO;

class ComercioController extends Controller {
    private PDO $db;

    public function __construct() {
        parent::__construct();
        AuthMiddleware::autenticar();
        $this->db = Database::getConnection();
    }

    public function detalle(): void {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $lugar = (new Lugar())->buscarPorId($id);
        if (!$lugar || $lugar['tipo_lugar'] !== 'COMERCIAL') {
            $_SESSION['admin_error'] = 'Comercio no encontrado.';
            header("Location: {$this->config['base_url']}/admin/lugares"); exit();
        }
        $stmt = $this->db->prepare("SELECT p.*, v.fecha_inicio, v.fecha_vencimiento FROM pagos p LEFT JOIN vigencias v ON v.id_pago = p.id_pago WHERE p.id_lugar = :id ORDER BY p.id_pago DESC LIMIT 15");
        $stmt->execute([':id' => $id]);
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $promos = $this->db->prepare("SELECT * FROM promociones WHERE id_lugar = :id ORDER BY activo DESC, fecha_fin DESC LIMIT 10");
        $promos->execute([':id' => $id]);
        $fotos = $this->db->prepare('SELECT COUNT(*) FROM fotografias WHERE id_lugar = :id'); $fotos->execute([':id' => $id]);
        $fotosTotal = (int)$fotos->fetchColumn();
        $promosActivas = 0; foreach ($promos->fetchAll(PDO::FETCH_ASSOC) as $promo) { if ((int)$promo['activo'] === 1 && $promo['fecha_fin'] >= date('Y-m-d')) $promosActivas++; }
        $campos = ['descripcion' => 'descripción', 'direccion' => 'dirección', 'horario_atencion' => 'horario', 'whatsapp_contacto' => 'WhatsApp'];
        $faltantes = []; foreach ($campos as $campo => $etiqueta) { if (empty(trim((string)($lugar[$campo] ?? '')))) $faltantes[] = $etiqueta; }
        if ($fotosTotal === 0) $faltantes[] = 'foto de portada';
        if (empty($lugar['latitud']) || empty($lugar['longitud'])) $faltantes[] = 'ubicación en mapa';
        $totalRequisitos = count($campos) + 3;
        $completitud = (int)round((($totalRequisitos - count($faltantes)) / $totalRequisitos) * 100);
        $operacion = new OperacionAdmin();
        try { $historial = $operacion->historial($id); $seguimientos = $operacion->seguimientos($id); }
        catch (\Throwable $ignored) { $historial = []; $seguimientos = []; }
        $this->render('admin/comercios/detalle', [
            'titulo' => 'Ficha 360: ' . $lugar['nombre'], 'lugar' => $lugar, 'pagos' => $pagos,
            'promocionesActivas' => $promosActivas, 'fotosTotal' => $fotosTotal,
            'faltantes' => $faltantes, 'completitud' => $completitud,
            'metricas' => TelemetriaService::obtenerMetricasLugar($id),
            'historial' => $historial, 'seguimientos' => $seguimientos,
            'csrfToken' => CsrfMiddleware::obtenerToken()
        ], 'admin');
    }

    public function guardarSeguimiento(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CsrfMiddleware::validarToken($_POST['csrf_token'] ?? '')) { http_response_code(403); exit('Solicitud no permitida.'); }
        $id = filter_var($_POST['id_lugar'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $proximo = trim($_POST['proximo_seguimiento'] ?? '');
        if ($proximo !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $proximo)) $proximo = '';
        try {
            (new OperacionAdmin())->registrarSeguimiento($id, (int)$_SESSION['admin_id'], trim($_POST['canal'] ?? 'WHATSAPP'), trim($_POST['resultado'] ?? 'ENVIADO'), trim($_POST['nota'] ?? ''), $proximo ?: null);
            $_SESSION['admin_flash'] = 'Seguimiento registrado correctamente.';
        } catch (\Throwable $e) { $_SESSION['admin_error'] = 'No se pudo registrar el seguimiento. Verifique que la migración administrativa esté aplicada.'; }
        header("Location: {$this->config['base_url']}/admin/comercios/detalle?id={$id}"); exit();
    }
}
