# Estándares de desarrollo

Estas reglas se aplican a toda mejora futura del proyecto web y sirven como referencia del equipo.

## Código y arquitectura

- Mantener controladores breves; extraer reglas complejas a clases o servicios cuando el módulo crezca.
- Validar siempre los datos en el servidor. Las validaciones del formulario son complementarias.
- Usar relaciones, claves foráneas, índices y restricciones de base de datos para proteger reglas críticas.
- Evitar duplicar lógica y reutilizar componentes, vistas parciales y el layout compartido.

## Seguridad

- Proteger rutas mediante autenticación y autorización por rol.
- Usar `POST`, token CSRF y confirmación para acciones que cambian datos.
- No almacenar secretos ni datos sensibles en el repositorio.
- Mantener límites de intentos y políticas de contraseña para acceso y registro.

## Pruebas y calidad

- Agregar o actualizar una prueba automatizada por cada regla de negocio crítica.
- Ejecutar la suite de pruebas, la compilación de vistas y la revisión visual antes de guardar un cambio.
- Mantener migrations reversibles y probarlas en la base local.

## Diseño

- Seguir la [identidad visual](diseno/identidad-visual.md) aprobada.
- Presentar enlaces de acción como botones claros; no como texto aislado.
- Usar el layout `resources/views/layouts/app.blade.php` y mantener diseño responsive y accesible.

## Versionado y documentación

- Usar commits pequeños y descriptivos por módulo o cambio coherente.
- Subir los cambios probados a la rama de trabajo y actualizar el pull request.
- Actualizar requerimientos, trazabilidad, historial de avances y bitácora de IA cuando corresponda.
