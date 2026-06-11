# Bitacora de desarrollo - Polla Mundialista Empresarial 2026

Fecha de inicio: 2026-06-01  
Proyecto: Laravel + MySQL + Blade + Tailwind + Breeze

---

## 1) Arranque del proyecto

- Se creo el proyecto Laravel en `polla-mundialista`.
- Se resolvio compatibilidad de versiones de PHP/Composer en Windows.
- Se instalo y configuro PHP 8.3 para Composer/Laravel.
- Se habilitaron extensiones necesarias: `openssl`, `curl`, `mbstring`, `pdo_mysql`, `zip`, `fileinfo`.
- Se ajustaron permisos de carpetas `bootstrap/cache` y `storage`.

---

## 2) Instalacion base de Laravel

- Instalacion completa de Laravel 13.
- Configuracion de `.env` con MySQL.
- Ejecucion de migraciones base (`users`, `cache`, `jobs`).

---

## 3) Autenticacion y frontend base

- Instalado `laravel/breeze` (Blade).
- Instaladas dependencias Node.
- Compilacion frontend con Vite.

---

## 4) Base de datos funcional (modelo del negocio)

Se crearon/ajustaron migraciones para:

- `users`
  - `name`, `email`, `password`, `department`, `role`, `status`, `remember_token`, timestamps.
- `teams`
  - `name`, `code`, `flag_url`, `group_name`, `status`, timestamps.
- `match_games`
  - `phase`, `group_name`, `home_team_id`, `away_team_id`, `match_date`, `home_score`, `away_score`, `status`, `prediction_deadline`, timestamps.
- `predictions`
  - `user_id`, `match_game_id`, marcador pronosticado, `points`, `is_exact_score`, `is_correct_result`, timestamps.
- `bonus_predictions`
  - `user_id` (unico), `champion_team_id`, `runner_up_team_id`, `top_scorer`, `points`, timestamps.
- `leaderboard_snapshots`
  - `user_id`, `total_points`, `exact_scores`, `correct_results`, `calculated_at`, timestamps.

Reglas clave implementadas:

- Foreign keys en relaciones principales.
- `cascadeOnDelete` donde aplica.
- Indices en columnas relevantes.
- Restriccion anti duplicado en `predictions` por (`user_id`, `match_game_id`).

---

## 5) Modelos Eloquent

Modelos creados/ajustados:

- `User`
- `Team`
- `MatchGame`
- `Prediction`
- `BonusPrediction`
- `LeaderboardSnapshot`

Incluye:

- `fillable`
- `casts`
- relaciones completas
- scopes:
  - `MatchGame::scheduled()`
  - `MatchGame::finished()`
  - `MatchGame::openForPrediction()`
  - `Prediction::withPoints()`
- metodos auxiliares:
  - `MatchGame->isPredictionOpen()`
  - `MatchGame->getFinalResult()`
  - `Prediction->getPredictedResult()`

---

## 6) Servicio de puntajes

Archivo: `app/Services/ScoringService.php`

Metodos implementados:

- `calculatePredictionPoints(Prediction $prediction): array`
- `recalculateMatchPredictions(MatchGame $matchGame): void`
- `recalculateUserTotal(User $user): int`

Reglas aplicadas:

- Marcador exacto: 5
- Ganador/empate: 3
- Goles exactos local: +1
- Goles exactos visitante: +1
- Sin acierto: 0

---

## 7) Controladores, middleware, rutas y requests

### Middleware

- `RoleMiddleware` (`role:admin`, `role:participant`) registrado en `bootstrap/app.php`.

### Form Requests

- `StoreTeamRequest`, `UpdateTeamRequest`
- `StoreMatchGameRequest`, `UpdateMatchGameRequest`, `UpdateMatchResultRequest`
- `UpsertPredictionRequest`
- `UpdateSettingsRequest`

### Controladores principales

- `Admin\DashboardController`
- `Admin\TeamController`
- `Admin\MatchGameController`
- `Admin\UserController`
- `Admin\SettingsController`
- `Participant\DashboardController`
- `Participant\PredictionController`
- `LeaderboardController`

### Rutas

- Rutas publicas, auth, participante y admin organizadas con nombre y middleware.

---

## 8) Diseno de vistas (Blade + Tailwind)

Se implemento estilo oscuro deportivo, responsive y componentes reutilizables.

### Layouts

- `layouts/app.blade.php`
- `layouts/guest.blade.php`
- `layouts/admin.blade.php`
- `layouts/navigation.blade.php`

### Componentes

- `card`
- `button`
- `badge`
- `table`
- `match-card`
- `ranking-row`
- `ranking-table`

### Vistas

- `welcome`
- `auth/login`
- `auth/register`
- `participant/dashboard`
- `participant/predictions/index`
- `participant/predictions/form`
- `participant/matches/index`
- `leaderboard/index`
- `admin/dashboard`
- `admin/teams/*`
- `admin/match-games/*`
- `admin/users/index`
- `admin/settings/edit`

---

## 9) Carga inicial de datos FIFA 2026

Se creo comando:

- `app:sync-fifa2026-data`

Archivo:

- `app/Console/Commands/SyncFifa2026Data.php`

Funcion:

- Consume feed FIFA (competition `17`, season `285023`).
- Inserta/actualiza equipos con grupo y bandera.
- Inserta/actualiza primeros partidos (fase de grupos) con fecha y limite.

Resultado de la carga ejecutada:

- 48 equipos.
- 72 partidos.

---

## 10) Modulo de configuracion y branding

Se creo tabla y modelo:

- `app_settings` (`key`, `value`).
- `AppSetting` con helpers (`getValue`, `setValue`, `getByKeys`).

Permite desde admin:

- subir logo de empresa
- editar nombre empresa
- editar titulo/subtitulo de landing
- editar correo de soporte

El branding dinamico se refleja en:

- landing
- guest layout
- navbar

---

## 11) Notificaciones de partidos del dia

Servicio:

- `app/Services/MatchNotificationService.php`

Incluye:

- envio por correo a participantes activos
- envio opcional a WhatsApp via webhook

Config:

- `config/services.php`
  - `whatsapp.group_webhook_url`

Ruta/accion admin:

- `admin.match-games.notify-today`

---

## 12) Zona horaria Colombia (Bogota)

Se ajusto timezone para evitar cierre incorrecto de pronosticos:

- `config/app.php` -> `timezone = env('APP_TIMEZONE', 'America/Bogota')`
- `.env` -> `APP_TIMEZONE=America/Bogota`

Ademas se limpio cache de configuracion.

---

## 13) Ajustes de experiencia participante

- Priorizacion de partidos de hoy y proximos 4 dias.
- Mejora de reglas para visibilidad y edicion de pronosticos.
- Fallback de apertura por `match_date` cuando no exista `prediction_deadline`.

---

## 14) Usuarios de prueba creados

- Admin:
  - `admin@empresa.com`
  - `Admin12345`
- Participante:
  - `participante@empresa.com`
  - `Demo12345`

> Recomendacion: cambiar contrasenas en ambiente real.

---

## 15) Comandos utiles

- Levantar app:
  - `php artisan serve`
  - `npm run dev`
- Recompilar vistas:
  - `php artisan view:clear`
  - `php artisan view:cache`
- Sincronizar FIFA:
  - `php artisan app:sync-fifa2026-data`
- Limpiar caches:
  - `php artisan optimize:clear`

---

## 16) Estado actual

El sistema ya cuenta con:

- autenticacion
- roles admin/participante
- gestion de equipos/partidos
- predicciones
- calculo de puntos
- ranking
- configuracion de marca
- notificaciones de partidos del dia
- carga inicial de equipos, grupos y primeros partidos desde FIFA
