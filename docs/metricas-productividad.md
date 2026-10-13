# Métricas de productividad — Parte 2

## Herramientas utilizadas

- **GitHub Actions:** ejecución y resultado del pipeline de Integración Continua.
- **PHPUnit / Laravel:** ejecución de pruebas automatizadas.
- **GitHub:** historial de commits, Pull Requests y artefactos de cada ejecución.

## Resultado obtenido

| Métrica | Resultado | Fuente |
| --- | --- | --- |
| Estado del pipeline | Exitoso | Workflow `Pruebas de Laravel`, ejecución #9. |
| Ejecución de pruebas automatizadas | Exitosa | Paso `Ejecutar pruebas automatizadas` de GitHub Actions. |
| Reporte de pruebas | Disponible | Artefacto `reporte-pruebas-phpunit`. |
| Retención del reporte | 14 días | Configuración del workflow. |
| Tamaño del artefacto | 2.173 bytes | GitHub Actions. |
| Tiempo de ciclo del PR #11 | 11 min 35 s | Desde el commit de la rama hasta el merge en `master`. |
| Éxito de pipelines de la muestra | 100 % (1 de 1) | Ejecución asociada al PR #11. |

## Cuellos de botella y oportunidades de mejora

| Hallazgo | Impacto | Estrategia de mejora |
| --- | --- | --- |
| Antes del PR #11, los resultados de pruebas no quedaban conservados. | Dificultaba revisar una ejecución anterior o adjuntar evidencia. | Conservar el reporte JUnit como artefacto de GitHub Actions. |
| La organización de tareas no contaba con un tablero centralizado. | Menor visibilidad sobre prioridades, responsables y bloqueos. | Aplicar el tablero Kanban documentado y migrarlo a GitHub Projects. |
| La aprobación y el merge continúan siendo manuales. | El tiempo de entrega depende de la disponibilidad de un revisor. | Definir responsables de revisión y reglas de protección de rama. |

## Tabla comparativa

| Aspecto | Antes de la mejora | Después de la mejora |
| --- | --- | --- |
| Validación de pruebas | El pipeline ejecutaba pruebas, sin reporte conservado. | El pipeline ejecuta pruebas y genera un reporte JUnit. |
| Trazabilidad | Solo se podía consultar el estado general de la ejecución. | El reporte puede descargarse y revisarse durante 14 días. |
| Detección de problemas | La falla quedaba visible en el workflow. | La falla queda visible junto con un reporte para su análisis. |
| Seguimiento del trabajo | Ramas y Pull Requests sin tablero explícito. | Flujo Kanban definido con estados y criterio de finalización. |

## Conclusión

La mejora aplicada incrementa la trazabilidad de las pruebas y facilita el análisis de fallos. El próximo paso de mejora es implementar el tablero en GitHub Projects y utilizar sus datos para medir el tiempo que cada tarea permanece en cada estado.
