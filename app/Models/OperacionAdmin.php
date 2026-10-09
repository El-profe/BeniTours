<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/** Bitácora y seguimiento comercial del panel administrativo. */
class OperacionAdmin {
    private PDO $db;

    public function __construct() { $this->db = Database::getConnection(); }

    public function registrar(int $idLugar, int $idAdmin, string $tipo, string $descripcion, ?string $referenciaTipo = null, ?int $referenciaId = null): void {
        $stmt = $this->db->prepare('INSERT INTO bitacora_admin (id_lugar, id_administrador, tipo_evento, descripcion, referencia_tipo, referencia_id) VALUES (:lugar, :admin, :tipo, :descripcion, :ref_tipo, :ref_id)');
        $stmt->execute([':lugar' => $idLugar ?: null, ':admin' => $idAdmin ?: null, ':tipo' => mb_substr($tipo, 0, 60), ':descripcion' => mb_substr($descripcion, 0, 500), ':ref_tipo' => $referenciaTipo, ':ref_id' => $referenciaId]);
    }

    public function registrarSeguimiento(int $idLugar, int $idAdmin, string $canal, string $resultado, string $nota = '', ?string $proximo = null): void {
        $canal = in_array($canal, ['WHATSAPP', 'LLAMADA', 'OTRO'], true) ? $canal : 'WHATSAPP';
        $resultado = in_array($resultado, ['ENVIADO', 'RESPONDIO', 'SIN_RESPUESTA', 'PROMETIO_PAGO', 'NO_INTERESADO'], true) ? $resultado : 'ENVIADO';
        $stmt = $this->db->prepare('INSERT INTO seguimientos_cobro (id_lugar, id_administrador, canal, resultado, nota, proximo_seguimiento) VALUES (:lugar, :admin, :canal, :resultado, :nota, :proximo)');
        $stmt->execute([':lugar' => $idLugar, ':admin' => $idAdmin ?: null, ':canal' => $canal, ':resultado' => $resultado, ':nota' => $nota !== '' ? mb_substr($nota, 0, 1000) : null, ':proximo' => $proximo ?: null]);
        $this->registrar($idLugar, $idAdmin, 'SEGUIMIENTO_' . $canal, 'Seguimiento: ' . str_replace('_', ' ', strtolower($resultado)) . ($nota ? '. ' . $nota : ''), 'seguimiento', (int)$this->db->lastInsertId());
    }

    public function historial(int $idLugar, int $limite = 30): array {
        $sql = 'SELECT b.*, a.nombre AS administrador FROM bitacora_admin b LEFT JOIN administradores a ON a.id_administrador = b.id_administrador WHERE b.id_lugar = :id ORDER BY b.created_at DESC, b.id_bitacora DESC LIMIT ' . max(1, min(100, $limite));
        $stmt = $this->db->prepare($sql); $stmt->execute([':id' => $idLugar]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function seguimientos(int $idLugar): array {
        $stmt = $this->db->prepare('SELECT s.*, a.nombre AS administrador FROM seguimientos_cobro s LEFT JOIN administradores a ON a.id_administrador = s.id_administrador WHERE s.id_lugar = :id ORDER BY s.created_at DESC LIMIT 20');
        $stmt->execute([':id' => $idLugar]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
