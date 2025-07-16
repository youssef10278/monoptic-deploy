# 🚀 Guide de Production - Monoptic sur Railway

## ✅ Application Déployée avec Succès

**URL Production :** https://monoptic-deploy-production.up.railway.app

## 🔗 Endpoints Disponibles

### **Production (Publics)**
- **`/`** : Page principale de l'application
- **`/health`** : Healthcheck pour Railway (retourne "OK")
- **`/api/health`** : Healthcheck détaillé (JSON)

### **Développement Uniquement**
- **`/debug`** : Informations techniques (désactivé en production)
- **`/test-db`** : Test de connexion base de données (désactivé en production)

## 🛠️ Architecture Technique

### **Stack**
- **Framework :** Laravel 11.x
- **Serveur :** Apache 2.4.62
- **PHP :** 8.2.29
- **Base de données :** PostgreSQL (Railway)
- **Plateforme :** Railway

### **Configuration**
- **Port :** Dynamique (assigné par Railway)
- **Environment :** Production
- **Debug :** Désactivé
- **Cache :** Optimisé (config, routes, views)

## 📋 Maintenance

### **Déploiement**
1. Push sur `main` → Déploiement automatique
2. Railway rebuild l'image Docker
3. Migrations automatiques
4. Cache Laravel reconstruit

### **Logs**
- **Railway Dashboard** → Service → Deployments → View Logs
- **Erreurs :** Visibles dans stderr
- **Accès :** Visibles dans stdout

### **Variables d'Environnement**
Voir `RAILWAY_VARIABLES.md` pour la liste complète.

## 🔒 Sécurité

### **Activé**
- ✅ `APP_DEBUG=false`
- ✅ `APP_ENV=production`
- ✅ Routes de debug désactivées
- ✅ Headers de sécurité Apache
- ✅ Protection fichiers sensibles

### **À Surveiller**
- Logs d'erreurs
- Performances base de données
- Utilisation mémoire
- Temps de réponse

## 🚨 Dépannage

### **Si l'application ne répond pas**
1. Vérifier les logs Railway
2. Tester `/health` endpoint
3. Vérifier variables d'environnement
4. Redéployer si nécessaire

### **Erreurs communes**
- **502 Bad Gateway** : Problème Apache/PHP
- **500 Internal Error** : Erreur Laravel (voir logs)
- **Database connection** : Vérifier variables DB

## 📞 Support

- **Repository :** GitHub - monoptic-deploy
- **Platform :** Railway Dashboard
- **Logs :** Railway Deployments

---

## 🎉 Application Fonctionnelle et Prête !

**Dernière mise à jour :** $(date)
**Status :** ✅ PRODUCTION READY
