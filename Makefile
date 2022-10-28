.PHONY: help
help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#################
### COMMANDS ####
#################

.PHONY: analyze
analyze: ## Runs static analysis tools
		 docker build -t strictlyphp/php-value-object . && docker run --user=1000:1000 --rm --name strictlyphp-php-value-object -v "${PWD}":/usr/src/myapp -w /usr/src/myapp strictlyphp/php-value-object php ./vendor/bin/phpstan analyse -l 6 -c phpstan.neon src

.PHONY: check-coverage
check-coverage: ## Check the test coverage of changed files
		git fetch origin && git diff origin/master > ${PWD}/diff.txt && docker build -t strictlyphp/php-value-object . && docker run --user=1000:1000 --rm --name strictlyphp-php-value-object -v "${PWD}":/usr/src/myapp -w /usr/src/myapp strictlyphp/php-value-object ./build/check-coverage.sh

.PHONY: install
install: ## Install dependencies
		 docker build -t strictlyphp/php-value-object . && docker run --user=1000:1000 --rm --name strictlyphp-php-value-object -v "${PWD}":/usr/src/myapp -w /usr/src/myapp strictlyphp/php-value-object composer install

.PHONY: style
style: ## Check coding style
		 docker build -t strictlyphp/php-value-object . && docker run --user=1000:1000 --rm --name strictlyphp-php-value-object -v "${PWD}":/usr/src/myapp -w /usr/src/myapp strictlyphp/php-value-object php ./vendor/bin/ecs