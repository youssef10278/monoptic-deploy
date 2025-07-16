<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Ajouter une Monture
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
              Rechercher une monture en stock
            </label>
            <div class="relative">
              <input
                id="product-search"
                v-model="productSearch"
                type="text"
                placeholder="Nom, référence, marque..."
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
            <h4 class="text-sm font-medium text-gray-700 mb-3">Montures disponibles</h4>
            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-md">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                @click="selectProduct(product)"
                class="p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                :class="{ 'bg-blue-50 border-blue-200': selectedProduct?.id === product.id }"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h5 class="text-sm font-medium text-gray-900">{{ product.name }}</h5>
                    <p class="text-sm text-gray-500">{{ product.brand || 'Sans marque' }}</p>
                    <p class="text-xs text-gray-400">Réf: {{ product.reference || 'N/A' }}</p>
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
                  placeholder="Description de la monture"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix unitaire (MAD) *
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                  Marque
                </label>
                <input
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  placeholder="Marque de la monture"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                  <option value="metal">Métal</option>
                  <option value="plastic">Plastique</option>
                  <option value="titanium">Titane</option>
                  <option value="acetate">Acétate</option>
                  <option value="mixed">Mixte</option>
                </select>
              </div>

              <div>
                <label for="size" class="block text-sm font-medium text-gray-700 mb-1">
                  Taille
                </label>
                <input
                  id="size"
                  v-model="form.size"
                  type="text"
                  placeholder="ex: 52-18-140"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>

            <!-- Résumé -->
            <div v-if="form.description && form.price && form.quantity" class="bg-gray-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Résumé</h4>
              <div class="text-sm text-gray-600">
                <p><strong>Description:</strong> {{ form.description }}</p>
                <p><strong>Prix unitaire:</strong> {{ formatPrice(form.price) }}</p>
                <p><strong>Quantité:</strong> {{ form.quantity }}</p>
                <p><strong>Total:</strong> {{ formatPrice(form.price * form.quantity) }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="addToCart"
            :disabled="!canAdd"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
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
  price: 0,
  quantity: 1,
  brand: '',
  color: '',
  material: '',
  size: ''
})

// Computed
const filteredProducts = computed(() => {
  if (!productSearch.value) return products.value
  
  const search = productSearch.value.toLowerCase()
  return products.value.filter(product => 
    product.name.toLowerCase().includes(search) ||
    (product.brand && product.brand.toLowerCase().includes(search)) ||
    (product.reference && product.reference.toLowerCase().includes(search))
  )
})

const canAdd = computed(() => {
  return form.value.description && form.value.price > 0 && form.value.quantity > 0
})

// Méthodes
const loadProducts = async () => {
  try {
    const response = await axios.get('/api/products?type=frame')
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
  form.value.material = product.material || ''
  form.value.size = product.size || ''
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const addToCart = () => {
  if (!canAdd.value) return

  const frameData = {
    product_id: selectedProduct.value?.id || null,
    description: form.value.description,
    quantity: form.value.quantity,
    price: form.value.price,
    details: {
      brand: form.value.brand,
      color: form.value.color,
      material: form.value.material,
      size: form.value.size
    }
  }

  emit('add', frameData)
  emit('close')
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>
