# GreenPoint

Sitio web corporativo y panel de administración para GreenPoint (comunicaciones, internet satelital). Backend Laravel 10 + API; frontend Vue 3 (sitio público y panel admin).

---

## Requisitos

- **PHP** 8.1 o superior (extensions: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml)
- **Laravel** 10.x
- **Node.js** 16+ y **npm** (o yarn/pnpm) para el frontend
- **MySQL** 5.7+ o **MariaDB** 10.3+
- **Composer** 2.x

---

## Instalación

1. **Clonar / entrar al proyecto**
   ```bash
   cd greenpoint-app
   ```

2. **Dependencias PHP**
   ```bash
   composer install
   ```

3. **Variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Editar `.env`: configurar `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `APP_URL`. Opcional: `VITE_API_URL` (ej. `http://localhost:8000/api`) para el frontend.

4. **Base de datos**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   El seeder crea roles (Administrador, Capturista), permisos y usuario admin por defecto (`admin@greenpoint.com` / ver seeders).

5. **Enlace de storage (imágenes)**
   ```bash
   php artisan storage:link
   ```

6. **Dependencias frontend**
   ```bash
   npm install
   ```

7. **(Opcional) Imágenes desde cotizaciones**  
   Para poblar banners, galería, clientes e iconos desde la carpeta `cotizaciones`:
   - En `.env`: `COTIZACIONES_PATH=../cotizaciones` (o la ruta correcta).
   - Ejecutar **antes** de seed si quiere rutas ya usadas por los seeders:  
     `php artisan app:import-cotizaciones-images`  
   Luego `php artisan db:seed`. Si no se ejecuta el comando, las rutas en BD pueden ser placeholders hasta subir imágenes por el panel.

---

## Ejecución

- **API (backend)**  
  ```bash
  php artisan serve
  ```
  Por defecto: `http://localhost:8000`. La API está bajo `/api` (p. ej. `/api/tokens/create` para login, `/api/public/home` para el sitio público).

- **Frontend (dev)**  
  ```bash
  npm run dev
  ```
  Vite sirve el frontend (por defecto en otro puerto, p. ej. 5173). Asegurar que `VITE_API_URL` en `.env` apunte a la URL base del backend (ej. `http://localhost:8000/api`).

- **Build para producción**
  ```bash
  npm run build
  php artisan serve
  ```
  Los assets compilados se sirven desde `public/`.

---

## Documentación de referencia

- **Arquitectura y reglas de código:** [.cursorrules](.cursorrules)
- **Lógica de negocio y mapa de módulos:** [ANALISIS_LOGICA_NEGOCIO.md](ANALISIS_LOGICA_NEGOCIO.md)
- **Alcance y tiempos:** ver `cotizaciones/COTIZACION_LARAVEL_VUE.md`

---

## Estructura de módulos (resumen)

- **Catálogos (panel admin):** Servicios, Clientes, Galería, Banners, Contactos, Redes sociales. CRUD vía API con permisos `catalogos.*`.
- **Módulos administrables:** Nosotros, Historia, Tecnología, Aviso de Privacidad. Edición por estructura completa (show/update) con permisos `modulos.nosotros`, `modulos.historia`, etc.
- **API pública (sin auth):** `/api/public/*` — home, servicios, clientes, galería, contactos, páginas (nosotros, historia, tecnologia, aviso), configuración pública, formulario de contacto.
- **Configuración y formularios:** Configuración general (clave-valor), bandeja de mensajes de contacto (permiso `formularios_contacto.ver`).

Roles: **Administrador** (todos los permisos), **Capturista** (catálogos, módulos, solo lectura de formularios de contacto; sin usuarios ni configuración crítica).

---

## Sitio público (Vue) y paleta

El frontend incluye un sitio público en las rutas `/`, `/nosotros`, `/historia`, `/aviso`, `/servicios`, `/clientes`, `/galeria`, `/tecnologia`, `/contacto/tabasco`, `/contacto/veracruz`, `/contacto/carmen`, con layout propio (Header/Footer).

**Paleta de colores (referencia cotizaciones):**

| Uso           | Valor     |
|---------------|-----------|
| Color primario   | `#f3663f` |
| Color secundario | `#24C373` |

Clases CSS del tema: `.gp-btn-primary`, `.gp-btn-secondary`, `.gp-text-primary`, `.gp-text-secondary`, variables en `resources/js/src/assets/sass/public-site.scss`.

- **Carrusel (Home):** Bootstrap 5 Carousel.
- **Galería:** Lightbox con modal Bootstrap 5.
- **Animaciones al scroll:** opcionales (p. ej. AOS).

---

## Imágenes desde cotizaciones (detalle)

Para usar imágenes del proyecto hermano `cotizaciones` (banners, clientes, galería, iconos de servicios):

1. En `.env`: `COTIZACIONES_PATH=../cotizaciones` (por defecto se usa `../cotizaciones`).
2. Ejecutar: `php artisan app:import-cotizaciones-images`.
3. El comando copia a `storage/app/public/` y crea el enlace `public/storage` si no existe. Los seeders guardan rutas como `banners/banner-01.jpg`, `icons/icon-01.png`, etc.

Para tener imágenes reales en los catálogos, ejecute el comando **antes** de `php artisan db:seed`. Si no, las rutas en BD pueden ser válidas pero los archivos no existirán hasta copiarlos o subirlos por el panel.

---

## Tests

```bash
php artisan test
```
Los tests Feature usan SQLite en memoria (`phpunit.xml`). Ver `tests/Feature/ModulosAdministrablesApiTest.php`, `PublicEndpointsApiTest.php`, `PermissionsApiTest.php`.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
