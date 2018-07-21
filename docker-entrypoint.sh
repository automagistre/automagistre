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

if [ -n "$WAIT_HOSTS" ] && [ "$WAIT_HOSTS" != "false" ]; then
    OLD_IFS=${IFS}

    IFS=',' read -ra HOSTS <<< "$WAIT_HOSTS"
    for target in "${HOSTS[@]}"; do
        IFS=':' read host port <<< "$target"

        if ! nc -z "$host" "$port"; then
            echo "Waiting service $host:$port..."

            while ! nc -z "$host" "$port"; do sleep 1; done

            echo "Service $host:$port has arrived!"
        fi
    done

    IFS=${OLD_IFS}
fi

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

MIGRATIONS=${MIGRATIONS:=true}
if [ "$MIGRATIONS" == "true" ]; then
    console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

if [ "$FIXTURES" == "true" ]; then
    console doctrine:fixtures:load --no-interaction
fi

if [ "$XDEBUG" == "true" ]; then
    enableExt xdebug
fi

exec "$@"
