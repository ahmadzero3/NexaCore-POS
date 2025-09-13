# Use the official PHP-FPM image with Alpine Linux for a smaller image size
FROM php:8.2-fpm-alpine

# Create www-data user with proper UID/GID to match host system
RUN if ! getent group www-data > /dev/null 2>&1; then addgroup -g 1000 -S www-data; fi && \
    if ! getent passwd www-data > /dev/null 2>&1; then adduser -u 1000 -D -S -G www-data www-data; fi

# Install system dependencies and PHP extensions
# These extensions are commonly required by Laravel and its packages
RUN apk add --no-cache \
    nginx \
    postgresql-dev \ 
    postgresql-client \
    nodejs \
    npm \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    libwebp-dev \
    libxml2-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    gmp-dev \
    libpq \
    libxslt-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    zip \
    gd \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    intl \
    xml \
    soap \
    gmp \
    pdo_pgsql \
    xsl \
    && docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --prefer-dist || true

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Create necessary directories and set proper permissions for Laravel
RUN mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Create entrypoint script to fix permissions on startup
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo '# Fix permissions for Laravel directories' >> /entrypoint.sh && \
    echo 'if [ "$(id -u)" = "0" ]; then' >> /entrypoint.sh && \
    echo '    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo '    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo '    # Fix permissions for installed file if it exists' >> /entrypoint.sh && \
    echo '    if [ -f /var/www/html/storage/installed ]; then' >> /entrypoint.sh && \
    echo '        chown www-data:www-data /var/www/html/storage/installed 2>/dev/null || true' >> /entrypoint.sh && \
    echo '        chmod 664 /var/www/html/storage/installed 2>/dev/null || true' >> /entrypoint.sh && \
    echo '    fi' >> /entrypoint.sh && \
    echo 'fi' >> /entrypoint.sh && \
    echo 'exec "$@"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Copy PHP config
COPY php.ini /usr/local/etc/php/

ENTRYPOINT ["/entrypoint.sh"]

# Start PHP-FPM
CMD ["php-fpm"]
