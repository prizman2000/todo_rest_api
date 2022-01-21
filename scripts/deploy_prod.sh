#!/bin/bash

git push

ssh root@147.191.167.23
# shellcheck disable=SC2164
cd /var/www/todo_api/scripts
bash build.sh