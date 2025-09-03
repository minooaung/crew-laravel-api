#!/bin/bash
set -e

# Default DB variables (fallback to environment or docker-compose values)
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

# Number of retries for waiting DB
RETRIES=30

echo "Waiting for database connection at $DB_HOST:$DB_PORT..."

# Wait until MySQL is ready and credentials work
until mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1; do
    RETRIES=$((RETRIES-1))
    if [ $RETRIES -le 0 ]; then
        echo "Database not reachable with credentials, exiting..."
        exit 1
    fi
    echo "Waiting for MySQL... ($RETRIES retries left)"
    sleep 5
done

echo "Database is ready!"

# Clear caches (optional, safe to run every container start)
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations & seed only if DB is empty
if ! php artisan tinker --execute="DB::table('users')->first()" >/dev/null 2>&1; then
    echo "Running migrations and seeders..."
    php artisan migrate --seed --force
else
    echo "Database already has data. Skipping migrations."
fi

# Ensure proper permissions for storage and cache
chown -R www-data:www-data storage bootstrap/cache

# Start Apache in foreground (or pass arguments)
exec docker-php-entrypoint "$@"
