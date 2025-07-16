/**
 * Templates d'impression pour le POS
 * Contient les templates pour tickets et devis
 */

import { formatPrice } from '../../utils/currency.js'
import { formatDate } from '../../utils/date.js'
import { getOpticianConfig, formatConfigForTemplates } from '../../utils/opticianConfig.js'

/**
 * Configuration de l'entreprise (sera remplacée par la config de l'opticien)
 */
let COMPANY_CONFIG = null

/**
 * Initialise la configuration de l'entreprise
 */
const initCompanyConfig = async () => {
  if (!COMPANY_CONFIG) {
    try {
      // Essayer de récupérer depuis le localStorage d'abord
      const cachedConfig = localStorage.getItem('opticianConfig')
      if (cachedConfig) {
        const parsedConfig = JSON.parse(cachedConfig)
        COMPANY_CONFIG = formatConfigForTemplates(parsedConfig)
        return COMPANY_CONFIG
      }

      // Sinon essayer l'API
      const opticianConfig = await getOpticianConfig()
      COMPANY_CONFIG = formatConfigForTemplates(opticianConfig)
    } catch (error) {
      console.warn('Utilisation de la configuration par défaut:', error)
      // Configuration de fallback améliorée
      COMPANY_CONFIG = {
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
          patente: 'PAT 789012',
          cnss: 'CNSS 345678',
          ice: 'ICE 001234567890123'
        },
        display: {
          showSlogan: true,
          showWebsite: true,
          showLegalInfo: true
        }
      }
    }
  }
  return COMPANY_CONFIG
}

/**
 * Template pour ticket de vente A4
 */
export const generateTicketA4Template = async (data, options = {}) => {
  const {
    showLogo = true,
    showDetails = true,
    showLegalInfo = true
  } = options

  // Debug: Afficher les données reçues
  console.log('generateTicketA4Template - Données reçues:', data)
  console.log('generateTicketA4Template - Articles:', data.items)

  // Vérifier et préparer les données
  const safeData = {
    id: data.id || 'NOUVEAU',
    date: data.date || new Date(),
    vendeur: data.vendeur || 'Système',
    client: data.client || null,
    items: Array.isArray(data.items) ? data.items : [],
    paymentMethod: data.paymentMethod || 'cash',
    paidAmount: typeof data.paidAmount === 'number' ? data.paidAmount : 0,
    remainingAmount: typeof data.remainingAmount === 'number' ? data.remainingAmount : 0,
    totalAmount: typeof data.totalAmount === 'number' ? data.totalAmount : 0
  }

  console.log('generateTicketA4Template - Données sécurisées:', safeData)

  // Utiliser les options d'impression si disponibles
  const useShowLogo = options.showLogo !== undefined ? options.showLogo : showLogo
  const useShowDetails = options.showDetails !== undefined ? options.showDetails : showDetails
  const useShowLegalInfo = options.showLegalInfo !== undefined ? options.showLegalInfo : showLegalInfo

  // Générer l'en-tête de l'entreprise
  const companyHeader = await generateCompanyHeader()

  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Ticket de Vente #${safeData.id}</title>
      <style>
        ${getTicketStyles()}
      </style>
    </head>
    <body>
      <div class="ticket-container">
        ${useShowLogo ? companyHeader : ''}

        <!-- Titre -->
        <div class="ticket-title">
          <h1>TICKET DE VENTE</h1>
          <div class="ticket-number">#${safeData.id}</div>
        </div>

        <!-- Informations vente -->
        <div class="sale-info">
          <div class="info-row">
            <div class="info-group">
              <strong>Date:</strong> ${formatDate(safeData.date)}
            </div>
            <div class="info-group">
              <strong>Heure:</strong> ${formatDate(safeData.date, { includeTime: true })}
            </div>
          </div>
          <div class="info-row">
            <div class="info-group">
              <strong>Vendeur:</strong> ${safeData.vendeur}
            </div>
            <div class="info-group">
              <strong>Caisse:</strong> POS-01
            </div>
          </div>
        </div>

        <!-- Informations client -->
        <div class="client-info">
          <h3>Client</h3>
          <div class="client-details">
            <div><strong>Nom:</strong> ${safeData.client?.name || 'Client de passage'}</div>
            ${safeData.client?.phone ? `<div><strong>Téléphone:</strong> ${safeData.client.phone}</div>` : ''}
            ${safeData.client?.email ? `<div><strong>Email:</strong> ${safeData.client.email}</div>` : ''}
          </div>
        </div>

        <!-- Articles -->
        <div class="items-section">
          <table class="items-table">
            <thead>
              <tr>
                <th>Article</th>
                <th>Qté</th>
                <th>P.U.</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              ${generateItemsRows(safeData.items, useShowDetails)}
            </tbody>
          </table>
        </div>

        <!-- Totaux -->
        <div class="totals-section">
          ${generateTotalsTable(safeData)}
        </div>

        <!-- Paiement -->
        <div class="payment-section">
          <h3>Paiement</h3>
          <div class="payment-details">
            <div class="payment-row">
              <span>Mode de paiement:</span>
              <span>${getPaymentMethodLabel(safeData.paymentMethod)}</span>
            </div>
            <div class="payment-row">
              <span>Montant payé:</span>
              <span class="amount">${formatPrice(safeData.paidAmount)}</span>
            </div>
            ${safeData.remainingAmount > 0 ? `
              <div class="payment-row highlight">
                <span>Reste à payer:</span>
                <span class="amount">${formatPrice(safeData.remainingAmount)}</span>
              </div>
            ` : ''}
          </div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
          <div class="thank-you">
            <p>Merci de votre confiance !</p>
          </div>

          ${useShowLegalInfo ? `
            <div class="legal-info">
              <p>RC: ${(await initCompanyConfig()).legal.rc} | ICE: ${(await initCompanyConfig()).legal.ice}</p>
            </div>
          ` : ''}

          <div class="print-info">
            <p>Imprimé le ${formatDate(new Date(), { includeTime: true })}</p>
          </div>
        </div>
      </div>
    </body>
    </html>
  `
}

/**
 * Template pour devis A4
 */
export const generateDevisA4Template = async (data, options = {}) => {
  const {
    showLogo = true,
    showDetails = true,
    showConditions = true,
    validityDays = 30
  } = options

  // Debug: Afficher les données reçues
  console.log('generateDevisA4Template - Données reçues:', data)
  console.log('generateDevisA4Template - Articles:', data.items)

  // Vérifier et préparer les données
  const safeData = {
    id: data.id || 'NOUVEAU',
    date: data.date || new Date(),
    vendeur: data.vendeur || 'Système',
    client: data.client || null,
    items: Array.isArray(data.items) ? data.items : [],
    totalAmount: typeof data.totalAmount === 'number' ? data.totalAmount : 0
  }

  console.log('generateDevisA4Template - Données sécurisées:', safeData)

  // Utiliser les options d'impression si disponibles
  const useShowLogo = options.showLogo !== undefined ? options.showLogo : showLogo
  const useShowDetails = options.showDetails !== undefined ? options.showDetails : showDetails
  const useShowConditions = options.showConditions !== undefined ? options.showConditions : showConditions
  const useValidityDays = options.validityDays !== undefined ? options.validityDays : validityDays

  const validityDate = new Date()
  validityDate.setDate(validityDate.getDate() + useValidityDays)

  // Générer l'en-tête de l'entreprise
  const companyHeader = await generateCompanyHeader()

  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Devis #DEV-${safeData.id}</title>
      <style>
        ${getDevisStyles()}
      </style>
    </head>
    <body>
      <div class="devis-container">
        ${useShowLogo ? companyHeader : ''}

        <!-- Titre -->
        <div class="devis-title">
          <h1>DEVIS</h1>
          <div class="devis-number">N° DEV-${safeData.id}</div>
        </div>

        <!-- Informations -->
        <div class="devis-info">
          <div class="info-section">
            <h3>Informations Devis</h3>
            <div class="info-details">
              <div><strong>Date d'émission:</strong> ${formatDate(safeData.date)}</div>
              <div><strong>Valable jusqu'au:</strong> ${formatDate(validityDate)}</div>
              <div><strong>Vendeur:</strong> ${safeData.vendeur}</div>
              <div><strong>Référence:</strong> DEV-${safeData.id}-${new Date().getFullYear()}</div>
            </div>
          </div>

          <div class="info-section">
            <h3>Client</h3>
            <div class="info-details">
              <div><strong>Nom:</strong> ${safeData.client?.name || 'À définir'}</div>
              ${safeData.client?.phone ? `<div><strong>Téléphone:</strong> ${safeData.client.phone}</div>` : ''}
              ${safeData.client?.email ? `<div><strong>Email:</strong> ${safeData.client.email}</div>` : ''}
              ${safeData.client?.address ? `<div><strong>Adresse:</strong> ${safeData.client.address}</div>` : ''}
            </div>
          </div>
        </div>

        <!-- Articles détaillés -->
        <div class="items-section">
          <h3>Détail des Articles</h3>
          <table class="devis-table">
            <thead>
              <tr>
                <th class="item-desc">Désignation</th>
                <th class="item-qty">Qté</th>
                <th class="item-price">Prix Unitaire HT</th>
                <th class="item-total">Total HT</th>
              </tr>
            </thead>
            <tbody>
              ${generateDetailedItemsRows(safeData.items, useShowDetails)}
            </tbody>
          </table>
        </div>

        <!-- Totaux détaillés -->
        <div class="totals-section">
          ${generateDetailedTotalsTable(safeData)}
        </div>

        ${useShowConditions ? generateConditionsSection() : ''}

        <!-- Signatures -->
        <div class="signatures-section">
          <div class="signature-box">
            <h4>Signature Client</h4>
            <div class="signature-area"></div>
            <p class="signature-label">Bon pour accord</p>
          </div>

          <div class="signature-box">
            <h4>Cachet Magasin</h4>
            <div class="signature-area"></div>
            <p class="signature-label">${(await initCompanyConfig()).name}</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="devis-footer">
          <div class="footer-content">
            <p><strong>${(await initCompanyConfig()).name}</strong> - ${(await initCompanyConfig()).slogan}</p>
            <p>${(await initCompanyConfig()).contact.phone} | ${(await initCompanyConfig()).contact.email}</p>
          </div>
        </div>
      </div>
    </body>
    </html>
  `
}

/**
 * Génère l'en-tête de l'entreprise avec la configuration de l'opticien
 */
const generateCompanyHeader = async () => {
  const config = await initCompanyConfig()

  return `
    <div class="company-header">
      <div class="company-logo">
        ${config.logo?.url ? `
          <img src="${config.logo.url}" alt="Logo ${config.name}" class="company-logo-img">
        ` : `
          <div class="logo-placeholder">
            <span class="logo-text">${config.name.substring(0, 3).toUpperCase()}</span>
          </div>
        `}
      </div>

      <div class="company-info">
        <h1 class="company-name">${config.name}</h1>
        ${config.display?.showSlogan ? `<p class="company-slogan">${config.slogan}</p>` : ''}

        <div class="company-details">
          <div class="address">
            <p>${config.address.street}</p>
            <p>${config.address.city} ${config.address.postalCode}</p>
            <p>${config.address.country}</p>
          </div>

          <div class="contact">
            <p>Tél: ${config.contact.phone}</p>
            <p>Email: ${config.contact.email}</p>
            ${config.display?.showWebsite && config.contact.website ? `<p>Web: ${config.contact.website}</p>` : ''}
          </div>
        </div>
      </div>
    </div>
  `
}

/**
 * Génère les lignes d'articles pour ticket
 */
const generateItemsRows = (items, showDetails) => {
  // Vérifier si items existe et est un tableau
  if (!items || !Array.isArray(items) || items.length === 0) {
    console.warn('Aucun article à afficher dans le ticket')
    return `
      <tr>
        <td colspan="4" class="text-center py-4 text-gray-500">
          Aucun article à afficher
        </td>
      </tr>
    `
  }

  return items.map(item => `
    <tr>
      <td class="item-description">
        <div class="item-name">${item.description || 'Article sans nom'}</div>
        ${showDetails && item.details ? `<div class="item-details">${formatItemDetails(item.details)}</div>` : ''}
      </td>
      <td class="item-quantity">${item.quantity || 1}</td>
      <td class="item-price">${formatPrice(item.price || 0)}</td>
      <td class="item-total">${formatPrice((item.price || 0) * (item.quantity || 1))}</td>
    </tr>
  `).join('')
}

/**
 * Génère les lignes d'articles détaillées pour devis
 */
const generateDetailedItemsRows = (items, showDetails) => {
  // Vérifier si items existe et est un tableau
  if (!items || !Array.isArray(items) || items.length === 0) {
    console.warn('Aucun article à afficher dans le devis')
    return `
      <tr>
        <td colspan="4" class="text-center py-4 text-gray-500">
          Aucun article à afficher
        </td>
      </tr>
    `
  }

  return items.map(item => `
    <tr>
      <td class="item-description">
        <div class="item-name">${item.description || 'Article sans nom'}</div>
        ${showDetails && item.details ? `<div class="item-details">${formatItemDetails(item.details)}</div>` : ''}
        ${item.warranty ? `<div class="item-warranty">Garantie: ${item.warranty}</div>` : ''}
      </td>
      <td class="item-quantity">${item.quantity || 1}</td>
      <td class="item-price">${formatPrice(item.price || 0)}</td>
      <td class="item-total">${formatPrice((item.price || 0) * (item.quantity || 1))}</td>
    </tr>
  `).join('')
}

/**
 * Génère le tableau des totaux simple
 */
const generateTotalsTable = (data) => {
  // Utiliser totalAmount s'il est fourni, sinon calculer
  let total = data.totalAmount

  if (total === undefined || total === null) {
    // Calcul de secours si totalAmount n'est pas disponible
    total = data.items?.reduce((sum, item) => {
      const price = item.price || 0
      const quantity = item.quantity || 1
      return sum + (price * quantity)
    }, 0) || 0
  }

  return `
    <table class="totals-table">
      <tr class="total-row">
        <td class="total-label">TOTAL:</td>
        <td class="total-amount">${formatPrice(total)}</td>
      </tr>
    </table>
  `
}

/**
 * Génère le tableau des totaux détaillé avec TVA
 */
const generateDetailedTotalsTable = (data) => {
  // Utiliser totalAmount s'il est fourni, sinon calculer
  let subtotal

  if (data.totalAmount !== undefined && data.totalAmount !== null) {
    // Si on a un total, on considère que c'est le TTC et on calcule le HT
    subtotal = data.totalAmount / 1.20 // Enlever la TVA de 20%
  } else {
    // Calcul de secours si totalAmount n'est pas disponible
    subtotal = data.items?.reduce((sum, item) => {
      const price = item.price || 0
      const quantity = item.quantity || 1
      return sum + (price * quantity)
    }, 0) || 0
  }

  const tva = subtotal * 0.20 // 20% TVA
  const total = subtotal + tva

  return `
    <table class="totals-table detailed">
      <tr>
        <td class="total-label">Sous-total HT:</td>
        <td class="total-amount">${formatPrice(subtotal)}</td>
      </tr>
      <tr>
        <td class="total-label">TVA (20%):</td>
        <td class="total-amount">${formatPrice(tva)}</td>
      </tr>
      <tr class="total-final">
        <td class="total-label">TOTAL TTC:</td>
        <td class="total-amount">${formatPrice(total)}</td>
      </tr>
    </table>
  `
}

/**
 * Génère la section des conditions
 */
const generateConditionsSection = () => `
  <div class="conditions-section">
    <h3>Conditions Générales</h3>
    <ul class="conditions-list">
      <li>Devis valable 30 jours à compter de la date d'émission</li>
      <li>Prix susceptibles de modification selon disponibilité des produits</li>
      <li>Acompte de 50% demandé à la commande</li>
      <li>Délai de livraison : 5 à 10 jours ouvrés selon les produits</li>
      <li>Paiement par espèces, chèque ou carte bancaire</li>
    </ul>
  </div>
`

/**
 * Utilitaires
 */
const formatItemDetails = (details) => {
  if (!details) return ''

  // Si c'est une chaîne, la retourner directement
  if (typeof details === 'string') return details

  // Si c'est un objet, le formater
  if (typeof details === 'object') {
    return Object.entries(details)
      .filter(([key, value]) => value && value !== '')
      .map(([key, value]) => `${key}: ${value}`)
      .join(' | ')
  }

  // Fallback
  return String(details)
}

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'Espèces',
    card: 'Carte bancaire',
    check: 'Chèque',
    partial: 'Paiement partiel',
    complete: 'Paiement complet'
  }
  return labels[method] || method
}

/**
 * Styles CSS pour les tickets
 */
const getTicketStyles = () => `
  @page {
    size: A4;
    margin: 15mm;
  }
  
  body {
    font-family: 'Arial', sans-serif;
    font-size: 12px;
    line-height: 1.4;
    color: #000;
    margin: 0;
    padding: 0;
  }
  
  .ticket-container {
    max-width: 100%;
    margin: 0 auto;
  }
  
  .company-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #333;
  }
  
  .company-logo {
    margin-right: 20px;
  }
  
  .logo-placeholder {
    width: 60px;
    height: 60px;
    background: #f0f0f0;
    border: 2px solid #333;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 10px;
  }
  
  .company-name {
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 5px 0;
  }
  
  .company-slogan {
    font-size: 12px;
    color: #666;
    margin: 0 0 10px 0;
  }
  
  .company-details {
    display: flex;
    gap: 30px;
    font-size: 10px;
  }
  
  .ticket-title {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .ticket-title h1 {
    font-size: 20px;
    font-weight: bold;
    margin: 0 0 5px 0;
  }
  
  .ticket-number {
    font-size: 14px;
    color: #666;
  }
  
  .sale-info, .client-info, .payment-section {
    margin-bottom: 20px;
    padding: 10px;
    background: #f9f9f9;
    border: 1px solid #ddd;
  }
  
  .info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
  }
  
  .items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  
  .items-table th,
  .items-table td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  .items-table th {
    background: #f0f0f0;
    font-weight: bold;
  }
  
  .item-quantity,
  .item-price,
  .item-total {
    text-align: right;
  }
  
  .item-details {
    font-size: 10px;
    color: #666;
    margin-top: 2px;
  }
  
  .totals-table {
    width: 100%;
    margin-top: 20px;
  }
  
  .total-row td {
    padding: 10px;
    font-size: 16px;
    font-weight: bold;
    text-align: right;
    border-top: 2px solid #333;
  }
  
  .payment-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  
  .payment-row {
    display: flex;
    justify-content: space-between;
  }
  
  .payment-row.highlight {
    font-weight: bold;
    color: #d32f2f;
  }
  
  .ticket-footer {
    margin-top: 30px;
    text-align: center;
    font-size: 10px;
  }
  
  .thank-you {
    margin-bottom: 15px;
  }
  
  .legal-info {
    margin-bottom: 10px;
    color: #666;
  }
  
  .print-info {
    color: #999;
  }
`

/**
 * Styles CSS pour les devis
 */
const getDevisStyles = () => `
  @page {
    size: A4;
    margin: 20mm;
  }
  
  body {
    font-family: 'Arial', sans-serif;
    font-size: 11px;
    line-height: 1.5;
    color: #000;
    margin: 0;
    padding: 0;
  }
  
  .devis-container {
    max-width: 100%;
    margin: 0 auto;
  }
  
  .company-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #333;
  }
  
  .devis-title {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .devis-title h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 10px 0;
    color: #333;
  }
  
  .devis-number {
    font-size: 16px;
    color: #666;
  }
  
  .devis-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
  }
  
  .info-section {
    flex: 1;
    margin-right: 30px;
  }
  
  .info-section:last-child {
    margin-right: 0;
  }
  
  .info-section h3 {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
  }
  
  .info-details div {
    margin-bottom: 5px;
  }
  
  .devis-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  
  .devis-table th,
  .devis-table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
  }
  
  .devis-table th {
    background: #f5f5f5;
    font-weight: bold;
  }
  
  .item-qty,
  .item-price,
  .item-total {
    text-align: right;
    width: 80px;
  }
  
  .item-desc {
    width: auto;
  }
  
  .totals-table.detailed {
    width: 300px;
    margin-left: auto;
    border-collapse: collapse;
  }
  
  .totals-table.detailed td {
    padding: 8px 15px;
    border: 1px solid #ddd;
  }
  
  .total-final {
    font-weight: bold;
    font-size: 14px;
    background: #f0f0f0;
  }
  
  .conditions-section {
    margin: 30px 0;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
  }
  
  .conditions-section h3 {
    margin-bottom: 10px;
    font-size: 14px;
  }
  
  .conditions-list {
    margin: 0;
    padding-left: 20px;
  }
  
  .conditions-list li {
    margin-bottom: 5px;
  }
  
  .signatures-section {
    display: flex;
    justify-content: space-between;
    margin: 40px 0;
  }
  
  .signature-box {
    width: 45%;
    text-align: center;
  }
  
  .signature-box h4 {
    margin-bottom: 10px;
    font-size: 12px;
  }
  
  .signature-area {
    height: 60px;
    border: 1px solid #333;
    margin-bottom: 5px;
  }
  
  .signature-label {
    font-size: 10px;
    color: #666;
  }
  
  .devis-footer {
    margin-top: 30px;
    text-align: center;
    font-size: 10px;
    color: #666;
    border-top: 1px solid #ddd;
    padding-top: 15px;
  }
`
