FROM php:8.3-alpine

WORKDIR /var/www/html

# Set environment variables
ENV WWWUSER=sail \
    WWWGROUP=sail

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
    && ln -s /usr/bin/php8 /usr/bin/php \
`# install usermod` \
    && echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories \
    && apk add -U shadow
COPY php.ini /etc/php/8.0/cli/conf.d/99-sail.ini

# Configure supervisord
RUN touch /var/run/supervisord.pid \
    && mkdir -p /etc/supervisor.d/conf.d \
    && mkdir -p /var/log/supervisor \
    && touch /var/log/supervisor/supervisord.log
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configure container startup script
COPY start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# Configure user for sail
RUN addgroup sail \
    && adduser -h /var/www/html -s /bin/bash -G sail -D sail

EXPOSE 80
ENTRYPOINT ["start-container"]