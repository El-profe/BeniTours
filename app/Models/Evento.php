<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Evento {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista los eventos vigentes y activos para la portada pública.
     */
    public function listarVigentesPublicos(): array {
        $sql = "SELECT id_evento, titulo, subtitulo_lugar, detalle_fechas, imagen_archivo, enlace_externo, fecha_inicio, fecha_fin, orden
                FROM eventos_agenda
                WHERE activo = 1 
                  AND CURDATE() BETWEEN fecha_inicio AND fecha_fin
                ORDER BY orden ASC, fecha_fin ASC, id_evento DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lista todos los eventos para el panel administrativo.
     */
    public function listarTodos(): array {
        $sql = "SELECT id_evento, titulo, subtitulo_lugar, detalle_fechas, imagen_archivo, enlace_externo, 
                       fecha_inicio, fecha_fin, activo, orden, created_at,
                       CASE 
                           WHEN activo = 0 THEN 'PAUSADO'
                           WHEN CURDATE() > fecha_fin THEN 'EXPIRADO'
                           WHEN CURDATE() < fecha_inicio THEN 'PROGRAMADO'
                           ELSE 'VIGENTE'
                       END AS estado_vigencia
                FROM eventos_agenda
                ORDER BY orden ASC, fecha_fin DESC, id_evento DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM eventos_agenda WHERE id_evento = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function crear(array $datos): int {
        $sql = "INSERT INTO eventos_agenda 
                (titulo, subtitulo_lugar, detalle_fechas, imagen_archivo, enlace_externo, fecha_inicio, fecha_fin, activo, orden)
                VALUES 
                (:titulo, :subtitulo_lugar, :detalle_fechas, :imagen_archivo, :enlace_externo, :fecha_inicio, :fecha_fin, :activo, :orden)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo'          => $datos['titulo'],
            ':subtitulo_lugar' => $datos['subtitulo_lugar'] ?? 'Trinidad · Beni',
            ':detalle_fechas'  => $datos['detalle_fechas'],
            ':imagen_archivo'  => $datos['imagen_archivo'],
            ':enlace_externo'  => !empty($datos['enlace_externo']) ? $datos['enlace_externo'] : null,
            ':fecha_inicio'    => $datos['fecha_inicio'],
            ':fecha_fin'       => $datos['fecha_fin'],
            ':activo'          => isset($datos['activo']) ? (int)$datos['activo'] : 1,
            ':orden'           => isset($datos['orden']) ? (int)$datos['orden'] : 1
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool {
        $campos = [
            'titulo = :titulo',
            'subtitulo_lugar = :subtitulo_lugar',
            'detalle_fechas = :detalle_fechas',
            'enlace_externo = :enlace_externo',
            'fecha_inicio = :fecha_inicio',
            'fecha_fin = :fecha_fin',
            'activo = :activo',
            'orden = :orden'
        ];

        $params = [
            ':id'              => $id,
            ':titulo'          => $datos['titulo'],
            ':subtitulo_lugar' => $datos['subtitulo_lugar'] ?? 'Trinidad · Beni',
            ':detalle_fechas'  => $datos['detalle_fechas'],
            ':enlace_externo'  => !empty($datos['enlace_externo']) ? $datos['enlace_externo'] : null,
            ':fecha_inicio'    => $datos['fecha_inicio'],
            ':fecha_fin'       => $datos['fecha_fin'],
            ':activo'          => isset($datos['activo']) ? (int)$datos['activo'] : 1,
            ':orden'           => isset($datos['orden']) ? (int)$datos['orden'] : 1
        ];

        if (!empty($datos['imagen_archivo'])) {
            $campos[] = 'imagen_archivo = :imagen_archivo';
            $params[':imagen_archivo'] = $datos['imagen_archivo'];
        }

        $sql = "UPDATE eventos_agenda SET " . implode(', ', $campos) . " WHERE id_evento = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function cambiarEstado(int $id, int $activo): bool {
        $stmt = $this->db->prepare("UPDATE eventos_agenda SET activo = :activo WHERE id_evento = :id");
        return $stmt->execute([
            ':activo' => $activo ? 1 : 0,
            ':id'     => $id
        ]);
    }

    public function eliminar(int $id): bool {
        $evento = $this->buscarPorId($id);
        if ($evento) {
            $archivo = $evento['imagen_archivo'];
            // Eliminar archivo físico si está en public/assets/img/eventos/
            $rutaSubida = dirname(__DIR__, 2) . '/public/assets/img/eventos/' . $archivo;
            if (is_file($rutaSubida)) {
                @unlink($rutaSubida);
            }
            // Si está en public/assets/img/
            $rutaRaiz = dirname(__DIR__, 2) . '/public/assets/img/' . $archivo;
            if (is_file($rutaRaiz)) {
                @unlink($rutaRaiz);
            }
        }
        $stmt = $this->db->prepare("DELETE FROM eventos_agenda WHERE id_evento = :id");
        return $stmt->execute([':id' => $id]);
    }
}
