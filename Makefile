exec=php -v
p=

SERVICE_NAME=more-less

# docker vars
DOCKER_SERVICE=${SERVICE_NAME}-service
DOCKER_IMAGE=${SERVICE_NAME}-img
DOCKER_CONTAINER=${SERVICE_NAME}-cont

DOCKER_PHP=php-fpm
DOCKER_MYSQL=mysql

COMPOSE=docker-compose --project-directory ./docker

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

logs:
	${COMPOSE} logs -f

########## App ##########

app-after-install: composer-install

composer-install:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer install

composer-update:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer update ${p}

composer-require:
	${COMPOSE} run --rm ${DOCKER_SERVICE}-${DOCKER_PHP} composer require ${p} --sort-packages
