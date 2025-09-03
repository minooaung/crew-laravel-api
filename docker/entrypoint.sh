#!/bin/bash
set -e
set -o pipefail

DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
RETRIES=20

echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
until mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" >/dev/null 2>&1
do
  RETRIES=$((RETRIES-1))
  if [ $RETRIES -le 0 ]; then
    echo "Database not reachable, exiting..."
    exit 1
  fi
  echo "Waiting for MySQL... ($RETRIES retries left)"
  sleep 5
done

echo "Database is up!"

# Check if migrations table exists
if ! php artisan migrate:status >/dev/null 2>&1; then
  echo "Running migrations and seeders..."
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan migrate --seed --force
else
  echo "Database already migrated. Skipping."
fi

# Start Apache
exec docker-php-entrypoint "$@"
