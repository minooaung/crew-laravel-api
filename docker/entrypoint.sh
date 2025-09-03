#!/bin/bash
set -e

# Wait until MySQL is ready
echo "Waiting for database connection..."
until nc -z -v -w30 db 3306
do
  echo "Waiting for MySQL..."
  sleep 5
done

echo "Database is up!"

# Check if 'users' table exists
if ! php artisan tinker --execute="DB::table('users')->first()" >/dev/null 2>&1; then
  echo "Running migrations and seeders..."
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan migrate --seed --force
else
  echo "Database already migrated. Skipping."
fi

# Start PHP-FPM (or Apache depending on your image)
exec docker-php-entrypoint "$@"