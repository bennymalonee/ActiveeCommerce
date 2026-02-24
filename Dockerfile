# Stage 1: Build frontend assets (Debian base: prebuilt node-sass + Python/build deps if needed)
FROM node:18-bookworm-slim AS frontend

RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 make g++ ca-certificates \
    && rm -rf /var/lib/apt/lists/*
ENV PYTHON=/usr/bin/python3

WORKDIR /app

COPY package.json ./
RUN npm install --legacy-peer-deps

COPY webpack.mix.js ./
COPY resources ./resources/

RUN npm run production && ls -la public/js public/css public/mix-manifest.json


# Stage 2: PHP app with nginx
FROM php:8.2-fpm-bookworm

# Install system deps and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    gd \
    intl \
    bcmath \
    opcache \
    fileinfo \
    exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer deps (composer.lock optional)
COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# App code
COPY . .

# Bring in built frontend assets
COPY --from=frontend /app/public/js ./public/js
COPY --from=frontend /app/public/css ./public/css
COPY --from=frontend /app/public/mix-manifest.json ./public/mix-manifest.json


RUN composer dump-autoload --optimize

# Laravel storage/cache dirs
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx + PHP-FPM config
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
