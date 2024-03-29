ARG PHP_VERSION=8.1
ARG NGINX_VERSION=1.23.3

# Builder images
FROM composer/composer:2-bin AS composer
FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# Set working directory
WORKDIR /var/www

COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache \
    bash \
    vim \
    nginx \
    npm \
    acl \
    fcgi \
    file \
    gettext \
    git \
    supervisor

# persistent / runtime deps
RUN set -eux; \
    install-php-extensions \
        intl \
        zip \
        apcu \
        opcache \
        amqp \
        pdo_mysql \
        sysvsem \
        gd \
        xdebug \
        excimer \
        http \
    ;

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add Composer binary directory to PATH
ENV PATH="${PATH}:/usr/local/bin"

# Copy 'composer.json' and 'composer.lock' first
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress && composer clear-cache

# Copy the rest of the application
COPY . /var/www

# Install Node.js and npm, and then install yarn
RUN apk add --update nodejs npm \
    && npm install -g yarn

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
