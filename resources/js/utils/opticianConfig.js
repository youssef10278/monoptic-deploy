/**
 * Configuration des informations de l'opticien
 * Ces informations apparaîtront sur tous les documents d'impression
 */

/**
 * Récupère la configuration de l'opticien depuis l'API ou le localStorage
 * @returns {Promise<Object>} Configuration de l'opticien
 */
export const getOpticianConfig = async () => {
  // D'abord, essayer de récupérer depuis le localStorage
  const cachedConfig = localStorage.getItem('opticianConfig')
  if (cachedConfig) {
    try {
      const config = JSON.parse(cachedConfig)
      return config
    } catch (error) {
      console.warn('Config locale corrompue:', error)
      localStorage.removeItem('opticianConfig') // Supprimer la config corrompue
    }
  }

  // Ensuite, essayer de récupérer depuis l'API
  try {
    const response = await fetch('/api/optician/config')
    if (response.ok) {
      const config = await response.json()
      // Sauvegarder en cache local
      localStorage.setItem('opticianConfig', JSON.stringify(config))
      return config
    }
  } catch (error) {
    console.warn('Impossible de récupérer la config depuis l\'API:', error)
  }

  // Configuration par défaut si rien n'est disponible
  const defaultConfig = getDefaultOpticianConfig()
  return defaultConfig
}

/**
 * Sauvegarde la configuration de l'opticien
 * @param {Object} config - Configuration à sauvegarder
 * @returns {Promise<boolean>} Succès de la sauvegarde
 */
export const saveOpticianConfig = async (config) => {
  try {
    // Toujours sauvegarder en local d'abord
    localStorage.setItem('opticianConfig', JSON.stringify(config))

    // Essayer de sauvegarder via l'API
    const response = await fetch('/api/optician/config', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify(config)
    })

    if (response.ok) {
      return true
    } else {
      return true // Retourner true car localStorage a fonctionné
    }
  } catch (error) {
    console.error('Erreur sauvegarde config:', error)

    // Vérifier que localStorage a bien fonctionné
    const saved = localStorage.getItem('opticianConfig')
    if (saved) {
      return true
    }
    return false
  }
}

/**
 * Configuration par défaut de l'opticien
 * @returns {Object} Configuration par défaut
 */
export const getDefaultOpticianConfig = () => ({
  // Informations de base
  name: 'Optique [Nom à définir]',
  slogan: 'Votre Opticien de Confiance',
  
  // Adresse
  address: {
    street: '[Adresse à définir]',
    city: '[Ville]',
    postalCode: '[Code postal]',
    country: 'Maroc'
  },
  
  // Contact
  contact: {
    phone: '[Téléphone à définir]',
    email: '[Email à définir]',
    website: '[Site web]'
  },
  
  // Informations légales
  legal: {
    rc: '[RC à définir]',
    patente: '[Patente à définir]',
    cnss: '[CNSS à définir]',
    ice: '[ICE à définir]'
  },
  
  // Logo
  logo: {
    url: null, // URL du logo uploadé
    showOnDocuments: true
  },
  
  // Préférences d'affichage
  display: {
    showSlogan: true,
    showWebsite: true,
    showLegalInfo: true,
    headerStyle: 'standard' // 'standard', 'compact', 'detailed'
  }
})

/**
 * Valide la configuration de l'opticien
 * @param {Object} config - Configuration à valider
 * @returns {Object} Résultat de validation {isValid, errors}
 */
export const validateOpticianConfig = (config) => {
  const errors = []
  
  // Vérifications obligatoires
  if (!config.name || config.name.includes('[') || config.name.includes('à définir')) {
    errors.push('Le nom de l\'optique est obligatoire')
  }
  
  if (!config.contact.phone || config.contact.phone.includes('[') || config.contact.phone.includes('à définir')) {
    errors.push('Le numéro de téléphone est obligatoire')
  }
  
  if (!config.address.street || config.address.street.includes('[') || config.address.street.includes('à définir')) {
    errors.push('L\'adresse est obligatoire')
  }
  
  if (!config.address.city || config.address.city.includes('[')) {
    errors.push('La ville est obligatoire')
  }
  
  // Vérifications format
  if (config.contact.email && !isValidEmail(config.contact.email)) {
    errors.push('Format d\'email invalide')
  }
  
  if (config.contact.phone && !isValidMoroccanPhone(config.contact.phone)) {
    errors.push('Format de téléphone marocain invalide')
  }
  
  return {
    isValid: errors.length === 0,
    errors
  }
}

/**
 * Vérifie si un email est valide
 * @param {string} email - Email à vérifier
 * @returns {boolean} True si valide
 */
const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Vérifie si un numéro de téléphone marocain est valide
 * @param {string} phone - Numéro à vérifier
 * @returns {boolean} True si valide
 */
const isValidMoroccanPhone = (phone) => {
  // Formats acceptés: +212XXXXXXXXX, 0XXXXXXXXX, 212XXXXXXXXX
  const phoneRegex = /^(\+212|212|0)[5-7][0-9]{8}$/
  return phoneRegex.test(phone.replace(/[\s-]/g, ''))
}

/**
 * Formate un numéro de téléphone marocain pour l'affichage
 * @param {string} phone - Numéro à formater
 * @returns {string} Numéro formaté
 */
export const formatMoroccanPhone = (phone) => {
  if (!phone) return ''
  
  // Nettoyer le numéro
  const cleaned = phone.replace(/[\s-+]/g, '')
  
  // Convertir au format +212
  let formatted = cleaned
  if (cleaned.startsWith('0')) {
    formatted = '212' + cleaned.substring(1)
  } else if (!cleaned.startsWith('212')) {
    formatted = '212' + cleaned
  }
  
  // Ajouter le +
  if (!formatted.startsWith('+')) {
    formatted = '+' + formatted
  }
  
  // Formater avec espaces: +212 6XX XX XX XX
  if (formatted.length === 13) {
    return formatted.replace(/(\+212)(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2$3 $4 $5 $6')
  }
  
  return formatted
}

/**
 * Génère un aperçu de l'en-tête avec la configuration
 * @param {Object} config - Configuration de l'opticien
 * @returns {string} HTML de l'aperçu
 */
export const generateHeaderPreview = (config) => {
  return `
    <div style="border: 1px solid #ddd; padding: 20px; background: white; font-family: Arial, sans-serif;">
      <div style="display: flex; align-items: center; margin-bottom: 15px;">
        ${config.logo.url ? `<img src="${config.logo.url}" style="width: 60px; height: 60px; margin-right: 20px; object-fit: contain;">` : ''}
        <div>
          <h1 style="margin: 0; font-size: 18px; font-weight: bold;">${config.name}</h1>
          ${config.display.showSlogan ? `<p style="margin: 5px 0; color: #666; font-size: 12px;">${config.slogan}</p>` : ''}
        </div>
      </div>
      
      <div style="display: flex; gap: 30px; font-size: 10px;">
        <div>
          <p style="margin: 2px 0;">${config.address.street}</p>
          <p style="margin: 2px 0;">${config.address.city} ${config.address.postalCode}</p>
          <p style="margin: 2px 0;">${config.address.country}</p>
        </div>
        
        <div>
          <p style="margin: 2px 0;">Tél: ${formatMoroccanPhone(config.contact.phone)}</p>
          <p style="margin: 2px 0;">Email: ${config.contact.email}</p>
          ${config.display.showWebsite && config.contact.website ? `<p style="margin: 2px 0;">Web: ${config.contact.website}</p>` : ''}
        </div>
      </div>
      
      ${config.display.showLegalInfo ? `
        <div style="margin-top: 10px; font-size: 9px; color: #666;">
          RC: ${config.legal.rc} | ICE: ${config.legal.ice}
        </div>
      ` : ''}
    </div>
  `
}

/**
 * Exporte la configuration pour utilisation dans les templates
 * @param {Object} config - Configuration de l'opticien
 * @returns {Object} Configuration formatée pour les templates
 */
export const formatConfigForTemplates = (config) => ({
  name: config.name,
  slogan: config.slogan,
  address: {
    street: config.address.street,
    city: config.address.city,
    postalCode: config.address.postalCode,
    country: config.address.country
  },
  contact: {
    phone: formatMoroccanPhone(config.contact.phone),
    email: config.contact.email,
    website: config.contact.website
  },
  legal: {
    rc: config.legal.rc,
    patente: config.legal.patente,
    cnss: config.legal.cnss,
    ice: config.legal.ice
  },
  logo: config.logo,
  display: config.display
})

/**
 * Initialise la configuration de l'opticien au démarrage de l'app
 * @returns {Promise<Object>} Configuration chargée
 */
export const initOpticianConfig = async () => {
  try {
    const config = await getOpticianConfig()
    
    // Vérifier si la configuration est complète
    const validation = validateOpticianConfig(config)
    if (!validation.isValid) {
      console.warn('Configuration opticien incomplète:', validation.errors)
      // Optionnel: afficher une notification pour compléter la config
    }
    
    return config
  } catch (error) {
    console.error('Erreur initialisation config opticien:', error)
    return getDefaultOpticianConfig()
  }
}
