FROM php:8.3.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libonig-dev \
 && docker-php-ext-install zip pdo pdo_mysql
