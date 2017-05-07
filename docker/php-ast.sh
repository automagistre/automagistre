#!/usr/bin/env bash

set -e

mkdir /tmp/ast
cd /tmp/ast

git clone https://github.com/nikic/php-ast.git --depth 1 /tmp/ast

phpize && ./configure && make && make install

rm -rf /tmp/ast
