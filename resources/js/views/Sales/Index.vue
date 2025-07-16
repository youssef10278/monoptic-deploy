<template>
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 md:mb-8 space-y-4 md:space-y-0">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Historique des Ventes</h1>
        <p class="mt-1 md:mt-2 text-sm md:text-base text-gray-600">Consultez toutes les ventes réalisées</p>
      </div>
      <div class="w-full md:w-auto">
        <router-link
          to="/pos"
          class="inline-flex items-center justify-center w-full md:w-auto px-4 py-3 md:py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nouvelle Vente
        </router-link>
      </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow rounded-lg mb-6">
      <!-- Header des filtres avec bouton toggle sur mobile -->
      <div class="md:hidden p-4 border-b border-gray-200">
        <button
          @click="showFilters = !showFilters"
          class="flex items-center justify-between w-full text-left"
        >
          <span class="text-sm font-medium text-gray-700">Filtres</span>
          <svg
            :class="showFilters ? 'rotate-180' : ''"
            class="w-4 h-4 text-gray-500 transition-transform"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
      </div>

      <!-- Contenu des filtres -->
      <div :class="showFilters || !isMobile ? 'block' : 'hidden'" class="p-4 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Recherche rapide sur mobile -->
          <div v-if="isMobile" class="md:hidden">
            <label for="quick-search" class="block text-sm font-medium text-gray-700 mb-1">
              Recherche rapide
            </label>
            <input
              id="quick-search"
              v-model="quickSearch"
              type="text"
              placeholder="Numéro de vente, client..."
              class="block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base"
            >
          </div>

          <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
              Statut
            </label>
            <select
              id="status-filter"
              v-model="filters.status"
              class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
            >
              <option value="">Tous les statuts</option>
              <option value="devis">Devis</option>
              <option value="en_commande">En commande</option>
              <option value="pret_pour_livraison">Prêt pour livraison</option>
              <option value="livre">Livré</option>
              <option value="annule">Annulé</option>
            </select>
          </div>

          <div>
            <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
              Du
            </label>
            <input
              id="date-from"
              v-model="filters.dateFrom"
              type="date"
              class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
            >
          </div>

          <div>
            <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
              Au
            </label>
            <input
              id="date-to"
              v-model="filters.dateTo"
              type="date"
              class="block w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base md:text-sm"
            >
          </div>

          <div class="flex items-end">
            <button
              @click="loadSales"
              class="w-full inline-flex justify-center items-center px-4 py-3 md:py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Filtrer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des ventes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="p-8 text-center">
        <div class="inline-flex items-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Chargement...
        </div>
      </div>

      <div v-else-if="sales.length === 0" class="p-8 text-center text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <p class="mt-2">Aucune vente trouvée</p>
        <p class="text-sm">Créez votre première vente avec le point de vente</p>
      </div>

      <!-- Vue mobile : Cards -->
      <div v-else-if="isMobile" class="space-y-4">
        <div
          v-for="sale in sales"
          :key="sale.id"
          class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
        >
          <!-- Header de la card -->
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                   :class="getStatusClasses(sale.status)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="sale.status === 'livre'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  <path v-else-if="sale.status === 'devis'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-900">Vente #{{ sale.id }}</p>
                <p class="text-xs text-gray-500">{{ formatDate(sale.created_at) }}</p>
              </div>
            </div>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  :class="getStatusBadgeClasses(sale.status)">
              {{ getStatusLabel(sale.status) }}
            </span>
          </div>

          <!-- Informations client -->
          <div class="mb-3">
            <div class="flex items-center text-sm text-gray-600">
              <svg v-if="sale.client" class="w-4 h-4 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <svg v-else class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <span class="truncate">{{ sale.client ? sale.client.name : 'Client de passage' }}</span>
            </div>
          </div>

          <!-- Informations financières et articles -->
          <div class="flex items-center justify-between mb-3">
            <div class="text-sm text-gray-600">
              <span class="font-medium">{{ sale.sale_items?.length || 0 }}</span> article(s)
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-900">{{ formatPrice(sale.total_amount) }}</p>
              <p class="text-xs text-gray-500">Payé: {{ formatPrice(sale.paid_amount || 0) }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end space-x-3 pt-2 border-t border-gray-100">
            <button
              @click="viewSale(sale)"
              class="flex items-center px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              Voir
            </button>

            <button
              v-if="sale.status !== 'livre' && sale.status !== 'annule'"
              @click="updateStatus(sale)"
              class="flex items-center px-3 py-2 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded-md transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Modifier
            </button>
          </div>
        </div>
      </div>

      <!-- Vue desktop : Liste -->
      <ul v-else class="divide-y divide-gray-200">
        <li
          v-for="sale in sales"
          :key="sale.id"
          class="px-6 py-4 hover:bg-gray-50"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center"
                       :class="getStatusClasses(sale.status)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="sale.status === 'livre'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      <path v-else-if="sale.status === 'devis'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium text-gray-900">
                      Vente #{{ sale.id }}
                    </p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadgeClasses(sale.status)">
                      {{ getStatusLabel(sale.status) }}
                    </span>
                  </div>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center">
                      <svg v-if="sale.client" class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <svg v-else class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                      </svg>
                      <span>{{ sale.client ? sale.client.name : 'Vente rapide (client de passage)' }}</span>
                    </div>
                    <p>{{ sale.sale_items?.length || 0 }} article(s)</p>
                    <p>{{ formatDate(sale.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ formatPrice(sale.total_amount) }}</p>
                <p class="text-xs text-gray-500">
                  Payé: {{ formatPrice(sale.paid_amount || 0) }}
                </p>
              </div>

              <div class="flex items-center space-x-2">
                <button
                  @click="viewSale(sale)"
                  class="text-blue-600 hover:text-blue-800"
                  title="Voir les détails"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>

                <button
                  v-if="sale.status !== 'livre' && sale.status !== 'annule'"
                  @click="updateStatus(sale)"
                  class="text-green-600 hover:text-green-800"
                  title="Changer le statut"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Modal des détails de vente -->
    <SaleDetailsModal
      :is-open="showDetailsModal"
      :sale="selectedSale"
      :loading="loadingDetails"
      @close="closeDetailsModal"
      @update-status="openChangeStatusModal"
    />

    <!-- Modal de changement de statut -->
    <ChangeStatusModal
      :is-open="showChangeStatusModal"
      :sale="selectedSale"
      :loading="updatingStatus"
      @close="closeChangeStatusModal"
      @update-status="handleStatusUpdate"
    />

    <!-- Bouton d'action flottant sur mobile -->
    <div v-if="isMobile" class="fixed bottom-6 right-6 z-50">
      <router-link
        to="/pos"
        class="bg-green-600 hover:bg-green-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110"
        title="Nouvelle vente"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import SaleDetailsModal from '../../Components/SaleDetailsModal.vue'
import ChangeStatusModal from '../../Components/ChangeStatusModal.vue'
import { formatPrice } from '../../utils/currency.js'

// État réactif
const sales = ref([])
const loading = ref(true)
const filters = ref({
  status: '',
  dateFrom: '',
  dateTo: ''
})

// État pour la modal des détails
const showDetailsModal = ref(false)
const selectedSale = ref(null)
const loadingDetails = ref(false)

// État pour la modal de changement de statut
const showChangeStatusModal = ref(false)
const updatingStatus = ref(false)

// Méthodes
const loadSales = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/sales', { params: filters.value })
    if (response.data.success) {
      sales.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des ventes:', error)
  } finally {
    loading.value = false
  }
}

const getStatusClasses = (status) => {
  const classes = {
    devis: 'bg-yellow-100 text-yellow-600',
    en_commande: 'bg-blue-100 text-blue-600',
    pret_pour_livraison: 'bg-purple-100 text-purple-600',
    livre: 'bg-green-100 text-green-600',
    annule: 'bg-red-100 text-red-600'
  }
  return classes[status] || 'bg-gray-100 text-gray-600'
}

const getStatusBadgeClasses = (status) => {
  const classes = {
    devis: 'bg-yellow-100 text-yellow-800',
    en_commande: 'bg-blue-100 text-blue-800',
    pret_pour_livraison: 'bg-purple-100 text-purple-800',
    livre: 'bg-green-100 text-green-800',
    annule: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    devis: 'Devis',
    en_commande: 'En commande',
    pret_pour_livraison: 'Prêt pour livraison',
    livre: 'Livré',
    annule: 'Annulé'
  }
  return labels[status] || status
}

// La fonction formatPrice est maintenant importée depuis utils/currency.js

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const viewSale = async (sale) => {
  try {
    loadingDetails.value = true
    showDetailsModal.value = true

    // Charger les détails complets de la vente
    const response = await axios.get(`/api/sales/${sale.id}`)

    if (response.data.success) {
      selectedSale.value = response.data.data
    } else {
      console.error('Erreur lors du chargement des détails:', response.data.message)
      alert('Erreur lors du chargement des détails de la vente')
      showDetailsModal.value = false
    }
  } catch (error) {
    console.error('Erreur lors du chargement des détails:', error)
    alert('Erreur lors du chargement des détails de la vente')
    showDetailsModal.value = false
  } finally {
    loadingDetails.value = false
  }
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedSale.value = null
}

const openChangeStatusModal = (sale) => {
  // Garder la vente sélectionnée et ouvrir la modal de changement de statut
  showChangeStatusModal.value = true
}

const closeChangeStatusModal = () => {
  showChangeStatusModal.value = false
}

const handleStatusUpdate = async (data) => {
  try {
    updatingStatus.value = true

    const response = await axios.put(`/api/sales/${data.saleId}/status`, {
      status: data.newStatus,
      comment: data.comment
    })

    if (response.data.success) {
      // Mettre à jour la vente dans la liste
      const saleIndex = sales.value.findIndex(s => s.id === data.saleId)
      if (saleIndex !== -1) {
        sales.value[saleIndex] = response.data.data
      }

      // Mettre à jour la vente sélectionnée si elle est affichée
      if (selectedSale.value && selectedSale.value.id === data.saleId) {
        selectedSale.value = response.data.data
      }

      // Fermer la modal de changement de statut
      closeChangeStatusModal()

      // Afficher un message de succès
      alert('Statut mis à jour avec succès !')
    } else {
      alert('Erreur lors de la mise à jour du statut: ' + response.data.message)
    }
  } catch (error) {
    console.error('Erreur lors de la mise à jour du statut:', error)
    if (error.response?.data?.message) {
      alert('Erreur: ' + error.response.data.message)
    } else {
      alert('Erreur lors de la mise à jour du statut')
    }
  } finally {
    updatingStatus.value = false
  }
}

const updateStatus = (sale) => {
  // Cette méthode est appelée depuis le bouton dans la liste
  selectedSale.value = sale
  openChangeStatusModal(sale)
}

// Mobile responsive
const showFilters = ref(false)
const quickSearch = ref('')
const isMobile = ref(false)

// Détecter si on est sur mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

// Lifecycle
onMounted(() => {
  loadSales()

  // Détecter la taille d'écran
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

// Nettoyage
onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
/* Améliorations mobile pour l'historique des ventes */
@media (max-width: 768px) {
  /* Améliorer la zone tactile des boutons */
  button {
    min-height: 44px;
  }

  /* Optimiser les inputs pour mobile */
  input[type="date"], select {
    font-size: 16px; /* Évite le zoom sur iOS */
  }

  /* Améliorer l'affichage des cards de vente */
  .sale-card {
    padding: 16px;
    border-radius: 12px;
  }

  /* Optimiser les badges de statut */
  .status-badge {
    font-size: 11px;
    padding: 4px 8px;
  }
}

/* Animation pour l'accordéon des filtres */
.filter-toggle {
  transition: all 0.3s ease-in-out;
}

/* Améliorer la visibilité des statuts */
.status-indicator {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Hover states pour les boutons d'action */
.action-button {
  transition: all 0.2s ease-in-out;
}

.action-button:hover {
  transform: translateY(-1px);
}

/* Améliorer la lisibilité des montants */
.amount-text {
  font-variant-numeric: tabular-nums;
}
</style>
