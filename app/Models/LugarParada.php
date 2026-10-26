<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class LugarParada {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todas las paradas de un sindicato / lugar específico
     */
    public function listarPorLugar(int $idLugar, bool $soloActivas = true): array {
        $sql = "SELECT * FROM lugares_paradas WHERE id_lugar = :id_lugar";
        if ($soloActivas) {
            $sql .= " AND activo = 1";
        }
        $sql .= " ORDER BY id_parada ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_lugar' => $idLugar]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todas las paradas activas con datos del lugar/sindicato para el mapa general
     */
    public function listarTodasActivas(): array {
        $sql = "SELECT p.*, l.nombre as sindicato_nombre, l.slug as sindicato_slug,
                       l.telefono_contacto as sindicato_telefono, l.whatsapp_contacto as sindicato_whatsapp
                FROM lugares_paradas p
                INNER JOIN lugares l ON l.id_lugar = p.id_lugar
                WHERE p.activo = 1
                ORDER BY l.nombre ASC, p.id_parada ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una nueva parada para un sindicato
     */
    public function crear(array $datos): int {
        $sql = "INSERT INTO lugares_paradas (
                    id_lugar, nombre_parada, tipo_parada, direccion, referencia,
                    latitud, longitud, telefono_directo, whatsapp_directo, activo
                ) VALUES (
                    :id_lugar, :nombre_parada, :tipo_parada, :direccion, :referencia,
                    :latitud, :longitud, :telefono_directo, :whatsapp_directo, 1
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_lugar'          => (int)$datos['id_lugar'],
            ':nombre_parada'     => trim($datos['nombre_parada']),
            ':tipo_parada'       => $datos['tipo_parada'] ?? 'PARADA_URBANA',
            ':direccion'         => !empty($datos['direccion']) ? trim($datos['direccion']) : null,
            ':referencia'        => !empty($datos['referencia']) ? trim($datos['referencia']) : null,
            ':latitud'           => !empty($datos['latitud']) ? (float)$datos['latitud'] : null,
            ':longitud'          => !empty($datos['longitud']) ? (float)$datos['longitud'] : null,
            ':telefono_directo'  => !empty($datos['telefono_directo']) ? trim($datos['telefono_directo']) : null,
            ':whatsapp_directo'  => !empty($datos['whatsapp_directo']) ? trim($datos['whatsapp_directo']) : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Elimina una parada
     */
    public function eliminar(int $idParada, ?int $idLugar = null): bool {
        if ($idLugar !== null) {
            $stmt = $this->db->prepare("DELETE FROM lugares_paradas WHERE id_parada = :id_parada AND id_lugar = :id_lugar");
            return $stmt->execute([':id_parada' => $idParada, ':id_lugar' => $idLugar]);
        }
        $stmt = $this->db->prepare("DELETE FROM lugares_paradas WHERE id_parada = :id_parada");
        return $stmt->execute([':id_parada' => $idParada]);
    }
}
