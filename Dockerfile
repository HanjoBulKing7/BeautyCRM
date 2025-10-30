FROM php:8.2-cli

# Instalar dependencias necesarias para Laravel y DomPDF
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd exif intl bcmath mbstring dom


# Establecer directorio de trabajo
WORKDIR /var/www/html

# ✅ Ya no copiamos composer.json ni ejecutamos composer install

# Copiar el resto de la aplicación
COPY ./src .

# Generar autoload optimizado (opcional, por si lo quieres refrescar)
RUN composer dump-autoload --optimize || true

# Permisos correctos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
