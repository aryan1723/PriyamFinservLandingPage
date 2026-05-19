FROM php:8.4-fpm-bookworm

# ── System dependencies ────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl zip unzip nginx supervisor \
        libpng-dev libonig-dev libxml2-dev libzip-dev \
        libpq-dev libfreetype6-dev libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ─────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo pdo_mysql pdo_pgsql \
        mbstring exif pcntl bcmath zip gd opcache

# ── Composer ───────────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── App source ─────────────────────────────────────────────────────────────────
WORKDIR /var/www

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install \
        --optimize-autoloader \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-scripts

# Copy the rest of the app
COPY . .

# Run composer scripts now that the full app is present
RUN composer run-script post-autoload-dump || true

# Storage / bootstrap permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ── Nginx config ───────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# ── Supervisor config ──────────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Startup script ─────────────────────────────────────────────────────────────
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
