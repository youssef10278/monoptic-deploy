# 🚀 Guide de Déploiement Monoptic sur Railway

## Prérequis

1. **Compte Railway** : [railway.app](https://railway.app)
2. **Repository Git** : Code poussé sur GitHub/GitLab
3. **Fichiers de configuration** : Tous les fichiers Docker créés

## Étapes de Déploiement

### 1. Préparation du Repository

```bash
# Ajouter tous les nouveaux fichiers
git add .
git commit -m "feat: Add Railway deployment configuration"
git push origin main
```

### 2. Création du Projet Railway

1. Connectez-vous à [Railway](https://railway.app)
2. Cliquez sur "New Project"
3. Sélectionnez "Deploy from GitHub repo"
4. Choisissez votre repository Monoptic

### 3. Configuration de la Base de Données

1. Dans votre projet Railway, cliquez sur "Add Service"
2. Sélectionnez "Database" → "PostgreSQL"
3. Railway créera automatiquement une base PostgreSQL

### 4. Configuration des Variables d'Environnement

Dans l'onglet "Variables" de votre service principal, ajoutez :

```env
APP_NAME=Monoptic
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Base de données (Railway les fournit automatiquement)
DB_CONNECTION=pgsql
PGHOST=${{Postgres.PGHOST}}
PGPORT=${{Postgres.PGPORT}}
PGDATABASE=${{Postgres.PGDATABASE}}
PGUSER=${{Postgres.PGUSER}}
PGPASSWORD=${{Postgres.PGPASSWORD}}

# Générer une nouvelle clé
APP_KEY=base64:VOTRE_CLE_GENEREE

# Sessions et cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (optionnel)
MAIL_MAILER=log
```

### 5. Génération de la Clé d'Application

```bash
# Localement, générez une clé
php artisan key:generate --show

# Copiez la clé générée dans APP_KEY sur Railway
```

### 6. Configuration du Domaine

1. Dans l'onglet "Settings" de votre service
2. Section "Domains"
3. Cliquez sur "Generate Domain" ou ajoutez un domaine personnalisé
4. Mettez à jour APP_URL avec le nouveau domaine

## Variables d'Environnement Complètes

```env
# Application
APP_NAME=Monoptic
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE_ICI
APP_DEBUG=false
APP_URL=https://votre-domaine.railway.app

# Base de données PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

# Cache et sessions
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=error

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@monoptic.com
MAIL_FROM_NAME=Monoptic
```

## Commandes Post-Déploiement

Railway exécutera automatiquement :

1. `composer install --no-dev --optimize-autoloader`
2. `npm install && npm run build`
3. `php artisan migrate --force`
4. `php artisan config:cache`
5. `php artisan route:cache`
6. `php artisan view:cache`

## Données de Démonstration

Pour ajouter des données de test :

```bash
# Connectez-vous au service Railway via CLI
railway login
railway link
railway run php artisan db:seed --class=ProductionSeeder
```

## Vérification du Déploiement

1. **Santé de l'application** : Visitez votre URL Railway
2. **Base de données** : Vérifiez les tables dans l'onglet PostgreSQL
3. **Logs** : Consultez les logs dans l'onglet "Deployments"

## Comptes de Test

Après le seeding :

- **Super Admin** : admin@monoptic.com / password123
- **Admin Magasin** : admin@optiquevision.ma / password123
- **Employé** : employe@optiquevision.ma / password123

## Dépannage

### Erreur de Migration
```bash
railway run php artisan migrate:fresh --seed
```

### Erreur de Cache
```bash
railway run php artisan cache:clear
railway run php artisan config:clear
```

### Erreur de Permissions
Vérifiez que les dossiers storage/ et bootstrap/cache/ sont accessibles en écriture.

## Monitoring

1. **Logs** : Onglet "Deployments" → "View Logs"
2. **Métriques** : Onglet "Metrics" pour CPU/RAM
3. **Base de données** : Onglet PostgreSQL pour les connexions

## Mise à Jour

Pour déployer une nouvelle version :

```bash
git add .
git commit -m "feat: nouvelle fonctionnalité"
git push origin main
```

Railway redéploiera automatiquement.

## Support

- **Documentation Railway** : [docs.railway.app](https://docs.railway.app)
- **Logs d'erreur** : Consultez les logs Railway pour diagnostiquer
- **Base de données** : Utilisez l'interface Railway pour inspecter PostgreSQL
