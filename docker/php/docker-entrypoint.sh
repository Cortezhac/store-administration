#!/bin/sh
set -e

# Asegurar de forma dinámica que existan los subdirectorios de Laravel
for dir in views cache sessions; do
    mkdir -p "/var/www/app/storage/framework/$dir"
done
mkdir -p /var/www/app/storage/logs

# Comprobar si el comando que viene es php-fpm para ejecutar tareas iniciales de Laravel si es necesario
if [ "$1" = 'php-fpm' ]; then
    # Por ejemplo, si deseas limpiar caché vieja al encender el contenedor web:
    # php artisan cache:clear
    echo "Estructura de almacenamiento validada con éxito."
fi

# Si no se pasó ningún comando (un fallback seguro)
if [ $# -eq 0 ]; then
    set -- php-fpm
fi

# Pasar el control al entrypoint oficial de la imagen de PHP
exec docker-php-entrypoint "$@"