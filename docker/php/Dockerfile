FROM php:7.3-fpm

RUN apt-get update \
    && apt-get install -y libicu-dev g++ \
    && docker-php-ext-install intl

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb