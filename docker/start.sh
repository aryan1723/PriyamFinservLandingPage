#!/bin/bash
set -e

echo "============================================"
echo "  Priyam Finserv — Production Boot Script  "
echo "============================================"

cd /var/www

# ── 1. Write .env from injected environment variables ─────────────────────────
echo "[boot] Writing .env from environment..."
cat > .env <<EOF
APP_NAME="${APP_NAME:-Priyam Finserv}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://priyamfinserv.onrender.com}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DATABASE_URL=${DATABASE_URL}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-smtp.gmail.com}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Priyam Finserv}"

LOG_CHANNEL=stack
LOG_LEVEL=error
EOF

echo "[boot] .env written."

# ── 2. Clear stale caches ─────────────────────────────────────────────────────
php artisan config:clear  2>/dev/null || true
php artisan cache:clear   2>/dev/null || true
php artisan route:clear   2>/dev/null || true
php artisan view:clear    2>/dev/null || true

# ── 3. Run database migrations ────────────────────────────────────────────────
echo "[boot] Running migrations..."
php artisan migrate --force --no-interaction

# ── 4. Storage symlink ────────────────────────────────────────────────────────
echo "[boot] Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# ── 5. Cache for performance ──────────────────────────────────────────────────
echo "[boot] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 6. Fix permissions ────────────────────────────────────────────────────────
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "[boot] ✓ Boot complete — starting Nginx + PHP-FPM via Supervisor"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
