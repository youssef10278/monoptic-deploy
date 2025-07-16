<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Gestion des Crédits</h1>
            <p class="text-gray-600">Clients ayant des factures impayées</p>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-500">Total des créances</div>
            <div class="text-2xl font-bold text-red-600">{{ formatPrice(totalCredits) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Clients à crédit
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ clientsWithCredits.length }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Factures impayées
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ totalUnpaidSales }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Créance moyenne
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ formatPrice(averageCredit) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table des clients à crédit -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Clients à crédit</h2>
        
        <div v-if="isLoading" class="flex justify-center py-8">
          <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <div v-else-if="clientsWithCredits.length === 0" class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun crédit en cours</h3>
          <p class="mt-1 text-sm text-gray-500">Tous vos clients sont à jour dans leurs paiements.</p>
        </div>

        <div v-else class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Client
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Montant dû
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Factures
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Depuis
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="client in clientsWithCredits" :key="client.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                    <div class="text-sm text-gray-500">
                      {{ client.email || 'Pas d\'email' }}
                    </div>
                    <div v-if="client.phone" class="text-sm text-gray-500">
                      {{ client.phone }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-red-600">
                    {{ formatPrice(client.total_due) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    {{ client.unpaid_sales_count }} facture(s)
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(client.oldest_unpaid_sale) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    @click="openPaymentModal(client)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Paiement
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Messages d'erreur/succès -->
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

    <!-- Formulaire de paiement -->
    <PaymentForm
      :show="showPaymentModal"
      :client="selectedClient"
      @close="closePaymentModal"
      @success="handlePaymentSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import PaymentForm from './PaymentForm.vue'

// État réactif
const clientsWithCredits = ref([])
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showPaymentModal = ref(false)
const selectedClient = ref(null)

// Charger la liste des clients à crédit
const loadClientsWithCredits = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.get('/api/clients/credits')
    if (response.data.success) {
      clientsWithCredits.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des clients à crédit:', error)
    if (error.response?.status === 403) {
      errorMessage.value = 'Accès refusé. Vous devez être associé à un magasin pour voir les crédits.'
    } else {
      errorMessage.value = 'Erreur lors du chargement des clients à crédit'
    }
  } finally {
    isLoading.value = false
  }
}

// Ouvrir le modal de paiement
const openPaymentModal = (client) => {
  selectedClient.value = client
  showPaymentModal.value = true
}

// Fermer le modal de paiement
const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedClient.value = null
  errorMessage.value = ''
}

// Gérer le succès d'un paiement
const handlePaymentSuccess = async (data) => {
  successMessage.value = data.message
  await loadClientsWithCredits()

  // Effacer le message après 3 secondes
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// Computed properties
const totalCredits = computed(() => {
  return clientsWithCredits.value.reduce((total, client) => total + parseFloat(client.total_due), 0)
})

const totalUnpaidSales = computed(() => {
  return clientsWithCredits.value.reduce((total, client) => total + client.unpaid_sales_count, 0)
})

const averageCredit = computed(() => {
  if (clientsWithCredits.value.length === 0) return 0
  return totalCredits.value / clientsWithCredits.value.length
})

import { formatPrice } from '../../utils/currency.js'

// Fonction utilitaire pour formater les dates
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Charger les données au montage du composant
onMounted(() => {
  loadClientsWithCredits()
})
</script>
