#!/bin/sh
set -e

# Asegurar la estructura de storage de Laravel
for dir in views cache sessions; do
    mkdir -p "/var/www/app/storage/framework/$dir"
done
mkdir -p /var/www/app/storage/logs

# Sincronizar los assets compilados en la imagen hacia el volumen compartido
# `store-build` (montado en /var/www/app/public/build). Docker solo copia el
# contenido de la imagen al volumen la primera vez; en cada redespliegue el
# entrypoint refresca el contenido para que store-nginx sirva el build nuevo.
if [ -d /var/www/app/build-cache ]; then
    mkdir -p /var/www/app/public/build
    cp -a /var/www/app/build-cache/. /var/www/app/public/build/
    chown -R www-data:www-data /var/www/app/public/build
fi

# El volumen store-storage llega montado como root: liberar permisos para el pool www-data
chown -R www-data:www-data /var/www/app/storage /var/www/app/bootstrap/cache || true

# Arrancar php-fpm (el entrypoint oficial de la imagen resuelve la config)
exec docker-php-entrypoint php-fpm