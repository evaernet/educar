# Historial de avances

## 29 de septiembre de 2026

- Se revisó la estructura del proyecto Laravel y se verificó la conexión con MySQL.
- Se identificó el estado de las migraciones y los módulos académicos existentes.
- Se completó el módulo de asignaciones académicas con edición y baja lógica mediante el campo `activo`.
- Se agregó validación de superposición de horarios para el mismo profesor o curso.
- Se aplicó la migración `2026_09_29_120000_add_activo_to_asignacion_academicas_table`.
- Se restauraron dependencias de desarrollo y se creó la estructura base de PHPUnit.
- Se agregó `tests/Feature/AcademicConfigurationTest.php`.
- Se ejecutaron 3 pruebas automatizadas con 13 aserciones aprobadas usando SQLite en memoria.
- Se protegió el cierre de sesión mediante POST, autenticación y token CSRF.
- Se incorporaron límites de intentos para inicio de sesión y registro, y una política reforzada para contraseñas nuevas.
- Se agregó la suite `SecurityTest` para validar estos controles.
- Se ejecutaron 6 pruebas automatizadas con 31 aserciones aprobadas.
- Se implementó la inscripción académica de alumnos por ciclo lectivo y curso, con edición y baja lógica.
- La base de datos impide que un alumno tenga más de una inscripción en el mismo ciclo.
- Se aplicó la migración `2026_09_29_130000_create_inscripcion_academicas_table`.
- Se agregaron pruebas del módulo; la suite completa alcanzó 8 tests y 42 aserciones aprobadas.
- Se implementó la gestión de deportes, horarios deportivos e inscripciones de alumnos.
- Se limita a dos deportes activos por alumno y se bloquean horarios deportivos superpuestos.
- Se aplicaron las migraciones de deportes y se agregaron pruebas; la suite completa alcanzó 10 tests y 52 aserciones aprobadas.
- Se implementó el módulo de comedor con turnos, cupos e inscripción única por alumno.
- Se aplicaron las migraciones de comedor y se incorporó su acceso al panel de administración.
- Se realizó la prueba manual de comedor en el entorno local: alta de turno, inscripción y baja lógica aprobadas.
- Se implementó el módulo de transporte con hasta cuatro recorridos, cupos e inscripción única por alumno.
- Se aplicaron las migraciones de transporte y se agregó el acceso desde el panel de administración.
- Se agregaron pruebas de cantidad máxima de recorridos, inscripción única y cupo; la suite completa alcanzó 16 pruebas y 82 aserciones aprobadas.
- Se realizó la prueba manual de transporte en el entorno local con resultado aprobado.
- Se implementó el vínculo entre padres e hijos y el panel familiar con consulta de curso, deportes, comedor, transporte y horarios.
- Se incorporaron pruebas que impiden que un padre vea alumnos de otra familia; la suite alcanzó 18 pruebas y 88 aserciones aprobadas.
- Se realizó la prueba manual del panel de padre con resultado aprobado.
- Se implementó el panel de reportes con resúmenes académicos, deportivos, comedor y transporte.
- Se validó el acceso exclusivo para administradores; la suite alcanzó 19 pruebas y 94 aserciones aprobadas.
- Se realizó la prueba manual de reportes con resultado aprobado.
- Se incorporó la API REST versión 1 con autenticación Sanctum, perfil y cierre de sesión por token.
- Se verificó la restricción de hijos para el rol padre y se documentaron los endpoints para móvil.
- Se realizó la prueba manual de inicio de sesión, perfil y cierre de sesión; la suite alcanzó 21 pruebas y 100 aserciones aprobadas.
- Se implementó la administración de usuarios: altas de administradores y padres/tutores, listado de cuentas y activación o desactivación segura.
- Las cuentas históricas de alumnos y profesores fueron vinculadas a sus fichas mediante migraciones; las nuevas altas desde los módulos académicos generan su cuenta automáticamente.
- Se restringió el alta de alumnos y profesores a sus módulos específicos para evitar cuentas sin ficha académica o laboral.
- Se unificó la validación de DNI: solo siete u ocho dígitos sin puntos ni guiones, con mensajes de validación en español para todo el sistema.
- Se realizó la prueba manual del módulo de usuarios y la suite alcanzó 28 pruebas automatizadas con 123 aserciones aprobadas.
- Se amplió la API REST para que los padres consulten exclusivamente sus hijos vinculados y el detalle de su información académica, horarios, deportes, comedor y transporte.
- Se bloqueó la emisión de tokens para cuentas inactivas y se agregaron respuestas JSON claras para accesos no autorizados.
- Se realizó la prueba manual local de API con una cuenta de padre, consulta de hija vinculada y cierre de sesión aprobados.

## Próximo avance

Crear la interfaz inicial de la aplicación móvil para padres, consumiendo los endpoints de autenticación, hijos y detalle de servicios.
