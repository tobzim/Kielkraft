# Marvento - developer shortcuts (run via Git Bash; `make` optional, see README)
.DEFAULT_GOAL := help
.PHONY: help up down build rebuild shell logs ps lint seed-user

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

up: ## Start the dev stack (shop :8080, mail :8025)
	docker compose up -d --build

down: ## Stop the dev stack
	docker compose down

build: ## Build images
	docker compose build

rebuild: ## Rebuild without cache
	docker compose build --no-cache

shell: ## Open a shell in the app container
	docker compose exec app sh

logs: ## Follow logs
	docker compose logs -f

ps: ## Show running services
	docker compose ps

lint: ## PHP lint all source files
	docker compose exec app sh -c 'find site public -name "*.php" -print0 | xargs -0 -n1 -P4 php -l'

seed-user: ## Create the first Panel admin (interactive)
	docker compose exec app php kirby make:user
