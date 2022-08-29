.PHONY: install up down test copy-files composer-install bash migrate

include .env

DC := docker-compose exec frontphp bash -c

setup: copy-files build composer-install up

install: up migrate seed

up:
	docker-compose up -d --force-recreate

down:
	docker-compose down --remove-orphans

build:
	docker-compose build

test:
	docker-compose run frontphp vendor/bin/phpunit

copy-files:
	if [ ! -f .env ]; then cp .env.example .env; fi

composer-install:
	docker-compose run frontphp composer install

bash:
	docker-compose run frontphp bash

migrateclean:
	${DC} "php artisan key:generate && php artisan optimize && php artisan cache:clear && php artisan route:clear && php artisan config:clear && php artisan view:clear"

migrate:
	${DC} "php artisan migrate"

cc:
	${DC} "php artisan optimize && php artisan config:clear && php artisan cache:clear"

run:
	${DC} "$(php)"

test:
	${DC} "php vendor/bin/phpunit"



