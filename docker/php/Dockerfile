# Imagen base 
FROM php:8.4-fpm-alpine

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

# Establecemos el directorio de trabajo
WORKDIR /var/www/app

# Exponemos los puertos necesarios 8000 para el servidor y 5173 para Vite
EXPOSE 8000 5173

# Comando de arranque
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]