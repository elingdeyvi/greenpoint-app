#!/bin/bash
set -e

echo "🚀 Iniciando contenedor Apache..."

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté disponible..."
until nc -z mysql 3306; do
    echo "MySQL no está disponible aún - esperando..."
    sleep 1
done

echo "✅ MySQL está disponible"

# Instalar dependencias de Composer si no existen
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --optimize-autoloader --no-interaction
else
    echo "✅ Dependencias de Composer ya instaladas"
fi

# Configurar permisos
echo "🔐 Configurando permisos..."
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true

# Ejecutar migraciones si la base de datos está lista
echo "🗄️  Verificando base de datos..."
php artisan migrate --force || echo "⚠️  No se pudieron ejecutar las migraciones"

echo "✨ Contenedor Apache listo!"

# Ejecutar el comando original
exec "$@"

