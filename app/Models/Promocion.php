<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Promocion {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarPorLugar(int $idLugar): array {
        $sql = "SELECT * FROM promociones WHERE id_lugar = :id ORDER BY id_promocion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idLugar]);
        return $stmt->fetchAll();
    }

    public function listarVigentesPublicas(int $idLugar): array {
        $sql = "SELECT p.*, 
                       COALESCE(p.imagen_archivo, (
                           SELECT f.nombre_archivo FROM fotografias f
                           WHERE f.id_lugar = p.id_lugar AND f.es_principal = 1
                           LIMIT 1
                       )) AS imagen
                FROM promociones p
                WHERE p.id_lugar = :id AND p.activo = 1 
                  AND CURDATE() BETWEEN fecha_inicio AND fecha_fin
                ORDER BY p.id_promocion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idLugar]);
        return $stmt->fetchAll();
    }

    /**
     * Lista todas las promociones vigentes públicas de lugares aprobados y activos.
     */
    public function listarTodasVigentesPublicas(?int $limite = null): array {
        $condicionVisibilidad = \App\Services\PublicacionService::getSqlCondicionVisibilidad('l', 'pub');
        $sql = "SELECT p.*, 
                       l.nombre AS lugar_nombre, 
                       l.slug AS lugar_slug, 
                       l.direccion AS lugar_direccion,
                       l.referencia_ubicacion AS lugar_referencia,
                       l.latitud,
                       l.longitud,
                       l.telefono_contacto, 
                       l.whatsapp_contacto,
                       c.nombre AS categoria_nombre, 
                       c.icono AS categoria_icono,
                        m.nombre AS municipio_nombre,
                        COALESCE(p.imagen_archivo, (SELECT f.nombre_archivo FROM fotografias f WHERE f.id_lugar = l.id_lugar AND f.es_principal = 1 LIMIT 1)) AS imagen
                FROM promociones p
                INNER JOIN lugares l ON p.id_lugar = l.id_lugar
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN municipios m ON l.id_municipio = m.id_municipio
                INNER JOIN publicaciones pub ON l.id_lugar = pub.id_lugar
                WHERE p.activo = 1 
                  AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin
                  AND {$condicionVisibilidad}
                ORDER BY p.id_promocion DESC";

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite;
        }

        return $this->db->query($sql)->fetchAll();
    }

    public function crear(array $datos): int {
        $sql = "INSERT INTO promociones (id_lugar, titulo, descripcion, descuento_texto, imagen_archivo, fecha_inicio, fecha_fin, activo)
                VALUES (:id_lugar, :titulo, :descripcion, :descuento, :imagen_archivo, :inicio, :fin, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_lugar'        => $datos['id_lugar'],
            ':titulo'          => $datos['titulo'],
            ':descripcion'     => $datos['descripcion'],
            ':descuento'       => $datos['descuento_texto'] ?? null,
            ':imagen_archivo'  => $datos['imagen_archivo'] ?? null,
            ':inicio'          => $datos['fecha_inicio'],
            ':fin'             => $datos['fecha_fin']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function buscarPorId(int $idPromocion, int $idLugar): ?array {
        $sql = "SELECT * FROM promociones WHERE id_promocion = :id AND id_lugar = :id_lugar LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idPromocion, ':id_lugar' => $idLugar]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function actualizar(int $idPromocion, int $idLugar, array $datos): bool {
        if (array_key_exists('imagen_archivo', $datos)) {
            $stmtImg = $this->db->prepare("SELECT imagen_archivo FROM promociones WHERE id_promocion = :id AND id_lugar = :id_lugar");
            $stmtImg->execute([':id' => $idPromocion, ':id_lugar' => $idLugar]);
            $imgVieja = $stmtImg->fetchColumn();

            if ($imgVieja && $imgVieja !== $datos['imagen_archivo']) {
                try {
                    \App\Services\ImagenService::eliminarArchivo($imgVieja);
                } catch (\Throwable $e) {}
            }

            $sql = "UPDATE promociones SET 
                        titulo = :titulo, 
                        descripcion = :descripcion, 
                        descuento_texto = :descuento, 
                        imagen_archivo = :imagen, 
                        fecha_inicio = :inicio, 
                        fecha_fin = :fin
                    WHERE id_promocion = :id AND id_lugar = :id_lugar";
            $params = [
                ':titulo'      => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':descuento'   => $datos['descuento_texto'] ?? null,
                ':imagen'      => $datos['imagen_archivo'] ?? null,
                ':inicio'      => $datos['fecha_inicio'],
                ':fin'         => $datos['fecha_fin'],
                ':id'          => $idPromocion,
                ':id_lugar'    => $idLugar
            ];
        } else {
            $sql = "UPDATE promociones SET 
                        titulo = :titulo, 
                        descripcion = :descripcion, 
                        descuento_texto = :descuento, 
                        fecha_inicio = :inicio, 
                        fecha_fin = :fin
                    WHERE id_promocion = :id AND id_lugar = :id_lugar";
            $params = [
                ':titulo'      => $datos['titulo'],
                ':descripcion' => $datos['descripcion'],
                ':descuento'   => $datos['descuento_texto'] ?? null,
                ':inicio'      => $datos['fecha_inicio'],
                ':fin'         => $datos['fecha_fin'],
                ':id'          => $idPromocion,
                ':id_lugar'    => $idLugar
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function cambiarEstado(int $idPromocion, int $idLugar, int $activo): bool {
        $sql = "UPDATE promociones SET activo = :activo WHERE id_promocion = :id AND id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo'   => $activo,
            ':id'       => $idPromocion,
            ':id_lugar' => $idLugar
        ]);
    }

    public function eliminar(int $idPromocion, int $idLugar): bool {
        $stmtImg = $this->db->prepare("SELECT imagen_archivo FROM promociones WHERE id_promocion = :id AND id_lugar = :id_lugar");
        $stmtImg->execute([':id' => $idPromocion, ':id_lugar' => $idLugar]);
        $img = $stmtImg->fetchColumn();
        if ($img) {
            try {
                \App\Services\ImagenService::eliminarArchivo($img);
            } catch (\Throwable $e) {
                // Silencioso si el archivo físico ya no existía
            }
        }

        $sql = "DELETE FROM promociones WHERE id_promocion = :id AND id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idPromocion, ':id_lugar' => $idLugar]);
    }
}
