# See ——————————————————————————————————————————————————————————————————————————
# http://fabien.potencier.org/symfony4-best-practices.html
# https://speakerdeck.com/mykiwi/outils-pour-ameliorer-la-vie-des-developpeurs-symfony?slide=47
# https://blog.theodo.fr/2018/05/why-you-need-a-makefile-on-your-project/
# https://www.strangebuzz.com/en/snippets/the-perfect-makefile-for-symfony

# Setup ————————————————————————————————————————————————————————————————————————
# Import environment variables
-include .env.local

# Make internals
SHELL := /bin/bash
.DEFAULT_GOAL := help

# Used programs (depending on the environment)
PHP ?= php
GIT ?= git
DOCKER_COMPOSE ?= docker-compose
COMPOSER ?= composer
SYMFONY ?= symfony
YARN ?= yarn
PHIVE ?= phive

# Used programs (not depending on the environment)
CONSOLE := $(SYMFONY) console

# For better readability dismiss all output
DISMISS_STDOUT := >/dev/null
# Keep the errors but provide a way to easily dismiss them (comment in the "2>&1")
DISMISS_STDERR := #2>&1
# Provide a possibility to force dismiss STDERR if they output infos via STDERR which we would expect on STDOUT
FORCE_DISMISS_STDERR := 2>&1

## —— Help ————————————————————————————————————
help: ## Show help
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

help-phive: ## Help for installing a PHAR used as a tool for development
	@printf "Example: copy PHP CS Fixer into the project's directory tools:\n\n\tsudo %s install --copy php-cs-fixer\n\n" "$(PHIVE)"
.PHONY: help-phive

## —— Build ———————————————————————————————————
build-dev: composer-dev ## Initially build the package for local development
.PHONY: build-dev

## —— Dependencies ————————————————————————————
composer-dev: ## Install PHP dependencies according to composer.lock for development
	$(COMPOSER) install ${DISMISS_STDOUT} ${FORCE_DISMISS_STDERR}
.PHONY: composer-dev

composer-validate: ## Check if composer.json is valid
	$(COMPOSER) validate --strict
.PHONY: composer-validate

security-check: ## Check whether the project's dependencies contain any known security vulnerability
	$(SYMFONY) check:security
.PHONY: security-check

## —— Tests ————————————————————
tests: ## Run tests
	$(SYMFONY) run vendor/bin/phpunit
.PHONY: tests

code-coverage: ## Create HTML code coverage report in ./var/coverage/ (also shows the text coverage report in STDOUT).
	$(SYMFONY) run vendor/bin/phpunit --coverage-html var/coverage/ --coverage-text
	google-chrome "file://$(shell pwd)/var/coverage/index.html"
.PHONY: code-coverage

## —— Static code analysis ————————————————————
cs-fixer-dry-run-stop: ## Lint PHP with CS Fixer (does not edit files, on errors exit with exit code; can be used during CI to refuse pull requests which do not adapt to the used code styles)
	$(PHP) ./tools/php-cs-fixer fix --diff -vvv --dry-run --stop-on-violation --using-cache=no ${DISMISS_STDOUT} ${FORCE_DISMISS_STDERR}
.PHONY: cs-fixer-dry-run-stop

cs-fixer-dry-run: ## Lint PHP with CS Fixer (does not edit files)
	$(PHP) ./tools/php-cs-fixer fix --diff -vvv --dry-run --using-cache=no
.PHONY: cs-fixer-dry-run

cs-fixer-fix: ## Lint PHP with CS Fixer and correct files with errors
	$(PHP) ./tools/php-cs-fixer fix --diff -vvv
.PHONY: cs-fixer-fix

psalm-dry-run: ## Lint PHP with Psalm (do not edit files, on errors exit with exit code; can be used during CI to refuse pull requests which do not adapt to the used code styles)
	$(PHP) ./tools/psalm --show-info=true
.PHONY: psalm-dry-run

psalm: ## Lint PHP with Psalm and correct files with errors
	$(PHP) ./tools/psalm --alter --issues=all
.PHONY: psalm
