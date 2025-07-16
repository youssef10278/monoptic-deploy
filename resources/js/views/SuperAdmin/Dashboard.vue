<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Tableau de Bord Super Administrateur</h1>
            <p class="text-gray-600">Vue d'ensemble de la plateforme MonOptic</p>
          </div>
          <div class="flex space-x-2">
            <button
              @click="refreshData"
              :disabled="isLoading"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-1" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Actualiser
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Indicateur de chargement global -->
    <div v-if="isLoading && !dashboardData" class="flex justify-center items-center py-12">
      <div class="text-center">
        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600">Chargement du tableau de bord...</p>
      </div>
    </div>

    <!-- KPIs principaux -->
    <div v-if="dashboardData" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Nombre total de tenants -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Total Tenants
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.total_tenants || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Tenants actifs -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Tenants Actifs
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.active_tenants || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- MRR -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  MRR (Revenu Mensuel)
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ formatPrice(dashboardData?.kpis?.mrr || 0) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenus business -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  CA Total Clients
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ formatPrice(dashboardData?.kpis?.total_business_revenue || 0) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPIs secondaires -->
    <div v-if="dashboardData" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Tenants en trial -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  En Période d'Essai
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.trial_tenants || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Tenants expirés -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Abonnements Expirés
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.expired_tenants || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Total utilisateurs -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Total Utilisateurs
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.total_users || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Total ventes -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Total Ventes
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ dashboardData?.kpis?.total_sales || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulaire d'ajout d'un nouvel opticien -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Ajouter un nouvel opticien</h2>

        <form @submit.prevent="createTenant" class="space-y-4">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label for="store_name" class="block text-sm font-medium text-gray-700">Nom du magasin</label>
              <input
                id="store_name"
                v-model="tenantForm.store_name"
                type="text"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Ex: Optique Centrale"
              />
            </div>

            <div>
              <label for="admin_name" class="block text-sm font-medium text-gray-700">Nom de l'administrateur</label>
              <input
                id="admin_name"
                v-model="tenantForm.admin_name"
                type="text"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Ex: Jean Dupont"
              />
            </div>

            <div>
              <label for="admin_email" class="block text-sm font-medium text-gray-700">Email de l'administrateur</label>
              <input
                id="admin_email"
                v-model="tenantForm.admin_email"
                type="email"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="admin@optique-centrale.fr"
              />
            </div>

            <div>
              <label for="admin_password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
              <input
                id="admin_password"
                v-model="tenantForm.admin_password"
                type="password"
                required
                minlength="6"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Minimum 6 caractères"
              />
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="isCreatingTenant"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isCreatingTenant" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Création...
              </span>
              <span v-else>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Créer l'opticien
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Table des tenants -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Gestion des Tenants</h2>
        
        <div v-if="isLoading" class="flex justify-center py-8">
          <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <div v-else-if="dashboardData?.tenants?.length === 0" class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun tenant</h3>
          <p class="mt-1 text-sm text-gray-500">Aucun magasin n'est encore enregistré sur la plateforme.</p>
        </div>

        <div v-else-if="dashboardData && dashboardData.tenants" class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Magasin
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Statut
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fin d'abonnement
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Activité
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  CA Total
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="tenant in dashboardData.tenants" :key="tenant.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ tenant.name }}</div>
                    <div class="text-sm text-gray-500">
                      {{ tenant.users_count }} utilisateur(s) • {{ tenant.clients_count }} client(s)
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(tenant.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ getStatusLabel(tenant.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(tenant.subscription_end_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div class="space-y-1">
                    <div>{{ tenant.sales_count }} vente(s)</div>
                    <div>{{ tenant.products_count }} produit(s)</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatPrice(tenant.total_revenue) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex space-x-2">
                    <button
                      @click="openUserModal(tenant)"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Modifier Admin
                    </button>
                    <button
                      @click="openSubscriptionModal(tenant)"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      Gérer l'abonnement
                    </button>
                  </div>
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

    <!-- Formulaire de gestion des abonnements -->
    <SubscriptionForm
      :show="showSubscriptionModal"
      :tenant="selectedTenant"
      @close="closeSubscriptionModal"
      @success="handleSubscriptionSuccess"
    />

    <!-- Formulaire de modification d'utilisateur -->
    <UserForm
      :show="showUserModal"
      :tenant="selectedTenantForUser"
      @close="closeUserModal"
      @success="handleUserSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import SubscriptionForm from './SubscriptionForm.vue'
import UserForm from './UserForm.vue'

// État réactif
const dashboardData = ref(null)
const isLoading = ref(false)
const isCreatingTenant = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showSubscriptionModal = ref(false)
const selectedTenant = ref(null)
const showUserModal = ref(false)
const selectedTenantForUser = ref(null)

// Formulaire de création de tenant
const tenantForm = ref({
  store_name: '',
  admin_name: '',
  admin_email: '',
  admin_password: ''
})

// Charger les données du tableau de bord
const loadDashboardData = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.get('/api/super-admin/dashboard')
    if (response.data.success) {
      dashboardData.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement du tableau de bord:', error)
    if (error.response?.status === 403) {
      errorMessage.value = 'Accès refusé. Vous devez être Super Administrateur pour accéder à cette page.'
    } else {
      errorMessage.value = 'Erreur lors du chargement des données du tableau de bord'
    }
  } finally {
    isLoading.value = false
  }
}

// Actualiser les données
const refreshData = async () => {
  await loadDashboardData()
}

// Ouvrir le modal de gestion d'abonnement
const openSubscriptionModal = (tenant) => {
  selectedTenant.value = tenant
  showSubscriptionModal.value = true
}

// Fermer le modal de gestion d'abonnement
const closeSubscriptionModal = () => {
  showSubscriptionModal.value = false
  selectedTenant.value = null
}

// Ouvrir le modal de modification d'utilisateur
const openUserModal = async (tenant) => {
  // Charger les utilisateurs du tenant si pas déjà chargés
  if (!tenant.users) {
    try {
      const response = await axios.get(`/api/tenants/${tenant.id}`)
      if (response.data.success) {
        tenant.users = response.data.data.users || []
      }
    } catch (error) {
      console.error('Erreur lors du chargement des utilisateurs:', error)
    }
  }

  selectedTenantForUser.value = tenant
  showUserModal.value = true
}

// Fermer le modal de modification d'utilisateur
const closeUserModal = () => {
  showUserModal.value = false
  selectedTenantForUser.value = null
}

// Gérer le succès de mise à jour d'utilisateur
const handleUserSuccess = async (data) => {
  successMessage.value = data.message
  await loadDashboardData()

  // Effacer le message après 3 secondes
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// Créer un nouveau tenant
const createTenant = async () => {
  if (isCreatingTenant.value) return

  isCreatingTenant.value = true
  errorMessage.value = ''

  try {
    const response = await axios.post('/api/tenants', tenantForm.value)

    if (response.data.success) {
      successMessage.value = 'Opticien créé avec succès !'

      // Réinitialiser le formulaire
      tenantForm.value = {
        store_name: '',
        admin_name: '',
        admin_email: '',
        admin_password: ''
      }

      // Recharger les données
      await loadDashboardData()

      // Effacer le message après 5 secondes
      setTimeout(() => {
        successMessage.value = ''
      }, 5000)
    }
  } catch (error) {
    console.error('Erreur lors de la création du tenant:', error)

    if (error.response?.data?.errors) {
      // Erreurs de validation
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat()
      errorMessage.value = errorMessages.join(', ')
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Erreur lors de la création de l\'opticien'
    }
  } finally {
    isCreatingTenant.value = false
  }
}

// Gérer le succès de mise à jour d'abonnement
const handleSubscriptionSuccess = async (data) => {
  successMessage.value = data.message
  await loadDashboardData()

  // Effacer le message après 3 secondes
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// Obtenir la classe CSS pour le badge de statut
const getStatusBadgeClass = (status) => {
  const classes = {
    'trial': 'bg-yellow-100 text-yellow-800',
    'active': 'bg-green-100 text-green-800',
    'suspended': 'bg-red-100 text-red-800',
    'cancelled': 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Obtenir le libellé du statut
const getStatusLabel = (status) => {
  const labels = {
    'trial': 'Période d\'essai',
    'active': 'Actif',
    'suspended': 'Suspendu',
    'cancelled': 'Annulé'
  }
  return labels[status] || status
}

// Fonction utilitaire pour formater les prix
const formatPrice = (price) => {
  if (!price) return '0,00 €'
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

// Fonction utilitaire pour formater les dates
const formatDate = (dateString) => {
  if (!dateString) return 'Aucune limite'
  const date = new Date(dateString)
  const now = new Date()

  // Vérifier si la date est dans le passé
  const isPast = date < now
  const formattedDate = date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })

  return isPast ? `⚠️ ${formattedDate}` : formattedDate
}

// Charger les données au montage du composant
onMounted(() => {
  loadDashboardData()
})
</script>
