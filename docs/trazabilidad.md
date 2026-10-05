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
| RF17 - comedor | `ComedorController.php`, modelos, migraciones y vista `comedor/` | `ComedorTest`: cupo, inscripción única y baja lógica | Cubierto |
| RF18 - transporte | `TransporteController.php`, modelos, migraciones y vista `transporte/` | `TransporteTest`: máximo de recorridos, cupo, inscripción única y baja lógica | Cubierto |
| RF19-RF20 - padre e hijos | `PadreHijoController.php`, relación `alumno_padre` y panel del padre | `PadreHijoTest`: vínculo administrativo y aislamiento entre familias | Cubierto parcialmente |
| RF21-RF23 - reportes | `ReporteController.php` y vista `reportes/` | `ReporteTest`: acceso exclusivo para administrador | Cubierto parcialmente |
| RF24 - API REST | `routes/api.php`, controladores `Api/` y Sanctum | `ApiTest`: token, perfil protegido y aislamiento de hijos | Cubierto parcialmente |
| RF25 - usuarios | `UsuarioController.php`, `ProfesorController.php`, `LoginController.php`, migraciones de cuentas y vistas `usuarios/` | `UsuarioTest`: alta por rol permitido, vínculo de cuentas, bloqueo de cuentas inactivas, validación de DNI y listado | Cubierto parcialmente |

## Regla de actualización

Cuando se agregue o cambie una funcionalidad, se debe actualizar esta matriz indicando archivo principal, caso de prueba y estado.
