.PHONY: stan
stan:
	php -d memory_limit=256M vendor/bin/phpstan analyse src -c vendor/gamee/php-code-checker-rules/phpstan.neon --level 8

.PHONY: cs
cs:
	vendor/bin/phpcs --standard=vendor/gamee/php-code-checker-rules/ruleset.xml --extensions=php,phpt --tab-width=4 --ignore=temp -sp src

.PHONY: csfix
csfix:
	vendor/bin/phpcbf --standard=vendor/gamee/php-code-checker-rules/ruleset.xml --extensions=php,phpt --tab-width=4 --ignore=temp -sp src
