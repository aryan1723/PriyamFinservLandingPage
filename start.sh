#!/bin/bash
set -e

cd /var/www/html

echo "[boot] Writing .env..."
cat > .env << ENVEOF
APP_NAME="Priyam Finserv"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL:-https://priyamfinserv.up.railway.app}

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
ENVEOF

echo "[boot] Running migrations..."
php artisan migrate --force --no-interaction

echo "[boot] Setting up storage..."
php artisan storage:link --force 2>/dev/null || true

echo "[boot] Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[boot] Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Handle Railway PORT env var - update Apache to listen on it
if [ -n "$PORT" ] && [ "$PORT" != "80" ]; then
    echo "[boot] Setting Apache to listen on PORT=$PORT"
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-enabled/*.conf
fi

echo "[boot] ✓ Starting Apache..."
exec apache2-foreground
