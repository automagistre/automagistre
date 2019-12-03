.PHONY: test contrib

MAKEFLAGS += --no-print-directory

DEBUG_PREFIX=" [DEBUG] "
DEBUG_ECHO=$(if $(MAKE_DEBUG),@echo ${DEBUG_PREFIX})

ifndef EM
ifdef TENANT
override EM = tenant
else
override EM = landlord
endif
endif

ifndef TENANT
override TENANT = msk
endif

BACKUP_SERVER="s3.automagistre.ru"

COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_NOTE = \033[33m

define OK
    @echo "$(COLOR_INFO)$(if $(filter 1,$(MAKE_DEBUG)),${DEBUG_PREFIX}) [OK] $1$(COLOR_RESET)"
endef
define FAIL
    @>&2 echo "$(if $(MAKE_DEBUG),${DEBUG_PREFIX}) [FAIL] $1$(COLOR_RESET)"
endef
define NOTE
    @echo "$(COLOR_NOTE)$(if $(MAKE_DEBUG),${DEBUG_PREFIX}) [NOTE] $1$(COLOR_RESET)"
endef

notify = $(DEBUG_ECHO) notify-send --urgency=low --expire-time=50 "Makefile" "$@ success!"

contrib:
	@cp -n -r contrib/.env contrib/* ./ || true

docker-hosts-updater:
	docker pull grachev/docker-hosts-updater
	docker rm -f docker-hosts-updater || true
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

###> ALIASES ###
pull:
	docker-compose pull
up: contrib pull composer permissions
	docker-compose up --detach --remove-orphans --no-build
	@$(notify)
cli: app-cli

down:
	docker-compose down -v --remove-orphans
###< ALIASES ###

###> APP ###
build:
	$(DEBUG_ECHO) docker build \
		--target base \
		--tag automagistre/app:base \
		.

APP = $(DEBUG_ECHO) @docker-compose $(if $(EXEC),exec,run --rm )\
	$(if $(ENTRYPOINT),--entrypoint "$(ENTRYPOINT)" )\
	$(if $(APP_ENV),-e APP_ENV=$(APP_ENV) )\
	$(if $(APP_DEBUG),-e APP_DEBUG=$(APP_DEBUG) )\
	app

PERMISSIONS = chown $(shell id -u):$(shell id -g) -R . && chmod 777 -R var/
permissions:
	$(APP) sh -c "$(PERMISSIONS) || true"
	$(call OK,"Permissions fixed.")

app-cli:
	$(APP) bash

composer:
	$(APP) sh -c 'rm -rf var/cache/* && composer install'

MIGRATION_CONSOLE = --em=${EM} $(TENANT_CONSOLE) --no-interaction

migration: migration-landlord migration-tenant
migration-landlord:
	@$(MAKE) do-migration EM=landlord
migration-tenant:
	@$(MAKE) do-migration EM=tenant
do-migration:
	$(APP) console doctrine:migration:migrate --allow-no-migration $(MIGRATION_CONSOLE)

migration-generate: do-migration-generate php-cs-fixer permissions
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
	$(APP) console doctrine:migration:diff $(MIGRATION_CONSOLE) || true
migration-diff-dry:
	$(APP) console doctrine:schema:update --dump-sql --em=${EM} $(TENANT_CONSOLE)

migration-test: APP_ENV=test
migration-test:
	$(APP) console doctrine:schema:validate
	$(APP) console doctrine:schema:validate --em=tenant --tenant=msk

schema-update:
	$(APP) console doctrine:schema:update --force --em=${EM} $(TENANT_CONSOLE)

test: APP_ENV=test
test: APP_DEBUG=1
test: php-cs-fixer cache phpstan psalm database-test migration-test fixtures phpunit

php-cs-fixer:
	$(APP) sh -c 'php-cs-fixer fix $(if $(DRY),--dry-run) $(if $(DEBUG),-vvv); $(PERMISSIONS)'

phpstan: APP_ENV=test
phpstan: APP_DEBUG=1
phpstan: cache
	$(APP) phpstan analyse --configuration phpstan.neon $(if $(DEBUG),--debug -vvv)

phpunit: APP_ENV=test
phpunit:
	$(APP) paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4) --stop-on-failure
requirements: APP_ENV=prod
requirements:
	$(APP) requirements-checker
psalm:
	$(APP) psalm --show-info=false

cache:
	$(APP) sh -c 'rm -rf var/cache/$$APP_ENV && console cache:warmup; $(PERMISSIONS)'

database-test:
	APP_ENV=test $(MAKE) database
	APP_ENV=test TENANT=msk $(MAKE) database

database:
	$(APP) sh -c "console doctrine:database:drop --if-exists --force --connection=${EM} ${TENANT_CONSOLE} && console doctrine:database:create --connection=${EM} ${TENANT_CONSOLE} && console doctrine:migration:migrate --allow-no-migration $(MIGRATION_CONSOLE)"

fixtures: APP_ENV=test
fixtures:
	$(APP) console doctrine:fixtures:load --no-interaction

backup: backup-restore migration
	@$(notify)
backup-update: backup-fresh backup-download backup
backup-latest: backup-download backup
backup-fresh:
	@ssh ${BACKUP_SERVER} automagistre_backup.sh
	$(call OK,"Backups creating on ${BACKUP_SERVER}")
backup-download:
	$(DEBUG_ECHO) @mkdir -p var/backups
	@$(MAKE) do-backup-download EM=landlord
	@$(MAKE) do-backup-download EM=tenant
do-backup-download:
	$(DEBUG_ECHO) @scp -q -o LogLevel=QUIET ${BACKUP_SERVER}:/home/automagistre/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
	$(call OK,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz downloaded.")

drop: drop-landlord drop-tenant
drop-landlord:
	@$(MAKE) do-drop EM=landlord
drop-tenant:
	@$(MAKE) do-drop EM=tenant
do-drop:
	$(APP) sh -c "console doctrine:database:drop --if-exists --force --connection=${EM} ${TENANT_CONSOLE} && console doctrine:database:create --connection=${EM} ${TENANT_CONSOLE}"
###< APP ###

###> MYSQL ###
MYSQL=$(DEBUG_ECHO) @docker-compose exec -T ${EM}$(if $(filter tenant,$(EM)),_$(TENANT))
TENANT_CONSOLE = $(if $(filter tenant,$(EM)),--tenant=$(TENANT))

mysql-cli:
	@$(MYSQL) bash
backup_file = var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
backup-restore: backup-restore-landlord backup-restore-tenant
backup-restore-landlord: drop-landlord
	@$(MAKE) do-backup-restore
backup-restore-tenant: drop-tenant
	@$(MAKE) do-backup-restore EM=tenant
do-backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@$(MYSQL) bash -c "gunzip < /usr/local/app/var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz | mysql ${EM}"
	$(call OK,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}) restored.")
else
	$(call FAIL,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif

SNAPSHOT_FILE_NAME = $(shell git rev-parse --abbrev-ref HEAD | sed 's\#/\#\_\#g')_${EM}$(if $(filter tenant,$(EM)),_$(TENANT)).sql.gz
SNAPSHOT_FILE_PATH = /usr/local/app/var/snapshots/$(SNAPSHOT_FILE_NAME)
SNAPSHOT_FILE_LOCAL = var/snapshots/$(SNAPSHOT_FILE_NAME)

snapshot: snapshot-landlord snapshot-tenant
snapshot-landlord:
	@$(MAKE) do-snapshot EM=landlord
snapshot-tenant:
	@$(MAKE) do-snapshot EM=tenant
do-snapshot:
ifneq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call FAIL,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" already exist! You can use \"snapshot-drop\" recipe.")
else
	$(MYSQL) bash -c "mysqldump ${EM} | gzip > $(SNAPSHOT_FILE_PATH)"
	$(call OK,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" created.")
endif

snapshot-drop: snapshot-drop-landlord snapshot-drop-tenant
snapshot-drop-landlord:
	@$(MAKE) do-snapshot-drop EM=landlord
snapshot-drop-tenant:
	@$(MAKE) do-snapshot-drop EM=tenant
do-snapshot-drop:
ifeq (,$(wildcard $(SNAPSHOT_FILE_LOCAL)))
	$(call FAIL,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" does not exist!")
else
	$(MYSQL) rm -f $(SNAPSHOT_FILE_PATH)
	$(call OK,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" deleted.")
endif

snapshot-restore: snapshot-restore-landlord snapshot-restore-tenant
snapshot-restore-landlord: drop-landlord
	@$(MAKE) do-snapshot-restore EM=landlord
snapshot-restore-tenant: drop-tenant
	@$(MAKE) do-snapshot-restore EM=tenant
do-snapshot-restore:
	$(MYSQL) bash -c "gunzip < $(SNAPSHOT_FILE_PATH) | mysql ${EM}"
	$(call OK,"Snapshot \"$(SNAPSHOT_FILE_NAME)\" restored.")
###< MYSQL ###

###> MEMCACHED ###
memcached-cli:
	docker-compose exec memcached sh
memcached-restart:
	docker-compose restart memcached
###< MEMCACHED ###
