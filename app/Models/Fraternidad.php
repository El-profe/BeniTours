<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Fraternidad {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todas las fraternidades activas ordenadas por rol de ingreso
     */
    public function listarTodas(): array {
        $stmt = $this->db->query("SELECT * FROM entrada_fraternidades WHERE activo = 1 ORDER BY orden ASC, id_fraternidad ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista para el panel administrativo (activas e inactivas)
     */
    public function listarAdmin(): array {
        $stmt = $this->db->query("SELECT * FROM entrada_fraternidades ORDER BY orden ASC, id_fraternidad ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM entrada_fraternidades WHERE id_fraternidad = ? LIMIT 1");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("INSERT INTO entrada_fraternidades (orden, hora_ingreso, nombre_fraternidad, danza, universidad, departamento, activo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            (int)($datos['orden'] ?? 1),
            trim($datos['hora_ingreso'] ?? '14:00'),
            trim($datos['nombre_fraternidad'] ?? ''),
            trim($datos['danza'] ?? ''),
            trim($datos['universidad'] ?? ''),
            trim($datos['departamento'] ?? 'Beni'),
            isset($datos['activo']) ? (int)$datos['activo'] : 1
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool {
        $stmt = $this->db->prepare("UPDATE entrada_fraternidades SET orden = ?, hora_ingreso = ?, nombre_fraternidad = ?, danza = ?, universidad = ?, departamento = ?, activo = ? WHERE id_fraternidad = ?");
        return $stmt->execute([
            (int)($datos['orden'] ?? 1),
            trim($datos['hora_ingreso'] ?? '14:00'),
            trim($datos['nombre_fraternidad'] ?? ''),
            trim($datos['danza'] ?? ''),
            trim($datos['universidad'] ?? ''),
            trim($datos['departamento'] ?? 'Beni'),
            isset($datos['activo']) ? (int)$datos['activo'] : 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM entrada_fraternidades WHERE id_fraternidad = ?");
        return $stmt->execute([$id]);
    }
}
