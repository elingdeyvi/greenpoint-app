# Análisis de integración – Requerimientos vs proyecto

Estado del proyecto frente a los requisitos indicados.  
**Leyenda:** ✅ Integrado | ⚠ Parcial / Ajuste menor | ❌ No integrado

---

## 1. Información general y sucursales

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| 2 sucursales: Villahermosa, Macuspana | ✅ | Tabla `sucursales`, `SucursalSeeder` con ambos nombres. |
| Ventas registradas por sucursal | ✅ | `ventas.sucursal_id`, filtros y reportes por sucursal. |

---

## 2. Descripción por sucursal

### Villahermosa
| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Puede vender | ✅ | `VentaController@store` con cualquier sucursal. |
| Ticket con QR que contiene lista de pedido | ✅ | `generarQrPayload()` genera `uuid\|id\|cantidad\|unidad\|...` (separador **pipe**). |
| UUID del ticket en el QR | ✅ | Primer campo del payload es `uuid` (se genera en `creating`). |
| Formato “id|cantidad|unidad” por producto | ⚠ | Implementado con **pipe (\|)**. Requerimiento dice “separado por coma”: actualmente es pipe; si se exige coma habría que cambiar `implode('\|', ...)` por coma. |
| No requiere foto al crear venta | ✅ | No se pide foto en `store`. |

### Macuspana
| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Puede vender | ✅ | Misma acción `store`; catálogo reducido en front. |
| QR “normales” para entrega y salida de material | ✅ | Al crear venta en Macuspana no se genera QR de pedido; al importar desde Villahermosa se llama `generarQrPayload()` y se guarda QR para entrega/salida. |
| Escanear ticket de Villahermosa e importar pedido | ✅ | `VentaController@importarPedido`: recibe `qr_payload`, parsea, crea venta local con `ticket_origen_uuid`. |
| Generar ticket propio con productos vinculados por id | ✅ | Se crean `venta_detalles` con productos de Macuspana (por nombre). |
| Generar QR para entrega de material y salida | ✅ | Tras importar se llama `generarQrPayload()` en la venta de Macuspana. |

---

## 3. Inventario y unidades de medida

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Unidad principal M3 | ✅ | `productos.unidad_medida` default `'m3'`. |
| Control de inventario | ✅ | `productos.stock_actual`, `Producto::reducirStock()` en ventas/importar. |
| No vender sin inventario | ✅ | `reducirStock()` lanza excepción si `stock_actual < cantidad`. |
| Aviso cuando un material se agote | ❌ | No hay endpoint ni vista de “alertas de stock” (ej. lista de productos con stock &lt; umbral). |

---

## 4. Productos y materiales

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Macuspana: solo Polvo, Rezaga, Balustre | ✅ | Front filtra por `NOMBRES_MACUSPANA`; productos se crean por BD/seed. |
| Villahermosa: Polvo, Rezaga, Balustre, Tortuguero, Granzón, Revestimiento, Grava 3/4, Grava 1/2, Gravón 6", Finos, Piedra Braza, Roca Maya | ⚠ | Catálogo “completo” en front (todos los productos de la sucursal). **No hay seeder** que cree estos 12 materiales en Villahermosa; hay que crearlos en BD o agregar seeder. |

---

## 5. Clientes y ventas

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Sin catálogo de clientes (espontáneos) | ✅ | No existe modelo Cliente; ventas solo con usuario/sucursal. |
| Donativos | ✅ | `ventas.tipo` = `'venta'` \| `'donativo'`; selector en vista ventas. |
| Estatus: Pendiente, Entrega parcial, Entregado | ✅ | `ventas.estatus` enum `pendiente`, `parcial`, `entregado`; se actualiza en modelo `Entrega`. |
| Cancelaciones (calidad) | ❌ | No hay estatus `cancelado` en ventas ni permiso/endpoint para cancelar. |

---

## 6. Pagos y precios

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Pago en efectivo | ✅ | Sin modelo de “forma de pago”; se asume efectivo. |
| Solo administrador cambia precio | ⚠ | Permiso `precios.modificar` existe y es de Admin; **no hay endpoint** para modificar `productos.precio_unitario` protegido por ese permiso. |
| Sin IVA | ✅ | No hay campo IVA en ventas/productos. |
| Sin descuentos; donativo como tipo de venta | ✅ | Sin descuentos; donativo es `tipo = 'donativo'`. |

---

## 7. Tickets y comprobantes

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Ticket: empresa, folio, fecha/hora, unidad de medida, total | ⚠ | Datos en BD (folio, total, detalles con unidad). **No hay plantilla de impresión** de ticket con “nombre empresa”; empresa está en `configuracion_empresa`. |
| DONATIVOS y QR para entrega | ✅ | Tipo donativo; QR en venta (Villahermosa o Macuspana según flujo). |

---

## 8. Usuarios y permisos

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Administrador, Vigilante, Despachador, Gerente de Producción | ✅ | Roles en migración de roles. |
| Cancelar ventas (permiso especial) | ❌ | No existe permiso `ventas.cancelar` ni lógica de cancelación. |
| Modificar precios (admin) | ⚠ | Permiso `precios.modificar`; falta endpoint que lo use. |
| Ajustar inventario (Gerente) | ✅ | `InventarioController@ajustar` con `inventario.ajustar`. |
| Ver reportes completos (Gerente) | ✅ | Permiso `reportes.ver` asignado a Gerente. |

---

## 9. Reglas de negocio y hardware

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Cámara IP, foto opcional antes del ticket; Villahermosa sin cámara | ✅ | Ventas no piden foto; BoletoController (salidas) tiene captura opcional; en ventas no se usa cámara. |
| Impresora: IP y nombre desde Laravel, envío por red | ✅ | `ImpresionController`: listar/crear/actualizar impresoras (IP, nombre), `imprimirRaw` por socket. |
| No vender sin inventario | ✅ | Validado en `reducirStock()`. |
| QR válido una sola vez | ⚠ | No hay lógica “QR de entrega usado una vez”; se puede escanear y registrar varias entregas parciales hasta completar. Si se requiere “un solo uso” habría que definir si es por ticket o por QR y añadir estado. |
| Entregas parciales: cantidad restante editable, restando hasta completar | ✅ | Dashboard entregas: barra de progreso, “cantidad a entregar ahora”, restante calculado; modelo actualiza `cantidad_entregada` y estatus. |
| Foto en entrega: opcional por entrega parcial | ✅ | `EntregaController`: foto opcional; etiqueta en front como “opcional por entrega”. |

---

## 10. Caja y gastos

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Apertura de caja con fondo inicial | ✅ | `CajaController@apertura` con `monto_inicial`. |
| Corte de caja X y Z detallado | ✅ | `CajaController@corte` devuelve reporte_x y reporte_z (totales, fechas, diferencia). |
| Registro de gastos | ⚠ | Tabla `gastos` y relación en `Caja`; corte incluye `total_gastos`. **No hay API ni vista** para alta de gastos (ej. `POST /api/cajas/{caja}/gastos`). |

---

## 11. Reportes

| Requerimiento | Estado | Detalle |
|---------------|--------|---------|
| Gráficas y exportar Excel | ✅ | Reportes ventas: gráficas (por día, por sucursal), exportar CSV/Excel. |
| Exportar PDF | ❌ | Solo CSV/Excel; no hay generación de PDF. |
| Ventas por día | ✅ | `estadisticasVentas` y gráfica por día. |
| Ventas por período | ✅ | Filtros fecha_desde / fecha_hasta. |
| Ventas por sucursal | ✅ | Filtro sucursal y gráfica por sucursal. |
| Reporte Excel: tickets entregados que vienen de Villahermosa, con fotos | ❌ | No hay reporte específico “tickets origen Villahermosa” ni columnas/rutas de fotos de entregas en el Excel. |

---

## 12. Roles (resumen)

| Rol | Requerimiento | Estado |
|-----|----------------|--------|
| Gerente de Producción | Ajustar/agregar inventario, ver reportes | ✅ `inventario.ajustar`, `reportes.ver` |
| Administrador | Gestionar usuarios, modificar precios | ✅ `usuarios.gestionar`, `precios.modificar`; falta endpoint precios |
| Vigilante | Solo escanear ticket para salida | ✅ `boletos.validar_salida` / operaciones.verificar (BoletoController) |
| Despachador | Control de entrega: escanear ticket, indicar m³ por entrega hasta completar; foto opcional por entrega | ✅ Dashboard entregas + `entregas.registrar`; foto opcional |

---

## Resumen de gaps (no integrado o parcial)

1. **Cancelación de ventas**: sin estatus `cancelado` ni permiso/endpoint.
2. **Aviso de material agotado**: sin listado/alertas de stock bajo.
3. **Catálogo Villahermosa**: sin seeder con los 12 materiales (Polvo, Rezaga, Balustre, Tortuguero, etc.).
4. **Modificar precios**: permiso existe; falta endpoint (ej. `PUT /api/productos/{id}` con `precios.modificar`).
5. **Registro de gastos**: tabla y relación listas; falta `GastoController` y rutas (ej. POST/GET por caja).
6. **Exportar PDF**: no implementado.
7. **Reporte Excel “tickets Villahermosa + fotos”**: no existe.
8. **Formato QR**: actualmente pipe (`|`); si se exige coma, cambiar en `generarQrPayload()` y en `importarPedido`.
9. **Ticket impreso**: datos en BD; falta plantilla/impresión con nombre de empresa, folio, fecha/hora, unidad, total.

Si quieres, el siguiente paso puede ser implementar solo los ítems que marques como prioritarios (por ejemplo: registro de gastos, modificar precios, cancelar ventas, seeder materiales Villahermosa).
