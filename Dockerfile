# syntax=docker/dockerfile:1.4
FROM php:8.2-apache

# System deps + PHP extensions needed by the app.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev unzip libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Apache: point DocumentRoot at /var/www/html/public so the front controller
# is the only entry point visible over HTTP.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
      /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && { \
        echo '<Directory ${APACHE_DOCUMENT_ROOT}>'; \
        echo '    Options -Indexes +FollowSymLinks'; \
        echo '    AllowOverride All'; \
        echo '    Require all granted'; \
        echo '</Directory>'; \
    } >> /etc/apache2/apache2.conf

# Recommended production-ish php.ini tweaks.
RUN { \
        echo 'upload_max_filesize = 8M'; \
        echo 'post_max_size = 10M'; \
        echo 'memory_limit = 256M'; \
        echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/public/uploads /var/www/html/storage

EXPOSE 80
