#!/bin/bash

# Attendre que la base de données soit prête
echo "Waiting for database..."
sleep 10

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Nettoyer le cache avant les migrations
php artisan config:clear
php artisan cache:clear

# Exécuter les migrations
php artisan migrate --force

# Optimiser l'application pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer Apache
apache2-foreground
