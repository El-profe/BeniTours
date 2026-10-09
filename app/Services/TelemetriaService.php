<?php
namespace App\Services;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Servicio de Telemetría e Interacciones Turísticas - BeniTours
 * Registra de forma silenciosa y anónima las acciones de valor de los turistas
 * (visitas a fichas, clics en WhatsApp, rutas en Google Maps y favoritos guardados)
 * para calcular el retorno de inversión de cada comercio y nutrir los reportes.
 */
class TelemetriaService {
    public const EVENTO_VISTA = 'VISTA';
    public const EVENTO_WHATSAPP = 'WHATSAPP';
    public const EVENTO_MAPA = 'MAPA';
    public const EVENTO_TELEFONO = 'TELEFONO';
    public const EVENTO_MENU = 'MENU';
    public const EVENTO_PROMOCION = 'PROMOCION';
    public const EVENTO_FAVORITO = 'FAVORITO';

    public const EVENTOS_VALIDOS = [
        self::EVENTO_VISTA,
        self::EVENTO_WHATSAPP,
        self::EVENTO_MAPA,
        self::EVENTO_TELEFONO,
        self::EVENTO_MENU,
        self::EVENTO_PROMOCION,
        self::EVENTO_FAVORITO
    ];
    private const COOKIE_VISITANTE = 'beniturs_visitante';

    /**
     * Obtiene el token anónimo de visitante (creándolo si no existe)
     */
    private static function obtenerVisitanteHash(): string {
        $token = $_COOKIE[self::COOKIE_VISITANTE] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $token = bin2hex(random_bytes(32));
            if (!headers_sent()) {
                setcookie(self::COOKIE_VISITANTE, $token, [
                    'expires'  => time() + 31536000,
                    'path'     => '/',
                    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
        }
        return hash('sha256', $token);
    }

    /**
     * Registra un evento de interacción con protección anti-rebote (debounce de 3 minutos)
     */
    public static function registrarEvento(int $idLugar, string $tipoEvento): bool {
        $tipoEvento = strtoupper(trim($tipoEvento));
        if ($idLugar <= 0 || !in_array($tipoEvento, self::EVENTOS_VALIDOS, true)) {
            return false;
        }

        $visitanteHash = self::obtenerVisitanteHash();

        try {
            $db = Database::getConnection();

            // Protección anti-duplicados por ráfaga (últimos 3 minutos)
            $stmtCheck = $db->prepare(
                "SELECT 1 FROM interacciones_lugar 
                 WHERE id_lugar = :id_lugar 
                   AND tipo_evento = :evento 
                   AND visitante_hash = :hash 
                   AND created_at >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) 
                 LIMIT 1"
            );
            $stmtCheck->execute([
                ':id_lugar' => $idLugar,
                ':evento'   => $tipoEvento,
                ':hash'     => $visitanteHash
            ]);

            if ($stmtCheck->fetchColumn()) {
                return true; // Ya registrado recientemente en la misma sesión
            }

            $stmtInsert = $db->prepare(
                "INSERT INTO interacciones_lugar (id_lugar, tipo_evento, fecha, visitante_hash) 
                 VALUES (:id_lugar, :evento, CURDATE(), :hash)"
            );
            return $stmtInsert->execute([
                ':id_lugar' => $idLugar,
                ':evento'   => $tipoEvento,
                ':hash'     => $visitanteHash
            ]);
        } catch (PDOException $e) {
            error_log('Error en TelemetriaService::registrarEvento: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene métricas individuales para el panel del negocio
     */
    public static function obtenerMetricasLugar(int $idLugar): array {
        $db = Database::getConnection();

        // 1. Métricas del Mes Actual
        $sqlMes = "SELECT tipo_evento, COUNT(*) AS total 
                   FROM interacciones_lugar 
                   WHERE id_lugar = :id 
                     AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
                     AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
                   GROUP BY tipo_evento";
        $stmtMes = $db->prepare($sqlMes);
        $stmtMes->execute([':id' => $idLugar]);
        $eventosMes = $stmtMes->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 2. Métricas del Mes Anterior
        $sqlMesAnt = "SELECT tipo_evento, COUNT(*) AS total 
                      FROM interacciones_lugar 
                      WHERE id_lugar = :id 
                        AND fecha >= DATE_SUB(DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01'), INTERVAL 1 MONTH) 
                        AND fecha < DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01') 
                      GROUP BY tipo_evento";
        $stmtMesAnt = $db->prepare($sqlMesAnt);
        $stmtMesAnt->execute([':id' => $idLugar]);
        $eventosMesAnt = $stmtMesAnt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 3. Totales Históricos
        $sqlTotal = "SELECT tipo_evento, COUNT(*) AS total 
                     FROM interacciones_lugar 
                     WHERE id_lugar = :id 
                     GROUP BY tipo_evento";
        $stmtTotal = $db->prepare($sqlTotal);
        $stmtTotal->execute([':id' => $idLugar]);
        $eventosTotal = $stmtTotal->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $vistasMes = (int)($eventosMes['VISTA'] ?? 0);
        $vistasMesAnt = (int)($eventosMesAnt['VISTA'] ?? 0);
        $waMes = (int)($eventosMes['WHATSAPP'] ?? 0);
        $waMesAnt = (int)($eventosMesAnt['WHATSAPP'] ?? 0);
        $mapaMes = (int)($eventosMes['MAPA'] ?? 0);
        $favMes = (int)($eventosMes['FAVORITO'] ?? 0);
        $telMes = (int)($eventosMes['TELEFONO'] ?? 0);
        $menuMes = (int)($eventosMes['MENU'] ?? 0);
        $promoMes = (int)($eventosMes['PROMOCION'] ?? 0);

        // Clientes potenciales contactados (WhatsApp + Mapa + Teléfono)
        $clientesPotencialesMes = $waMes + $mapaMes + $telMes;

        // Variaciones porcentuales
        $varVistas = $vistasMesAnt > 0 ? round((($vistasMes - $vistasMesAnt) / $vistasMesAnt) * 100, 1) : null;
        $varWa = $waMesAnt > 0 ? round((($waMes - $waMesAnt) / $waMesAnt) * 100, 1) : null;

        return [
            'vistas_mes'               => $vistasMes,
            'vistas_mes_ant'           => $vistasMesAnt,
            'variacion_vistas'         => $varVistas,
            'whatsapp_mes'             => $waMes,
            'whatsapp_mes_ant'         => $waMesAnt,
            'variacion_whatsapp'       => $varWa,
            'mapa_mes'                 => $mapaMes,
            'favoritos_mes'            => $favMes,
            'telefono_mes'             => $telMes,
            'menu_mes'                 => $menuMes,
            'promocion_mes'            => $promoMes,
            'clientes_potenciales_mes' => $clientesPotencialesMes,
            'total_historico_vistas'   => (int)($eventosTotal['VISTA'] ?? 0),
            'total_historico_whatsapp' => (int)($eventosTotal['WHATSAPP'] ?? 0),
            'total_historico_mapa'     => (int)($eventosTotal['MAPA'] ?? 0),
            'total_historico_favoritos'=> (int)($eventosTotal['FAVORITO'] ?? 0),
        ];
    }

    /**
     * Obtiene el ranking de lugares con mayor atracción y conversiones para el Admin
     */
    public static function obtenerRankingComercial(?string $desde = null, ?string $hasta = null, int $limite = 20): array {
        $db = Database::getConnection();
        $joinCond = [];
        $params = [];

        if (!empty($desde)) {
            $joinCond[] = "i.fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if (!empty($hasta)) {
            $joinCond[] = "i.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }

        $extraJoin = !empty($joinCond) ? " AND " . implode(' AND ', $joinCond) : "";

        $sql = "SELECT l.id_lugar, l.nombre AS establecimiento, l.slug, c.nombre AS categoria,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'VISTA' THEN 1 ELSE 0 END), 0) AS vistas,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'WHATSAPP' THEN 1 ELSE 0 END), 0) AS contactos_whatsapp,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'MAPA' THEN 1 ELSE 0 END), 0) AS rutas_mapa,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'TELEFONO' THEN 1 ELSE 0 END), 0) AS llamadas,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'MENU' THEN 1 ELSE 0 END), 0) AS clics_menu,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'PROMOCION' THEN 1 ELSE 0 END), 0) AS clics_promocion,
                       COALESCE(SUM(CASE WHEN i.tipo_evento = 'FAVORITO' THEN 1 ELSE 0 END), 0) AS guardado_favoritos,
                       COALESCE(SUM(CASE WHEN i.tipo_evento IN ('WHATSAPP', 'MAPA', 'TELEFONO') THEN 1 ELSE 0 END), 0) AS total_conversiones
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                LEFT JOIN interacciones_lugar i ON l.id_lugar = i.id_lugar {$extraJoin}
                WHERE l.tipo_lugar = 'COMERCIAL'
                GROUP BY l.id_lugar, l.nombre, l.slug, c.nombre
                ORDER BY total_conversiones DESC, vistas DESC
                LIMIT " . (int)$limite;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Totales acumulados de conversión para KPIs ejecutivos
     */
    public static function obtenerTotalesGlobales(?string $desde = null, ?string $hasta = null): array {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if (!empty($desde)) {
            $where[] = "fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if (!empty($hasta)) {
            $where[] = "fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN tipo_evento = 'VISTA' THEN 1 ELSE 0 END), 0) AS total_vistas,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'WHATSAPP' THEN 1 ELSE 0 END), 0) AS total_whatsapp,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'MAPA' THEN 1 ELSE 0 END), 0) AS total_mapa,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'FAVORITO' THEN 1 ELSE 0 END), 0) AS total_favoritos,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'TELEFONO' THEN 1 ELSE 0 END), 0) AS total_telefono,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'MENU' THEN 1 ELSE 0 END), 0) AS total_menu,
                    COALESCE(SUM(CASE WHEN tipo_evento = 'PROMOCION' THEN 1 ELSE 0 END), 0) AS total_promociones,
                    COALESCE(SUM(CASE WHEN tipo_evento IN ('WHATSAPP', 'MAPA', 'TELEFONO') THEN 1 ELSE 0 END), 0) AS total_conversiones
                FROM interacciones_lugar {$whereClause}";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_vistas'        => (int)($res['total_vistas'] ?? 0),
            'total_whatsapp'      => (int)($res['total_whatsapp'] ?? 0),
            'total_mapa'          => (int)($res['total_mapa'] ?? 0),
            'total_favoritos'     => (int)($res['total_favoritos'] ?? 0),
            'total_telefono'      => (int)($res['total_telefono'] ?? 0),
            'total_menu'          => (int)($res['total_menu'] ?? 0),
            'total_promociones'   => (int)($res['total_promociones'] ?? 0),
            'total_conversiones'  => (int)($res['total_conversiones'] ?? 0),
            'tasa_conversion_gral'=> (int)($res['total_vistas'] ?? 0) > 0 
                ? round(((int)$res['total_conversiones'] / (int)$res['total_vistas']) * 100, 1) 
                : 0.0
        ];
    }
}
