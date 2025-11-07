# =====================================================
# 1️⃣ ETAPA DE BUILD (Frontend con Node)
# =====================================================
FROM node:18 AS assets
WORKDIR /app

# Instalar dependencias del frontend
COPY package*.json ./
RUN npm ci

# Copiar el resto del proyecto y construir los assets (Vite, Tailwind, etc.)
COPY . .
RUN npm run build


# =====================================================
# 2️⃣ ETAPA DE RUNTIME (Backend Laravel + PHP + Composer)
# =====================================================
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias (incluye PostgreSQL)
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
    libpq-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
 && rm -rf /var/lib/apt/lists/*

# Instalar Composer (copiado desde la imagen oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos principales para instalar dependencias PHP
COPY composer.json composer.lock ./

# Configurar Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalar dependencias PHP (sin ejecutar scripts, todavía no existe artisan)
RUN php -d memory_limit=-1 /usr/bin/composer install \
    --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Copiar el resto del proyecto
COPY . .

# Copiar los assets compilados desde la etapa de Node
COPY --from=assets /app/public /var/www/html/public

# Crear carpetas necesarias y asignar permisos
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 755 storage bootstrap/cache

# =====================================================
# 🚀 INICIO DEL SERVIDOR PHP EMBEBIDO
# =====================================================

# Exponer el puerto que Render o Railway detectará
ENV PORT=8080
EXPOSE 8080

# Comando de arranque
CMD ["sh", "-lc", "\
  echo '🚀 Iniciando Karina Dashboard...' && \
  php artisan config:clear && \
  php artisan route:clear && \
  php artisan view:clear && \
  php artisan optimize:clear && \
  php artisan storage:link || true && \
  php -d variables_order=EGPCS -S 0.0.0.0:${PORT} -t public public/index.php \
"]
