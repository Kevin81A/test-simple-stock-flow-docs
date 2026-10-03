FROM php:8.2-cli-alpine

RUN apk add --no-cache bash mysql-client git libzip-dev \
    && docker-php-ext-install pdo_mysql bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

RUN mkdir -p /var/www/media \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 777 storage bootstrap/cache /var/www/media

EXPOSE 8000

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
