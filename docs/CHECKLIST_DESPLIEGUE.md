# Checklist de despliegue

## Antes de subir a Git

- Revisar `git status`.
- Confirmar que `.env` no se sube.
- Confirmar que no hay passwords o tokens en archivos versionados.
- Ejecutar pruebas o validaciones disponibles.
- Compilar frontend si se va a subir manualmente: `npm run build`.

## Antes de subir a Hostinger

- Hosting con PHP 8.3 o superior.
- Base de datos MySQL creada.
- Usuario y password MySQL disponibles.
- Dominio configurado.
- SMTP configurado si se usaran correos reales.
- `.env` de produccion preparado.

## Despues de subir

- Ejecutar `composer install --no-dev --optimize-autoloader`.
- Ejecutar `npm install` y `npm run build`, o subir `public/build` ya compilado.
- Ejecutar `php artisan key:generate` si `APP_KEY` esta vacio.
- Ejecutar `php artisan migrate --force`.
- Ejecutar `php artisan storage:link`.
- Ejecutar caches de produccion.
- Probar `/login`.
- Probar `/admin/dashboard`.
- Probar `/leaderboard`.
- Probar carga de logo y premios.
- Probar que una imagen subida se vea publicamente.

## Seguridad minima

- `APP_ENV=production`.
- `APP_DEBUG=false`.
- Password admin cambiada.
- Registro limitado por dominios o correos autorizados si aplica.
- Backups de base de datos habilitados.
- HTTPS activo.
