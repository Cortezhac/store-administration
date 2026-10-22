# Imagen base
#
# Versión FIJADA a la patch que corrige CVE-2026-109618 y CVE-2026-109619
# ("PHP2Shell", CVSS 9.2, RCE sin autenticación). El tag flotante
# `php:8.4-fpm-alpine` se mueve solo, así que no deja evidencia de qué
# versión corría en cada build; el tag con patch explícito sí.
#
#   PHP 8.4 → 8.4.27   (fijo en esta imagen)
#   PHP 8.3 → 8.3.36
#   PHP 8.2 → 8.2.35
#   PHP 8.5 → 8.5.12
#
# Al subir la versión de PHP hay que actualizar también el piso de versión
# en tests/Feature/Security/PhpRuntimeSecurityTest.php y en el gate de
# deploy (deploy.yml).
FROM php:8.4.27-fpm-alpine

# Instalamos las dependencias
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    zip \
    unzip \
    postgresql-dev \
    nodejs \
    npm

# Instalamos pnpm
RUN npm install -g pnpm

# Instalamos las extensiones
RUN docker-php-ext-install pdo pdo_pgsql zip bcmath gd

# Copiamos el composer de la imagen oficial al contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de PHP (exhide versión, desactiva session.upload_progress,
# topes de la request). Reemplaza por completo el php.ini de la imagen.
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Establecemos el directorio de trabajo
WORKDIR /var/www/app

# Exponemos los puertos necesarios 8000 para el servidor y 5173 para Vite
EXPOSE 8000 5173

# Comando de arranque
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]