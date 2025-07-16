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
APP_URL=https://${APP_URL}

# Forcer HTTPS pour Railway
ASSET_URL=https://${APP_URL}
MIX_ASSET_URL=https://${APP_URL}
VITE_APP_URL=https://${APP_URL}

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

echo ""
echo "=== 🔍 VÉRIFICATION CONFIGURATION URLS ==="
echo "📋 APP_URL configurée: https://${APP_URL}"
echo "📋 ASSET_URL configurée: https://${APP_URL}"
echo "📋 Variables d'environnement Railway:"
echo "   - APP_URL (Railway): ${APP_URL}"
echo "   - PORT: ${PORT}"

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

# ========================================
# DIAGNOSTIC COMPLET - PHASE 1: AVANT BUILD
# ========================================
echo "=== 🔍 DIAGNOSTIC AVANT BUILD ==="

echo "📋 Node.js version:"
node --version
echo "📋 NPM version:"
npm --version

echo "📋 Workspace structure:"
ls -la /workspace/

echo "📋 Public directory:"
ls -la public/ || echo "❌ Public directory not found"

echo "📋 Package.json exists:"
ls -la package.json || echo "❌ package.json not found"

echo "📋 Node modules before install:"
ls -la node_modules/ 2>/dev/null | head -3 || echo "❌ node_modules not found"

echo "📋 Existing build directory:"
ls -la public/build/ 2>/dev/null || echo "✅ No existing build directory (normal)"

# Nettoyer et compiler les assets Vue.js avec HTTPS
echo ""
echo "=== 🏗️ BUILDING FRONTEND WITH HTTPS ==="
rm -rf public/build

echo "📦 Installing dependencies..."
npm install 2>&1 | tee /tmp/npm-install.log

echo "🔨 Building assets..."
npm run build 2>&1 | tee /tmp/npm-build.log

# ========================================
# DIAGNOSTIC COMPLET - PHASE 2: APRÈS BUILD
# ========================================
echo ""
echo "=== 🔍 DIAGNOSTIC APRÈS BUILD ==="

echo "📋 Build logs summary:"
echo "--- NPM Install Log (last 10 lines) ---"
tail -10 /tmp/npm-install.log || echo "❌ No install log found"
echo "--- NPM Build Log (last 15 lines) ---"
tail -15 /tmp/npm-build.log || echo "❌ No build log found"

echo ""
echo "📋 Build directory structure:"
if [ -d "public/build" ]; then
    echo "✅ Build directory exists"
    ls -la public/build/

    echo ""
    echo "📋 Assets directory:"
    if [ -d "public/build/assets" ]; then
        echo "✅ Assets directory exists"
        ls -la public/build/assets/

        echo ""
        echo "📋 Asset files count:"
        find public/build/assets/ -type f | wc -l

        echo ""
        echo "📋 JS files:"
        find public/build/assets/ -name "*.js" -exec ls -la {} \;

        echo ""
        echo "📋 CSS files:"
        find public/build/assets/ -name "*.css" -exec ls -la {} \;

        echo ""
        echo "📋 File permissions:"
        ls -la public/build/assets/ | head -5

        echo ""
        echo "📋 Testing file readability:"
        for file in public/build/assets/*.js; do
            if [ -f "$file" ]; then
                echo "Testing: $file"
                head -1 "$file" 2>/dev/null && echo "✅ Readable" || echo "❌ Not readable"
                break
            fi
        done

    else
        echo "❌ Assets directory missing!"
        echo "Build directory contents:"
        ls -la public/build/
    fi
else
    echo "❌ Build directory missing!"
    echo "Public directory contents:"
    ls -la public/
    echo "Checking for build errors..."
    grep -i error /tmp/npm-build.log || echo "No obvious errors in build log"
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
