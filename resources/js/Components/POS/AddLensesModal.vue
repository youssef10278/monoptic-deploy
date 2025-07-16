<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Ajouter des Verres Personnalisés
            </h3>
            <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="space-y-6">
            <!-- Corrections optiques -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-4">Corrections Optiques</h4>
              
              <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                        Œil
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                        Sphère (SPH)
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                        Cylindre (CYL)
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                        Axe (°)
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Addition (ADD)
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Œil Droit (OD) -->
                    <tr>
                      <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-300 bg-blue-50">
                        Œil Droit (OD)
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.od.sph"
                          type="number"
                          step="0.25"
                          min="-30"
                          max="30"
                          placeholder="±0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.od.cyl"
                          type="number"
                          step="0.25"
                          min="-10"
                          max="10"
                          placeholder="±0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.od.axe"
                          type="number"
                          min="0"
                          max="180"
                          placeholder="0-180"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <input
                          v-model.number="form.od.add"
                          type="number"
                          step="0.25"
                          min="0"
                          max="5"
                          placeholder="+0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                    </tr>

                    <!-- Œil Gauche (OG) -->
                    <tr>
                      <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-300 bg-green-50">
                        Œil Gauche (OG)
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.og.sph"
                          type="number"
                          step="0.25"
                          min="-30"
                          max="30"
                          placeholder="±0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.og.cyl"
                          type="number"
                          step="0.25"
                          min="-10"
                          max="10"
                          placeholder="±0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3 border-r border-gray-300">
                        <input
                          v-model.number="form.og.axe"
                          type="number"
                          min="0"
                          max="180"
                          placeholder="0-180"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <input
                          v-model.number="form.og.add"
                          type="number"
                          step="0.25"
                          min="0"
                          max="5"
                          placeholder="+0.00"
                          class="block w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Actions rapides -->
              <div class="mt-4 flex space-x-2">
                <button
                  type="button"
                  @click="copyOdToOg"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50"
                >
                  Copier OD → OG
                </button>
                <button
                  type="button"
                  @click="clearCorrections"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50"
                >
                  Effacer corrections
                </button>
              </div>
            </div>

            <!-- Matériau et traitements -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="material" class="block text-sm font-medium text-gray-700 mb-2">
                  Matériau des verres *
                </label>
                <select
                  id="material"
                  v-model="form.material"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Sélectionner un matériau</option>
                  <option value="mineral">Minéral</option>
                  <option value="organic">Organique</option>
                  <option value="polycarbonate">Polycarbonate</option>
                  <option value="trivex">Trivex</option>
                  <option value="high_index">Haut indice</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Traitements
                </label>
                <div class="space-y-3">
                  <!-- Anti-reflet -->
                  <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <label class="flex items-center">
                      <input
                        v-model="form.treatments.antireflet.selected"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      >
                      <span class="ml-2 text-sm font-medium text-gray-700">Anti-reflet</span>
                    </label>
                    <div class="flex items-center space-x-2">
                      <input
                        v-model.number="form.treatments.antireflet.price"
                        :disabled="!form.treatments.antireflet.selected"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
                      >
                      <span class="text-sm text-gray-500">MAD</span>
                    </div>
                  </div>

                  <!-- Durcissant -->
                  <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <label class="flex items-center">
                      <input
                        v-model="form.treatments.durcissant.selected"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      >
                      <span class="ml-2 text-sm font-medium text-gray-700">Durcissant</span>
                    </label>
                    <div class="flex items-center space-x-2">
                      <input
                        v-model.number="form.treatments.durcissant.price"
                        :disabled="!form.treatments.durcissant.selected"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
                      >
                      <span class="text-sm text-gray-500">MAD</span>
                    </div>
                  </div>

                  <!-- Photochromique -->
                  <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <label class="flex items-center">
                      <input
                        v-model="form.treatments.photochromique.selected"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      >
                      <span class="ml-2 text-sm font-medium text-gray-700">Photochromique</span>
                    </label>
                    <div class="flex items-center space-x-2">
                      <input
                        v-model.number="form.treatments.photochromique.price"
                        :disabled="!form.treatments.photochromique.selected"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
                      >
                      <span class="text-sm text-gray-500">€</span>
                    </div>
                  </div>

                  <!-- Anti-buée -->
                  <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <label class="flex items-center">
                      <input
                        v-model="form.treatments.antibuee.selected"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      >
                      <span class="ml-2 text-sm font-medium text-gray-700">Anti-buée</span>
                    </label>
                    <div class="flex items-center space-x-2">
                      <input
                        v-model.number="form.treatments.antibuee.price"
                        :disabled="!form.treatments.antibuee.selected"
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
                      >
                      <span class="text-sm text-gray-500">€</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Prix et quantité -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                  Quantité *
                </label>
                <input
                  id="quantity"
                  v-model.number="form.quantity"
                  type="number"
                  min="1"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="base-price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix de base (€) *
                </label>
                <input
                  id="base-price"
                  v-model.number="form.basePrice"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label for="total-price" class="block text-sm font-medium text-gray-700 mb-1">
                  Prix total (MAD)
                </label>
                <input
                  id="total-price"
                  :value="totalPrice"
                  type="number"
                  step="0.01"
                  readonly
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                >
              </div>
            </div>

            <!-- Résumé -->
            <div v-if="hasCorrections && form.material && form.basePrice > 0" class="bg-gray-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Résumé des verres</h4>
              <div class="text-sm text-gray-600 space-y-1">
                <div v-if="hasOdCorrection">
                  <strong>OD:</strong> {{ formatCorrection(form.od) }}
                </div>
                <div v-if="hasOgCorrection">
                  <strong>OG:</strong> {{ formatCorrection(form.og) }}
                </div>
                <div>
                  <strong>Matériau:</strong> {{ getMaterialLabel(form.material) }}
                </div>
                <div v-if="selectedTreatments.length > 0">
                  <strong>Traitements:</strong> {{ selectedTreatments.map(t => `${t.name} (${formatPrice(t.price)})`).join(', ') }}
                </div>
                <div>
                  <strong>Prix total:</strong> {{ formatPrice(totalPrice) }} ({{ form.quantity }} paire{{ form.quantity > 1 ? 's' : '' }})
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="addToCart"
            :disabled="!canAdd"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Ajouter au panier
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Emits
const emit = defineEmits(['close', 'add'])

// État réactif
const form = ref({
  od: {
    sph: null,
    cyl: null,
    axe: null,
    add: null
  },
  og: {
    sph: null,
    cyl: null,
    axe: null,
    add: null
  },
  material: '',
  treatments: {
    antireflet: { selected: false, price: 0 },
    durcissant: { selected: false, price: 0 },
    photochromique: { selected: false, price: 0 },
    antibuee: { selected: false, price: 0 }
  },
  quantity: 1,
  basePrice: 0
})

// Computed
const hasOdCorrection = computed(() => {
  return form.value.od.sph !== null || form.value.od.cyl !== null ||
         form.value.od.axe !== null || form.value.od.add !== null
})

const hasOgCorrection = computed(() => {
  return form.value.og.sph !== null || form.value.og.cyl !== null ||
         form.value.og.axe !== null || form.value.og.add !== null
})

const hasCorrections = computed(() => {
  return hasOdCorrection.value || hasOgCorrection.value
})

const totalPrice = computed(() => {
  let price = form.value.basePrice

  // Ajouter le coût des traitements sélectionnés
  Object.values(form.value.treatments).forEach(treatment => {
    if (treatment.selected && treatment.price > 0) {
      price += treatment.price
    }
  })

  return price * form.value.quantity
})

const selectedTreatments = computed(() => {
  const treatments = []
  Object.entries(form.value.treatments).forEach(([key, treatment]) => {
    if (treatment.selected) {
      const names = {
        antireflet: 'Anti-reflet',
        durcissant: 'Durcissant', 
        photochromique: 'Photochromique',
        antibuee: 'Anti-buée'
      }
      treatments.push({
        name: names[key],
        price: treatment.price || 0
      })
    }
  })
  return treatments
})

const canAdd = computed(() => {
  return hasCorrections.value && form.value.material && form.value.basePrice > 0 && form.value.quantity > 0
})

// Méthodes
const copyOdToOg = () => {
  form.value.og.sph = form.value.od.sph
  form.value.og.cyl = form.value.od.cyl
  form.value.og.axe = form.value.od.axe
  form.value.og.add = form.value.od.add
}

const clearCorrections = () => {
  form.value.od = { sph: null, cyl: null, axe: null, add: null }
  form.value.og = { sph: null, cyl: null, axe: null, add: null }
}

const formatCorrection = (eye) => {
  const parts = []
  if (eye.sph !== null) parts.push(`SPH: ${eye.sph >= 0 ? '+' : ''}${eye.sph}`)
  if (eye.cyl !== null) parts.push(`CYL: ${eye.cyl >= 0 ? '+' : ''}${eye.cyl}`)
  if (eye.axe !== null) parts.push(`AXE: ${eye.axe}°`)
  if (eye.add !== null) parts.push(`ADD: +${eye.add}`)
  return parts.length > 0 ? parts.join(', ') : 'Non renseigné'
}

const getMaterialLabel = (material) => {
  const labels = {
    mineral: 'Minéral',
    organic: 'Organique',
    polycarbonate: 'Polycarbonate',
    trivex: 'Trivex',
    high_index: 'Haut indice'
  }
  return labels[material] || material
}

import { formatPrice } from '../../utils/currency.js'

const generateDescription = () => {
  const parts = ['Verres personnalisés']

  if (hasOdCorrection.value && hasOgCorrection.value) {
    parts.push('(OD + OG)')
  } else if (hasOdCorrection.value) {
    parts.push('(OD uniquement)')
  } else if (hasOgCorrection.value) {
    parts.push('(OG uniquement)')
  }

  if (form.value.material) {
    parts.push(`- ${getMaterialLabel(form.value.material)}`)
  }

  if (selectedTreatments.value.length > 0) {
    parts.push(`- ${selectedTreatments.value.map(t => t.name).join(', ')}`)
  }

  return parts.join(' ')
}

const addToCart = () => {
  if (!canAdd.value) return

  const lensesData = {
    description: generateDescription(),
    quantity: form.value.quantity,
    price: totalPrice.value / form.value.quantity, // Prix unitaire
    details: {
      od: hasOdCorrection.value ? form.value.od : null,
      og: hasOgCorrection.value ? form.value.og : null,
      material: form.value.material,
      treatments: selectedTreatments.value,
      basePrice: form.value.basePrice
    }
  }

  emit('add', lensesData)
  emit('close')
}
</script>
