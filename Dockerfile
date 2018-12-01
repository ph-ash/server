FROM php:7.2-fpm-alpine
RUN apk add autoconf \
      gcc \
      libzmq \
      zeromq-dev \
      zeromq \
      coreutils \
      build-base \
      zlib \
      zlib-dev \
      supervisor \
   && pecl install zmq-beta \
      mongodb \
   && docker-php-ext-install zip \
   && docker-php-ext-enable zmq \
      mongodb \
      zip \
   && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY . /var/www/html
RUN cd /var/www/html \
    && composer install \
    && php bin/console cache:warmup

RUN mkdir -p /var/log/supervisord

COPY ./Docker/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]
