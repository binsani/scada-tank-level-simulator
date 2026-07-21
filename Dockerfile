# Stage 1: Build frontend assets
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci || npm install
COPY . .
RUN npm run build || mkdir -p public/build

# Stage 2: PHP Application setup
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
COPY --from=node-builder /app/public/build /var/www/html/public/build

RUN composer install --no-interaction --no-progress --prefer-dist --no-dev --optimize-autoloader || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]

