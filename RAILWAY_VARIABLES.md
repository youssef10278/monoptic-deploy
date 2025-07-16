# Variables d'Environnement Railway - Monoptic

## 🚨 Variables OBLIGATOIRES à configurer dans Railway

### **Variables d'Application**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.up.railway.app
APP_KEY=base64:VOTRE_CLE_GENEREE
```

### **Variables de Base de Données**
```env
DB_CONNECTION=pgsql
DB_HOST=postgres.railway.internal
DB_PORT=5432
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=VOTRE_MOT_DE_PASSE_POSTGRES
```

### **Variables de Cache et Session**
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
```

## 🔧 Comment Configurer

### **1. Générer APP_KEY**
```bash
php artisan key:generate --show
```
Copiez la clé générée dans Railway.

### **2. Variables PostgreSQL**
Dans Railway → Service PostgreSQL → Variables :
- Copiez `PGPASSWORD` → Utilisez pour `DB_PASSWORD`
- `DB_HOST` = `postgres.railway.internal`
- `DB_DATABASE` = `railway`
- `DB_USERNAME` = `postgres`

### **3. APP_URL**
Dans Railway → Service Principal → Settings → Domains :
- Copiez l'URL générée (ex: `https://monoptic-prod.up.railway.app`)

## ✅ Vérification

Après configuration, vos logs devraient montrer :
```
Railway Port: 8080
Database: postgres.railway.internal:5432/railway
=== OPTIMIZING LARAVEL FOR PRODUCTION ===
Configuration cached successfully.
Route cache cleared!
Routes cached successfully.
Views cached successfully.
=== STARTING APACHE ON PORT 8080 ===
```

## 🚨 Variables à NE PAS oublier

- `APP_ENV=production` (pas `local` ou `development`)
- `APP_DEBUG=false` (sécurité)
- `APP_KEY` (généré avec artisan)
- `APP_URL` (URL Railway exacte)
