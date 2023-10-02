#!/bin/bash

composer install
php artisan migrate
php -v
php artisan serve --host=0.0.0.0 --port=8000
