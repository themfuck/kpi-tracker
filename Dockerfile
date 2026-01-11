# --- Production Runtime ---
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
    sqlite-dev \
    composer

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

# Copy Application Code (vendor already included from local)
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Supervisor (runs Nginx + PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
