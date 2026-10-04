# Matriz de trazabilidad

| Requisito | Módulo / archivo principal | Prueba / evidencia | Estado |
| --- | --- | --- | --- |
| RF08 - roles | `app/Http/Middleware/VerificarRol.php` | `AcademicConfigurationTest::test_admin_dashboard_requires_an_authenticated_administrator` | Cubierto |
| RNF - seguridad de acceso | `LoginController.php`, `RegisterController.php`, rutas y layout | `SecurityTest`: cierre de sesión, contraseña y límite de intentos | Cubierto |
| RF01-RF04 - alumnos | `AlumnoController.php` y vistas `alumnos/` | Pruebas pendientes | Parcial |
| RF05-RF07 - profesores | `ProfesorController.php` y asignaciones | Pruebas pendientes | Parcial |
| RF09-RF11 - configuración | Controladores de niveles, cursos y materias | Pruebas pendientes | Parcial |
| RF07/RF11 - asignaciones | `AsignacionAcademicaController.php` | Prueba de duplicado y baja lógica | Cubierto parcialmente |
| RF12 - horarios | `HorarioClaseController.php` | Prueba de superposición para curso o profesor | Cubierto parcialmente |
| RF13 - inscripción académica | `InscripcionAcademicaController.php`, modelo, migración y vistas `inscripciones/` | `AcademicEnrollmentTest`: alta única, edición y baja lógica | Cubierto |
| RF14-RF15 - deportes | Modelos y controladores de deportes, horarios e inscripciones deportivas | `SportsEnrollmentTest`: límite de dos deportes y conflicto horario | Cubierto |
| RF19-RF20 - padre e hijos | Pendiente | Pendiente | No iniciado |
| RF24 - API REST | Pendiente | Pendiente | No iniciado |

## Regla de actualización

Cuando se agregue o cambie una funcionalidad, se debe actualizar esta matriz indicando archivo principal, caso de prueba y estado.
