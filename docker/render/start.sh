#!/bin/sh
set -eu

export PORT="${PORT:-10000}"
case "$PORT" in
    ''|*[!0-9]*) echo 'PORT must be a number.' >&2; exit 1 ;;
esac
if [ "$PORT" -lt 1024 ] || [ "$PORT" -gt 65535 ]; then
    echo 'PORT must be between 1024 and 65535.' >&2
    exit 1
fi

: "${APP_KEY:?Set a permanent APP_KEY in Render Environment.}"
: "${APP_URL:?Set APP_URL to the HTTPS URL of this service.}"
: "${DB_URL:?Set DB_URL to the Render Postgres Internal Database URL.}"
if [ "${DB_CONNECTION:-pgsql}" != 'pgsql' ]; then
    echo 'This image requires DB_CONNECTION=pgsql.' >&2
    exit 1
fi

php artisan config:cache

# FreeプランにはPre-Deploy Commandがないため起動時に実行。
# データの初期化、Seeder、APP_KEYの生成は行わない。
case "${RUN_MIGRATIONS:-true}" in
    true) php artisan migrate --force --no-interaction ;;
    false) echo 'Skipping migrations (RUN_MIGRATIONS=false).' ;;
    *) echo 'RUN_MIGRATIONS must be true or false.' >&2; exit 1 ;;
esac

php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
