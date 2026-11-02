<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AgendaActividad {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Utilidad de migración/instalación: asegura que la tabla de la agenda exista (usar en despliegue/instalador).
     */
    public static function asegurarTabla(): void {
        $db = Database::getConnection();
        $sql = "CREATE TABLE IF NOT EXISTS agenda_actividades (
            id_actividad INT AUTO_INCREMENT PRIMARY KEY,
            fecha_corta VARCHAR(50) NOT NULL,
            dia_orden INT NOT NULL DEFAULT 1,
            hora VARCHAR(50) NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            organiza VARCHAR(255) NOT NULL,
            lugar VARCHAR(255) NOT NULL,
            tipo VARCHAR(100) NOT NULL,
            categoria VARCHAR(50) NOT NULL DEFAULT 'academico',
            icono VARCHAR(50) NOT NULL DEFAULT 'bi-calendar-event',
            badge VARCHAR(100) NOT NULL DEFAULT 'bg-teal',
            activo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_categoria (categoria),
            INDEX idx_activo (activo),
            INDEX idx_dia_orden (dia_orden)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }

    /**
     * Lista todas las actividades activas para la vista pública (calendario).
     */
    public function listarTodasPublicas(): array {
        $stmt = $this->db->query("SELECT * FROM agenda_actividades WHERE activo = 1 ORDER BY dia_orden ASC, hora ASC, id_actividad ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lista todas las actividades para el panel de administración.
     */
    public function listarAdmin(): array {
        $stmt = $this->db->query("SELECT * FROM agenda_actividades ORDER BY dia_orden ASC, hora ASC, id_actividad ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function contar(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM agenda_actividades");
        return (int)$stmt->fetchColumn();
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM agenda_actividades WHERE id_actividad = ? LIMIT 1");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function crear(array $datos): int {
        $diaOrden = $this->calcularDiaOrden($datos['fecha_corta'] ?? '');
        $stmt = $this->db->prepare("INSERT INTO agenda_actividades 
            (fecha_corta, dia_orden, hora, titulo, organiza, lugar, tipo, categoria, icono, badge, activo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            trim($datos['fecha_corta'] ?? '03/11'),
            $diaOrden,
            trim($datos['hora'] ?? '08:00'),
            trim($datos['titulo'] ?? ''),
            trim($datos['organiza'] ?? 'BeniTours'),
            trim($datos['lugar'] ?? 'Trinidad · Beni'),
            trim($datos['tipo'] ?? 'ACTIVIDAD'),
            trim($datos['categoria'] ?? 'cultura'),
            trim($datos['icono'] ?? $this->obtenerIconoPorCategoria($datos['categoria'] ?? '')),
            trim($datos['badge'] ?? $this->obtenerBadgePorCategoria($datos['categoria'] ?? '')),
            isset($datos['activo']) ? (int)$datos['activo'] : 1
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool {
        $diaOrden = $this->calcularDiaOrden($datos['fecha_corta'] ?? '');
        $stmt = $this->db->prepare("UPDATE agenda_actividades SET 
            fecha_corta = ?,
            dia_orden = ?,
            hora = ?,
            titulo = ?,
            organiza = ?,
            lugar = ?,
            tipo = ?,
            categoria = ?,
            icono = ?,
            badge = ?,
            activo = ?
            WHERE id_actividad = ?");

        return $stmt->execute([
            trim($datos['fecha_corta'] ?? '03/11'),
            $diaOrden,
            trim($datos['hora'] ?? '08:00'),
            trim($datos['titulo'] ?? ''),
            trim($datos['organiza'] ?? 'BeniTours'),
            trim($datos['lugar'] ?? 'Trinidad · Beni'),
            trim($datos['tipo'] ?? 'ACTIVIDAD'),
            trim($datos['categoria'] ?? 'cultura'),
            trim($datos['icono'] ?? $this->obtenerIconoPorCategoria($datos['categoria'] ?? '')),
            trim($datos['badge'] ?? $this->obtenerBadgePorCategoria($datos['categoria'] ?? '')),
            isset($datos['activo']) ? (int)$datos['activo'] : 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM agenda_actividades WHERE id_actividad = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Extrae el primer número de día de la cadena "03/11", "10/11", "03/11 al 06/11" para ordenación
     */
    private function calcularDiaOrden(string $fechaCorta): int {
        if (preg_match('/(\d{1,2})/', $fechaCorta, $matches)) {
            return (int)$matches[1];
        }
        return 1;
    }

    public function obtenerIconoPorCategoria(string $categoria): string {
        return match($categoria) {
            'academico'  => 'bi-mortarboard-fill',
            'cultura'    => 'bi-palette-fill',
            'ferias'     => 'bi-shop',
            'festivales' => 'bi-fire',
            'folklore'   => 'bi-music-note-beamed',
            'remates'    => 'bi-hammer',
            'civicos'    => 'bi-flag-fill',
            'fexpobeni'  => 'bi-award-fill',
            default      => 'bi-calendar-event-fill'
        };
    }

    public function obtenerBadgePorCategoria(string $categoria): string {
        return match($categoria) {
            'academico'  => 'bg-teal',
            'cultura'    => 'bg-purple text-white',
            'ferias'     => 'bg-info text-dark',
            'festivales' => 'bg-danger text-white',
            'folklore'   => 'bg-success text-white',
            'remates'    => 'bg-secondary text-white',
            'civicos'    => 'bg-warning text-dark',
            'fexpobeni'  => 'bg-amber text-dark',
            default      => 'bg-teal'
        };
    }
}
