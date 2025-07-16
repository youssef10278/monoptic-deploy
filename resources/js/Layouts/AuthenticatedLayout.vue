<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
      <div class="flex items-center justify-center h-16 bg-blue-600">
        <h1 class="text-white text-xl font-bold">Monoptic</h1>
      </div>

      <nav class="mt-8">
        <div class="px-4 space-y-1">
          <!-- Tableau de bord -->
          <router-link
            to="/dashboard"
            class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
            :class="{ 'bg-blue-100 text-blue-700': $route.name === 'DashboardHome' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
            Tableau de bord
          </router-link>

          <!-- Séparateur -->
          <div class="py-2">
            <div class="border-t border-gray-200"></div>
          </div>

          <!-- Lien POS proéminent -->
          <router-link
            to="/pos"
            class="flex items-center px-4 py-3 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium shadow-md mb-3"
            :class="{ 'bg-green-700': $route.name === 'POSIndex' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Point de Vente
          </router-link>

          <!-- Section Gestion -->
          <div class="space-y-1">

            <router-link
              to="/clients"
              class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
              :class="{ 'bg-blue-100 text-blue-700': $route.name === 'ClientsIndex' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              Clients
            </router-link>

            <router-link
              to="/stock"
              class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
              :class="{ 'bg-blue-100 text-blue-700': $route.name === 'StockIndex' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              Stock
            </router-link>
          </div>

          <!-- Séparateur -->
          <div class="py-2">
            <div class="border-t border-gray-200"></div>
          </div>

          <!-- Section Ventes & Finances -->
          <div class="space-y-1">
            <router-link
              to="/sales"
              class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
              :class="{ 'bg-blue-100 text-blue-700': $route.name === 'SalesIndex' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Historique des Ventes
            </router-link>

            <router-link
              to="/credits"
              class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
              :class="{ 'bg-blue-100 text-blue-700': $route.name === 'CreditsIndex' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              Gestion des Crédits
            </router-link>
          </div>

          <!-- Séparateur -->
          <div class="py-2">
            <div class="border-t border-gray-200"></div>
          </div>

          <!-- Section Rapports -->
          <div class="space-y-1">
            <router-link
              to="/reports"
              class="flex items-center px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
              :class="{ 'bg-blue-100 text-blue-700': $route.name === 'ReportsIndex' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Rapports & Analyses
            </router-link>
          </div>

        </div>
      </nav>
    </div>

    <!-- Main content -->
    <div class="lg:ml-64">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-4 py-4">
          <div class="flex items-center">
            <button
              @click="sidebarOpen = !sidebarOpen"
              class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>

          <div class="flex items-center space-x-4">
            <!-- Boutons POS (affichés seulement sur la page POS) -->
            <div v-if="$route.name === 'POSIndex'" class="flex items-center space-x-3">
              <button
                @click="resetPOSSale"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Nouvelle Vente
              </button>
              <router-link
                to="/sales"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 hover:shadow-md transition-all duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Historique
              </router-link>
            </div>

            <!-- User menu -->
            <div class="relative">
              <button
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">{{ userInitials }}</span>
                  </div>
                  <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-900">{{ user?.name }}</p>
                    <p class="text-xs text-gray-500">{{ user?.role }}</p>
                  </div>
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </button>

              <!-- Dropdown menu -->
              <div
                v-show="userMenuOpen"
                @click.away="userMenuOpen = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                <router-link to="/settings/optician" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configuration Optique</router-link>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                <hr class="my-1">
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Se déconnecter
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div
      v-show="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// État réactif
const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const user = ref(null)

// Computed properties
const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  return user.value.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Charger les données utilisateur au montage
onMounted(() => {
  const userData = localStorage.getItem('user_data')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})

// Fonction de déconnexion
const handleLogout = async () => {
  try {
    // Fermer le menu utilisateur
    userMenuOpen.value = false

    // Appeler l'API de déconnexion
    await axios.post('/api/logout')

    // Supprimer les données locales
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')

    // Rediriger vers la page de connexion
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)

    // Même en cas d'erreur, on supprime les données locales et on redirige
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')
    router.push('/login')
  }
}
// Méthode pour réinitialiser la vente POS
const resetPOSSale = () => {
  // Émettre un événement global pour réinitialiser le POS
  window.dispatchEvent(new CustomEvent('reset-pos-sale'))
}
</script>
