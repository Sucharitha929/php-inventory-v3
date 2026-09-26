#!/usr/bin/env bash
set -e
php /var/www/html/scripts/init-db.php
exec "$@"
