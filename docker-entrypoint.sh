#!/bin/bash

# Run composer
composer self-update
composer install

# Run php-fpm
php-fpm -F
