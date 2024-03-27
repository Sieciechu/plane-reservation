ARG SOURCE_SYSTEM_IMAGE
ARG WWWGROUP
ARG WWWUSER
FROM ${SOURCE_SYSTEM_IMAGE:-planereservation:php8.3} as prod

COPY --link --chown=${WWWUSER}:${WWWGROUP} . /var/www/html

RUN rm -Rf /var/www/html/storage/framework/cache/ \
    && mkdir -p /var/www/html/storage/framework/cache/data \
    && chown -R sail:sail /var/www/html/storage/framework/cache
