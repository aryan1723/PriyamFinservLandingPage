FROM php:8.4-cli-bookworm

# ── System dependencies (PHP + Node.js) ───────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl zip unzip gnupg \
        libpng-dev libonig-dev libxml2-dev libzip-dev \
        libpq-dev libfreetype6-dev libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# ── Node.js 20 ────────────────────────────────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
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

# PHP deps first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist --no-scripts

# Node deps
COPY package.json package-lock.json ./
RUN npm ci

# Full app copy
COPY . .

# Build Vite assets
RUN npm run build

# Post-install composer scripts
RUN composer run-script post-autoload-dump 2>/dev/null || true

# ── Permissions ────────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ── Startup ────────────────────────────────────────────────────────────────────
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
