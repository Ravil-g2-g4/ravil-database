.PHONY: setup
setup: build up php-bash composer-install create-table down
# подготовка к работе (только при первом запуске)

.PHONY: start
start: up php-bash index
# запуск программы

.PHONY: stop
stop: exit down
# остановка программы

.PHONY: build
build: # билдим программу
	docker-compose build


.PHONY: up
up: # поднимаем контейнеры
	docker-compose up -d


.PHONY: down
down: # роняем контейнеры
	docker-compose down


.PHONY: php-bash
php-bash: # прыгаем в контейнер с php
	docker-compose exec php bash


.PHONY: composer-install
composer-install: # ставим composer
	composer install


.PHONY: create-table
create-table: # создаем таблицу пользователей
	php createTable.php


.PHONY: index
index: # запуск файла index.php (основная программа)
	php index.php

.PHONY: exit
exit: # выход
	exit

