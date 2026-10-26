<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class MapaProvinciaFoto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todas las provincias del mapa indexadas por su código
     * @return array<string, array>
     */
    public function listarTodasIndexadas(): array {
        $sql = "SELECT * FROM mapa_provincias_fotos ORDER BY FIELD(codigo, 'vacadiez', 'ballivian', 'yacuma', 'mamore', 'itenez', 'cercado', 'moxos', 'marban')";
        $stmt = $this->db->query($sql);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($filas as $fila) {
            $resultado[$fila['codigo']] = $fila;
        }
        return $resultado;
    }

    /**
     * Retorna la lista estándar ordenada
     */
    public function listarTodas(): array {
        $sql = "SELECT * FROM mapa_provincias_fotos ORDER BY FIELD(codigo, 'vacadiez', 'ballivian', 'yacuma', 'mamore', 'itenez', 'cercado', 'moxos', 'marban')";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una provincia por su código
     */
    public function obtenerPorCodigo(string $codigo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM mapa_provincias_fotos WHERE codigo = :codigo LIMIT 1");
        $stmt->execute([':codigo' => $codigo]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Actualiza la fotografía y título de una provincia
     */
    public function actualizarFoto(string $codigo, string $nombreArchivo, ?string $titulo = null): bool {
        $sql = "UPDATE mapa_provincias_fotos 
                SET imagen = :imagen, 
                    titulo = COALESCE(:titulo, titulo),
                    updated_at = NOW() 
                WHERE codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo' => $codigo,
            ':imagen' => $nombreArchivo,
            ':titulo' => $titulo ? trim($titulo) : null
        ]);
    }
}
