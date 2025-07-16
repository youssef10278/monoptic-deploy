<template>
  <!-- Modal overlay -->
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="submitForm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                  Enregistrer un paiement
                </h3>

                <!-- Informations client -->
                <div v-if="client" class="mb-6 p-4 bg-blue-50 rounded-md">
                  <h4 class="font-medium text-blue-900">{{ client.name }}</h4>
                  <p class="text-sm text-blue-700">{{ client.email || 'Pas d\'email' }}</p>
                  <div class="mt-2 flex justify-between items-center">
                    <span class="text-sm text-blue-700">Montant dû :</span>
                    <span class="font-medium text-red-600">{{ formatPrice(client.total_due) }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm text-blue-700">Factures impayées :</span>
                    <span class="font-medium text-blue-900">{{ client.unpaid_sales_count }}</span>
                  </div>
                </div>

                <!-- Sélection de la vente -->
                <div class="mb-4">
                  <label for="sale_id" class="block text-sm font-medium text-gray-700">
                    Facture à payer <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="sale_id"
                    v-model="form.sale_id"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    @change="updateMaxAmount"
                  >
                    <option value="">Sélectionnez une facture</option>
                    <option v-for="sale in unpaidSales" :key="sale.id" :value="sale.id">
                      Facture #{{ sale.id }} - {{ formatPrice(sale.total_amount - sale.paid_amount) }} restant
                      ({{ formatDate(sale.created_at) }})
                    </option>
                  </select>
                </div>
                
                <div class="space-y-4">
                  <!-- Montant -->
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">
                      Montant du paiement <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :max="maxAmount"
                        required
                        class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="0.00"
                      />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">MAD</span>
                      </div>
                    </div>
                    <p v-if="maxAmount > 0" class="mt-1 text-sm text-gray-500">
                      Maximum : {{ formatPrice(maxAmount) }}
                    </p>
                  </div>

                  <!-- Méthode de paiement -->
                  <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">
                      Méthode de paiement <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="payment_method"
                      v-model="form.payment_method"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                      <option value="">Sélectionnez une méthode</option>
                      <option value="cash">Espèces</option>
                      <option value="card">Carte bancaire</option>
                      <option value="transfer">Virement</option>
                      <option value="check">Chèque</option>
                      <option value="other">Autre</option>
                    </select>
                  </div>
                </div>

                <!-- Messages d'erreur -->
                <div v-if="errorMessage" class="mt-4 rounded-md bg-red-50 p-4">
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
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enregistrement...
              </span>
              <span v-else>
                Enregistrer le paiement
              </span>
            </button>
            <button
              type="button"
              @click="closeModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  client: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// État réactif
const isLoading = ref(false)
const errorMessage = ref('')
const unpaidSales = ref([])
const maxAmount = ref(0)

// Formulaire
const form = ref({
  sale_id: '',
  amount: '',
  payment_method: ''
})

// Réinitialiser le formulaire (déclarée AVANT le watcher)
const resetForm = () => {
  form.value = {
    sale_id: '',
    amount: '',
    payment_method: ''
  }
  errorMessage.value = ''
  maxAmount.value = 0
}

// Charger les ventes impayées du client
const loadUnpaidSales = async () => {
  if (!props.client) return

  try {
    // Utiliser l'endpoint spécifique pour les ventes du client
    const response = await axios.get(`/api/clients/${props.client.id}/sales`)
    if (response.data.success) {
      // Filtrer les ventes qui ne sont pas entièrement payées
      unpaidSales.value = response.data.data.filter(sale =>
        sale.paid_amount < sale.total_amount
      )
    }
  } catch (error) {
    console.error('Erreur lors du chargement des ventes:', error)
    errorMessage.value = 'Erreur lors du chargement des factures'
  }
}

// Mettre à jour le montant maximum quand une vente est sélectionnée
const updateMaxAmount = () => {
  const selectedSale = unpaidSales.value.find(sale => sale.id == form.value.sale_id)
  if (selectedSale) {
    maxAmount.value = selectedSale.total_amount - selectedSale.paid_amount
    // Réinitialiser le montant si il dépasse le maximum
    if (form.value.amount > maxAmount.value) {
      form.value.amount = maxAmount.value
    }
  } else {
    maxAmount.value = 0
  }
}

// Watcher pour charger les ventes quand le client change
watch(() => props.client, (newClient) => {
  if (newClient) {
    loadUnpaidSales()
  } else {
    resetForm()
    unpaidSales.value = []
  }
}, { immediate: true })

// Watcher pour réinitialiser quand le modal se ferme
watch(() => props.show, (show) => {
  if (!show) {
    resetForm()
  }
})

// Fermer le modal
const closeModal = () => {
  resetForm()
  emit('close')
}

// Soumettre le formulaire
const submitForm = async () => {
  if (isLoading.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await axios.post('/api/payments', form.value)

    if (response.data.success) {
      emit('success', {
        message: 'Paiement enregistré avec succès',
        payment: response.data.data
      })
      closeModal()
    }
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement du paiement:', error)

    if (error.response?.data?.errors) {
      // Erreurs de validation
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat()
      errorMessage.value = errorMessages.join(', ')
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Erreur lors de l\'enregistrement du paiement'
    }
  } finally {
    isLoading.value = false
  }
}

import { formatPrice } from '../../utils/currency.js'

// Fonction utilitaire pour formater les dates
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
