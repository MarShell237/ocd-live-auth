FROM serversideup/php:8.4-frankenphp

USER root

RUN install-php-extensions intl bcmath

COPY --chown=www-data:www-data . /var/www/html

USER www-data

RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction

RUN rm -rf /var/www/html/.composer/cache
