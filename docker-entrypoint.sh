#!/bin/bash

set -e

export DOCKER_BRIDGE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    echo "machine github.com login ${GITHUB_AUTH_TOKEN}" > ~/.netrc
fi

# Skip entrypoint for following commands
case "$1" in
   sh|php|composer) exec "$@" && exit 0;;
esac

APP_ENV=${APP_ENV:=prod}
case "$APP_ENV" in
   prod|dev|test) ;;
   *) >&2 echo env "APP_ENV" must be in \"prod, dev, test\" && exit 1;;
esac

APP_DEBUG=${APP_DEBUG:=0}
case "$APP_DEBUG" in
   0) COMPOSER_SCRIPT_OPTIONS="-q";;
   1) touch "$APP_DIR/public/config.php";;
   *) >&2 echo env "APP_DEBUG" must be in \"1, 0\" && exit 1;;
esac

if [ -z "$SYMFONY_ENV" ]; then export SYMFONY_ENV=${APP_ENV}; fi
if [ -z "$SYMFONY_DEBUG" ]; then export SYMFONY_DEBUG=${APP_DEBUG}; fi

SWARM_SECRETS_DIR=${SWARM_SECRETS_DIR:="/run/secrets"}
if [ -d ${SWARM_SECRETS_DIR} ] && [[ $(ls -A "$SWARM_SECRETS_DIR") ]]; then
    for file in $(ls -A "$SWARM_SECRETS_DIR"); do
        env_name=$(echo "$file" | tr '[:lower:]' '[:upper:]')
        env_value=`cat $SWARM_SECRETS_DIR/$file`

        eval `echo export ${env_name}=${env_value}`
    done
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

if [ "$APP_ENV" == "dev" ]; then
    XDEBUG=${XDEBUG:=true}
    OPCACHE=${OPCACHE:=false}
    APCU=${APCU:=false}

    if [ -f "$APP_DIR/.env" ]; then
        loadEnvFile "$APP_DIR/.env"
    fi

elif [ "$APP_ENV" == "test" ]; then
	REQUIREMENTS=${REQUIREMENTS:=true}
	FIXTURES=${FIXTURES:=false}
    MIGRATIONS=${MIGRATIONS:=false}

    loadEnvFile "$APP_DIR/.env.dist"
fi

OPCACHE=${OPCACHE:=true}
APCU=${APCU:=true}
MIGRATIONS=${MIGRATIONS:=true}
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

if [ ! -z "$COMPOSER_EXEC" ] && [ -z "$SKIP_ENTRYPOINT" ]; then
    ${COMPOSER_EXEC}
fi

if [ "$COMPOSER_SCRIPT" != "false" ] && [ -z "$SKIP_ENTRYPOINT" ]; then
    composer run-script ${COMPOSER_SCRIPT_OPTIONS} ${COMPOSER_SCRIPT} --working-dir=${APP_DIR}
fi

# Remove after fix https://github.com/symfony/symfony/pull/22321
sed -i 's~/./SymfonyRequirements.php~/../var/SymfonyRequirements.php~g' "$APP_DIR/bin/symfony_requirements" || true

if [ -n "$WAIT_HOSTS" ]; then
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

if [ "$MIGRATIONS" == "true" ] && [ -z "$SKIP_ENTRYPOINT" ]; then
    console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

if [ "$FIXTURES" == "true" ] && [ -z "$SKIP_ENTRYPOINT" ]; then
    console doctrine:fixtures:load --fixtures=src/DataFixtures/ORM/ --no-interaction --env=dev --append
fi

if [ "$XDEBUG" == "true" ]; then
    enableExt xdebug
fi

if [ -f "$APP_DIR/public/config.php" ]; then
	sed -i "s~'::1',~'::1', '$DOCKER_BRIDGE_IP',~g" "public/config.php"
fi

exec "$@"
