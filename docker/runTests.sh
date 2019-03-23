#!/bin/ash

export APP_ENV=TEST
cd /var/www/html

# install missing extensions only needed for tests
apk add php7-curl \
        php7-pdo \
        php7-simplexml \
        php7-xml \
        php7-xmlwriter \
        composer

# install dev dependencies
composer install --no-progress
composer dump-autoload --optimize

# run unit tests
bin/phpunit
