#!/bin/bash

git pull

composer update
composer install

php bin/console cache:clear
php bin/console cache:warmap