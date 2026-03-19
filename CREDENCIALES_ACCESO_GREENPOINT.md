# Credenciales de acceso (GreenPoint)

Este documento resume los usuarios base creados/actualizados por el seeder `RolesAndPermissionsSeeder` (ejecutado dentro de `DatabaseSeeder`).

## Contraseña por defecto

La contraseña definida por defecto para estos usuarios es:

`12345678`

## Usuarios y roles

### 1) Administrador (panel administrativo completo)

- **Email:** `admin@greenpoint.com`
- **Contraseña:** `12345678`
- **Rol:** `Administrador`

### 2) Capturista (operativo para catálogos/módulos)

- **Email:** `capturista@greenpoint.com`
- **Contraseña:** `12345678`
- **Rol:** `Capturista`

## Re-generar (si fuera necesario)

Para recrear/actualizar estos usuarios y sus roles/permisos:

```bash
php artisan db:seed
```

