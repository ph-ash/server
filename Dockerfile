FROM composer:1 as composer
COPY . /var/www/html
WORKDIR /var/www/html
ENV APP_ENV=prod

RUN composer install --no-dev --no-scripts --optimize-autoloader --ignore-platform-reqs \
    && composer run auto-scripts

# next stage #

FROM php:7.2-fpm-alpine
COPY --from=composer /var/www/html /var/www/html
WORKDIR /var/www/html
ENV APP_ENV=prod \
    API_TOKEN=pleaseChooseASecretTokenForThePublicAPI \
    MONGODB_URL=mongodb://mongo:27017 \
    MONGODB_DB=phash \
    WEBSOCKET_SERVICE_NAME=board \
    WEBSOCKET_REALM=realm1

RUN apk add autoconf \
       gcc \
       libzmq \
       zeromq-dev \
       zeromq \
       coreutils \
       build-base \
       supervisor \
    && pecl install zmq-beta \
       mongodb \
    && docker-php-ext-enable zmq \
       mongodb \
    && php bin/console cache:warmup

ENTRYPOINT ["supervisord", "--configuration", "/var/www/html/docker/supervisord.conf"]
