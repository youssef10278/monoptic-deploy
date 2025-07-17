<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Gestion des Clients</h1>
            <p class="text-gray-600">Gérez votre base de données clients</p>
          </div>
          <button
            @click="showCreateForm = true"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nouveau Client
          </button>
        </div>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
      <StatCardSkeleton v-if="isLoading" />
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
                  Total Clients
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ clients.length }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <StatCardSkeleton v-if="isLoading" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Avec Email
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ clients.filter(c => c.email).length }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <StatCardSkeleton v-if="isLoading" />
      <div v-else class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Avec Téléphone
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ clients.filter(c => c.phone).length }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des clients -->
    <TableSkeleton v-if="isLoading" :rows="8" :columns="5" />
    <div v-else class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Liste des clients</h2>

        <EmptyState
          v-if="clients.length === 0"
          title="Aucun client"
          description="Commencez par ajouter votre premier client."
          action-text="Ajouter un client"
          @action="showCreateForm = true"
        >
          <template #icon>
            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </template>
        </EmptyState>

        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nom
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contact
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Adresse
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date d'ajout
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="client in clients" :key="client.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    <div v-if="client.email" class="flex items-center">
                      <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                      </svg>
                      {{ client.email }}
                    </div>
                    <div v-if="client.phone" class="flex items-center mt-1">
                      <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                      </svg>
                      {{ client.phone }}
                    </div>
                    <div v-if="!client.email && !client.phone" class="text-gray-500">Aucun contact</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">
                    {{ client.address || 'Non renseignée' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(client.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <router-link
                      :to="`/clients/${client.id}`"
                      class="text-green-600 hover:text-green-900"
                    >
                      Voir
                    </router-link>
                    <button
                      @click="editClient(client)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Modifier
                    </button>
                    <button
                      @click="confirmDelete(client)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Supprimer
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

    <!-- Formulaire de création/modification -->
    <ClientForm
      :show="showCreateForm"
      :client="editingClient"
      @close="closeForm"
      @success="handleFormSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ClientForm from './ClientForm.vue'
import StatCardSkeleton from '../../Components/LoadingStates/StatCardSkeleton.vue'
import TableSkeleton from '../../Components/LoadingStates/TableSkeleton.vue'
import EmptyState from '../../Components/LoadingStates/EmptyState.vue'

// État réactif
const clients = ref([])
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const showCreateForm = ref(false)
const editingClient = ref(null)

// Charger la liste des clients
const loadClients = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.get('/api/clients')
    if (response.data.success) {
      clients.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des clients:', error)
    if (error.response?.status === 403) {
      errorMessage.value = 'Accès refusé. Vous devez être associé à un magasin pour voir les clients.'
    } else {
      errorMessage.value = 'Erreur lors du chargement des clients'
    }
  } finally {
    isLoading.value = false
  }
}

// Modifier un client
const editClient = (client) => {
  editingClient.value = client
  showCreateForm.value = true
}

// Fermer le formulaire
const closeForm = () => {
  showCreateForm.value = false
  editingClient.value = null
  errorMessage.value = ''
}

// Gérer le succès du formulaire
const handleFormSuccess = async (data) => {
  successMessage.value = data.message
  await loadClients()

  // Effacer le message après 3 secondes
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// Confirmer la suppression
const confirmDelete = (client) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer le client "${client.name}" ?`)) {
    deleteClient(client.id)
  }
}

// Supprimer un client
const deleteClient = async (clientId) => {
  try {
    const response = await axios.delete(`/api/clients/${clientId}`)
    if (response.data.success) {
      successMessage.value = 'Client supprimé avec succès'
      await loadClients()

      // Effacer le message après 3 secondes
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors de la suppression du client'
  }
}

// Fonction utilitaire pour formater les dates
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Charger les données au montage du composant
onMounted(() => {
  loadClients()
})
</script>
