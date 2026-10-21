<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Publicacion {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function crear(int $idLugar, int $aprobado = 0, int $habilitado = 1, ?int $idAdmin = null): bool {
        $sql = "INSERT INTO publicaciones (id_lugar, aprobado, habilitado, id_administrador_aprobacion, fecha_aprobacion) 
                VALUES (:id_lugar, :aprobado, :habilitado, :id_admin, :fecha_aprobacion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_lugar'          => $idLugar,
            ':aprobado'          => $aprobado,
            ':habilitado'        => $habilitado,
            ':id_admin'          => $idAdmin,
            ':fecha_aprobacion'  => $aprobado ? date('Y-m-d H:i:s') : null
        ]);
    }

    public function cambiarAprobacion(int $idLugar, int $aprobado, int $idAdmin): bool {
        $sql = "UPDATE publicaciones 
                SET aprobado = :aprobado, 
                    id_administrador_aprobacion = :id_admin,
                    fecha_aprobacion = :fecha 
                WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':aprobado'  => $aprobado,
            ':id_admin'  => $idAdmin,
            ':fecha'     => $aprobado ? date('Y-m-d H:i:s') : null,
            ':id_lugar'  => $idLugar
        ]);
    }

    public function cambiarHabilitacion(int $idLugar, int $habilitado, ?string $motivo = null): bool {
        $sql = "UPDATE publicaciones 
                SET habilitado = :habilitado, 
                    motivo_suspension = :motivo,
                    baja_voluntaria = CASE WHEN :habilitado_flag = 1 THEN 0 ELSE baja_voluntaria END
                WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':habilitado'      => $habilitado,
            ':motivo'          => $motivo,
            ':habilitado_flag' => $habilitado,
            ':id_lugar'        => $idLugar
        ]);
    }

    public function registrarBajaVoluntaria(int $idLugar, string $motivo, ?string $comentario = null): bool {
        $sql = "UPDATE publicaciones 
                SET habilitado = 0,
                    motivo_suspension = :motivo_susp,
                    baja_voluntaria = 1,
                    baja_fecha = NOW(),
                    baja_motivo = :motivo,
                    baja_comentario = :comentario
                WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':motivo_susp' => 'Baja voluntaria solicitada por el titular: ' . $motivo,
            ':motivo'      => mb_substr($motivo, 0, 100),
            ':comentario'  => !empty($comentario) ? mb_substr(trim($comentario), 0, 1000) : null,
            ':id_lugar'    => $idLugar
        ]);
    }
}