#!/usr/bin/env bash
set -euo pipefail
echo '=== COMPILE / LINT ==='
find app -name '*.php' -print0 | xargs -0 -n1 php -l
echo '=== BUILD ==='
bash scripts/build.sh
echo '=== TEST ==='
if [[ -x vendor/bin/phpunit ]]; then vendor/bin/phpunit --testdox; else echo 'Install dependencies first: composer install'; exit 1; fi
echo '=== PACKAGE ==='
bash scripts/package.sh
echo 'CI pipeline completed successfully.'
