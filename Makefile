.PHONY: dev contrib

MAKEFLAGS += --no-print-directory

DEBUG_PREFIX=" [DEBUG] "
DEBUG_ECHO=$(if $(filter 1,$(DEBUG)),@echo ${DEBUG_PREFIX})

ifndef EM
ifdef TENANT
override EM = tenant
else
override EM = landlord
endif
else
ifndef TENANT
override TENANT = msk
endif
endif

BACKUP_SERVER="s2.automagistre.ru"

###> CONSTANTS ###
DOCKER_COMPOSE_VERSION=1.23.1
APP_DIR = .
###< CONSTANTS ###

define success
    @tput setaf 2
    @echo "$(if $(filter 1,$(DEBUG)),${DEBUG_PREFIX}) [OK] $1"
    @tput sgr0
endef
define failed
    @tput setaf 1
    @echo "$(if $(filter 1,$(DEBUG)),${DEBUG_PREFIX}) [FAIL] $1"
    @tput sgr0
endef

notify = $(DEBUG_ECHO) notify-send --urgency="critical" "Makefile: $@" "COMPLETE!"

init:
	cp -n .env.dist .env || true
	cp -n docker-compose.override.yml.dist docker-compose.override.yml || true
	cp -n contrib/* ./ || true
	cp -n -r contrib/* ./ || true
	mkdir -p var/snapshots var/backups
un-init:
	rm -rf .env
re-init: un-init init

bootstrap: init pull do-install docker-hosts-updater do-up cache permissions db-wait fixtures

install: do-install permissions
do-install: app-install

do-update: docker-compose-install pull do-install do-up db-wait permissions cache restart migration
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
	$(DEBUG_ECHO) docker build \
		--build-arg APP_ENV=dev \
		--build-arg APP_DEBUG=1 \
		--build-arg APP_VERSION=dev \
		--build-arg APP_BUILD_TIME="`date --rfc-2822`" \
		--target app \
		--tag $(APP_IMAGE) \
		.
app-push:
	docker push $(APP_IMAGE)

APP = $(DEBUG_ECHO) @docker-compose $(if $(EXEC),exec,run --rm )\
	$(if $(ENTRYPOINT),--entrypoint "$(ENTRYPOINT)" )\
	$(if $(APP_ENV),-e APP_ENV=$(APP_ENV) )\
	$(if $(APP_DEBUG),-e APP_DEBUG=$(APP_DEBUG) )\
	app

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

MIGRATION_CONSOLE = --db=${EM} --em=${EM} $(TENANT_CONSOLE) --no-interaction

migration: migration-landlord migration-tenant
migration-landlord:
	@$(MAKE) do-migration EM=landlord
migration-tenant:
	@$(MAKE) do-migration EM=tenant
do-migration:
	$(APP) console doctrine:migration:migrate --allow-no-migration $(MIGRATION_CONSOLE)

migration-generate: do-migration-generate php-cs-fixer
do-migration-generate:
	$(APP) console doctrine:migrations:generate
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(APP) console doctrine:migration:execute --down $(latest) $(MIGRATION_CONSOLE)

migration-diff: migration-diff-all php-cs-fixer
migration-diff-all: migration-diff-landlord migration-diff-tenant
migration-diff-landlord:
		@$(MAKE) do-migration-diff EM=landlord
migration-diff-tenant:
		@$(MAKE) do-migration-diff EM=tenant
do-migration-diff:
	$(APP) console doctrine:migration:diff $(MIGRATION_CONSOLE)
migration-diff-dry:
	$(APP) console doctrine:schema:update --dump-sql --em=${EM} $(TENANT_CONSOLE)

migration-test: APP_ENV=test
migration-test:
	$(APP) console doctrine:schema:validate

schema-update:
	$(APP) console doctrine:schema:update --force --em=${EM} $(TENANT_CONSOLE)

app-test:
	$(APP) console test

test: APP_ENV=test
test: DRY=true
test: do-php-cs-fixer phpstan migration-test phpunit

do-php-cs-fixer:
	$(APP) php-cs-fixer fix $(if $(filter true,$(DRY)),--dry-run) $(if $(filter 1,$(APP_DEBUG)),-vvv,--quiet $(if $(filter 1,$(DEBUG)),,> /dev/null))
	$(call success,"php-cs-fixer fix")
php-cs-fixer: do-php-cs-fixer permissions

phpstan:
	$(APP) phpstan analyse --configuration phpstan.neon $(if $(filter true,$(DEBUG)),--debug -vvv)

phpunit: APP_ENV=test
phpunit:
	$(APP) paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4) --stop-on-failure
requirements: APP_ENV=prod
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
fixtures: APP_ENV=test
fixtures: database cache do-fixtures
	@$(notify)
do-fixtures:
	$(APP) console doctrine:fixtures:load --no-interaction
backup: backup-restore migration
	@$(notify)
backup-update: backup-fresh backup-download backup
backup-fresh:
	@ssh ${BACKUP_SERVER} automagistre_backup.sh
	$(call success,"Backups creating on ${BACKUP_SERVER}")
backup-download:
	$(DEBUG_ECHO) @mkdir -p var/backups
	@$(MAKE) do-backup-download
	@$(MAKE) do-backup-download "TENANT=msk"
do-backup-download:
	$(DEBUG_ECHO) @scp -q -o LogLevel=QUIET ${BACKUP_SERVER}:/home/automagistre/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
	$(call success,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz downloaded.")

drop: drop-landlord drop-tenant
drop-landlord:
	@$(MAKE) do-drop EM=landlord
drop-tenant:
	@$(MAKE) do-drop EM=tenant
do-drop:
	$(APP) sh -c "console doctrine:database:drop --force --connection=${EM} ${TENANT_CONSOLE} || true && console doctrine:database:create --connection=${EM} ${TENANT_CONSOLE}"

db-wait:
	$(APP) wait-for-it.sh landlord:3306 -- wait-for-it.sh tenant_msk:3306
###< APP ###

###> MYSQL ###
mysql-cli:
	@$(MYSQL) bash
backup_file = $(APP_DIR)/var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
backup-restore: backup-restore-landlord backup-restore-tenant
backup-restore-landlord: drop-landlord
	@$(MAKE) do-backup-restore
backup-restore-tenant: drop-tenant
	@$(MAKE) do-backup-restore TENANT=msk
do-backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@$(MYSQL) bash -c "gunzip < /usr/local/app/var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz | mysql ${EM}"
	$(call success,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}) restored.")
else
	$(call failed,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif

MYSQL=$(DEBUG_ECHO) @docker-compose exec ${EM}$(if $(filter tenant,$(EM)),_$(TENANT))
TENANT_CONSOLE = $(if $(filter tenant,$(EM)),--tenant=$(TENANT))

SNAPSHOT_FILE_NAME = $(shell git rev-parse --abbrev-ref HEAD | sed 's\#/\#\_\#g')_${EM}$(if $(filter tenant,$(EM)),_$(TENANT)).sql.gz
SNAPSHOT_FILE_PATH = /usr/local/app/var/snapshots/$(SNAPSHOT_FILE_NAME)
SNAPSHOT_FILE_LOCAL = $(APP_DIR)/var/snapshots/$(SNAPSHOT_FILE_NAME)

snapshot: snapshot-landlord snapshot-tenant
snapshot-landlord:
	@$(MAKE) do-snapshot EM=landlord
snapshot-tenant:
	@$(MAKE) do-snapshot EM=tenant
do-snapshot:
ifneq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" already exist! You can use \"snapshot-drop\" recipe.")
else
	$(MYSQL) bash -c "mysqldump ${EM} | gzip > $(SNAPSHOT_FILE_PATH)"
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" created.")
endif

snapshot-drop: snapshot-drop-landlord snapshot-drop-tenant
snapshot-drop-landlord:
	@$(MAKE) do-snapshot-drop EM=landlord
snapshot-drop-tenant:
	@$(MAKE) do-snapshot-drop EM=tenant
do-snapshot-drop:
ifeq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call failed,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" does not exist!")
else
	$(MYSQL) rm -f $(SNAPSHOT_FILE_PATH)
	$(call success,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" deleted.")
endif

snapshot-restore: snapshot-restore-landlord snapshot-restore-tenant
snapshot-restore-landlord: drop-landlord
	@$(MAKE) do-snapshot-restore EM=landlord
snapshot-restore-tenant: drop-tenant
	@$(MAKE) do-snapshot-restore EM=tenant
do-snapshot-restore:
	$(MYSQL) bash -c "gunzip < $(SNAPSHOT_FILE_PATH) | mysql ${EM}"
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
