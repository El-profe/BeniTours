# Despliegue en hosting

El archivo `.env` y `config/comercial.local.php` no se incluyen con valores reales en el paquete. Antes de publicar, copia `.env.example` como `.env` y completa los datos de base de datos, la URL HTTPS del dominio y los datos de Telegram.

## Subida

1. Sube y descomprime el paquete en el directorio de la aplicación. Si el hosting lo permite, define `public/` como *document root*. Si no, conserva el `.htaccess` de la raíz: redirige las visitas a `public/` y bloquea las carpetas privadas.
2. Crea la base de datos e importa `database/migrations/benitours.sql` desde phpMyAdmin. Si ya existe una base de datos activa, realiza antes una copia de seguridad y no la reemplaces con ese archivo.
3. Da permisos de escritura al usuario web sobre `storage/logs`, `storage/cache` y `storage/uploads` (habitualmente 755 o 775, según el proveedor).
4. Copia `.env.example` a `.env`, configura `APP_ENV=production`, `APP_DEBUG=false`, `BENITURS_BASE_URL=https://tu-dominio.com` y la conexión MySQL.

## Telegram

1. Crea un bot con `@BotFather`, copia su token en `TELEGRAM_BOT_TOKEN` y envía al menos un mensaje al bot desde la cuenta administradora.
2. Obtén tu identificador de chat privado y colócalo en `TELEGRAM_ADMIN_CHAT_ID`. No uses el ID del administrador de la base de datos; ese valor corresponde opcionalmente a `TELEGRAM_ADMIN_LOCAL_ID`.
3. Genera un secreto aleatorio de al menos 16 caracteres con letras, números, `_` o `-` y guárdalo en `TELEGRAM_WEBHOOK_SECRET`.
4. Desde la terminal del hosting ejecuta:

   ```sh
   php bin/verificar_telegram.php
   php bin/configurar_telegram.php https://tu-dominio.com/telegram_webhook.php
   ```

5. Programa un cron cada minuto para procesar la cola:

   ```sh
   * * * * * /usr/bin/php /ruta/absoluta/benitours/bin/telegram_notificaciones.php >/dev/null 2>&1
   ```

El webhook requiere HTTPS válido y está protegido por el secreto que Telegram envía en cada callback. No subas ni compartas el archivo `.env`.
