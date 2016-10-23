#!/usr/bin/env bash

# Run composer update and artisan commands in case repo was update since last start
cd /vagrant
composer install
php artisan migrate
cd -