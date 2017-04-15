cs:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app php-cs-fixer fix --config .php_cs.dist
phpstan:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app phpstan analyse --level 5 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 app phpunit
