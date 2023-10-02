FROM php:8.1-alpine3.18

RUN apk add --no-cache shadow openrc openssl bash mysql-client nodejs npm

RUN docker-php-ext-install pdo pdo_mysql

ENV DOCKERIZE_VERSION v0.7.0

RUN apk update --no-cache \
    && apk add --no-cache wget openssl \
    && wget -O - https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz | tar xzf - -C /usr/local/bin \
    && apk del wget

WORKDIR /var/www

RUN rm -rf /var/www/html
RUN ln -s public html

COPY ./entrypoint.sh /tmp/entrypoint.sh
RUN chmod +x /tmp/entrypoint.sh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data
USER www-data

ENTRYPOINT ["tail", "-f", "dev/nul"]
