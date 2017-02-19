FROM php:7.1.1-apache

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app \
    COMPOSER_CACHE_DIR=/var/cache/composer \
    COMPOSER_ALLOW_SUPERUSER=1

ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && echo "deb http://httpredir.debian.org/debian/ stretch main" > /etc/apt/sources.list.d/stretch.list \
    && { \
        echo 'Package: *'; \
        echo 'Pin: release a=stable'; \
        echo 'Pin-Priority: 900'; \
    } > /etc/apt/preferences \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        openssh-client \
        zlib1g-dev \
    && apt-get install -y --no-install-recommends -t testing libicu-dev \
    && docker-php-ext-install zip intl pdo_mysql iconv opcache \
    && rm -rf ${PHP_INI_DIR}/conf.d/docker-php-ext-opcache.ini \
    && pecl install xdebug apcu \
    && rm -r /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY docker/composer.sh ./composer.sh
RUN ./composer.sh --install-dir=/usr/local/bin --filename=composer \
    && composer global require "hirak/prestissimo:^0.3" \
    && rm -rf composer.sh

ARG SOURCE_DIR=.

COPY $SOURCE_DIR/composer.* ${APP_DIR}/
RUN if [ -f composer.json ]; then \
    composer install --no-scripts --no-interaction --no-autoloader --no-progress --prefer-dist \
    && rm -rf ${COMPOSER_CACHE_DIR}/* ; fi

COPY $SOURCE_DIR/ ${APP_DIR}/
COPY docker/docker-entrypoint.sh /docker-entrypoint.sh

COPY docker/php/* ${PHP_INI_DIR}/
COPY docker/bin/* /usr/local/bin/

VOLUME ${APP_DIR}/var/logs
VOLUME ${APP_DIR}/var/sessions

ENTRYPOINT ["bash", "/docker-entrypoint.sh"]
