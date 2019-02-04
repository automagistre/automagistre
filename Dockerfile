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

FROM composer:1.8.3 as composer
FROM php:7.2.14-cli-stretch as app

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
    && rm -r /var/lib/apt/lists/*

RUN set -ex \
    && docker-php-ext-install zip pdo_mysql iconv opcache pcntl

RUN set -ex \
	&& cd /tmp \
	&& curl http://download.icu-project.org/files/icu4c/63.1/icu4c-63_1-src.tgz | tar xz \
	&& cd icu/source \
	&& ./configure --prefix=/opt/icu && make && make install \
	&& docker-php-ext-configure intl --with-icu-dir=/opt/icu \
	&& docker-php-ext-install intl \
	&& rm -rf /tmp/icu

RUN set -ex \
    && pecl install xdebug memcached \
    && docker-php-ext-enable xdebug memcached

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN set -ex \
    && composer global require "hirak/prestissimo:^0.3"

ENV WAIT_FOR_IT /usr/local/bin/wait-for-it.sh
RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o ${WAIT_FOR_IT} \
    && chmod +x ${WAIT_FOR_IT}

ENV RR_VERSION 1.3.4
RUN set -ex \
    && cd /tmp \
    && curl -L https://github.com/spiral/roadrunner/releases/download/v${RR_VERSION}/roadrunner-${RR_VERSION}-linux-amd64.tar.gz | tar xz \
    && cp roadrunner-${RR_VERSION}-linux-amd64/rr /usr/local/bin/rr \
    && rm -rf roadrunner-${RR_VERSION}-linux-amd64

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
ARG APP_BUILD_TIME
ENV APP_BUILD_TIME ${APP_BUILD_TIME}

ENV PHP_MEMORY_LIMIT 128m
ENV PHP_OPCACHE_ENABLE 1
COPY config/php.ini ${PHP_INI_DIR}/php.ini

COPY ./ ${APP_DIR}/

COPY --from=node ${APP_DIR}/public/assets/build/* ${APP_DIR}/public/assets/build/

RUN set -ex \
    && composer install --no-interaction --no-progress \
        $(if [ "prod" = "$APP_ENV" ]; then echo "--no-dev --classmap-authoritative"; fi) \
    && chown -R www-data:www-data ${APP_DIR}/var

HEALTHCHECK --interval=5s --timeout=5s --start-period=5s CMD nc -z 127.0.0.1 80
