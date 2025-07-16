<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- En-tête -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Changer le statut de la vente #{{ sale?.id }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Statut actuel : <span class="font-medium" :class="getStatusTextColor(sale?.status)">{{ getStatusLabel(sale?.status) }}</span>
                </p>
              </div>
            </div>
          </div>

          <!-- Formulaire -->
          <form @submit.prevent="handleSubmit" class="mt-6">
            <!-- Sélection du nouveau statut -->
            <div class="mb-4">
              <label for="new-status" class="block text-sm font-medium text-gray-700 mb-2">
                Nouveau statut
              </label>
              <select
                id="new-status"
                v-model="form.newStatus"
                required
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Sélectionner un statut</option>
                <option 
                  v-for="status in availableStatuses" 
                  :key="status.value" 
                  :value="status.value"
                  :disabled="status.value === sale?.status"
                >
                  {{ status.label }}
                </option>
              </select>
            </div>

            <!-- Commentaire optionnel -->
            <div class="mb-4">
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                Commentaire (optionnel)
              </label>
              <textarea
                id="comment"
                v-model="form.comment"
                rows="3"
                placeholder="Ajouter un commentaire sur ce changement de statut..."
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
            </div>

            <!-- Informations sur le nouveau statut -->
            <div v-if="form.newStatus" class="mb-4 p-3 bg-blue-50 rounded-lg">
              <h4 class="text-sm font-medium text-blue-900 mb-1">
                {{ getStatusLabel(form.newStatus) }}
              </h4>
              <p class="text-sm text-blue-700">
                {{ getStatusDescription(form.newStatus) }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="!form.newStatus || loading"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="loading" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Mise à jour...
                </span>
                <span v-else>Changer le statut</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  sale: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'update-status'])

// État réactif
const form = ref({
  newStatus: '',
  comment: ''
})

// Statuts disponibles avec leurs labels et descriptions
const statusOptions = [
  {
    value: 'devis',
    label: 'Devis',
    description: 'Devis en cours de préparation ou en attente de validation client'
  },
  {
    value: 'en_commande',
    label: 'En commande',
    description: 'Commande validée et passée aux fournisseurs'
  },
  {
    value: 'pret_pour_livraison',
    label: 'Prêt pour livraison',
    description: 'Produits reçus et prêts à être récupérés par le client'
  },
  {
    value: 'livre',
    label: 'Livré',
    description: 'Produits livrés au client, vente terminée'
  },
  {
    value: 'annule',
    label: 'Annulé',
    description: 'Vente annulée'
  }
]

// Statuts disponibles (excluant le statut actuel)
const availableStatuses = computed(() => {
  return statusOptions.filter(status => status.value !== props.sale?.status)
})

// Méthodes
const closeModal = () => {
  form.value.newStatus = ''
  form.value.comment = ''
  emit('close')
}

const handleSubmit = () => {
  if (!form.value.newStatus) return

  emit('update-status', {
    saleId: props.sale.id,
    newStatus: form.value.newStatus,
    comment: form.value.comment
  })
}

const getStatusLabel = (status) => {
  const statusOption = statusOptions.find(s => s.value === status)
  return statusOption ? statusOption.label : status
}

const getStatusDescription = (status) => {
  const statusOption = statusOptions.find(s => s.value === status)
  return statusOption ? statusOption.description : ''
}

const getStatusTextColor = (status) => {
  const colors = {
    devis: 'text-yellow-600',
    en_commande: 'text-blue-600',
    pret_pour_livraison: 'text-purple-600',
    livre: 'text-green-600',
    annule: 'text-red-600'
  }
  return colors[status] || 'text-gray-600'
}

// Watcher pour réinitialiser le formulaire quand la modal s'ouvre
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    form.value.newStatus = ''
    form.value.comment = ''
  }
})
</script>
