.PHONY: app compose contrib deploy test

DOCKER_COMPOSE_VERSION=1.21.2
DOCKER_UBUNTU_VERSION=18.05.0~ce-0~ubuntu

ifeq ($(wildcard $(app_dir)/.php_cs),)
    php_cs_config = .php_cs.dist
else
    php_cs_config = .php_cs
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
		--file $(compose_path)/docker-compose.$1.yml \
		config > docker-compose.tmp \
	&& mv -f docker-compose.tmp docker-compose.yml
	$(call success,"docker-compose.yml merged with [$1] environemnt")
endef

app_dir=.
app_image=automagistre/app:dev
compose_path = ./compose

notify = notify-send --urgency="critical" "Makefile: $@" "COMPLETE!"

init:
	cp -n $(app_dir)/.env.dist $(app_dir)/.env || true
	cp -n $(app_dir)/docker/php/php.ini $(app_dir)/docker/php/php-dev.ini || true
	cp -n ./contrib/* ./ || true
	mv -f ./git/hooks/* ./.git/hooks/ || true
	cp -n -r $(app_dir)/contrib/* $(app_dir)/ || true
	mkdir -p $(app_dir)/var/null $(app_dir)/var/snapshots && touch $(app_dir)/var/null/composer.null && touch $(app_dir)/var/null/package.null
un-init:
	rm -rf $(app_dir)/.env
re-init: un-init init

bootstrap: init pull do-install-parallel docker-hosts-updater do-up cache permissions db-wait fixtures

do-install: install-app
do-install-parallel:
	@$(MAKE) --no-print-directory -j2 do-install
install: do-install

do-update: docker-install-compose pull do-install-parallel do-up db-wait permissions cache restart migration
update: do-update
	@$(notify)
master: git-check-stage-is-clear git-fetch git-checkout-master git-reset-master do-update
	@$(notify)

default:
	@docker-compose --file $(compose_path)/docker-compose.yml config > docker-compose.yml
	$(call success,"docker-compose.yml was reset to default")
dev-app:
	$(call compose-extend,dev-app)
dev-memcached:
	$(call compose-extend,dev-memcached)

prod: default
dev: default dev-app dev-memcached

qa: git-reset prod pull do-install-parallel cache docker-rm-restartable do-up clear-logs
	@$(notify)

clear-logs: app-clear-logs

permissions:
	@docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R $(app_dir)/var || true"
	$(call success,"Permissions fixed.")
docker-hosts-updater:
	docker rm -f docker-hosts-updater || true
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

# To prevent idea to adding this phar to *.iml config
vendor-phar-remove:
	@rm -rf $(app_dir)/vendor/twig/twig/test/Twig/Tests/Loader/Fixtures/phar/phar-sample.phar $(app_dir)/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/includes/ProjectWithXsdExtensionInPhar.phar $(app_dir)/vendor/phpunit/phpunit/tests/_files/phpunit-example-extension/tools/phpunit.d/phpunit-example-extension-1.0.1.phar $(app_dir)/vendor/phar-io/manifest/tests/_fixture/test.phar || true

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

###> DOCKER
docker-install: docker-install-engine docker-install-compose
docker-install-engine:
	curl -fsSL get.docker.com | sh
	sudo usermod -a -G docker `whoami`
docker-install-compose:
ifneq ($(shell docker-compose version --short), $(DOCKER_COMPOSE_VERSION))
	sudo rm -rf /usr/local/bin/docker-compose /etc/bash_completion.d/docker-compose
	sudo curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
	sudo chmod +x /usr/local/bin/docker-compose
	sudo curl -L https://raw.githubusercontent.com/docker/compose/$(DOCKER_COMPOSE_VERSION)/contrib/completion/bash/docker-compose -o /etc/bash_completion.d/docker-compose
endif
docker-upgrade: docker-upgrade-engine docker-install-compose
docker-upgrade-engine:
	sudo apt-get remove -y docker-ce && sudo apt-get install docker-ce=$(DOCKER_UBUNTU_VERSION)

pull:
	docker-compose pull
do-up:
	docker-compose up --detach --remove-orphans
up: do-up
	@$(notify)
status:
	watch docker-compose ps
cli: cli-app

restartable = $(shell docker inspect --format='{{index .Config.Labels "com.docker.compose.service"}}' `docker ps --filter label="restartable=true" --quiet`)

restart:
	docker-compose restart $(restartable)
docker-rm-restartable:
	docker-compose rm --force --stop $(restartable) || true
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs-all:
	docker-compose logs --follow
###< DOCKER ###

###> APP ###
app = docker-compose run --rm -e XDEBUG=false -e WAIT_HOSTS=false app
app-xdebug = docker-compose run --rm -e WAIT_HOSTS=false app
app-prod = docker-compose run --rm -e APP_ENV=prod -e APP_DEBUG=0 -e XDEBUG=false -e WAIT_HOSTS=false app
app-test = docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=1 -e XDEBUG=false -e WAIT_HOSTS=false app
php = docker-compose run --rm --entrypoint php app -d memory_limit=-1
php-xdebug = docker-compose run --rm --entrypoint docker-entrypoint-xdebug.sh app php -d memory_limit=-1
sh = docker-compose run --rm --entrypoint sh app -c

build-app:
	docker build --tag "$(app_image)" --target app --build-arg SOURCE_DIR=var/null --build-arg APP_VERSION=dev --build-arg APP_BUILD_TIME="`date --rfc-2822`" $(app_dir)
push-app:
	docker push $(app_image)

cli-app:
	$(app) bash
	@$(MAKE) --no-print-directory permissions > /dev/null
cli-app-xdebug:
	$(app-xdebug) bash
	@$(MAKE) --no-print-directory permissions > /dev/null
restart-app:
	docker-compose restart app
logs-app:
	docker-compose logs --follow app

install-app: composer

composer = docker-compose run --rm --no-deps -e XDEBUG=false -e MIGRATIONS=false -e WAIT_HOSTS=false app composer
composer: composer-install
composer-install: cache-clear
	$(composer) install --prefer-dist
	@$(MAKE) --no-print-directory permissions > /dev/null
	@$(MAKE) --no-print-directory vendor-phar-remove
composer-run-script:
	$(composer) run-script symfony-scripts
composer-update:
	$(composer) update --prefer-dist
	@$(MAKE) --no-print-directory permissions > /dev/null
	@$(MAKE) --no-print-directory vendor-phar-remove
composer-update-lock:
	$(composer) update --lock
	@$(MAKE) --no-print-directory permissions > /dev/null
composer-outdated:
	$(composer) outdated

migration:
	@$(app) console doctrine:migration:migrate --no-interaction --allow-no-migration
migration-generate:
	$(app) console doctrine:migrations:generate
	@$(MAKE) --no-print-directory permissions > /dev/null
	@$(MAKE) --no-print-directory php-cs-fixer
migration-rollback:latest = $(shell $(app) console doctrine:migration:latest | tr '\r' ' ')
migration-rollback:
	$(app) console doctrine:migration:execute --down --no-interaction $(latest)
migration-diff:
	$(app) console doctrine:migration:diff
	@$(MAKE) --no-print-directory permissions > /dev/null
	@$(MAKE) --no-print-directory php-cs-fixer
migration-diff-dry:
	$(app) console doctrine:schema:update --dump-sql
schema-update:
	$(app) console doctrine:schema:update --force

app-test:
	$(app) console test

test: php-cs-fixer-test cache-test phpstan schema-test phpunit-test

php-cs-fixer = vendor/bin/php-cs-fixer fix --config $(php_cs_config)
php-cs-fixer:
	$(php) $(php-cs-fixer)
php-cs-fixer-debug:
	$(php-xdebug) $(php-cs-fixer) -vvv
	@$(MAKE) --no-print-directory permissions > /dev/null
php-cs-fixer-test:
	$(php) vendor/bin/php-cs-fixer fix --config=.php_cs.dist --verbose --dry-run

phpstan = vendor/bin/phpstan analyse --configuration phpstan.neon
phpstan:
	$(php) $(phpstan)
phpstan-debug:
	$(php-xdebug) $(phpstan) --debug
phpunit = $(app-test) paratest -p $(shell grep -c ^processor /proc/cpuinfo || 4)
phpunit:
	$(phpunit) --stop-on-failure
phpunit-test:
	$(phpunit)
requirements:
	$(app-test) requirements-checker
schema-test:
	$(app-test) console doctrine:schema:validate

cache: cache-clear cache-warmup
cache-prod: cache-clear-prod cache-warmup-prod
cache-test: cache-clear-test cache-warmup-test
cache-clear:
	@$(sh) 'rm -rf ./var/cache/"$$APP_ENV"' || true
	@$(MAKE) --no-print-directory permissions > /dev/null
cache-profiler:
	@$(sh) 'rm -rf ./var/cache/"$$APP_ENV"/profiler' || true
cache-warmup:
	@$(app) console cache:warmup
	@$(MAKE) --no-print-directory permissions > /dev/null
cache-clear-prod:
	@$(sh) 'rm -rf ./var/cache/prod'
	@$(MAKE) --no-print-directory permissions > /dev/null
cache-warmup-prod:
	@$(app-prod) console cache:warmup
	@$(MAKE) --no-print-directory permissions > /dev/null
cache-clear-test:
	@$(sh) 'rm -rf ./var/cache/test'
	@$(MAKE) --no-print-directory permissions > /dev/null
cache-warmup-test:
	@$(app-test) console cache:warmup
	@$(MAKE) --no-print-directory permissions > /dev/null

app-clear-logs:
	$(sh) 'rm -rf var/logs/*'

do-fixtures: cache-test
	$(app-test) console doctrine:fixtures:load --no-interaction
database: drop migration
fixtures: database do-fixtures
	@$(notify)
backup: drop backup-restore migration
	@$(notify)
drop:
	@$(sh) "console doctrine:database:drop --force || true && console doctrine:database:create"
db-wait:
	@docker-compose run --rm -e XDEBUG=false app /bin/true > /dev/null
###< APP ###

###> MYSQL ###
cli-mysql:
	docker-compose exec mysql bash
restart-mysql:
	docker-compose restart mysql
logs-mysql:
	docker-compose logs --follow mysql

backup_file = $(app_dir)/var/backup.sql.gz
backup-restore:
ifneq (,$(wildcard $(backup_file)))
	@docker-compose exec mysql bash -c "gunzip < /usr/local/app/var/backup.sql.gz | mysql db"
	$(call success,"Backup restored.")
else
	$(call failed,"Backup \"$(backup_file)\" does not exist!")
	@exit 1
endif

snapshot_filename = $(shell git rev-parse --abbrev-ref HEAD | sed 's\#/\#\_\#g').sql.gz
snapshot_file = /usr/local/app/var/snapshots/$(snapshot_filename)
snapshot_file_local = $(app_dir)/var/snapshots/$(snapshot_filename)
snapshot:
ifneq (,$(wildcard $(snapshot_file_local)))
	$(call failed,"Snapshot \"$(snapshot_filename)\" already exist! You can use \"snapshot-drop\" recipe.")
else
	@docker-compose exec mysql bash -c "mysqldump db | gzip > $(snapshot_file)"
	$(call success,"Snapshot \"$(snapshot_filename)\" created.")
endif
snapshot-drop:
ifeq (,$(wildcard $(snapshot_file_local)))
	$(call failed,"Snapshot \"$(snapshot_filename)\" does not exist!")
else
	@docker-compose exec mysql rm -f $(snapshot_file)
	$(call success,"Snapshot \"$(snapshot_filename)\" deleted.")
endif

restore: drop do-restore migration
do-restore:
	@docker-compose exec mysql bash -c "gunzip < $(snapshot_file) | mysql db"
	$(call success,"Snapshot \"$(snapshot_filename)\" restored.")
###< MYSQL ###

###> MEMCACHED ###
cli-memcached:
	docker-compose exec memcached sh
restart-memcached:
	docker-compose restart memcached

build-memcached:
	docker build --tag otrada/memcached:latest ./memcached/
###< MEMCACHED ###
