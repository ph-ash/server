#!/bin/ash

export APP_ENV=TEST
cd /var/www/html

# install composer
/var/www/html/docker/getComposer.sh

# install dev dependencies
./composer.phar install --no-progress
./composer.phar dump-autoload --optimize

# run unit tests
bin/phpunit
