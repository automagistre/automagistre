#
# Composer
#
FROM composer:2.1.9 as composer

#
# rector
#
FROM rector/rector:0.11.36 as rector

#
# PHP
#
FROM php:8.0.12-fpm-alpine3.14 as php-raw

LABEL MAINTAINER="Konstantin Grachev <me@grachevko.ru>"

ENV APP_DIR=/usr/local/app
ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

#
# > PHP EXTENSIONS
#
ENV PHP_EXT_DIR /usr/local/lib/php/extensions/no-debug-non-zts-20200930
RUN set -ex \
    && if [ `pear config-get ext_dir` != ${PHP_EXT_DIR} ]; then echo PHP_EXT_DIR must be `pear config-get ext_dir` && exit 1; fi

FROM php-raw AS php-build
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add --update-cache \
        $PHPIZE_DEPS

FROM php-build AS php-ext-gd
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

FROM php-build AS php-ext-zip
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        libzip-dev \
    && docker-php-ext-install zip

FROM php-build AS php-ext-pdo
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        postgresql-dev \
    && docker-php-ext-install pdo_pgsql

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
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        icu-dev \
	&& docker-php-ext-install intl

FROM php-build AS php-ext-apcu
RUN set -ex \
    && pecl install apcu

FROM php-build AS php-ext-xdebug
RUN set -ex \
    && pecl install xdebug

FROM php-build AS php-ext-uuid
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        util-linux-dev \
    && pecl install uuid

FROM php-build AS php-ext-pcov
RUN set -ex \
    && pecl install pcov

FROM php-build AS php-ext-redis
RUN set -ex \
    && pecl install redis

FROM php-build AS php-ext-bcmath
RUN set -ex \
    && docker-php-ext-install bcmath

FROM php-build AS php-ext-buffer
ENV EXT_BUFFER_VERSION 0.1.0
RUN set -ex \
    && curl -L https://github.com/phpinnacle/ext-buffer/archive/${EXT_BUFFER_VERSION}.tar.gz | tar xz \
    && cd ext-buffer-${EXT_BUFFER_VERSION} \
    && phpize && ./configure && make && make install

FROM php-build AS php-ext-snappy
ENV EXT_SNAPPY_VERSION 0.2.1
RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add snappy-dev \
    && curl -L https://github.com/kjdev/php-ext-snappy/archive/${EXT_SNAPPY_VERSION}.tar.gz | tar xz \
    && cd php-ext-snappy-${EXT_SNAPPY_VERSION} \
    && ls -al \
    && phpize && ./configure --with-snappy-includedir=/usr && make && make install
#
# < PHP EXTENSIONS
#

FROM php-raw AS php-base
COPY --from=php-ext-gd ${PHP_EXT_DIR}/gd.so ${PHP_EXT_DIR}/
COPY --from=php-ext-zip ${PHP_EXT_DIR}/zip.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pdo ${PHP_EXT_DIR}/pdo_pgsql.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pcntl ${PHP_EXT_DIR}/pcntl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-sockets ${PHP_EXT_DIR}/sockets.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl ${PHP_EXT_DIR}/intl.so ${PHP_EXT_DIR}/
COPY --from=php-ext-intl /usr/local /usr/local
COPY --from=php-ext-apcu ${PHP_EXT_DIR}/apcu.so ${PHP_EXT_DIR}/
COPY --from=php-ext-xdebug ${PHP_EXT_DIR}/xdebug.so ${PHP_EXT_DIR}/
COPY --from=php-ext-uuid ${PHP_EXT_DIR}/uuid.so ${PHP_EXT_DIR}/
COPY --from=php-ext-pcov ${PHP_EXT_DIR}/pcov.so ${PHP_EXT_DIR}/
COPY --from=php-ext-bcmath ${PHP_EXT_DIR}/bcmath.so ${PHP_EXT_DIR}/
COPY --from=php-ext-buffer ${PHP_EXT_DIR}/buffer.so ${PHP_EXT_DIR}/
COPY --from=php-ext-snappy ${PHP_EXT_DIR}/snappy.so ${PHP_EXT_DIR}/
COPY --from=php-ext-redis ${PHP_EXT_DIR}/redis.so ${PHP_EXT_DIR}/

RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add \
        # composer
        git \
        # healcheck
        fcgi \
        # ext-zip
        libzip \
        # ext-gd
        libpng \
        libjpeg-turbo \
        freetype \
        # ext-pdo_pgsql
        libpq \
        # ext-uuid
        libuuid \
        # ext-intl
        icu \
        snappy \
    && docker-php-ext-enable \
        apcu \
        buffer \
        gd \
        intl \
        opcache \
        pcntl \
        pcov \
        bcmath \
        pdo_pgsql \
        snappy \
        sockets \
        uuid \
        redis \
        zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY etc/php.ini ${PHP_INI_DIR}/php.ini
COPY etc/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY etc/php-fpm.www.conf /usr/local/etc/php-fpm.d/www.conf

ENV PHP_MEMORY_LIMIT 1G
ENV PHP_ZEND_ASSERTIONS 1
ENV PCOV_ENABLED 1

FROM php-base as php

ENV APP_ENV prod
ENV APP_DEBUG 0
ENV PHP_OPCACHE_ENABLE 1
ENV PHP_ZEND_ASSERTIONS -1
ENV PCOV_ENABLED 0
ENV COMPOSER_CACHE_DIR /var/cache/composer

COPY composer.json composer.lock symfony.lock ./
RUN --mount=type=cache,target=/var/cache/composer \
    set -ex \
    && composer install --no-interaction --no-progress --no-dev --no-plugins --profile --no-autoloader

COPY bin bin
COPY config config
COPY easyadmin easyadmin
COPY public public
COPY src src
COPY templates templates
COPY translations translations
COPY views views

RUN --mount=type=cache,target=/var/cache/composer \
    set -ex \
    && composer dump-autoload --no-dev --no-plugins --profile --classmap-authoritative \
    && console cache:warmup \
    && console assets:install public \
    && chown -R www-data:www-data ${APP_DIR}/var

#ENV PHP_OPCACHE_PRELOAD ${APP_DIR}/var/cache/prod/App_KernelProdContainer.preload.php

HEALTHCHECK --interval=10s --timeout=5s --start-period=5s \
        CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000

#
# nginx
#
FROM nginx:1.21.3-alpine as nginx-base

WORKDIR /usr/local/app/public

RUN --mount=type=cache,target=/var/cache/apk \
    set -ex \
    && apk add gzip brotli \
    && tempDir="$(mktemp -d)" \
    && chown nobody:nobody $tempDir \
    && apk add --virtual .build-deps \
        gcc \
        libc-dev \
        make \
        openssl-dev \
        pcre-dev \
        zlib-dev \
        linux-headers \
        libxslt-dev \
        gd-dev \
        geoip-dev \
        perl-dev \
        libedit-dev \
        mercurial \
        bash \
        alpine-sdk \
        findutils \
        brotli-dev \
    && su nobody -s /bin/sh -c " \
        export HOME=${tempDir} \
        && cd ${tempDir} \
        && curl -L https://nginx.org/download/nginx-${NGINX_VERSION}.tar.gz | tar xz \
        && curl -L https://github.com/google/ngx_brotli/archive/v1.0.0rc.tar.gz | tar xz \
        && curl -L https://github.com/openresty/headers-more-nginx-module/archive/v0.33.tar.gz | tar xz \
        && cd nginx-${NGINX_VERSION} \
        && ./configure `2>&1 nginx -V | grep _module | awk -F ':' ' { print $2 }'` --with-compat \
            --add-dynamic-module=${tempDir}/ngx_brotli-1.0.0rc \
            --add-dynamic-module=${tempDir}/headers-more-nginx-module-0.33 \
        && make modules \
        " \
    && cp ${tempDir}/nginx-${NGINX_VERSION}/objs/ngx_http_brotli_filter_module.so /etc/nginx/modules/ \
    && cp ${tempDir}/nginx-${NGINX_VERSION}/objs/ngx_http_brotli_static_module.so /etc/nginx/modules/ \
    && cp ${tempDir}/nginx-${NGINX_VERSION}/objs/ngx_http_headers_more_filter_module.so /etc/nginx/modules/ \
    && rm -rf ${tempDir} \
    && apk del .build-deps

FROM nginx-base AS nginx

ENV NGINX_ENTRYPOINT_QUIET_LOGS 1

COPY --from=php /usr/local/app/public/favicon.ico favicon.ico
COPY --from=php /usr/local/app/public/assets assets
COPY --from=php /usr/local/app/public/bundles bundles
COPY --from=php /usr/local/app/public/robots.txt .

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
    -exec brotli --best --suffix=.br --keep {} \; \
    -exec echo Compressed: {} \;

HEALTHCHECK --interval=5s --timeout=3s --start-period=5s CMD curl --fail http://127.0.0.1/healthcheck || exit 1
