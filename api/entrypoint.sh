#!/bin/bash
set -e

# Wait for database connection
echo "Waiting for database connection..."
until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }"; do
  sleep 1
done
echo "Database is ready!"

# Install vendor if volume is empty
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Ensure storage directories exist with write permissions
mkdir -p /var/www/media
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R 777 storage bootstrap/cache /var/www/media

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Seed admin user
echo "Bootstrapping initial admin user..."
php artisan app:bootstrap-admin

exec "$@"
