#!/bin/bash

echo "🧹 RESET COMPLET - NETTOYAGE TOTAL"

# 1. Supprimer tous les builds et caches
echo "📦 Suppression des builds et caches..."
rm -rf public/build
rm -rf node_modules/.vite
rm -rf storage/framework/cache
rm -rf storage/framework/sessions
rm -rf storage/framework/views
rm -rf bootstrap/cache/*.php

# 2. Nettoyer les caches Laravel
echo "🔄 Nettoyage des caches Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled

# 3. Réinstaller les dépendances Node
echo "📦 Réinstallation des dépendances Node..."
rm -rf node_modules
npm install

# 4. Rebuild complet des assets
echo "🏗️ Rebuild complet des assets..."
npm run build

# 5. Vérifier les URLs générées
echo "🔍 Vérification des URLs dans le manifest..."
if [ -f "public/build/manifest.json" ]; then
    echo "📄 Contenu du manifest:"
    cat public/build/manifest.json | head -20
    
    echo "🔍 Recherche de doubles protocoles:"
    grep -n "https://" public/build/manifest.json || echo "Aucun https:// trouvé"
    grep -n "https//" public/build/manifest.json || echo "Aucun https// trouvé"
fi

echo "✅ Reset complet terminé!"
