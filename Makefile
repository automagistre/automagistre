.PHONY: app compose contrib deploy test

###> CONSTANTS ###
TARGET = @$(MAKE) --no-print-directory

export COMPOSE_PROJECT_NAME=automagistre

DOCKER_COMPOSE_VERSION=1.23.0
APP_DIR = .
APP_IMAGE = automagistre/app
COMPOSE_PATH = ./dev
###< CONSTANTS ###

ifeq ($(wildcard $(APP_DIR)/.php_cs),)
    PHP_CS_CONFIG_FILE = .php_cs.dist
else
    PHP_CS_CONFIG_FILE = .php_cs
endif

define success
      @tput setaf 2
      @echo " [OK] $1"
      @tput sgr0
endef
define failed
      @tput setaf 1
      @echo " [FAIL] $1"
      @tput sgr0
endef

define compose-extend
	@docker-compose \
		--file docker-compose.yml \
		--file $(COMPOSE_PATH)/docker-compose.$1.yml \
		config > docker-compose.tmp \
	&& mv -f docker-compose.tmp docker-compose.yml
	$(call success,"docker-compose.yml merged with [$1] environemnt")
endef

notify = notify-send --urgency="critical" "Makefile: $@" "COMPLETE!"

init:
	cp -n $(APP_DIR)/.env.dist $(APP_DIR)/.env || true
	cp -n $(APP_DIR)/docker/php/php.ini $(APP_DIR)/docker/php/php-dev.ini || true
	cp -n ./contrib/* ./ || true
	mv -f ./git/hooks/* ./.git/hooks/ || true
	cp -n -r $(APP_DIR)/contrib/* $(APP_DIR)/ || true
	mkdir -p $(APP_DIR)/var/null $(APP_DIR)/var/snapshots && touch $(APP_DIR)/var/null/composer.null && touch $(APP_DIR)/var/null/package.null
un-init:
	rm -rf $(APP_DIR)/.env
re-init: un-init init

bootstrap: init pull do-install-parallel docker-hosts-updater do-up cache permissions db-wait fixtures

do-install: app-install
do-install-parallel:
	$(TARGET) -j2 do-install
install: do-install

do-update: docker-compose-install pull do-install-parallel do-up db-wait permissions cache restart migration
update: do-update
	@$(notify)
master: git-check-stage-is-clear git-fetch git-checkout-master git-reset-master do-update
	@$(notify)

###> DOCKER-COMPOSE ENVIRONMENT ###
prod: default
dev: default dev-app dev-memcached

default:
	@docker-compose --file $(COMPOSE_PATH)/docker-compose.yml config > docker-compose.yml
	$(call success,"docker-compose.yml was reset to default")
dev-app:
	$(call compose-extend,dev-app)
dev-memcached:
	$(call compose-extend,dev-memcached)

xdebug-on:
	$(call compose-extend,xdebug-on)
xdebug-off:
	$(call compose-extend,xdebug-off)
###< DOCKER-COMPOSE ENVIRONMENT ###

qa: git-reset prod pull do-install-parallel cache docker-rm-restartable do-up clear-logs
	@$(notify)

clear-logs: app-clear-logs

permissions:
	@docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R $(APP_DIR)/var || true"
	$(call success,"Permissions fixed.")
docker-hosts-updater:
	docker rm -f docker-hosts-updater || true
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

# Prevent idea to adding this phar to *.iml config
vendor-phar-remove:
	@rm -rf $(APP_DIR)/vendor/twig/twig/test/Twig/Tests/Loader/Fixtures/phar/phar-sample.phar $(APP_DIR)/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/includes/ProjectWithXsdExtensionInPhar.phar $(APP_DIR)/vendor/phpunit/phpunit/tests/_files/phpunit-example-extension/tools/phpunit.d/phpunit-example-extension-1.0.1.phar $(APP_DIR)/vendor/phar-io/manifest/tests/_fixture/test.phar || true

###> GIT ###
git-check-stage-is-clear:
	@git diff --exit-code > /dev/null
	@git diff --cached --exit-code > /dev/null
git-checkout-master:
	git checkout master
git-reset-master:
	git reset --hard origin/master
git-fetch:
	git fetch origin
git-pull: git-fetch
	if git branch -a | fgrep remotes/origin/$(shell git rev-parse --abbrev-ref HEAD); then git pull origin $(shell git rev-parse --abbrev-ref HEAD); fi
git-reset: git-fetch
	if git branch -a | fgrep remotes/origin/$(shell git rev-parse --abbrev-ref HEAD); then git reset --hard origin/$(shell git rev-parse --abbrev-ref HEAD); fi
empty-commit:
	git commit --allow-empty -m "Empty commit."
	git push
###< GIT ###

###> ALIASES ###
pull:
	docker-compose pull
do-up:
	docker-compose up --detach --remove-orphans
up: do-up
	@$(notify)
status:
	watch docker-compose ps
cli:
	$(TARGET) app-cli CMD='bash'
	$(TARGET) permissions > /dev/null

RESTARTABLE_SERVICES = $(shell docker inspect --format='{{index .Config.Labels "com.docker.compose.service"}}' `docker ps --filter label="restartable=true" --filter label="com.docker.compose.project=${COMPOSE_PROJECT_NAME}" --quiet`)

restart:
	docker-compose restart $(RESTARTABLE_SERVICES)
docker-rm-restartable:
	docker-compose rm --force --stop $(RESTARTABLE_SERVICES) || true
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs-all:
	docker-compose logs --follow
###< ALIASES ###

###> DOCKER ###
docker-install: docker-install-engine docker-compose-install
docker-install-engine:
	curl -fsSL get.docker.com | sh
	sudo usermod -a -G docker `whoami`
docker-compose-install:
ifneq ($(shell docker-compose version --short), $(DOCKER_COMPOSE_VERSION))
	sudo rm -rf /usr/local/bin/docker-compose /etc/bash_completion.d/docker-compose
	sudo curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
	sudo chmod +x /usr/local/bin/docker-compose
	sudo curl -L https://raw.githubusercontent.com/docker/compose/$(DOCKER_COMPOSE_VERSION)/contrib/completion/bash/docker-compose -o /etc/bash_completion.d/docker-compose
	$(call success,"docker-compose version $(DOCKER_COMPOSE_VERSION) installed.")
else
	$(call success,"docker-compose already is up to date of $(DOCKER_COMPOSE_VERSION) version.")
endif
###< DOCKER ###

###> APP ###
app-build:
	docker build --tag "$(APP_IMAGE):dev" --target app --build-arg SOURCE_DIR=var/null --build-arg APP_VERSION=dev --build-arg APP_BUILD_TIME="`date --rfc-2822`" $(APP_DIR)
app-push:
	docker push $(APP_IMAGE):dev
app-cli:
	@docker-compose run \
	--rm --entrypoint="sh -c" app ' \
	APP_ENV=$(if $(ENV),$(ENV),$$APP_ENV) \
	APP_DEBUG=$(if $(DEBUG),$(DEBUG),$$APP_DEBUG) \
	XDEBUG=$(if $(XDEBUG),$(XDEBUG),$$XDEBUG) \
	WAIT_HOSTS=$(if $(WAIT),$(WAIT),$$WAIT_HOSTS) \
	/docker-entrypoint.sh $(CMD)'

app-install: composer

composer: composer-install
composer-install: cache-clear
	$(TARGET) app-cli CMD='composer install --prefer-dist'
	$(TARGET) permissions > /dev/null
	$(TARGET) vendor-phar-remove
composer-update:
	$(TARGET) app-cli CMD='composer update --prefer-dist'
	$(TARGET) permissions > /dev/null
	$(TARGET) vendor-phar-remove
composer-update-lock:
	$(TARGET) app-cli CMD='composer update --lock'
	$(TARGET) permissions > /dev/null
composer-outdated:
	$(TARGET) app-cli CMD='composer outdated'

migration:
	$(TARGET) app-cli CMD='console doctrine:migration:migrate --no-interaction --allow-no-migration'
migration-generate:
	$(TARGET) app-cli CMD='console doctrine:migrations:generate'
	$(TARGET) permissions > /dev/null
	$(TARGET) php-cs-fixer
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(TARGET) app-cli CMD='console doctrine:migration:execute --down --no-interaction $(latest)'
migration-diff:
	$(TARGET) app-cli CMD='console doctrine:migration:diff'
	$(TARGET) permissions > /dev/null
	$(TARGET) php-cs-fixer
migration-diff-dry:
	$(TARGET) app-cli CMD='console doctrine:schema:update --dump-sql'
migration-test:
	$(TARGET) app-cli CMD='console doctrine:schema:validate' ENV=test

schema-update:
	$(TARGET) app-cli CMD='console doctrine:schema:update --force'

app-test:
	$(TARGET) app-cli CMD='console test'

test:
	$(TARGET) php-cs-fixer
	$(TARGET) php-cs-fixer-test
	$(TARGET) cache ENV=test
	$(TARGET) phpstan
	$(TARGET) migration-test
	$(TARGET) phpunit

do-php-cs-fixer:
	$(TARGET) app-cli CMD='php -d memory_limit=-1 vendor/bin/php-cs-fixer fix --config $(PHP_CS_CONFIG_FILE) -vvv $(if $(filter true,$(DRY)),--dry-run)'
php-cs-fixer: do-php-cs-fixer
	$(TARGET) permissions > /dev/null
php-cs-fixer-test:
	$(TARGET) do-php-cs-fixer PHP_CS_CONFIG_FILE=.php_cs.dist DRY=true

phpstan:
	$(TARGET) app-cli CMD='php -d memory_limit=-1 vendor/bin/phpstan analyse --configuration phpstan.neon $(if $(filter true,$(DEBUG)),--debug -vvv)'

phpunit:
	$(TARGET) app-cli CMD='paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4) --stop-on-failure' ENV=test
requirements:
	$(TARGET) app-cli CMD='requirements-checker' ENV=test

cache: cache-clear cache-warmup
cache-clear: do-cache-clear
	$(TARGET) permissions > /dev/null
cache-warmup: do-cache-warmup
	$(TARGET) permissions > /dev/null
cache-profiler:
	@$(TARGET) app-cli CMD='rm -rf $$APP_DIR/var/cache/$(if $(APP_ENV),$(APP_ENV),"$$APP_ENV")/profiler' || true

do-cache-clear:
	$(TARGET) app-cli CMD='rm -rf $$$$APP_DIR/var/cache/$$$$APP_ENV' || true
do-cache-warmup:
	$(TARGET) app-cli CMD='console cache:warmup'

app-clear-logs:
	$(TARGET) app-cli CMD='rm -rf var/logs/*'

do-fixtures:
	$(TARGET) cache ENV=test
	$(TARGET) app-cli CMD='console doctrine:fixtures:load --no-interaction'
database: drop migration
fixtures: database do-fixtures
	@$(notify)
backup: drop backup-restore migration
	@$(notify)
drop:
	@$(TARGET) app-cli CMD='console doctrine:database:drop --force || true && console doctrine:database:create'
db-wait:
	@docker-compose run --rm -e XDEBUG=false app /bin/true > /dev/null
###< APP ###

###> MYSQL ###
mysql-cli:
	docker-compose exec	mysql bash
backup_file = $(APP_DIR)/var/backup.sql.gz
backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@docker-compose exec mysql bash -c "gunzip < /usr/local/app/var/backup.sql.gz | mysql db"
	$(call success,"Backup restored.")
else
	$(call failed,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif

SNAPSHOT_FILE_NAME = $(shell git rev-parse --abbrev-ref HEAD | sed 's\#/\#\_\#g').sql.gz
SNAPSHOT_FILE_PATH = /usr/local/app/var/snapshots/$(SNAPSHOT_FILE_NAME)
SNAPSHOT_FILE_LOCAL = $(APP_DIR)/var/snapshots/$(SNAPSHOT_FILE_NAME)
snapshot:
ifneq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" already exist! You can use \"snapshot-drop\" recipe.")
else
	@docker-compose exec mysql bash -c "mysqldump db | gzip > $(SNAPSHOT_FILE_PATH)"
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" created.")
endif
snapshot-drop:
ifeq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" does not exist!")
else
	@docker-compose exec mysql rm -f $(SNAPSHOT_FILE_PATH)
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" deleted.")
endif

snapshot-restore: drop do-snapshot-restore migration
do-snapshot-restore:
	@docker-compose exec mysql bash -c "gunzip < $(SNAPSHOT_FILE_PATH) | mysql db"
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" restored.")
###< MYSQL ###

###> MEMCACHED ###
memcached-cli:
	docker-compose exec memcached sh
memcached-restart:
	docker-compose restart memcached
###< MEMCACHED ###

###> NODE ###
NODE_IMAGE = node:10.13.0-alpine
node-install:
	docker run --rm -v `pwd`:/usr/local/app -w /usr/local/app $(NODE_IMAGE) sh -c "apk add --no-cache git && npm install"
	$(TARGET) permissions
node-cli:
	docker run --rm -v `pwd`:/usr/local/app -w /usr/local/app -ti $(NODE_IMAGE) sh
	$(TARGET) permissions
node-build:
	docker run --rm -v `pwd`:/usr/local/app -w /usr/local/app $(NODE_IMAGE) ./node_modules/.bin/gulp build:main-script build:scripts build:less
	$(TARGET) permissions
###< NODE ###
