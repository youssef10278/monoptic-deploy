<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Ajouter un Accessoire
            </h3>
            <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Recherche de produit -->
          <div class="mb-6">
            <label for="product-search" class="block text-sm font-medium text-gray-700 mb-2">
              Rechercher un accessoire en stock
            </label>
            <div class="relative">
              <input
                id="product-search"
                v-model="productSearch"
                type="text"
                placeholder="Nom, type, marque..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                @input="searchProducts"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Liste des produits -->
          <div v-if="filteredProducts.length > 0" class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Accessoires disponibles</h4>
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                @click="selectProduct(product)"
                class="p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                :class="{ 'bg-yellow-50 border-yellow-200': selectedProduct?.id === product.id }"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h5 class="text-sm font-medium text-gray-900">{{ product.name }}</h5>
                    <p class="text-sm text-gray-500">{{ product.brand || 'Sans marque' }}</p>
                    <p class="text-xs text-gray-400">{{ product.description || 'Pas de description' }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">{{ formatPrice(product.price) }}</p>
                    <p class="text-xs text-gray-500">Stock: {{ product.quantity }}</p>
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
                  placeholder="Description de l'accessoire"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                  Type d'accessoire
                </label>
                <select
                  id="type"
                  v-model="form.type"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner un type</option>
                  <option value="case">Étui</option>
                  <option value="cleaning_kit">Kit de nettoyage</option>
                  <option value="chain">Chaînette</option>
                  <option value="cord">Cordon</option>
                  <option value="cloth">Chiffon</option>
                  <option value="spray">Spray nettoyant</option>
                  <option value="solution">Solution d'entretien</option>
                  <option value="other">Autre</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                  Marque
                </label>
                <input
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  placeholder="Marque de l'accessoire"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                  Couleur
                </label>
                <input
                  id="color"
                  v-model="form.color"
                  type="text"
                  placeholder="Couleur"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
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

            <!-- Notes supplémentaires -->
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                Notes supplémentaires
              </label>
              <textarea
                id="notes"
                v-model="form.notes"
                rows="3"
                placeholder="Notes ou spécifications particulières..."
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
            </div>

            <!-- Résumé -->
            <div v-if="form.description && form.price && form.quantity" class="bg-gray-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Résumé</h4>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Description:</strong> {{ form.description }}</p>
                <p v-if="form.type"><strong>Type:</strong> {{ getTypeLabel(form.type) }}</p>
                <p v-if="form.brand"><strong>Marque:</strong> {{ form.brand }}</p>
                <p v-if="form.color"><strong>Couleur:</strong> {{ form.color }}</p>
                <p><strong>Quantité:</strong> {{ form.quantity }}</p>
                <p><strong>Prix unitaire:</strong> {{ formatPrice(form.price) }}</p>
                <p><strong>Total:</strong> {{ formatPrice(form.price * form.quantity) }}</p>
                <p v-if="form.notes"><strong>Notes:</strong> {{ form.notes }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="addToCart"
            :disabled="!canAdd"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
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
  type: '',
  brand: '',
  color: '',
  quantity: 1,
  price: 0,
  notes: ''
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
    const response = await axios.get('/api/products?type=accessory')
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
  form.value.color = product.color || ''
  form.value.type = product.type || ''
}

const getTypeLabel = (type) => {
  const labels = {
    case: 'Étui',
    cleaning_kit: 'Kit de nettoyage',
    chain: 'Chaînette',
    cord: 'Cordon',
    cloth: 'Chiffon',
    spray: 'Spray nettoyant',
    solution: 'Solution d\'entretien',
    other: 'Autre'
  }
  return labels[type] || type
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const addToCart = () => {
  if (!canAdd.value) return

  const accessoryData = {
    product_id: selectedProduct.value?.id || null,
    description: form.value.description,
    quantity: form.value.quantity,
    price: form.value.price,
    details: {
      type: form.value.type,
      brand: form.value.brand,
      color: form.value.color,
      notes: form.value.notes
    }
  }

  emit('add', accessoryData)
  emit('close')
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>
