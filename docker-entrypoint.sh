#!/bin/sh
set -e

php artisan migrate --force --seed
exec "$@"
