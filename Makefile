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
	
#php bin/console with args
console:
	 docker-compose --env-file "./api/.env.local" exec api bin/console $(filter-out $@,$(MAKECMDGOALS))