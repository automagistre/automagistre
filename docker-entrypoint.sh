#!/bin/bash

set -e

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    echo "machine github.com login ${GITHUB_AUTH_TOKEN}" > ~/.netrc
fi

APP_ENV=${APP_ENV:=prod}
case "$APP_ENV" in
   prod|dev|test) ;;
   *) >&2 echo env "APP_ENV" must be in \"prod, dev, test\" && exit 1;;
esac
export APP_ENV

APP_DEBUG=${APP_DEBUG:=0}
case "$APP_DEBUG" in
   0|1) ;;
   *) >&2 echo env "APP_DEBUG" must be in \"1, 0\" && exit 1;;
esac
export APP_DEBUG

enableExt() {
    extension=$1
    docker-php-ext-enable ${extension}

    if [ "$APP_DEBUG" == 1 ]; then
        echo -e " > $extension enabled"
    fi
}

OPCACHE=${OPCACHE:=true}
if [ "$OPCACHE" == "true" ]; then
    enableExt opcache
fi

if [ "$XDEBUG" == "true" ]; then
    enableExt xdebug
fi

exec "$@"
