# Guia de operacion

Esta guia resume como administrar la plataforma una vez desplegada.

## Acceso admin

Entrar por `/login` con un usuario cuyo campo `role` sea `admin`.

## Panel admin

Rutas principales:

- `/admin/dashboard`: resumen administrativo.
- `/admin/teams`: gestion de equipos.
- `/admin/match-games`: gestion de partidos y resultados.
- `/admin/users`: gestion de participantes.
- `/admin/settings`: configuracion general, registros autorizados y premios.

## Participantes

Desde `/admin/users` el admin puede:

- Activar o desactivar participantes.
- Editar nombre, correo, celular, area y estado.
- Cambiar contrasena.
- Eliminar participantes.

Al eliminar un participante tambien se eliminan sus predicciones por las reglas de la base de datos.

## Premios secretos

Desde `/admin/settings` se configuran:

- Premio primer lugar.
- Imagen primer lugar.
- Premio segundo lugar.
- Imagen segundo lugar.
- Premio tercer lugar.
- Imagen tercer lugar.
- Fecha de destape.

Antes del destape:

- El admin puede ver los premios reales.
- Los participantes ven premios tapados.
- Las imagenes reales no se renderizan para participantes.

Desde el destape:

- Todos ven nombres e imagenes reales.

Si no se configura fecha de destape, el sistema usa la fecha del partido cuya fase sea `Final`.

## Ranking

Ruta: `/leaderboard`.

El ranking ordena por:

- Total de puntos.
- Marcadores exactos.
- Resultados acertados.
- Nombre.

## Pronosticos

Los participantes usan:

- `/participant/dashboard`
- `/participant/predictions`

Solo pueden pronosticar partidos abiertos segun fecha limite o fecha del partido.

## Notificaciones

Desde el panel de partidos se puede notificar partidos del dia.

El webhook de WhatsApp se configura desde `/admin/settings`, en la seccion `Integraciones de notificaciones`.

El link de invitacion al grupo tambien se guarda desde `/admin/settings`, pero solo sirve para compartir acceso al grupo.

Si el webhook esta configurado, se envia al grupo por WhatsApp mediante webhook. Si queda vacio, las notificaciones se envian solo por correo.

Un link como `https://chat.whatsapp.com/...` no es un webhook y no permite enviar mensajes automaticos.

`WHATSAPP_GROUP_WEBHOOK_URL` en `.env` queda como respaldo opcional si no hay valor guardado en settings.

## Mantenimiento rapido

Limpiar y regenerar cache:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Revisar logs:

```bash
storage/logs/laravel.log
```
