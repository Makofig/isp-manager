FROM php:8.3-fpm

# --------------------------------------------------
# System dependencies
# --------------------------------------------------

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
        zip \
    && rm -rf /var/lib/apt/lists/*


# --------------------------------------------------
# OPcache
# --------------------------------------------------

RUN docker-php-ext-enable opcache

COPY docker/php/opcache.ini \
    /usr/local/etc/php/conf.d/opcache.ini


# --------------------------------------------------
# Composer
# --------------------------------------------------

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# --------------------------------------------------
# Application
# --------------------------------------------------

WORKDIR /var/www


# --------------------------------------------------
# Composer dependencies
# --------------------------------------------------

COPY composer.json composer.lock ./

# RUN composer install \
#     --no-dev \
#     --no-interaction \
#     --no-progress \
#     --prefer-dist \
#     --optimize-autoloader \
#     --no-scripts

ARG APP_ENV=production

RUN if [ "$APP_ENV" = "production" ]; then \
        composer install \
            --no-dev \
            --no-interaction \
            --no-progress \
            --prefer-dist \
            --optimize-autoloader \
            --no-scripts; \
    else \
        composer install \
            --no-interaction \
            --no-progress \
            --prefer-dist \
            --optimize-autoloader \
            --no-scripts; \
    fi

# --------------------------------------------------
# Application source
# --------------------------------------------------

COPY . .

# Setup script
COPY docker/entrypoint/setup.sh /usr/local/bin/setup.sh

RUN chmod +x /usr/local/bin/setup.sh

# --------------------------------------------------
# Laravel permissions
# --------------------------------------------------

RUN chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache


# --------------------------------------------------
# Laravel Composer scripts
# --------------------------------------------------

RUN php artisan package:discover --ansi


# --------------------------------------------------
# Runtime user
# --------------------------------------------------

USER www-data


# --------------------------------------------------
# PHP-FPM
# --------------------------------------------------

CMD ["php-fpm", "-F"]
