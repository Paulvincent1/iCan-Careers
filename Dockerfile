# Stage 1 - Backend (Composer first)
FROM php:8.2-fpm AS backend
WORKDIR /var/www/html
COPY . .
RUN apt-get update && apt-get install -y git unzip libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Stage 2 - Frontend (depends on vendor)
FROM node:18 AS frontend
WORKDIR /app
COPY --from=backend /var/www/html ./
RUN npm install
RUN npm run build

# Stage 3 - Final
FROM php:8.2-fpm
WORKDIR /var/www/html
COPY --from=backend /var/www/html .
COPY --from=frontend /app/public/dist ./public/dist
