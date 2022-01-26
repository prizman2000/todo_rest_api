#!/bin/bash

echo "Hello world"

rest_todo_api/vendor/bin/php-cs-fixer fix src
git add .
