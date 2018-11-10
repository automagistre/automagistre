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

FROM php:7.2.12-apache-stretch as app

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app
ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        openssh-client \
        zlib1g-dev \
        netcat \
        libmemcached-dev \
	\
	&& curl http://download.icu-project.org/files/icu4c/63.1/icu4c-63_1-src.tgz -o /tmp/icu4c.tgz \
	&& tar zxvf /tmp/icu4c.tgz > /dev/null \
	&& cd icu/source \
	&& ./configure --prefix=/opt/icu && make && make install \
	\
	&& docker-php-ext-configure intl --with-icu-dir=/opt/icu \
    && docker-php-ext-install zip intl pdo_mysql iconv opcache pcntl \
    && rm -rf ${PHP_INI_DIR}/conf.d/docker-php-ext-opcache.ini \
    && pecl install xdebug-2.6.1 apcu memcached \
    && docker-php-ext-enable memcached apcu \
    \
    && rm -r /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV COMPOSER_VERSION=1.7.3
ENV COMPOSER_EXEC='php -d memory_limit=-1 /usr/local/bin/composer --no-interaction'
ENV COMPOSER_CACHE_DIR=/var/cache/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_INSTALL_OPTS="--apcu-autoloader --no-progress --prefer-dist"

RUN curl -s https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer \
    | php -- --quiet --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}  \
    && composer global require "hirak/prestissimo:^0.3" \
    && composer --version

ENV WAIT_FOR_IT /usr/local/bin/wait-for-it.sh
RUN curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -o ${WAIT_FOR_IT} \
    && chmod +x ${WAIT_FOR_IT}

ARG SOURCE_DIR=.
COPY ${SOURCE_DIR}/composer.* ${APP_DIR}/
RUN if [ -f composer.json ]; then \
    mkdir -p var \
    && ${COMPOSER_EXEC} install ${COMPOSER_INSTALL_OPTS} --no-scripts \
    ; fi

ARG APP_ENV
ENV APP_ENV ${APP_ENV}
ARG APP_DEBUG
ENV APP_DEBUG ${APP_DEBUG}
ARG APP_VERSION
ENV APP_VERSION ${APP_VERSION}
ARG APP_BUILD_TIME
ENV APP_BUILD_TIME ${APP_BUILD_TIME}

COPY docker/apache/apache.conf ${APACHE_CONFDIR}/sites-enabled/000-default.conf
COPY docker/php/* ${PHP_INI_DIR}/
COPY docker/bin/* /usr/local/bin/

COPY ${SOURCE_DIR}/ ${APP_DIR}/

COPY --from=node ${APP_DIR}/public/assets/build/* ${APP_DIR}/public/assets/build/

RUN if [ "prod" = "$APP_ENV" ]; then docker-php-ext-enable opcache; fi
RUN if [ -f composer.json ]; then \
        ${COMPOSER_EXEC} install ${COMPOSER_INSTALL_OPTS} $(if [ "prod" = "$APP_ENV" ]; then echo "--no-dev"; fi) \
        && chown -R www-data:www-data ${APP_DIR}/var \
    ; fi

HEALTHCHECK --interval=5s --timeout=5s --start-period=5s CMD nc -z 127.0.0.1 80
