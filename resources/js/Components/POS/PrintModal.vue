<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">
          {{ type === 'ticket' ? 'Impression Ticket de Vente' : 'Impression Devis' }}
        </h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Options d'impression -->
      <div class="p-6 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
            <select v-model="printOptions.format" class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="a4">A4 (21 x 29.7 cm)</option>
              <option value="thermal">Ticket Thermique (8cm)</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Orientation</label>
            <select v-model="printOptions.orientation" class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="portrait">Portrait</option>
              <option value="landscape">Paysage</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Copies</label>
            <input v-model="printOptions.copies" type="number" min="1" max="10" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
          </div>
        </div>
        
        <!-- Options supplémentaires -->
        <div class="mt-4 space-y-2">
          <label class="flex items-center">
            <input v-model="printOptions.showLogo" type="checkbox" class="mr-2">
            <span class="text-sm text-gray-700">Inclure le logo</span>
          </label>
          
          <label class="flex items-center">
            <input v-model="printOptions.showDetails" type="checkbox" class="mr-2">
            <span class="text-sm text-gray-700">Afficher les détails produits</span>
          </label>
          
          <label v-if="type === 'devis'" class="flex items-center">
            <input v-model="printOptions.showValidityDate" type="checkbox" class="mr-2">
            <span class="text-sm text-gray-700">Afficher la date de validité</span>
          </label>
        </div>
      </div>

      <!-- Aperçu -->
      <div class="flex-1 overflow-y-auto p-6 min-h-0">
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="text-sm font-medium text-gray-700 mb-4">Aperçu</h3>
          <div id="print-preview" class="bg-white shadow-sm" :class="getPreviewClass()">
            <!-- Le contenu sera généré dynamiquement -->
            <div v-html="previewContent"></div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex-shrink-0 flex items-center justify-end space-x-3 p-6 border-t border-gray-200">
        <button @click="$emit('close')" 
                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
          Annuler
        </button>
        
        <button @click="previewPrint" 
                class="px-4 py-2 text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-md transition-colors">
          Aperçu
        </button>
        
        <button @click="printDocument" 
                class="px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">
          <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
          Imprimer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { formatPrice } from '../../utils/currency.js'
import { formatDate } from '../../utils/date.js'
import { generateTicketA4Template, generateDevisA4Template } from './PrintTemplates.js'
import { getOpticianConfig, formatConfigForTemplates, initOpticianConfig } from '../../utils/opticianConfig.js'

const props = defineProps({
  show: Boolean,
  type: {
    type: String,
    required: true,
    validator: value => ['ticket', 'devis'].includes(value)
  },
  data: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'printed'])

// Options d'impression
const printOptions = ref({
  format: 'a4',
  orientation: 'portrait',
  copies: 1,
  showLogo: true,
  showDetails: true,
  showValidityDate: true
})

// Contenu de l'aperçu
const previewContent = ref('<p>Chargement de l\'aperçu...</p>')

// Classes CSS pour l'aperçu
const getPreviewClass = () => {
  const baseClass = 'mx-auto'
  if (printOptions.value.format === 'a4') {
    return `${baseClass} ${printOptions.value.orientation === 'portrait' ? 'w-[210mm] h-[297mm]' : 'w-[297mm] h-[210mm]'} scale-50 origin-top`
  }
  return `${baseClass} w-[80mm] scale-75 origin-top`
}

// Configuration de l'opticien (fonctions utilitaires)
const getLocalOpticianConfig = () => {
  // Configuration par défaut
  const defaultConfig = {
    name: 'Optique Vision Plus',
    slogan: 'Votre Opticien de Confiance',
    address: {
      street: '123 Avenue Mohammed V',
      city: 'Casablanca',
      postalCode: '20000',
      country: 'Maroc'
    },
    contact: {
      phone: '+212 522 123 456',
      email: 'contact@optiquevision.ma',
      website: 'www.optiquevision.ma'
    },
    legal: {
      rc: 'RC 123456',
      ice: 'ICE 001234567890123'
    },
    display: {
      showSlogan: true,
      showWebsite: true,
      showLegalInfo: true
    }
  }

  // Essayer de récupérer la configuration depuis le localStorage
  try {
    const cachedConfig = localStorage.getItem('opticianConfig')
    return cachedConfig ? JSON.parse(cachedConfig) : defaultConfig
  } catch (error) {
    console.error('Erreur chargement config:', error)
    return defaultConfig
  }
}

const generateHeaderSync = () => {
  const config = getLocalOpticianConfig()

  return `
    <div class="text-center mb-6 border-b border-gray-300 pb-4">
      <div class="flex items-center justify-center mb-2">
        ${config.logo?.url ? `
          <img src="${config.logo.url}" alt="Logo ${config.name}" class="w-16 h-16 mr-4 object-contain">
        ` : ''}
        <div>
          <h1 class="text-xl font-bold">${config.name}</h1>
          ${config.display?.showSlogan ? `<p class="text-sm text-gray-600">${config.slogan}</p>` : ''}
        </div>
      </div>
      <div class="text-xs text-gray-500">
        <p>${config.address.street}, ${config.address.city} ${config.address.postalCode}</p>
        <p>Tél: ${config.contact.phone} | Email: ${config.contact.email}</p>
        ${config.display?.showWebsite && config.contact.website ? `<p>Web: ${config.contact.website}</p>` : ''}
      </div>
    </div>
  `
}

const generateFooterSync = () => {
  const config = getLocalOpticianConfig()

  return `
    <div class="mt-8 pt-4 border-t border-gray-200 text-center text-xs text-gray-500">
      <p>Merci de votre confiance !</p>
      ${config.display?.showLegalInfo ? `
        <div class="mt-2">
          <p>RC: ${config.legal.rc} | ICE: ${config.legal.ice}</p>
        </div>
      ` : ''}
    </div>
  `
}

// Styles CSS pour l'aperçu
const getPreviewStyles = () => `
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: white;
      color: #333;
    }
    .ticket-container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .text-lg { font-size: 18px; }
    .text-sm { font-size: 14px; }
    .text-xs { font-size: 12px; }
    .mb-2 { margin-bottom: 8px; }
    .mb-4 { margin-bottom: 16px; }
    .mb-6 { margin-bottom: 24px; }
    .mb-8 { margin-bottom: 32px; }
    .mt-2 { margin-top: 8px; }
    .mt-4 { margin-top: 16px; }
    .mt-8 { margin-top: 32px; }
    .p-6 { padding: 24px; }
    .py-2 { padding-top: 8px; padding-bottom: 8px; }
    .pt-4 { padding-top: 16px; }
    .pb-4 { padding-bottom: 16px; }
    .border-b { border-bottom: 1px solid #e5e7eb; }
    .border-t { border-top: 1px solid #e5e7eb; }
    .border-gray-300 { border-color: #d1d5db; }
    .border-gray-200 { border-color: #e5e7eb; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .grid { display: grid; }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .gap-4 { gap: 16px; }
    .gap-8 { gap: 32px; }
    .w-full { width: 100%; }
    .w-16 { width: 64px; }
    .h-16 { height: 64px; }
    .mr-4 { margin-right: 16px; }
    .flex { display: flex; }
    .items-center { align-items: center; }
    .justify-center { justify-content: center; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
    }
    th, td {
      padding: 8px;
      border-bottom: 1px solid #e5e7eb;
      vertical-align: top;
    }
    th {
      background-color: #f9fafb;
      font-weight: bold;
      text-align: left;
    }
    .text-center th, .text-center td { text-align: center; }
    .text-right th, .text-right td { text-align: right; }
    .company-header {
      text-align: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #e5e7eb;
    }
    .company-header h1 {
      font-size: 24px;
      font-weight: bold;
      margin: 0 0 8px 0;
      color: #1f2937;
    }
    .company-header p {
      margin: 4px 0;
      color: #6b7280;
    }
    .totals-section {
      margin-top: 24px;
      padding-top: 16px;
      border-top: 1px solid #e5e7eb;
    }
    .payment-section {
      margin-top: 16px;
      padding: 16px;
      background-color: #f9fafb;
      border-radius: 8px;
    }
    .payment-section h3 {
      margin: 0 0 12px 0;
      font-size: 16px;
      font-weight: bold;
    }
    .payment-row {
      display: flex;
      justify-content: space-between;
      margin: 8px 0;
    }
    .amount {
      font-weight: bold;
    }
    .highlight {
      background-color: #fef3c7;
      padding: 4px 8px;
      border-radius: 4px;
    }
    .footer-section {
      margin-top: 24px;
      padding-top: 12px;
      border-top: 1px solid #e5e7eb;
      text-align: center;
      font-size: 12px;
      color: #6b7280;
    }
  </style>
`

// Génération du contenu ticket
const generateTicketContent = () => {
  const { data } = props

  // Utiliser une version synchrone pour éviter les problèmes de Promise
  const header = printOptions.value.showLogo ? generateHeaderSync() : ''
  const footer = generateFooterSync()

  return `
    ${getPreviewStyles()}
    <div class="ticket-container">
      ${header}

      <!-- Informations vente -->
      <div class="mb-6">
        <h2 class="text-lg font-bold text-center mb-4">TICKET DE VENTE</h2>
        <div class="grid grid-cols-2 gap-4 text-xs">
          <div>
            <p><strong>N° Vente:</strong> #${data.id || 'NOUVEAU'}</p>
            <p><strong>Date:</strong> ${formatDate(data.date || new Date())}</p>
            <p><strong>Vendeur:</strong> ${data.vendeur || 'Système'}</p>
          </div>
          <div>
            <p><strong>Client:</strong> ${data.client?.name || 'Client de passage'}</p>
            ${data.client?.phone ? `<p><strong>Tél:</strong> ${data.client.phone}</p>` : ''}
            ${data.client?.email ? `<p><strong>Email:</strong> ${data.client.email}</p>` : ''}
          </div>
        </div>
      </div>

      <!-- Articles -->
      <div class="mb-6">
        <table class="w-full text-xs">
          <thead>
            <tr class="border-b border-gray-300">
              <th class="text-left py-2">Article</th>
              <th class="text-center py-2">Qté</th>
              <th class="text-right py-2">P.U.</th>
              <th class="text-right py-2">Total</th>
            </tr>
          </thead>
          <tbody>
            ${generateArticlesRows(data.items || [])}
          </tbody>
        </table>
      </div>

      <!-- Totaux -->
      ${generateTotalsSection(data)}

      <!-- Paiement -->
      ${generatePaymentSection(data)}

      <!-- Footer -->
      ${footer}
    </div>
  `
}

// Styles CSS pour le devis
const getDevisStyles = () => `
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: white;
      color: #333;
      line-height: 1.4;
    }
    .devis-container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .font-semibold { font-weight: 600; }
    .text-xl { font-size: 24px; }
    .text-lg { font-size: 18px; }
    .text-sm { font-size: 14px; }
    .text-xs { font-size: 12px; }
    .mb-2 { margin-bottom: 8px; }
    .mb-4 { margin-bottom: 16px; }
    .mb-6 { margin-bottom: 24px; }
    .mb-8 { margin-bottom: 32px; }
    .mt-2 { margin-top: 8px; }
    .mt-8 { margin-top: 32px; }
    .mt-12 { margin-top: 48px; }
    .mt-16 { margin-top: 64px; }
    .p-8 { padding: 32px; }
    .py-2 { padding-top: 8px; padding-bottom: 8px; }
    .pt-2 { padding-top: 8px; }
    .pb-4 { padding-bottom: 16px; }
    .border-b { border-bottom: 1px solid #e5e7eb; }
    .border-t { border-top: 1px solid #e5e7eb; }
    .border-gray-300 { border-color: #d1d5db; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .grid { display: grid; }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .gap-8 { gap: 32px; }
    .w-full { width: 100%; }
    .space-y-1 > * + * { margin-top: 4px; }
    h1, h2, h3 { margin: 0; }
    h2 {
      font-size: 28px;
      font-weight: bold;
      color: #1f2937;
      margin-bottom: 24px;
    }
    h3 {
      font-size: 16px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 12px;
    }
    p { margin: 6px 0; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
      border: 1px solid #e5e7eb;
    }
    th, td {
      padding: 12px;
      border: 1px solid #e5e7eb;
      vertical-align: top;
    }
    th {
      background-color: #f9fafb;
      font-weight: bold;
      text-align: left;
    }
    .text-center th, .text-center td { text-align: center; }
    .text-right th, .text-right td { text-align: right; }
    .company-header {
      text-align: center;
      margin-bottom: 32px;
      padding-bottom: 20px;
      border-bottom: 2px solid #e5e7eb;
    }
    .company-header h1 {
      font-size: 28px;
      font-weight: bold;
      margin: 0 0 8px 0;
      color: #1f2937;
    }
    .devis-title {
      text-align: center;
      margin-bottom: 32px;
      padding: 20px;
      background-color: #f9fafb;
      border-radius: 8px;
    }
    .devis-number {
      font-size: 18px;
      color: #6b7280;
      margin-top: 8px;
    }
    .info-section {
      background-color: #f9fafb;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .totals-section {
      margin-top: 24px;
      padding: 20px;
      background-color: #f9fafb;
      border-radius: 8px;
    }
    .totals-table {
      width: 100%;
      margin-top: 16px;
    }
    .totals-table td {
      padding: 8px 16px;
      border: none;
      border-bottom: 1px solid #e5e7eb;
    }
    .total-final {
      font-weight: bold;
      font-size: 16px;
      background-color: #e5e7eb;
    }
    .signatures-section {
      margin-top: 48px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
    }
    .signature-box {
      text-align: center;
    }
    .signature-area {
      height: 80px;
      border-bottom: 1px solid #374151;
      margin: 20px 0;
    }
    .conditions-section {
      margin-top: 32px;
      padding: 20px;
      background-color: #f9fafb;
      border-radius: 8px;
    }
    .conditions-section ul {
      margin: 0;
      padding-left: 20px;
    }
    .conditions-section li {
      margin: 8px 0;
      color: #4b5563;
    }
  </style>
`

// Génération du contenu devis
const generateDevisContent = () => {
  const { data } = props

  const header = printOptions.value.showLogo ? generateHeaderSync() : ''
  const validityDate = new Date()
  validityDate.setDate(validityDate.getDate() + 30)

  return `
    ${getDevisStyles()}
    <div class="devis-container">
      ${header}

      <!-- Titre -->
      <div class="devis-title">
        <h2 class="text-xl font-bold text-center">DEVIS</h2>
        <div class="devis-number">N° DEV-${data.id || Date.now()}</div>
      </div>

      <!-- Informations devis -->
      <div class="mb-8">
        <div class="grid grid-cols-2 gap-8">
          <div class="info-section">
            <h3 class="font-semibold mb-2">Informations Devis</h3>
            <p><strong>N° Devis:</strong> DEV-${data.id || Date.now()}</p>
            <p><strong>Date:</strong> ${formatDate(new Date())}</p>
            <p><strong>Valide jusqu'au:</strong> ${formatDate(validityDate)}</p>
            <p><strong>Vendeur:</strong> ${data.vendeur || 'Système'}</p>
          </div>
          <div class="info-section">
            <h3 class="font-semibold mb-2">Client</h3>
            <p><strong>Nom:</strong> ${data.client?.name || 'À définir'}</p>
            ${data.client?.phone ? `<p><strong>Téléphone:</strong> ${data.client.phone}</p>` : ''}
            ${data.client?.email ? `<p><strong>Email:</strong> ${data.client.email}</p>` : ''}
            ${data.client?.address ? `<p><strong>Adresse:</strong> ${data.client.address}</p>` : ''}
          </div>
        </div>
      </div>
      
      <!-- Articles détaillés -->
      <div class="mb-8">
        <h3 class="font-semibold mb-4">Détail des Articles</h3>
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="border-b-2 border-gray-300">
              <th class="text-left py-3">Désignation</th>
              <th class="text-center py-3">Qté</th>
              <th class="text-right py-3">Prix Unitaire</th>
              <th class="text-right py-3">Total HT</th>
            </tr>
          </thead>
          <tbody>
            ${generateDetailedArticlesRows(data.items || [])}
          </tbody>
        </table>
      </div>
      
      <!-- Totaux détaillés -->
      ${generateDetailedTotalsSection(data)}
      
      <!-- Conditions -->
      ${generateConditionsSection()}
      
      <!-- Footer devis -->
      ${generateDevisFooter()}
    </div>
  `
}

// Méthodes utilitaires (fonctions déjà déclarées plus haut)

const generateArticlesRows = (items) => {
  return items.map(item => `
    <tr class="border-b border-gray-100">
      <td class="py-2">
        <div class="font-medium">${item.description}</div>
        ${printOptions.value.showDetails && item.details ? `<div class="text-xs text-gray-500">${formatItemDetails(item.details)}</div>` : ''}
      </td>
      <td class="text-center py-2">${item.quantity}</td>
      <td class="text-right py-2">${formatPrice(item.price)}</td>
      <td class="text-right py-2 font-medium">${formatPrice(item.price * item.quantity)}</td>
    </tr>
  `).join('')
}

const generateTotalsSection = (data) => {
  const total = data.items?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0
  
  return `
    <div class="border-t border-gray-300 pt-4">
      <div class="flex justify-between items-center text-lg font-bold">
        <span>TOTAL:</span>
        <span>${formatPrice(total)}</span>
      </div>
    </div>
  `
}

const generatePaymentSection = (data) => {
  if (props.type !== 'ticket') return ''
  
  return `
    <div class="mt-6 pt-4 border-t border-gray-200">
      <h3 class="font-semibold mb-2">Paiement</h3>
      <p><strong>Mode:</strong> ${getPaymentMethodLabel(data.paymentMethod)}</p>
      <p><strong>Montant payé:</strong> ${formatPrice(data.paidAmount || 0)}</p>
      ${data.remainingAmount ? `<p><strong>Reste à payer:</strong> ${formatPrice(data.remainingAmount)}</p>` : ''}
    </div>
  `
}

// Fonction generateFooterSync déjà déclarée plus haut

// Méthodes d'impression
const previewPrint = () => {
  const printWindow = window.open('', '_blank')
  printWindow.document.write(generatePrintHTML())
  printWindow.document.close()
}

const printDocument = () => {
  const printWindow = window.open('', '_blank')
  printWindow.document.write(generatePrintHTML())
  printWindow.document.close()
  
  printWindow.onload = () => {
    printWindow.print()
    printWindow.close()
    emit('printed', { type: props.type, options: printOptions.value })
  }
}

const generatePrintHTML = () => {
  console.log('PrintModal - generatePrintHTML appelé')
  console.log('PrintModal - Type:', props.type)
  console.log('PrintModal - Données:', props.data)
  console.log('PrintModal - Articles dans les données:', props.data?.items)

  // Utiliser les versions synchrones pour éviter les problèmes de Promise
  if (props.type === 'ticket') {
    return generateTicketContent()
  } else {
    return generateDevisContent()
  }
}

const generatePrintStyles = () => `
  @page {
    size: ${printOptions.value.format === 'a4' ? 'A4' : '80mm auto'};
    margin: ${printOptions.value.format === 'a4' ? '20mm' : '5mm'};
  }
  
  body {
    font-family: Arial, sans-serif;
    font-size: ${printOptions.value.format === 'a4' ? '12px' : '10px'};
    line-height: 1.4;
    color: #000;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    padding: 4px;
    text-align: left;
  }
  
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .font-bold { font-weight: bold; }
  .border-b { border-bottom: 1px solid #ccc; }
  .border-t { border-top: 1px solid #ccc; }
`

// Utilitaires
const formatItemDetails = (details) => {
  if (!details) return ''
  return Object.entries(details)
    .map(([key, value]) => `${key}: ${value}`)
    .join(' | ')
}

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'Espèces',
    card: 'Carte bancaire',
    check: 'Chèque',
    partial: 'Paiement partiel'
  }
  return labels[method] || method
}

const getValidityDate = () => {
  const date = new Date()
  date.setDate(date.getDate() + 30) // Valide 30 jours
  return date
}

// Méthodes manquantes pour le devis
const generateDetailedArticlesRows = (items) => {
  return items.map(item => `
    <tr class="border-b border-gray-100">
      <td class="py-3">
        <div class="font-medium">${item.description}</div>
        ${printOptions.value.showDetails && item.details ? `<div class="text-sm text-gray-500 mt-1">${formatItemDetails(item.details)}</div>` : ''}
      </td>
      <td class="text-center py-3">${item.quantity}</td>
      <td class="text-right py-3">${formatPrice(item.price)}</td>
      <td class="text-right py-3 font-medium">${formatPrice(item.price * item.quantity)}</td>
    </tr>
  `).join('')
}

const generateDetailedTotalsSection = (data) => {
  const subtotal = data.items?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0
  const tva = subtotal * 0.20 // 20% TVA
  const total = subtotal + tva

  return `
    <div class="border-t-2 border-gray-300 pt-4">
      <div class="flex justify-between items-center mb-2">
        <span>Sous-total HT:</span>
        <span>${formatPrice(subtotal)}</span>
      </div>
      <div class="flex justify-between items-center mb-2">
        <span>TVA (20%):</span>
        <span>${formatPrice(tva)}</span>
      </div>
      <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 pt-2">
        <span>TOTAL TTC:</span>
        <span>${formatPrice(total)}</span>
      </div>
    </div>
  `
}

const generateConditionsSection = () => `
  <div class="mt-8 pt-4 border-t border-gray-200">
    <h3 class="font-semibold mb-3">Conditions Générales</h3>
    <ul class="text-sm text-gray-600 space-y-1">
      <li>• Devis valable 30 jours à compter de la date d'émission</li>
      <li>• Prix susceptibles de modification selon disponibilité des produits</li>
      <li>• Acompte de 50% demandé à la commande</li>
      <li>• Délai de livraison : 5 à 10 jours ouvrés</li>
    </ul>
  </div>
`

const generateDevisFooter = () => `
  <div class="mt-8 pt-4 border-t border-gray-200">
    <div class="grid grid-cols-2 gap-8">
      <div>
        <h4 class="font-semibold mb-2">Signature Client</h4>
        <div class="border border-gray-300 h-16 rounded"></div>
        <p class="text-xs text-gray-500 mt-1">Bon pour accord</p>
      </div>
      <div>
        <h4 class="font-semibold mb-2">Cachet Magasin</h4>
        <div class="border border-gray-300 h-16 rounded"></div>
      </div>
    </div>

    <div class="text-center text-xs text-gray-500 mt-6">
      <p>Merci de votre confiance !</p>
      <p>MONOPTIC - Votre Opticien de Confiance</p>
    </div>
  </div>
`

// Fonction pour mettre à jour l'aperçu
const updatePreviewContent = () => {
  if (!props.data || !props.type) {
    previewContent.value = '<p>Aucune donnée à afficher</p>'
    return
  }

  try {
    if (props.type === 'ticket') {
      previewContent.value = generateTicketContent()
    } else {
      previewContent.value = generateDevisContent()
    }
  } catch (error) {
    console.error('Erreur génération aperçu:', error)
    previewContent.value = '<p>Erreur lors de la génération de l\'aperçu</p>'
  }
}

// Watchers
watch(() => props.data, updatePreviewContent, { immediate: true, deep: true })
watch(() => props.type, updatePreviewContent, { immediate: true })
watch(() => printOptions.value, updatePreviewContent, { deep: true })
</script>

<style scoped>
/* Styles pour l'aperçu */
#print-preview {
  transform-origin: top left;
  border: 1px solid #e5e7eb;
}

@media print {
  .no-print {
    display: none !important;
  }
}
</style>
