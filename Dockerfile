FROM composer:2.5.8 AS composer

FROM php:8.2-fpm AS base

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    cron \
    git \
    libgmp-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    zlib1g \
    zlib1g-dev \
    mariadb-client

RUN curl 'http://pecl.php.net/get/redis-5.3.7.tgz' -o redis.tgz \
        && pecl install redis.tgz \
        &&  rm -rf redis.tgz

RUN docker-php-ext-configure intl \
 && docker-php-ext-install zip pdo pdo_mysql intl gmp opcache bcmath \
 && docker-php-ext-enable opcache redis

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY contrib/php-fpm/access-format.conf /usr/local/etc/php-fpm.d
COPY contrib/php-fpm/z-www.conf /usr/local/etc/php-fpm.d
COPY contrib/php/php.ini /usr/local/etc/php/conf.d

WORKDIR /var/www

ENV PATH="$PATH:/var/www/vendor/bin"
ENV PATH="$PATH:/var/www/vendor-bin/spatie/vendor/bin"

# Install dependencies
COPY composer.* ./
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

FROM base AS prod

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"

COPY . ./
RUN chgrp -R www-data storage bootstrap/cache && \
    chmod -R ug+rwx storage bootstrap/cache && \
    composer dump-autoload --no-scripts --no-dev --optimize

FROM base AS dev

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"

RUN composer install --no-scripts --no-autoloader

COPY . ./
RUN chgrp -R www-data storage bootstrap/cache && \
    chmod -R ug+rwx storage bootstrap/cache && \
    composer dump-autoload --no-scripts --optimize
