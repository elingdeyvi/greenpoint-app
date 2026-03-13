# Análisis de lógica de negocio – Sistema SCV (Ventas, Caseta y Almacén)

## 1. Contexto general y tipos de sucursal

El sistema SCV administra **ventas y salidas de materiales de construcción** con dos perfiles operativos definidos por la sucursal activa en `ConfiguracionEmpresa`:

- **Sucursal tipo `venta` (Villahermosa)**  
  - Genera ventas normales con ticket y QR.  
  - No maneja inventario físico local (no descuenta stock en `productos`).  
  - Flujo: venta directa en mostrador → ticket/QR → el material se despacha en otra unidad (almacén).

- **Sucursal tipo `venta_almacen` (Macuspana)**  
  - Opera como **caseta (Vigilante)** y **oficina central (Caja/Oficina)**.  
  - Controla inventario físico (stock) y salidas de material.  
  - Flujo principal:
    1. Caseta genera/escanea QRs y toma fotos de evidencias.
    2. Oficina valida pedidos, registra pagos (incluyendo donativos) y genera tickets definitivos.
    3. Entregas parciales controlan el material realmente despachado.

> La sucursal activa se obtiene con `ConfiguracionEmpresa::obtenerConfiguracion()->sucursal` y el front deriva de ella el `perfilInterfaz` (`VENTA` o `VENTA_ALMACEN`) para mostrar/ocultar módulos.

---

## 2. Ventas y pagos (módulo común)

### 2.1 Creación de ventas (`VentaController::store`)

- Si no se envía `sucursal_id`, se usa la sucursal configurada en `ConfiguracionEmpresa`.
- Cliente por defecto: `Cliente::clienteMostrador()` cuando `cliente_id` es nulo.
- Campos clave:
  - `tipo`: `'venta'` o `'donativo'`.
  - `es_donativo` (bool) y `observaciones` (texto).
  - `estatus`: enum (`pendiente`, `pendiente_pago`, `pagado`, `parcial`, `entregado`, `cancelado`).
  - `total` calculado como suma de `precio_unitario * cantidad_pedida` por detalle.

### 2.2 Regla de inventario por sucursal

- **Sucursal tipo `venta` (Villahermosa)**:
  - **No descuenta stock** al crear la venta (`reducirStock` no se llama).
  - La venta representa un pedido/compromiso, pero el stock real se controla en el almacén.

- **Sucursal tipo `venta_almacen` (Macuspana)**:
  - **Siempre descuenta stock** al crear ventas o importar pedidos.  
    - Implementado en `VentaController::store` e `importarPedido` llamando a `Producto::reducirStock($cantidad)`.
  - Aplica tanto para ventas normales como para **donativos** (los donativos también consumen inventario).

### 2.3 Pagos divididos (`PagoDetalle`) y donativos

- Tabla `pago_detalles`:
  - `venta_id`, `metodo_pago` (`efectivo`, `tarjeta`, `transferencia`, `credito`, ...), `monto`, `referencia_pago`.
  - Relación en `Venta`: `$venta->pagos()`.

- **Ventas normales (ingreso)**:
  - En `store`:
    - Se acepta `pagos[]` y se valida que la **suma de montos sea igual al total** de la venta.
  - En `registrarPagos` (flujo Oficina Macuspana):
    - De nuevo se exige que la suma de pagos coincida con `venta.total`.
    - Los pagos existentes se reemplazan por los nuevos.
    - La venta pasa a `estatus = 'pagado'` y se asegura tener `qr_payload` generado.

- **Donativos**:
  - Una venta es donativo si:
    - `tipo === 'donativo'` o `es_donativo = true`.
  - Regla de negocio:
    - **Observaciones son obligatorias** (justificación del donativo) tanto en `store` como en `registrarPagos`.
    - Puede no llevar pagos (`pagos` vacío) y, por tanto, no genera ingresos monetarios.
    - **Sí descuenta inventario** en sucursales `venta_almacen`.
    - En reportes y caja **no se suman a ingresos**, pero sí se contabiliza el valor del material despachado como “donativos”.

### 2.4 Inventario y movimientos

- **Modelo `Producto`**:
  - Campos relevantes para inventario: `precio_unitario`, `stock_actual`, `stock_minimo`, `unidad_medida`, `unidad_medida_id`, `activo`.
  - En sucursal `venta_almacen` los flujos de **venta**, **importación de pedido** e **importación silenciosa** siempre llaman a `Producto::reducirStock($cantidad)` cuando corresponde.
- **Historial `inventario_movimientos`**:
  - Tabla `inventario_movimientos` registra cada impacto de inventario:
    - `producto_id`, `tipo` (`ajuste`, `venta`, `donativo`, `cancelacion`, etc.), `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `usuario_id`.
  - Se generan movimientos automáticamente en:
    - `VentaController::store` (salida por venta/donativo en Macuspana).
    - `VentaController::importarPedido` (salida por importar pedido desde QR).
    - `EntregaController::importarPedidoSilencio` (salida por importación silenciosa de QR externo).
    - `VentaController::cancelar` (entrada por devolución al cancelar ventas en Macuspana).
  - Los **ajustes manuales** de stock (`InventarioController::ajustar`) también escriben en `inventario_movimientos` con tipo `ajuste` y motivo obligatorio.

---

## 3. Flujo Caseta → Oficina (solo Macuspana)

### 3.1 Generación de pedido en caseta (`VentaController::generarQrVigilanteLocal`)

- Requisitos:
  - Sucursal configurada y de tipo `venta_almacen`.
  - Se recibe `producto_id`, opcionalmente `venta_id` y `numero_viajes`.
- **Restricción de productos (sucursal venta_almacen)**:
  - Cuando la sucursal activa es tipo `venta_almacen`, **solo se permiten** los productos: **Polvo**, **Rezaga** y **Balastre** para generar nuevos tickets/pedidos locales.
  - La validación se delega a `CasetaService::isProductAllowedForLocalQr(Producto)`.
  - Si el producto no está permitido, se retorna **422** con mensaje claro en español indicando los productos permitidos.
  - **No aplica** a `validarQrVigilante` ni a `EntregaController::validarYRegistrarAcceso`: el vigilante puede **escanear y procesar cualquier material** que venga en un QR externo (Villahermosa) mediante la importación silenciosa.
- Comportamiento:
  - Si se indica `venta_id`, se actualiza o fija `viajes_permitidos` en la venta.
  - Si no hay `venta_id`:
    - Se crea una nueva `Venta` con:
      - `estatus = 'pendiente_pago'` (pendiente de cobro en Oficina).
      - `tipo = 'venta'`.
      - `viajes_permitidos = numero_viajes`, `viajes_usados = 0`.
  - Se genera un registro en `vigilante_qrs` (`VigilanteQr`) con:
    - `venta_id`, `uuid`, sucursal origen/local, `viajes_permitidos/usados`, `estatus = 'activo'`.
  - Se construye el payload plano del QR:
    - `uuid|idSucursal|idProd|cant|idUnidad|...`  
    - En el flujo local “cant” representa el número de viajes autorizados.

> La foto de evidencia en caseta no se guarda en esta acción, sino al registrar el primer acceso mediante `EntregaController::validarYRegistrarAcceso`.

### 3.2 Escaneo de QR y registro de accesos (`VentaController::validarQrVigilante` + `EntregaController::validarYRegistrarAcceso`)

- **Validación del QR (VentaController::validarQrVigilante)**:
  - Soporta payloads `uuid:sucursal|...` o `uuid|sucursal|...`.
  - Distingue si el QR es:
    - **Local** (origen Macuspana) → se usa `VigilanteQr` para controlar `viajes_permitidos/usados`.
    - **Externo** (p.ej. Villahermosa) → se crea registro `VigilanteQr` importado la primera vez.
  - Incrementa `viajes_usados` a nivel de **pedido (venta)** cuando `venta_id` está asociado, marcando `estatus = 'agotado'` cuando se consumen todos los viajes.

- **Registro de acceso con foto (EntregaController::validarYRegistrarAcceso)**:
  - Solo disponible en sucursal `venta_almacen`.
  - Recibe:
    - `qr_payload`, `cantidad_viaje` (opcional) y `foto` (obligatoria para evidencias).
  - Parseo del payload:
    - Obtiene `uuid`, `sucursal_origen_id`, `producto_id`, `cantidadTotal`, `unidad_id`.
  - **Importación automática (“importación silenciosa”)**:
    - Si el QR es externo (idSucursal ≠ Macuspana) y no hay venta local asociada a ese `uuid`, se crea una venta local:
      - Usa método privado `importarPedidoSilencio()`:
        - Crea `Venta` con detalles mapeando productos, descuenta stock y genera `qr_payload`.
      - La venta queda lista para seguimiento y reporte, sin intervención manual de “Importar pedido”.
  - Control de remanente/cantidad:
    - Si hay venta ligada por `VigilanteQr.venta_id`, los viajes se controlan a nivel de pedido.
    - Crea un registro `Entrega` con:
      - `venta_id` (si existe), `uuid_qr`, `numero_viaje`, `cantidad_despachada`, `foto_path`.
  - Bloqueos:
    - No permite más viajes si la venta está en `estatus` `'entregado'` o `'cancelado'`.

### 3.3 Cobro en Oficina Central (Validación de pedidos)

- Endpoint `VentaController::pedidosPendientesPago`:
  - Sucursal debe ser `venta_almacen`.
  - Lista ventas con `estatus = 'pendiente_pago'` de esa sucursal, con relaciones `detalles` y `pagos`.
- Endpoint `VentaController::registrarPagos`:
  - Recibe `pagos[]`, opcional `es_donativo` y `observaciones`.
  - Para ventas normales:
    - Exige al menos un pago y que la suma de montos sea igual al total.
  - Para donativos:
    - No exige pagos, pero **sí observaciones**; marca la venta como `tipo = 'donativo'`, `es_donativo = true`.
  - En ambos casos:
    - Cambia `estatus` a `'pagado'`.
    - Genera `qr_payload` definitivo si no existía.

> La UI `cajas/validar-pedidos.vue` consume estos endpoints y permite múltiples métodos de pago, así como marcar ventas como donativo con su observación.

---

## 4. Entregas parciales y seguimiento de pedidos

- Controlador `EntregaController`:
  - `registrar` (para ventas con detalle):
    - Valida que `cantidad_despachada` no exceda el restante (`cantidad_pedida - cantidad_entregada`).
    - Crea `Entrega` ligada a `venta_id` y `venta_detalle_id`.
    - `Entrega::booted` actualiza `cantidad_entregada` en `venta_detalles` y el `estatus` de la `Venta`:
      - `'parcial'` si hay algún detalle sin completar.
      - `'entregado'` si todos los detalles están completamente entregados.
  - `validarYRegistrarAcceso` (ver sección 3.2) maneja entregas asociadas a QRs (locales o externos) mediante `uuid_qr`.

---

## 5. Caja y corte X/Z

- `CajaController::apertura`:
  - Una sola caja `estatus = 'abierta'` por sucursal.
  - Guarda `monto_inicial` y marca fecha de apertura.

- `CajaController::corte`:
  - Cierra caja (`estatus = 'cerrada'`, `fecha_cierre`, `monto_final`).
  - Obtiene ventas del período (`created_at` entre apertura/cierre, `estatus != 'cancelado'`).
  - Separa:
    - **Ventas de ingreso**: ventas no donativo (`tipo != 'donativo'` y `es_donativo = false`).
    - **Donativos**: ventas donde `tipo = 'donativo'` o `es_donativo = true`.
  - Calcula:
    - `total_ventas_ingreso` = suma de `total` de ventas de ingreso.
    - `total_donativos` = suma de `total` de donativos (valor referencial).
    - Usa `PagoDetalle` para:
      - `total_cobrado` (suma de pagos solo de ventas no donativo del período).
      - `pagos_por_metodo` (efectivo, tarjeta, transferencia, etc.).
  - Diferencia de caja:
    - `diferencia = (monto_inicial + totalEfectivo) - totalGastos - monto_final_reportado`.
    - **Solo el efectivo** afecta el cuadre físico, los donativos no distorsionan este cálculo.
  - Devuelve:
    - `reporte_x` (informe del período).
    - `reporte_z` (cierre definitivo).
    - Incluyen `total_ventas_ingreso`, `total_donativos`, `total_cobrado`, `pagos_por_metodo`.

---

## 6. Reportes y Dashboard

### 6.1 Dashboard y estadísticas de boletos

- **Dashboard unificado (cualquier sucursal)**:
  - El front consume **`GET /api/dashboard/estadisticas`**, que delega en `ReporteController::dashboardEstadisticas`.
  - El backend detecta la sucursal activa y el tipo (`venta` o `venta_almacen`) y devuelve un payload adaptado al perfil:
    - Siempre incluye estadísticas de **boletos** (total, pendientes, utilizados, cancelados, series por día, salidas_hoy, etc.).
    - Incluye estadísticas de **ventas** filtradas por sucursal activa (total_ventas, cantidad_ventas, series por día).
    - Para sucursales tipo `venta_almacen` añade métricas de inventario y operaciones:
      - `donativos_total` (valor monetario de donativos).
      - `alertas_inventario` (productos con `stock_actual <= 0`).
      - `productos_stock_bajo` (productos con `stock_minimo > 0` y `stock_actual < stock_minimo`).
      - `valor_inventario` (suma de `precio_unitario * stock_actual` de productos activos).
      - `pendientes_cobro` (ventas con `estatus = 'pendiente_pago'` en la sucursal).
      - `entregas_hoy` y `volumen_entregado_hoy` (viajes y m³ despachados en el día).
  - De esta forma el dashboard carga correctamente para usuarios de Villahermosa y Macuspana sin 403, y muestra datos relevantes según el perfil de sucursal.
- **Reportes de salidas (boletos legacy)**:
  - Las rutas **`/api/reportes/salidas`**, **`/api/reportes/estadisticas`** y **`/api/reportes/exportar-csv`** están bajo middleware **`sucursal.tipo:venta_almacen`**.
  - Trabajan sobre la tabla `boletos` (flujo legacy de camión/volteo).
  - Usan relaciones `usuarioGenerador` y `usuarioValidador`.
  - El módulo de salidas solo se muestra en sucursales `venta_almacen`.

### 6.2 Reportes de ventas (estadísticas y exportación)

- `ReporteController::estadisticasVentas`:
  - Base: `ventas` con `estatus != 'cancelado'` y dentro del rango de fechas.
  - Para estadísticas de ingreso:
    - Considera solo **ventas no donativo** (`tipo != 'donativo'` y `es_donativo = false`).
    - Devuelve:
      - `por_dia`, `por_sucursal`, `series_por_sucursal`.
      - `total_ventas` (solo ingresos monetarios).
      - `cantidad_ventas`.

- `exportarVentasExcel`:
  - Incluye columnas:
    - `Folio,Sucursal,Usuario,Total,Estatus,Tipo,Observaciones,Fecha,Creado`.
  - `Tipo`:
    - `"Donativo"` si la venta es donativo; `"Venta"` en caso contrario.
  - `Observaciones`:
    - Texto plano sin saltos de línea (para compatibilidad con CSV/Excel).

- `exportarVentasPdf` (`reportes.ventas-pdf`):
  - Tabla con columnas:
    - Folio, Sucursal, Usuario, Total, Estatus, Tipo, Observaciones, Fecha.
  - Pie del reporte:
    - Resume:
      - Total de ventas de ingreso (cantidad y suma).
      - Total de salidas por donativo (cantidad y suma referencial de material donado).

- `exportarTicketsVillahermosaExcel`:
  - Reporte de tickets originados en Villahermosa (`ticket_origen_uuid`, `estatus = 'entregado'`) con URLs de fotos de entregas asociadas.

---

## 7. Productos (catálogo)

- **Tabla `productos`**:
  - Campos relevantes: `nombre`, `codigo` (opcional, slug por producto), `precio_unitario`, `stock_actual`, `unidad_medida`, `unidad_medida_id`, `activo`.
- **Seeders**:
  - **ProductSeeder**: catálogo base con los 12 nombres (Polvo, Rezaga, Balastre, Tortuguero, Granzón, Revestimiento, Grava de 3/4, Grava de 1/2, Gravón de 6", Finos, Piedra Braza, Roca Maya). Usa `updateOrCreate` por `nombre` para evitar duplicados.
  - **ProductoMacuspanaSeeder**: los 3 productos con IDs fijos (1 Polvo, 2 Rezaga, 3 Balastre) para alinear con el sistema Villahermosa al importar pedidos por QR.
- **Restricción en caseta (backend)**: `CasetaService::getAllowedProductNamesForLocalQr()` devuelve `['Polvo', 'Rezaga', 'Balastre']`; solo esos se aceptan en `generarQrVigilanteLocal` cuando la sucursal es `venta_almacen`.

---

## 8. Visibilidad de módulos por tipo de sucursal (front-end)

Basado en `perfilInterfaz` almacenado en Vuex:

- **Perfil `VENTA` (Villahermosa)**:
  - Menú:
    - `Ventas`:
      - `Nueva venta` (pagos múltiples).
      - `Productos`, `Clientes`.
    - `Reportes`:
      - Solo **Ventas**.
  - Oculto:
    - `Entregas`, `Vigilante`, `Inventario`, `Reportes de salidas`, `Validación de pedidos` (Oficina).

- **Perfil `VENTA_ALMACEN` (Macuspana)**:
  - Menú:
    - `Ventas`:
      - `Entregas`.
      - `Control de acceso (Vigilante)`.
    - `Inventario`:
      - `Gestión de inventario` (listado de existencias, ajuste manual de stock con observación obligatoria y acceso a historial de movimientos).
      - `Alertas de stock` (productos con stock por debajo de un umbral).
    - `Caja`:
      - `Caja y gastos`.
      - `Validación de pedidos` (Oficina Central para cobros/donativos).
    - `Reportes`:
      - `Salidas (boletos)` y `Ventas`.
  - Opción manual de **“Importar pedido (QR)”** se oculta del menú; la importación es automática al escanear en Vigilante.

- **Filtro de productos en Caseta (front)**:
  - Cuando `perfilInterfaz === 'VENTA_ALMACEN'`, el selector de productos en la sección **"Generar QR local"** (Vigilante) solo muestra **Polvo**, **Rezaga** y **Balastre**.
  - La sección **"Escanear QR"** y el registro de acceso **no** aplican este filtro: se puede procesar cualquier material recibido vía QR (local o externo).
  - Implementado en `VigilanteAcceso.vue` y `VigilanteScanner.vue` con `productosParaGenerar` (computed que filtra por nombre según perfil).

---

## 9. Diagrama de flujo resumido

```text
Villahermosa (VENTA):
  [VentaController::store]
    → Venta (tipo venta, sin descuento de stock local)
    → Ticket + QR (Venta::generarQrPayload)
    → Reportes de ventas (ingresos)

Macuspana (VENTA_ALMACEN):

  Caseta (Vigilante):
    - Generar pedido local:
        generarQrVigilanteLocal (solo productos Polvo/Rezaga/Balastre si venta_almacen)
        → Venta(estatus=pendiente_pago, viajes_permitidos)
        → VigilanteQr (uuid + viajes)
    - Escanear QR (local o externo):
        validarQrVigilante → valida UUID / viajes
        validarYRegistrarAcceso → Entrega + foto (y, si es externo y nuevo, importarPedidoSilencio → Venta local)

  Oficina Central:
    - Validar pedidos:
        pedidosPendientesPago → lista de ventas pendiente_pago
        registrarPagos → pagos múltiples o donativo (observaciones obligatorias)
        → estatus = pagado, qr_payload definitivo

  Entregas:
    - registrar → Entrega parcial ligada a detalle
        → actualiza cantidad_entregada y estatus (pendiente/parcial/entregado)

Caja:
    - apertura → caja abierta con monto_inicial
    - corte → reporte X/Z
        total_ventas_ingreso (ventas no donativo)
        total_donativos (valor donado)
        total_cobrado, pagos_por_metodo, diferencia basada en efectivo
```

Este documento resume la lógica de negocio **actual** del sistema, alineada con la estructura de datos, la distinción de sucursales (`venta` vs `venta_almacen`), los flujos de Caseta→Oficina, la restricción de productos en generación local de QR (Polvo, Rezaga, Balastre), los pagos múltiples, los donativos, el dashboard unificado por sucursal activa (con indicadores específicos para Macuspana) y la segregación correcta de ingresos y salidas de material en reportes, inventario y corte de caja.

