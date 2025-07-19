/**
 * Utilitaires pour le formatage des devises
 * Application MonOpti - Maroc
 */

/**
 * Formate un prix en Dirham marocain (MAD)
 * @param {number} price - Le prix à formater
 * @param {object} options - Options de formatage
 * @returns {string} - Prix formaté en MAD
 */
export const formatPrice = (price, options = {}) => {
  if (!price && price !== 0) return '0,00 MAD'
  
  const defaultOptions = {
    style: 'currency',
    currency: 'MAD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
    ...options
  }

  try {
    // Utiliser la locale française pour le formatage (virgule comme séparateur décimal)
    return new Intl.NumberFormat('fr-MA', defaultOptions).format(price)
  } catch (error) {
    // Fallback si la locale fr-MA n'est pas supportée
    return new Intl.NumberFormat('fr-FR', defaultOptions).format(price).replace('€', 'MAD')
  }
}

/**
 * Formate un prix en MAD sans décimales
 * @param {number} price - Le prix à formater
 * @returns {string} - Prix formaté en MAD sans décimales
 */
export const formatPriceInteger = (price) => {
  return formatPrice(price, {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}

/**
 * Formate un prix en MAD avec symbole personnalisé
 * @param {number} price - Le prix à formater
 * @param {string} symbol - Symbole à utiliser (par défaut 'DH')
 * @returns {string} - Prix formaté avec symbole personnalisé
 */
export const formatPriceWithSymbol = (price, symbol = 'DH') => {
  if (!price && price !== 0) return `0,00 ${symbol}`
  
  const formatted = new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
  
  return `${formatted} ${symbol}`
}

/**
 * Parse un prix depuis une chaîne formatée
 * @param {string} priceString - Chaîne de prix formatée
 * @returns {number} - Prix en nombre
 */
export const parsePrice = (priceString) => {
  if (!priceString) return 0
  
  // Supprimer les symboles de devise et espaces
  const cleaned = priceString
    .replace(/MAD|DH|€/g, '')
    .replace(/\s/g, '')
    .replace(',', '.')
  
  return parseFloat(cleaned) || 0
}

/**
 * Valide qu'un prix est valide
 * @param {number} price - Prix à valider
 * @returns {boolean} - True si le prix est valide
 */
export const isValidPrice = (price) => {
  return typeof price === 'number' && !isNaN(price) && price >= 0
}

/**
 * Configuration par défaut pour les devises
 */
export const CURRENCY_CONFIG = {
  code: 'MAD',
  symbol: 'DH',
  name: 'Dirham marocain',
  locale: 'fr-MA',
  fallbackLocale: 'fr-FR'
}
