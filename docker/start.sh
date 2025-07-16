#!/bin/bash
set -e

echo "=== MONOPTIC DEPLOYMENT START ==="

# Créer le fichier .env avec les variables d'environnement
echo "Creating .env file from environment variables..."
cat > .env << EOF
APP_NAME=${APP_NAME:-Monoptic}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-${PGHOST:-postgres.railway.internal}}
DB_PORT=${DB_PORT:-${PGPORT:-5432}}
DB_DATABASE=${DB_DATABASE:-${PGDATABASE:-railway}}
DB_USERNAME=${DB_USERNAME:-${PGUSER:-postgres}}
DB_PASSWORD=${DB_PASSWORD:-${PGPASSWORD}}

SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-error}

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@monoptic.com}
MAIL_FROM_NAME=${MAIL_FROM_NAME:-Monoptic}
EOF

# Vérifier les variables critiques
echo "=== ENVIRONMENT CHECK ==="
echo "APP_ENV: $APP_ENV"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: '$DB_HOST'"
echo "DB_PORT: '$DB_PORT'"
echo "DB_DATABASE: '$DB_DATABASE'"
echo "DB_USERNAME: '$DB_USERNAME'"
echo "DB_PASSWORD: '$(echo $DB_PASSWORD | cut -c1-3)***'"
echo "APP_URL: '$APP_URL'"

# Vérifier si les variables essentielles sont définies
if [ -z "$DB_HOST" ]; then
    echo "❌ CRITICAL: DB_HOST is not set!"
    echo "Available environment variables containing 'PG':"
    env | grep -i pg || echo "No PG variables found"
    echo "Available environment variables containing 'DB':"
    env | grep -i db || echo "No DB variables found"
fi

# Tester la connexion à la base de données
echo "=== DATABASE CONNECTION TEST ==="
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "Testing connection to $DB_HOST:$DB_PORT..."
    nc -z "$DB_HOST" "$DB_PORT" && echo "✅ Database port is reachable" || echo "❌ Cannot reach database port"
else
    echo "❌ DB_HOST or DB_PORT is empty"
fi

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
    # Relire la clé générée
    APP_KEY=$(grep APP_KEY .env | cut -d '=' -f2)
    echo "Generated APP_KEY: $APP_KEY"
fi

# Attendre que la base de données soit prête
echo "Waiting for database connection..."
sleep 15

# Nettoyer seulement la config avant les migrations
echo "=== CLEARING CONFIG CACHE ==="
php artisan config:clear

# Exécuter les migrations avec fresh pour éviter les conflits
echo "=== RUNNING FRESH MIGRATIONS ==="
php artisan migrate:fresh --force

# Nettoyer le cache après que les tables existent
echo "=== CLEARING APPLICATION CACHE ==="
php artisan cache:clear

# Ajouter des données de test
echo "=== SEEDING DATABASE ==="
php artisan db:seed --class=ProductionSeeder --force

# Optimiser l'application pour la production
echo "=== OPTIMIZING FOR PRODUCTION ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier que l'application fonctionne
echo "=== TESTING APPLICATION ==="
php artisan route:list | head -5

echo "=== STARTING APACHE ==="
# Démarrer Apache
apache2-foreground
