#!/bin/bash
set -e

echo "=== MONOPTIC MINIMAL START ==="

# Configuration Railway
export PORT=${PORT:-80}
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

echo "Port: $PORT"
echo "Environment: $APP_ENV"

# Créer .env minimal avec HTTPS forcé
cat > .env << EOF
APP_NAME=Monoptic
APP_ENV=${APP_ENV}
APP_DEBUG=${APP_DEBUG}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL}

# Forcer HTTPS pour Railway
ASSET_URL=${APP_URL}
MIX_ASSET_URL=${APP_URL}
VITE_APP_URL=${APP_URL}

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

# Nettoyer et compiler les assets Vue.js avec HTTPS
echo "=== BUILDING FRONTEND WITH HTTPS ==="
rm -rf public/build
npm run build

# Vérifier que les assets sont générés
echo "=== VERIFYING ASSETS ==="
if [ -d "public/build" ]; then
    echo "✅ Build directory exists"
    ls -la public/build/assets/ | head -5

    echo "✅ Assets will be served via Laravel routes"

else
    echo "❌ Build directory missing!"
    exit 1
fi

# Migrations
echo "=== MIGRATIONS ==="
php artisan migrate --force

# Vérifier que Laravel fonctionne
echo "=== TESTING LARAVEL ==="
php artisan --version || echo "Laravel command failed"

# Nettoyer et reconstruire les caches
echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Reconstruire les caches
echo "=== REBUILDING CACHES ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer .htaccess pour Laravel si manquant
if [ ! -f "public/.htaccess" ]; then
    echo "Creating .htaccess for Laravel..."
    cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
fi

# Healthcheck
echo "OK" > public/health

# Vérifier que les routes existent
echo "=== VERIFYING ROUTES ==="
php artisan route:list | head -3 || echo "Routes verification failed"

echo "=== STARTING APACHE ==="
exec apache2-foreground
