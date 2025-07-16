# 🔍 Monoptic - Système de Gestion pour Opticiens

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Vue.js-3-green?style=for-the-badge&logo=vue.js" alt="Vue.js 3">
  <img src="https://img.shields.io/badge/Railway-Deploy-purple?style=for-the-badge&logo=railway" alt="Railway">
  <img src="https://img.shields.io/badge/PostgreSQL-Database-blue?style=for-the-badge&logo=postgresql" alt="PostgreSQL">
</p>

## 📋 À Propos

**Monoptic** est une application web complète de gestion pour opticiens, développée avec Laravel 12 et Vue.js 3. Elle offre une solution multi-tenant permettant à plusieurs magasins d'optique de gérer leurs activités de manière indépendante.

### ✨ Fonctionnalités Principales

- **🏪 Multi-tenant** : Gestion de plusieurs magasins indépendants
- **👥 Gestion des rôles** : Super Admin, Admin Magasin, Employé
- **👤 Gestion des clients** : CRUD complet avec historique des achats
- **📦 Gestion du stock** : Produits, catégories, marques de lentilles
- **💰 Point de Vente (POS)** : Interface intuitive de vente
- **🖨️ Système d'impression** : Tickets et devis professionnels
- **💳 Gestion des paiements** : Suivi des paiements et crédits
- **📊 Rapports et analytics** : Tableaux de bord avec graphiques
- **🔐 Authentification sécurisée** : Laravel Sanctum

## 🚀 Déploiement Rapide sur Railway

### Option 1: Script Automatique (Recommandé)

```bash
# Windows
./deploy.bat

# Linux/Mac
chmod +x deploy.sh
./deploy.sh
```

### Option 2: Déploiement Manuel

1. **Préparer le repository**
```bash
git add .
git commit -m "feat: Add Railway deployment configuration"
git push origin main
```

2. **Créer le projet Railway**
   - Aller sur [railway.app](https://railway.app)
   - Nouveau projet → Deploy from GitHub
   - Sélectionner votre repository

3. **Ajouter PostgreSQL**
   - Add Service → Database → PostgreSQL

4. **Configurer les variables d'environnement**
```env
APP_NAME=Monoptic
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_URL=https://votre-domaine.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

📖 **Guide complet** : Consultez [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

## 🛠️ Développement Local

### Prérequis
- PHP 8.2+
- Composer
- Node.js 18+
- SQLite (développement)

### Installation
```bash
# Cloner le repository
git clone https://github.com/votre-username/monoptic.git
cd monoptic

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données
touch database/database.sqlite
php artisan migrate --seed

# Build des assets
npm run dev

# Démarrer le serveur
php artisan serve
```

### Comptes de Test
- **Super Admin** : admin@monoptic.com / password123
- **Admin Magasin** : admin@optiquevision.ma / password123
- **Employé** : employe@optiquevision.ma / password123

## 🏗️ Architecture Technique

### Backend (Laravel 12)
- **API REST** avec contrôleurs séparés par domaine
- **Multi-tenant** avec isolation des données
- **Authentification** Laravel Sanctum
- **Base de données** SQLite (dev) / PostgreSQL (prod)

### Frontend (Vue.js 3)
- **Composition API** avec TypeScript
- **Vue Router** avec guards d'authentification
- **Tailwind CSS** pour le styling
- **Chart.js** pour les graphiques
- **Vite.js** pour le build

## 📚 Documentation

- [Guide de Déploiement](DEPLOYMENT_GUIDE.md)
- [Checklist Post-Déploiement](POST_DEPLOYMENT_CHECKLIST.md)
- [Guide d'Impression POS](docs/IMPRESSION_POS.md)
- [Configuration Opticien](docs/GUIDE_PERSONNALISATION_ENTETE.md)

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez :
1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
