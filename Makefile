ifeq ($(wildcard .php_cs),)
    php_cs_config = .php_cs.dist
else
    php_cs_config = .php_cs
endif

DOCKER_COMPOSE_VERSION=1.17.0
DOCKER_UBUNTU_VERSION=17.11.0~ce-0~ubuntu

APP_DIR=.

all: init docker-pull docker-build

init:
	cp -n docker-compose.yml.dist docker-compose.yml || true
	cp -n $(APP_DIR)/.env.dist $(APP_DIR)/.env || true
	mkdir -p $(APP_DIR)/var/null && touch $(APP_DIR)/var/null/composer.null
un-init:
	rm -rf docker-compose.yml $(APP_DIR)/.env
re-init: un-init init

do-install: install-app
install: do-install up db-wait migration permissions

update: pull build install
fresh: pull build do-install up db-wait permissions cache flush
fresh-backup: pull build do-install up db-wait permissions cache drop backup-restore migration

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"
docker-hosts-updater:
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

# To prevent idea to adding this phar to *.iml config
vendor-phar-remove:
	rm -rf $(APP_DIR)/vendor/twig/twig/test/Twig/Tests/Loader/Fixtures/phar/phar-sample.phar $(APP_DIR)/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/includes/ProjectWithXsdExtensionInPhar.phar $(APP_DIR)/vendor/phpunit/phpunit/tests/_files/phpunit-example-extension/tools/phpunit.d/phpunit-example-extension-1.0.1.phar app/vendor/phar-io/manifest/tests/_fixture/test.phar || true

###> GIT ###
pull:
	git fetch origin
	git pull origin $(shell git rev-parse --abbrev-ref HEAD) || true
empty-commit:
	git commit --allow-empty -m "Empty commit."
	git push
###< GIT ###

###> DOCKER
docker-install: docker-install-engine docker-install-compose
docker-install-engine:
	curl -fsSL get.docker.com | sh
	sudo usermod -a -G docker `whoami`
docker-install-compose:
	sudo rm -rf /usr/local/bin/docker-compose /etc/bash_completion.d/docker-compose
	sudo curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
	sudo chmod +x /usr/local/bin/docker-compose
	sudo curl -L https://raw.githubusercontent.com/docker/compose/$(DOCKER_COMPOSE_VERSION)/contrib/completion/bash/docker-compose -o /etc/bash_completion.d/docker-compose
docker-upgrade:
	sudo apt-get remove -y docker-ce && sudo apt-get install docker-ce=$(DOCKER_UBUNTU_VERSION)

build: docker-build
docker-build:
	docker-compose build
docker-pull:
	docker-compose pull
up:
	docker-compose up -d
serve: up
status:
	watch docker-compose ps
cli: cli-app
restart: restart-app
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs:
	docker-compose logs --follow
###< DOCKER ###

###> APP ###
cli-app:
	docker-compose run --rm -e XDEBUG=false --entrypoint bash app
	@$(MAKE) permissions > /dev/null
restart-app:
	docker-compose restart app
logs-app:
	docker-compose logs --follow app

install-app: composer

composer=docker-compose run --rm --no-deps -e XDEBUG=false -e COMPOSER_SCRIPT=false -e MIGRATIONS=false -e WAIT_HOSTS=false app composer
composer: composer-install
composer-install:
	$(composer) install --prefer-dist
	@$(MAKE) permissions > /dev/null
	@$(MAKE) vendor-phar-remove
composer-run-script:
	$(composer) run-script symfony-scripts
composer-update:
	$(composer) update --prefer-dist
	@$(MAKE) permissions > /dev/null
	@$(MAKE) vendor-phar-remove
composer-update-lock:
	$(composer) update --lock
	@$(MAKE) permissions > /dev/null
composer-outdated:
	$(composer) outdated

fixtures:
	docker-compose run --rm -e XDEBUG=false app console doctrine:fixtures:load --env=dev --no-interaction

migration:
	docker-compose run --rm -e XDEBUG=false app console doctrine:migration:migrate --no-interaction --allow-no-migration
migration-generate:
	docker-compose run --rm -e XDEBUG=false app console doctrine:migrations:generate
	@$(MAKE) permissions > /dev/null
	@$(MAKE) cs
migration-rollback:latest = $(shell docker-compose run --rm -e XDEBUG=false app console doctrine:migration:latest | tr '\r' ' ')
migration-rollback:
	docker-compose run --rm -e XDEBUG=false app console doctrine:migration:execute --down --no-interaction $(latest)
migration-diff:
	docker-compose run --rm -e XDEBUG=false app console doctrine:migration:diff
	@$(MAKE) permissions > /dev/null
	@$(MAKE) cs
migration-diff-dry:
	docker-compose run --rm -e XDEBUG=false app console doctrine:schema:update --dump-sql
schema-update:
	docker-compose run --rm -e XDEBUG=false app console doctrine:schema:update --force

test-command:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console test -vvv

check: cs-check phpstan cache-test schema-check phpunit-check

php-cs-fixer = docker-compose run --rm --no-deps --entrypoint php-cs-fixer -e PHP_CS_FIXER_FUTURE_MODE=1 app

cs:
	$(php-cs-fixer) fix --config $(php_cs_config)
	@$(MAKE) permissions > /dev/null
cs-check:
	$(php-cs-fixer) fix --config=.php_cs.dist --verbose --dry-run
phpstan:
	docker-compose run --rm -e APP_ENV=test -e XDEBUG=false -e WAIT_HOSTS=false app php -d memory_limit=-1 vendor/bin/phpstan analyse --level 6 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=1 app phpunit --debug --stop-on-failure
phpunit-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e XDEBUG=false app phpunit
requirements:
	docker-compose run --rm --no-deps -e APP_ENV=test -e APP_DEBUG=0 -e XDEBUG=false app symfony_requirements
schema-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e XDEBUG=false app console doctrine:schema:validate

cache: cache-clear cache-warmup
cache-test: cache-clear-test cache-warmup-test
cache-clear:
	docker-compose run --rm --no-deps --entrypoint sh app -c 'rm -rf ./var/cache/"$$APP_ENV"' || true
	@$(MAKE) permissions > /dev/null
cache-warmup:
	docker-compose run --rm --no-deps -e XDEBUG=false -e WAIT_HOSTS=false app console cache:warmup
	@$(MAKE) permissions > /dev/null
cache-clear-test:
	docker-compose run --rm --no-deps -e APP_ENV=test --entrypoint sh app -c 'rm -rf ./var/cache/"$$APP_ENV"'
	@$(MAKE) permissions > /dev/null
cache-warmup-test:
	docker-compose run --rm --no-deps -e XDEBUG=false -e APP_ENV=test app console cache:warmup
	@$(MAKE) permissions > /dev/null

flush: drop migration fixtures
drop:
	docker-compose run --rm -e XDEBUG=false app  bash -c "console doctrine:database:drop --force || true && console doctrine:database:create"
db-wait:
	docker-compose run --rm -e COMPOSER_SCRIPT=false -e XDEBUG=false app echo OK
###< APP ###

###> MYSQL ###
cli-mysql:
	docker-compose exec mysql bash
restart-mysql:
	docker-compose restart mysql
logs-mysql:
	docker-compose logs --follow mysql

backup-restore:
	test -s $(APP_DIR)/var/backup.sql.gz || exit 1
	docker-compose exec mysql bash -c "gunzip < /usr/local/app/var/backup.sql.gz | mysql db"
###< MYSQL ###
