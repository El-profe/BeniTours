<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class GaleriaFoto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todas las fotos para el panel de administración
     */
    public function listarTodas(): array {
        $sql = "SELECT g.*, l.nombre as lugar_nombre, l.slug as lugar_slug
                FROM galeria_fotos g
                LEFT JOIN lugares l ON l.id_lugar = g.id_lugar
                ORDER BY g.es_destacada DESC, g.orden ASC, g.id_galeria DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista únicamente las fotos activas para la portada pública
     */
    public function listarActivas(int $limite = 8): array {
        $sql = "SELECT g.*, l.nombre as lugar_nombre, l.slug as lugar_slug, c.nombre as categoria_nombre
                FROM galeria_fotos g
                LEFT JOIN lugares l ON l.id_lugar = g.id_lugar
                LEFT JOIN categorias c ON c.id_categoria = l.id_categoria
                WHERE g.activo = 1
                ORDER BY g.es_destacada DESC, g.orden ASC, g.id_galeria DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta una nueva foto de galería
     */
    public function crear(array $datos): int {
        if (!empty($datos['es_destacada'])) {
            $this->db->exec("UPDATE galeria_fotos SET es_destacada = 0");
        }

        $sql = "INSERT INTO galeria_fotos (titulo, subtitulo, creditos_autor, url_fuente, nombre_archivo, id_lugar, es_destacada, orden, activo)
                VALUES (:titulo, :subtitulo, :creditos_autor, :url_fuente, :nombre_archivo, :id_lugar, :es_destacada, :orden, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo'         => trim($datos['titulo']),
            ':subtitulo'      => !empty($datos['subtitulo']) ? trim($datos['subtitulo']) : null,
            ':creditos_autor' => !empty($datos['creditos_autor']) ? trim($datos['creditos_autor']) : null,
            ':url_fuente'     => !empty($datos['url_fuente']) ? trim($datos['url_fuente']) : null,
            ':nombre_archivo' => $datos['nombre_archivo'],
            ':id_lugar'       => !empty($datos['id_lugar']) ? (int)$datos['id_lugar'] : null,
            ':es_destacada'   => !empty($datos['es_destacada']) ? 1 : 0,
            ':orden'          => !empty($datos['orden']) ? (int)$datos['orden'] : 1
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Alterna estado activo/inactivo
     */
    public function cambiarEstado(int $id, int $activo): bool {
        $stmt = $this->db->prepare("UPDATE galeria_fotos SET activo = :activo WHERE id_galeria = :id");
        return $stmt->execute([':activo' => $activo, ':id' => $id]);
    }

    /**
     * Establece una foto como la destacada principal
     */
    public function establecerDestacada(int $id): bool {
        $this->db->exec("UPDATE galeria_fotos SET es_destacada = 0");
        $stmt = $this->db->prepare("UPDATE galeria_fotos SET es_destacada = 1, activo = 1 WHERE id_galeria = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Elimina una foto de la galería
     */
    public function eliminar(int $id): ?string {
        $stmt = $this->db->prepare("SELECT nombre_archivo FROM galeria_fotos WHERE id_galeria = :id");
        $stmt->execute([':id' => $id]);
        $foto = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$foto) return null;

        $stmtDel = $this->db->prepare("DELETE FROM galeria_fotos WHERE id_galeria = :id");
        $stmtDel->execute([':id' => $id]);

        return $foto['nombre_archivo'];
    }
}
