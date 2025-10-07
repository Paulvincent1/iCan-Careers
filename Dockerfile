# Stage 1 - Backend (Laravel + PHP + Composer)
FROM php:8.2-fpm AS backend

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y git unzip libpq-dev libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Stage 2 - Frontend (Vite)
FROM node:18 AS frontend

WORKDIR /app

# Copy Laravel files including vendor (from backend stage)
COPY --from=backend /var/www .

# Install frontend dependencies
RUN npm install

# Build frontend assets
RUN npm run build

# Stage 3 - Final
FROM php:8.2-fpm

WORKDIR /var/www

# Copy Laravel backend
COPY --from=backend /var/www .

# Copy built frontend
COPY --from=frontend /app/public/build ./public/build

# Expose Render's port
ENV PORT=10000
EXPOSE $PORT

# Start Laravel built-in server
CMD php artisan serve --host=0.0.0.0 --port=$PORT
