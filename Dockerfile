FROM php:8.2-cli

# Instalar dependencias necesarias para Laravel y DomPDF
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd exif intl bcmath mbstring dom

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- \
    && mv composer.phar /usr/local/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto del servidor PHP
EXPOSE 8000

# Comando por defecto (Composer instala dependencias si hacen falta)
CMD sh -c "composer install --no-interaction --prefer-dist --optimize-autoloader && php artisan serve --host=0.0.0.0 --port=8000"
