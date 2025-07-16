#!/bin/bash

echo "=== MONOPTIC SIMPLE START ==="

# Variables par défaut
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export DB_CONNECTION=${DB_CONNECTION:-pgsql}

# Créer .env simple
cat > .env << EOF
APP_NAME=Monoptic
APP_ENV=${APP_ENV}
APP_DEBUG=${APP_DEBUG}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

DB_CONNECTION=${DB_CONNECTION}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
EOF

echo "Database: ${DB_HOST}:${DB_PORT}/${DB_DATABASE}"

# Générer clé si nécessaire
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Attendre la DB
sleep 10

# Migrations simples
echo "=== MIGRATIONS ==="
php artisan migrate:fresh --force || {
    echo "Migration failed, trying basic migrate..."
    php artisan migrate --force
}

# Créer healthcheck
echo "OK" > public/health

echo "=== STARTING SERVER ==="
echo "Waiting 5 seconds for Apache to fully start..."

# Démarrer Apache en arrière-plan
apache2ctl start

# Attendre qu'Apache soit prêt
sleep 5

# Vérifier qu'Apache fonctionne
curl -f http://localhost/health || echo "Health check failed"

# Garder le container en vie
apache2-foreground
