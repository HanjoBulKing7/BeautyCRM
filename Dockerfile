FROM php:8.2-cli

# Instalar dependencias necesarias para Laravel y DomPDF
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libicu-dev g++ \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd exif intl bcmath mbstring dom

# Instalar Composer globalmente [cite: 1, 2]
RUN curl -sS https://getcomposer.org/installer | php -- \
    && mv composer.phar /usr/local/bin/composer [cite: 2]

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# ==========================================================
# ⚡️ SECCIÓN MODIFICADA PARA ARREGLAR EL TIMEOUT Y OPTIMIZAR
# ==========================================================

# Copiar solo los archivos de configuración de Composer necesarios para la instalación
COPY composer.json composer.lock ./

# **Ajuste Clave:** Aumentar el límite de tiempo de espera (process-timeout) de Composer a 1000 segundos (o a 0 para ilimitado, aunque 1000 es más seguro).
RUN composer config process-timeout 1000

# **Mejora:** Instalar las dependencias AHORA (en la capa de construcción).
# Esto acelera el inicio del contenedor y aprovecha la caché de Docker.
ENV COMPOSER_PROCESS_TIMEOUT=600
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copiar el resto del código fuente (esto debe ir DESPUÉS de composer install)
# Si usas Docker Compose, esta línea puede no ser necesaria si usas Bind Mounts
# (que es el caso en tu docker-compose.yml), pero es buena práctica para imágenes finales.
# Si estás usando Bind Mounts, este paso se ignora a favor del volumen.
# Si no usas Bind Mounts, debes agregar: COPY . .

# ==========================================================

# Exponer el puerto del servidor PHP
EXPOSE 8000

# Comando por defecto (Solo iniciar el servidor, las dependencias ya están instaladas)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]