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