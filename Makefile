.PHONY: test contrib help
.DEFAULT_GOAL := help-short

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

notify = $(DEBUG_ECHO) notify-send --urgency=low --expire-time=50 "Success!" "make $@"

help-short:
	@grep -E '^[a-zA-Z_-]+:[ a-zA-Z_-]+?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

contrib:
	$(DEBUG_ECHO) @cp -n -r contrib/.env contrib/* ./ || true

docker-hosts-updater:
	$(DEBUG_ECHO) docker pull grachev/docker-hosts-updater
	$(DEBUG_ECHO) docker rm -f docker-hosts-updater || true
	$(DEBUG_ECHO) docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

###> ALIASES ###
pull:
	$(DEBUG_ECHO) docker-compose pull
do-up: contrib pull composer permissions
	$(DEBUG_ECHO) docker-compose up --detach --remove-orphans --no-build
up: do-up ## Up project
	@$(notify)
latest: do-up backup-latest ## Up project with latest backup from server
	@$(notify)
cli: app-cli ## Get terminal inside php container

down: ## Stop and remove all containers, volumes and networks
	$(DEBUG_ECHO) docker-compose down -v --remove-orphans
###< ALIASES ###

###> APP ###
APP = $(DEBUG_ECHO) @docker-compose $(if $(EXEC),exec,run --rm )\
	$(if $(ENTRYPOINT),--entrypoint "$(ENTRYPOINT)" )\
	$(if $(APP_ENV),-e APP_ENV=$(APP_ENV) )\
	$(if $(APP_DEBUG),-e APP_DEBUG=$(APP_DEBUG) )\
	app

PERMISSIONS = chown $(shell id -u):$(shell id -g) -R . && chmod 777 -R var/
permissions: ## Fix file permissions in project
	$(APP) sh -c "$(PERMISSIONS) || true"
	$(call OK,"Permissions fixed.")

app-cli:
	$(APP) bash

composer: ### composer install
	$(APP) sh -c 'rm -rf var/cache/* && composer install'

MIGRATION_CONSOLE = --em=${EM} $(TENANT_CONSOLE) --no-interaction

migration: migration-landlord migration-tenant ## Run migrations
migration-landlord:
	@$(MAKE) APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) do-migration EM=landlord
migration-tenant:
	@$(MAKE) APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) do-migration EM=tenant
do-migration:
	$(APP) console doctrine:migration:migrate --allow-no-migration $(MIGRATION_CONSOLE)

migration-generate: do-migration-generate php-cs-fixer permissions ## Generate empty migration
do-migration-generate:
	$(APP) console doctrine:migrations:generate
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(APP) console doctrine:migration:execute --down $(latest) $(MIGRATION_CONSOLE)

migration-diff: migration-diff-all php-cs-fixer ## Generate diff migrations for landlord and tenant databases
migration-diff-all: migration-diff-landlord migration-diff-tenant
migration-diff-landlord: ### Generate diff migration for landlord database
	@$(MAKE) do-migration-diff EM=landlord
migration-diff-tenant: ### Generate diff migration for tenant database
	@$(MAKE) do-migration-diff EM=tenant
do-migration-diff:
	$(APP) console doctrine:migration:diff --formatted $(MIGRATION_CONSOLE) && sed -i "s/this->abortIf.*/&\n\n        \$$this->skipIf(0 !== strpos(\$$this->connection->getDatabase(), '${EM}'), '${EM} only');/" `git ls-files --others --exclude-standard | tail -n 1` || true
migration-diff-dry:
	$(APP) console doctrine:schema:update --dump-sql --em=${EM} $(TENANT_CONSOLE)

migration-validate: ### Validate database schema for landlord and tenant databases
	$(APP) console doctrine:schema:validate
	$(APP) console doctrine:schema:validate --em=tenant --tenant=msk

schema-update:
	$(APP) console doctrine:schema:update --force --em=${EM} $(TENANT_CONSOLE)

test: APP_ENV=test
test: APP_DEBUG=1
test: php-cs-fixer cache phpstan psalm doctrine-ensure-production-settings database migration-validate fixtures paratest ## Run all checks and tests

php-cs-fixer: ### Fix coding style
	$(APP) sh -c 'php-cs-fixer fix $(if $(DRY),--dry-run) $(if $(DEBUG),-vvv); $(PERMISSIONS)'

phpstan: APP_ENV=test
phpstan: APP_DEBUG=1
phpstan: cache ### Run phpstan
	$(APP) phpstan analyse --configuration phpstan.neon $(if $(DEBUG),--debug -vvv)
phpstan-baseline: APP_ENV=test
phpstan-baseline: APP_DEBUG=1
phpstan-baseline: cache ### Update phpstan baseline
	$(APP) phpstan analyse --configuration phpstan.neon --generate-baseline

phpunit: APP_ENV=test
phpunit: APP_DEBUG=1
phpunit: clear-log ### Run phpunit
	$(APP) phpunit --stop-on-failure
paratest: APP_ENV=test
paratest: APP_DEBUG=1
paratest: clear-log ### Run paratest
	$(APP) paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4) --stop-on-failure

requirements: APP_ENV=prod
requirements: ### Check symfony requirements
	$(APP) requirements-checker APP_ENV=prod APP_DEBUG=0

psalm: ### Run psalm
	$(APP) psalm --show-info=false
psalm-baseline: ### Update psalm baseline
	$(APP) psalm --update-baseline --set-baseline=psalm-baseline.xml

doctrine-ensure-production-settings: APP_ENV=prod
doctrine-ensure-production-settings: APP_DEBUG=0
doctrine-ensure-production-settings:
	$(APP) sh -c 'rm -rf var/cache/$$APP_ENV && console doctrine:ensure-production-settings'

cache-prod:
	@$(MAKE) APP_ENV=prod APP_DEBUG=0 cache
cache: ## Clear then warmup symfony cache
	$(APP) sh -c 'rm -rf var/cache/$$APP_ENV && console cache:warmup; $(PERMISSIONS)'
clear-log:
	$(APP) rm -rf var/log/$$APP_ENV.log
database: drop migration ### Drop database then restore from migrations

fixtures: APP_ENV=test
fixtures: ### Load fixtures for landlord and tenant databases
	$(APP) console doctrine:fixtures:load --no-interaction --group landlord
	$(APP) console doctrine:fixtures:load --no-interaction --group tenant --em=tenant --tenant=msk

backup: backup-restore migration ### Restore local backup then run migrations
	@$(notify)
backup-update: backup-fresh backup-download backup ### Backup production database then download and restore it
backup-latest: backup-download backup ### Download latest backup from server then restore it
backup-fresh:
	@ssh ${BACKUP_SERVER} automagistre_backup.sh
	$(call OK,"Backups creating on ${BACKUP_SERVER}")
backup-download:
	$(DEBUG_ECHO) @mkdir -p var/backups
	@$(MAKE) do-backup-download EM=landlord
	@$(MAKE) do-backup-download EM=tenant
do-backup-download:
	$(DEBUG_ECHO) @scp -q -o LogLevel=QUIET ${BACKUP_SERVER}:/opt/am/db/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
	$(call OK,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz downloaded.")

drop: drop-landlord drop-tenant ### Drop landlord and tenant databases
drop-landlord: ### Drop landlord database
	@$(MAKE) EM=landlord APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) drop-connection
	@$(MAKE) EM=landlord APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) do-drop
drop-tenant: ### Drop tenant database
	@$(MAKE) EM=tenant APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) drop-connection
	@$(MAKE) EM=tenant APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) do-drop
drop-connection:
	$(APP) console doctrine:query:sql --connection=${EM} ${TENANT_CONSOLE} "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '${EM}$(if $(filter test,$(APP_ENV)),_test)' AND pid <> pg_backend_pid();" || true
do-drop:
	$(APP) sh -c "console doctrine:database:drop --if-exists --force --connection=${EM} ${TENANT_CONSOLE} && console doctrine:database:create --connection=${EM} ${TENANT_CONSOLE}"
###< APP ###

###> DATABASE ###
DB=$(DEBUG_ECHO) @docker-compose exec -T ${EM}$(if $(filter tenant,$(EM)),_$(TENANT))
TENANT_CONSOLE = $(if $(filter tenant,$(EM)),--tenant=$(TENANT))

backup_file = var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz
backup-restore: backup-restore-landlord backup-restore-tenant
backup-restore-landlord: drop-landlord
	@$(MAKE) do-backup-restore
backup-restore-tenant: drop-tenant
	@$(MAKE) do-backup-restore EM=tenant
do-backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@$(DB) bash -c "gunzip < /usr/local/app/var/backups/$(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}).sql.gz | psql -U db ${EM}"
	$(call OK,"Backup $(if $(filter tenant,$(EM)),tenant_$(TENANT),${EM}) restored.")
else
	$(call FAIL,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif
###< DATABASE ###
