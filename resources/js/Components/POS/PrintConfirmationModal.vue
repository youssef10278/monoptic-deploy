<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full modal-content">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-lg font-medium text-gray-900">
              {{ title }}
            </h3>
            <p class="text-sm text-gray-500">
              {{ subtitle }}
            </p>
          </div>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 modal-scrollable p-6">
        <!-- Résumé de l'opération -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <h4 class="text-sm font-medium text-gray-900 mb-3">Résumé de l'opération</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">{{ operationType === 'sale' ? 'Vente' : 'Devis' }} N°:</span>
              <span class="font-medium">#{{ operationId }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Client:</span>
              <span class="font-medium">{{ clientName || 'Client de passage' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Articles:</span>
              <span class="font-medium">{{ itemCount }} article(s)</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Montant total:</span>
              <span class="font-medium text-green-600">{{ formatPrice(totalAmount) }}</span>
            </div>
            <div v-if="operationType === 'sale' && paidAmount < totalAmount" class="flex justify-between">
              <span class="text-gray-600">Montant payé:</span>
              <span class="font-medium">{{ formatPrice(paidAmount) }}</span>
            </div>
            <div v-if="operationType === 'sale' && remainingAmount > 0" class="flex justify-between">
              <span class="text-gray-600">Reste à payer:</span>
              <span class="font-medium text-orange-600">{{ formatPrice(remainingAmount) }}</span>
            </div>
          </div>
        </div>

        <!-- Options d'impression -->
        <div class="mb-6">
          <h4 class="text-sm font-medium text-gray-900 mb-3">Souhaitez-vous imprimer un document ?</h4>
          
          <div class="space-y-3">
            <!-- Option: Pas d'impression -->
            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
              <input 
                v-model="selectedOption" 
                type="radio" 
                value="none" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              >
              <div class="ml-3">
                <div class="text-sm font-medium text-gray-900">Pas d'impression</div>
                <div class="text-xs text-gray-500">Continuer sans imprimer de document</div>
              </div>
            </label>

            <!-- Option: Imprimer ticket (pour ventes) -->
            <label 
              v-if="operationType === 'sale'" 
              class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <input 
                v-model="selectedOption" 
                type="radio" 
                value="ticket" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              >
              <div class="ml-3 flex-1">
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <div class="text-sm font-medium text-gray-900">Imprimer ticket de vente</div>
                </div>
                <div class="text-xs text-gray-500 mt-1">Document officiel de la vente avec détails du paiement</div>
              </div>
            </label>

            <!-- Option: Imprimer devis (pour devis) -->
            <label 
              v-if="operationType === 'quote'" 
              class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <input 
                v-model="selectedOption" 
                type="radio" 
                value="devis" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              >
              <div class="ml-3 flex-1">
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <div class="text-sm font-medium text-gray-900">Imprimer devis</div>
                </div>
                <div class="text-xs text-gray-500 mt-1">Proposition commerciale avec conditions et signature</div>
              </div>
            </label>

            <!-- Option: Imprimer reçu (pour paiements partiels) -->
            <label 
              v-if="operationType === 'sale' && remainingAmount > 0" 
              class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <input 
                v-model="selectedOption" 
                type="radio" 
                value="receipt" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              >
              <div class="ml-3 flex-1">
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  <div class="text-sm font-medium text-gray-900">Imprimer reçu d'acompte</div>
                </div>
                <div class="text-xs text-gray-500 mt-1">Justificatif du paiement partiel effectué</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Options d'impression rapide -->
        <div v-if="selectedOption !== 'none'" class="mb-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
          <div class="flex items-center mb-2">
            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-sm font-medium text-blue-900">Options d'impression</span>
          </div>
          
          <div class="space-y-2">
            <label class="flex items-center">
              <input v-model="printOptions.showLogo" type="checkbox" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
              <span class="ml-2 text-xs text-blue-800">Inclure le logo de l'entreprise</span>
            </label>
            
            <label class="flex items-center">
              <input v-model="printOptions.showDetails" type="checkbox" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
              <span class="ml-2 text-xs text-blue-800">Afficher les détails des produits</span>
            </label>
            
            <label v-if="selectedOption === 'devis'" class="flex items-center">
              <input v-model="printOptions.showConditions" type="checkbox" class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
              <span class="ml-2 text-xs text-blue-800">Inclure les conditions générales</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex-shrink-0 flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
        <button 
          @click="$emit('close')" 
          class="px-4 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md transition-colors"
        >
          Annuler
        </button>
        
        <button 
          @click="handleConfirm" 
          class="px-6 py-2 text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors flex items-center"
        >
          <svg v-if="selectedOption !== 'none'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
          {{ selectedOption === 'none' ? 'Continuer' : 'Imprimer et Continuer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { formatPrice } from '../../utils/currency.js'

const props = defineProps({
  show: Boolean,
  operationType: {
    type: String,
    required: true,
    validator: value => ['sale', 'quote'].includes(value)
  },
  operationId: {
    type: [String, Number],
    required: true
  },
  clientName: String,
  itemCount: {
    type: Number,
    required: true
  },
  totalAmount: {
    type: Number,
    required: true
  },
  paidAmount: {
    type: Number,
    default: 0
  },
  remainingAmount: {
    type: Number,
    default: 0
  },
  saleData: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'confirm'])

// État local
const selectedOption = ref('none')
const printOptions = ref({
  showLogo: true,
  showDetails: true,
  showConditions: true
})

// Computed
const title = computed(() => {
  return props.operationType === 'sale' ? 'Vente finalisée avec succès !' : 'Devis sauvegardé avec succès !'
})

const subtitle = computed(() => {
  return props.operationType === 'sale' 
    ? 'La vente a été enregistrée dans le système.'
    : 'Le devis a été sauvegardé et peut être consulté ultérieurement.'
})

// Méthodes
const handleConfirm = () => {
  console.log('PrintConfirmationModal - handleConfirm appelé')
  console.log('PrintConfirmationModal - saleData:', props.saleData)

  emit('confirm', {
    printType: selectedOption.value,
    printOptions: printOptions.value,
    saleData: props.saleData // Transmettre les données complètes
  })
}

// Réinitialiser les options quand le modal s'ouvre
const resetOptions = () => {
  selectedOption.value = 'none'
  printOptions.value = {
    showLogo: true,
    showDetails: true,
    showConditions: true
  }
}

// Watcher pour réinitialiser quand le modal s'ouvre
watch(() => props.show, (newValue) => {
  if (newValue) {
    resetOptions()
  }
})
</script>

<style scoped>
/* Styles pour les options radio personnalisées */
input[type="radio"]:checked + div {
  @apply bg-blue-50 border-blue-200;
}

/* Animation d'entrée */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

/* Amélioration du défilement dans le modal */
.modal-content {
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.modal-scrollable {
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.modal-scrollable::-webkit-scrollbar {
  width: 6px;
}

.modal-scrollable::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 3px;
}

.modal-scrollable::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

.modal-scrollable::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Responsive pour petits écrans */
@media (max-height: 600px) {
  .modal-content {
    max-height: 95vh;
  }
}

@media (max-height: 500px) {
  .modal-content {
    max-height: 98vh;
  }
}
</style>
