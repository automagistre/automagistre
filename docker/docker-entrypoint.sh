#!/bin/bash

set -e

cd "$APP_DIR"

export DOCKER_BRIDGE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    echo "machine github.com login ${GITHUB_AUTH_TOKEN}" > ~/.netrc
fi

# Skip entrypoint for following commands
case "$1" in
   sh|php|composer) exec "$@" && exit 0;;
esac

if [ ! -z "$SKIP_ENTRYPOINT" ]; then
    exec "$@" && exit 0
fi

# Set variable from env file if variable not defined
loadEnvFile() {
    OLD_IFS="$IFS"
    IFS='='
    while read env_name env_value
    do
        if [ -z "$env_name" ]; then continue; fi

        IFS=
        eval `echo export ${env_name}=\$\{${env_name}\:=${env_value}\}`
        IFS='='
    done < $1
    IFS="$OLD_IFS"
}

if [ -f "$APP_DIR/.env" ]; then
    loadEnvFile "$APP_DIR/.env"
fi

case "$APP_ENV" in
   prod|dev|test) ;;
   *) >&2 echo env "APP_ENV" must be in \"prod, dev, test\" && exit 1;;
esac

case "$APP_DEBUG" in
   0) COMPOSER_SCRIPT_OPTIONS="-q";;
   1) touch ${APP_DIR}/web/config.php;;
   *) >&2 echo env "APP_DEBUG" must be in \"1, 0\" && exit 1;;
esac

if [ -z "$SYMFONY_ENV" ]; then export SYMFONY_ENV=${APP_ENV}; fi
if [ -z "$SYMFONY_DEBUG" ]; then export SYMFONY_DEBUG=${APP_DEBUG}; fi

COMMAND="$@"

if [ "$APP_ENV" == "dev" ]; then
    XDEBUG=${XDEBUG:=true}
    OPCACHE=${OPCACHE:=false}
    APCU=${APCU:=false}

elif [ "$APP_ENV" == "test" ]; then
	REQUIREMENTS=${REQUIREMENTS:=true}
#	FIXTURES=${FIXTURES:=true}

    loadEnvFile "$APP_DIR/.env.dist"
fi

COMMAND=${COMMAND:=apache}
OPCACHE=${OPCACHE:=true}
APCU=${APCU:=true}
MIGRATION=${MIGRATION:=true}
COMPOSER_SCRIPT=${COMPOSER_SCRIPT:="post-install-cmd"}

enableExt() {
    extension=$1
    docker-php-ext-enable ${extension}

    if [ "$APP_DEBUG" == 1 ]; then
        echo -e " > $extension enabled"
    fi
}

if [ "$OPCACHE" == "true" ]; then
    enableExt opcache
fi

if [ "$APCU" == "true" ]; then
    enableExt apcu
fi

if [ ! -z "$COMPOSER_EXEC" ]; then
    ${COMPOSER_EXEC}
fi

if [ "$COMPOSER_SCRIPT" != "false" ]; then
    composer run-script ${COMPOSER_SCRIPT_OPTIONS} ${COMPOSER_SCRIPT}
fi

if [ "$MIGRATION" == "true" ]; then
    bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet
fi

if [ "$FIXTURES" == "true" ]; then
    bin/console doctrine:fixtures:load --no-interaction
fi

if [ "$XDEBUG" == "true" ]; then
    enableExt xdebug
fi

if [ -f ${APP_DIR}/web/config.php ]; then
	sed -i "s~'::1',~'::1', '$DOCKER_BRIDGE_IP',~g" "$APP_DIR/web/config.php"
fi

${COMMAND}
