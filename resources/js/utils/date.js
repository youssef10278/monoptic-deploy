/**
 * Utilitaires pour le formatage des dates
 */

/**
 * Formate une date selon le format français
 * @param {Date|string} date - Date à formater
 * @param {object} options - Options de formatage
 * @returns {string} Date formatée
 */
export const formatDate = (date, options = {}) => {
  const {
    locale = 'fr-FR',
    dateStyle = 'short',
    timeStyle = null,
    includeTime = false
  } = options

  if (!date) return ''

  try {
    const dateObj = typeof date === 'string' ? new Date(date) : date
    
    if (isNaN(dateObj.getTime())) {
      return ''
    }

    const formatOptions = {
      dateStyle
    }

    if (includeTime || timeStyle) {
      formatOptions.timeStyle = timeStyle || 'short'
    }

    return new Intl.DateTimeFormat(locale, formatOptions).format(dateObj)
  } catch (error) {
    console.error('Erreur formatage date:', error)
    return ''
  }
}

/**
 * Formate une date pour l'affichage long
 * @param {Date|string} date - Date à formater
 * @returns {string} Date formatée (ex: "15 janvier 2024")
 */
export const formatDateLong = (date) => {
  return formatDate(date, {
    locale: 'fr-FR',
    dateStyle: 'long'
  })
}

/**
 * Formate une date avec l'heure
 * @param {Date|string} date - Date à formater
 * @returns {string} Date et heure formatées
 */
export const formatDateTime = (date) => {
  return formatDate(date, {
    locale: 'fr-FR',
    dateStyle: 'short',
    timeStyle: 'short'
  })
}

/**
 * Formate une date pour les inputs HTML
 * @param {Date|string} date - Date à formater
 * @returns {string} Date au format YYYY-MM-DD
 */
export const formatDateForInput = (date) => {
  if (!date) return ''
  
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : date
    
    if (isNaN(dateObj.getTime())) {
      return ''
    }

    return dateObj.toISOString().split('T')[0]
  } catch (error) {
    return ''
  }
}

/**
 * Calcule l'âge à partir d'une date de naissance
 * @param {Date|string} birthDate - Date de naissance
 * @returns {number} Âge en années
 */
export const calculateAge = (birthDate) => {
  if (!birthDate) return 0
  
  try {
    const birth = typeof birthDate === 'string' ? new Date(birthDate) : birthDate
    const today = new Date()
    
    let age = today.getFullYear() - birth.getFullYear()
    const monthDiff = today.getMonth() - birth.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--
    }
    
    return age
  } catch (error) {
    return 0
  }
}

/**
 * Vérifie si une date est dans le passé
 * @param {Date|string} date - Date à vérifier
 * @returns {boolean} True si la date est passée
 */
export const isPastDate = (date) => {
  if (!date) return false
  
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : date
    return dateObj < new Date()
  } catch (error) {
    return false
  }
}

/**
 * Vérifie si une date est dans le futur
 * @param {Date|string} date - Date à vérifier
 * @returns {boolean} True si la date est future
 */
export const isFutureDate = (date) => {
  if (!date) return false
  
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : date
    return dateObj > new Date()
  } catch (error) {
    return false
  }
}

/**
 * Ajoute des jours à une date
 * @param {Date|string} date - Date de base
 * @param {number} days - Nombre de jours à ajouter
 * @returns {Date} Nouvelle date
 */
export const addDays = (date, days) => {
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : new Date(date)
    dateObj.setDate(dateObj.getDate() + days)
    return dateObj
  } catch (error) {
    return new Date()
  }
}

/**
 * Calcule la différence en jours entre deux dates
 * @param {Date|string} date1 - Première date
 * @param {Date|string} date2 - Deuxième date
 * @returns {number} Différence en jours
 */
export const daysDifference = (date1, date2) => {
  try {
    const d1 = typeof date1 === 'string' ? new Date(date1) : date1
    const d2 = typeof date2 === 'string' ? new Date(date2) : date2
    
    const timeDiff = Math.abs(d2.getTime() - d1.getTime())
    return Math.ceil(timeDiff / (1000 * 3600 * 24))
  } catch (error) {
    return 0
  }
}

/**
 * Formate une durée en texte lisible
 * @param {number} minutes - Durée en minutes
 * @returns {string} Durée formatée
 */
export const formatDuration = (minutes) => {
  if (!minutes || minutes < 0) return '0 min'
  
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  
  if (hours === 0) {
    return `${mins} min`
  } else if (mins === 0) {
    return `${hours}h`
  } else {
    return `${hours}h ${mins}min`
  }
}

/**
 * Obtient le début de la journée
 * @param {Date|string} date - Date de référence
 * @returns {Date} Début de journée (00:00:00)
 */
export const startOfDay = (date = new Date()) => {
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : new Date(date)
    dateObj.setHours(0, 0, 0, 0)
    return dateObj
  } catch (error) {
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    return today
  }
}

/**
 * Obtient la fin de la journée
 * @param {Date|string} date - Date de référence
 * @returns {Date} Fin de journée (23:59:59)
 */
export const endOfDay = (date = new Date()) => {
  try {
    const dateObj = typeof date === 'string' ? new Date(date) : new Date(date)
    dateObj.setHours(23, 59, 59, 999)
    return dateObj
  } catch (error) {
    const today = new Date()
    today.setHours(23, 59, 59, 999)
    return today
  }
}

/**
 * Vérifie si deux dates sont le même jour
 * @param {Date|string} date1 - Première date
 * @param {Date|string} date2 - Deuxième date
 * @returns {boolean} True si même jour
 */
export const isSameDay = (date1, date2) => {
  try {
    const d1 = typeof date1 === 'string' ? new Date(date1) : date1
    const d2 = typeof date2 === 'string' ? new Date(date2) : date2
    
    return d1.getFullYear() === d2.getFullYear() &&
           d1.getMonth() === d2.getMonth() &&
           d1.getDate() === d2.getDate()
  } catch (error) {
    return false
  }
}

/**
 * Obtient le nom du mois en français
 * @param {number} monthIndex - Index du mois (0-11)
 * @returns {string} Nom du mois
 */
export const getMonthName = (monthIndex) => {
  const months = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
  ]
  
  return months[monthIndex] || ''
}

/**
 * Obtient le nom du jour en français
 * @param {number} dayIndex - Index du jour (0-6, 0 = dimanche)
 * @returns {string} Nom du jour
 */
export const getDayName = (dayIndex) => {
  const days = [
    'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 
    'Jeudi', 'Vendredi', 'Samedi'
  ]
  
  return days[dayIndex] || ''
}
