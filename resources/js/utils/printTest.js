/**
 * Utilitaire de test pour le système d'impression POS
 * Permet de tester les fonctionnalités d'impression sans passer par l'interface
 */

import { generateTicketA4Template, generateDevisA4Template } from '../Components/POS/PrintTemplates.js'

/**
 * Données de test pour l'impression
 */
export const TEST_DATA = {
  // Données de base
  id: "12345",
  date: new Date(),
  vendeur: "Jean Dupont",
  
  // Client de test
  client: {
    name: "Marie Martin",
    phone: "+212 661 234 567",
    email: "marie.martin@email.com",
    address: "123 Rue Hassan II, Casablanca"
  },
  
  // Articles de test
  items: [
    {
      description: "Monture Ray-Ban Aviator Classic",
      quantity: 1,
      price: 180.00,
      details: {
        brand: "Ray-Ban",
        reference: "RB3025",
        couleur: "Or",
        taille: "58-14-135"
      }
    },
    {
      description: "Verres progressifs Varilux Comfort",
      quantity: 1,
      price: 250.00,
      details: {
        type: "Progressif",
        indice: "1.67",
        traitement: "Anti-reflet + Durci"
      }
    },
    {
      description: "Étui de protection",
      quantity: 1,
      price: 25.00,
      details: {
        type: "Accessoire",
        matériau: "Cuir synthétique"
      }
    }
  ],
  
  // Informations de paiement
  paymentMethod: "card",
  paidAmount: 455.00,
  remainingAmount: 0
}

/**
 * Données de test pour devis
 */
export const TEST_DEVIS_DATA = {
  ...TEST_DATA,
  paymentMethod: null,
  paidAmount: 0,
  remainingAmount: 455.00
}

/**
 * Teste la génération d'un ticket de vente
 */
export const testTicketGeneration = () => {
  console.log('🧪 Test génération ticket de vente...')
  
  try {
    const ticketHTML = generateTicketA4Template(TEST_DATA, {
      showLogo: true,
      showDetails: true,
      showLegalInfo: true
    })
    
    console.log('✅ Ticket généré avec succès')
    console.log('📄 Taille du HTML:', ticketHTML.length, 'caractères')
    
    return ticketHTML
  } catch (error) {
    console.error('❌ Erreur génération ticket:', error)
    return null
  }
}

/**
 * Teste la génération d'un devis
 */
export const testDevisGeneration = () => {
  console.log('🧪 Test génération devis...')
  
  try {
    const devisHTML = generateDevisA4Template(TEST_DEVIS_DATA, {
      showLogo: true,
      showDetails: true,
      showConditions: true,
      validityDays: 30
    })
    
    console.log('✅ Devis généré avec succès')
    console.log('📄 Taille du HTML:', devisHTML.length, 'caractères')
    
    return devisHTML
  } catch (error) {
    console.error('❌ Erreur génération devis:', error)
    return null
  }
}

/**
 * Ouvre un document dans une nouvelle fenêtre pour test
 */
export const previewDocument = (html, title = 'Aperçu Document') => {
  if (!html) {
    console.error('❌ Aucun HTML à prévisualiser')
    return
  }
  
  try {
    const previewWindow = window.open('', '_blank', 'width=800,height=600')
    previewWindow.document.write(html)
    previewWindow.document.close()
    previewWindow.document.title = title
    
    console.log('✅ Document ouvert dans nouvelle fenêtre')
  } catch (error) {
    console.error('❌ Erreur ouverture aperçu:', error)
  }
}

/**
 * Teste l'impression directe
 */
export const testDirectPrint = (html) => {
  if (!html) {
    console.error('❌ Aucun HTML à imprimer')
    return
  }
  
  try {
    const printWindow = window.open('', '_blank')
    printWindow.document.write(html)
    printWindow.document.close()
    
    printWindow.onload = () => {
      printWindow.print()
      setTimeout(() => {
        printWindow.close()
      }, 1000)
    }
    
    console.log('✅ Impression lancée')
  } catch (error) {
    console.error('❌ Erreur impression:', error)
  }
}

/**
 * Lance tous les tests
 */
export const runAllTests = () => {
  console.log('🚀 Lancement des tests d\'impression...')
  console.log('=====================================')
  
  // Test ticket
  const ticketHTML = testTicketGeneration()
  if (ticketHTML) {
    console.log('✅ Test ticket: RÉUSSI')
  } else {
    console.log('❌ Test ticket: ÉCHEC')
  }
  
  console.log('-------------------------------------')
  
  // Test devis
  const devisHTML = testDevisGeneration()
  if (devisHTML) {
    console.log('✅ Test devis: RÉUSSI')
  } else {
    console.log('❌ Test devis: ÉCHEC')
  }
  
  console.log('=====================================')
  console.log('🏁 Tests terminés')
  
  return {
    ticket: ticketHTML,
    devis: devisHTML
  }
}

/**
 * Fonction de test interactive pour la console
 */
export const interactiveTest = () => {
  console.log('🎮 Mode test interactif activé')
  console.log('Commandes disponibles:')
  console.log('- testTicketGeneration() : Teste la génération de ticket')
  console.log('- testDevisGeneration() : Teste la génération de devis')
  console.log('- previewDocument(html, title) : Aperçu dans nouvelle fenêtre')
  console.log('- testDirectPrint(html) : Test d\'impression directe')
  console.log('- runAllTests() : Lance tous les tests')
  
  // Exposer les fonctions globalement pour les tests
  window.printTest = {
    testTicketGeneration,
    testDevisGeneration,
    previewDocument,
    testDirectPrint,
    runAllTests,
    TEST_DATA,
    TEST_DEVIS_DATA
  }
  
  console.log('✅ Fonctions exposées dans window.printTest')
  console.log('Exemple: window.printTest.runAllTests()')
}

/**
 * Validation des données avant impression
 */
export const validatePrintData = (data) => {
  const errors = []
  
  // Vérifications obligatoires
  if (!data.id) errors.push('ID manquant')
  if (!data.date) errors.push('Date manquante')
  if (!data.items || data.items.length === 0) errors.push('Aucun article')
  
  // Vérifications des articles
  data.items?.forEach((item, index) => {
    if (!item.description) errors.push(`Article ${index + 1}: description manquante`)
    if (!item.quantity || item.quantity <= 0) errors.push(`Article ${index + 1}: quantité invalide`)
    if (!item.price || item.price < 0) errors.push(`Article ${index + 1}: prix invalide`)
  })
  
  // Vérifications paiement (pour tickets)
  if (data.paymentMethod && !data.paidAmount && data.paidAmount !== 0) {
    errors.push('Montant payé manquant')
  }
  
  return {
    isValid: errors.length === 0,
    errors
  }
}

/**
 * Calcule les statistiques d'un document
 */
export const getDocumentStats = (data) => {
  const totalItems = data.items?.length || 0
  const totalQuantity = data.items?.reduce((sum, item) => sum + item.quantity, 0) || 0
  const subtotal = data.items?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0
  const tva = subtotal * 0.20
  const total = subtotal + tva
  
  return {
    totalItems,
    totalQuantity,
    subtotal,
    tva,
    total,
    hasClient: !!data.client,
    isPaid: data.paidAmount >= total,
    remainingAmount: Math.max(0, total - (data.paidAmount || 0))
  }
}

// Auto-initialisation en mode développement
if (process.env.NODE_ENV === 'development') {
  console.log('🔧 Mode développement détecté')
  console.log('Tapez interactiveTest() pour activer les tests d\'impression')
  
  // Exposer la fonction d'initialisation
  window.interactiveTest = interactiveTest
}
