.PHONY: test contrib help
.DEFAULT_GOAL := help-short

MAKEFLAGS += --no-print-directory

DEBUG_PREFIX=" [DEBUG] "
DEBUG_ECHO=$(if $(MAKE_DEBUG),@echo ${DEBUG_PREFIX})

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
	$(DEBUG_ECHO) docker-compose up --detach --remove-orphans --no-build \
	traefik \
	crm \
	nginx \
	php-fpm \
	postgres \
	redis \
	nsqd \
	nsqadmin \
	host.docker.internal

up: down do-up up-hasura up-crm ## Up project
	@$(notify)
latest: down do-up backup-latest up-hasura permissions ## Up project with latest backup from server
	@$(notify)

cli: ## Get terminal inside php container
	$(APP) sh
cli-root: UID=root
cli-root:
	$(APP) sh

down: ## Stop and remove all containers, volumes and networks
	$(DEBUG_ECHO) docker-compose down -v --remove-orphans
###< ALIASES ###

rector:
	$(DEBUG_ECHO) docker-compose run --rm rector

###> Hasura ###
console: ## Get terminal inside hasura-console container
	docker-compose exec hasura-console bash

up-hasura:
	docker-compose up -d --force-recreate hasura
	docker-compose exec postgres sh -c "until nc -z hasura 80; do sleep 0.5; done"
	docker-compose up -d --force-recreate hasura-console
###< Hasura ###


###> CRM ###
CRM = $(DEBUG_ECHO) @docker-compose $(if $(EXEC),exec,run --rm ) \
	crm

crm-npm-install:
	$(CRM) npm install

up-crm: crm-npm-install
	$(DEBUG_ECHO) docker-compose up -d crm

cli-crm:
	$(DEBUG_ECHO) docker-compose run --rm --entrypoint sh crm
###< CRM ###

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

composer: ### composer install
	$(COMPOSER) install

outdated: ## Show outdated composer packages
	$(COMPOSER) $@

migration: ## Run migrations
	$(DEBUG_ECHO) @docker-compose exec hasura-console sh -c " \
		hasura-cli metadata apply \
		&& hasura-cli migrate status --database-name default \
		&& hasura-cli migrate apply --all-databases \
		&& hasura-cli metadata reload \
		&& hasura-cli migrate status --database-name default \
		"

migration-generate: ## Generate empty migration
	$(COMPOSER) $@
migration-rollback:latest = $(shell make app-cli CMD="console doctrine:migration:latest" | tr '\r' ' ')
migration-rollback:
	$(APP) console doctrine:migration:execute --down $(latest) --no-interaction

migration-diff: ## Generate diff migrations for database
	$(COMPOSER) $@
migration-diff-dry:
	$(COMPOSER) $@

schema: ### Validate database schema
	$(COMPOSER) $@

test: ## Run all checks and tests
	$(DEBUG_ECHO) @docker-compose exec postgres sh -c "\
		psql -U db -c \"SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'db_test' AND pid <> pg_backend_pid();\"; \
		psql -U db -c \"DROP DATABASE db_test\" \
		&& psql -U db -c \"CREATE DATABASE db_test\" \
		"
	$(DEBUG_ECHO) @docker-compose up -d --force-recreate hasura-test
	$(DEBUG_ECHO) @docker-compose exec postgres sh -c "until nc -z hasura-test 80; do sleep 0.5; done"
	$(DEBUG_ECHO) @docker-compose exec hasura-test sh -c " \
		hasura-cli metadata apply \
		&& hasura-cli migrate status --database-name default \
		&& hasura-cli migrate apply --all-databases \
		&& hasura-cli metadata reload \
		&& hasura-cli migrate status --database-name default \
		"
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
	$(APP) sh -c " \
		bin/console doctrine:query:sql --quiet \"SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'db' AND pid <> pg_backend_pid();\"; \
		bin/console doctrine:database:drop --if-exists --force; \
		bin/console doctrine:database:create \
	"

fixtures: ### Load fixtures to database
	$(COMPOSER) $@

backup: database backup-restore ### Restore local backup then run migrations
	@$(notify)
backup-update: backup-fresh backup-download backup ### Backup production database then download and restore it
backup-latest: backup-download backup ### Download latest backup from server then restore it
backup-fresh:
	$(DEBUG_ECHO) @ssh ${BACKUP_SERVER} 'docker exec -i $$(docker ps --filter name=automagistre_postgres_backup -q | head -1) /backup.sh'
	$(call OK,"Backups creating on ${BACKUP_SERVER}")
backup-download:
	$(DEBUG_ECHO) @mkdir -p var/backups
	@$(MAKE) do-backup-download
do-backup-download:
	$(DEBUG_ECHO) @scp -q -o LogLevel=QUIET ${BACKUP_SERVER}:$$(ssh ${BACKUP_SERVER} ls -t /opt/am/backups/postgres/*automagistre.sql.gz | head -1) $(backup_file)
	$(call OK,"Backup automagistre.sql.gz downloaded.")

drop: drop-connection do-drop ### Drop database
drop-connection:
	$(APP) console doctrine:query:sql "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = 'db$(if $(filter test,$(APP_ENV)),_test)' AND pid <> pg_backend_pid();" || true
do-drop:
	$(APP) sh -c "console doctrine:database:drop --if-exists --force && console doctrine:database:create"
###< APP ###

###> DATABASE ###
DB=$(DEBUG_ECHO) @docker-compose exec -w /usr/local/app postgres

backup_file = var/backups/automagistre.sql.gz
backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@$(DB) bash -c "gunzip < $(backup_file) | psql -U db"
	$(call OK,"Backup $(backup_file) restored.")
else
	$(call FAIL,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif
###< DATABASE ###
