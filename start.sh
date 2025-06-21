#!/bin/sh

# Install PHP dependencies
composer install --no-interaction --prefer-dist

# Start the server
php -S 0.0.0.0:8080 -t public
