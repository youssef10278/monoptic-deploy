#!/bin/bash

# Attendre que la base de données soit prête
echo "Waiting for database..."
sleep 10

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Exécuter les migrations
php artisan migrate --force

# Optimiser l'application pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer Apache
apache2-foreground
