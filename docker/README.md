# Configuración Docker para Laravel 10 + Vue 3

Esta carpeta contiene todos los archivos de configuración de Docker para el proyecto.

## Estructura

- `Dockerfile` - Imagen PHP 8.1-Apache con extensiones necesarias para Laravel
- `apache/000-default.conf` - Configuración del servidor web Apache
- `php/local.ini` - Configuración de PHP (límites de upload, memoria)

## Uso

### 1. Configurar archivo .env

Asegúrate de tener un archivo `.env` con la siguiente configuración de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=root
```

**Importante**: El `DB_HOST` debe ser `mysql` (nombre del servicio en docker-compose) para que Laravel pueda conectarse a la base de datos dentro de la red de Docker.

### 2. Iniciar los contenedores

**Nota**: Las dependencias de Composer y npm se instalarán automáticamente al iniciar los contenedores.

Desde la carpeta `docker/`:

```bash
docker-compose up -d --build
```

O desde la raíz del proyecto:

```bash
cd docker
docker-compose up -d --build
```

### 3. Instalar dependencias manualmente (opcional)

Si necesitas instalar o actualizar las dependencias manualmente:

#### Instalar dependencias de Composer:
```bash
docker-compose exec apache composer install
```

#### Instalar dependencias de npm:
```bash
docker-compose exec node npm install
```

O ejecutar ambos a la vez:
```bash
docker-compose exec apache composer install
docker-compose exec node npm install
```

### 4. Generar clave de aplicación (si no existe)

```bash
docker-compose exec apache php artisan key:generate
```

**Nota**: Los permisos de storage y las migraciones se ejecutan automáticamente al iniciar el contenedor Apache.

## Servicios

- **apache**: Contenedor PHP 8.1-Apache con Laravel (puerto 8000)
- **mysql**: Base de datos MySQL 8.0 (puerto 3306)
- **node**: Contenedor Node.js para Vite (puerto 5173)

## Acceso

- **Aplicación web**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173
- **MySQL**: localhost:3306

## Comandos útiles

### Ver logs
```bash
docker-compose logs -f
```

### Acceder al contenedor de la aplicación
```bash
docker-compose exec apache bash
```

### Instalar/Actualizar dependencias

#### Composer (dentro del contenedor Apache)
```bash
docker-compose exec apache composer install
docker-compose exec apache composer update
```

#### npm (dentro del contenedor Node)
```bash
docker-compose exec node npm install
docker-compose exec node npm update
```

#### Reinstalar todo desde cero
```bash
# Eliminar vendor y node_modules
docker-compose exec apache rm -rf vendor
docker-compose exec node rm -rf node_modules

# Reinstalar
docker-compose exec apache composer install
docker-compose exec node npm install
```

### Detener contenedores
```bash
docker-compose down
```

### Reconstruir contenedores
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Limpiar volúmenes
```bash
docker-compose down -v
```

