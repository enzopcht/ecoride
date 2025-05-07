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
    && a2enmod rewrite


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer