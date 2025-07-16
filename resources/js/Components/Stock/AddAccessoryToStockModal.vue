<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- En-tête -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Ajouter un accessoire au stock
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Ajoutez un nouvel accessoire directement dans votre stock
                </p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Formulaire -->
          <form @submit.prevent="submitForm" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Type d'accessoire -->
              <div>
                <label for="accessory_type" class="block text-sm font-medium text-gray-700 mb-1">
                  Type d'accessoire *
                </label>
                <select
                  id="accessory_type"
                  v-model="form.attributes.accessory_type"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
                  <option value="">Sélectionner un type...</option>
                  <option value="case">Étui</option>
                  <option value="cloth">Chiffon de nettoyage</option>
                  <option value="cord">Cordon/Chaîne</option>
                  <option value="cleaning_kit">Kit de nettoyage</option>
                  <option value="nose_pad">Plaquettes nasales</option>
                  <option value="screw">Vis et accessoires</option>
                  <option value="strap">Sangle sport</option>
                  <option value="other">Autre</option>
                </select>
              </div>

              <!-- Nom/Description -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                  Nom/Description *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Ex: Étui rigide noir, Chiffon microfibre..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Marque -->
              <div>
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                  Marque
                </label>
                <input
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  placeholder="Ex: Ray-Ban, Oakley, Générique..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Référence -->
              <div>
                <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">
                  Référence
                </label>
                <input
                  id="reference"
                  v-model="form.reference"
                  type="text"
                  placeholder="Référence produit ou fournisseur"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Couleur -->
              <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                  Couleur
                </label>
                <input
                  id="color"
                  v-model="form.attributes.color"
                  type="text"
                  placeholder="Ex: Noir, Marron, Transparent..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Matériau -->
              <div>
                <label for="material" class="block text-sm font-medium text-gray-700 mb-1">
                  Matériau
                </label>
                <select
                  id="material"
                  v-model="form.attributes.material"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
                  <option value="">Sélectionner...</option>
                  <option value="leather">Cuir</option>
                  <option value="fabric">Tissu</option>
                  <option value="plastic">Plastique</option>
                  <option value="metal">Métal</option>
                  <option value="microfiber">Microfibre</option>
                  <option value="silicone">Silicone</option>
                  <option value="other">Autre</option>
                </select>
              </div>

              <!-- Prix d'achat -->
              <div>
                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix d'achat (MAD)
                </label>
                <input
                  id="purchase_price"
                  v-model.number="form.purchase_price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Prix de vente -->
              <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix de vente (MAD) *
                </label>
                <input
                  id="selling_price"
                  v-model.number="form.selling_price"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  placeholder="0.00"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Quantité -->
              <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                  Quantité en stock *
                </label>
                <input
                  id="quantity"
                  v-model.number="form.quantity"
                  type="number"
                  min="0"
                  required
                  placeholder="1"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>

              <!-- Code-barres -->
              <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">
                  Code-barres
                </label>
                <input
                  id="barcode"
                  v-model="form.barcode"
                  type="text"
                  placeholder="Code-barres ou EAN"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                >
              </div>
            </div>

            <!-- Fournisseur -->
            <div class="mt-4">
              <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">
                Fournisseur
              </label>
              <input
                id="supplier"
                v-model="form.attributes.supplier"
                type="text"
                placeholder="Nom du fournisseur"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
              >
            </div>

            <!-- Notes -->
            <div class="mt-4">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                Notes
              </label>
              <textarea
                id="notes"
                v-model="form.attributes.notes"
                rows="2"
                placeholder="Notes ou informations supplémentaires..."
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
              ></textarea>
            </div>

            <!-- Message d'erreur -->
            <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
              <p class="text-sm text-red-600">{{ errorMessage }}</p>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="isLoading"
                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="isLoading" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Ajout en cours...
                </span>
                <span v-else>Ajouter l'accessoire</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  accessoryCategory: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// État réactif
const isLoading = ref(false)
const errorMessage = ref('')

const form = ref({
  name: '',
  brand: '',
  reference: '',
  purchase_price: null,
  selling_price: null,
  quantity: 1,
  barcode: '',
  type: 'accessory',
  product_category_id: null,
  attributes: {
    accessory_type: '',
    color: '',
    material: '',
    supplier: '',
    notes: ''
  }
})

// Méthodes
const resetForm = () => {
  form.value = {
    name: '',
    brand: '',
    reference: '',
    purchase_price: null,
    selling_price: null,
    quantity: 1,
    barcode: '',
    type: 'accessory',
    product_category_id: props.accessoryCategory?.id || null,
    attributes: {
      accessory_type: '',
      color: '',
      material: '',
      supplier: '',
      notes: ''
    }
  }
  errorMessage.value = ''
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const submitForm = async () => {
  if (isLoading.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.post('/api/products', form.value)

    if (response.data.success) {
      emit('success', {
        message: 'Accessoire ajouté au stock avec succès',
        product: response.data.data
      })
      closeModal()
    }
  } catch (error) {
    console.error('Erreur lors de l\'ajout de l\'accessoire:', error)
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage.value = errors.join(', ')
    } else {
      errorMessage.value = 'Erreur lors de l\'ajout de l\'accessoire'
    }
  } finally {
    isLoading.value = false
  }
}

// Watcher pour réinitialiser le formulaire quand la modal s'ouvre
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    resetForm()
  }
})

// Watcher pour mettre à jour la catégorie
watch(() => props.accessoryCategory, (newCategory) => {
  if (newCategory) {
    form.value.product_category_id = newCategory.id
  }
})
</script>
