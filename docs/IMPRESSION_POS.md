# 🖨️ Système d'Impression POS - Guide d'Utilisation

## Vue d'Ensemble

Le système d'impression POS de Monoptic permet d'imprimer des **tickets de vente** et des **devis** au format A4 professionnel directement depuis l'interface de vente.

## 🎯 Fonctionnalités

### ✅ Types de Documents

-   **Ticket de Vente A4** - Document de vente finalisée
-   **Devis A4** - Proposition commerciale avec conditions

### ✅ Options d'Impression

-   **Format** : A4 (21 x 29.7 cm) ou Ticket thermique (8cm)
-   **Orientation** : Portrait ou Paysage
-   **Copies** : 1 à 10 exemplaires
-   **Personnalisation** : Logo, détails produits, informations légales

## 🚀 Utilisation

### 1. Accès aux Fonctions d'Impression

Dans l'interface POS, vous trouverez deux boutons d'impression :

```
┌─────────────────┐  ┌─────────────────┐
│ 🖨️ Imprimer Devis │  │ 🖨️ Imprimer Ticket │
└─────────────────┘  └─────────────────┘
```

### 2. Impression de Devis

**Quand utiliser :** Avant finalisation de la vente, pour présenter une proposition au client.

**Contenu inclus :**

-   Informations entreprise (logo, coordonnées)
-   Détails du devis (numéro, date, validité)
-   Informations client
-   Liste détaillée des articles avec prix HT
-   Totaux avec TVA (20%)
-   Conditions générales
-   Zones de signature (client + magasin)

**Processus :**

1. Ajoutez les articles au panier
2. Sélectionnez le client (optionnel)
3. Cliquez sur "Imprimer Devis"
4. Configurez les options d'impression
5. Prévisualisez le document
6. Imprimez

### 3. Impression de Ticket de Vente

**Quand utiliser :** Après finalisation et paiement de la vente.

**Contenu inclus :**

-   Informations entreprise
-   Numéro de ticket et date/heure
-   Informations vendeur et caisse
-   Détails client
-   Liste des articles avec quantités et prix
-   Total de la vente
-   Informations de paiement
-   Mentions légales et garantie

**Processus :**

1. Finalisez la vente (paiement complet ou partiel)
2. Le système propose automatiquement l'impression
3. Ou cliquez manuellement sur "Imprimer Ticket"
4. Configurez les options
5. Imprimez

## ⚙️ Configuration

### Options d'Impression Disponibles

```javascript
// Format
- A4 (21 x 29.7 cm)     // Recommandé
- Ticket thermique (8cm) // Pour imprimantes spécialisées

// Orientation
- Portrait              // Recommandé pour A4
- Paysage              // Pour tableaux larges

// Personnalisation
- Inclure le logo       // Affichage du logo entreprise
- Afficher les détails  // Détails techniques des produits
- Date de validité      // Pour les devis (30 jours par défaut)
```

### Informations Entreprise

Les informations suivantes sont automatiquement incluses :

```javascript
MONOPTIC
Votre Opticien de Confiance

123 Avenue Mohammed V
Casablanca 20000, Maroc

Tél: +212 522 123 456
Email: contact@monoptic.ma
Web: www.monoptic.ma

RC: RC 123456 | ICE: ICE 901234567890123
```

## 📋 Templates de Documents

### Template Ticket de Vente

```
┌─────────────────────────────────────┐
│            MONOPTIC                 │
│     Votre Opticien de Confiance     │
│                                     │
│        TICKET DE VENTE              │
│             #12345                  │
│                                     │
│ Date: 15/01/2024    Heure: 14:30   │
│ Vendeur: Jean       Caisse: POS-01  │
│                                     │
│ Client: Marie Dupont                │
│ Tél: +212 661 234 567              │
│                                     │
│ ─────────────────────────────────── │
│ Article         Qté  P.U.   Total   │
│ ─────────────────────────────────── │
│ Monture Ray-Ban  1   180MAD 180MAD  │
│ Verres progressifs 1  250MAD 250MAD │
│ ─────────────────────────────────── │
│                    TOTAL:    430MAD │
│                                     │
│ Paiement: Carte bancaire            │
│ Montant payé: 430,00 MAD           │
│                                     │
│ Merci de votre confiance !          │
│ Garantie selon conditions fabricant │
└─────────────────────────────────────┘
```

### Template Devis

```
┌─────────────────────────────────────┐
│            MONOPTIC                 │
│     Votre Opticien de Confiance     │
│                                     │
│              DEVIS                  │
│           N° DEV-12345              │
│                                     │
│ Date: 15/01/2024                   │
│ Valable jusqu'au: 14/02/2024       │
│ Vendeur: Jean Dupont               │
│                                     │
│ Client: Marie Martin               │
│ Tél: +212 661 234 567             │
│                                     │
│ ─────────────────────────────────── │
│ Désignation    Qté  P.U.HT  Total  │
│ ─────────────────────────────────── │
│ Monture...      1   150MAD  150MAD │
│ Verres...       1   200MAD  200MAD │
│ ─────────────────────────────────── │
│ Sous-total HT:           350,00MAD │
│ TVA (20%):                70,00MAD │
│ TOTAL TTC:               420,00MAD │
│                                     │
│ CONDITIONS GÉNÉRALES:               │
│ • Devis valable 30 jours           │
│ • Acompte 50% à la commande        │
│ • Délai livraison: 5-10 jours      │
│                                     │
│ Signature Client    Cachet Magasin  │
│ ┌─────────────┐    ┌─────────────┐  │
│ │             │    │             │  │
│ │             │    │             │  │
│ └─────────────┘    └─────────────┘  │
│ Bon pour accord         MONOPTIC    │
└─────────────────────────────────────┘
```

## 🔧 Intégration Technique

### Composants Utilisés

```javascript
// Composant principal
PrintModal.vue;

// Templates d'impression
PrintTemplates.js;

// Utilitaires
currency.js; // Formatage des prix
date.js; // Formatage des dates
```

### Workflow d'Impression

```javascript
1. Utilisateur clique sur "Imprimer"
   ↓
2. Ouverture du modal PrintModal
   ↓
3. Préparation des données (preparePrintData)
   ↓
4. Génération du template HTML
   ↓
5. Aperçu dans le modal
   ↓
6. Configuration des options
   ↓
7. Génération du document final
   ↓
8. Ouverture dans nouvelle fenêtre
   ↓
9. Impression via navigateur
```

### Données Requises

```javascript
// Structure des données pour impression
{
  id: "12345",                    // ID vente/devis
  date: new Date(),               // Date de création
  vendeur: "Jean Dupont",         // Nom du vendeur
  client: {                       // Informations client
    name: "Marie Martin",
    phone: "+212 661 234 567",
    email: "marie@email.com",
    address: "123 Rue..."
  },
  items: [                        // Articles
    {
      description: "Monture Ray-Ban",
      quantity: 1,
      price: 180.00,
      details: {
        brand: "Ray-Ban",
        reference: "RB3025"
      }
    }
  ],
  paymentMethod: "card",          // Mode de paiement
  paidAmount: 430.00,            // Montant payé
  remainingAmount: 0             // Reste à payer
}
```

## 🎨 Personnalisation

### Modifier les Informations Entreprise

Éditez le fichier `PrintTemplates.js` :

```javascript
export const COMPANY_CONFIG = {
    name: "VOTRE_ENTREPRISE",
    slogan: "Votre slogan",
    address: {
        street: "Votre adresse",
        city: "Votre ville",
        // ...
    },
};
```

### Ajouter un Logo

1. Placez votre logo dans `public/images/logo.png`
2. Modifiez la fonction `generateCompanyHeader()` dans `PrintTemplates.js`

### Personnaliser les Styles

Modifiez les fonctions `getTicketStyles()` et `getDevisStyles()` dans `PrintTemplates.js`.

## 🐛 Dépannage

### Problèmes Courants

**L'impression ne fonctionne pas :**

-   Vérifiez que les pop-ups sont autorisées
-   Testez avec un autre navigateur
-   Vérifiez la connexion de l'imprimante

**Le formatage est incorrect :**

-   Vérifiez les paramètres d'impression du navigateur
-   Utilisez "Imprimer en arrière-plan" désactivé
-   Sélectionnez le bon format de papier (A4)

**Les données ne s'affichent pas :**

-   Vérifiez que la vente est finalisée (pour les tickets)
-   Vérifiez que le panier contient des articles (pour les devis)

### Support

Pour toute assistance technique, contactez l'équipe de développement Monoptic.

---

**Version :** 1.0  
**Dernière mise à jour :** Janvier 2024  
**Auteur :** Équipe Monoptic
