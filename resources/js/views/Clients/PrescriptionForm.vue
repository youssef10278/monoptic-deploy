<template>
  <!-- Modal overlay -->
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <form @submit.prevent="submitForm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6" id="modal-title">
                  Ajouter une prescription
                </h3>

                <!-- Informations générales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label for="prescriber" class="block text-sm font-medium text-gray-700">
                      Prescripteur <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="prescriber"
                      v-model="form.prescriber"
                      type="text"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      placeholder="Dr. Nom du médecin"
                    />
                  </div>
                  
                  <div>
                    <label for="prescription_date" class="block text-sm font-medium text-gray-700">
                      Date de prescription <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="prescription_date"
                      v-model="form.prescription_date"
                      type="date"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    />
                  </div>
                </div>

                <!-- Mesures optiques -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                  <!-- Œil Droit (OD) -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                      Œil Droit (OD)
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="od_sph" class="block text-sm font-medium text-gray-700">Sphère</label>
                        <input
                          id="od_sph"
                          v-model="form.od_sph"
                          type="number"
                          step="0.25"
                          min="-30"
                          max="30"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="±0.00"
                        />
                      </div>
                      
                      <div>
                        <label for="od_cyl" class="block text-sm font-medium text-gray-700">Cylindre</label>
                        <input
                          id="od_cyl"
                          v-model="form.od_cyl"
                          type="number"
                          step="0.25"
                          min="-10"
                          max="10"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="±0.00"
                        />
                      </div>
                      
                      <div>
                        <label for="od_axe" class="block text-sm font-medium text-gray-700">Axe (°)</label>
                        <input
                          id="od_axe"
                          v-model="form.od_axe"
                          type="number"
                          min="0"
                          max="180"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="0-180"
                        />
                      </div>
                      
                      <div>
                        <label for="od_add" class="block text-sm font-medium text-gray-700">Addition</label>
                        <input
                          id="od_add"
                          v-model="form.od_add"
                          type="number"
                          step="0.25"
                          min="0"
                          max="5"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="+0.00"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Œil Gauche (OG) -->
                  <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                      Œil Gauche (OG)
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="og_sph" class="block text-sm font-medium text-gray-700">Sphère</label>
                        <input
                          id="og_sph"
                          v-model="form.og_sph"
                          type="number"
                          step="0.25"
                          min="-30"
                          max="30"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="±0.00"
                        />
                      </div>
                      
                      <div>
                        <label for="og_cyl" class="block text-sm font-medium text-gray-700">Cylindre</label>
                        <input
                          id="og_cyl"
                          v-model="form.og_cyl"
                          type="number"
                          step="0.25"
                          min="-10"
                          max="10"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="±0.00"
                        />
                      </div>
                      
                      <div>
                        <label for="og_axe" class="block text-sm font-medium text-gray-700">Axe (°)</label>
                        <input
                          id="og_axe"
                          v-model="form.og_axe"
                          type="number"
                          min="0"
                          max="180"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="0-180"
                        />
                      </div>
                      
                      <div>
                        <label for="og_add" class="block text-sm font-medium text-gray-700">Addition</label>
                        <input
                          id="og_add"
                          v-model="form.og_add"
                          type="number"
                          step="0.25"
                          min="0"
                          max="5"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="+0.00"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Écarts pupillaires -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div>
                    <label for="pupillary_distance_l" class="block text-sm font-medium text-gray-700">
                      Écart pupillaire gauche (mm)
                    </label>
                    <input
                      id="pupillary_distance_l"
                      v-model="form.pupillary_distance_l"
                      type="number"
                      step="0.5"
                      min="20"
                      max="80"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      placeholder="32.0"
                    />
                  </div>
                  
                  <div>
                    <label for="pupillary_distance_r" class="block text-sm font-medium text-gray-700">
                      Écart pupillaire droit (mm)
                    </label>
                    <input
                      id="pupillary_distance_r"
                      v-model="form.pupillary_distance_r"
                      type="number"
                      step="0.5"
                      min="20"
                      max="80"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                      placeholder="32.0"
                    />
                  </div>
                </div>

                <!-- Upload du scan -->
                <div class="mb-6">
                  <label for="scan" class="block text-sm font-medium text-gray-700">
                    Scan de l'ordonnance
                  </label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="scan" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                          <span>Télécharger un fichier</span>
                          <input
                            id="scan"
                            ref="fileInput"
                            type="file"
                            class="sr-only"
                            accept=".pdf,.jpg,.jpeg,.png"
                            @change="handleFileChange"
                          />
                        </label>
                        <p class="pl-1">ou glisser-déposer</p>
                      </div>
                      <p class="text-xs text-gray-500">
                        PDF, PNG, JPG jusqu'à 5MB
                      </p>
                      <div v-if="selectedFile" class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ selectedFile.name }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Messages d'erreur -->
                <div v-if="errorMessage" class="mb-4 rounded-md bg-red-50 p-4">
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
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enregistrement...
              </span>
              <span v-else>
                Enregistrer la prescription
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
import { ref, watch } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  clientId: {
    type: [String, Number],
    required: true
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// État réactif
const isLoading = ref(false)
const errorMessage = ref('')
const selectedFile = ref(null)
const fileInput = ref(null)

// Formulaire
const form = ref({
  prescriber: '',
  prescription_date: '',
  od_sph: '',
  od_cyl: '',
  od_axe: '',
  od_add: '',
  og_sph: '',
  og_cyl: '',
  og_axe: '',
  og_add: '',
  pupillary_distance_l: '',
  pupillary_distance_r: ''
})

// Réinitialiser le formulaire
const resetForm = () => {
  form.value = {
    prescriber: '',
    prescription_date: '',
    od_sph: '',
    od_cyl: '',
    od_axe: '',
    od_add: '',
    og_sph: '',
    og_cyl: '',
    og_axe: '',
    og_add: '',
    pupillary_distance_l: '',
    pupillary_distance_r: ''
  }
  selectedFile.value = null
  errorMessage.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Gérer le changement de fichier
const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Vérifier la taille du fichier (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      errorMessage.value = 'Le fichier ne doit pas dépasser 5MB'
      event.target.value = ''
      return
    }

    // Vérifier le type de fichier
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
      errorMessage.value = 'Seuls les fichiers PDF, JPG, JPEG et PNG sont autorisés'
      event.target.value = ''
      return
    }

    selectedFile.value = file
    errorMessage.value = ''
  }
}

// Watcher pour réinitialiser quand le modal se ferme
watch(() => props.show, (show) => {
  if (!show) {
    resetForm()
  } else {
    // Définir la date d'aujourd'hui par défaut
    form.value.prescription_date = new Date().toISOString().split('T')[0]
  }
})

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
    // Créer FormData pour l'upload de fichier
    const formData = new FormData()

    // Ajouter les champs du formulaire
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== '' && form.value[key] !== null) {
        formData.append(key, form.value[key])
      }
    })

    // Ajouter le fichier si présent
    if (selectedFile.value) {
      formData.append('scan', selectedFile.value)
    }

    const response = await axios.post(`/api/clients/${props.clientId}/prescriptions`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      emit('success', {
        message: 'Prescription créée avec succès',
        prescription: response.data.data
      })
      closeModal()
    }
  } catch (error) {
    console.error('Erreur lors de la création de la prescription:', error)

    if (error.response?.data?.errors) {
      // Erreurs de validation
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat()
      errorMessage.value = errorMessages.join(', ')
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Erreur lors de la création de la prescription'
    }
  } finally {
    isLoading.value = false
  }
}
</script>
