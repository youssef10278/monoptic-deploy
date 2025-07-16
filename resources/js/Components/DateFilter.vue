<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <!-- Boutons de période prédéfinie -->
      <div class="flex flex-wrap gap-2">
        <button
          v-for="periodOption in periodOptions"
          :key="periodOption.value"
          @click="selectPeriod(periodOption.value)"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
            selectedPeriod === periodOption.value
              ? 'bg-blue-600 text-white shadow-sm'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ periodOption.label }}
        </button>
      </div>

      <!-- Sélecteur de dates personnalisées -->
      <div v-if="selectedPeriod === 'custom'" class="flex items-center gap-3">
        <div class="flex items-center gap-2">
          <label class="text-sm font-medium text-gray-700">Du :</label>
          <input
            v-model="customDateFrom"
            type="date"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="updateCustomDates"
          >
        </div>
        <div class="flex items-center gap-2">
          <label class="text-sm font-medium text-gray-700">Au :</label>
          <input
            v-model="customDateTo"
            type="date"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="updateCustomDates"
          >
        </div>
      </div>

      <!-- Indicateur de période actuelle -->
      <div class="text-sm text-gray-600 font-medium">
        {{ currentPeriodLabel }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      period: 'month',
      dateFrom: null,
      dateTo: null
    })
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'change'])

// État réactif
const selectedPeriod = ref(props.modelValue.period || 'month')
const customDateFrom = ref('')
const customDateTo = ref('')

// Options de période
const periodOptions = [
  { value: 'today', label: "Aujourd'hui" },
  { value: 'week', label: 'Cette semaine' },
  { value: 'month', label: 'Ce mois' },
  { value: 'custom', label: 'Personnalisé' }
]

// Libellé de la période actuelle
const currentPeriodLabel = computed(() => {
  const today = new Date()
  
  switch (selectedPeriod.value) {
    case 'today':
      return `Aujourd'hui (${formatDate(today)})`
    case 'week':
      const startOfWeek = getStartOfWeek(today)
      const endOfWeek = getEndOfWeek(today)
      return `Cette semaine (${formatDate(startOfWeek)} - ${formatDate(endOfWeek)})`
    case 'month':
      const monthName = today.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
      return `Ce mois (${monthName})`
    case 'custom':
      if (customDateFrom.value && customDateTo.value) {
        return `Du ${formatDate(new Date(customDateFrom.value))} au ${formatDate(new Date(customDateTo.value))}`
      }
      return 'Période personnalisée'
    default:
      return 'Période sélectionnée'
  }
})

// Méthodes
const selectPeriod = (period) => {
  selectedPeriod.value = period
  
  if (period !== 'custom') {
    customDateFrom.value = ''
    customDateTo.value = ''
  } else {
    // Initialiser avec le mois en cours pour la période personnalisée
    const today = new Date()
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0)
    
    customDateFrom.value = formatDateInput(startOfMonth)
    customDateTo.value = formatDateInput(endOfMonth)
  }
  
  emitChange()
}

const updateCustomDates = () => {
  if (selectedPeriod.value === 'custom' && customDateFrom.value && customDateTo.value) {
    emitChange()
  }
}

const emitChange = () => {
  const filterData = {
    period: selectedPeriod.value,
    dateFrom: selectedPeriod.value === 'custom' ? customDateFrom.value : null,
    dateTo: selectedPeriod.value === 'custom' ? customDateTo.value : null
  }
  
  emit('update:modelValue', filterData)
  emit('change', filterData)
}

// Fonctions utilitaires
const formatDate = (date) => {
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatDateInput = (date) => {
  return date.toISOString().split('T')[0]
}

const getStartOfWeek = (date) => {
  const d = new Date(date)
  const day = d.getDay()
  const diff = d.getDate() - day + (day === 0 ? -6 : 1) // Lundi comme premier jour
  return new Date(d.setDate(diff))
}

const getEndOfWeek = (date) => {
  const startOfWeek = getStartOfWeek(date)
  const endOfWeek = new Date(startOfWeek)
  endOfWeek.setDate(startOfWeek.getDate() + 6)
  return endOfWeek
}

// Watcher pour les changements de props
watch(() => props.modelValue, (newValue) => {
  if (newValue.period !== selectedPeriod.value) {
    selectedPeriod.value = newValue.period
  }
  if (newValue.dateFrom) {
    customDateFrom.value = newValue.dateFrom
  }
  if (newValue.dateTo) {
    customDateTo.value = newValue.dateTo
  }
}, { deep: true })

// Initialisation
if (props.modelValue.period === 'custom' && props.modelValue.dateFrom && props.modelValue.dateTo) {
  customDateFrom.value = props.modelValue.dateFrom
  customDateTo.value = props.modelValue.dateTo
}
</script>
