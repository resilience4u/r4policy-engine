# ------------------------------------------------------------------------------
# R4Policy Engine - Makefile de desenvolvimento
# ------------------------------------------------------------------------------
APP = r4policy

build:
	docker compose build

up:
	docker compose up -d

shell:
	docker compose exec $(APP) bash

validate:
	docker compose exec $(APP) ./bin/r4policy validate examples/resilience.yaml

dump:
	docker compose exec $(APP) ./bin/r4policy dump examples/resilience.yaml

diff:
	docker compose exec $(APP) ./bin/r4policy diff examples/resilience.yaml examples/resilience.yaml

test:
	docker compose exec $(APP) ./vendor/bin/phpunit --colors=always

logs:
	docker compose logs -f $(APP)

stop:
	docker compose down
