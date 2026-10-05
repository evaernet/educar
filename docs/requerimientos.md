# Requerimientos del sistema

## Alcance actual

El sistema administra información académica, administrativa y extracurricular del Centro Educativo Educar Para Transformar. La aplicación Laravel es el núcleo compartido para la administración web y, a futuro, para el sitio institucional y la aplicación móvil.

## Requerimientos funcionales priorizados

| ID | Requerimiento | Estado |
| --- | --- | --- |
| RF01-RF04 | Registrar, editar, dar de baja y buscar alumnos. | Implementado parcialmente |
| RF05-RF07 | Gestionar profesores y sus asignaciones académicas. | Implementado parcialmente |
| RF08 | Controlar acceso por rol. | Implementado |
| RF09-RF12 | Gestionar niveles, cursos, materias, ciclos, asignaciones y horarios. | Implementado parcialmente |
| RF13 | Inscribir alumno a un único curso por ciclo. | Implementado |
| RF14-RF15 | Gestionar hasta dos deportes y evitar conflictos de horarios. | Implementado |
| RF17 | Gestionar comedor: turnos, cupos e inscripciones. | Implementado parcialmente |
| RF18 | Gestionar transporte: recorridos, cupos e inscripciones. | Implementado parcialmente |
| RF19-RF20 | Vincular padres con hijos y permitirles consultar sus datos y servicios. | Implementado parcialmente |
| RF21-RF23 | Generar reportes académicos, deportivos y de servicios. | Implementado parcialmente |
| RF24 | Exponer API REST para aplicación móvil. | Implementado parcialmente |
| RF25 | Administrar usuarios del sistema. | Implementado parcialmente |

## Requerimientos no funcionales

- Interfaz simple, responsive y apta para escritorio y móvil.
- Autenticación con contraseñas cifradas, sesiones, autorización por rol y cierre de sesión protegido por CSRF.
- Prevención básica de fuerza bruta: límite de cinco intentos de acceso por minuto y tres registros por IP por minuto.
- Las nuevas contraseñas deben tener al menos ocho caracteres, mayúsculas, minúsculas y un número.
- Respuesta normal de interfaz en menos de tres segundos.
- Arquitectura MVC, código mantenible y documentación interna.
- Diseño extensible para incorporar deportes, pagos, servicios, API y aplicación móvil.

## Reglas de negocio

- Un alumno pertenece a un único curso por ciclo lectivo.
- Un curso pertenece a un único nivel educativo.
- Una materia puede dictarse en varios cursos y con profesores diferentes.
- Una asignación académica no puede repetirse para el mismo ciclo, curso y materia.
- Un horario no puede superponerse, el mismo día, para el mismo profesor ni para el mismo curso.
- Un alumno puede tener hasta dos deportes simultáneos; no deben superponerse sus horarios.
- Un padre solo puede consultar y gestionar a sus hijos vinculados.
- El transporte tiene cuatro recorridos y cada alumno solo puede inscribirse a uno.
- Las inscripciones no pueden duplicarse.
- Un alumno solo puede tener una inscripción de comedor y el turno no puede superar su cupo.
- El transporte admite hasta cuatro recorridos; un alumno solo puede tener una inscripción y el recorrido no puede superar su cupo.
- Un padre solo puede consultar la información de los alumnos que tenga vinculados.
- Las cuentas de alumno se crean exclusivamente al registrar su ficha académica; las cuentas de profesor se crean al registrar su ficha laboral.
- El panel de usuarios administra altas de administradores y padres/tutores, además de activar o desactivar cuentas existentes.
- Una cuenta inactiva no puede iniciar sesión y un administrador no puede desactivar su propia cuenta.
- El DNI de alumnos y profesores debe tener siete u ocho dígitos, sin puntos ni guiones.

## Próximo requisito a implementar

**Aplicación móvil:** ampliar los endpoints de la API REST para que la futura aplicación móvil pueda consultar los servicios asociados a cada alumno.
