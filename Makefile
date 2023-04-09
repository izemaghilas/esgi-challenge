# docker commands

# build images
up:
	docker-compose --env-file "./api/.env.local" up -d
# remove all services
down:
	docker-compose --env-file "./api/.env.local" down
# stop services
stop:   
	docker-compose --env-file "./api/.env.local" stop
# start services
start:
	docker-compose --env-file "./api/.env.local" start

#php Make migrate
migrate:
	docker-compose --env-file "./api/.env.local" exec api bin/console doctrine:migrations:migrate

#php bin/console with args
console:
	 docker-compose --env-file "./api/.env.local" exec api bin/console $(filter-out $@,$(MAKECMDGOALS))
	 
#php Make fixtures
fixtures:
	docker-compose --env-file "./api/.env.local" exec api bin/console hautelook:fixtures:load

tests:
	docker-compose --env-file "./api/.env.local" exec api bin/phpunit
test-migrate:
	docker-compose --env-file "./api/.env.local" exec api bin/console --env=test d:m:m
func-tests:
	docker-compose --env-file "./api/.env.test.local" exec api bin/phpunit --testsuite functional
test-drop-db:
	docker-compose --env-file "./api/.env.local" exec api bin/console --env=test doctrine:database:drop --force
test-create-db:
	docker-compose --env-file "./api/.env.local" exec api bin/console --env=test doctrine:database:create
jwt-keys:
	docker compose --env-file "./api/.env.local" exec api sh -c 'set -e;\
	apk add openssl;\
	php bin/console lexik:jwt:generate-keypair;\
	setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt;\
	setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt;\
 	'
