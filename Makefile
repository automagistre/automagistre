init:
	cp docker-compose.yml.dist docker-compose.yml
	cp .env.dist .env
install:
	docker-compose build
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app composer install --prefer-dist
	$(MAKE) permissions
up:
	docker-compose up -d

check: cache-clean cs-check phpstan yaml-lint schema-check phpunit
cs:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app php-cs-fixer fix --config .php_cs.dist
cs-check:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app php-cs-fixer fix --config=.php_cs.dist --verbose --dry-run
phpstan:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app phpstan analyse --level 5 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 app phpunit
requirements:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 app symfony_requirements
yaml-lint:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 app console lint:yaml etc
schema-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 app console doctrine:schema:validate


cache-clean:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app rm -rf var/cache/*

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"
