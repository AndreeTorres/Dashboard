# ============================
# 1️⃣ ETAPA DE BUILD (Node)
# ============================
FROM node:18 AS assets
WORKDIR /app

# Instalar dependencias de Node
COPY package*.json ./
RUN npm ci

# Copiar el resto del proyecto para compilar assets
COPY . .
RUN npm run build


# ============================
# 2️⃣ ETAPA DE RUNTIME (PHP + Composer)
# ============================
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
 && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# --- Paso 1: copiar composer.json y composer.lock e instalar dependencias sin scripts ---
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# --- Paso 2: copiar el resto del código (ahora ya existe artisan) ---
COPY . .

# --- Paso 3: copiar los assets compilados desde la etapa Node ---
COPY --from=assets /app/public /var/www/html/public

# --- Paso 4: permisos de Laravel ---
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 755 storage bootstrap/cache

# --- Paso 5: ejecutar scripts de Artisan y optimizar ---
RUN php artisan package:discover --ansi \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache || true

# ===== Servidor embebido + bootstrap en runtime =====
ENV PORT=8080
EXPOSE 8080

# Limpia caches y (opcional) corre migraciones al iniciar, luego levanta HTTP
CMD ["sh", "-lc", "\
  php artisan config:clear && \
  php artisan route:clear && \
  php artisan view:clear && \
  php artisan optimize:clear && \
  php artisan storage:link || true && \
  php -d variables_order=EGPCS -S 0.0.0.0:${PORT} -t public public/index.php \
"]