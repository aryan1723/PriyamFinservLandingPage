#!/bin/bash
set -e

cd /var/www

# Force HTTPS — override any http:// value Railway may inject
export APP_URL="https://priyamfinservlandingpage-production.up.railway.app"
export ASSET_URL="https://priyamfinservlandingpage-production.up.railway.app"

echo "=== Writing .env ==="
cat > .env <<ENVEOF
APP_NAME="Priyam Finserv"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=https://priyamfinservlandingpage-production.up.railway.app
ASSET_URL=https://priyamfinservlandingpage-production.up.railway.app

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DATABASE_URL=${DATABASE_URL}

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=${MAIL_USERNAME:-priyamfinserve@gmail.com}
MAIL_PASSWORD="${MAIL_PASSWORD}"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-priyamfinserve@gmail.com}
MAIL_FROM_NAME="Priyam Finserv"

LOG_CHANNEL=errorlog
LOG_LEVEL=error

ADMIN_EMAIL=${ADMIN_EMAIL:-admin@priyamfinserv.com}
ADMIN_NAME=${ADMIN_NAME:-Admin}
ADMIN_PASSWORD=${ADMIN_PASSWORD:-changeme123}
ENVEOF

echo "=== Migrating database ==="
php artisan migrate --force --no-interaction

echo "=== Seeding admin account ==="
php artisan db:seed --class=AdminSeeder --force --no-interaction

echo "=== Storage link ==="
php artisan storage:link --force 2>/dev/null || true

echo "=== Clearing old caches ==="
php artisan view:clear
php artisan cache:clear
php artisan config:clear

echo "=== Caching ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Fixing permissions ==="
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

PORT=${PORT:-8080}
echo "=== Starting PHP server on port $PORT ==="
exec php artisan serve --host=0.0.0.0 --port="$PORT"
