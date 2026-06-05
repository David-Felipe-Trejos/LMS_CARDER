#!/usr/bin/env bash
set -e

echo "==> Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Instalando dependencias Node y compilando assets..."
npm ci
npm run build

echo "==> Cacheando configuracion de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build completado."