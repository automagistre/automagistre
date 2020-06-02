#
# PHP-FPM
#
FROM composer:1.10.6 as composer
FROM php:7.4.6-fpm-buster as base

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app
ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        openssh-client \
        libzip-dev \
        netcat \
        libmemcached-dev \
        unzip \
        libfcgi-bin \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libpq-dev \
        uuid-dev \
    && rm -r /var/lib/apt/lists/*

RUN set -ex \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) zip pdo_pgsql iconv opcache pcntl gd

RUN set -ex \
	&& cd /tmp \
	&& curl -L https://github.com/unicode-org/icu/releases/download/release-65-1/icu4c-65_1-Ubuntu18.04-x64.tgz | tar xz \
	&& cp -R icu/usr/local/* /usr/local/ \
	&& docker-php-ext-install -j$(nproc) intl \
	&& rm -rf /tmp/icu

RUN set -ex \
    && pecl install memcached apcu xdebug mongodb uuid \
    && docker-php-ext-enable memcached apcu mongodb uuid

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN set -ex \
    && composer global require "hirak/prestissimo:^0.3"

ENV WAIT_FOR_IT /usr/local/bin/wait-for-it.sh
RUN set -ex \
    && curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o ${WAIT_FOR_IT} \
    && chmod +x ${WAIT_FOR_IT}

COPY composer.json composer.lock ${APP_DIR}/
RUN set -ex \
    && composer validate \
    && composer install --no-interaction --no-progress --no-scripts

COPY etc/php.ini ${PHP_INI_DIR}/php.ini
COPY etc/php-fpm.conf /usr/local/etc/php-fpm.d/automagistre.conf

ENV PHP_MEMORY_LIMIT 1G
ENV PHP_OPCACHE_ENABLE 1
ENV PHP_ZEND_ASSERTIONS 1

FROM base as app

ARG APP_ENV
ENV APP_ENV prod
ARG APP_DEBUG
ENV APP_DEBUG 0
ENV PHP_ZEND_ASSERTIONS -1

COPY bin bin
COPY config config
COPY public public
COPY src src
COPY templates templates
COPY translations translations

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
FROM nginx:1.19.0-alpine as nginx-base

WORKDIR /usr/local/app/public

RUN apk add --no-cache gzip curl

FROM nginx-base AS nginx

COPY --from=app /usr/local/app/public/favicon.ico favicon.ico
COPY --from=app /usr/local/app/public/assets assets
COPY --from=app /usr/local/app/public/bundles bundles

COPY etc/nginx.conf /etc/nginx/nginx.conf

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
