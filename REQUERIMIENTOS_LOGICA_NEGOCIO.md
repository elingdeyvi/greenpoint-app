# 📋 REQUERIMIENTOS PARA ESTABLECER LA LÓGICA DE NEGOCIO
## Sistema de Ventas con Tickets - Materiales de Construcción


## 🎯 INFORMACIÓN GENERAL

### 1. Sucursales
- [ ] ¿Cuántas sucursales o puntos de venta tienen?
- [ ] ¿Cada sucursal manejará su propio inventario o será centralizado?
- [ ] ¿Las ventas se registrarán por sucursal? (cada sucursal tiene sus propias ventas)

---

## 📦 INVENTARIO Y UNIDADES DE MEDIDA

### 2. Unidades de Medida para Ventas
- [ ] ¿Cómo medirán sus ventas? (Marque las que apliquen)
  - [ ] Volteo tamaño mediano
  - [ ] Volteo tamaño grande
  - [ ] Metros cúbicos (m³)
  - [ ] Toneladas
  - [ ] Piezas
  - [ ] Kilogramos (kg)
  - [ ] Metros lineales
  - [ ] Metros cuadrados (m²)
  - [ ] Otros: _______________

### 3. Control de Inventario
- [ ] ¿Necesitan control de inventario? (saber cuántas unidades hay en existencia)
- [ ] ¿Qué unidades de medida usarán para el inventario? (debe coincidir con las unidades de venta)
- [ ] ¿El sistema debe avisar cuando un material se agota?
- [ ] ¿Necesitan un nivel mínimo de inventario? (cuando llegue a X unidades, avisar que hay que comprar más)

---

## 🛍️ PRODUCTOS Y SERVICIOS

### 4. Tipos de Materiales o Servicios
- [ ] ¿Qué venderán? (Marque los que apliquen)
  - [ ] Materiales físicos (cemento, varilla, block, arena, grava, etc.)
  - [ ] Servicios de construcción (albañilería, plomería, electricidad, transporte, etc.)
  - [ ] Ambos (materiales y servicios)

### 5. Si venden Materiales
- [ ] ¿Qué tipos de materiales manejan? (Ejemplo: cemento, varilla, block, arena, grava, tubería, material eléctrico, etc.)
- [ ] ¿Los materiales tienen códigos de barras o códigos internos?
- [ ] ¿Necesitan categorías para organizar materiales? (Ejemplo: estructurales, acabados, herramientas, etc.)

### 6. Si venden Servicios
- [ ] ¿Qué tipos de servicios ofrecen? (Ejemplo: albañilería, plomería, electricidad, demolición, transporte de materiales, etc.)
- [ ] ¿Cómo se medirán los servicios? (por hora, por metro cuadrado, por proyecto, por volteo, etc.)

---

## 👥 CLIENTES

### 7. Catálogo de Clientes
- [ ] ¿Necesitan catálogo de clientes? (registrar información de los clientes)
- [ ] ¿Qué información de clientes necesitan registrar?
  - [ ] Nombre
  - [ ] Dirección
  - [ ] Teléfono
  - [ ] Email
  - [ ] RFC (si aplica)
  - [ ] Otros: _______________
- [ ] ¿Algunos clientes tendrán descuentos especiales?

---

## 💰 VENTAS Y PAGOS

### 8. Proceso de Venta
- [ ] ¿Cómo será el flujo de una venta? (seleccionar material/servicio → cantidad → cliente → forma de pago → generar ticket con QR)
- [ ] ¿El ticket con código QR será para la entrega de material? (el cliente presenta el QR para recibir el material, similar a como el Vigilante valida los boletos actuales)
- [ ] ¿El ticket tendrá estatus como en el sistema actual? (Pendiente → Entregado/Utilizado)
- [ ] ¿Quién valida la entrega del material? (¿el rol Vigilante/Entrega escanea el QR y marca como entregado?)
- [ ] ¿Se pueden cancelar o devolver ventas? ¿Bajo qué condiciones?
- [ ] ¿Necesitan facturación electrónica (CFDI) o solo tickets?

### 9. Formas de Pago
- [ ] ¿Qué formas de pago aceptarán?
  - [ ] Efectivo
  - [ ] Tarjeta de crédito
  - [ ] Tarjeta de débito
  - [ ] Transferencia bancaria
  - [ ] Cheque
  - [ ] Crédito (pagar después)
  - [ ] Otros: _______________
- [ ] ¿Pueden combinar formas de pago en una sola venta? (Ejemplo: parte en efectivo, parte con tarjeta)
- [ ] ¿Necesitan registrar el cambio que se da al cliente? (cuando pagan en efectivo)

### 10. Precios
- [ ] ¿Los materiales/servicios tienen un precio fijo o pueden variar?
- [ ] ¿Necesitan diferentes precios para diferentes clientes? (precio de mayoreo vs menudeo)
- [ ] ¿Aplicarán descuentos? ¿Cómo? (porcentaje, cantidad fija, por volumen de compra)
- [ ] ¿Los precios incluyen impuestos o se agregan después?

### 11. Impuestos
- [ ] ¿Aplican impuestos a las ventas? (IVA, IEPS, etc.)
- [ ] ¿Qué porcentaje de impuestos aplican?
- [ ] ¿Todos los productos tienen el mismo porcentaje de impuesto o varía?

---

## 🧾 TICKETS Y COMPROBANTES

### 12. Información en el Ticket
- [ ] ¿Qué información debe aparecer en el ticket impreso?
  - [ ] Nombre de la empresa
  - [ ] Dirección y teléfono
  - [ ] Número de ticket (folio) - **generado automáticamente como en el sistema actual**
  - [ ] Fecha y hora
  - [ ] Lista de materiales/servicios con cantidades y precios
  - [ ] Unidad de medida (volteo mediano, volteo grande, m³, etc.)
  - [ ] Subtotal
  - [ ] Descuentos
  - [ ] Impuestos
  - [ ] Total
  - [ ] Forma de pago
  - [ ] Nombre del cliente
  - [ ] Nombre del vendedor/despachador (usuario que genera el ticket)
  - [ ] Código QR (para entrega de material) - **generado automáticamente como en el sistema actual**
  - [ ] Foto del material/camión (si aplica, como en el sistema actual)
  - [ ] Otros: _______________

### 13. Impresión de Tickets
- [ ] ¿Qué tipo de impresora usarán? (térmica de 80mm, impresora normal, etc.)
- [ ] ¿El ticket se imprime automáticamente o el usuario debe presionar un botón?

---

## 💵 CORTE DE CAJA

### 14. Apertura y Cierre de Caja
- [ ] ¿Cada cajero tiene su propia caja registradora?
- [ ] ¿Al iniciar el día, el cajero debe hacer una "apertura de caja" con dinero inicial?
  - [ ] ¿Cuánto dinero inicial debe tener?
  - [ ] ¿Quién autoriza la apertura?
- [ ] ¿Al final del día, el cajero debe hacer un "corte de caja"?
  - [ ] ¿Qué información debe mostrar el corte? (ventas totales, efectivo esperado, efectivo real, diferencias)
  - [ ] ¿Quién autoriza el cierre?
- [ ] ¿Pueden hacer cortes parciales durante el día? (cortes de caja a mediodía, por ejemplo)

### 15. Movimientos de Efectivo
- [ ] ¿Pueden hacer retiros de efectivo durante el día? (sacar dinero de la caja)
- [ ] ¿Pueden hacer depósitos de efectivo durante el día? (agregar dinero a la caja)
- [ ] ¿Necesitan registrar gastos de la caja? (Ejemplo: compra de material de oficina)
- [ ] ¿Estos movimientos requieren autorización?

---

## 📊 REPORTES

### 16. Reportes Necesarios
- [ ] ¿Qué reportes necesitan?
  - [ ] Ventas del día (similar al reporte de boletos actual)
  - [ ] Ventas por período (semana, mes, año)
  - [ ] Ventas por sucursal
  - [ ] Materiales/servicios más vendidos
  - [ ] Clientes que más compran
  - [ ] Inventario actual
  - [ ] Materiales con poco inventario
  - [ ] Corte de caja diario
  - [ ] Ventas por vendedor/despachador (similar a "usuario generador" en el sistema actual)
  - [ ] Ventas por forma de pago
  - [ ] Ventas a crédito y saldos pendientes
  - [ ] Tickets pendientes de entrega (similar a boletos pendientes)
  - [ ] Tickets entregados (similar a boletos utilizados)
  - [ ] Trazabilidad completa de tickets (quién generó, quién entregó, fecha, etc.)
  - [ ] Otros: _______________

### 17. Exportación de Datos
- [ ] ¿Necesitan exportar reportes a Excel?
- [ ] ¿Necesitan exportar reportes a PDF?

---

## 👤 USUARIOS Y PERMISOS

### 18. Roles de Usuario (Basados en la estructura actual del sistema)
El sistema actual tiene los siguientes roles. Indique si los mantendrá o necesita modificarlos:

- [ ] **Administrador** (acceso total)
  - [ ] ¿Mantener este rol?
  - [ ] ¿Qué funciones específicas debe tener? (gestión de usuarios, configuración, reportes completos, etc.)
  
- [ ] **Despachador/Vendedor** (genera tickets de venta)
  - [ ] ¿Mantener este rol o renombrarlo? (Ejemplo: "Vendedor", "Cajero")
  - [ ] ¿Qué funciones debe tener?
    - [ ] Generar tickets de venta
    - [ ] Capturar foto (si aplica)
    - [ ] Imprimir tickets
    - [ ] Consultar solo sus propias ventas
    - [ ] Abrir/cerrar caja
    - [ ] Otros: _______________

- [ ] **Vigilante/Entrega** (valida entrega de material con QR)
  - [ ] ¿Mantener este rol o renombrarlo? (Ejemplo: "Entrega", "Almacenista")
  - [ ] ¿Qué funciones debe tener?
    - [ ] Verificar tickets con código QR
    - [ ] Validar entrega de material
    - [ ] Marcar ticket como "entregado"
    - [ ] Otros: _______________

- [ ] ¿Necesitan agregar roles adicionales?
  - [ ] Supervisor (puede ver reportes y autorizar)
  - [ ] Almacenista (solo inventarios)
  - [ ] Otros: _______________

### 19. Permisos Específicos
- [ ] ¿Qué operaciones requieren autorización especial o permisos específicos?
  - [ ] Cancelar ventas (¿qué rol puede hacerlo?)
  - [ ] Aplicar descuentos mayores a cierto porcentaje (¿qué rol puede hacerlo?)
  - [ ] Modificar precios (¿qué rol puede hacerlo?)
  - [ ] Hacer ajustes de inventario (¿qué rol puede hacerlo?)
  - [ ] Abrir/cerrar caja (¿qué rol puede hacerlo?)
  - [ ] Ver reportes completos (¿qué rol puede hacerlo?)
  - [ ] Gestionar usuarios y roles (¿solo Administrador?)
  - [ ] Configurar hardware (cámara, impresora) (¿solo Administrador?)
  - [ ] Otros: _______________

---

## 🔧 CONFIGURACIÓN Y HARDWARE

### 20. Equipos y Dispositivos
- [ ] ¿Necesitan cámara para capturar algo? (ya tienen cámara IP configurada en el sistema actual)
  - [ ] ¿Para qué usarán la cámara? (Ejemplo: evidencia de entrega de material, foto del camión/material antes de generar ticket)
  - [ ] ¿La foto será obligatoria antes de generar el ticket? (como en el sistema actual de volteos)

---

## ⚠️ REGLAS DE NEGOCIO ESPECIALES

### 21. Validaciones Importantes
- [ ] ¿Hay restricciones que el sistema debe validar?
  - [ ] No permitir ventas si no hay inventario suficiente
  - [ ] No permitir ventas si el cliente tiene crédito vencido
  - [ ] No permitir descuentos mayores a cierto porcentaje sin autorización
  - [ ] Validar que el código QR solo se pueda usar una vez para entrega (similar a como los boletos actuales pasan de "Pendiente" a "Utilizado")
  - [ ] Validar que el ticket esté en estatus "Pendiente" antes de permitir la entrega
  - [ ] Otros: _______________

### 22. Casos Especiales
- [ ] ¿Hay alguna regla de negocio especial que debamos considerar?
  - [ ] Ejemplo: "Los clientes frecuentes tienen 5% de descuento"
  - [ ] Ejemplo: "Ventas mayores a X cantidad requieren autorización"
  - [ ] Ejemplo: "El código QR del ticket solo es válido por X días"
  - [ ] Otros: _______________

---

## 📝 NOTAS ADICIONALES

### 23. Información Complementaria
- [ ] ¿Hay algo más que debamos saber sobre cómo funciona su negocio?
- [ ] ¿Tienen algún proceso manual actual que quieren automatizar?

