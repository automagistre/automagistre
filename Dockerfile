#
# PHP-FPM
#
FROM composer:2.0.7 as composer
FROM amd64/php:8.0.0-fpm-alpine3.12 as php-raw

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app
ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}
ENV WAIT_FOR_IT /usr/local/bin/wait-for-it.sh

WORKDIR ${APP_DIR}

RUN set -ex \
    && apk add --no-cache \
        # healcheck
        fcgi \
        # ext-zip
        libzip \
        # ext-memcached
        libmemcached \
        # ext-gd
        libpng \
        libjpeg-turbo \
        freetype \
        # ext-pdo_pgsql
        libpq \
        # ext-uuid
        libuuid \
        # ext-intl
        icu

#
# > PHP EXTENSIONS
#
ENV PHP_EXT_DIR /usr/local/lib/php/extensions/no-debug-non-zts-20190902
RUN set -ex \
    && if [ `pear config-get ext_dir` != ${PHP_EXT_DIR} ]; then echo PHP_EXT_DIR must be `pear config-get ext_dir` && exit 1; fi

FROM php-raw AS php-build
RUN set -ex \
    && apk add --no-cache $PHPIZE_DEPS 

FROM php-build AS php-ext-gd
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-zip
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        libzip-dev \
    && docker-php-ext-install zip \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-pdo
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-iconv
RUN set -ex \
    && docker-php-ext-install iconv

FROM php-build AS php-ext-pcntl
RUN set -ex \
    && docker-php-ext-install pcntl

FROM php-build AS php-ext-sockets
RUN set -ex \
    && docker-php-ext-install sockets

FROM php-build AS php-ext-intl
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        icu-dev \
	&& docker-php-ext-install intl \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-memcached
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        libzip-dev \
        libmemcached-dev \
    && pecl install memcached \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-apcu
RUN set -ex \
    && pecl install apcu

FROM php-build AS php-ext-xdebug
RUN set -ex \
    && pecl install xdebug

FROM php-build AS php-ext-mongodb
RUN set -ex \
    && pecl install mongodb

FROM php-build AS php-ext-uuid
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        util-linux-dev \
    && pecl install uuid \
    && apk del --no-network .build-deps

FROM php-build AS php-ext-pcov
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
    && pecl install pcov \
    && apk del --no-network .build-deps
#
# < PHP EXTENSIONS
#

FROM php-raw AS wait-for-it
RUN set -ex \
    && curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o ${WAIT_FOR_IT} \
    && chmod +x ${WAIT_FOR_IT}

FROM php-raw AS php-base
COPY --from=php-ext-gd ${PHP_EXT_DIR}/gd.so ${PHP_EXT_DIR}/
COPY --from=php-ext-zip ${PHP_EXT_DIR}/zip.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pdo ${PHP_EXT_DIR}/pdo_pgsql.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pcntl ${PHP_EXT_DIR}/pcntl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-sockets ${PHP_EXT_DIR}/sockets.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl ${PHP_EXT_DIR}/intl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl /usr/local /usr/local
COPY --from=php-ext-memcached ${PHP_EXT_DIR}/memcached.so ${PHP_EXT_DIR}/
COPY --from=php-ext-apcu ${PHP_EXT_DIR}/apcu.so ${PHP_EXT_DIR}/
COPY --from=php-ext-xdebug ${PHP_EXT_DIR}/xdebug.so ${PHP_EXT_DIR}/
COPY --from=php-ext-mongodb ${PHP_EXT_DIR}/mongodb.so ${PHP_EXT_DIR}/
COPY --from=php-ext-uuid ${PHP_EXT_DIR}/uuid.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pcov ${PHP_EXT_DIR}/pcov.so ${PHP_EXT_DIR}/
COPY --from=wait-for-it ${WAIT_FOR_IT} ${WAIT_FOR_IT}

RUN set -ex \
    && docker-php-ext-enable \
        apcu \
        gd \
        intl \
        memcached \
        mongodb \
        opcache \
        pcntl \
        pcov \
        pdo_pgsql \
        sockets \
        uuid \
        zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY etc/php.ini ${PHP_INI_DIR}/php.ini
COPY etc/php-fpm.conf /usr/local/etc/php-fpm.d/automagistre.conf

ENV PHP_MEMORY_LIMIT 1G
ENV PHP_OPCACHE_ENABLE 1
ENV PHP_ZEND_ASSERTIONS 1
ENV PCOV_ENABLED 1

FROM php-base as php

ARG APP_ENV
ENV APP_ENV prod
ARG APP_DEBUG
ENV APP_DEBUG 0
ENV PHP_ZEND_ASSERTIONS -1
ENV PCOV_ENABLED 0

COPY bin bin
COPY config config
COPY public public
COPY src src
COPY templates templates
COPY translations translations
COPY composer.json composer.lock symfony.lock ${APP_DIR}/

RUN set -ex \
    && composer install --no-interaction --no-progress --no-dev --classmap-authoritative \
    && console cache:warmup \
    && console assets:install public \
    && chown -R www-data:www-data ${APP_DIR}/var

HEALTHCHECK --interval=10s --timeout=5s --start-period=5s \
        CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000

#
# nginx
#
FROM nginx:1.19.5-alpine as nginx-base

WORKDIR /usr/local/app/public

RUN apk add --no-cache gzip curl

FROM nginx-base AS nginx

COPY --from=php /usr/local/app/public/favicon.ico favicon.ico
COPY --from=php /usr/local/app/public/assets assets
COPY --from=php /usr/local/app/public/bundles bundles

COPY etc/nginx.conf /etc/nginx/nginx.conf
COPY etc/nginx.cors.conf /etc/nginx/cors.conf
COPY etc/nginx.cors-setup.conf /etc/nginx/cors.setup.conf

RUN find . \
    -type f \
    \( \
        -name "*.css" \
        -or -name "*.eot" \
        -or -name "*.html" \
        -or -name "*.js" \
        -or -name "*.json" \
        -or -name "*.otf" \
        -or -name "*.svg" \
        -or -name "*.ttf" \
        -or -name "*.woff" \
     \) \
    -exec gzip -9 --name --suffix=.gz --keep {} \; \
    -exec echo Compressed: {} \;

HEALTHCHECK --interval=5s --timeout=3s --start-period=5s CMD curl --fail http://127.0.0.1/healthcheck || exit 1
