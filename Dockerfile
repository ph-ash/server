FROM composer:1 as composer
COPY . /var/www/html
WORKDIR /var/www/html
ENV APP_ENV=prod

RUN composer install --no-dev --no-scripts --ignore-platform-reqs \
    && composer dump-autoload --optimize \
    && composer run auto-scripts

# next stage #

FROM alpine:3.8
COPY --from=composer /var/www/html /var/www/html
WORKDIR /var/www/html
ENV APP_ENV=prod \
    API_TOKEN=pleaseChooseASecretTokenForThePublicAPI \
    MONGODB_URL=mongodb://mongo:27017 \
    MONGODB_DB=phash \
    WAMP_HOST=board \
    WAMP_REALM=realm1

RUN apk add --no-cache php7-fpm \
       php7-cli \
       php7-ctype \
       php7-dom \
       php7-iconv \
       php7-mbstring \
       php7-openssl \
       php7-session \
       php7-simplexml \
       php7-tokenizer \
       php7-zip \
       php7-pecl-zmq \
       php7-pecl-mongodb \
       supervisor \
       fcgi \
    && cp docker/*-fpm.conf /etc/php7/php-fpm.d/ \
    && php bin/console cache:warmup \
    && crontab /var/www/html/docker/crontab

EXPOSE 9000

ENTRYPOINT ["supervisord", "--configuration", "/var/www/html/docker/supervisord.conf"]
