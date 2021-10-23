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
	$(DEBUG_ECHO) @cp -n -r contrib/* ./ || true

docker-hosts-updater:
	$(DEBUG_ECHO) docker pull grachev/docker-hosts-updater
	$(DEBUG_ECHO) docker rm -f docker-hosts-updater || true
	$(DEBUG_ECHO) docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

###> ALIASES ###
pull:
	$(DEBUG_ECHO) docker-compose pull
do-up: contrib pull composer permissions
	$(DEBUG_ECHO) docker-compose up --detach --remove-orphans --no-build \
	nginx \
	php-fpm \
	postgres \
	redis \
	nsqd \
	nsqadmin \
	host.docker.internal

up: do-up ## Up project
	@$(notify)
latest: do-up backup-latest permissions ## Up project with latest backup from server
	@$(notify)
cli: ## Get terminal inside app container
	$(APP) sh
cli-root: UID=root
cli-root:
	$(APP) sh

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

PERMISSIONS = chown $(shell id -u):$(shell id -g) -R . && chmod 777 -R var/
permissions: UID=root
permissions: ## Fix file permissions in project
	sudo $(PERMISSIONS)
	$(call OK,"Permissions fixed.")

test: ## Run all checks and tests
	# todo

###< APP ###

###> DATABASE ###

backup: ### Restore local backup then run migrations
	@$(MAKE) backup-restore
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
