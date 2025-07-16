#!/bin/bash

# Attendre que la base de données soit prête
echo "Waiting for database..."
sleep 10

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Vérifier les variables d'environnement importantes
echo "Checking environment variables..."
echo "APP_ENV: $APP_ENV"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "APP_URL: $APP_URL"

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Nettoyer le cache avant les migrations
echo "Clearing cache..."
php artisan config:clear
php artisan cache:clear

# Exécuter les migrations
echo "Running migrations..."
php artisan migrate --force

# Optimiser l'application pour la production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier que l'application fonctionne
echo "Testing application..."
php artisan route:list | head -5

echo "Starting Apache..."
# Démarrer Apache
apache2-foreground
