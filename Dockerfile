FROM php:7.1.9-apache

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app \
    COMPOSER_CACHE_DIR=/var/cache/composer \
    COMPOSER_ALLOW_SUPERUSER=1

ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        openssh-client \
        zlib1g-dev \
        netcat \
        libicu-dev \
    && docker-php-ext-install zip intl pdo_mysql iconv opcache pcntl \
    && rm -rf ${PHP_INI_DIR}/conf.d/docker-php-ext-opcache.ini \
    && pecl install xdebug apcu \
    && rm -r /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV COMPOSER_VERSION 1.5.2
COPY docker/composer.sh ./composer.sh
RUN ./composer.sh --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}  \
    && composer global require "hirak/prestissimo:^0.3" \
    && rm -rf composer.sh \
    && composer --version

ARG SOURCE_DIR=.
COPY ${SOURCE_DIR}/composer.* ${APP_DIR}/
RUN if [ -f composer.json ]; then \
    mkdir -p var \
    && composer install --no-scripts --no-interaction --apcu-autoloader --no-progress --prefer-dist \
    && rm -rf ${COMPOSER_CACHE_DIR}/* ; fi

COPY ./docker-entrypoint.sh /docker-entrypoint.sh
COPY docker/apache/apache.conf ${APACHE_CONFDIR}/sites-enabled/000-default.conf
COPY docker/php/* ${PHP_INI_DIR}/
COPY docker/bin/* /usr/local/bin/

COPY ${SOURCE_DIR}/ ${APP_DIR}/

ARG APP_BUILD=dev
ENV APP_BUILD ${APP_BUILD}
ARG APP_BUILD_TIME=''
ENV APP_BUILD_TIME ${APP_BUILD_TIME}

ENTRYPOINT ["bash", "/docker-entrypoint.sh"]
CMD ["apache"]
HEALTHCHECK --interval=30s --timeout=30s --start-period=360s CMD nc -z 127.0.0.1 80
