# Despliegue en Hostinger

Esta guia cubre el despliegue de la Polla Mundialista Empresarial 2026 en Hostinger.

## Requisitos del hosting

- PHP 8.3 o superior.
- MySQL/MariaDB.
- Composer disponible por SSH o posibilidad de subir `vendor/` generado localmente.
- Node.js disponible por SSH o posibilidad de subir `public/build` generado localmente.
- Acceso para configurar el document root hacia la carpeta `public/`.

Si el plan no permite cambiar el document root, usar la alternativa de estructura manual descrita abajo.

## Archivos importantes

- Entrada publica Laravel: `public/index.php`.
- Variables de entorno: `.env`.
- Dependencias PHP: `vendor/`.
- Assets compilados: `public/build/`.
- Archivos publicos subidos: `storage/app/public` expuesto por `public/storage`.

## Opcion A: despliegue con SSH y Git

Esta es la opcion recomendada.

1. Entrar por SSH al servidor.

```bash
ssh usuario@servidor
```

2. Clonar el repositorio fuera de `public_html` si Hostinger lo permite.

```bash
git clone https://github.com/usuario/polla-mundialista.git polla-mundialista
cd polla-mundialista
```

3. Instalar dependencias PHP.

```bash
composer install --no-dev --optimize-autoloader
```

4. Instalar dependencias frontend y compilar assets.

```bash
npm install
npm run build
```

Si Node.js no esta disponible en Hostinger, ejecutar `npm install` y `npm run build` localmente y subir la carpeta `public/build`.

5. Crear `.env` de produccion.

```bash
cp .env.example .env
```

Editar `.env` con los datos reales. Ver `docs/ENV.md`.

6. Generar clave de aplicacion.

```bash
php artisan key:generate
```

7. Ejecutar migraciones.

```bash
php artisan migrate --force
```

8. Crear enlace publico de storage.

```bash
php artisan storage:link
```

9. Optimizar Laravel.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

10. Configurar document root.

El dominio debe apuntar a:

```text
/ruta/del/proyecto/polla-mundialista/public
```

## Opcion B: carga manual si no hay SSH completo

Usar esta opcion si Hostinger no permite ejecutar Composer o Node.

1. En local, preparar proyecto:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

2. Subir por FTP/SFTP todo el proyecto excepto:

- `.env` local.
- `node_modules/`.
- `.git/`.
- archivos temporales o backups.

3. En Hostinger, crear un `.env` nuevo de produccion.

4. Si no puedes cambiar document root a `public/`, usar esta estructura:

```text
/home/usuario/polla-mundialista       Proyecto Laravel completo
/home/usuario/public_html             Contenido de la carpeta public
```

Luego editar `public_html/index.php` para apuntar al proyecto real:

```php
require __DIR__.'/../polla-mundialista/vendor/autoload.php';
$app = require_once __DIR__.'/../polla-mundialista/bootstrap/app.php';
```

Tambien copiar el contenido de `public/` hacia `public_html/`, incluyendo `build/`.

## Configuracion de base de datos

En Hostinger crear una base MySQL desde el panel. Guardar:

- Nombre de base de datos.
- Usuario.
- Password.
- Host MySQL.

Configurar en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_db
DB_USERNAME=usuario_db
DB_PASSWORD=password_db
```

En algunos planes el host no es `localhost`; usar el valor indicado por Hostinger.

## Permisos

Laravel necesita escritura en:

- `storage/`
- `bootstrap/cache/`

Si hay errores de permisos, ajustar desde SSH o el administrador de archivos.

## Tareas despues del despliegue

Ejecutar:

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Crear un usuario admin si no existe. Puede hacerse con Tinker si el servidor lo permite:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@empresa.com',
    'password' => 'Admin12345',
    'role' => 'admin',
    'status' => 'active',
]);
```

Cambiar la contrasena despues del primer ingreso.

## Actualizar despliegue desde Git

Cuando haya nuevos cambios:

```bash
git pull
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si no hay cambios frontend, `npm install` y `npm run build` pueden omitirse.

## Problemas comunes

- Error 500: revisar `.env`, permisos, logs en `storage/logs/laravel.log`.
- Assets sin estilos: falta `public/build` o no se ejecuto `npm run build`.
- Imagenes no cargan: falta `php artisan storage:link` o `public/storage`.
- APP_KEY vacia: ejecutar `php artisan key:generate`.
- Migraciones fallan: revisar datos MySQL y version PHP.
