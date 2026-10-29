#!/usr/bin/env bash
set -e

cd /var/www/html

# 1. Crear archivo .env si no existe
if [ ! -f ".env" ]; then
    echo "Creando archivo .env desde .env.example..."
    cp .env.example .env
fi

# 2. Instalar dependencias de Composer si no existe la carpeta vendor
if [ ! -f "vendor/autoload.php" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 3. Generar APP_KEY si no está definida
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force --ansi
fi

# 4. Ajustar permisos de escritura en storage y cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 5. Esperar a que MySQL/MariaDB esté disponible y ejecutar migraciones + seeders
if [ "${DB_CONNECTION}" = "mysql" ]; then
    echo "Esperando conexión con la base de datos (${DB_HOST:-mysql}:${DB_PORT:-3306})..."
    MAX_RETRIES=30
    COUNT=0
    until php -r "new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'mysql') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '');" >/dev/null 2>&1; do
        COUNT=$((COUNT + 1))
        if [ $COUNT -ge $MAX_RETRIES ]; then
            echo "No se pudo conectar a MySQL después de ${MAX_RETRIES} intentos."
            break
        fi
        sleep 2
    done

    echo "Ejecutando migraciones y seeders iniciales..."
    php artisan migrate --seed --force --ansi || true
fi

echo "Aplicación lista en http://localhost:${APP_PORT:-8080} (Swagger: http://localhost:${APP_PORT:-8080}/swagger/index.html)"
exec "$@"
