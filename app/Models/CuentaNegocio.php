<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CuentaNegocio {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Busca una cuenta por su usuario, email, número de WhatsApp o teléfono de contacto.
     * Permite al comerciante iniciar sesión con su número celular directamente.
     */
    public function buscarPorCredencial(string $identificador): ?array {
        $idLimpio = trim($identificador);
        $numerosSolo = preg_replace('/\D/', '', $idLimpio);

        // Si ingresó dígitos (ej. 78201234 o 59178201234), buscar coincidencias telefónicas
        $condicionTelefono = '';
        $params = [
            ':id_user'  => $idLimpio,
            ':id_email' => $idLimpio
        ];

        if (!empty($numerosSolo) && strlen($numerosSolo) >= 7) {
            // Últimos 8 dígitos estándar de Bolivia
            $ochoDigitos = substr($numerosSolo, -8);
            $condicionTelefono = " OR RIGHT(REGEXP_REPLACE(l.whatsapp_contacto, '[^0-9]', ''), 8) = :tel_wa
                                   OR RIGHT(REGEXP_REPLACE(l.telefono_contacto, '[^0-9]', ''), 8) = :tel_fijo";
            $params[':tel_wa'] = $ochoDigitos;
            $params[':tel_fijo'] = $ochoDigitos;
        }

        $sql = "SELECT cn.*, l.nombre AS nombre_negocio, l.tipo_lugar, l.slug
                FROM cuentas_negocio cn
                INNER JOIN lugares l ON cn.id_lugar = l.id_lugar
                WHERE (cn.usuario = :id_user OR cn.email = :id_email {$condicionTelefono}) 
                  AND cn.activo = 1
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $cuenta = $stmt->fetch();
        return $cuenta ?: null;
    }

    public function buscarPorLugar(int $idLugar): ?array {
        $sql = "SELECT cn.*, l.nombre AS nombre_negocio, l.tipo_lugar, l.slug
                FROM cuentas_negocio cn
                INNER JOIN lugares l ON cn.id_lugar = l.id_lugar
                WHERE cn.id_lugar = :id_lugar
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_lugar' => $idLugar]);
        $cuenta = $stmt->fetch();
        return $cuenta ?: null;
    }

    public function actualizarUltimoAcceso(int $idCuenta): void {
        $sql = "UPDATE cuentas_negocio SET ultimo_acceso = NOW() WHERE id_cuenta = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCuenta]);
    }

    public function cambiarEstadoActivo(int $idLugar, int $activo): bool {
        $sql = "UPDATE cuentas_negocio SET activo = :activo WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo'   => $activo,
            ':id_lugar' => $idLugar
        ]);
    }

    public function crear(int $idLugar, string $usuario, string $email, string $passwordPlano): int {
        $sql = "INSERT INTO cuentas_negocio (id_lugar, usuario, email, password_hash)
                VALUES (:lugar, :usuario, :email, :hash)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':lugar'    => $idLugar,
            ':usuario'  => $usuario,
            ':email'    => $email,
            ':hash'     => password_hash($passwordPlano, PASSWORD_BCRYPT)
        ]);
        return (int)$this->db->lastInsertId();
    }
}
