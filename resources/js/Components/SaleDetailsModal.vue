<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- En-tête -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                   :class="getStatusClasses(sale?.status)">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="sale?.status === 'livre'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  <path v-else-if="sale?.status === 'devis'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Détails de la vente #{{ sale?.id }}
                </h3>
                <p class="text-sm text-gray-500">
                  {{ formatDate(sale?.created_at) }}
                </p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Statut et informations générales -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Statut</h4>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusBadgeClasses(sale?.status)">
                {{ getStatusLabel(sale?.status) }}
              </span>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Type de vente</h4>
              <p class="text-sm text-gray-900">
                {{ sale?.type === 'dossier_lunettes' ? 'Dossier Lunettes' : 'Vente Directe' }}
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Vendeur</h4>
              <p class="text-sm text-gray-900">{{ sale?.user?.name }}</p>
            </div>
          </div>

          <!-- Informations client -->
          <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Informations Client</h4>
            <div v-if="sale?.client" class="bg-blue-50 p-4 rounded-lg">
              <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h5 class="font-medium text-gray-900">{{ sale.client.name }}</h5>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div v-if="sale.client.email">
                  <span class="text-gray-600">Email:</span>
                  <span class="ml-2 text-gray-900">{{ sale.client.email }}</span>
                </div>
                <div v-if="sale.client.phone">
                  <span class="text-gray-600">Téléphone:</span>
                  <span class="ml-2 text-gray-900">{{ sale.client.phone }}</span>
                </div>
                <div v-if="sale.client.address" class="md:col-span-2">
                  <span class="text-gray-600">Adresse:</span>
                  <span class="ml-2 text-gray-900">{{ sale.client.address }}</span>
                </div>
              </div>
            </div>
            <div v-else class="bg-green-50 p-4 rounded-lg">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="text-gray-900 font-medium">Vente rapide (client de passage)</span>
              </div>
            </div>
          </div>

          <!-- Articles vendus -->
          <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Articles vendus</h4>
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in sale?.sale_items" :key="item.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ item.product?.name }}</div>
                      <div v-if="item.product?.brand" class="text-sm text-gray-500">{{ item.product.brand }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatPrice(item.price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ formatPrice(item.price * item.quantity) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Résumé financier -->
          <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Résumé financier</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <span class="text-sm text-gray-600">Total de la vente:</span>
                  <p class="text-lg font-semibold text-gray-900">{{ formatPrice(sale?.total_amount) }}</p>
                </div>
                <div>
                  <span class="text-sm text-gray-600">Montant payé:</span>
                  <p class="text-lg font-semibold text-green-600">{{ formatPrice(sale?.paid_amount || 0) }}</p>
                </div>
                <div>
                  <span class="text-sm text-gray-600">Reste à payer:</span>
                  <p class="text-lg font-semibold" 
                     :class="(sale?.total_amount - (sale?.paid_amount || 0)) > 0 ? 'text-red-600' : 'text-green-600'">
                    {{ formatPrice((sale?.total_amount || 0) - (sale?.paid_amount || 0)) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Historique des statuts -->
          <div v-if="sale?.status_history && sale.status_history.length > 0" class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-3">Historique des statuts</h4>
            <div class="flow-root">
              <ul class="-mb-8">
                <li v-for="(history, index) in sale.status_history" :key="index" class="relative pb-8">
                  <div v-if="index !== sale.status_history.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                  <div class="relative flex space-x-3">
                    <div>
                      <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                            :class="getStatusClasses(history.status)">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="history.status === 'livre'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-500">
                          Statut changé vers <span class="font-medium text-gray-900">{{ getStatusLabel(history.status) }}</span>
                        </p>
                        <p v-if="history.comment" class="text-sm text-gray-500 mt-1">{{ history.comment }}</p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500">
                        <time>{{ formatDate(history.created_at) }}</time>
                        <p v-if="history.user">Par {{ history.user.name }}</p>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="closeModal"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Fermer
          </button>
          <button
            v-if="sale?.status !== 'livre' && sale?.status !== 'annule'"
            @click="$emit('update-status', sale)"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Changer le statut
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatPrice } from '../utils/currency.js'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  sale: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'update-status'])

// Méthodes
const closeModal = () => {
  emit('close')
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
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
