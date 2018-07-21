#!/bin/bash

set -e

docker-php-ext-enable xdebug

exec "$@"
