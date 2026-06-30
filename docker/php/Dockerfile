FROM php:8.4-fpm-alpine

# 1. Instalar dependencias del sistema y extensiones de PHP en un solo paso limpio
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# 2. Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configurar directorio de trabajo y asegurar permisos iniciales
WORKDIR /var/www/app
RUN chown -R www-data:www-data /var/www/app

# 4. Copiar configuraciones y scripts de entrada
COPY php.ini $PHP_INI_DIR/conf.d/custom.ini
COPY docker-entrypoint.sh /usr/local/bin/custom-entrypoint
RUN chmod +x /usr/local/bin/custom-entrypoint

# 5. Configurar el punto de entrada y cambiar al usuario no-root por defecto
ENTRYPOINT ["custom-entrypoint"]
USER www-data

CMD ["php-fpm"]