# Cambios aplicados: tickets = ventas, 2 sucursales, configuración empresa

## Resumen

- **Los boletos se generan con la venta:** cada venta genera el ticket con QR. No existe ya el flujo separado "Operaciones > Generar Boleto".
- **Dos sucursales:** Villahermosa (solo ventas) y Macuspana (ventas y almacén).
- **Configuración de empresa** relacionada con sucursales: sucursal venta (Villahermosa) y sucursal almacén (Macuspana).
- **Formato QR:** `uuid:idSucursal|idProducto|cantidad|idUnidad|...` (idUnidad = número del catálogo unidades de medida).
- **Entregas:** fotos al entregar; cantidad en m³.
- **Catálogo de clientes** con "Cliente mostrador" por defecto (no se puede borrar).
- **Seeders** con sucursales, productos por sucursal, config empresa, cliente mostrador.

---

## 1. Migraciones nuevas

- **tipo_sucursal en sucursales:** `venta` (solo ventas) o `venta_almacen` (ventas y almacén).
- **unidades_medida:** catálogo (id, codigo, nombre); productos con `unidad_medida_id`.
- **clientes:** nombre, es_mostrador, activo.
- **ventas.cliente_id:** FK a clientes (por defecto Cliente mostrador).
- **configuracion_empresa:** `sucursal_venta_id`, `sucursal_almacen_id` (FK a sucursales).

---

## 2. Modelos

- **Sucursal:** `tipo_sucursal`; `isSucursalVenta()`, `isSucursalVentaAlmacen()` (y deprecated `isVillahermosa()` / `isMacuspana()`).
- **ConfiguracionEmpresa:** `sucursal_venta_id`, `sucursal_almacen_id`; relaciones `sucursalVenta()`, `sucursalAlmacen()`.
- **UnidadMedida:** nuevo modelo.
- **Cliente:** nuevo modelo; `Cliente::clienteMostrador()`; no se puede borrar si `es_mostrador`.
- **Producto:** `unidad_medida_id`, relación `unidadMedida()`, accessor `unidad_nombre`.
- **Venta:** `cliente_id`, relación `cliente()`; `generarQrPayload()` con formato `uuid:idSucursal|idProd|cant|idUnidad|...`.

---

## 3. Backend

- **VentaController:**  
  - Siempre genera ticket con QR al crear venta (Villahermosa y Macuspana).  
  - `cliente_id` por defecto Cliente mostrador; opcional en request.  
  - `importarPedido` parsea QR con formato `uuid:idSucursal|...`.
- **ClienteController:** index, store, update, destroy (no borrar cliente mostrador).
- **UnidadMedidaController:** index.
- **Rutas API:**  
  - Eliminado prefijo `/boletos` (generar, verificar, lista, capturar-foto, validar).  
  - Añadidas: GET/POST/PUT/DELETE `/clientes`, GET `/unidades-medida`.

---

## 4. Frontend

- **Menú (sidebar y header):** eliminada la sección "Operaciones" (Generar Boleto, Verificar Boleto, Lista de Boletos).
- **Router:** eliminadas rutas `/boletos/generar`, `/boletos/verificar`, `/boletos/lista`.

Las vistas en `views/boletos/` siguen en el proyecto pero ya no están enlazadas en el menú ni en el router.

---

## 5. Seeders

- **UnidadMedidaSeeder:** m3 (codigo 1).
- **ClienteSeeder:** "Cliente mostrador" (es_mostrador = true).
- **SucursalSeeder:** Villahermosa (tipo venta), Macuspana (tipo venta_almacen); `updateOrCreate` para actualizar tipo si ya existían.
- **ProductoSeeder:**  
  - Macuspana: Polvo, Rezaga, Balustre.  
  - Villahermosa: Polvo, Rezaga, Balustre, Tortuguero, Granzón, Revestimiento, Grava de 3/4, Grava de 1/2, Gravón de 6", Finos, Piedra Braza, Roca Maya.
- **ConfiguracionEmpresaSeeder:** firstOrCreate por nombre "Entrada"; actualiza `sucursal_venta_id` (Villahermosa) y `sucursal_almacen_id` (Macuspana).

`DatabaseSeeder` llama: UnidadMedidaSeeder, ClienteSeeder, SucursalSeeder, ProductoSeeder, ConfiguracionEmpresaSeeder.

---

## 6. Cómo aplicar en un entorno existente

1. **Migrar:** `php artisan migrate`
2. **Seed (o solo seeders que falten):** `php artisan db:seed`
3. Si ya había sucursales sin `tipo_sucursal`, ejecutar de nuevo: `php artisan db:seed --class=SucursalSeeder`
4. Si ya había configuracion_empresa, el ConfiguracionEmpresaSeeder actualiza las sucursales si existen Villahermosa y Macuspana.

---

## 7. Pendiente opcional (frontend)

- Vista/listado de **Clientes** (alta/edición; cliente mostrador solo lectura y no borrable).
- En **Configuración de empresa**, pantalla para elegir sucursal venta y sucursal almacén (ya están en BD y seeders).
- En **Ventas**, selector de cliente (por defecto Cliente mostrador) usando GET `/api/clientes`.
