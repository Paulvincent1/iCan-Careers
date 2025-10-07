# Stage 1 - Backend (Laravel + PHP + Composer)
FROM php:8.2-fpm AS backend

WORKDIR /var/www

RUN apt-get update && apt-get install -y git unzip libpq-dev libonig-dev libzip-dev zip supervisor \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Stage 2 - Frontend (Vite)
FROM node:18 AS frontend

WORKDIR /app
COPY --from=backend /var/www .
RUN npm install
RUN npm run build

# Stage 3 - Final
FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y supervisor

# Copy backend and frontend build
COPY --from=backend /var/www .
COPY --from=frontend /app/public/build ./public/build

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Render's port
ENV PORT=10000
EXPOSE $PORT

# Run migrations then start Supervisor
CMD php artisan migrate --force && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
