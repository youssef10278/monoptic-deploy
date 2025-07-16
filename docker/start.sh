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

# Configurer Apache pour Railway
echo "=== CONFIGURING APACHE FOR RAILWAY ==="

# Créer un lien symbolique vers le bon répertoire pour Railway
ln -sf /var/www/html /workspace

# Modifier la configuration des ports Apache
echo "Listen $PORT" > /etc/apache2/ports.conf

# Substituer la variable PORT dans la configuration du VirtualHost
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf > /tmp/apache-config
cp /tmp/apache-config /etc/apache2/sites-available/000-default.conf

# Activer les modules Apache nécessaires pour Laravel
a2enmod rewrite
a2enmod headers

# Attendre la base de données
echo "=== WAITING FOR DATABASE ==="
sleep 10

# Exécuter les migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force

# Optimisations Laravel pour la production (OBLIGATOIRE)
echo "=== OPTIMIZING LARAVEL FOR PRODUCTION ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer les endpoints de healthcheck
echo "=== CREATING HEALTHCHECK ENDPOINTS ==="
echo "OK" > public/health
mkdir -p public/api
echo '{"status":"ok","app":"monoptic","port":"'$PORT'"}' > public/api/health

# Vérifier que les fichiers critiques existent
echo "=== VERIFYING LARAVEL FILES ==="
if [ ! -f "public/index.php" ]; then
    echo "ERROR: public/index.php not found!"
    exit 1
fi

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

echo "=== STARTING APACHE ON PORT $PORT ==="
echo "Application will be available on port $PORT"
echo "Health endpoints: /health and /api/health"

# Démarrer Apache
exec apache2-foreground
