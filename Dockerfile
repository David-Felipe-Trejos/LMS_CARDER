FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libxml2-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php -S 0.0.0.0:$PORT -t public"]