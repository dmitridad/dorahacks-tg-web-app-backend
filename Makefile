exec=php -v
package=

SERVICE_NAME=system

# docker vars
DOCKER_SERVICE=${SERVICE_NAME}-service
DOCKER_IMAGE=${SERVICE_NAME}-img
DOCKER_CONTAINER=${SERVICE_NAME}-cont

DOCKER_PHP=php-cli
DOCKER_MYSQL=mysql
DOCKER_DATASTORE=datastore

COMPOSE=docker-compose --project-directory ./docker-local

pre-install: auth-json

# launching the service
install: build start app-after-install

########## Docker ##########

build:
	${COMPOSE} build

build-no-cache:
	${COMPOSE} build --no-cache

rebuild-php:
	${COMPOSE} build ${DOCKER_SERVICE}-${DOCKER_PHP}

remove-php:
	docker rmi ${DOCKER_SERVICE}-${DOCKER_PHP}

start:
	${COMPOSE} up -d

stop:
	${COMPOSE} down --remove-orphans

stop-hard:
	${COMPOSE} down -v

restart: stop start

php:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} ${exec}

mysql:
	docker exec -it ${DOCKER_CONTAINER}-${DOCKER_MYSQL} bash

datastore:
	docker exec -it ${DOCKER_CONTAINER}-${DOCKER_DATASTORE} bash

logs:
	${COMPOSE} logs -f

########## App ##########

app-after-install: composer-install

composer-install:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer install

composer-update:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer update ${package}

auth-json:
	cp docker-local/php/cli/conf/auth.json.sample docker-local/php/cli/conf/auth.json

fixer:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} vendor/bin/php-cs-fixer fix src
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} vendor/bin/php-cs-fixer fix tests

update-shared:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer update route4me/laravel-shared-modules