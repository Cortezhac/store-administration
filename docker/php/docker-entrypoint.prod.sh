#!/bin/sh
set -e

# Asegurar la estructura de storage de Laravel
for dir in views cache sessions; do
    mkdir -p "/var/www/app/storage/framework/$dir"
done
mkdir -p /var/www/app/storage/logs

# El volumen store-storage llega montado como root: liberar permisos para el pool www-data
chown -R www-data:www-data /var/www/app/storage /var/www/app/bootstrap/cache || true

# Arrancar php-fpm (el entrypoint oficial de la imagen resuelve la config)
exec docker-php-entrypoint php-fpm