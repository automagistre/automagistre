#!/bin/bash

set -e

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    echo "machine github.com login ${GITHUB_AUTH_TOKEN}" > ~/.netrc
fi

APP_ENV=${APP_ENV:=prod}
case "$APP_ENV" in
   prod) ;;
   dev|test) ;;
   *) >&2 echo env "APP_ENV" must be in \"prod, dev, test\" && exit 1;;
esac
export APP_ENV

APP_DEBUG=${APP_DEBUG:=0}
case "$APP_DEBUG" in
   0|1) ;;
   *) >&2 echo env "APP_DEBUG" must be in \"1, 0\" && exit 1;;
esac
export APP_DEBUG

SWARM_SECRETS_DIR=${SWARM_SECRETS_DIR:="/run/secrets"}
if [ -d ${SWARM_SECRETS_DIR} ] && [[ $(ls -A "$SWARM_SECRETS_DIR") ]]; then
    for file in $(ls -A "$SWARM_SECRETS_DIR"); do
        env_name=$(echo "$file" | tr '[:lower:]' '[:upper:]')
        env_value=`cat $SWARM_SECRETS_DIR/$file`

        eval `echo export ${env_name}=${env_value}`
    done
fi

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

COMPOSER_SCRIPT=${COMPOSER_SCRIPT:="post-install-cmd"}
if [ "$COMPOSER_SCRIPT" != "false" ]; then
    composer run-script ${COMPOSER_SCRIPT_OPTIONS} ${COMPOSER_SCRIPT} --working-dir=${APP_DIR}

    if [ "$APP_ENV" == "prod" ]; then
        rm -rf "$APP_DIR/public/check.php"
    fi
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
