#!/bin/bash
set -e

echo "============================================"
echo "  Priyam Finserv — Production Boot Script"
echo "============================================"

cd /var/www

# ── 1. Generate APP_KEY if missing ────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "[boot] No APP_KEY set — generating one..."
    php artisan key:generate --force
fi

# ── 2. Create .env from environment variables (Render injects them directly) ──
# Laravel reads from $_ENV / getenv() — no .env file needed on Render.
# But if no .env exists, create a minimal one so Laravel doesn't error.
if [ ! -f .env ]; then
    echo "[boot] No .env found — creating minimal .env from environment..."
    cat > .env <<EOF
APP_NAME="${APP_NAME:-Priyam Finserv}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_URL=${DATABASE_URL}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-smtp.gmail.com}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Priyam Finserv}"
EOF
fi

# ── 3. Clear any stale cache ─────────────────────────────────────────────────
echo "[boot] Clearing caches..."
php artisan config:clear
# php artisan cache:clear
php artisan route:clear
php artisan view:clear

# ── 4. Run database migrations ───────────────────────────────────────────────
echo "[boot] Running migrations..."
php artisan migrate --force --no-interaction

# ── 5. Create storage symlink ────────────────────────────────────────────────
echo "[boot] Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# ── 6. Cache config/routes/views for performance ────────────────────────────
echo "[boot] Caching config, routes & views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 7. Fix permissions ───────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "[boot] ✓ Boot complete. Starting services..."

# ── 8. Start Supervisor (Nginx + PHP-FPM) ───────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
