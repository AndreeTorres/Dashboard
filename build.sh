#!/bin/bash

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --only=production

# Build assets
npm run build

# Create storage directories and set permissions
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Create an optimized class loader
php artisan optimize

# Clear and cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completed successfully!"
