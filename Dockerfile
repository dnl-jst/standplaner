# Multi-stage Dockerfile für Production
FROM php:8.3-fpm-alpine AS base

# Installiere System-Dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        intl \
        opcache

# Installiere Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Erstelle User für Anwendung
RUN addgroup -g 1000 app && adduser -D -s /bin/sh -u 1000 -G app app

# Arbeitsverzeichnis setzen
WORKDIR /var/www/html

# Kopiere composer files zuerst (für besseres Caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Kopiere Anwendungscode
COPY . .

# Setze Berechtigungen
RUN chown -R app:app /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p var/cache var/log \
    && chown -R app:app var \
    && chmod -R 777 var

# Führe Composer-Scripts aus
RUN composer dump-autoload --optimize --no-dev

# PHP-FPM Konfiguration
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini

# Nginx Konfiguration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Supervisor Konfiguration
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Startup Script
COPY docker/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Expose Port
EXPOSE 80

# Health Check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Wechsle zu app user
USER app

# Start Command
CMD ["/usr/local/bin/startup.sh"]
