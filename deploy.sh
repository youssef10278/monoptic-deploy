#!/bin/bash

echo "🚀 Préparation du déploiement Monoptic sur Railway..."

# Vérifier si nous sommes dans un repository git
if [ ! -d ".git" ]; then
    echo "❌ Erreur: Ce n'est pas un repository Git"
    echo "Initialisez d'abord un repository avec: git init"
    exit 1
fi

# Vérifier si les fichiers Docker existent
if [ ! -f "Dockerfile" ]; then
    echo "❌ Erreur: Dockerfile manquant"
    exit 1
fi

if [ ! -f "railway.json" ]; then
    echo "❌ Erreur: railway.json manquant"
    exit 1
fi

echo "✅ Vérification des fichiers de configuration..."

# Générer une clé d'application si elle n'existe pas
if [ ! -f ".env" ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
    php artisan key:generate
fi

echo "🔑 Clé d'application générée:"
php artisan key:generate --show

echo ""
echo "📋 Variables d'environnement à configurer sur Railway:"
echo "=================================================="
echo "APP_NAME=Monoptic"
echo "APP_ENV=production"
echo "APP_DEBUG=false"
echo "APP_KEY=$(php artisan key:generate --show)"
echo "APP_URL=https://votre-domaine.railway.app"
echo ""
echo "DB_CONNECTION=pgsql"
echo "DB_HOST=\${{Postgres.PGHOST}}"
echo "DB_PORT=\${{Postgres.PGPORT}}"
echo "DB_DATABASE=\${{Postgres.PGDATABASE}}"
echo "DB_USERNAME=\${{Postgres.PGUSER}}"
echo "DB_PASSWORD=\${{Postgres.PGPASSWORD}}"
echo ""
echo "SESSION_DRIVER=database"
echo "CACHE_STORE=database"
echo "QUEUE_CONNECTION=database"
echo "LOG_CHANNEL=stack"
echo "LOG_LEVEL=error"
echo "=================================================="
echo ""

# Vérifier si des changements doivent être committés
if [ -n "$(git status --porcelain)" ]; then
    echo "📦 Ajout des fichiers au repository..."
    git add .
    
    echo "💬 Message de commit:"
    read -p "Entrez un message de commit (ou appuyez sur Entrée pour le message par défaut): " commit_message
    
    if [ -z "$commit_message" ]; then
        commit_message="feat: Add Railway deployment configuration"
    fi
    
    git commit -m "$commit_message"
    
    echo "🔄 Push vers le repository distant..."
    git push origin main
    
    if [ $? -eq 0 ]; then
        echo "✅ Code poussé avec succès!"
    else
        echo "❌ Erreur lors du push. Vérifiez votre repository distant."
        exit 1
    fi
else
    echo "✅ Repository à jour, aucun changement à committer."
fi

echo ""
echo "🎉 Préparation terminée!"
echo ""
echo "📋 Prochaines étapes:"
echo "1. Allez sur https://railway.app"
echo "2. Créez un nouveau projet"
echo "3. Connectez votre repository GitHub/GitLab"
echo "4. Ajoutez un service PostgreSQL"
echo "5. Configurez les variables d'environnement (voir ci-dessus)"
echo "6. Déployez!"
echo ""
echo "📖 Consultez DEPLOYMENT_GUIDE.md pour plus de détails."
