# Polla Mundialista Empresarial 2026

Plataforma interna para empleados: registro, pronosticos, puntajes automaticos, ranking y panel administrativo.

## Stack

- Laravel 13
- PHP 8.3
- MySQL
- Blade + Tailwind CSS
- Laravel Breeze (auth)

## Documentacion de despliegue

- Subir a Git: `docs/GIT.md`
- Despliegue en Hostinger: `docs/HOSTINGER.md`
- Variables de entorno: `docs/ENV.md`
- Operacion del sistema: `docs/OPERACION.md`
- Checklist de despliegue: `docs/CHECKLIST_DESPLIEGUE.md`

## Funcionalidades implementadas

- Autenticacion (login/registro).
- Roles: `admin` y `participant`.
- CRUD de equipos.
- CRUD de partidos.
- Registro y actualizacion de predicciones por participante.
- Calculo automatico de puntos con `ScoringService`.
- Ranking general (desempate por exactos y aciertos).
- Panel admin de participantes: activar/desactivar, editar datos, eliminar y cambiar contrasena.
- Configuracion general (logo, textos editables y restricciones de registro).
- Premios secretos para los tres primeros lugares.
- Carga de imagen por premio desde el panel admin.
- Destape automatico de premios el dia de la final o en una fecha configurada.
- Vista de premios tapados en el dashboard del participante antes del destape.
- Notificaciones de partidos del dia (correo + webhook WhatsApp opcional).
- Carga inicial de equipos/grupos/partidos desde feed FIFA 2026.

## Estructura clave

- Servicio de puntajes: `app/Services/ScoringService.php`
- Servicio de notificaciones: `app/Services/MatchNotificationService.php`
- Sync FIFA: `app/Console/Commands/SyncFifa2026Data.php`
- Vistas admin: `resources/views/admin/`
- Vistas participante: `resources/views/participant/`
- Ranking: `resources/views/leaderboard/index.blade.php`
- Navegacion/layout: `resources/views/layouts/`
- Ajustes generales y premios: `app/Models/AppSetting.php`

## Cambios recientes

- `/admin/users` ahora permite administrar participantes con acciones de editar, activar/desactivar, eliminar y cambiar contrasena.
- Se agrego la vista `resources/views/admin/users/edit.blade.php` para editar datos del participante y actualizar su contrasena.
- `/admin/settings` ahora incluye la seccion `Premios secretos` para definir los premios de primer, segundo y tercer lugar.
- Cada premio puede tener nombre e imagen propia.
- Los premios se guardan en `app_settings`; las imagenes se almacenan en el disco publico dentro de `storage/app/public/prizes`.
- En `/leaderboard`, los premios se muestran ocultos para participantes antes del destape y visibles para admin.
- En `/participant/dashboard`, los participantes ven tarjetas de premios tapadas antes de la final.
- Los premios se destapan automaticamente desde la fecha configurada en `prize_reveal_at`; si no existe, se usa la fecha del partido con fase `Final`.

## Instalacion local

1. Instalar dependencias:

```bash
composer install
npm install
```

2. Configurar `.env` (base de datos y zona horaria):

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=polla_mundialista
DB_USERNAME=root
DB_PASSWORD=

APP_TIMEZONE=America/Bogota
```

3. Ejecutar migraciones:

```bash
php artisan migrate
```

4. (Opcional) cargar datos FIFA 2026:

```bash
php artisan app:sync-fifa2026-data
```

5. Levantar entorno:

```bash
php artisan serve
npm run dev
```

## Usuarios de prueba

- Admin:
  - `admin@empresa.com`
  - `Admin12345`

- Participante:
  - `participante@empresa.com`
  - `Demo12345`

> Cambiar credenciales en produccion.

## Rutas principales

- Landing: `/`
- Dashboard: `/dashboard`
- Ranking: `/leaderboard`
- Participante:
  - `/participant/dashboard`
  - `/participant/predictions`
- Admin:
  - `/admin/dashboard`
  - `/admin/teams`
  - `/admin/match-games`
  - `/admin/users`
  - `/admin/users/{user}/edit`
  - `/admin/settings`

## Premios secretos

Los premios se configuran desde `/admin/settings` en la seccion `Premios secretos`.

- `Primer lugar`: nombre e imagen del premio.
- `Segundo lugar`: nombre e imagen del premio.
- `Tercer lugar`: nombre e imagen del premio.
- `Fecha de destape`: fecha desde la cual los participantes pueden ver los premios reales.

Antes del destape:

- El admin puede ver los premios reales en el ranking.
- Los participantes ven premios tapados en `/leaderboard` y `/participant/dashboard`.
- La imagen real no se renderiza para participantes antes del destape.

Desde el destape:

- Todos los usuarios pueden ver nombre e imagen de cada premio.

## Configuracion de WhatsApp (opcional)

La forma recomendada es configurar desde `/admin/settings`, en la seccion `Integraciones de notificaciones`.

- Link de invitacion al grupo: sirve para guardar o compartir el acceso al grupo.
- Webhook para mensajes automaticos: debe ser una URL externa que acepte solicitudes POST.

Un link de invitacion como `https://chat.whatsapp.com/...` no funciona como webhook.

Como respaldo, tambien puedes agregar en `.env`:

```env
WHATSAPP_GROUP_WEBHOOK_URL=https://tu-webhook
```

Si no esta configurado en settings ni en `.env`, el sistema envia solo correos.

## Nota

Para el historial tecnico detallado de todo lo realizado, revisar:

- `BITACORA_DESARROLLO.md`
