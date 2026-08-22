#!/bin/sh

set -e

echo "========================================"
echo " Laravel application setup"
echo "========================================"

echo ""
echo "[1/4] Discovering Laravel packages..."
php artisan package:discover --ansi

echo ""
echo "[2/4] Running database migrations..."
php artisan migrate --force

echo ""
echo "[3/4] Running database seeders..."
php artisan db:seed --force

echo ""
echo "[4/4] Optimizing Laravel..."
php artisan optimize

echo ""
echo "========================================"
echo " Laravel setup completed successfully"
echo "========================================"
