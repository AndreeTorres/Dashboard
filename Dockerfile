# =========================================================
# 🧩 ETAPA 1: Construcción de assets con Node
# =========================================================
FROM node:18 AS build

# Directorio de trabajo para el build
WORKDIR /app

# Copiar los archivos necesarios
COPY package*.json ./
RUN npm ci

COPY . .

# Construir los assets del frontend (Vite, Tailwind, etc.)
RUN npm run build


# =========================================================
# 🐘 ETAPA 2: Entorno de ejecución PHP (Laravel)
# =========================================================
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto (sin node_modules)
COPY . .

# Copiar los assets compilados desde la etapa anterior
COPY --from=build /app/public /var/www/html/public

# Variables de entorno para Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_MEMORY_LIMIT=512M

# Instalar dependencias PHP (modo producción)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Crear directorios y asignar permisos
RUN mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Optimizar Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exponer el puerto del servicio (Render o Railway usará la variable PORT)
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
