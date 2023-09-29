#!/bin/bash

composer install
mkdir /tmp/cron
echo '* * * * * php /var/www/artisan schedule:run 1>> /dev/null 2>&1' > /tmp/cron/cron_artisan.text
crond -l 2 -b -c /tmp/cron
php artisan migrate
php -v
php artisan serve --host=0.0.0.0 --port=8000
