#!/usr/bin/env bash

php bin/console doctrine:migrations:migrate --env=test --no-interaction --quiet
php bin/console doctrine:fixtures:load --purge-with-truncate --env=test --no-interaction --quiet
php bin/phpunit
