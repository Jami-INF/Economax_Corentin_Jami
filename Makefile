OS := $(shell uname)

start_dev:
ifeq ($(OS),Darwin)
	docker volume create --name=lpa-sf6-files
	docker-compose -f docker-compose.yml up -d
	docker-sync start
else
	docker-compose up -d
endif

stop_dev:           ## Stop the Docker containers
ifeq ($(OS),Darwin)
	docker-compose stop
	docker-sync stop
else
	docker-compose stop
endif

goto_php_container:
	docker exec -it -u root lpa_sf6_php bash
