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

install: install-backend up db-wait migration fixtures permissions
update: pull docker-build install

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"

cli: cli-app

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

###> API ###
install-backend: composer

composer: composer-install
composer-install:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app composer install --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-run-script:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app composer run-script symfony-scripts
composer-update:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app composer update --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-update-lock:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app composer update --lock
	@$(MAKE) permissions > /dev/null

fixtures:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:fixtures:load --fixtures=src/DataFixtures/ORM/ --no-interaction
migrations:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:migration:migrate --no-interaction --allow-no-migration
migrations-rollback:latest = $(shell docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:migration:latest | tr '\r' ' ')
migrations-rollback:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:migration:execute --down --no-interaction $(latest)
migrations-diff:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:migration:diff
	@$(MAKE) permissions > /dev/null
migrations-diff-dry:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:schema:update --dump-sql
schema-update:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:schema:update --force

cli-app:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app bash
	@$(MAKE) permissions > /dev/null
cli-mysql:
	docker-compose exec mysql bash

check: cs-check phpstan yaml-lint cache-clear schema-check phpunit-check

cs:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app php-cs-fixer fix --config $(php_cs_config)
	@$(MAKE) permissions > /dev/null
cs-check:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app php-cs-fixer fix --config=.php_cs.dist --verbose --dry-run
phpstan:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app phpstan analyse --level 5 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app phpunit --debug --stop-on-failure
phpunit-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app phpunit
requirements:
	docker-compose run --rm --no-deps -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app symfony_requirements
yaml-lint:
	docker-compose run --rm --no-deps -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app console lint:yaml config
schema-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false app console doctrine:schema:validate

cache-clear:
	@$(MAKE) cache-clear-exec || $(MAKE) cache-clear-run
cache-clear-run:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app console cache:clear --no-warmup
	@$(MAKE) permissions > /dev/null
cache-clear-exec:
	docker-compose exec app console cache:clear --no-warmup
	@$(MAKE) permissions > /dev/null
cache-warmup: cache-clear
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true app console cache:warmup
	@$(MAKE) permissions > /dev/null

flush: flush-db migration fixtures
flush-db:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:database:drop --force || true
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console doctrine:database:create
db-wait:
	docker-compose run --rm -e COMPOSER_SCRIPT=false app echo OK
###< API ###
