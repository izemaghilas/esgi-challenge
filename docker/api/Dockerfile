FROM php:8.2-fpm-bullseye

RUN apt-get update
RUN apt-get install -y \
    libpq-dev \
    && pecl install xdebug-3.2.0

RUN docker-php-ext-install pdo_pgsql \
    && docker-php-ext-enable xdebug 

# configure xdebug
RUN echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request = yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# symfony cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli -y

# composer
COPY --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer

WORKDIR /api

CMD composer update; symfony server:start