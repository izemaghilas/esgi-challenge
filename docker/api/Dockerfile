FROM php:8.2-fpm-alpine3.17

RUN apk --no-cache update && apk --no-cache add \
    bash \
    postgresql-dev

RUN docker-php-ext-install pdo_pgsql

# symfony cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

# composer
COPY --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer

WORKDIR /api

CMD composer update; symfony server:start