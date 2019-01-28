FROM php:7.3-fpm

RUN apt-get update \
    && apt-get install -y libicu-dev libzip-dev zip git g++ \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip

# Install MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer