# Lógica de negocio del sistema (documento de referencia para aplicar cambios)

Este documento describe la **lógica de negocio implementada** en el proyecto: entidades, migraciones, seeders, roles, permisos, rutas y reglas. Sirve como referencia al aplicar cambios en backend, frontend o base de datos.

---

## 1. Resumen del sistema

| Aspecto | Detalle |
|--------|---------|
| **Nombre funcional** | Sistema de Control de Salidas de Volteos (SCV) + Módulo de Ventas e Inventario |
| **Objetivo (SCV)** | Registrar la salida de camiones de volteo: el **Despachador** genera un boleto (con foto y QR) y el **Vigilante** lo valida en la salida escaneando el QR o el folio. Cada boleto es único y no reutilizable. |
| **Objetivo (Ventas)** | Dos sucursales (Villahermosa, Macuspana): **Villahermosa** genera ventas con ticket y QR; **Macuspana** importa pedidos escaneando el QR de Villahermosa, registra entregas parciales (con foto opcional) y puede cerrar venta. Incluye inventario por producto/sucursal, alertas de stock bajo, caja (apertura/cierre) y registro de gastos por caja. Cancelación de ventas (cuando no cumple calidad) con devolución a inventario. Reportes de ventas (estadísticas, Excel, PDF) y reporte Excel “tickets entregados de Villahermosa” con URLs de fotos. Impresión de ticket de venta (nombre empresa, folio, fecha/hora, sucursal, líneas, total) vía impresora configurada. |
| **Backend** | Laravel (API REST), PHP. |
| **Frontend** | Vue 3 (SPA), `resources/js/src/`. |
| **Base de datos** | MySQL / PostgreSQL. |
| **Autenticación** | Laravel Sanctum (`auth:sanctum`). |
| **Autorización** | Spatie Laravel Permission (roles y permisos). |
| **Guard** | `web` (usado en permisos y roles). |

---

## 2. Migraciones (orden de ejecución)

Las migraciones se ejecutan por nombre (fecha). Orden relevante del proyecto:

| Orden | Archivo | Descripción |
|-------|---------|-------------|
| 1 | `2014_10_12_000000_create_users_table.php` | Tabla `users` base. |
| 2 | `2014_10_12_100000_create_password_reset_tokens_table.php` | Tabla para reset de contraseña. |
| 3 | `2019_08_19_000000_create_failed_jobs_table.php` | Jobs fallidos. |
| 4 | `2019_12_14_000001_create_personal_access_tokens_table.php` | Tokens Sanctum. |
| 5 | `2023_11_06_083819_create_permission_tables.php` | Tablas de Spatie Permission. |
| 6 | `2024_01_15_000000_create_roles_and_test_users.php` | Permisos, roles, usuarios de prueba y asignación. |
| 7 | `2024_01_15_000009_create_configuracion_empresa_table.php` | Tabla `configuracion_empresa`. |
| 8 | `2024_01_15_000013_add_estatus_to_users_table.php` | Columna `estatus` en `users`. |
| 9 | `2024_01_20_000003_create_marcas_table.php` | Tabla `marcas`. |
| 10 | `2024_01_25_000001_create_tipos_carga_table.php` | Tabla `tipos_carga`. |
| 11 | `2024_01_25_000002_create_destinos_table.php` | Tabla `destinos`. |
| 12 | `2024_01_25_000003_create_configuracion_hardware_table.php` | Tabla `configuracion_hardware`. |
| 13 | `2024_01_25_000004_create_boletos_table.php` | Tabla `boletos`. |
| 14 | `2024_01_25_000005_create_placas_frecuentes_table.php` | Tabla `placas_frecuentes`. |
| 15 | `2025_02_22_000001_create_sucursales_table.php` | Tabla `sucursales`. |
| 16 | `2025_02_22_000002_create_productos_table.php` | Tabla `productos`. |
| 17 | `2025_02_22_000003_create_ventas_table.php` | Tabla `ventas`. |
| 18 | `2025_02_22_000004_create_venta_detalles_table.php` | Tabla `venta_detalles`. |
| 19 | `2025_02_22_000005_create_entregas_table.php` | Tabla `entregas`. |
| 20 | `2025_02_22_000006_create_cajas_table.php` | Tabla `cajas`. |
| 21 | `2025_02_22_000007_create_gastos_table.php` | Tabla `gastos`. |
| 22 | `2025_02_22_000008_add_uuid_to_ventas_and_detalle_to_entregas.php` | UUID en ventas, venta_detalle_id y foto_ruta en entregas. |
| 23 | `2025_02_22_000009_add_gerente_produccion_and_permissions.php` | Rol Gerente de Producción y permisos: ventas.*, productos.*, inventario.consultar, entregas.registrar, cajas.abrir_cerrar, gastos.registrar. |
| 24 | `2025_02_22_000010_add_cancelado_to_ventas_and_permission.php` | Estatus `cancelado` en ventas y permiso `ventas.cancelar`. |

---

## 3. Esquema detallado de tablas

### 3.1 `users` (Laravel + migración estatus)

| Columna | Tipo | Restricciones | Descripción |
|--------|------|---------------|-------------|
| id | bigint | PK, auto_increment | |
| name | string | not null | |
| email | string | unique, not null | |
| email_verified_at | timestamp | nullable | |
| **estatus** | enum('activo','inactivo','suspendido') | default 'activo' | Añadido en `2024_01_15_000013`. |
| password | string | not null | |
| remember_token | string(100) | nullable | |
| created_at | updated_at | timestamp | nullable |

**Nota:** El modelo `User` puede incluir en `$fillable`: `estatus`, `autorizado`, `cliente_id`, `nota`, `uuid`, según migraciones o uso.

### 3.2 Tablas de Spatie Permission

Nombres por defecto: `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`. En este proyecto los roles tienen permisos vía `role_has_permissions`; los usuarios tienen roles vía `model_has_roles`.

### 3.3 `configuracion_empresa`

Nombre empresa, logo, favicon, dirección, horarios, etc. Usado en ticket impreso y reportes PDF.

### 3.4 `marcas`, `tipos_carga`, `destinos`, `configuracion_hardware`, `boletos`, `placas_frecuentes`

Descritos en la versión anterior del documento; corresponden al módulo SCV (control de salidas).

### 3.5 `sucursales`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| nombre | string | Nombre de la sucursal (ej. Villahermosa, Macuspana) |
| activo | boolean | default true |
| created_at, updated_at | timestamp | nullable |

### 3.6 `productos`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| nombre | string | |
| descripcion | text | nullable |
| precio_unitario | decimal(12,2) | default 0 |
| stock_actual | decimal(12,2) | default 0 |
| unidad_medida | string | ej. m3 |
| sucursal_id | FK | → sucursales |
| activo | boolean | default true |
| created_at, updated_at | timestamp | nullable |

Al vender se descuenta `stock_actual`; al cancelar venta se devuelve lo no entregado.

### 3.7 `ventas`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| uuid | string | UUID único (para QR y ticket_origen) |
| folio | string | único, ej. VTA-YYYYMMDD-XXXXXX |
| sucursal_id | FK | → sucursales |
| usuario_id | FK | → users |
| total | decimal(12,2) | |
| estatus | enum | 'pendiente', 'parcial', 'entregado', 'cancelado' |
| tipo | string | nullable, ej. venta, donativo |
| ticket_origen_uuid | string | nullable; si viene de Macuspana importando pedido de Villahermosa |
| qr_payload | text | nullable; formato uuid\|id_prod\|cant\|unid\|... |
| created_at, updated_at | timestamp | nullable |

### 3.8 `venta_detalles`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| venta_id | FK | → ventas, cascadeOnDelete |
| producto_id | FK | → productos |
| cantidad_pedida | decimal(12,2) | |
| cantidad_entregada | decimal(12,2) | default 0 |
| created_at, updated_at | timestamp | nullable |

### 3.9 `entregas`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| venta_id | FK | → ventas |
| venta_detalle_id | FK | nullable → venta_detalles |
| usuario_id | FK | → users |
| cantidad_despachada | decimal(12,2) | |
| foto_ruta | string | nullable; foto de la unidad al entregar |
| created_at | timestamp | (UPDATED_AT null) |

Cada registro actualiza `cantidad_entregada` del detalle; si todos los detalles quedan completos, la venta pasa a `entregado`.

### 3.10 `cajas`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| sucursal_id | FK | → sucursales |
| usuario_id | FK | → users (quien abre) |
| monto_inicial | decimal(12,2) | |
| monto_final | decimal(12,2) | nullable; al cerrar |
| estatus | enum | 'abierta', 'cerrada' |
| fecha_apertura | timestamp | |
| fecha_cierre | timestamp | nullable |
| created_at, updated_at | timestamp | nullable |

Solo puede haber una caja `abierta` por sucursal.

### 3.11 `gastos`

| Columna | Tipo | Descripción |
|--------|------|-------------|
| id | bigint | PK |
| caja_id | FK | → cajas, cascadeOnDelete |
| descripcion | string | |
| monto | decimal(12,2) | |
| created_at | timestamp | nullable |

Solo se pueden crear gastos cuando la caja está `abierta`.

---

## 4. Roles y permisos (detalle)

### 4.1 Listado de permisos

Incluye los creados en `2024_01_15_000000_create_roles_and_test_users.php` y los añadidos en `2025_02_22_000009` y `2025_02_22_000010`:

| # | Nombre del permiso | Módulo / Uso |
|---|--------------------|--------------|
| 1 | `dashboard.ver` | Dashboard principal |
| 2 | `operaciones.generar` | Generar boleto (SCV) |
| 3 | `operaciones.verificar` | Verificar boleto (SCV) |
| 4 | `operaciones.consultar` | Lista de boletos |
| 5 | `reportes.consultar` | Reportes completos |
| 6 | `reportes.consultar_propios` | Reportes solo propios |
| 7 | `administracion.usuarios` | CRUD usuarios |
| 8 | `administracion.roles` | Roles y permisos |
| 9 | `administracion.configuracion_hardware` | Cámaras e impresoras |
| 10 | `administracion.catalogos` | Tipos de carga, destinos, placas frecuentes |
| 11 | `ventas.crear` | Crear venta, importar pedido |
| 12 | `ventas.ver` | Ver listado y detalle de ventas |
| 13 | `ventas.cancelar` | Cancelar venta (devolución a inventario) |
| 14 | `ventas.entregar` | (asignado a roles; lógica de entrega usa entregas.registrar) |
| 15 | `entregas.registrar` | Registrar entregas parciales |
| 16 | `productos.ver` | Ver productos |
| 17 | `productos.administrar` | Administrar productos |
| 18 | `inventario.consultar` | Consultar inventario / alertas de stock |
| 19 | `inventario.ajustar` | Ajustar stock (Gerente de Producción) |
| 20 | `cajas.abrir_cerrar` | Abrir y cerrar caja |
| 21 | `gastos.registrar` | Registrar gastos en caja abierta |

### 4.2 Roles y asignación de permisos

| Rol | Permisos asignados |
|-----|---------------------|
| **Administrador** | Todos los permisos anteriores. |
| **Despachador** | `operaciones.generar`, `reportes.consultar_propios`. |
| **Vigilante** | `operaciones.verificar`, `ventas.ver`, `entregas.registrar`. |
| **Gerente de Producción** | `dashboard.ver`, `ventas.crear`, `ventas.ver`, `ventas.entregar`, `productos.ver`, `productos.administrar`, `inventario.consultar`, `entregas.registrar`, `cajas.abrir_cerrar`, `gastos.registrar`, `reportes.consultar_propios`. |

Constantes en código (si existen): `User::ADMIN_ROL`, `User::DESPACHADOR_ROL`, `User::VIGILANTE_ROL`.

### 4.3 Comportamiento en frontend

- **Administrador:** el endpoint `/api/tokens/permissions` devuelve `all: true`; `hasPermission()` devuelve true para cualquier permiso.
- **Otros roles:** solo los permisos listados; las rutas con `meta.permission` se evalúan con `usePermissions()`. Sin permiso → redirección a Home.

---

## 5. Rutas backend (API)

### 5.1 Rutas públicas (sin `auth:sanctum`)

| Método | Ruta | Controlador @ método |
|--------|------|----------------------|
| POST | /api/registro | UserController@registro |
| GET | /api/configuracion-empresa/public | ConfiguracionEmpresaController@publicConfig |
| GET | /api/storage-link | Closure (Artisan::call storage:link) |
| GET | /api/generate-key | Closure (Artisan::call key:generate) |
| POST | /api/tokens/create | TokenController@createToken |
| POST | /api/tokens/pw | TokenController@createResetPW |
| POST | /api/tokens/savepw | TokenController@saveResetPW |
| POST | /api/tokens/getpwuuid | TokenController@getUserUuid |
| POST | /api/boletos/capturar-foto-test | BoletoController@capturarFotoTest |
| GET | /api/probar-video-test | ConfiguracionHardwareController@probarVideoTest |

### 5.2 Rutas protegidas (`auth:sanctum`) — resumen por módulo

**Dashboard y tokens:** `/api/dashboard/estadisticas`, `/api/tokens/{token}` DELETE, `/api/tokens/permissions` GET.

**Users, roles, configuración empresa, marcas:** según documentación anterior (users, roles, configuracion-empresa, marcas).

**Operaciones (SCV):** `/api/boletos` GET/POST, `/api/boletos/{boleto}` GET, `/api/boletos/capturar-foto` POST, `/api/boletos/validar` POST.

**Configuración hardware, tipos-carga, destinos, placas-frecuentes:** CRUD estándar.

**Reportes:**

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | /api/reportes/salidas | Salidas (boletos), paginado y filtros |
| GET | /api/reportes/estadisticas | Estadísticas boletos |
| GET | /api/reportes/exportar-csv | CSV salidas |
| GET | /api/reportes/ventas/estadisticas | Estadísticas ventas (por día, sucursal) |
| GET | /api/reportes/ventas/exportar-excel | CSV/Excel ventas (base64) |
| GET | /api/reportes/ventas/exportar-pdf | PDF ventas (mismos filtros) |
| GET | /api/reportes/ventas/tickets-villahermosa-excel | CSV tickets entregados Villahermosa con URLs de fotos |

**Catálogos ventas:** GET `/api/sucursales`, GET `/api/productos`.

**Ventas:**

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | /api/ventas | Listado con filtros |
| POST | /api/ventas | Crear venta |
| POST | /api/ventas/importar-pedido | Importar pedido (Macuspana) por QR/uuid |
| GET | /api/ventas/{venta} | Detalle |
| POST | /api/ventas/{venta}/cancelar | Cancelar venta (requiere ventas.cancelar; devuelve stock no entregado) |
| GET | /api/ventas/{venta}/ticket-contenido | Contenido texto del ticket para impresión |

**Entregas:** GET `/api/entregas`, POST `/api/entregas/registrar`.

**Inventario:** POST `/api/inventario/ajustar` (requiere inventario.ajustar), GET `/api/inventario/alertas` (sucursal_id, umbral opcionales).

**Cajas:** GET `/api/cajas`, GET `/api/cajas/caja-abierta`, POST `/api/cajas/apertura`, POST `/api/cajas/corte`. GET `/api/cajas/{caja}/gastos`, POST `/api/cajas/{caja}/gastos`.

**Impresión:** GET/POST/PUT `/api/impresion`, POST `/api/impresion/imprimir-raw` (impresora_id, contenido, encoding opcional).

---

## 6. Frontend: rutas Vue y permisos

Definidas en `resources/js/src/router/index.js`. `meta.permission` exige ese permiso (o uno de la lista con hasAny).

| Ruta | meta.permission | Descripción |
|------|-----------------|-------------|
| / | — | Home; con token y dashboard.ver → redirige a Dashboard. |
| /dashboard | dashboard.ver | Dashboard. |
| /auth/login, /auth/pass-recovery-boxed, /auth/changepw/:uuid | layout: auth | Login y recuperación. |
| /users/profile | — | Perfil. |
| /users/lista | administracion.usuarios | Usuarios. |
| /marcas/lista | — | Marcas. |
| /boletos/generar | operaciones.generar | Generar boleto. |
| /boletos/verificar | operaciones.verificar | Verificar boleto. |
| /boletos/lista | operaciones.consultar | Lista boletos. |
| /configuracion-hardware/lista | administracion.configuracion_hardware | Hardware. |
| /tipos-carga/lista, /destinos/lista, /placas-frecuentes/lista | administracion.catalogos | Catálogos SCV. |
| /ventas | ventas.crear, ventas.ver (hasAny) | Nueva venta / Lista. |
| /ventas/importar-pedido | ventas.crear, ventas.ver (hasAny) | Importar pedido (Macuspana). |
| /entregas | entregas.registrar, ventas.ver (hasAny) | Dashboard de entrega. |
| /inventario/alertas | inventario.consultar, inventario.ajustar (hasAny) | Alertas de stock. |
| /cajas | cajas.abrir_cerrar, gastos.registrar (hasAny) | Caja y gastos. |
| /reportes/salidas | reportes.consultar, reportes.consultar_propios (hasAny) | Salidas (boletos). |
| /reportes/ventas | reportes.consultar, reportes.consultar_propios (hasAny) | Reportes de ventas. |
| /roles/lista | administracion.roles | Roles y permisos. |

---

## 7. Menú lateral (sidebar) y permisos

Archivo: `resources/js/src/components/layout/sidebar.vue`. Se usa `can(permission)` y `canAny(permissions)`.

| Sección | Permiso(s) para ver | Submenús |
|---------|----------------------|----------|
| **Dashboard** | dashboard.ver | Dashboard → /dashboard |
| **Operaciones** | operaciones.generar O verificar O consultar | Generar Boleto → /boletos/generar, Verificar Boleto → /boletos/verificar, Lista de Boletos → /boletos/lista |
| **Administración** | administracion.usuarios O roles O configuracion_hardware O catalogos | Usuarios, Roles, Hardware, Catálogos (Tipos de Carga, Destinos, Placas Frecuentes) |
| **Ventas** | ventas.crear O ventas.ver O entregas.registrar | Nueva venta / Lista → /ventas, Importar pedido (Macuspana) → /ventas/importar-pedido, Dashboard de entrega → /entregas |
| **Inventario** | inventario.consultar O inventario.ajustar | Alertas de stock → /inventario/alertas |
| **Caja** | cajas.abrir_cerrar O gastos.registrar | Caja y gastos → /cajas |
| **Reportes** | reportes.consultar O reportes.consultar_propios | Salidas (boletos) → /reportes/salidas, Ventas → /reportes/ventas |
| **Mi Perfil** | (sin permiso) | /users/profile |

---

## 8. Flujos de negocio implementados

### 8.1 SCV: Generar y validar boleto

- Generar boleto: permiso `operaciones.generar`, opcional captura de foto, POST boletos con placa (obligatorio), conductor, tipo_carga_id, destino_id, etc. Folio único BOL-YYYYMMDD-XXXXXX, estatus pendiente.
- Validar boleto: permiso `operaciones.verificar`, POST boletos/validar con folio o codigo_qr; si pendiente → estatus utilizado, usuario_validador_id, fecha_validacion.

### 8.2 Ventas (Villahermosa)

- Usuario con `ventas.crear` / `ventas.ver`: crea venta en sucursal con detalles (producto_id, cantidad_pedida). Backend genera folio VTA-..., uuid, qr_payload (uuid|id|cant|unid|...), descuenta stock. Opción de imprimir ticket (GET ticket-contenido + POST impresion/imprimir-raw).

### 8.3 Ventas (Macuspana): Importar pedido

- Usuario accede a /ventas/importar-pedido, escanea QR o ingresa payload de Villahermosa. POST /api/ventas/importar-pedido con el uuid o payload; backend crea venta en Macuspana con ticket_origen_uuid y mismos productos/cantidades.

### 8.4 Cancelar venta

- Requiere `ventas.cancelar`. POST /api/ventas/{venta}/cancelar. Solo si estatus es pendiente o parcial; se devuelve al inventario (cantidad_pedida - cantidad_entregada) por cada detalle; estatus → cancelado.

### 8.5 Entregas parciales

- Usuario con `entregas.registrar`: en Dashboard de entrega busca venta por folio/QR, ve líneas y progreso; registra cantidad a entregar (y opcionalmente foto). POST entregas/registrar actualiza cantidad_entregada del detalle y, si todos completos, venta → entregado.

### 8.6 Inventario: alertas de stock

- GET /api/inventario/alertas con sucursal_id y umbral (default 0). Devuelve productos activos con stock_actual ≤ umbral. Vista /inventario/alertas muestra tabla con producto, sucursal, stock actual, unidad.

### 8.7 Caja y gastos

- Caja: una abierta por sucursal. POST /api/cajas/apertura (sucursal_id, monto_inicial). POST /api/cajas/corte (caja_id, monto_final opcional) cierra y puede devolver reporte X/Z. Gastos: GET/POST /api/cajas/{caja}/gastos solo si caja está abierta (descripcion, monto). Vista /cajas permite abrir caja, ver caja abierta, listar gastos, registrar gasto, cerrar caja.

### 8.8 Ticket impreso

- GET /api/ventas/{venta}/ticket-contenido devuelve JSON con texto plano del ticket (nombre empresa, folio, fecha/hora, sucursal, líneas producto–cantidad–unidad, total). El front envía ese contenido a POST /api/impresion/imprimir-raw con impresora_id para imprimir en impresora térmica.

### 8.9 Reportes de ventas

- Estadísticas: GET /api/reportes/ventas/estadisticas (fecha_desde, fecha_hasta, sucursal_id). Exportar Excel (CSV base64): GET reportes/ventas/exportar-excel. Exportar PDF: GET reportes/ventas/exportar-pdf. Tickets Villahermosa (CSV con URLs de fotos): GET reportes/ventas/tickets-villahermosa-excel (ventas con ticket_origen_uuid no nulo y estatus entregado).

---

## 9. Reglas de negocio resumidas

- **Boletos:** Un boleto solo puede validarse una vez (estatus pendiente → utilizado). Folio único; QR con folio y timestamp.
- **Ventas:** Folio único VTA-...; estatus pendiente → parcial (entregas) → entregado; o cancelado. Al cancelar se devuelve al inventario lo no entregado.
- **Inventario:** Productos por sucursal; stock se descuenta al vender y se devuelve al cancelar (solo lo no entregado). Alertas: productos con stock ≤ umbral.
- **Caja:** Una caja abierta por sucursal; gastos solo en caja abierta.
- **Entregas:** Actualizan cantidad_entregada por detalle; cuando todos los detalles están completos, venta pasa a entregado.
- **Impresión:** Impresoras en configuracion_hardware (tipo impresora); imprimir-raw envía contenido por socket a IP/puerto; ticket de venta se genera desde ticket-contenido.

---

## 10. Dónde tocar al aplicar cambios

| Cambio | Archivos / Ubicación |
|--------|----------------------|
| Boletos (SCV) | `App\Models\Boleto`, `BoletoController` (store, validar, capturarFoto). |
| Ventas, cancelar, ticket | `App\Models\Venta`, `VentaDetalle`, `VentaController` (store, importarPedido, cancelar, ticketContenido). |
| Entregas | `App\Models\Entrega`, `EntregaController`; modelo VentaDetalle (cantidad_entregada). |
| Inventario | `App\Models\Producto`, `InventarioController` (ajustar, alertas). |
| Caja y gastos | `App\Models\Caja`, `Gasto`, `CajaController`, `GastoController`. |
| Reportes ventas / PDF / Villahermosa | `ReporteController` (estadisticasVentas, exportarVentasExcel, exportarVentasPdf, exportarTicketsVillahermosaExcel); vista `resources/views/reportes/ventas-pdf.blade.php`. |
| Impresión | `ImpresionController` (imprimirRaw); configuración en Hardware. |
| Roles y permisos | Migraciones de permisos/roles; frontend: `use-permissions.js`, `router/index.js`, `sidebar.vue`. |
| Menú y rutas frontend | `router/index.js`, `components/layout/sidebar.vue`; vistas en `resources/js/src/views/`. |
| Configuración empresa | `ConfiguracionEmpresaController`, `ConfiguracionEmpresa` (nombre en ticket y PDF). |

Con este nivel de detalle se pueden aplicar cambios de forma consistente en migraciones, backend y frontend.
