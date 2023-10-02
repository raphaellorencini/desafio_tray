FROM php:8.1-alpine3.18

RUN apk add --no-cache shadow openrc openssl bash

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

RUN rm -rf /var/www/html
RUN ln -s public html

RUN echo "* * * * * php /var/www/artisan schedule:run >> /dev/null 2>&1" >> /var/spool/cron/crontabs/root

ENTRYPOINT ["crond", "-f"]
