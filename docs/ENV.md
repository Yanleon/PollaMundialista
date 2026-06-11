# Variables de entorno

Ejemplo recomendado para produccion en Hostinger.

```env
APP_NAME="Polla Mundialista Empresarial 2026"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tudominio.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_CO
APP_TIMEZONE=America/Bogota

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_db
DB_USERNAME=usuario_db
DB_PASSWORD=password_db

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=correo@tudominio.com
MAIL_PASSWORD=password_correo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=correo@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"

WHATSAPP_GROUP_WEBHOOK_URL=

VITE_APP_NAME="${APP_NAME}"
```

## Notas

- `APP_KEY` se genera con `php artisan key:generate`.
- `APP_DEBUG` debe ser `false` en produccion.
- `APP_URL` debe ser el dominio final con `https`.
- `FILESYSTEM_DISK=public` ayuda a manejar imagenes publicas como logos y premios.
- `WHATSAPP_GROUP_WEBHOOK_URL` es opcional y funciona como respaldo. La forma recomendada es configurarlo desde `/admin/settings` en `Integraciones de notificaciones`.

## Variables sensibles

Nunca subir `.env` a Git. Contiene credenciales de base de datos, correo y llaves privadas.
