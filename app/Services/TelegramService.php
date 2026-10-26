<?php
namespace App\Services;

use App\Core\Database;
use RuntimeException;
use Throwable;

class TelegramService {
    // Transporte sustituible en pruebas aisladas, nunca desde parámetros HTTP.
    private static ?\Closure $transporte = null;
    public static function configuracion(): array {
        return require dirname(__DIR__, 2) . '/config/comercial.php';
    }

    public static function configurado(): bool {
        $c = self::configuracion();
        if (empty($c['telegram_activo'])) {
            return false;
        }
        return preg_match('/\A\d+:[A-Za-z0-9_-]+\z/D', (string)($c['telegram_token'] ?? '')) === 1
            && preg_match('/\A-?[1-9]\d*\z/D', (string)($c['admin_chat_id'] ?? '')) === 1;
    }

    /** Comprueba token y conectividad sin enviar mensajes ni modificar datos. */
    public static function verificarConexion(): string {
        $respuesta = self::api('getMe', []);
        return (string)($respuesta['result']['username'] ?? '');
    }

    private static function api(string $metodo, array $datos): array {
        if (self::$transporte !== null) return (self::$transporte)($metodo, $datos);
        if (!self::configurado()) throw new RuntimeException('Telegram no está configurado.');
        $config = self::configuracion();
        $curl = curl_init('https://api.telegram.org/bot' . $config['telegram_token'] . '/' . $metodo);
        curl_setopt_array($curl, [CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>$datos,
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>20,
            CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS, CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2]);
        $respuesta = curl_exec($curl);
        $errorCurl = curl_errno($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json = is_string($respuesta) ? json_decode($respuesta, true) : null;
        if ($status !== 200 || !is_array($json) || empty($json['ok'])) {
            // Nunca incluir la URL (contiene el token), el cuerpo o credenciales en logs.
            if ($metodo === 'editMessageCaption' && ($json['description'] ?? '') === 'Bad Request: message is not modified') return [];
            throw new RuntimeException('Telegram no confirmó ' . $metodo . ' (HTTP ' . $status . ', cURL ' . $errorCurl . ').');
        }
        return $json;
    }

    private static function html(string $texto): string {
        return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function notificarNuevaSolicitud(int $idSolicitud, array $datos, string $rutaComprobante): int {
        $config = self::configuracion();
        $mime = ComprobanteService::validarImagen($rutaComprobante);
        $caption = "🔔 <b>NUEVA SOLICITUD COMERCIAL - BENITOURS</b>\n" .
            '🏪 <b>Negocio:</b> ' . self::html($datos['nombre_establecimiento']) . "\n" .
            '📌 <b>Categoría:</b> ' . self::html($datos['categoria']) . "\n" .
            '👤 <b>Dueño:</b> ' . self::html($datos['nombre_solicitante']) . "\n" .
            '📞 <b>WhatsApp:</b> ' . self::html($datos['telefono_contacto']) . "\n" .
            '💳 <b>Plan:</b> ' . self::html($datos['plan_solicitado']) . ' (Bs ' . number_format((float)$datos['monto_declarado'], 2) . ")\n" .
            '🕒 <b>Fecha:</b> ' . date('d/m/Y H:i', strtotime($datos['created_at'])) . "\nSolicitud #" . $idSolicitud;
        $teclado = ['inline_keyboard'=>[
            [['text'=>'✅ Aprobar y Activar','callback_data'=>'aprobar_solicitud_' . $idSolicitud]],
            [['text'=>'❌ Rechazar','callback_data'=>'rechazar_solicitud_' . $idSolicitud]]
        ]];
        $respuesta = self::api('sendPhoto', ['chat_id'=>$config['admin_chat_id'],
            'photo'=>new \CURLFile($rutaComprobante, $mime, basename($rutaComprobante)),
            'caption'=>$caption, 'parse_mode'=>'HTML', 'protect_content'=>'true',
            'reply_markup'=>json_encode($teclado, JSON_UNESCAPED_UNICODE)]);
        return (int)$respuesta['result']['message_id'];
    }

    public static function notificarNuevaSolicitudTexto(int $idSolicitud, array $datos): int {
        $config = self::configuracion();
        $texto = "🔔 <b>NUEVA SOLICITUD EN EFECTIVO - BENITOURS</b>\n" .
            '🏪 <b>Negocio:</b> ' . self::html($datos['nombre_establecimiento']) . "\n" .
            '📌 <b>Categoría:</b> ' . self::html($datos['categoria'] ?? '') . "\n" .
            '👤 <b>Dueño:</b> ' . self::html($datos['nombre_solicitante']) . "\n" .
            '📞 <b>WhatsApp:</b> ' . self::html($datos['telefono_contacto']) . "\n" .
            "💵 <b>Método:</b> PAGO EN EFECTIVO (Pendiente de cobro)\n" .
            '💳 <b>Plan:</b> ' . self::html($datos['plan_solicitado']) . ' (Bs ' . number_format((float)$datos['monto_declarado'], 2) . ")\n" .
            '🕒 <b>Fecha:</b> ' . date('d/m/Y H:i', strtotime($datos['created_at'])) . "\nSolicitud #" . $idSolicitud;
        $teclado = ['inline_keyboard'=>[
            [['text'=>'✅ Cobrar Efectivo y Activar','callback_data'=>'aprobar_solicitud_' . $idSolicitud]],
            [['text'=>'❌ Rechazar','callback_data'=>'rechazar_solicitud_' . $idSolicitud]]
        ]];
        $respuesta = self::api('sendMessage', ['chat_id'=>$config['admin_chat_id'],
            'text'=>$texto, 'parse_mode'=>'HTML', 'protect_content'=>'true',
            'reply_markup'=>json_encode($teclado, JSON_UNESCAPED_UNICODE)]);
        return (int)$respuesta['result']['message_id'];
    }

    public static function actualizarMensaje(int $chatId, int $messageId, string $nuevoTexto): void {
        try {
            self::api('editMessageCaption', ['chat_id'=>$chatId, 'message_id'=>$messageId,
                'caption'=>$nuevoTexto, 'reply_markup'=>json_encode(['inline_keyboard'=>[]])]);
        } catch (\Throwable $e) {
            try {
                self::api('editMessageText', ['chat_id'=>$chatId, 'message_id'=>$messageId,
                    'text'=>$nuevoTexto, 'reply_markup'=>json_encode(['inline_keyboard'=>[]])]);
            } catch (\Throwable $e2) {}
        }
    }

    public static function responderCallback(string $callbackId, string $texto, bool $showAlert = false): void {
        self::api('answerCallbackQuery', ['callback_query_id'=>$callbackId,'text'=>$texto,'show_alert'=>$showAlert ? 'true' : 'false']);
    }

    public static function registrarWebhook(string $url): void {
        $secret = self::configuracion()['webhook_secret'];
        if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https' ||
            !preg_match('/\A[A-Za-z0-9_-]{16,256}\z/D', $secret)) {
            throw new RuntimeException('Se requiere una URL HTTPS pública y un secreto de 16 a 256 caracteres (letras, números, _ o -).');
        }
        self::api('setWebhook', ['url'=>$url,'secret_token'=>$secret,'allowed_updates'=>'["callback_query","message"]']);
    }

    public static function encolar(int $idSolicitud, string $tipo): void {
        if (!self::configurado()) return;
        if (!in_array($tipo, ['NUEVA','RESOLUCION'], true)) throw new RuntimeException('Tipo de notificación inválido.');
        Database::getConnection()->prepare('INSERT INTO telegram_notificaciones (id_solicitud,tipo) VALUES (?,?) ON DUPLICATE KEY UPDATE id_solicitud=VALUES(id_solicitud)')
            ->execute([$idSolicitud,$tipo]);
    }

    // Reserva corta en BD, HTTP fuera de la transacción. Un trabajador caído se reintenta.
    public static function procesarPendientes(int $limite = 10, ?int $idSolicitud = null): int {
        if (!self::configurado()) return 0;
        $db = Database::getConnection();
        $procesados = 0;
        for ($i = 0; $i < $limite; $i++) {
            if (!Database::beginTransaction()) throw new RuntimeException('Trabajador dentro de una transacción.');
            try {
                $consulta = $db->prepare("SELECT * FROM telegram_notificaciones WHERE estado='PENDIENTE'
                    AND disponible_desde <= NOW()" . ($idSolicitud !== null ? ' AND id_solicitud = ?' : '') .
                    ' ORDER BY id_notificacion LIMIT 1 FOR UPDATE');
                $consulta->execute($idSolicitud !== null ? [$idSolicitud] : []);
                $job = $consulta->fetch();
                if (!$job) { $db->commit(); break; }
                $db->prepare('UPDATE telegram_notificaciones SET intentos=intentos+1, disponible_desde=DATE_ADD(NOW(), INTERVAL 2 MINUTE) WHERE id_notificacion=?')
                    ->execute([$job['id_notificacion']]);
                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                throw $e;
            }
            try {
                self::procesar($job);
                $db->prepare("UPDATE telegram_notificaciones SET estado='ENVIADA', ultimo_error=NULL WHERE id_notificacion=?")->execute([$job['id_notificacion']]);
                $procesados++;
            } catch (Throwable $e) {
                $detalle = preg_match('/\ATelegram no confirmó [A-Za-z]+ \(HTTP \d+, cURL \d+\)\.\z/u', $e->getMessage())
                    ? $e->getMessage() : 'No se confirmó la entrega; se reintentará.';
                $db->prepare('UPDATE telegram_notificaciones SET ultimo_error=?, disponible_desde=DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id_notificacion=?')
                    ->execute([$detalle, $job['id_notificacion']]);
                error_log('Telegram: entrega pendiente #' . $job['id_notificacion']);
            }
        }
        return $procesados;
    }

    private static function procesar(array $job): void {
        $db = Database::getConnection();
        $s = (new \App\Models\Solicitud())->buscarPorId((int)$job['id_solicitud']);
        if ($job['tipo'] === 'NUEVA') {
            if ($s['estado'] !== 'PENDIENTE' || $s['telegram_message_id']) return;
            if (!empty($s['comprobante_archivo'])) {
                $messageId = self::notificarNuevaSolicitud((int)$s['id_solicitud'], $s, ComprobanteService::ruta($s['comprobante_archivo']));
            } else {
                $messageId = self::notificarNuevaSolicitudTexto((int)$s['id_solicitud'], $s);
            }
            $db->prepare('UPDATE solicitudes SET telegram_message_id=? WHERE id_solicitud=?')->execute([$messageId,$s['id_solicitud']]);
            // Si la resolución coincidió con sendPhoto, reactivar el trabajo de resolución.
            $db->prepare("UPDATE telegram_notificaciones SET estado='PENDIENTE', disponible_desde=NOW() WHERE id_solicitud=? AND tipo='RESOLUCION'")->execute([$s['id_solicitud']]);
            return;
        }
        if ($s['estado'] === 'PENDIENTE') return;
        $chatId = (int)self::configuracion()['admin_chat_id'];
        $texto = ($s['estado'] === 'ACEPTADA' ? '🟢 SOLICITUD APROBADA Y NEGOCIO ACTIVADO' : '🔴 SOLICITUD RECHAZADA') .
            "\n#{$s['id_solicitud']} · {$s['nombre_establecimiento']}\n" . date('d/m/Y H:i');
        if ($s['telegram_message_id']) self::actualizarMensaje($chatId, (int)$s['telegram_message_id'], $texto);
        if ($s['estado'] === 'ACEPTADA') {
            $link = SolicitudService::enlaceBienvenida($s);
            $mensaje = self::html($texto);
            if ($link) $mensaje .= "\n" . '<a href="' . self::html($link) . '">📲 Enviar credenciales por WhatsApp</a>';
            self::api('sendMessage', ['chat_id'=>$chatId,'text'=>$mensaje,
                'parse_mode'=>'HTML','protect_content'=>'true','link_preview_options'=>'{"is_disabled":true}']);
        }
    }

    public static function validarWebhook(array $update, string $secreto): array {
        $config = self::configuracion();
        if (!self::configurado() || $config['webhook_secret'] === '' || !hash_equals($config['webhook_secret'], $secreto)) {
            throw new \InvalidArgumentException('Webhook no autorizado.');
        }

        // 1. Manejo de Callback Queries (botones de aprobación o comandos rápidos)
        $callback = $update['callback_query'] ?? null;
        if (is_array($callback)) {
            if ((string)($callback['from']['id'] ?? '') !== (string)$config['admin_chat_id'] ||
                (string)($callback['message']['chat']['id'] ?? '') !== (string)$config['admin_chat_id'] ||
                ($callback['message']['chat']['type'] ?? '') !== 'private') {
                throw new \InvalidArgumentException('Administrador de Telegram no autorizado.');
            }

            $data = (string)($callback['data'] ?? '');

            // Comandos rápidos interactivos emitidos desde botones
            if (str_starts_with($data, 'cmd_')) {
                return [
                    'tipo'        => 'callback_comando',
                    'comando'     => substr($data, 4),
                    'callback_id' => $callback['id'],
                    'chat_id'     => (int)$config['admin_chat_id']
                ];
            }

            if (!preg_match('/\A(aprobar|rechazar)_solicitud_([1-9][0-9]{0,9})\z/D', $data, $m) ||
                !is_int($callback['message']['message_id'] ?? null)) {
                throw new \InvalidArgumentException('Callback inválido.');
            }

            return [
                'tipo'         => 'callback_solicitud',
                'accion'       => $m[1],
                'id_solicitud' => (int)$m[2],
                'callback_id'  => $callback['id'],
                'chat_id'      => (int)$config['admin_chat_id'],
                'message_id'   => $callback['message']['message_id'],
                'admin_id'     => $config['telegram_admin_id'] ?: null
            ];
        }

        // 2. Manejo de Mensajes y Comandos de Texto del Administrador
        $message = $update['message'] ?? null;
        if (is_array($message)) {
            if ((string)($message['from']['id'] ?? '') !== (string)$config['admin_chat_id'] ||
                (string)($message['chat']['id'] ?? '') !== (string)$config['admin_chat_id'] ||
                ($message['chat']['type'] ?? '') !== 'private') {
                throw new \InvalidArgumentException('Mensaje de usuario no autorizado.');
            }

            $texto = trim((string)($message['text'] ?? ''));
            if ($texto === '') {
                return [];
            }

            return [
                'tipo'       => 'mensaje',
                'texto'      => $texto,
                'chat_id'    => (int)$config['admin_chat_id'],
                'message_id' => (int)($message['message_id'] ?? 0)
            ];
        }

        return [];
    }

    /**
     * Obtiene los comercios con membresía próxima a vencer (en los siguientes $diasMax días).
     */
    public static function obtenerComerciosPorVencer(int $diasMax = 15): array {
        $db = Database::getConnection();
        $sql = "SELECT l.id_lugar, l.nombre, l.whatsapp_contacto, l.telefono_contacto, l.direccion,
                       c.nombre AS categoria,
                       v.fecha_vencimiento, DATEDIFF(v.fecha_vencimiento, CURDATE()) AS dias_restantes,
                       COALESCE(p.monto, (SELECT t.monto FROM tarifas t WHERE t.activo = 1 ORDER BY t.vigente_desde DESC LIMIT 1)) AS monto_estimado
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN (
                    SELECT v1.id_lugar, v1.fecha_vencimiento
                    FROM vigencias v1
                    INNER JOIN (
                        SELECT id_lugar, MAX(fecha_vencimiento) AS max_venc
                        FROM vigencias GROUP BY id_lugar
                    ) v2 ON v1.id_lugar = v2.id_lugar AND v1.fecha_vencimiento = v2.max_venc
                ) v ON l.id_lugar = v.id_lugar
                LEFT JOIN (
                    SELECT p1.id_lugar, p1.monto
                    FROM pagos p1
                    INNER JOIN (
                        SELECT id_lugar, MAX(id_pago) AS max_id
                        FROM pagos WHERE estado = 'CONFIRMADO' GROUP BY id_lugar
                    ) p2 ON p1.id_lugar = p2.id_lugar AND p1.id_pago = p2.max_id
                ) p ON l.id_lugar = p.id_lugar
                WHERE l.tipo_lugar = 'COMERCIAL'
                  AND v.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                ORDER BY v.fecha_vencimiento ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':dias' => $diasMax]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los comercios que ya vencieron en los últimos $diasAtras días.
     */
    public static function obtenerComerciosVencidos(int $diasAtras = 30): array {
        $db = Database::getConnection();
        $sql = "SELECT l.id_lugar, l.nombre, l.whatsapp_contacto, l.telefono_contacto, l.direccion,
                       c.nombre AS categoria,
                       v.fecha_vencimiento, DATEDIFF(CURDATE(), v.fecha_vencimiento) AS dias_vencido,
                       COALESCE(p.monto, (SELECT t.monto FROM tarifas t WHERE t.activo = 1 ORDER BY t.vigente_desde DESC LIMIT 1)) AS monto_estimado
                FROM lugares l
                INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                INNER JOIN (
                    SELECT v1.id_lugar, v1.fecha_vencimiento
                    FROM vigencias v1
                    INNER JOIN (
                        SELECT id_lugar, MAX(fecha_vencimiento) AS max_venc
                        FROM vigencias GROUP BY id_lugar
                    ) v2 ON v1.id_lugar = v2.id_lugar AND v1.fecha_vencimiento = v2.max_venc
                ) v ON l.id_lugar = v.id_lugar
                LEFT JOIN (
                    SELECT p1.id_lugar, p1.monto
                    FROM pagos p1
                    INNER JOIN (
                        SELECT id_lugar, MAX(id_pago) AS max_id
                        FROM pagos WHERE estado = 'CONFIRMADO' GROUP BY id_lugar
                    ) p2 ON p1.id_lugar = p2.id_lugar AND p1.id_pago = p2.max_id
                ) p ON l.id_lugar = p.id_lugar
                WHERE l.tipo_lugar = 'COMERCIAL'
                  AND v.fecha_vencimiento < CURDATE()
                  AND v.fecha_vencimiento >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                ORDER BY v.fecha_vencimiento DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':dias' => $diasAtras]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Resumen numérico en tiempo real para el administrador.
     */
    public static function obtenerResumenEjecutivo(): array {
        $db = Database::getConnection();
        $totalComercios = (int)$db->query("SELECT COUNT(*) FROM lugares WHERE tipo_lugar = 'COMERCIAL'")->fetchColumn();
        $totalPublicos = (int)$db->query("SELECT COUNT(*) FROM lugares WHERE tipo_lugar = 'PUBLICO'")->fetchColumn();
        $solicitudesPendientes = (int)$db->query("SELECT COUNT(*) FROM solicitudes WHERE estado = 'PENDIENTE'")->fetchColumn();
        $comerciosActivos = (int)$db->query("SELECT COUNT(DISTINCT v.id_lugar) FROM vigencias v INNER JOIN lugares l ON l.id_lugar = v.id_lugar WHERE l.tipo_lugar = 'COMERCIAL' AND v.fecha_vencimiento >= CURDATE()")->fetchColumn();
        $comerciosPorVencer = count(self::obtenerComerciosPorVencer(15));
        $comerciosVencidos = count(self::obtenerComerciosVencidos(30));

        $visitas = \App\Services\AnaliticaVisitasService::resumen();

        return [
            'comercios_activos'      => $comerciosActivos,
            'comercios_total'        => $totalComercios,
            'lugares_publicos'       => $totalPublicos,
            'por_vencer'             => $comerciosPorVencer,
            'vencidos'               => $comerciosVencidos,
            'solicitudes_pendientes' => $solicitudesPendientes,
            'visitas_hoy'            => $visitas['hoy'] ?? 0,
            'visitas_mes'            => $visitas['mes'] ?? 0,
        ];
    }

    /**
     * Genera la URL oficial https://wa.me/ con mensaje ya codificado.
     */
    public static function generarUrlWhatsApp(string $telefono, string $mensaje): ?string {
        $raw = preg_replace('/\D/', '', $telefono);
        if (strlen($raw) === 8) {
            $raw = '591' . $raw;
        }
        if (strlen($raw) < 8) {
            return null;
        }
        return 'https://wa.me/' . $raw . '?text=' . rawurlencode($mensaje);
    }

    /**
     * Redacta un mensaje de cobro formal, educado y personalizado.
     */
    public static function redactarMensajeCobro(array $c): string {
        $nombre = $c['nombre'];
        $dias = (int)($c['dias_restantes'] ?? 0);
        $fechaFmt = date('d/m/Y', strtotime($c['fecha_vencimiento']));
        $monto = number_format((float)($c['monto_estimado'] ?? 250), 0);

        if ($dias === 0) {
            return "Hola *{$nombre}*, le saludamos cordialmente del equipo de *BeniTours Trinidad*.\n\nLe recordamos que su membresía comercial en la plataforma turística vence *HOY {$fechaFmt}*. Para mantener activa su presencia y promociones ante los turistas sin interrupciones, puede realizar su renovación por transferencia o QR bancario (Bs {$monto}).\n\n¿Desea que le compartamos los datos de pago?";
        } elseif ($dias <= 3) {
            return "Hola *{$nombre}*, le saludamos cordialmente del equipo de *BeniTours Trinidad*.\n\nLe recordamos que su membresía comercial en la guía turística vence en solo *{$dias} días* ({$fechaFmt}). Para mantener visible su negocio y promociones, puede realizar su renovación anticipada de Bs {$monto}.\n\n¿Gusta que le facilitemos los datos de transferencia o QR?";
        } else {
            return "Hola *{$nombre}*, le saludamos cordialmente del equipo de *BeniTours Trinidad*.\n\nLe recordamos que su membresía comercial vence el *{$fechaFmt}* (en {$dias} días). Para asegurar la continuidad de su ficha en el catálogo y mapa interactivo, ya puede gestionar su renovación (Bs {$monto}).\n\n¿Desea que le facilitemos los datos de pago?";
        }
    }

    /**
     * Redacta mensaje de invitación a reactivar membresía vencida.
     */
    public static function redactarMensajeReactivacion(array $c): string {
        $nombre = $c['nombre'];
        $diasVencido = (int)($c['dias_vencido'] ?? 0);
        $fechaFmt = date('d/m/Y', strtotime($c['fecha_vencimiento']));
        $monto = number_format((float)($c['monto_estimado'] ?? 250), 0);

        return "Hola *{$nombre}*, le saludamos cordialmente del equipo de *BeniTours Trinidad*.\n\nLe recordamos que su membresía comercial en la guía turística y mapa oficial venció el {$fechaFmt} (hace {$diasVencido} días). Para reactivar la visibilidad de su establecimiento y promociones ante los turistas que visitan la ciudad, puede realizar su renovación por transferencia o QR bancario (Bs {$monto}).\n\n¿Desea que le compartamos los datos de cuenta?";
    }

    /**
     * Envía la lista de alertas de vencimiento con botones directos para WhatsApp.
     */
    public static function enviarAlertaVencimientos(?int $chatId = null): void {
        $chatId = $chatId ?: (int)self::configuracion()['admin_chat_id'];
        $porVencer = self::obtenerComerciosPorVencer(15);

        if (empty($porVencer)) {
            $texto = "✅ <b>TODOS LOS COMERCIOS AL DÍA</b>\n\nNo hay negocios con membresía por vencer en los próximos 15 días en BeniTours.";
            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $texto,
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '📊 Ver Resumen General', 'callback_data' => 'cmd_resumen']]
                    ]
                ], JSON_UNESCAPED_UNICODE)
            ]);
            return;
        }

        $total = count($porVencer);
        $header = "⚠️ <b>ALERTAS DE COBRANZA - BENITOURS</b>\n" .
                  "Se encontraron <b>{$total}</b> comercio(s) con membresía próxima a vencer (en los siguientes 15 días):\n";

        self::api('sendMessage', [
            'chat_id'    => $chatId,
            'text'       => $header,
            'parse_mode' => 'HTML'
        ]);

        foreach ($porVencer as $c) {
            $dias = (int)$c['dias_restantes'];
            $fechaFmt = date('d/m/Y', strtotime($c['fecha_vencimiento']));
            $montoFmt = number_format((float)$c['monto_estimado'], 0);
            $urgencia = $dias === 0 ? '🚨 <b>¡VENCE HOY!</b>' : ($dias <= 3 ? "⚡ <b>Vence en {$dias} días</b>" : "⏳ <b>Vence en {$dias} días</b>");

            $msgWa = self::redactarMensajeCobro($c);
            $telefono = trim($c['whatsapp_contacto'] ?: ($c['telefono_contacto'] ?: ''));
            $urlWa = self::generarUrlWhatsApp($telefono, $msgWa);

            $card = "🏪 <b>" . self::html($c['nombre']) . "</b>\n" .
                    "📌 Categoría: " . self::html($c['categoria']) . "\n" .
                    "📅 Vencimiento: {$fechaFmt} ({$urgencia})\n" .
                    "📞 Contacto: " . self::html($telefono ?: 'Sin teléfono registrado') . "\n" .
                    "💵 Renovación est.: Bs {$montoFmt}";

            $teclado = ['inline_keyboard' => []];
            if ($urlWa) {
                $teclado['inline_keyboard'][] = [
                    ['text' => '📲 Cobrar por WhatsApp', 'url' => $urlWa]
                ];
            } else {
                $card .= "\n⚠️ <i>No tiene número de WhatsApp registrado.</i>";
            }

            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $card,
                'parse_mode'   => 'HTML',
                'reply_markup' => !empty($teclado['inline_keyboard']) ? json_encode($teclado, JSON_UNESCAPED_UNICODE) : null
            ]);
        }
    }

    /**
     * Envía la lista de comercios vencidos con botones directos para WhatsApp.
     */
    public static function enviarAlertaVencidos(?int $chatId = null): void {
        $chatId = $chatId ?: (int)self::configuracion()['admin_chat_id'];
        $vencidos = self::obtenerComerciosVencidos(30);

        if (empty($vencidos)) {
            $texto = "✅ <b>SIN COMERCIOS VENCIDOS</b>\n\nNo hay negocios con membresía vencida en los últimos 30 días.";
            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $texto,
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '📊 Ver Resumen General', 'callback_data' => 'cmd_resumen']]
                    ]
                ], JSON_UNESCAPED_UNICODE)
            ]);
            return;
        }

        $total = count($vencidos);
        $header = "🔴 <b>COMERCIOS VENCIDOS / INACTIVOS - BENITOURS</b>\n" .
                  "Se encontraron <b>{$total}</b> negocio(s) vencido(s) en los últimos 30 días:\n";

        self::api('sendMessage', [
            'chat_id'    => $chatId,
            'text'       => $header,
            'parse_mode' => 'HTML'
        ]);

        foreach ($vencidos as $c) {
            $dias = (int)$c['dias_vencido'];
            $fechaFmt = date('d/m/Y', strtotime($c['fecha_vencimiento']));
            $montoFmt = number_format((float)$c['monto_estimado'], 0);

            $msgWa = self::redactarMensajeReactivacion($c);
            $telefono = trim($c['whatsapp_contacto'] ?: ($c['telefono_contacto'] ?: ''));
            $urlWa = self::generarUrlWhatsApp($telefono, $msgWa);

            $card = "🏪 <b>" . self::html($c['nombre']) . "</b>\n" .
                    "📌 Categoría: " . self::html($c['categoria']) . "\n" .
                    "⏳ Venció el: {$fechaFmt} (hace {$dias} días)\n" .
                    "📞 Contacto: " . self::html($telefono ?: 'Sin teléfono registrado') . "\n" .
                    "💵 Renovación est.: Bs {$montoFmt}";

            $teclado = ['inline_keyboard' => []];
            if ($urlWa) {
                $teclado['inline_keyboard'][] = [
                    ['text' => '📲 Reactivar por WhatsApp', 'url' => $urlWa]
                ];
            } else {
                $card .= "\n⚠️ <i>No tiene número de WhatsApp registrado.</i>";
            }

            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $card,
                'parse_mode'   => 'HTML',
                'reply_markup' => !empty($teclado['inline_keyboard']) ? json_encode($teclado, JSON_UNESCAPED_UNICODE) : null
            ]);
        }
    }

    /**
     * Envía el tablero ejecutivo con botones interactivos.
     */
    public static function enviarResumenEjecutivo(?int $chatId = null): void {
        $chatId = $chatId ?: (int)self::configuracion()['admin_chat_id'];
        $r = self::obtenerResumenEjecutivo();
        $fecha = date('d/m/Y H:i');

        $texto = "📊 <b>TABLERO EJECUTIVO - BENITOURS</b>\n" .
                 "🕒 <i>Actualizado: {$fecha}</i>\n\n" .
                 "🏪 <b>Comercios Activos:</b> {$r['comercios_activos']} / {$r['comercios_total']}\n" .
                 "⚠️ <b>Por Vencer (15d):</b> {$r['por_vencer']}\n" .
                 "🔴 <b>Suscripciones Vencidas:</b> {$r['vencidos']}\n" .
                 "📩 <b>Solicitudes Pendientes:</b> {$r['solicitudes_pendientes']}\n" .
                 "👥 <b>Visitas Web Hoy:</b> {$r['visitas_hoy']} (Mes: {$r['visitas_mes']})\n\n" .
                 "👇 <i>Toca una opción para gestionar:</i>";

        $teclado = [
            'inline_keyboard' => [
                [
                    ['text' => '⚠️ Ver por Vencer (' . $r['por_vencer'] . ')', 'callback_data' => 'cmd_vencimientos'],
                    ['text' => '🔴 Ver Vencidos (' . $r['vencidos'] . ')', 'callback_data' => 'cmd_vencidos']
                ],
                [
                    ['text' => '🔄 Actualizar Resumen', 'callback_data' => 'cmd_resumen']
                ]
            ]
        ];

        self::api('sendMessage', [
            'chat_id'      => $chatId,
            'text'         => $texto,
            'parse_mode'   => 'HTML',
            'reply_markup' => json_encode($teclado, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * Busca negocios por nombre y devuelve su ficha de contacto.
     */
    public static function buscarComercio(string $termino, int $chatId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT l.id_lugar, l.nombre, l.tipo_lugar, l.telefono_contacto, l.whatsapp_contacto,
                                     c.nombre AS categoria,
                                     (SELECT v.fecha_vencimiento FROM vigencias v WHERE v.id_lugar = l.id_lugar ORDER BY v.fecha_vencimiento DESC LIMIT 1) AS fecha_vencimiento,
                                     (SELECT p.aprobado FROM publicaciones p WHERE p.id_lugar = l.id_lugar LIMIT 1) AS aprobado,
                                     (SELECT p.habilitado FROM publicaciones p WHERE p.id_lugar = l.id_lugar LIMIT 1) AS habilitado
                              FROM lugares l
                              INNER JOIN categorias c ON l.id_categoria = c.id_categoria
                              WHERE l.nombre LIKE :termino OR l.direccion LIKE :termino
                              ORDER BY l.nombre ASC LIMIT 5");
        $stmt->execute([':termino' => '%' . $termino . '%']);
        $lugares = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($lugares)) {
            self::api('sendMessage', [
                'chat_id'    => $chatId,
                'text'       => "🔍 No se encontraron negocios que coincidan con: <b>" . self::html($termino) . "</b>",
                'parse_mode' => 'HTML'
            ]);
            return;
        }

        $header = "🔍 <b>RESULTADOS PARA: " . self::html($termino) . "</b> (" . count($lugares) . " encontrado(s)):\n";
        self::api('sendMessage', [
            'chat_id'    => $chatId,
            'text'       => $header,
            'parse_mode' => 'HTML'
        ]);

        foreach ($lugares as $l) {
            $estadoPub = ((int)($l['aprobado'] ?? 0) === 1 && (int)($l['habilitado'] ?? 0) === 1) ? '🟢 Publicado' : '🔴 Inactivo';
            $venc = !empty($l['fecha_vencimiento']) ? date('d/m/Y', strtotime($l['fecha_vencimiento'])) : 'Sin vigencia';
            $tel = trim($l['whatsapp_contacto'] ?: ($l['telefono_contacto'] ?: ''));

            $card = "🏪 <b>" . self::html($l['nombre']) . "</b>\n" .
                    "📌 Categoría: " . self::html($l['categoria']) . "\n" .
                    "🏷️ Tipo: " . self::html($l['tipo_lugar']) . " (" . $estadoPub . ")\n" .
                    "📅 Membresía: " . $venc . "\n" .
                    "📞 Teléfono: " . self::html($tel ?: 'No registrado');

            $teclado = ['inline_keyboard' => []];
            if ($tel) {
                $raw = preg_replace('/\D/', '', $tel);
                if (strlen($raw) === 8) $raw = '591' . $raw;
                if (strlen($raw) >= 8) {
                    $msgWa = "Hola *{$l['nombre']}*, le saludamos del equipo de *BeniTours*.";
                    $teclado['inline_keyboard'][] = [
                        ['text' => '📲 Abrir WhatsApp', 'url' => 'https://wa.me/' . $raw . '?text=' . rawurlencode($msgWa)]
                    ];
                }
            }

            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $card,
                'parse_mode'   => 'HTML',
                'reply_markup' => !empty($teclado['inline_keyboard']) ? json_encode($teclado, JSON_UNESCAPED_UNICODE) : null
            ]);
        }
    }

    /**
     * Alerta matutina diaria automática para el administrador.
     */
    public static function enviarAlertaMatutinaDiaria(?int $chatId = null): void {
        $chatId = $chatId ?: (int)self::configuracion()['admin_chat_id'];
        $porVencer = self::obtenerComerciosPorVencer(7);
        $solicitudes = (int)Database::getConnection()->query("SELECT COUNT(*) FROM solicitudes WHERE estado = 'PENDIENTE'")->fetchColumn();

        if (empty($porVencer) && $solicitudes === 0) {
            return;
        }

        $totalVenc = count($porVencer);
        $texto = "🌅 <b>REPORTE MATUTINO BENITOURS</b>\n" .
                 "📅 <i>" . date('d/m/Y') . "</i>\n\n";

        if ($solicitudes > 0) {
            $texto .= "📩 <b>Solicitudes Pendientes:</b> {$solicitudes} negocio(s) esperando aprobación.\n";
        }
        if ($totalVenc > 0) {
            $texto .= "⚠️ <b>Cobranzas Esta Semana:</b> {$totalVenc} negocio(s) vencen en los próximos 7 días.\n";
        }
        $texto .= "\n👇 <i>Gestionar ahora:</i>";

        $teclado = [
            'inline_keyboard' => [
                [
                    ['text' => '⚠️ Ver Cobranzas (' . $totalVenc . ')', 'callback_data' => 'cmd_vencimientos'],
                    ['text' => '📊 Resumen General', 'callback_data' => 'cmd_resumen']
                ]
            ]
        ];

        self::api('sendMessage', [
            'chat_id'      => $chatId,
            'text'         => $texto,
            'parse_mode'   => 'HTML',
            'reply_markup' => json_encode($teclado, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * Procesa los comandos de texto enviados por el administrador.
     */
    public static function procesarComandoMensaje(string $texto, int $chatId): void {
        $cmd = strtolower(trim($texto));

        if ($cmd === '/start' || $cmd === '/ayuda' || $cmd === '/menu' || $cmd === 'ayuda' || $cmd === 'menu') {
            $msg = "🤖 <b>ASISTENTE DE COBRANZAS Y CONTROL BENITOURS</b>\n\n" .
                   "¡Hola! Soy tu asistente operativo. Puedes escribirme cualquiera de estos comandos:\n\n" .
                   "📊 <b>/resumen</b> - Estado general de comercios, solicitudes y visitas.\n" .
                   "⚠️ <b>/vencimientos</b> - Comercios que vencen en los próximos 15 días con botón directo para cobrar por WhatsApp.\n" .
                   "🔴 <b>/vencidos</b> - Comercios que expiraron recientemente con botón para reactivarlos.\n" .
                   "🔍 <b>/buscar [nombre]</b> - Buscar los datos y contacto de un negocio.\n" .
                   "ℹ️ <b>/ayuda</b> - Ver esta lista de comandos.";

            $teclado = [
                'inline_keyboard' => [
                    [
                        ['text' => '📊 Resumen General', 'callback_data' => 'cmd_resumen'],
                        ['text' => '⚠️ Ver Vencimientos', 'callback_data' => 'cmd_vencimientos']
                    ],
                    [
                        ['text' => '🔴 Ver Vencidos', 'callback_data' => 'cmd_vencidos']
                    ]
                ]
            ];

            self::api('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $msg,
                'parse_mode'   => 'HTML',
                'reply_markup' => json_encode($teclado, JSON_UNESCAPED_UNICODE)
            ]);
            return;
        }

        if ($cmd === '/resumen' || $cmd === '/estado' || $cmd === 'resumen') {
            self::enviarResumenEjecutivo($chatId);
            return;
        }

        if ($cmd === '/vencimientos' || $cmd === '/cobros' || $cmd === 'vencimientos' || $cmd === 'cobros') {
            self::enviarAlertaVencimientos($chatId);
            return;
        }

        if ($cmd === '/vencidos' || $cmd === 'vencidos') {
            self::enviarAlertaVencidos($chatId);
            return;
        }

        if (str_starts_with($cmd, '/buscar ') || str_starts_with($cmd, 'buscar ')) {
            $termino = trim(preg_replace('/^\/?buscar\s+/i', '', $texto));
            if ($termino !== '') {
                self::buscarComercio($termino, $chatId);
            } else {
                self::api('sendMessage', [
                    'chat_id'    => $chatId,
                    'text'       => "ℹ️ Indica qué negocio deseas buscar. Ejemplo:\n<code>/buscar restaurante</code>",
                    'parse_mode' => 'HTML'
                ]);
            }
            return;
        }

        // Si escribió algo no reconocido:
        self::api('sendMessage', [
            'chat_id'    => $chatId,
            'text'       => "❓ Comando no reconocido: <code>" . self::html($texto) . "</code>\n\nEscribe <b>/ayuda</b> para ver los comandos disponibles.",
            'parse_mode' => 'HTML'
        ]);
    }
}
