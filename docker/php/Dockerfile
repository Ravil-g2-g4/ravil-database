FROM php:8.4-fpm

RUN apt-get update && apt-get install -y git && apt-get install -y zip

RUN docker-php-ext-install mysqli

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
