#!/bin/sh

set -e

export DOCKER_BRIDGE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    composer config -g github-oauth.github.com ${GITHUB_AUTH_TOKEN}
fi

# Skip entrypoint if first argument exist in $PATH
if which "$1" > /dev/null; then exec "$@" && exit 0; fi

case "$APP_ENV" in
   prod|dev|test) ;;
   *) >&2 echo env "APP_ENV" must be in \"prod, dev, test\" && exit 1;;
esac

case "$APP_DEBUG" in
   0) ;;
   1) touch ${APP_DIR}/web/config.php;;
   *) >&2 echo env "APP_DEBUG" must be in \"1, 0\" && exit 1;;
esac

if [ -z "$SYMFONY_ENV" ]; then export SYMFONY_ENV=${APP_ENV}; fi
if [ -z "$SYMFONY_DEBUG" ]; then export SYMFONY_DEBUG=${APP_DEBUG}; fi

COMMAND="$@"
COMPOSER_DEFAULT_EXEC=${COMPOSER_DEFAULT_EXEC:="composer install --no-interaction --prefer-dist"}

if [ "$APP_ENV" == "dev" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --optimize-autoloader --verbose --profile"}

    XDEBUG=${XDEBUG:=true}
    OPCACHE=${OPCACHE:=false}
    APCU=${APCU:=false}

    COMMAND=${COMMAND:=php-server}

elif [ "$APP_ENV" == "test" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --apcu-autoloader --no-progress"}

	REQUIREMENTS=${REQUIREMENTS:=true}
	FIXTURES=${FIXTURES:=true}

	COMMAND=${COMMAND:=run-test}

elif [ "$APP_ENV" == "prod" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --no-dev --apcu-autoloader --no-progress"}

    COMMAND=${COMMAND:=apache}
fi

OPCACHE=${OPCACHE:=true}
APCU=${APCU:=true}
MIGRATION=${MIGRATION:=true}

enableExt() {
    extension=$1
    docker-php-ext-enable ${extension}
    echo -e " > $extension enabled"
}

if [ "$OPCACHE" == "true" ]; then
    enableExt opcache
fi

if [ "$APCU" == "true" ]; then
    enableExt apcu
fi
env | fgrep _ENV
if [ "$COMPOSER_EXEC" != "false" ]; then
    ${COMPOSER_EXEC}
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

exec "$COMMAND"
