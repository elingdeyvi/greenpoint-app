# Análisis: Menú vs nueva lógica de negocio

## 1. Resumen de la lógica de negocio actual

El sistema hoy combina:

- **SCV (legacy):** Control de salidas de volteos — boletos, generar/verificar, reportes de salidas.
- **Ventas e inventario:** Dos sucursales (Villahermosa, Macuspana), ventas con ticket/QR, entregas parciales, cancelar ventas, inventario por producto/sucursal, alertas de stock bajo.
- **Caja y gastos:** Apertura/cierre de caja por sucursal, registro de gastos por caja.
- **Reportes:** Salidas (boletos), ventas (estadísticas, Excel, PDF), tickets entregados Villahermosa (Excel con fotos).
- **Impresión:** Configuración de impresoras (Hardware), impresión de ticket de venta (contenido vía API).

**Permisos relevantes (backend):**

| Módulo        | Permisos |
|---------------|----------|
| Dashboard     | `dashboard.ver` |
| Operaciones   | `operaciones.generar`, `operaciones.verificar`, `operaciones.consultar` |
| Administración| `administracion.usuarios`, `administracion.roles`, `administracion.configuracion_hardware`, `administracion.catalogos` |
| Ventas        | `ventas.crear`, `ventas.ver`, `ventas.cancelar`, `ventas.entregar` |
| Entregas      | `entregas.registrar` |
| Productos     | `productos.ver`, `productos.administrar` |
| Inventario    | `inventario.consultar`, `inventario.ajustar` |
| Cajas         | `cajas.abrir_cerrar` |
| Gastos        | `gastos.registrar` |
| Reportes      | `reportes.consultar`, `reportes.consultar_propios` |

---

## 2. Estado actual del menú (sidebar)

| Sección         | Ítems actuales                           | Permiso(s) para ver |
|-----------------|-------------------------------------------|----------------------|
| **Dashboard**   | Dashboard                                 | `dashboard.ver` |
| **Operaciones** | Generar Boleto, Verificar Boleto, Lista de Boletos | `operaciones.*` |
| **Administración** | Usuarios, Roles, Hardware, Catálogos (Tipos Carga, Destinos, Placas) | `administracion.*` |
| **Ventas**      | Nueva venta / Lista, Dashboard de entrega | `ventas.crear` / `ventas.ver` / `entregas.registrar` |
| **Reportes**    | Salidas (boletos), Ventas                 | `reportes.consultar` / `reportes.consultar_propios` |
| **Mi Perfil**   | (enlace directo)                          | Sin permiso |

---

## 3. Desalineaciones detectadas

### 3.1 Ventas

- **Falta en menú:** "Importar pedido" (Macuspana). La ruta `/ventas/importar-pedido` existe en el router pero no aparece en el sidebar. Quien trabaje en Macuspana no tiene un acceso claro para importar el pedido escaneado desde Villahermosa.
- **Acción cancelar:** El permiso `ventas.cancelar` existe en backend; no es un ítem de menú sino una acción (botón) en lista/detalle de ventas. Debe seguir visible solo si el usuario tiene `ventas.cancelar`.

### 3.2 Inventario

- **No hay ítem de menú:** No existe entrada para inventario ni para alertas de stock. Las APIs existen (`/api/inventario/alertas`, `/api/inventario/ajustar`).
- **Permisos:** `inventario.consultar`, `inventario.ajustar` (Gerente de Producción / Admin) no tienen reflejo en el menú.
- **Consecuencia:** No hay forma desde el menú de ver “material agotado” o stock bajo, ni de gestionar inventario.

### 3.3 Cajas y gastos

- **No hay ítem de menú:** No existe sección ni entrada para Caja ni para Gastos. Las APIs existen: cajas (listar, abrir, cerrar, caja abierta), `GET/POST /api/cajas/{caja}/gastos`.
- **Permisos:** `cajas.abrir_cerrar`, `gastos.registrar` no tienen entrada en el menú.
- **Consecuencia:** No hay acceso desde el menú para abrir/cerrar caja ni para registrar gastos por caja.

### 3.4 Reportes

- **Solo dos enlaces:** "Salidas (boletos)" y "Ventas". Correcto como agrupación.
- **Dentro de Ventas:** Las acciones "Exportar PDF", "Exportar Excel" y "Tickets Villahermosa (Excel)" son propias de la vista de reporte de ventas, no necesitan ítem aparte en el menú; basta que la vista de Reportes > Ventas las ofrezca.

### 3.5 Administración

- **Catálogos:** Incluye Tipos de Carga, Destinos, Placas Frecuentes (orientados a boletos). Con la nueva lógica también existen **Sucursales** y **Productos** (para ventas/inventario); no aparecen en el menú. Las rutas y vistas para productos/sucursales habría que confirmar o crear.
- **Configuración de empresa:** Existe API y repositorio en front; no hay ruta en el router ni entrada en el sidebar. Si la empresa debe editar nombre, logo, etc. desde la app, falta esa pantalla y su enlace (p. ej. bajo Administración).

### 3.6 Operaciones (boletos)

- **Coherente con SCV:** Generar/Verificar/Lista de Boletos sigue alineado con la lógica legacy. Si se mantiene el módulo de salidas de volteos, el menú está bien.

---

## 4. Resumen de gaps

| Gap                              | Severidad | Acción recomendada |
|----------------------------------|-----------|--------------------|
| Sin entrada "Importar pedido" en Ventas | Media     | Añadir en sidebar bajo Ventas (y comprobar permiso). |
| Sin menú Inventario / alertas de stock  | Alta     | Añadir sección o ítem "Inventario" (alertas; opcional ajuste) con `inventario.consultar` / `inventario.ajustar`. |
| Sin menú Cajas / Gastos          | Alta     | Añadir sección "Caja" o "Caja y gastos" con ítems para caja abierta, gastos, según `cajas.abrir_cerrar` y `gastos.registrar`. |
| Productos/Sucursales no en Catálogos    | Media   | Si existen vistas, añadirlas bajo Administración > Catálogos. |
| Configuración empresa sin vista/menú     | Baja    | Si aplica, crear vista y enlace bajo Administración. |
| Acción "Cancelar" venta                 | N/A    | Mantener como acción en UI; comprobar que se muestre solo con `ventas.cancelar`. |

---

## 5. Recomendación de estructura de menú (alineada a la lógica)

- **Dashboard** — Sin cambios.
- **Operaciones** — Sin cambios (Generar/Verificar/Lista de Boletos) si se mantiene SCV.
- **Ventas**
  - Nueva venta / Lista
  - **Importar pedido** (Macuspana) → `/ventas/importar-pedido`
  - Dashboard de entrega
- **Inventario** (nuevo, si `inventario.consultar` o `inventario.ajustar`)
  - Alertas de stock / Material agotado → vista que use `GET /api/inventario/alertas`
  - (Opcional) Ajuste de inventario → si existe vista, con `inventario.ajustar`
- **Caja** (nuevo, si `cajas.abrir_cerrar` o `gastos.registrar`)
  - Caja (abrir/cerrar, ver caja abierta)
  - Gastos (registro por caja; puede ser misma vista con pestaña o sección)
- **Reportes**
  - Salidas (boletos)
  - Ventas (con opciones PDF, Excel, Tickets Villahermosa en la propia vista)
- **Administración**
  - Usuarios, Roles, Hardware
  - Catálogos: Tipos de Carga, Destinos, Placas Frecuentes; y si aplica **Productos**, **Sucursales**
  - (Opcional) Configuración de empresa → si se añade vista
- **Mi Perfil** — Sin cambios.

Este documento sirve como referencia para ajustar el menú y las rutas del frontend a la nueva lógica de negocio.
