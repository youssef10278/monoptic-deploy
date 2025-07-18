#!/bin/bash

echo "🧹 Clearing Laravel caches for domain change..."

# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled services
php artisan clear-compiled

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ All caches cleared and optimized!"
