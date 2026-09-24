	.PHONY: install deploy

	deploy:
		ssh -A digest 'cd sites/fauneetfloremairine.site && git pull origin main && composer install'

	install: vendor/autoload.php
		php bin/console d:m:m -n
		composer dump-env prod
		php bin/console cache:clear

	vendor/autoload.php: composer.json composer.lock
		composer install --no-dev --optimize-autoloader
		touch vendor/autoload.php