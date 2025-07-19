#!/bin/bash

echo "🚂 Configuring for Railway deployment..."

# Clear all caches
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

echo "✅ Railway configuration complete!"
echo "🌐 App should be available at: https://monopti-deploy-production.up.railway.app"
