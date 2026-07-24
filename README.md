# GreenPoint — Laravel 12 + Vue 3 + AdminLTE 4

CMS / panel administrativo de GreenPoint migrado desde `greenpoint-app` (Laravel 10 SPA) a esta versión:

- **Laravel 12** + autenticación Breeze (sesión)
- **Vue 3** (Composition API / `<script setup>`) en **todo** el front: admin + sitio público + auth
- **Inertia.js** (`@inertiajs/vue3`) — sin Vue Router; las rutas viven en Laravel
- **AdminLTE 4** (Bootstrap 5) en el panel
- **Spatie Permission** (roles Administrador / Capturista)
- API pública JSON en `/api/public/*` para el sitio web

## Requisitos

- PHP **8.2+**
- Composer
- Node.js **20+**
- MySQL / MariaDB (base `greenpoint`, utf8mb4)
- Extensiones PHP: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

## Instalación

```bash
composer install
cp .env.example .env   # si aún no tienes .env
php artisan key:generate
# Crear BD MySQL: CREATE DATABASE greenpoint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

`.env` usa MySQL por defecto (`DB_CONNECTION=mysql`, `DB_DATABASE=greenpoint`).

En otra terminal (hot reload):

```bash
npm run dev
```

Abre: http://127.0.0.1:8000

## Credenciales

| Email | Password | Rol |
|-------|----------|-----|
| `admin@greenpoint.com` | `admin123456` | Administrador |
| `admin@admin.com` | `password` | Administrador |

## Módulos admin (`/admin/*`)

- Catálogos: Servicios, Clientes, Galería, Banners, Contactos, Redes sociales
- Páginas CMS: Nosotros, Historia, Tecnología, Aviso de privacidad
- Mensajes de contacto, Configuración, Usuarios, Roles

## Sitio público (web)

| Método | Ruta | Nombre |
|--------|------|--------|
| GET | `/` | `public.home` |
| GET | `/nosotros` | `public.nosotros` |
| GET | `/historia` | `public.historia` |
| GET | `/servicios` | `public.servicios.index` |
| GET | `/servicios/{servicio}` | `public.servicios.show` |
| GET | `/clientes` | `public.clientes` |
| GET | `/galeria` | `public.galeria` |
| GET | `/tecnologia` | `public.tecnologia` |
| GET | `/contacto` | `public.contacto` |
| POST | `/contacto` | `public.contacto.store` |
| GET | `/aviso-de-privacidad` | `public.aviso` |

Panel admin: `/login` → `/dashboard` → `/admin/*`

## API pública

| Método | Ruta |
|--------|------|
| GET | `/api/public/home` |
| GET | `/api/public/servicios` |
| GET | `/api/public/servicios/{id}` |
| GET | `/api/public/clientes` |
| GET | `/api/public/galeria` |
| GET | `/api/public/contactos` |
| GET | `/api/public/redes-sociales` |
| GET | `/api/public/pagina-nosotros\|historia\|tecnologia\|aviso` |
| POST | `/api/public/formulario-contacto` |

## Notas de migración

- Se omitió código legacy de caseta/caja/ventas del proyecto anterior.
- El panel ya no usa Vue Router + Sanctum tokens: usa Inertia + sesión.
- El sitio público es Inertia (páginas en `resources/js/Pages/Public/`), no el SPA Axios del origen.
- La API `/api/public/*` se mantiene para consumidores externos.
- Zona horaria: `America/Mexico_City`. Locale: `es`.
