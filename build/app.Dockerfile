FROM planereservation:php8.3 as dev

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

FROM planereservation:php8.3 as prod

COPY . /var/www/html