FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./app/ /var/www/html/

# Remplace ici la config Apache
COPY ./apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data var vendor public

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

EXPOSE 80