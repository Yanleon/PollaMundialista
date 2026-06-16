# ⚽ Polla Mundialista Empresarial 2026

Aplicacion Laravel para gestionar una polla mundialista interna: participantes, pronosticos, puntajes, ranking, panel administrativo, notificaciones y premios secretos para el top 3.

## Stack

- Laravel 13
- PHP 8.3+
- MySQL/MariaDB
- Blade + Tailwind CSS
- Laravel Breeze
- Vite

## Documentacion

- Resumen del proyecto: `README_PROYECTO.md`
- Subir a Git: `docs/GIT.md`
- Despliegue en Hostinger: `docs/HOSTINGER.md`
- Variables `.env`: `docs/ENV.md`
- Operacion del sistema: `docs/OPERACION.md`
- Checklist de despliegue: `docs/CHECKLIST_DESPLIEGUE.md`

## Instalacion local rapida

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

## Produccion

Para desplegar en Hostinger, seguir `docs/HOSTINGER.md` y usar el checklist `docs/CHECKLIST_DESPLIEGUE.md`.

## Nota de seguridad

No subir `.env`, credenciales, backups, tokens ni archivos privados al repositorio.
