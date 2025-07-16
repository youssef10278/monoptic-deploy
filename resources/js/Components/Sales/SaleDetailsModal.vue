<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- En-tête -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Détails de la vente #{{ sale?.id }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Informations complètes de la vente
                </p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Contenu -->
        <div v-if="sale" class="px-4 pb-4 sm:px-6 sm:pb-6">
          <!-- Informations générales -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Informations générales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Date de vente</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(sale.created_at) }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Vendeur</label>
                <p class="mt-1 text-sm text-gray-900">{{ sale.user?.name || 'Non spécifié' }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Statut</label>
                <span 
                  class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusBadgeClass(sale.status)"
                >
                  {{ getStatusLabel(sale.status) }}
                </span>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Type de vente</label>
                <p class="mt-1 text-sm text-gray-900">{{ getTypeLabel(sale.type) }}</p>
              </div>
            </div>
          </div>

          <!-- Articles vendus -->
          <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Articles vendus</h4>
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Article
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Quantité
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Prix unitaire
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Total
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in sale.sale_items" :key="item.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ item.product_name }}</div>
                        <div v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatPrice(item.unit_price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ formatPrice(item.total_price) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Résumé financier -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Résumé financier</h4>
            <div class="space-y-2">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Sous-total:</span>
                <span class="text-sm font-medium text-gray-900">{{ formatPrice(sale.total_amount) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Montant payé:</span>
                <span class="text-sm font-medium text-gray-900">{{ formatPrice(sale.paid_amount || 0) }}</span>
              </div>
              <div class="border-t border-gray-200 pt-2">
                <div class="flex justify-between items-center">
                  <span class="text-base font-medium text-gray-900">Montant restant:</span>
                  <span 
                    class="text-base font-bold"
                    :class="sale.remaining_amount > 0 ? 'text-red-600' : 'text-green-600'"
                  >
                    {{ formatPrice(sale.remaining_amount || 0) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Statut de paiement -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Statut de paiement</h4>
            <div class="flex items-center space-x-3">
              <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="getPaymentStatusClass(sale.payment_status)"
              >
                {{ getPaymentStatusLabel(sale.payment_status) }}
              </span>
              <div v-if="sale.payment_status === 'partial'" class="text-sm text-gray-600">
                {{ formatPrice(sale.paid_amount) }} payé sur {{ formatPrice(sale.total_amount) }}
              </div>
            </div>
          </div>

          <!-- Historique des paiements (si disponible) -->
          <div v-if="sale.payments && sale.payments.length > 0" class="mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Historique des paiements</h4>
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Montant
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Méthode
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Reçu par
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="payment in sale.payments" :key="payment.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDate(payment.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ formatPrice(payment.amount) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ getPaymentMethodLabel(payment.method) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ payment.user?.name || 'Non spécifié' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="closeModal"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatPrice } from '../../utils/currency.js'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  sale: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close'])

// Méthodes
const closeModal = () => {
  emit('close')
}

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

const getTypeLabel = (type) => {
  switch (type) {
    case 'dossier_lunettes':
      return 'Dossier lunettes'
    case 'vente_directe':
      return 'Vente directe'
    default:
      return 'Non spécifié'
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
      return 'Entièrement payé'
    case 'partial':
      return 'Partiellement payé'
    case 'unpaid':
      return 'Non payé'
    default:
      return 'Inconnu'
  }
}

const getPaymentMethodLabel = (method) => {
  switch (method) {
    case 'cash':
      return 'Espèces'
    case 'card':
      return 'Carte bancaire'
    case 'check':
      return 'Chèque'
    case 'transfer':
      return 'Virement'
    default:
      return 'Non spécifié'
  }
}
</script>
