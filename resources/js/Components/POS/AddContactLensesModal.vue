<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                🔍 Recherche Intelligente de Lentilles
              </h3>
              <p class="text-sm text-gray-600 mt-1">Trouvez la lentille exacte correspondant aux paramètres du client</p>
            </div>
            <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Étape A: Sélection de la Marque et du Modèle -->
          <div class="mb-8">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-4">
              <h4 class="text-sm font-semibold text-blue-900 mb-2">📋 Étape 1 : Marque et Modèle</h4>
              <p class="text-xs text-blue-700">Sélectionnez d'abord la famille de lentilles que porte le client</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Recherche de modèle -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Rechercher un modèle de lentilles
                </label>
                <div class="relative">
                  <input
                    v-model="modelSearch"
                    type="text"
                    placeholder="Ex: Acuvue Oasys, Biofinity, Dailies..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    @input="searchModels"
                  >
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Modèle sélectionné -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Modèle sélectionné
                </label>
                <div v-if="selectedModel" class="p-3 bg-green-50 border border-green-200 rounded-md">
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-medium text-green-900">{{ selectedModel.name }}</h5>
                      <p class="text-xs text-green-700">{{ selectedModel.brand }}</p>
                    </div>
                    <button @click="selectedModel = null" class="text-green-600 hover:text-green-800">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div v-else class="p-3 bg-gray-50 border border-gray-200 rounded-md text-center text-gray-500 text-sm">
                  Aucun modèle sélectionné
                </div>
              </div>
            </div>

            <!-- Liste des modèles trouvés -->
            <div v-if="filteredModels.length > 0 && !selectedModel" class="mt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Modèles disponibles</h4>
              <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-md">
                <div
                  v-for="model in filteredModels"
                  :key="model.id"
                  @click="selectModel(model)"
                  class="p-3 border-b border-gray-100 last:border-b-0 cursor-pointer hover:bg-gray-50"
                >
                  <div class="flex justify-between items-center">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">{{ model.name }}</h5>
                      <p class="text-xs text-gray-500">{{ model.brand }}</p>
                    </div>
                    <span class="text-xs text-blue-600">Sélectionner</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Formulaire de saisie -->
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                  Description *
                </label>
                <input
                  id="description"
                  v-model="form.description"
                  type="text"
                  required
                  placeholder="Description des lentilles"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                  Marque
                </label>
                <input
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  placeholder="Marque des lentilles"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>

            <!-- Spécifications techniques -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-3">Spécifications</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="power-od" class="block text-sm font-medium text-gray-700 mb-1">
                    Puissance OD
                  </label>
                  <input
                    id="power-od"
                    v-model.number="form.powerOd"
                    type="number"
                    step="0.25"
                    min="-30"
                    max="30"
                    placeholder="±0.00"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>

                <div>
                  <label for="power-og" class="block text-sm font-medium text-gray-700 mb-1">
                    Puissance OG
                  </label>
                  <input
                    id="power-og"
                    v-model.number="form.powerOg"
                    type="number"
                    step="0.25"
                    min="-30"
                    max="30"
                    placeholder="±0.00"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>

                <div>
                  <label for="diameter" class="block text-sm font-medium text-gray-700 mb-1">
                    Diamètre (mm)
                  </label>
                  <input
                    id="diameter"
                    v-model.number="form.diameter"
                    type="number"
                    step="0.1"
                    min="8"
                    max="20"
                    placeholder="14.2"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="base-curve" class="block text-sm font-medium text-gray-700 mb-1">
                  Courbure de base
                </label>
                <input
                  id="base-curve"
                  v-model.number="form.baseCurve"
                  type="number"
                  step="0.1"
                  min="6"
                  max="12"
                  placeholder="8.6"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="lens-type" class="block text-sm font-medium text-gray-700 mb-1">
                  Type de lentille
                </label>
                <select
                  id="lens-type"
                  v-model="form.lensType"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="daily">Journalière</option>
                  <option value="weekly">Hebdomadaire</option>
                  <option value="monthly">Mensuelle</option>
                  <option value="yearly">Annuelle</option>
                </select>
              </div>

              <div>
                <label for="material" class="block text-sm font-medium text-gray-700 mb-1">
                  Matériau
                </label>
                <select
                  id="material"
                  v-model="form.material"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner</option>
                  <option value="hydrogel">Hydrogel</option>
                  <option value="silicone_hydrogel">Silicone hydrogel</option>
                  <option value="rigid">Rigide</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                  Quantité *
                </label>
                <input
                  id="quantity"
                  v-model.number="form.quantity"
                  type="number"
                  min="1"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix unitaire (€) *
                </label>
                <input
                  id="price"
                  v-model.number="form.price"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="total" class="block text-sm font-medium text-gray-700 mb-1">
                  Total (€)
                </label>
                <input
                  id="total"
                  :value="form.price * form.quantity"
                  type="number"
                  readonly
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                >
              </div>
            </div>

            <!-- Résumé -->
            <div v-if="form.description && form.price && form.quantity" class="bg-gray-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Résumé</h4>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Description:</strong> {{ form.description }}</p>
                <p v-if="form.brand"><strong>Marque:</strong> {{ form.brand }}</p>
                <div v-if="form.powerOd !== null || form.powerOg !== null">
                  <strong>Puissances:</strong>
                  <span v-if="form.powerOd !== null">OD: {{ form.powerOd >= 0 ? '+' : '' }}{{ form.powerOd }}</span>
                  <span v-if="form.powerOg !== null" class="ml-2">OG: {{ form.powerOg >= 0 ? '+' : '' }}{{ form.powerOg }}</span>
                </div>
                <p v-if="form.lensType"><strong>Type:</strong> {{ getLensTypeLabel(form.lensType) }}</p>
                <p><strong>Total:</strong> {{ formatPrice(form.price * form.quantity) }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="addToCart"
            :disabled="!canAdd"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Ajouter au panier
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Emits
const emit = defineEmits(['close', 'add'])

// État réactif
const productSearch = ref('')
const products = ref([])
const selectedProduct = ref(null)

const form = ref({
  description: '',
  brand: '',
  powerOd: null,
  powerOg: null,
  diameter: null,
  baseCurve: null,
  lensType: '',
  material: '',
  quantity: 1,
  price: 0
})

// Computed
const filteredProducts = computed(() => {
  if (!productSearch.value) return products.value
  
  const search = productSearch.value.toLowerCase()
  return products.value.filter(product => 
    product.name.toLowerCase().includes(search) ||
    (product.brand && product.brand.toLowerCase().includes(search)) ||
    (product.description && product.description.toLowerCase().includes(search))
  )
})

const canAdd = computed(() => {
  return form.value.description && form.value.price > 0 && form.value.quantity > 0
})

// Méthodes
const loadProducts = async () => {
  try {
    const response = await axios.get('/api/products?type=contact_lenses')
    if (response.data.success) {
      products.value = response.data.data.filter(p => p.quantity > 0)
    }
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
  }
}

const searchProducts = () => {
  // La recherche est gérée par le computed filteredProducts
}

const selectProduct = (product) => {
  selectedProduct.value = product
  form.value.description = product.name
  form.value.price = product.price
  form.value.brand = product.brand || ''
  
  // Essayer d'extraire les spécifications du produit si disponibles
  if (product.specifications) {
    form.value.powerOd = product.specifications.powerOd || null
    form.value.powerOg = product.specifications.powerOg || null
    form.value.diameter = product.specifications.diameter || null
    form.value.baseCurve = product.specifications.baseCurve || null
    form.value.lensType = product.specifications.lensType || ''
    form.value.material = product.specifications.material || ''
  }
}

const getLensTypeLabel = (type) => {
  const labels = {
    daily: 'Journalière',
    weekly: 'Hebdomadaire',
    monthly: 'Mensuelle',
    yearly: 'Annuelle'
  }
  return labels[type] || type
}

import { formatPrice } from '../../utils/currency.js'

const addToCart = () => {
  if (!canAdd.value) return

  const contactLensesData = {
    product_id: selectedProduct.value?.id || null,
    description: form.value.description,
    quantity: form.value.quantity,
    price: form.value.price,
    details: {
      brand: form.value.brand,
      powerOd: form.value.powerOd,
      powerOg: form.value.powerOg,
      diameter: form.value.diameter,
      baseCurve: form.value.baseCurve,
      lensType: form.value.lensType,
      material: form.value.material
    }
  }

  emit('add', contactLensesData)
  emit('close')
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>
