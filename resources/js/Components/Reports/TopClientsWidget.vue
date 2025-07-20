<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Top 5 - Meilleurs Clients
        </h3>
        <div class="flex items-center text-sm text-gray-500">
          <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
          </svg>
          Par chiffre d'affaires
        </div>
      </div>
      
      <div v-if="isLoading" class="flex justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <div v-else-if="clients && clients.length > 0" class="space-y-3">
        <div
          v-for="(client, index) in clients"
          :key="client.id"
          class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-100 hover:shadow-md transition-shadow duration-200"
        >
          <div class="flex items-center space-x-4">
            <!-- Rang -->
            <div class="flex-shrink-0">
              <span 
                class="inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-bold"
                :class="getRankClass(index)"
              >
                {{ index + 1 }}
              </span>
            </div>
            
            <!-- Informations client -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-2">
                <p class="text-sm font-medium text-gray-900 truncate">
                  {{ client.name }}
                </p>
                <span v-if="client.is_vip" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  VIP
                </span>
              </div>
              <div class="flex items-center space-x-4 mt-1">
                <p class="text-xs text-gray-500">
                  {{ client.total_orders }} commande{{ client.total_orders > 1 ? 's' : '' }}
                </p>
                <p class="text-xs text-gray-500">
                  Dernière visite: {{ formatDate(client.last_order_date) }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Chiffre d'affaires -->
          <div class="flex-shrink-0 text-right">
            <div class="text-lg font-semibold text-gray-900">
              {{ formatPrice(client.total_revenue) }}
            </div>
            <div class="text-xs text-gray-500">
              Moy: {{ formatPrice(client.average_order) }}
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-500">Aucun client trouvé</p>
      </div>
      
      <!-- Statistiques résumées -->
      <div v-if="clients && clients.length > 0" class="mt-6 pt-4 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-4 text-center">
          <div>
            <div class="text-lg font-semibold text-gray-900">
              {{ formatPrice(totalRevenue) }}
            </div>
            <div class="text-xs text-gray-500">CA Total Top 5</div>
          </div>
          <div>
            <div class="text-lg font-semibold text-gray-900">
              {{ totalOrders }}
            </div>
            <div class="text-xs text-gray-500">Commandes Total</div>
          </div>
          <div>
            <div class="text-lg font-semibold text-gray-900">
              {{ formatPrice(averageOrderValue) }}
            </div>
            <div class="text-xs text-gray-500">Panier Moyen</div>
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
  clients: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

// Computed properties
const totalRevenue = computed(() => {
  return props.clients.reduce((sum, client) => sum + parseFloat(client.total_revenue || 0), 0)
})

const totalOrders = computed(() => {
  return props.clients.reduce((sum, client) => sum + parseInt(client.total_orders || 0), 0)
})

const averageOrderValue = computed(() => {
  return totalOrders.value > 0 ? totalRevenue.value / totalOrders.value : 0
})

// Methods
const getRankClass = (index) => {
  const classes = [
    'bg-yellow-100 text-yellow-800 border-2 border-yellow-300', // 1er
    'bg-gray-100 text-gray-800 border-2 border-gray-300',       // 2ème
    'bg-orange-100 text-orange-800 border-2 border-orange-300', // 3ème
    'bg-blue-100 text-blue-800',                                // 4ème
    'bg-green-100 text-green-800'                               // 5ème
  ]
  return classes[index] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'Hier'
  if (diffDays < 7) return `Il y a ${diffDays} jours`
  if (diffDays < 30) return `Il y a ${Math.floor(diffDays / 7)} semaine${Math.floor(diffDays / 7) > 1 ? 's' : ''}`
  
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short'
  })
}
</script>
