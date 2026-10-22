#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Despliegue de Cuentas Claras en producción.
#
# Uso:  ./deploy.sh
# Requisitos: estar en el servidor, con .env de producción ya en su lugar.
# =============================================================================
set -euo pipefail

cd "$(dirname "$0")"

echo "▶ 1/8 · Modo mantenimiento"
php artisan down --render="errors.503" --retry=60 || true

echo "▶ 2/8 · Audit de dependencias"
composer audit --no-dev --locked --format=plain
# Si falla, el script se detiene aquí (set -e) y no se despliega nada.

echo "▶ 3/8 · Instalando dependencias"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "▶ 4/8 · Migrando base de datos"
php artisan migrate --force

echo "▶ 5/8 · Re-cacheando"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "▶ 6/8 · Permisos"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "▶ 7/8 · Reiniciando workers"
php artisan queue:restart || true

echo "▶ 8/8 · Saliendo de mantenimiento"
php artisan up

echo "✓ Deploy completado"