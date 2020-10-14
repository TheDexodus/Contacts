FROM tunet/php:7.4.6-fpm-v3

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/cron/crontab /var/spool/cron/crontabs/crontab
COPY ./ /var/www/app.loc/

ARG STAGE_PUBLIC_AUTH_KEY
RUN mkdir -p /root/.ssh \
    && echo $STAGE_PUBLIC_AUTH_KEY >> /root/.ssh/authorized_keys \
    && chmod 0600 /root/.ssh/authorized_keys

WORKDIR /var/www/app.loc

RUN composer install --prefer-dist --no-suggest --no-interaction --no-scripts --no-dev \
    && bin/console cache:warmup --env=prod \
    && bin/console assets:install
