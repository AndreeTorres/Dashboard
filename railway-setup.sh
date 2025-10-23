#!/bin/bash

# Railway Setup Script para Karina's Dashboard
echo "🚂 Configurando proyecto para Railway..."

# Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node.js
npm ci --only=production

# Compilar assets
npm run build

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Optimizar aplicación para producción
php artisan optimize

# Ejecutar migraciones automáticamente
php artisan migrate --force --no-interaction

# Crear usuario admin por defecto (opcional)
# php artisan db:seed --class=UserSeeder --force

echo "✅ Setup completado para Railway"
