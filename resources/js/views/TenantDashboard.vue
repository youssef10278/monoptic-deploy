<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Tableau de Bord</h1>
            <p class="text-gray-600">Bienvenue sur votre tableau de bord MonOptic</p>
          </div>
          <div class="flex items-center space-x-4">
            <div v-if="user" class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
              <p class="text-sm text-gray-500">{{ user.tenant?.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Clients -->
      <StatCardSkeleton v-if="isLoadingStats" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Clients
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.clients }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Produits -->
      <StatCardSkeleton v-if="isLoadingStats" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Produits
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.products }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Ventes -->
      <StatCardSkeleton v-if="isLoadingStats" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
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
                  Ventes
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.sales }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Chiffre d'affaires -->
      <StatCardSkeleton v-if="isLoadingStats" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Chiffre d'Affaires
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ formatPrice(stats.revenue) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <router-link
            to="/clients"
            class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
          >
            <div>
              <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </span>
            </div>
            <div class="mt-8">
              <h3 class="text-lg font-medium">
                <span class="absolute inset-0" aria-hidden="true"></span>
                Gérer les Clients
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Ajouter, modifier et consulter vos clients
              </p>
            </div>
          </router-link>

          <router-link
            to="/stock"
            class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
          >
            <div>
              <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </span>
            </div>
            <div class="mt-8">
              <h3 class="text-lg font-medium">
                <span class="absolute inset-0" aria-hidden="true"></span>
                Gérer le Stock
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Gérer vos produits et catégories
              </p>
            </div>
          </router-link>

          <router-link
            to="/pos"
            class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
          >
            <div>
              <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-700 ring-4 ring-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </span>
            </div>
            <div class="mt-8">
              <h3 class="text-lg font-medium">
                <span class="absolute inset-0" aria-hidden="true"></span>
                Nouvelle Vente
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Effectuer une vente en caisse
              </p>
            </div>
          </router-link>

          <router-link
            to="/reports"
            class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
          >
            <div>
              <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                </svg>
              </span>
            </div>
            <div class="mt-8">
              <h3 class="text-lg font-medium">
                <span class="absolute inset-0" aria-hidden="true"></span>
                Rapports
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                Consulter vos analyses et rapports
              </p>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { formatPrice } from '../utils/currency.js'
import StatCardSkeleton from '../Components/LoadingStates/StatCardSkeleton.vue'

// États de chargement
const isLoadingStats = ref(true)
const isLoadingUser = ref(true)

// État réactif
const user = ref(null)
const stats = ref({
  clients: 0,
  products: 0,
  sales: 0,
  revenue: 0
})

// Filtre de dates
const dateFilter = ref({
  period: 'month',
  dateFrom: null,
  dateTo: null
})

// Charger les informations utilisateur
const loadUserInfo = async () => {
  isLoadingUser.value = true
  try {
    const response = await axios.get('/api/user')
    if (response.data.success) {
      user.value = response.data.data.user
    }
  } catch (error) {
    console.error('Erreur lors du chargement des informations utilisateur:', error)
  } finally {
    isLoadingUser.value = false
  }
}

// Charger les statistiques
const loadStats = async () => {
  isLoadingStats.value = true
  try {
    const response = await axios.get('/api/dashboard')
    if (response.data.success) {
      stats.value = response.data.data.stats
    }
  } catch (error) {
    console.error('Erreur lors du chargement des statistiques:', error)
    // Valeurs par défaut en cas d'erreur
    stats.value = {
      clients: 0,
      products: 0,
      sales: 0,
      revenue: 0
    }
  } finally {
    isLoadingStats.value = false
  }
}

// Charger les données au montage du composant
onMounted(async () => {
  // Charger les données en parallèle pour optimiser
  await Promise.all([
    loadUserInfo(),
    loadStats()
  ])
})
</script>
