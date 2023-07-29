# Use an official PHP-FPM image as the base image
ARG PHP_VERSION=8.1
ARG NGINX_VERSION=1.23.3

# Builder images
FROM composer/composer:2-bin AS composer
FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# Set working directory
WORKDIR /var/www

COPY --from=php_extension_installer --link /usr/bin/install-php-extensions /usr/local/bin/

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

# nginx - nginx app permissions
RUN mkdir -p /run/nginx

# nginx - config files
COPY .docker/nginx/nginx.conf  /etc/nginx/
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Install Node.js and Yarn
RUN apk add --update nodejs npm
RUN npm install -g yarn

#cron
COPY .docker/cron/ /var/www/cron/
RUN chmod +x /var/www/cron/jobs/*.sh

COPY .docker/cron/crontab /var/spool/cron/crontabs/root

#supervisor
COPY .docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# docker entrypoint
COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh

CMD ["/entrypoint.sh"]

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

RUN set -eux; \
    if [ -f composer.json ]; then \
		composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]
