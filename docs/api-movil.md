# API móvil — versión 1

Base local: `http://127.0.0.1:8000/api/v1`

La API usa tokens Bearer de Laravel Sanctum. Todas las respuestas son JSON.

## Iniciar sesión

`POST /login`

```json
{
  "email": "padre.prueba@educar.com",
  "password": "Padre1234"
}
```

La respuesta incluye `token`, `token_type` y los datos básicos del usuario. El token debe enviarse en las siguientes consultas:

```text
Authorization: Bearer {token}
```

## Consultar perfil

`GET /me`

Requiere token. Para un padre devuelve únicamente los hijos vinculados a su cuenta. Para un alumno devuelve su ficha básica. Para los demás roles devuelve la identidad y el rol autenticados.

## Consultar hijos vinculados

`GET /hijos`

Requiere token de una cuenta con rol `padre`. Devuelve solamente los alumnos vinculados a ese padre o tutor. Otros roles reciben una respuesta `403`.

## Consultar detalle de un hijo

`GET /hijos/{id}`

Requiere token de una cuenta con rol `padre`. El identificador debe pertenecer a un alumno vinculado; de lo contrario la API responde `404` sin exponer información ajena.

La respuesta incluye datos básicos del alumno y los servicios disponibles: inscripción académica, horarios de clase, deportes, comedor y transporte. Los servicios aún no asignados se devuelven vacíos o como `null`.

## Cerrar sesión

`POST /logout`

Requiere token. Invalida solamente el token utilizado en la solicitud.

## Seguridad

- Las contraseñas no se exponen en las respuestas.
- Un token es necesario para todo endpoint protegido.
- La información de hijos está limitada por el vínculo familiar registrado.
- Las cuentas inactivas no pueden obtener tokens de acceso.
- Los endpoints de hijos no exponen información de alumnos no vinculados al padre autenticado.
- La API no permite altas, bajas ni modificaciones en esta primera versión.
