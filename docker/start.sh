#!/bin/bash
set -e

echo "=== MONOPTIC RAILWAY DEPLOYMENT ==="

# Configuration Railway
export PORT=${PORT:-80}
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export DB_CONNECTION=${DB_CONNECTION:-pgsql}

echo "Railway Port: $PORT"
echo "Database: ${DB_HOST}:${DB_PORT}/${DB_DATABASE}"

# Créer .env pour Laravel
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

# Générer clé d'application si nécessaire
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Configurer Apache pour le port Railway
echo "=== CONFIGURING APACHE FOR PORT $PORT ==="

# Modifier la configuration des ports Apache
echo "Listen $PORT" > /etc/apache2/ports.conf

# Substituer la variable PORT dans la configuration du VirtualHost
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf > /tmp/apache-config
cp /tmp/apache-config /etc/apache2/sites-available/000-default.conf

# Attendre la base de données
echo "=== WAITING FOR DATABASE ==="
sleep 10

# Exécuter les migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate:fresh --force || {
    echo "Fresh migration failed, trying regular migrate..."
    php artisan migrate --force
}

# Créer les endpoints de healthcheck
echo "=== CREATING HEALTHCHECK ENDPOINTS ==="
echo "OK" > public/health
mkdir -p public/api
echo '{"status":"ok","app":"monoptic","port":"'$PORT'"}' > public/api/health

# Optimiser pour la production
echo "=== OPTIMIZING APPLICATION ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== STARTING APACHE ON PORT $PORT ==="
echo "Application will be available on port $PORT"
echo "Health endpoints: /health and /api/health"

# Démarrer Apache
exec apache2-foreground
