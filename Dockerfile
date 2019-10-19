FROM node:10.13.0-alpine as node

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app
ENV PATH=${APP_DIR}/node_modules/.bin:${PATH}

WORKDIR ${APP_DIR}

RUN apk add --no-cache git

COPY package.json package-lock.json ${APP_DIR}/
RUN npm install

COPY gulpfile.js ${APP_DIR}
COPY assets ${APP_DIR}/assets

RUN gulp build:main-script build:scripts build:less

#
# PHP-FPM
#
FROM composer:1.9.0 as composer
FROM php:7.3.10-fpm-stretch as app

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
    && rm -r /var/lib/apt/lists/*

RUN set -ex \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include --with-jpeg-dir=/usr/include \
    && docker-php-ext-install -j$(nproc) zip pdo_mysql iconv opcache pcntl gd

RUN set -ex \
	&& cd /tmp \
	&& curl -L https://github.com/unicode-org/icu/releases/download/release-65-1/icu4c-65_1-src.tgz | tar xz \
	&& cd icu/source \
	&& ./configure --prefix=/opt/icu && make -j "$(nproc)" && make install \
	&& docker-php-ext-configure intl --with-icu-dir=/opt/icu \
	&& docker-php-ext-install -j$(nproc) intl \
	&& rm -rf /tmp/icu

RUN set -ex \
    && pecl install memcached apcu xdebug \
    && docker-php-ext-enable memcached apcu

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN set -ex \
    && composer global require "hirak/prestissimo:^0.3"

ENV WAIT_FOR_IT /usr/local/bin/wait-for-it.sh
RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o ${WAIT_FOR_IT} \
    && chmod +x ${WAIT_FOR_IT}

COPY composer.json composer.lock ${APP_DIR}/
RUN set -ex \
    && composer validate \
    && mkdir -p var \
    && composer install --no-interaction --no-progress --no-scripts

ARG APP_ENV
ENV APP_ENV ${APP_ENV}
ARG APP_DEBUG
ENV APP_DEBUG ${APP_DEBUG}
ARG APP_VERSION
ENV APP_VERSION ${APP_VERSION}

ENV PHP_MEMORY_LIMIT 128m
ENV PHP_OPCACHE_ENABLE 1

COPY ./ ${APP_DIR}/

RUN set -ex \
    && mv config/php.ini ${PHP_INI_DIR}/php.ini \
    && mv config/php-fpm.conf /usr/local/etc/php-fpm.d/automagistre.conf \
    && rm -f config/nginx.conf \
    && composer install --no-interaction --no-progress \
        $(if [ "prod" = "$APP_ENV" ]; then echo "--no-dev --classmap-authoritative"; fi) \
    && chown -R www-data:www-data ${APP_DIR}/var

HEALTHCHECK --interval=10s --timeout=5s --start-period=5s \
        CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000

#
# nginx
#
FROM nginx:1.17.1-alpine as nginx

WORKDIR /usr/local/app/public

RUN apk add --no-cache gzip curl

COPY --from=app /usr/local/app/public/favicon.ico favicon.ico
COPY --from=app /usr/local/app/public/assets assets
COPY --from=app /usr/local/app/public/bundles bundles
COPY --from=app /usr/local/app/public/includes includes
COPY --from=node /usr/local/app/public/assets/build assets/build

COPY config/nginx.conf /etc/nginx/nginx.conf

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
