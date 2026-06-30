FROM node:22-alpine AS assets-builder

WORKDIR /app

# Habilitar corepack para poder usar pnpm de forma nativa
RUN corepack enable && corepack prepare pnpm@latest --activate

# Copiar archivos de dependencias de pnpm para aprovechar la caché de capas
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install --frozen-lockfile

# Copiar el resto del código fuente para que Vite pueda compilar
COPY . .
RUN pnpm run build

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

# 3. Configurar el directorio de trabajo como root para poder cambiar permisos
WORKDIR /var/www/app

# 4. Crear la carpeta base y asegurar que le pertenezca a www-data antes de montar nada
RUN chown -R www-data:www-data /var/www/app

COPY php.ini $PHP_INI_DIR/conf.d/custom.ini
COPY docker-entrypoint.sh /usr/local/bin/custom-entrypoint
RUN chmod +x /usr/local/bin/custom-entrypoint

ENTRYPOINT ["custom-entrypoint"]

# 5. Cambiar de forma definitiva al usuario de PHP
USER www-data

CMD ["php-fpm"]