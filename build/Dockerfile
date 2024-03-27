FROM alpine:3.19 as base

WORKDIR /var/www/html

# Set environment variables
ARG WWWGROUP
ARG WWWUSER
ENV WWWUSER=${WWWUSER:-sail} \
    WWWGROUP=${WWWGROUP:-www-data}

# Install basic packages
RUN apk update \
    && apk --no-cache add  \
        py3-setuptools \
        supervisor \
`# https://wiki.alpinelinux.org/wiki/Setting_the_timezone` \
        tzdata \
    && cp /usr/share/zoneinfo/Etc/UTC /etc/localtime \
    && echo "Etc/UTC" > /etc/timezone \
    && date \
`# Install and configure bash` \
    && apk --no-cache add bash \
    && sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# Install and configure PHP
RUN apk --no-cache add \
        php83 \
        php83-common \
        php83-fpm \
        php83-pdo \
        php83-opcache \
        php83-zip \
        php83-phar \
        php83-iconv \
        php83-cli \
        php83-curl \
        php83-openssl \
        php83-mbstring \
        php83-tokenizer \
        php83-fileinfo \
        php83-json \
        php83-xml \
        php83-xmlwriter \
        php83-simplexml \
        php83-dom \
        php83-pdo_sqlite \
        php83-pdo_pgsql \
        php83-pgsql \
        php83-tokenizer \
        php83-pecl-redis \
        php83-ctype \
        php83-gd \
        php83-xmlreader \
    && ln -s /usr/bin/php83 /usr/bin/php \
`# install usermod` \
    && echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories \
    && apk add -U shadow
COPY ./php.ini /etc/php/8.0/cli/conf.d/99-sail.ini

# Configure supervisord
RUN touch /var/run/supervisord.pid \
    && mkdir -p /etc/supervisor.d/conf.d \
    && mkdir -p /var/log/supervisor \
    && touch /var/log/supervisor/supervisord.log
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configure container startup script
COPY ./start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# Configure user for sail
RUN addgroup -g ${WWWGROUP} sail \
    && adduser -D -h /var/www/html -s /bin/bash -G sail -u ${WWWUSER} sail

EXPOSE 80
ENTRYPOINT ["start-container"]


FROM base as dev

# install composer
RUN \
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
    && apk update && apk add --no-cache php83-pecl-xdebug

# setup xdebug.ini
RUN echo "[XDebug]" > /etc/php83/conf.d/50_xdebug.ini \
    && echo "zend_extension=xdebug.so" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.mode=debug,develop" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.client_port=9003" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /etc/php83/conf.d/50_xdebug.ini \
    && echo "xdebug.start_with_request=yes ; so it turns on for every http request" >> /etc/php83/conf.d/50_xdebug.ini
