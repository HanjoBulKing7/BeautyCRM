FROM php:8.2-cli

# Instalar dependencias necesarias INCLUYENDO GIT
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd exif intl bcmath mbstring dom

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

# Primero copiar TODO el código
COPY . .

# Luego instalar dependencias (ahora los scripts de Laravel funcionarán)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]