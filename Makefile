.PHONY: dev contrib

###> CONSTANTS ###
DOCKER_COMPOSE_VERSION=1.23.1
APP_DIR = .
###< CONSTANTS ###

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

notify = notify-send --urgency="critical" "Makefile: $@" "COMPLETE!"

init:
	cp -n .env.dist .env || true
	cp -n docker-compose.override.yml.dist docker-compose.override.yml || true
	cp -n contrib/* ./ || true
	cp -n -r contrib/* ./ || true
	mkdir -p var/snapshots
un-init:
	rm -rf .env
re-init: un-init init

bootstrap: init pull do-install-parallel docker-hosts-updater do-up cache permissions db-wait fixtures

install: do-install-parallel permissions
do-install-parallel:
	@$(MAKE) --no-print-directory -j2 do-install
do-install: app-install

do-update: docker-compose-install pull do-install-parallel do-up db-wait permissions cache restart migration
update: do-update
	@$(notify)
master: git-check-stage-is-clear git-fetch git-checkout-master git-reset-master do-update
	@$(notify)

cs: do-php-cs-fixer permissions

clear-logs: app-clear-logs

docker-hosts-updater:
	docker rm -f docker-hosts-updater || true
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater:0.2

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
	docker-compose up --detach --remove-orphans --no-build
up: do-up
	@$(notify)
status:
	watch docker-compose ps
cli: do-cli permissions
do-cli:
	$(APP) bash
cs: do-php-cs-fixer

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
APP_IMAGE = automagistre/app:dev
app-build:
	docker build \
		--build-arg APP_ENV=dev \
		--build-arg APP_DEBUG=1 \
		--build-arg APP_VERSION=dev \
		--build-arg APP_BUILD_TIME="`date --rfc-2822`" \
		--target app \
		--tag $(APP_IMAGE) \
		.
app-push:
	docker push $(APP_IMAGE)

APP = docker-compose $(if $(EXEC),exec,run --rm )\
	$(if $(ENTRYPOINT),--entrypoint "$(ENTRYPOINT)" )\
	$(if $(ENV),-e APP_ENV=$(ENV) )\
	$(if $(DEBUG),-e APP_DEBUG=$(DEBUG) )app

permissions:
	$(APP) sh -c "chown $(shell id -u):$(shell id -g) -R . && chmod 777 -R var/ || true"
	$(call success,"Permissions fixed.")

app-cli:
	$(APP)
app-workers: EXEC=true
app-workers:
	$(APP) rr http:workers -i

app-install: do-composer-install vendor-phar-remove

composer: do-cache-clear composer-install
composer-install: do-composer-install permissions vendor-phar-remove
composer-update: do-composer-update permissions vendor-phar-remove
composer-update-lock: do-composer-update-lock permissions
composer-outdated:
	$(APP) composer outdated
do-composer-install:
	$(APP) composer install
do-composer-update:
	$(APP) composer update
do-composer-update-lock:
	$(APP) composer update --lock
# Prevent idea to adding this phar to *.iml config
vendor-phar-remove:
	@rm -rf $(APP_DIR)/vendor/twig/twig/test/Twig/Tests/Loader/Fixtures/phar/phar-sample.phar $(APP_DIR)/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/includes/ProjectWithXsdExtensionInPhar.phar $(APP_DIR)/vendor/phpunit/phpunit/tests/_files/phpunit-example-extension/tools/phpunit.d/phpunit-example-extension-1.0.1.phar $(APP_DIR)/vendor/phar-io/manifest/tests/_fixture/test.phar || true

migration: migration-landlord migration-tenant
do-migration:
	$(APP) console doctrine:migration:migrate --no-interaction --allow-no-migration --db=${EM} --em=${EM}
migration-landlord:
	@$(MAKE) EM=landlord do-migration --no-print-directory
migration-tenant:
	@$(MAKE) EM=tenant do-migration --no-print-directory

migration-generate: do-migration-generate php-cs-fixer
do-migration-generate:
	$(APP) console doctrine:migrations:generate
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(APP) console doctrine:migration:execute --down --no-interaction $(latest)

migration-diff: migration-diff-all php-cs-fixer
migration-diff-all: migration-diff-landlord migration-diff-tenant
migration-diff-landlord:
		@$(MAKE) EM=landlord do-migration-diff --no-print-directory
migration-diff-tenant:
		@$(MAKE) EM=tenant do-migration-diff --no-print-directory
do-migration-diff:
	$(APP) console doctrine:migration:diff --em=${EM} --db=${EM}
migration-diff-dry:
	$(APP) console doctrine:schema:update --dump-sql --em=${EM}

migration-test: ENV=test
migration-test:
	$(APP) console doctrine:schema:validate

schema-update:
	$(APP) console doctrine:schema:update --force

app-test:
	$(APP) console test

test: ENV=test
test: DRY=true
test: do-php-cs-fixer phpstan migration-test phpunit

do-php-cs-fixer:
	$(APP) php-cs-fixer fix -vvv $(if $(filter true,$(DRY)),--dry-run)
php-cs-fixer: do-php-cs-fixer permissions

phpstan:
	$(APP) phpstan analyse --configuration phpstan.neon $(if $(filter true,$(DEBUG)),--debug -vvv)

phpunit: ENV=test
phpunit:
	$(APP) paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4) --stop-on-failure
requirements: ENV=prod
requirements:
	$(APP) requirements-checker

cache: do-cache-clear do-cache-warmup permissions
cache-clear: do-cache-clear permissions
cache-warmup: do-cache-warmup permissions
cache-profiler:
	$(APP) sh -c 'rm -rf var/cache/$$APP_ENV/profiler' || true

do-cache-clear:
	$(APP) sh -c 'rm -rf var/cache/$$APP_ENV'
do-cache-warmup:
	$(APP) console cache:warmup

app-clear-logs:
	$(APP) rm -rf var/logs/*

database: drop migration
fixtures: ENV=test
fixtures: database cache do-fixtures
	@$(notify)
do-fixtures:
	$(APP) console doctrine:fixtures:load --no-interaction
backup: drop backup-restore migration
	@$(notify)

do-drop:
	@$(APP) sh -c "console doctrine:database:drop --force --connection=${EM} || true && console doctrine:database:create --connection=${EM}"
drop-landlord:
	@$(MAKE) do-drop EM=landlord --no-print-directory
drop-tenant:
	@$(MAKE) do-drop EM=tenant --no-print-directory
drop-all: drop-landlord drop-tenant
drop:
	@$(MAKE) --no-print-directory -j2 drop-all

db-wait:
	$(APP) wait-for-it.sh mysql:3306
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

SNAPSHOT_FILE_NAME = $(shell git rev-parse --abbrev-ref HEAD | sed 's\#/\#\_\#g')_${EM}.sql.gz
SNAPSHOT_FILE_PATH = /usr/local/app/var/snapshots/$(SNAPSHOT_FILE_NAME)
SNAPSHOT_FILE_LOCAL = $(APP_DIR)/var/snapshots/$(SNAPSHOT_FILE_NAME)
snapshot: snapshot-landlord snapshot-tenant
snapshot-landlord:
	@$(MAKE) do-snapshot EM=landlord --no-print-directory
snapshot-tenant:
	@$(MAKE) do-snapshot EM=tenant --no-print-directory
do-snapshot:
ifneq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" already exist! You can use \"snapshot-drop\" recipe.")
else
	@docker-compose exec ${EM} bash -c "mysqldump ${EM} | gzip > $(SNAPSHOT_FILE_PATH)"
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" created.")
endif

snapshot-drop:
ifeq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" does not exist!")
else
	@docker-compose exec mysql rm -f $(SNAPSHOT_FILE_PATH)
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" deleted.")
endif

snapshot-restore: drop snapshot-restore-landlord snapshot-restore-tenant
snapshot-restore-landlord:
	@$(MAKE) EM=landlord do-snapshot-restore --no-print-directory
snapshot-restore-tenant:
	@$(MAKE) EM=tenant do-snapshot-restore --no-print-directory
do-snapshot-restore:
	@docker-compose exec ${EM} bash -c "gunzip < $(SNAPSHOT_FILE_PATH) | mysql ${EM}"
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
node-install: do-node-install permissions
do-node-install:
	docker run --rm -v `pwd`:/usr/local/app -w /usr/local/app $(NODE_IMAGE) sh -c "apk add --no-cache git && npm install"
node-cli: do-node-cli permissions
do-node-cli:
	docker run --rm -v `pwd`:/usr/local/app -w /usr/local/app -ti $(NODE_IMAGE) sh
node-build: do-node-build permissions
do-node-build:
	docker run --rm -ti -v `pwd`:/usr/local/app -w /usr/local/app $(NODE_IMAGE) ./node_modules/.bin/gulp build:main-script build:scripts build:less
###< NODE ###
