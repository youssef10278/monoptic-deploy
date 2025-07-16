#!/bin/bash
set -e

echo "=== MONOPTIC MINIMAL START ==="

# Configuration Railway
export PORT=${PORT:-80}
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

echo "Port: $PORT"
echo "Environment: $APP_ENV"

# Créer .env minimal
cat > .env << EOF
APP_NAME=Monoptic
APP_ENV=${APP_ENV}
APP_DEBUG=${APP_DEBUG}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

DB_CONNECTION=pgsql
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

# Générer clé si nécessaire
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Configuration Apache
echo "ServerName localhost" >> /etc/apache2/apache2.conf
echo "Listen $PORT" > /etc/apache2/ports.conf

# Lien symbolique pour Railway
ln -sf /var/www/html /workspace

# Substituer PORT dans la config
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf > /tmp/apache-config
cp /tmp/apache-config /etc/apache2/sites-available/000-default.conf

# Migrations
echo "=== MIGRATIONS ==="
php artisan migrate --force

# Healthcheck
echo "OK" > public/health

echo "=== STARTING APACHE ==="
exec apache2-foreground
