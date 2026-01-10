# --- 1. Build Frontend Assets ---
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* vite.config.js ./
RUN npm ci
COPY resources ./resources
COPY public ./public
# Copy other necessary files if any
COPY . .
RUN npm run build

# --- 2. Build Backend Dependencies ---
FROM composer:2.6 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Install dependencies without scripts to avoid needing the full codebase yet
RUN composer install --no-dev --no-interaction --prefer-dist --ignore-platform-reqs --optimize-autoloader --no-scripts

# --- 3. Production Runtime ---
FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

# Install system dependencies
# - nginx: Web server
# - supervisor: Process manager
# - icu-dev / intl: Required by Filament/Laravel
# - libpng-dev, etc: For image processing (GD)
RUN apk add --no-cache \
    nginx \
    supervisor \
    icu-dev \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    git \
    linux-headers \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    pdo_sqlite \
    zip \
    gd \
    bcmath \
    opcache

# Configure Nginx and Supervisor
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Copy Application Code
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Final PHP setup
# Run dump-autoload to generate optimized class maps including the scripts now that code is present
RUN php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Supervisor (runs Nginx + PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
