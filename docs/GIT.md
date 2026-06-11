# Guia para subir el proyecto a Git

Esta guia deja el proyecto listo para subirlo a GitHub, GitLab o Bitbucket.

## Requisitos

- Git instalado.
- Cuenta en GitHub, GitLab o Bitbucket.
- No subir archivos sensibles como `.env`, claves, backups o credenciales.

## Archivos que no se deben subir

El archivo `.gitignore` ya excluye:

- `.env`
- `.env.production`
- `vendor/`
- `node_modules/`
- `public/build/`
- `public/storage`
- logs y cache local

Antes de hacer commit revisa que no haya credenciales dentro de archivos versionados.

## Inicializar repositorio local

Ejecutar desde la raiz del proyecto:

```bash
git init
git status
git add .
git commit -m "Initial commit"
```

## Crear repositorio remoto

Crear un repositorio vacio en GitHub/GitLab/Bitbucket. Luego vincularlo:

```bash
git remote add origin https://github.com/usuario/polla-mundialista.git
git branch -M main
git push -u origin main
```

## Flujo recomendado de trabajo

Antes de hacer cambios:

```bash
git status
git pull
```

Despues de hacer cambios:

```bash
git status
git add .
git commit -m "Describe el cambio"
git push
```

## Checklist antes de subir

- `.env` no aparece en `git status`.
- No hay claves, passwords ni tokens en archivos versionados.
- `composer.lock` si esta versionado.
- `package-lock.json` si existe debe estar versionado.
- Las imagenes subidas por usuarios o premios no se versionan; quedan en `storage/app/public`.
