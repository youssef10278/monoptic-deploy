<template>
  <!-- Modal overlay -->
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="submitForm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                  {{ isEditing ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                </h3>
                
                <div class="space-y-4">
                  <!-- Nom -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      placeholder="Ex: Montures, Verres, Accessoires"
                    />
                  </div>
                </div>

                <!-- Messages d'erreur -->
                <div v-if="errorMessage" class="mt-4 rounded-md bg-red-50 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h3 class="text-sm font-medium text-red-800">{{ errorMessage }}</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isEditing ? 'Modification...' : 'Création...' }}
              </span>
              <span v-else>
                {{ isEditing ? 'Modifier' : 'Créer' }}
              </span>
            </button>
            <button
              type="button"
              @click="closeModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  category: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// État réactif
const isLoading = ref(false)
const errorMessage = ref('')

// Formulaire
const form = ref({
  name: ''
})

// Computed
const isEditing = computed(() => !!props.category)

// Réinitialiser le formulaire (déclarée AVANT le watcher)
const resetForm = () => {
  form.value = {
    name: ''
  }
  errorMessage.value = ''
}

// Watcher pour remplir le formulaire lors de l'édition
watch(() => props.category, (newCategory) => {
  if (newCategory) {
    form.value = {
      name: newCategory.name || ''
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// Fermer le modal
const closeModal = () => {
  resetForm()
  emit('close')
}

// Soumettre le formulaire
const submitForm = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  errorMessage.value = ''

  try {
    let response
    
    if (isEditing.value) {
      // Modification
      response = await axios.put(`/api/product-categories/${props.category.id}`, form.value)
    } else {
      // Création
      response = await axios.post('/api/product-categories', form.value)
    }

    if (response.data.success) {
      emit('success', {
        message: isEditing.value ? 'Catégorie modifiée avec succès' : 'Catégorie créée avec succès',
        category: response.data.data
      })
      closeModal()
    }
  } catch (error) {
    console.error('Erreur lors de la soumission:', error)
    
    if (error.response?.data?.errors) {
      // Erreurs de validation
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat()
      errorMessage.value = errorMessages.join(', ')
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = `Erreur lors de la ${isEditing.value ? 'modification' : 'création'} de la catégorie`
    }
  } finally {
    isLoading.value = false
  }
}
</script>
