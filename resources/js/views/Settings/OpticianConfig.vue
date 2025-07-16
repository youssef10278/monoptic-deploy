<template>
  <div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Configuration de l'Optique</h1>
      <p class="text-gray-600 mt-2">
        Personnalisez les informations qui apparaîtront sur vos documents d'impression (tickets, devis, etc.)
      </p>
    </div>

    <!-- Formulaire de configuration -->
    <form @submit.prevent="saveConfiguration" class="space-y-8">
      
      <!-- Informations de base -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de Base</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nom de l'optique *
            </label>
            <input
              v-model="config.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: Optique Vision Plus"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Slogan
            </label>
            <input
              v-model="config.slogan"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: Votre Opticien de Confiance"
            >
          </div>
        </div>
      </div>

      <!-- Adresse -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresse</h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Adresse *
            </label>
            <input
              v-model="config.address.street"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: 123 Avenue Mohammed V"
            >
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Ville *
              </label>
              <input
                v-model="config.address.city"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ex: Casablanca"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Code postal
              </label>
              <input
                v-model="config.address.postalCode"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ex: 20000"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Pays
              </label>
              <input
                v-model="config.address.country"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="Maroc"
                readonly
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Contact -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Téléphone *
            </label>
            <input
              v-model="config.contact.phone"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: +212 6XX XX XX XX"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="config.contact.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: contact@monoptique.ma"
            >
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Site web
            </label>
            <input
              v-model="config.contact.website"
              type="url"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: www.monoptique.ma"
            >
          </div>
        </div>
      </div>

      <!-- Informations légales -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations Légales</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Registre de Commerce (RC)
            </label>
            <input
              v-model="config.legal.rc"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: RC 123456"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Identifiant Commun de l'Entreprise (ICE)
            </label>
            <input
              v-model="config.legal.ice"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: ICE 001234567890123"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Patente
            </label>
            <input
              v-model="config.legal.patente"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: PAT 789012"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              CNSS
            </label>
            <input
              v-model="config.legal.cnss"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Ex: CNSS 345678"
            >
          </div>
        </div>
      </div>

      <!-- Options d'affichage -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Options d'Affichage</h2>
        
        <div class="space-y-4">
          <label class="flex items-center">
            <input
              v-model="config.display.showSlogan"
              type="checkbox"
              class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <span class="text-sm text-gray-700">Afficher le slogan sur les documents</span>
          </label>
          
          <label class="flex items-center">
            <input
              v-model="config.display.showWebsite"
              type="checkbox"
              class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <span class="text-sm text-gray-700">Afficher le site web sur les documents</span>
          </label>
          
          <label class="flex items-center">
            <input
              v-model="config.display.showLegalInfo"
              type="checkbox"
              class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <span class="text-sm text-gray-700">Afficher les informations légales (RC, ICE) sur les documents</span>
          </label>
        </div>
      </div>

      <!-- Aperçu -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aperçu de l'En-tête</h2>
        <div v-html="headerPreview" class="border border-gray-200 rounded-md"></div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end space-x-4">
        <button
          type="button"
          @click="resetToDefault"
          class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
        >
          Réinitialiser
        </button>

        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 rounded-md transition-colors"
        >
          {{ saving ? 'Sauvegarde...' : 'Sauvegarder' }}
        </button>
      </div>
    </form>

    <!-- Messages -->
    <div v-if="message" class="mt-4 p-4 rounded-md" :class="messageClass">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { 
  getOpticianConfig, 
  saveOpticianConfig, 
  getDefaultOpticianConfig, 
  validateOpticianConfig,
  generateHeaderPreview
} from '../../utils/opticianConfig.js'

// État du composant
const config = ref(getDefaultOpticianConfig())
const saving = ref(false)
const message = ref('')
const messageType = ref('success')

// Computed
const messageClass = computed(() => ({
  'bg-green-100 text-green-800 border border-green-200': messageType.value === 'success',
  'bg-red-100 text-red-800 border border-red-200': messageType.value === 'error',
  'bg-yellow-100 text-yellow-800 border border-yellow-200': messageType.value === 'warning'
}))

const headerPreview = computed(() => {
  return generateHeaderPreview(config.value)
})

// Méthodes
const loadConfiguration = async () => {
  try {
    const loadedConfig = await getOpticianConfig()
    config.value = loadedConfig
    showMessage('Configuration chargée avec succès', 'success')
  } catch (error) {
    console.error('Erreur chargement configuration:', error)
    showMessage('Erreur lors du chargement de la configuration', 'error')
  }
}

const saveConfiguration = async () => {
  // Validation
  const validation = validateOpticianConfig(config.value)
  if (!validation.isValid) {
    showMessage(`Erreurs de validation: ${validation.errors.join(', ')}`, 'error')
    return
  }

  saving.value = true
  try {
    const success = await saveOpticianConfig(config.value)

    if (success) {
      showMessage('Configuration sauvegardée avec succès !', 'success')
    } else {
      showMessage('Erreur lors de la sauvegarde', 'error')
    }
  } catch (error) {
    console.error('Erreur sauvegarde:', error)
    showMessage('Erreur lors de la sauvegarde: ' + error.message, 'error')
  } finally {
    saving.value = false
  }
}

const resetToDefault = () => {
  if (confirm('Êtes-vous sûr de vouloir réinitialiser la configuration ?')) {
    config.value = getDefaultOpticianConfig()
    showMessage('Configuration réinitialisée', 'warning')
  }
}



const showMessage = (text, type = 'success') => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

// Lifecycle
onMounted(() => {
  loadConfiguration()
})
</script>
