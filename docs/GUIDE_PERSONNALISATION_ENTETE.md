# 🏢 Guide - Personnalisation de l'En-tête pour l'Opticien

## ✅ **Problème Résolu**

**Avant :** L'en-tête des documents d'impression affichait les informations génériques de "MONOPTIC" au lieu des informations personnalisées de chaque opticien.

**Maintenant :** Chaque opticien peut configurer ses propres informations qui apparaîtront sur tous ses documents d'impression.

## 🎯 **Fonctionnalités Implémentées**

### **1. Système de Configuration Personnalisée**

#### **Informations Configurables :**
- ✅ **Nom de l'optique** (obligatoire)
- ✅ **Slogan** (optionnel)
- ✅ **Adresse complète** (rue, ville, code postal)
- ✅ **Coordonnées** (téléphone, email, site web)
- ✅ **Informations légales** (RC, ICE, Patente, CNSS)
- ✅ **Logo** (upload d'image - à implémenter)
- ✅ **Options d'affichage** (slogan, site web, infos légales)

#### **Validation Automatique :**
- ✅ Nom de l'optique obligatoire
- ✅ Téléphone obligatoire
- ✅ Adresse obligatoire
- ✅ Format email valide
- ✅ Format téléphone marocain valide

### **2. Page de Configuration**

#### **Accès :**
```
Menu utilisateur → "Configuration Optique"
ou
URL directe : /settings/optician
```

#### **Interface :**
- ✅ **Formulaire organisé** en sections logiques
- ✅ **Aperçu en temps réel** de l'en-tête
- ✅ **Validation en temps réel** des champs
- ✅ **Sauvegarde locale et serveur** (avec fallback)
- ✅ **Réinitialisation** aux valeurs par défaut

### **3. Intégration dans les Documents**

#### **Documents Concernés :**
- ✅ **Tickets de vente** (A4 et thermique)
- ✅ **Devis** (A4 avec en-tête complet)
- ✅ **Reçus** (format compact)

#### **Affichage Adaptatif :**
- ✅ **Logo** (si uploadé)
- ✅ **Nom de l'optique** (toujours affiché)
- ✅ **Slogan** (si activé dans les options)
- ✅ **Adresse complète** (formatée automatiquement)
- ✅ **Coordonnées** (téléphone formaté, email)
- ✅ **Site web** (si activé et renseigné)
- ✅ **Infos légales** (RC, ICE si activées)

## 📋 **Guide d'Utilisation**

### **Étape 1 : Accéder à la Configuration**

1. **Connectez-vous** à votre interface Monoptic
2. **Cliquez sur votre avatar** en haut à droite
3. **Sélectionnez "Configuration Optique"**

### **Étape 2 : Remplir les Informations de Base**

#### **Champs Obligatoires :**
```
✅ Nom de l'optique : "Optique Vision Plus"
✅ Téléphone : "+212 6XX XX XX XX"
✅ Adresse : "123 Avenue Mohammed V"
✅ Ville : "Casablanca"
```

#### **Champs Optionnels :**
```
• Slogan : "Votre Vision, Notre Passion"
• Code postal : "20000"
• Email : "contact@optiquevision.ma"
• Site web : "www.optiquevision.ma"
```

### **Étape 3 : Configurer les Informations Légales**

```
• RC : "RC 123456"
• ICE : "ICE 001234567890123"
• Patente : "PAT 789012"
• CNSS : "CNSS 345678"
```

### **Étape 4 : Personnaliser l'Affichage**

#### **Options Disponibles :**
- ☑️ **Afficher le slogan** sur les documents
- ☑️ **Afficher le site web** sur les documents
- ☑️ **Afficher les informations légales** (RC, ICE)

### **Étape 5 : Prévisualiser et Sauvegarder**

1. **Vérifiez l'aperçu** en temps réel
2. **Corrigez** si nécessaire
3. **Cliquez "Sauvegarder"**
4. **Confirmez** le message de succès

## 🎨 **Exemples d'En-têtes Personnalisés**

### **Exemple 1 : Optique Moderne**
```
┌─────────────────────────────────────────┐
│  [LOGO]  OPTIQUE VISION PLUS            │
│          Votre Vision, Notre Passion    │
│                                         │
│  123 Avenue Mohammed V, Casablanca 20000│
│  Tél: +212 6XX XX XX XX                 │
│  Email: contact@optiquevision.ma        │
│  Web: www.optiquevision.ma              │
│                                         │
│  RC: RC 123456 | ICE: ICE 001234567890123│
└─────────────────────────────────────────┘
```

### **Exemple 2 : Optique Classique**
```
┌─────────────────────────────────────────┐
│         OPTIQUE AL BASAR                │
│                                         │
│  45 Rue de la Liberté, Rabat 10000     │
│  Tél: +212 5XX XX XX XX                 │
│  Email: info@optiquebasar.ma            │
└─────────────────────────────────────────┘
```

### **Exemple 3 : Optique Compacte**
```
┌─────────────────────────────────────────┐
│  OPTIQUE NOUR - Votre Opticien de Confiance │
│  67 Boulevard Hassan II, Fès            │
│  Tél: +212 6XX XX XX XX                 │
└─────────────────────────────────────────┘
```

## 🔧 **Fonctionnalités Techniques**

### **Sauvegarde des Données**
```javascript
// Sauvegarde automatique
- Serveur (API) : Priorité 1
- Local Storage : Fallback automatique
- Cache navigateur : Performances
```

### **Validation des Formats**
```javascript
// Téléphone marocain
Formats acceptés : +212XXXXXXXXX, 0XXXXXXXXX, 212XXXXXXXXX

// Email
Format standard : nom@domaine.extension

// Champs obligatoires
Validation en temps réel avec messages d'erreur
```

### **Gestion des Erreurs**
```javascript
// Cas gérés
- Perte de connexion → Sauvegarde locale
- Données corrompues → Valeurs par défaut
- Champs manquants → Messages d'aide
```

## 📱 **Impact sur les Documents**

### **Avant la Configuration**
```
En-tête générique :
┌─────────────────────────────────────────┐
│  MONOPTIC                               │
│  Votre Opticien de Confiance           │
│  123 Avenue Mohammed V, Casablanca     │
│  Tél: +212 522 123 456                 │
└─────────────────────────────────────────┘
```

### **Après la Configuration**
```
En-tête personnalisé :
┌─────────────────────────────────────────┐
│  [LOGO] OPTIQUE [NOM PERSONNALISÉ]      │
│  [SLOGAN PERSONNALISÉ]                  │
│  [ADRESSE PERSONNALISÉE]                │
│  [COORDONNÉES PERSONNALISÉES]           │
│  [INFOS LÉGALES PERSONNALISÉES]         │
└─────────────────────────────────────────┘
```

## ✅ **Avantages pour l'Opticien**

### **Professionnalisme**
- ✅ **Documents personnalisés** avec identité visuelle
- ✅ **Informations légales** conformes
- ✅ **Coordonnées exactes** pour contact client

### **Conformité**
- ✅ **RC et ICE** affichés selon réglementation
- ✅ **Adresse officielle** sur tous documents
- ✅ **Informations à jour** automatiquement

### **Marketing**
- ✅ **Slogan** pour renforcer l'image de marque
- ✅ **Site web** pour diriger vers boutique en ligne
- ✅ **Présentation professionnelle** qui inspire confiance

## 🚀 **Prochaines Améliorations**

### **Fonctionnalités à Venir**
- 📸 **Upload de logo** (images PNG, JPG)
- 🎨 **Thèmes d'en-tête** (couleurs, polices)
- 📄 **Templates personnalisés** par type de document
- 🌐 **Multi-langues** (français, arabe)
- 📊 **Statistiques d'utilisation** des documents

### **Intégrations Futures**
- 🔗 **API externe** pour validation RC/ICE
- 📱 **QR Code** avec coordonnées
- 💳 **Informations bancaires** pour paiements
- 📧 **Signature email** automatique

## 📞 **Support**

### **En Cas de Problème**
1. **Vérifiez** que tous les champs obligatoires sont remplis
2. **Testez** avec un document d'impression
3. **Contactez le support** si problème persistant

### **Conseils d'Utilisation**
- ✅ **Mettez à jour** régulièrement vos informations
- ✅ **Testez** l'aperçu avant impression
- ✅ **Sauvegardez** après chaque modification
- ✅ **Vérifiez** la conformité des informations légales

**Votre optique a maintenant une identité unique sur tous ses documents !** 🎉
