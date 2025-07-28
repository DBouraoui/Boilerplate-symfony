.PHONY: dev, prod, down, check, consume, test

dev:
	docker compose -f compose.yml --env-file .env.local up -d --wait

prod:
	docker compose -f compose.yml -f compose.override.yml --env-file .env.local up -d --wait

down:
	docker compose down -v

check:
	vendor/bin/phpstan analyse

consume:
	php bin/console messenger:consume async -vv

test:
	php bin/phpunit --testdox
