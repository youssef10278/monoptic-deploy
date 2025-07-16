# 🔍 Debug - Synchronisation des Données d'Impression

## Problème Persistant

Malgré les corrections, l'aperçu d'impression affiche toujours "Aucun article à afficher" et des totaux à 0,00 MAD.

## 🧪 Tests de Debug à Effectuer

### **Test 1: Vérification des Logs Console**

#### **Procédure :**
1. Ouvrez les **Outils de Développement** (F12)
2. Allez dans l'onglet **Console**
3. Effectuez une vente complète dans le POS
4. Observez les logs qui s'affichent

#### **Logs Attendus :**
```javascript
// Lors de la finalisation de vente
Index - handlePrintConfirmation - Données reçues: {printType: "ticket", printOptions: {...}, saleData: {...}}
Index - handlePrintConfirmation - saleData du confirmation: {id: "12345", items: [...], ...}
Index - handlePrintConfirmation - Articles: [{description: "...", quantity: 1, price: 180}, ...]

// Lors de l'ouverture du modal d'impression
PrintModal - generatePrintHTML appelé
PrintModal - Type: ticket
PrintModal - Données: {id: "12345", items: [...], ...}
PrintModal - Articles dans les données: [{...}, {...}]

// Lors de la génération du template
generateTicketA4Template - Données reçues: {id: "12345", items: [...], ...}
generateTicketA4Template - Articles: [{...}, {...}]
generateTicketA4Template - Données sécurisées: {id: "12345", items: [...], ...}
```

#### **Si les Logs Montrent :**
- **`saleData: undefined`** → Problème de sauvegarde des données
- **`Articles: []`** → Problème de transmission des articles
- **`Données reçues: {}`** → Problème de passage des props

### **Test 2: Vérification Étape par Étape**

#### **Étape 1: Sauvegarde des Données**
```javascript
// Dans finalizeSale() ou finalizeSalePartial()
// Vérifiez dans la console si ces logs apparaissent :
console.log('Sauvegarde saleData:', saleData)
console.log('Articles dans saleData:', saleData.items)
```

#### **Étape 2: Transmission au Modal de Confirmation**
```javascript
// Dans PrintConfirmationModal
// Vérifiez si ces logs apparaissent :
console.log('PrintConfirmationModal - saleData reçu:', props.saleData)
```

#### **Étape 3: Transmission au Modal d'Impression**
```javascript
// Dans handlePrintConfirmation
// Vérifiez si ces logs apparaissent :
console.log('Données transmises au PrintModal:', printDataComplete)
```

### **Test 3: Inspection Manuelle des Données**

#### **Procédure :**
1. Ouvrez la **Console** des outils de développement
2. Effectuez une vente
3. Avant de cliquer sur "Imprimer", tapez dans la console :

```javascript
// Vérifier les données stockées
console.log('confirmationData:', window.Vue?.$data?.confirmationData)

// Ou si accessible globalement
console.log('Données POS:', document.querySelector('[data-page="pos"]')?.__vue__?.$data)
```

## 🔧 Solutions selon les Résultats

### **Si `saleData` est `undefined` :**

#### **Problème :** Les données ne sont pas sauvegardées correctement

#### **Solution :**
```javascript
// Vérifier que cartItems.value n'est pas vide au moment de la sauvegarde
console.log('cartItems avant sauvegarde:', cartItems.value)

// Ajouter une vérification dans finalizeSale
if (cartItems.value.length === 0) {
  console.error('Aucun article dans le panier !')
  return
}
```

### **Si `items` est un tableau vide `[]` :**

#### **Problème :** Les articles ne sont pas correctement mappés

#### **Solution :**
```javascript
// Vérifier le mapping des articles
const saleData = {
  // ...autres propriétés
  items: cartItems.value.map(item => {
    console.log('Mapping article:', item)
    return {
      description: item.description || item.name || 'Article sans nom',
      quantity: item.quantity || 1,
      price: item.price || 0,
      details: item.details || {}
    }
  })
}
```

### **Si les Données Arrivent Mais Ne S'Affichent Pas :**

#### **Problème :** Problème dans les templates

#### **Solution :**
```javascript
// Forcer l'affichage même avec des données de test
const generateItemsRows = (items, showDetails) => {
  console.log('generateItemsRows appelé avec:', items)
  
  // Test avec données statiques
  if (!items || items.length === 0) {
    return `
      <tr>
        <td>Article de test</td>
        <td>1</td>
        <td>100,00 MAD</td>
        <td>100,00 MAD</td>
      </tr>
    `
  }
  
  // Reste du code...
}
```

## 🛠️ Corrections Temporaires pour Test

### **Correction 1: Données de Test Statiques**

Ajoutez temporairement dans `handlePrintConfirmation` :

```javascript
// Test avec données statiques
const testData = {
  id: 'TEST-123',
  date: new Date(),
  vendeur: 'Test Vendeur',
  client: { name: 'Client Test' },
  items: [
    {
      description: 'Monture Test',
      quantity: 1,
      price: 180,
      details: { marque: 'Test Brand' }
    },
    {
      description: 'Verres Test',
      quantity: 1,
      price: 250,
      details: { type: 'Progressif' }
    }
  ],
  totalAmount: 430,
  paidAmount: 430,
  remainingAmount: 0,
  paymentMethod: 'cash'
}

printData.value = testData
```

### **Correction 2: Bypass du Modal de Confirmation**

Testez en appelant directement l'impression :

```javascript
// Dans le POS, après finalisation
openPrintModal('ticket')
```

## 📋 Checklist de Debug

### **Vérifications Console :**
- [ ] Logs de sauvegarde des données apparaissent
- [ ] `saleData` contient les articles
- [ ] Transmission au modal de confirmation OK
- [ ] Transmission au modal d'impression OK
- [ ] Templates reçoivent les bonnes données

### **Vérifications Interface :**
- [ ] Modal de confirmation s'ouvre
- [ ] Résumé affiche les bonnes informations
- [ ] Modal d'impression s'ouvre
- [ ] Aperçu est généré (même vide)

### **Vérifications Données :**
- [ ] `cartItems.value` n'est pas vide avant sauvegarde
- [ ] Mapping des articles fonctionne
- [ ] Props sont correctement passés
- [ ] Templates reçoivent des données non-vides

## 🎯 Actions Immédiates

### **Action 1: Test avec Données Statiques**
Remplacez temporairement les données dynamiques par des données statiques pour isoler le problème.

### **Action 2: Vérification des Props**
Ajoutez des `console.log` à chaque étape de transmission des données.

### **Action 3: Test des Templates Isolés**
Testez les templates avec des données connues pour vérifier qu'ils fonctionnent.

### **Action 4: Vérification du Timing**
Assurez-vous que `resetSale()` n'est pas appelé avant la sauvegarde des données.

## 🚨 Points Critiques à Vérifier

1. **Timing de `resetSale()`** - Appelé après sauvegarde ?
2. **Structure de `cartItems`** - Format correct ?
3. **Transmission des Props** - Toutes les données passent ?
4. **Réactivité Vue** - Les refs sont-elles mises à jour ?
5. **Erreurs JavaScript** - Pas d'erreurs qui bloquent ?

Une fois ces vérifications effectuées, nous pourrons identifier précisément où se situe le problème et le corriger définitivement.
