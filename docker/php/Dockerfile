FROM php:8.3-fpm

# Dependencias del sistema 
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Composer 
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer 

# Directorio de trabajo 
WORKDIR /var/www

# Copiamos el codigo 
COPY . .

# Permisos 
#RUN chown -R www-data:www-data /var/www \
#    && chmod -R 755 storage bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Instalamos las dependencias de PHP 
RUN composer install --no-dev --optimize-autoloader

USER www-data 

CMD ["php-fpm"] 
