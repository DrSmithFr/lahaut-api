APP_DIR := $(abspath $(lastword $(MAKEFILE_LIST)))

.PHONY: hooks

build: reload
install: env hooks dependencies start build start database
reload: kill start

env:
	sudo apt install php8.1-cli php8.1-fpm php8.1-common php8.1-curl php8.1-pgsql php8.1-xml php8.1-mbstring php8.1-intl php-xdebug
	sudo apt-get install php8.1-sqlite
	sudo apt install nginx

dependencies:
	symfony composer self-update --2
	symfony composer install

assets:
	symfony console assets:install --symlink
	npm install

start:
	docker compose -f docker-compose.yml -f docker-compose.override.yml up -d
	npm run watch

kill:
	docker compose kill
	docker compose rm -f

database:
	-symfony console doctrine:database:drop --force
	symfony console doctrine:database:create
	symfony console doctrine:migration:migrate -n

fixtures:
	symfony console doctrine:fixtures:load -n

migration:
	symfony console doctrine:migration:diff

test:
	symfony php bin/phpunit --stop-on-failure

mock: database fixtures
	symfony console app:monitor:mock boby
	symfony console app:monitor:mock lola
	symfony console app:monitor:mock fred

hooks:
	chmod +x hooks/pre-commit.sh
	chmod +x hooks/pre-push.sh
	rm -f .git/hooks/pre-commit
	rm -f .git/hooks/pre-push
	ln -s -f ../../hooks/pre-commit.sh .git/hooks/pre-commit
	ln -s -f ../../hooks/pre-push.sh .git/hooks/pre-push

jwt:
	mkdir -p config/jwt
	openssl genrsa -out config/jwt/private.pem -aes256 4096
	openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
	openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
	mv config/jwt/private2.pem config/jwt/private.pem
	chmod 700 config/jwt/*
