FROM node:20-alpine AS assets

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js .
RUN npm run build

FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libzip-dev libicu-dev unzip git \
    && docker-php-ext-install pdo_pgsql mbstring bcmath intl zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
