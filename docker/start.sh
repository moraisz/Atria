#!/usr/bin/env bash
set -e

if [ "$APP_ENV" = "development" ]; then
    echo "Running composer install..."
    composer install

    echo "Running NPM install..."
    npm install

    echo "Running NPM dev in background..."
    npm run dev -- --host 0.0.0.0 &
fi

if [[ "$APP_ENV" = "production" ]]; then
    echo "Running composer install on production..."
    composer install --no-dev --optimize-autoloader

    echo "Building frontend..."
    npm run build
fi

echo "Starting FrankenPHP..."
exec frankenphp run --config /etc/caddy/Caddyfile
