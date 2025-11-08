# ============================
# 1) BUILD de assets (Vite)
# ============================
FROM node:18 AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ============================
# 2) RUNTIME PHP (Laravel)
# ============================
FROM php:8.2-fpm

# Paquetes del sistema + extensiones PHP (incluye PGSQL)
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev libpq-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install \
    pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalar vendors SIN scripts (todavía no existe artisan)
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN php -d memory_limit=-1 /usr/bin/composer install \
    --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Copiar código y assets compilados
COPY . .
COPY --from=assets /app/public /var/www/html/public

# Permisos
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 755 storage bootstrap/cache

# No cacheamos en build (las vars llegan en runtime)

# ===== Servidor embebido + bootstrap en runtime =====
ENV PORT=8080
EXPOSE 8080

# Limpia caches, crea symlink, (opcional) crea tabla de sesiones si la usas,
# corre migraciones y luego levanta el servidor HTTP embebido.
CMD ["sh", "-lc", "\
  php artisan config:clear && \
  php artisan route:clear && \
  php artisan view:clear && \
  php artisan optimize:clear && \
  php artisan storage:link || true && \
  if [ \"${SESSION_DRIVER}\" = \"database\" ]; then php artisan session:table || true; fi && \
  php artisan migrate --force --no-interaction || true && \
  php -d variables_order=EGPCS -S 0.0.0.0:${PORT} -t public \
"]

