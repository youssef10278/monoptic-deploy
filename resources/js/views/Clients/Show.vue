<template>
  <div class="space-y-6">
    <!-- En-tête avec informations client -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ client?.name }}</h1>
            <div class="space-y-1 text-sm text-gray-600">
              <div v-if="client?.email" class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
                {{ client.email }}
              </div>
              <div v-if="client?.phone" class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                {{ client.phone }}
              </div>
              <div v-if="client?.address" class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ client.address }}
              </div>
            </div>
          </div>
          <div class="flex space-x-2">
            <button
              @click="$router.push('/clients')"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Retour
            </button>

            <button
              @click="editClient"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Modifier
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Section Historique des ventes -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Historique des ventes</h2>
      </div>

      <div class="p-6">
        <!-- Loading state -->
        <div v-if="isLoadingSales" class="flex justify-center py-8">
          <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 7 14 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <!-- Aucune vente -->
        <div v-else-if="sales.length === 0" class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune vente</h3>
          <p class="mt-1 text-sm text-gray-500">Ce client n'a pas encore effectué d'achat.</p>
        </div>

        <!-- Liste des ventes -->
        <div v-else class="space-y-4">
          <div
            v-for="sale in sales"
            :key="sale.id"
            class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <!-- En-tête de la vente -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <h4 class="text-sm font-medium text-gray-900">
                      Vente #{{ sale.id }}
                    </h4>
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusBadgeClass(sale.status)"
                    >
                      {{ getStatusLabel(sale.status) }}
                    </span>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-500">{{ formatDate(sale.created_at) }}</p>
                    <p class="text-lg font-semibold text-gray-900">{{ formatPrice(sale.total_amount) }}</p>
                  </div>
                </div>

                <!-- Articles de la vente -->
                <div class="space-y-2">
                  <div
                    v-for="item in sale.sale_items"
                    :key="item.id"
                    class="flex justify-between items-center text-sm"
                  >
                    <div class="flex-1">
                      <span class="text-gray-900">{{ item.product_name }}</span>
                      <span v-if="item.quantity > 1" class="text-gray-500 ml-1">x{{ item.quantity }}</span>
                    </div>
                    <span class="text-gray-700 font-medium">{{ formatPrice(item.unit_price * item.quantity) }}</span>
                  </div>
                </div>

                <!-- Informations de paiement -->
                <div v-if="sale.payment_status" class="mt-3 pt-3 border-t border-gray-200">
                  <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Statut de paiement:</span>
                    <span 
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                      :class="getPaymentStatusClass(sale.payment_status)"
                    >
                      {{ getPaymentStatusLabel(sale.payment_status) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="ml-4 flex flex-col space-y-2">
                <button
                  @click="viewSaleDetails(sale)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Voir détails
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Messages d'erreur et de succès -->
    <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
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

    <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-green-800">{{ successMessage }}</h3>
        </div>
      </div>
    </div>

    <!-- Modal des détails de vente -->
    <SaleDetailsModal
      :is-open="showSaleDetailsModal"
      :sale="selectedSale"
      @close="closeSaleDetailsModal"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { formatPrice } from '../../utils/currency.js'
import SaleDetailsModal from '../../Components/Sales/SaleDetailsModal.vue'

// Route et paramètres
const route = useRoute()
const clientId = route.params.id

// État réactif
const client = ref(null)
const sales = ref([])
const isLoadingClient = ref(false)
const isLoadingSales = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

// État pour la modal des détails de vente
const showSaleDetailsModal = ref(false)
const selectedSale = ref(null)

// Charger les informations du client
const loadClient = async () => {
  isLoadingClient.value = true
  errorMessage.value = ''

  try {
    const response = await axios.get(`/api/clients/${clientId}`)
    if (response.data.success) {
      client.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement du client:', error)
    if (error.response?.status === 404) {
      errorMessage.value = 'Client non trouvé'
    } else if (error.response?.status === 403) {
      errorMessage.value = 'Accès refusé. Vous n\'avez pas les droits pour voir ce client.'
    } else {
      errorMessage.value = 'Erreur lors du chargement du client'
    }
  } finally {
    isLoadingClient.value = false
  }
}

// Charger l'historique des ventes du client
const loadSales = async () => {
  isLoadingSales.value = true
  errorMessage.value = ''

  try {
    const response = await axios.get(`/api/clients/${clientId}/sales`)
    if (response.data.success) {
      sales.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des ventes:', error)
    if (error.response?.status === 403) {
      errorMessage.value = 'Accès refusé. Vous n\'avez pas les droits pour voir les ventes de ce client.'
    } else {
      errorMessage.value = 'Erreur lors du chargement des ventes'
    }
  } finally {
    isLoadingSales.value = false
  }
}

// Méthodes utilitaires
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
    case 'completed':
      return 'Terminée'
    case 'pending':
      return 'En attente'
    case 'cancelled':
      return 'Annulée'
    default:
      return 'Inconnue'
  }
}

const getPaymentStatusClass = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-100 text-green-800'
    case 'partial':
      return 'bg-yellow-100 text-yellow-800'
    case 'unpaid':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getPaymentStatusLabel = (status) => {
  switch (status) {
    case 'paid':
      return 'Payé'
    case 'partial':
      return 'Partiellement payé'
    case 'unpaid':
      return 'Non payé'
    default:
      return 'Inconnu'
  }
}

// Actions
const editClient = () => {
  // Rediriger vers la page d'édition du client
  // Cette fonctionnalité peut être implémentée plus tard
  console.log('Édition du client:', client.value)
}

const viewSaleDetails = async (sale) => {
  try {
    // Charger les détails complets de la vente
    const response = await axios.get(`/api/sales/${sale.id}`)
    if (response.data.success) {
      selectedSale.value = response.data.data
      showSaleDetailsModal.value = true
    }
  } catch (error) {
    console.error('Erreur lors du chargement des détails de la vente:', error)
    errorMessage.value = 'Erreur lors du chargement des détails de la vente'
  }
}

const closeSaleDetailsModal = () => {
  showSaleDetailsModal.value = false
  selectedSale.value = null
}

// Initialisation
onMounted(async () => {
  await Promise.all([
    loadClient(),
    loadSales()
  ])
})
</script>
