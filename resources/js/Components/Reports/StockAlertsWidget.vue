<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Alertes de Stock
        </h3>
        <div class="flex items-center space-x-2">
          <span v-if="lowStockCount > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            {{ lowStockCount }} stock faible
          </span>
          <span v-if="outOfStockCount > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            {{ outOfStockCount }} rupture
          </span>
        </div>
      </div>
      
      <div v-if="isLoading" class="flex justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <div v-else-if="hasAlerts" class="space-y-3">
        <!-- Produits en rupture de stock -->
        <div v-if="outOfStockProducts && outOfStockProducts.length > 0">
          <h4 class="text-sm font-medium text-red-800 mb-2 flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Rupture de Stock
          </h4>
          <div class="space-y-2">
            <div
              v-for="product in outOfStockProducts"
              :key="product.id"
              class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200"
            >
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ product.name }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ product.product_category?.name || 'Sans catégorie' }}
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-bold text-red-600">
                  Stock: {{ product.stock_quantity }}
                </div>
                <div class="text-xs text-gray-500">
                  Prix: {{ formatPrice(product.price) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Produits à stock faible -->
        <div v-if="lowStockProducts && lowStockProducts.length > 0">
          <h4 class="text-sm font-medium text-yellow-800 mb-2 flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Stock Faible (≤ 5)
          </h4>
          <div class="space-y-2">
            <div
              v-for="product in lowStockProducts"
              :key="product.id"
              class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200"
            >
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="h-8 w-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-yellow-800">{{ product.stock_quantity }}</span>
                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ product.name }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ product.product_category?.name || 'Sans catégorie' }}
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-medium" :class="getStockLevelClass(product.stock_quantity)">
                  Stock: {{ product.stock_quantity }}
                </div>
                <div class="text-xs text-gray-500">
                  Prix: {{ formatPrice(product.price) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-500">Aucune alerte de stock</p>
        <p class="text-xs text-gray-400">Tous les produits ont un stock suffisant</p>
      </div>
      
      <!-- Résumé des alertes -->
      <div v-if="hasAlerts" class="mt-4 pt-4 border-t border-gray-200">
        <div class="grid grid-cols-2 gap-4 text-center">
          <div>
            <div class="text-lg font-semibold text-red-600">
              {{ outOfStockCount }}
            </div>
            <div class="text-xs text-gray-500">Ruptures</div>
          </div>
          <div>
            <div class="text-lg font-semibold text-yellow-600">
              {{ lowStockCount }}
            </div>
            <div class="text-xs text-gray-500">Stock Faible</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatPrice } from '../../utils/currency.js'

const props = defineProps({
  lowStockProducts: {
    type: Array,
    default: () => []
  },
  outOfStockProducts: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

// Computed properties
const lowStockCount = computed(() => props.lowStockProducts?.length || 0)
const outOfStockCount = computed(() => props.outOfStockProducts?.length || 0)
const hasAlerts = computed(() => lowStockCount.value > 0 || outOfStockCount.value > 0)

// Methods
const getStockLevelClass = (quantity) => {
  if (quantity <= 0) return 'text-red-600'
  if (quantity <= 2) return 'text-red-500'
  if (quantity <= 5) return 'text-yellow-600'
  return 'text-green-600'
}
</script>
