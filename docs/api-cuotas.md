# API de cuotas, facturas y pagos (para la app Expo)

Base: `http://TU-IP:8000/api/v1` — todos los pedidos llevan `Authorization: Bearer <token>` y `Accept: application/json`.
El token se obtiene con `POST /login` (`{ "email": "...", "password": "..." }`; la API pide el email del padre).
Los endpoints de cuotas son **solo para padres**: si el usuario no es padre devuelven 403, y si el alumno/factura no es de un hijo suyo devuelven 404.

| Método | Ruta | Para qué sirve |
|---|---|---|
| GET | `/hijos` | Lista de hijos (ya existía) |
| GET | `/hijos/{alumno}/facturas?desde=&hasta=` | Facturas del hijo en un rango de fechas (fecha de emisión) |
| GET | `/hijos/{alumno}/comprobantes?desde=&hasta=` | Comprobantes de pago emitidos + comprobantes de transferencia subidos |
| GET | `/hijos/{alumno}/deuda` | Deuda total, ítems pendientes e historial agrupado por ítem |
| GET | `/facturas/{id}` | Detalle de una factura con sus ítems y comprobantes |
| GET | `/facturas/{id}/pdf` | PDF de la factura |
| POST | `/facturas/{id}/comprobantes` | Sube el comprobante de transferencia (multipart: `archivo` jpg/png/pdf hasta 5 MB, `observacion` opcional). Una factura puede tener varios |
| POST | `/facturas/{id}/pagar` | Paga ítems elegidos: `{ "items": [1,2] }`. Exige que la factura tenga un comprobante subido (o mandar `archivo` en el mismo pedido). Devuelve el pago emitido |
| GET | `/pagos/{id}/pdf` | PDF del comprobante de pago que emite el sistema |
| GET | `/comprobantes/{id}/archivo` | Descarga un comprobante de transferencia subido |
| POST | `/cambiar-password` | `{ password_actual, password, password_confirmation }` |
| POST | `/logout` | Cierra sesión (ya existía) |

Estados de factura: `pendiente`, `parcial`, `pagada`. Cada factura trae `vencida` (true si pasó el día 20 y no está pagada).

## Comandos de Laravel
- `php artisan migrate` — crea las tablas nuevas.
- `php artisan facturas:generar 2026-11` — genera las facturas del periodo.
- `php artisan facturas:recordatorio --forzar` — mail de fin de mes (sin `--forzar` solo actúa el último día hábil).
- `php artisan facturas:deuda --forzar` — mail del día 20 con la deuda.
- `php artisan schedule:work` — deja corriendo el programador (corre ambos comandos todos los días a las 08:00).
Con `MAIL_MAILER=log` los mails se ven en `storage/logs/laravel.log`.

## Pantallas web del administrador
- `/admin/cuotas`: fija cuota, comedor, valor de cada deporte (y su profesor) y de cada recorrido; genera facturas.
- `/admin/reportes/pagos`: ingresos por período, pagos completos/incompletos, pagos por deporte (nivel, horario, profesor) y por recorrido, con filtros por fecha, año y alumno.
