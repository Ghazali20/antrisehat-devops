#!/bin/sh
set -e

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Tunggu database MySQL siap menerima koneksi
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Menunggu koneksi MySQL di ${DB_HOST}:${DB_PORT}..."
    until php -r "new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
        sleep 2
    done
    echo "MySQL siap! Menjalankan migrasi dan seeder..."
    php artisan migrate --force
    php artisan db:seed --force || true
fi

exec "$@"
