# Análisis de Lógica de Negocio – Proyecto GreenPoint (Laravel 10 + Vue 3)

> Comentario: Este archivo resume la lógica de negocio actual (backend y frontend) para facilitar la modificación o eliminación de módulos.

---

## 1. Visión general

- **Backend (Laravel 10)**  
  - Autenticación vía Sanctum y roles/permisos con Spatie.  
  - API de administración protegida bajo `auth:sanctum`.  
  - API pública para el sitio web bajo `/api/public/*`.  
  - Lógica de archivos centralizada en `ImageService` y servicios específicos para módulos.

- **Frontend (Vue 3, Composition API)**  
  - Panel admin (rutas protegidas con permisos).  
  - Sitio público (rutas sin auth, consumen `/api/public/*`).  
  - Repositorios en `resources/js/src/repositories/`.  
  - Composables en `resources/js/src/composables/`.

---

## 2. Backend – Módulos y responsabilidades

### 2.1 Autenticación, usuarios y roles

- **Controladores**
  - `app/Http/Controllers/TokenController.php`  
    - `createToken`: login, valida email/password, genera token Sanctum y devuelve:
      - `token` (string)  
      - `user` con `id`, `name`, `email`, `roles`, `permissions`.  
    - `expire`: expira tokens (logout).  
    - Flujo de recuperación de contraseña (`createResetPW`, `saveResetPW`, `getUserUuid`).
  - `app/Http/Controllers/UserController.php`  
    - CRUD de usuarios, actualización de perfil y contraseña.
  - `app/Http/Controllers/RoleController.php`  
    - Listado de roles y permisos, asignación de permisos a roles.

- **Modelos / permisos**
  - `app/Models/User.php` + migraciones base (`create_users_table`, `add_estatus_to_users_table`).  
  - Permisos y roles gestionados por Spatie (`create_permission_tables.php`).
  - Seeder `database/seeders/RolesAndPermissionsSeeder.php`:
    - Rol **Administrador** con todos los permisos.  
    - Rol **Capturista** con permisos de catálogos, módulos administrables y solo lectura de formularios de contacto.  
    - Usuario admin por defecto `admin@greenpoint.com`.

- **Rutas API**
  - `routes/api.php`  
    - `/api/registro` (registro usuario).  
    - `/api/tokens/*` (login/reset/logout/permissions).  
    - Grupo `auth:sanctum` para `/users/*` y `/roles/*` protegido con `permission:administracion.usuarios` y `permission:administracion.roles`.

**Qué modificar/eliminar**  
- Si se cambia el sistema de roles/permisos:
  - Ajustar `RolesAndPermissionsSeeder`, `RoleController` y los middlewares `permission:*` en `routes/api.php`.  
- Si se elimina la gestión de usuarios/roles desde el panel:
  - Eliminar/ajustar rutas `/users/*`, `/roles/*` y las vistas admin de usuarios/roles (frontend).

---

### 2.2 Catálogos (Servicios, Clientes, Galería, Banners, Contactos, Redes Sociales)

- **Modelos**
  - `Servicio` (`app/Models/Servicio.php`) – tabla `servicios`.  
  - `Cliente` (`app/Models/Cliente.php`) – tabla `clientes`.  
  - `Galeria` (`app/Models/Galeria.php`) – tabla `galeria`.  
  - `Banner` (`app/Models/Banner.php`) – tabla `banners`.  
  - `Contacto` (`app/Models/Contacto.php`) – tabla `contactos`.  
  - `RedSocial` (`app/Models/RedSocial.php`) – tabla `redes_sociales`.

- **Controladores**
  - `ServicioController`, `ClienteController`, `GaleriaController`, `BannerController`, `ContactoController`, `RedSocialController`.  
  - Métodos: `index` (paginado), `store`, `show`, `update`, `destroy`.  
  - Subida de imágenes (donde aplica) delegada a `ImageService`.

- **FormRequests**
  - `Store/UpdateServicioRequest`, `Store/UpdateClienteRequest`, `Store/UpdateGaleriaRequest`, `Store/UpdateBannerRequest`, `Store/UpdateContactoRequest`, `Store/UpdateRedSocialRequest`.

- **Rutas API admin (todas bajo `auth:sanctum`)**
  - `Route::apiResource('servicios', ServicioController::class)->middleware('permission:catalogos.servicios');`  
  - `Route::apiResource('clientes', ClienteController::class)->middleware('permission:catalogos.clientes');`  
  - `Route::apiResource('galeria', GaleriaController::class)->middleware('permission:catalogos.galeria');`  
  - `Route::apiResource('banners', BannerController::class)->middleware('permission:catalogos.banners');`  
  - `Route::apiResource('contactos', ContactoController::class)->middleware('permission:catalogos.contactos');`  
  - `Route::apiResource('redes-sociales', RedSocialController::class)->middleware('permission:catalogos.redes_sociales');`

**Qué modificar/eliminar**  
- Para desactivar un catálogo completo:
  - Eliminar rutas apiResource correspondientes en `routes/api.php`.  
  - Eliminar/ajustar controlador y modelo si ya no se usan.  
  - Quitar repositorio + composable + vistas Vue relacionadas (ver secciones 3 y 4).

---

### 2.3 Configuración general y formularios de contacto

- **Modelos**
  - `Configuracion` (`configuracion`): clave-valor.  
  - `FormularioContacto` (`formularios_contacto`): mensajes recibidos desde el sitio público.

- **Controladores**
  - `ConfiguracionController`:
    - `index`: lista todas las claves/valores.  
    - `update`: actualiza múltiples claves mediante `items: [{ clave, valor }, ...]`.  
  - `FormularioContactoController` (admin):
    - `index`: listado paginado, ordenado por `leido` y `created_at`.  
    - `show`: detalle.  
    - `update`: marcar como leído (`leido` boolean).

- **Rutas API**
  - `GET/PUT /api/configuracion` (admin, `administracion.configuracion_critica`).  
  - `/api/formularios-contacto/*` (admin, `formularios_contacto.ver`).

**Qué modificar/eliminar**  
- Si cambian las claves de configuración:  
  - Solo ajustar lógica de frontend que las usa y, si se desea, documentar las nuevas claves en la vista de configuración.  
- Para desactivar la bandeja de mensajes:
  - Eliminar rutas `formularios-contacto` y la vista admin correspondiente; el endpoint público de envío puede mantenerse o no según necesidades.

---

### 2.4 Módulos administrables (Nosotros, Historia, Tecnología, Aviso)

- **Modelos**  
  - Nosotros: `PaginaNosotros`, `PaginaNosotrosImagen`, `PaginaNosotrosProgreso`.  
  - Historia: `PaginaHistoria`, `PaginaHistoriaEvento`, `PaginaHistoriaImagen`.  
  - Tecnología: `PaginaTecnologia`, `PaginaTecnologiaSeccion`.  
  - Aviso: `PaginaAviso`, `PaginaAvisoSeccion`, `PaginaAvisoLista`.

- **Servicios**
  - `PaginaNosotrosService`: sincroniza campos de `pagina_nosotros` + hijos (imágenes, progreso) a partir de un payload completo.
  - *Planes similares* para Historia, Tecnología y Aviso (según se implementen).

- **Controladores admin**
  - `PaginaNosotrosController`:
    - `show`: devuelve la única fila de `pagina_nosotros` con `imagenes` y `progreso`.  
    - `update`: recibe estructura completa y delega a `PaginaNosotrosService`.
  - (Controllers para Historia/Tecnología/Aviso se implementan con el mismo patrón).

- **Rutas API admin**
  - `GET/PUT /api/pagina-nosotros` (`modulos.nosotros`).  
  - `GET/PUT /api/pagina-historia` (`modulos.historia`).  
  - `GET/PUT /api/pagina-tecnologia` (`modulos.tecnologia`).  
  - `GET/PUT /api/pagina-aviso` (`modulos.aviso`).

**Qué modificar/eliminar**  
- Para desactivar un módulo:
  - Eliminar rutas correspondientes en `routes/api.php`.  
  - Dejar o eliminar modelos y servicios según se requiera (si no se usará más).  
  - Quitar vistas admin y públicas asociadas (frontend).

---

### 2.5 API pública del sitio

- **Controlador** `PublicSiteController`
  - `home`: devuelve `banners` activos y `servicios` activos.  
  - `serviciosIndex` / `serviciosShow`: listado y detalle de servicios.  
  - `clientesIndex`: clientes activos.  
  - `galeriaIndex`: galería activa.  
  - `contactosIndex`: contactos ordenados.  
  - `paginaNosotros`, `paginaHistoria`, `paginaTecnologia`, `paginaAviso`: devuelven estructuras completas de las páginas administrables.  
  - `enviarFormularioContacto`: valida y guarda un mensaje en `formularios_contacto`.

- **Rutas API públicas (`routes/api.php`)**
  - `/api/public/home`  
  - `/api/public/servicios`, `/api/public/servicios/{servicio}`  
  - `/api/public/clientes`  
  - `/api/public/galeria`  
  - `/api/public/contactos`  
  - `/api/public/pagina-nosotros`  
  - `/api/public/pagina-historia`  
  - `/api/public/pagina-tecnologia`  
  - `/api/public/pagina-aviso`  
  - `POST /api/public/formulario-contacto` (con `throttle`).

**Qué modificar/eliminar**  
- Para cambiar el contenido mostrado públicamente sin tocar el panel:
  - Modificar la lógica de consulta en `PublicSiteController` (por ejemplo, filtros adicionales).  
- Para desactivar una sección pública:
  - Eliminar o cambiar las rutas específicas (`/public/...`) y las vistas/composables del frontend que las consumen.

---

### 2.6 Gestión de imágenes y archivos

- **Servicio** `App\Services\ImageService`
  - **Subida:** `storeImage(UploadedFile $file, string $directory): string` — guarda en disco `public` y devuelve la ruta relativa (ej. `banners/xyz.jpg`). Si en `config/image.php` están definidos `max_width` o `max_height` (vía `IMAGE_MAX_WIDTH` / `IMAGE_MAX_HEIGHT` en `.env`), las imágenes que superen esos tamaños se redimensionan (jpeg, png, gif, webp) antes de guardar.
  - **Eliminación:** `deleteImage(?string $path): void` — borra el archivo del disco si existe. Se invoca al reemplazar una imagen (update) o al eliminar el registro (destroy).
  - **URL pública:** `urlFor(?string $path): string` — devuelve la URL absoluta (opcional; el frontend suele construir la URL con la base + `/storage/` + path).

- **Uso por módulo**
  - **Catálogos:** `BannerController`, `ServicioController`, `ClienteController` (logo), `GaleriaController` — en store/update suben con `ImageService::storeImage`, en update (reemplazo) y destroy llaman `deleteImage`.
  - **Módulos administrables:** `PaginaNosotrosService`, `PaginaHistoriaService`, `PaginaTecnologiaService` — imagen destacada e imágenes múltiples; al actualizar o eliminar items de imagen llaman `deleteImage` y guardan con `storeImage`.

- **URLs utilizables desde el frontend**
  - El API devuelve siempre la **ruta relativa** (ej. `galeria/abc.jpg`, `servicios/def.png`). El frontend debe construir la URL final como: `baseUrl + '/storage/' + path`, donde `baseUrl` es la raíz del backend (p. ej. `import.meta.env.VITE_API_URL` sin el sufijo `/api`, o `window.location.origin` si la app y el API comparten origen). El enlace simbólico `public/storage` → `storage/app/public` se crea con `php artisan storage:link`.

---

## 3. Frontend – Repositorios y composables (panel admin)

### 3.1 Repositorios admin (`resources/js/src/repositories/`)

- Auth / administración:
  - `AuthRepository.js`, `RoleRepository.js`, `UserRepository.js`.

- Catálogos:
  - `ServicioRepository.js` → `/api/servicios`.  
  - `ClienteCatalogoRepository.js` → `/api/clientes`.  
  - `GaleriaRepository.js` → `/api/galeria`.  
  - `BannerRepository.js` → `/api/banners`.  
  - `ContactoRepository.js` → `/api/contactos`.  
  - `RedSocialRepository.js` → `/api/redes-sociales`.  
  - `ConfiguracionRepository.js` → `/api/configuracion`.  
  - `FormularioContactoRepository.js` → `/api/formularios-contacto/*`.

- Módulos administrables:
  - `PaginaNosotrosRepository.js` → `/api/pagina-nosotros`.  
  - `PaginaHistoriaRepository.js` → `/api/pagina-historia`.  
  - `PaginaTecnologiaRepository.js` → `/api/pagina-tecnologia`.  
  - `PaginaAvisoRepository.js` → `/api/pagina-aviso`.

**Qué modificar/eliminar**  
- Eliminar un módulo de panel implica borrar el repositorio correspondiente y actualizar cualquier composable/vista que lo use.

---

### 3.2 Composables admin (`resources/js/src/composables/`)

- Utilidades:
  - `use-meta.js` (meta tags), `use-permissions.js` (carga y chequeo de permisos).

- Catálogos:
  - `useServicio.js`, `useCliente.js`, `useGaleria.js`, `useBanner.js`, `useContacto.js`, `useRedSocial.js`, `useFormularioContacto.js`.  
  - Cada uno expone `items`, `currentItem`, `loading`, `error` y métodos `fetchList`, `fetchById`, `create`, `update`, `deleteItem` (o `markAsRead` en formularios).

**Qué modificar/eliminar**  
- Quitar un catálogo en el admin:
  - Eliminar su composable, su repositorio y ajustar las vistas/router/menú que lo referencian.

---

## 4. Frontend – Repositorios y composables (sitio público)

### 4.1 Repositorio público

- `PublicSiteRepository.js`
  - `getHome`, `getServicios`, `getServicioById`, `getClientes`, `getGaleria`, `getContactos`.  
  - `getPaginaNosotros`, `getPaginaHistoria`, `getPaginaTecnologia`, `getPaginaAviso`.  
  - `sendContacto` (POST a `/api/public/formulario-contacto`).

### 4.2 Composables públicos

- `usePublicHome.js` – Home (banners + servicios).  
- `usePublicServicios.js` – lista y detalle de servicios.  
- `usePublicClientes.js` – clientes.  
- `usePublicGaleria.js` – galería.  
- `usePublicContactos.js` – contactos.  
- `usePublicNosotros.js`, `usePublicHistoria.js`, `usePublicTecnologia.js`, `usePublicAviso.js` – módulos administrables.

**Qué modificar/eliminar**  
- Para desactivar una sección pública:
  - Eliminar o ajustar el método en `PublicSiteRepository`, el composable respectivo y la vista/ruta que lo consume.

---

## 5. Rutas Vue y vistas (estado actual)

- **Router (`resources/js/src/router/index.js`)**  
  - Rutas admin existentes:
    - `/dashboard` (requiere permiso `dashboard.ver`).  
    - `/auth/login` y rutas auth.  
    - `/users/profile`, `/users/lista` (usuarios).  
    - `/roles/lista` (roles).  
  - Rutas públicas GreenPoint **pendientes de implementar**:
    - Home, Nosotros, Historia, Servicios, Clientes, Galería, Tecnología, Contacto, Aviso de Privacidad.

- **Vistas actuales relevantes**
  - Auth: `views/auth/*.vue`.  
  - Admin: `views/dashboard.vue`, `views/users/*.vue`, `views/roles/index.vue`.  
  - Demo/base: `views/index.vue`, `views/index2.vue`, `views/widgets.vue`, `views/components/*.vue`.  
  - Vistas específicas de GreenPoint (panel y público) aún deben crearse según las especificaciones de los prompts.

**Qué modificar/eliminar**  
- Cualquier cambio en la navegación del panel o del sitio público pasará por:
  - Actualizar `router/index.js` y `components/layout/sidebar.vue` (panel).  
  - Crear/editar un layout público (header/footer) y las vistas públicas en `views/public`.

---

## 6. Resumen para decidir qué modificar/eliminar

1. **Eliminar completamente un módulo de negocio (catálogo o página):**
   - Backend: modelo(s), controlador(es), service(s) y rutas asociadas en `routes/api.php`.  
   - Frontend admin: repositorio, composable, vistas y entradas de menú/router.  
   - Frontend público (si aplica): métodos en `PublicSiteRepository`, composables públicos y vistas/rutas.

2. **Modificar únicamente el comportamiento o estructura de datos:**
   - Ajustar migraciones futuras (nuevas), modelos y servicios correspondientes.  
   - Actualizar controladores y repositorios que consumen esos modelos.  
   - Alinear formularios Vue (admin/público) con los nuevos campos.

3. **Ajustar permisos (qué rol ve qué cosa):**
   - Editar `RolesAndPermissionsSeeder` (permisos asignados a roles).  
   - Ajustar middlewares `permission:*` en `routes/api.php`.  
   - Adaptar el menú del panel (`sidebar.vue`) para mostrar/ocultar entradas según `use-permissions`.

Este archivo sirve como mapa de qué piezas de backend y frontend intervienen en cada área funcional (auth, catálogos, módulos, sitio público) para poder planear limpiezas o cambios de negocio sin romper el resto del sistema.+

