# GreenPoint — Laravel 12 + Vue 3 + AdminLTE 4

CMS / panel administrativo de GreenPoint migrado desde `greenpoint-app` (Laravel 10 SPA) a esta versión:

- **Laravel 12** + autenticación Breeze (sesión)
- **Vue 3** + **Inertia.js**
- **AdminLTE 4** (Bootstrap 5)
- **Spatie Permission** (roles Administrador / Capturista)
- API pública JSON en `/api/public/*` para el sitio web

## Requisitos

- PHP **8.2+**
- Composer
- Node.js **20+**
- Extensiones PHP: `mbstring`, `openssl`, `pdo_sqlite` (o MySQL), `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

## Instalación

```bash
composer install
cp .env.example .env   # si aún no tienes .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

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

## API pública

| Método | Ruta |
|--------|------|
| GET | `/api/public/home` |
| GET | `/api/public/servicios` |
| GET | `/api/public/clientes` |
| GET | `/api/public/galeria` |
| GET | `/api/public/contactos` |
| GET | `/api/public/pagina-nosotros\|historia\|tecnologia\|aviso` |
| POST | `/api/public/formulario-contacto` |

## Notas de migración

- Se omitió código legacy de caseta/caja/ventas del proyecto anterior.
- El panel ya no usa Vue Router + Sanctum tokens: usa Inertia + sesión.
- La UI AdminLTE de la plantilla reemplaza el theme SPA anterior.
- Zona horaria: `America/Mexico_City`. Locale: `es`.
