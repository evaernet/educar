# Tablero Kanban — Proyecto Educar

Este tablero organiza el trabajo del equipo en cinco estados. Cada tarea debe avanzar de izquierda a derecha; una tarea no puede pasar a **Finalizado** sin pruebas y revisión.

| Pendiente | En progreso | En revisión | En pruebas | Finalizado |
| --- | --- | --- | --- | --- |
| Configurar un tablero visual en GitHub Projects. | — | — | — | Integración del módulo de cuotas, facturas, pagos y reportes. |
| Registrar nuevos Issues para funcionalidades y defectos. | — | — | — | PR #11: reporte JUnit como artefacto de CI. |
| Definir responsables y fechas para cada Issue. | — | — | — | Migraciones del módulo de cuotas aplicadas y validadas. |

## Flujo de trabajo

1. Registrar la tarea o incidencia en **Pendiente**.
2. Asignarla a un integrante y moverla a **En progreso**.
3. Abrir un Pull Request y moverla a **En revisión**.
4. Ejecutar pruebas automáticas y manuales; moverla a **En pruebas**.
5. Integrar el Pull Request aprobado y moverla a **Finalizado**.

## Criterio de finalización

Una tarea se considera finalizada cuando cuenta con implementación, pruebas exitosas, revisión mediante Pull Request, integración a `master` y documentación actualizada cuando corresponda.
