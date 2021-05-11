.PHONY: test contrib help
.DEFAULT_GOAL := help-short

MAKEFLAGS += --no-print-directory

DEBUG_PREFIX=" [DEBUG] "
DEBUG_ECHO=$(if $(MAKE_DEBUG),@echo ${DEBUG_PREFIX})

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

flag: ## Remove code between //> ${FLAG} and //< ${FLAG} - require FLAG env
	find bin config etc public src templates tests -type f -exec sed -i '/\/\/> ${FLAG}/,/\/\/< ${FLAG}/d' {} +
	grep -rl "//- ${FLAG}" bin config etc public src templates tests | xargs rm
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
latest: do-up backup-latest permissions ## Up project with latest backup from server
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
	--user $(if $(UID),${UID},1000)\
	php-fpm

COMPOSER = $(APP) composer

PERMISSIONS = chown $(shell id -u):$(shell id -g) -R . && chmod 777 -R var/
permissions: UID=root
permissions: ## Fix file permissions in project
	$(APP) sh -c "$(PERMISSIONS) || true"
	$(call OK,"Permissions fixed.")

app-cli:
	$(APP) sh

composer: ### composer install
	$(COMPOSER) install

migration: ## Run migrations
	$(COMPOSER) $@

migration-generate: ## Generate empty migration
	$(COMPOSER) $@
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(APP) console doctrine:migration:execute --down $(latest) --no-interaction

migration-diff: ## Generate diff migrations for landlord and tenant databases
	$(COMPOSER) $@
migration-diff-dry:
	$(COMPOSER) $@

schema: ### Validate database schema
	$(COMPOSER) $@

test: ## Run all checks and tests
	$(COMPOSER) $@

php-cs-fixer: ### Fix coding style
	$(COMPOSER) $@

phpstan: ### Run phpstan
	$(COMPOSER) $@
phpstan-baseline: ### Update phpstan baseline
	$(COMPOSER) $@

phpunit: ### Run phpunit
	$(COMPOSER) $@
paratest: ### Run phpunit in parallel
	$(COMPOSER) $@

phpmetrics: ## Generate phpmetrics to public/phpmetrics folder
	$(COMPOSER) $@

requirements: ### Check symfony requirements
	$(COMPOSER) symfony-requirements

psalm: ### Run psalm
	$(COMPOSER) $@
psalm-baseline: ### Update psalm baseline
	$(COMPOSER) $@

cache: ## Clear then warmup symfony cache
	$(COMPOSER) $@
database: ### Drop database then restore from migrations
	$(COMPOSER) $@

fixtures: ### Load fixtures to database
	$(COMPOSER) $@

backup: ### Restore local backup then run migrations
	$(COMPOSER) database
	@$(MAKE) backup-restore
	$(COMPOSER) migration
	@$(notify)
backup-update: backup-fresh backup-download backup ### Backup production database then download and restore it
backup-latest: backup-download backup ### Download latest backup from server then restore it
backup-fresh:
	@ssh ${BACKUP_SERVER} automagistre_backup.sh
	$(call OK,"Backups creating on ${BACKUP_SERVER}")
backup-download:
	$(DEBUG_ECHO) @mkdir -p var/backups
	@$(MAKE) do-backup-download
do-backup-download:
	$(DEBUG_ECHO) @scp -q -o LogLevel=QUIET ${BACKUP_SERVER}:/opt/am/db/backups/tenant_$(TENANT).sql.gz var/backups/tenant_$(TENANT).sql.gz
	$(call OK,"Backup tenant_$(TENANT).sql.gz downloaded.")

drop: drop-connection do-drop ### Drop database
drop-connection:
	$(APP) console doctrine:query:sql "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'db$(if $(filter test,$(APP_ENV)),_test)' AND pid <> pg_backend_pid();" || true
do-drop:
	$(APP) sh -c "console doctrine:database:drop --if-exists --force && console doctrine:database:create"
###< APP ###

###> DATABASE ###
DB=$(DEBUG_ECHO) @docker-compose exec db

db:
	$(DB) bash

backup_file = var/backups/tenant_$(TENANT).sql.gz
backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@$(DB) bash -c "gunzip < /usr/local/app/var/backups/tenant_$(TENANT).sql.gz | psql -U db"
	$(call OK,"Backup tenant_$(TENANT).sql.gz restored.")
else
	$(call FAIL,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif
###< DATABASE ###
