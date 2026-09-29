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

## Próximo avance

Implementar inscripción académica y registrar en este archivo las decisiones, pruebas y resultados.
