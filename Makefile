ifeq ($(wildcard .php_cs),)
    php_cs_config = .php_cs.dist
else
    php_cs_config = .php_cs
endif

all: init docker-pull docker-build

init:
	cp -n docker-compose.yml.dist docker-compose.yml || true
	cp -n ./.env.dist ./.env || true
	mkdir -p ./var/null && touch ./var/null/composer.null
un-init:
	rm -rf docker-compose.yml ./.env ./front/var
re-init: un-init init

do-install: install-app
install: do-install up db-wait migration permissions

update: pull build install
fresh: pull build do-install up db-wait flush

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"

cli: cli-app
cli-app:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app bash
	@$(MAKE) permissions > /dev/null
cli-mysql:
	docker-compose exec mysql bash

###> GIT ###
pull:
	git fetch origin
	git pull origin $(shell git rev-parse --abbrev-ref HEAD)
empty-commit:
	git commit --allow-empty -m "Empty commit."
	git push
###< GIT ###

### DOCKER
build: docker-build
docker-build:
	docker-compose build
docker-pull:
	docker-compose pull
up:
	docker-compose up -d
up-mysql:
	docker-compose up -d mysql
serve: up
restart:
	docker-compose restart app
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs:
	docker-compose logs --follow
logs-app:
	docker-compose logs --follow app
logs-mysql:
	docker-compose logs --follow mysql
###< DOCKER ###

###> APP ###
install-app: composer

composer: composer-install
composer-install:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer install --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-run-script:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer run-script symfony-scripts
composer-update:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer update --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-update-lock:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer update --lock
	@$(MAKE) permissions > /dev/null

fixtures:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:fixtures:load --fixtures=src/DataFixtures/ORM/ --no-interaction
migration:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:migrate --no-interaction --allow-no-migration
migration-rollback:latest = $(shell docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:latest | tr '\r' ' ')
migration-rollback:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:execute --down --no-interaction $(latest)
migration-diff:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:diff
	@$(MAKE) cs
	@$(MAKE) permissions > /dev/null
migration-diff-dry:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:schema:update --dump-sql
schema-update:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:schema:update --force

check: cs-check phpstan cache schema-check phpunit-check

cs:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app php-cs-fixer fix --config $(php_cs_config)
	@$(MAKE) permissions > /dev/null
cs-check:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app php-cs-fixer fix --config=.php_cs.dist --verbose --dry-run
phpstan:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app php -d memory_limit=-1 vendor/bin/phpstan analyse --level 6 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app phpunit --debug --stop-on-failure
phpunit-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app phpunit
requirements:
	docker-compose run --rm --no-deps -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app symfony_requirements
schema-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app console doctrine:schema:validate

cache: cache-warmup
cache-clear:
	docker-compose run --rm --no-deps --entrypoint sh app -c "rm -rf ./var/cache/dev ./var/cache/test ./var/cache/prod"
	@$(MAKE) permissions > /dev/null
cache-warmup: cache-clear
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console cache:warmup
	@$(MAKE) permissions > /dev/null

flush: flush-db migration fixtures
flush-db:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:database:drop --force || true
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:database:create
db-wait:
	docker-compose run --rm -e COMPOSER_SCRIPT=false app echo OK
restore-db: flush-db
	test -s ./var/backup.sql.gz || exit 1
	docker-compose exec mysql bash -c "gunzip < /usr/local/app/var/backup.sql.gz | mysql db"
###< APP ###
