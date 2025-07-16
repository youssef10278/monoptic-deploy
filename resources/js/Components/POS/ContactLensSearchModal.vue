<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                🧾 Configuration des Lentilles de Contact
              </h3>
              <p class="text-sm text-gray-600 mt-1">Configuration professionnelle étape par étape</p>
            </div>
            <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- 1. Type de lentilles (obligatoire) -->
          <div class="mb-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-4">
              <h4 class="text-sm font-semibold text-blue-900 mb-2">🔹 1. Type de lentilles (obligatoire)</h4>
              <p class="text-xs text-blue-700">Permet d'afficher dynamiquement les bons champs selon le type sélectionné</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label
                v-for="type in lensTypes"
                :key="type.value"
                class="relative flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                :class="{ 'border-blue-500 bg-blue-50': form.lensType === type.value }"
              >
                <input
                  v-model="form.lensType"
                  type="radio"
                  :value="type.value"
                  class="sr-only"
                >
                <div class="flex items-center">
                  <div class="w-4 h-4 border-2 rounded-full mr-3"
                       :class="form.lensType === type.value ? 'border-blue-500 bg-blue-500' : 'border-gray-300'">
                    <div v-if="form.lensType === type.value" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                  </div>
                  <span class="text-sm font-medium text-gray-900">{{ type.label }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- 2. Œil concerné -->
          <div class="mb-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-4">
              <h4 class="text-sm font-semibold text-green-900 mb-2">🔹 2. Œil concerné</h4>
              <p class="text-xs text-green-700">L'opticien choisit pour quel(s) œil(s) il configure les lentilles</p>
            </div>

            <div class="flex space-x-6">
              <label class="flex items-center">
                <input
                  v-model="form.includeOD"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="ml-2 text-sm font-medium text-gray-900">Œil droit (OD)</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.includeOG"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="ml-2 text-sm font-medium text-gray-900">Œil gauche (OG)</span>
              </label>
            </div>
          </div>

          <!-- 3. Paramètres techniques par œil -->
          <div v-if="form.lensType && (form.includeOD || form.includeOG)" class="mb-6">
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200 mb-4">
              <h4 class="text-sm font-semibold text-purple-900 mb-2">🔹 3. Paramètres techniques par œil</h4>
              <p class="text-xs text-purple-700">S'adaptent dynamiquement au type de lentille choisi</p>
            </div>

            <div class="grid gap-6" :class="form.includeOD && form.includeOG ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1'">
              <!-- Œil Droit (OD) -->
              <div v-if="form.includeOD" class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="flex justify-between items-center mb-4">
                  <h5 class="text-sm font-semibold text-blue-900 flex items-center">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs mr-2">OD</span>
                    Œil Droit
                  </h5>
                  <button
                    v-if="form.includeOG"
                    @click="copyODtoOG"
                    class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                  >
                    📋 Copier vers OG
                  </button>
                </div>

                <div class="space-y-3">
                  <!-- Sphère (toujours présente) -->
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Sphère (SPH) *</label>
                      <input
                        v-model.number="form.od.sphere"
                        type="number"
                        step="0.25"
                        placeholder="-2.50"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Rayon courbure (BC) *</label>
                      <input
                        v-model.number="form.od.bc"
                        type="number"
                        step="0.1"
                        placeholder="8.6"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                  </div>

                  <!-- Cylindre et Axe (pour toriques) -->
                  <div v-if="form.lensType === 'torique'" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Cylindre (CYL)</label>
                      <input
                        v-model.number="form.od.cylinder"
                        type="number"
                        step="0.25"
                        placeholder="-0.75"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Axe (AXE)</label>
                      <input
                        v-model.number="form.od.axis"
                        type="number"
                        min="0"
                        max="180"
                        placeholder="90"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                  </div>

                  <!-- Addition (pour multifocales) -->
                  <div v-if="form.lensType === 'multifocale'" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Addition (ADD)</label>
                      <input
                        v-model.number="form.od.addition"
                        type="number"
                        step="0.25"
                        placeholder="+1.50"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Diamètre (DIA)</label>
                      <input
                        v-model.number="form.od.diameter"
                        type="number"
                        step="0.1"
                        placeholder="14.2"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                  </div>

                  <!-- Diamètre (pour sphériques et toriques) -->
                  <div v-if="['spherique', 'torique'].includes(form.lensType)" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Diamètre (DIA)</label>
                      <input
                        v-model.number="form.od.diameter"
                        type="number"
                        step="0.1"
                        placeholder="14.2"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                    </div>
                    <div v-if="form.lensType === 'cosmétique'">
                      <label class="block text-xs font-medium text-blue-800 mb-1">Couleur</label>
                      <select
                        v-model="form.od.color"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                        <option value="">Sélectionner</option>
                        <option value="bleu">Bleu</option>
                        <option value="vert">Vert</option>
                        <option value="gris">Gris</option>
                        <option value="marron">Marron</option>
                        <option value="noisette">Noisette</option>
                      </select>
                    </div>
                  </div>

                  <!-- Matériau (pour rigides/sclérales) -->
                  <div v-if="['rigide', 'sclerale'].includes(form.lensType)" class="grid grid-cols-1 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-blue-800 mb-1">Matériau</label>
                      <select
                        v-model="form.od.material"
                        class="block w-full px-2 py-1 text-sm border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                      >
                        <option value="">Sélectionner</option>
                        <option value="rgp">RGP</option>
                        <option value="silicone">Silicone</option>
                        <option value="pmma">PMMA</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Œil Gauche (OG) -->
              <div v-if="form.includeOG" class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h5 class="text-sm font-semibold text-purple-900 mb-4 flex items-center">
                  <span class="w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs mr-2">OG</span>
                  Œil Gauche
                </h5>

                <div class="space-y-3">
                  <!-- Sphère (toujours présente) -->
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Sphère (SPH) *</label>
                      <input
                        v-model.number="form.og.sphere"
                        type="number"
                        step="0.25"
                        placeholder="-2.50"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Rayon courbure (BC) *</label>
                      <input
                        v-model.number="form.og.bc"
                        type="number"
                        step="0.1"
                        placeholder="8.6"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                  </div>

                  <!-- Cylindre et Axe (pour toriques) -->
                  <div v-if="form.lensType === 'torique'" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Cylindre (CYL)</label>
                      <input
                        v-model.number="form.og.cylinder"
                        type="number"
                        step="0.25"
                        placeholder="-0.75"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Axe (AXE)</label>
                      <input
                        v-model.number="form.og.axis"
                        type="number"
                        min="0"
                        max="180"
                        placeholder="90"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                  </div>

                  <!-- Addition (pour multifocales) -->
                  <div v-if="form.lensType === 'multifocale'" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Addition (ADD)</label>
                      <input
                        v-model.number="form.og.addition"
                        type="number"
                        step="0.25"
                        placeholder="+1.50"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Diamètre (DIA)</label>
                      <input
                        v-model.number="form.og.diameter"
                        type="number"
                        step="0.1"
                        placeholder="14.2"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                  </div>

                  <!-- Diamètre (pour sphériques et toriques) -->
                  <div v-if="['spherique', 'torique'].includes(form.lensType)" class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Diamètre (DIA)</label>
                      <input
                        v-model.number="form.og.diameter"
                        type="number"
                        step="0.1"
                        placeholder="14.2"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                    </div>
                    <div v-if="form.lensType === 'cosmétique'">
                      <label class="block text-xs font-medium text-purple-800 mb-1">Couleur</label>
                      <select
                        v-model="form.og.color"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      ><option value="">Sélectionner</option>
                        <option value="bleu">Bleu</option>
                        <option value="vert">Vert</option>
                        <option value="gris">Gris</option>
                        <option value="marron">Marron</option>
                        <option value="noisette">Noisette</option>
                      </select>
                    </div>
                  </div>

                  <!-- Matériau (pour rigides/sclérales) -->
                  <div v-if="['rigide', 'sclerale'].includes(form.lensType)" class="grid grid-cols-1 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-purple-800 mb-1">Matériau</label>
                      <select
                        v-model="form.og.material"
                        class="block w-full px-2 py-1 text-sm border border-purple-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                      >
                        <option value="">Sélectionner</option>
                        <option value="rgp">RGP</option>
                        <option value="silicone">Silicone</option>
                        <option value="pmma">PMMA</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Produit et Marque -->
          <div v-if="hasValidParameters" class="mb-6">
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200 mb-4">
              <h4 class="text-sm font-semibold text-orange-900 mb-2">🔹 4. Produit et Marque</h4>
              <p class="text-xs text-orange-700">Pour choisir le modèle spécifique disponible en magasin</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                <input
                  v-model="form.brand"
                  type="text"
                  placeholder="ex. Acuvue, Biofinity, Dailies..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  @input="searchBrands"
                  @focus="showBrandSuggestions = true"
                  @blur="hideBrandSuggestions"
                >

                <!-- Suggestions d'autocomplétion -->
                <div v-if="showBrandSuggestions && brandSuggestions.length > 0"
                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                  <div
                    v-for="(suggestion, index) in brandSuggestions"
                    :key="index"
                    @mousedown="selectBrand(suggestion)"
                    class="px-3 py-2 cursor-pointer hover:bg-blue-50 text-sm"
                  >
                    {{ suggestion }}
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modèle/Type</label>
                <input
                  v-model="form.model"
                  type="text"
                  placeholder="ex. Oasys, Total 1"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durée de port</label>
                <select
                  v-model="form.duration"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                ><option value="">Sélectionner</option>
                  <option value="journaliere">Journalière</option>
                  <option value="bimensuelle">Bimensuelle</option>
                  <option value="mensuelle">Mensuelle</option>
                  <option value="trimestrielle">Trimestrielle</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenance boîte</label>
                <select
                  v-model="form.boxSize"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                ><option value="">Sélectionner</option>
                  <option value="6">6 lentilles</option>
                  <option value="30">30 lentilles</option>
                  <option value="90">90 lentilles</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 5. Quantité -->
          <div v-if="hasValidParameters" class="mb-6">
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 mb-4">
              <h4 class="text-sm font-semibold text-yellow-900 mb-2">🔹 5. Quantité</h4>
              <p class="text-xs text-yellow-700">Nombre de boîtes pour chaque œil</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div v-if="form.includeOD">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité OD (boîtes)</label>
                <input
                  v-model.number="form.quantityOD"
                  type="number"
                  min="0"
                  placeholder="2"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div v-if="form.includeOG">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité OG (boîtes)</label>
                <input
                  v-model.number="form.quantityOG"
                  type="number"
                  min="0"
                  placeholder="1"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>
          </div>

          <!-- 6. Prix et Total -->
          <div v-if="hasValidParameters" class="mb-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-4">
              <h4 class="text-sm font-semibold text-green-900 mb-2">🔹 6. Prix et Total</h4>
              <p class="text-xs text-green-700">Calcul automatique du prix</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€/boîte)</label>
                <input
                  v-model.number="form.unitPrice"
                  type="number"
                  step="0.01"
                  placeholder="35.90"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total HT</label>
                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-900 font-medium">
                  {{ formatPrice(totalHT) }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total TTC</label>
                <div class="px-3 py-2 bg-green-100 border border-green-300 rounded-md text-green-900 font-bold">
                  {{ formatPrice(totalTTC) }}
                </div>
              </div>
            </div>
          </div>

          <!-- 7. Remarques complémentaires -->
          <div v-if="hasValidParameters" class="mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">🔹 7. Remarques complémentaires</h4>
              <p class="text-xs text-gray-700">Champ libre pour que l'opticien ajoute une note</p>
            </div>

            <textarea
              v-model="form.notes"
              rows="3"
              placeholder="Exemple : Adaptation récente, Client préfère journalière, etc."
              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
          </div>
        </div>

        <!-- 8. Actions -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="addToCart"
            :disabled="!canAddToCart"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            ✅ Ajouter au panier
          </button>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'

// Emits
const emit = defineEmits(['close', 'add'])

// Types de lentilles
const lensTypes = ref([
  { value: 'spherique', label: 'Sphérique souple' },
  { value: 'torique', label: 'Torique souple' },
  { value: 'multifocale', label: 'Multifocale' },
  { value: 'rigide', label: 'Rigide (RGP)' },
  { value: 'sclerale', label: 'Sclérale' },
  { value: 'cosmetique', label: 'Cosmétique' }
])

// Formulaire principal
const form = ref({
  // 1. Type de lentilles
  lensType: '',

  // 2. Œil concerné
  includeOD: false,
  includeOG: false,

  // 3. Paramètres techniques OD
  od: {
    sphere: null,
    cylinder: null,
    axis: null,
    addition: null,
    bc: null,
    diameter: null,
    material: '',
    color: ''
  },

  // 3. Paramètres techniques OG
  og: {
    sphere: null,
    cylinder: null,
    axis: null,
    addition: null,
    bc: null,
    diameter: null,
    material: '',
    color: ''
  },

  // 4. Produit et marque
  brand: '',
  model: '',
  duration: '',
  boxSize: '',

  // 5. Quantité
  quantityOD: 0,
  quantityOG: 0,

  // 6. Prix
  unitPrice: null,

  // 7. Remarques
  notes: ''
})

// Computed
const hasValidParameters = computed(() => {
  return form.value.lensType && (form.value.includeOD || form.value.includeOG)
})

const totalHT = computed(() => {
  const totalQuantity = (form.value.quantityOD || 0) + (form.value.quantityOG || 0)
  return (form.value.unitPrice || 0) * totalQuantity
})

const totalTTC = computed(() => {
  return totalHT.value * 1.2 // TVA 20%
})

const canAddToCart = computed(() => {
  return hasValidParameters.value &&
         form.value.unitPrice > 0 &&
         ((form.value.quantityOD || 0) + (form.value.quantityOG || 0)) > 0 &&
         ((form.value.includeOD && form.value.od.sphere !== null && form.value.od.bc !== null) ||
          (form.value.includeOG && form.value.og.sphere !== null && form.value.og.bc !== null))
})

// Variables réactives pour l'autocomplétion des marques
const brandSuggestions = ref([])
const showBrandSuggestions = ref(false)
const searchTimeout = ref(null)

// Variables pour le calcul automatique des prix
const isCalculatingPrice = ref(false)
const priceCalculationError = ref(null)
// Méthodes
const copyODtoOG = () => {
  if (form.value.includeOG) {
    form.value.og = { ...form.value.od }
  }
}



const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const addToCart = () => {
  if (!canAddToCart.value) return

  const items = []

  // Ajouter OD si inclus et quantité > 0
  if (form.value.includeOD && form.value.quantityOD > 0) {
    items.push({
      type: 'custom',
      itemType: 'contact_lenses',
      description: `Lentilles OD: ${form.value.brand} ${form.value.model} (${form.value.lensType})`,
      quantity: form.value.quantityOD,
      price: form.value.unitPrice,
      details: {
        eye: 'OD',
        lensType: form.value.lensType,
        brand: form.value.brand,
        model: form.value.model,
        duration: form.value.duration,
        boxSize: form.value.boxSize,
        sphere: form.value.od.sphere,
        cylinder: form.value.od.cylinder,
        axis: form.value.od.axis,
        addition: form.value.od.addition,
        bc: form.value.od.bc,
        diameter: form.value.od.diameter,
        material: form.value.od.material,
        color: form.value.od.color,
        notes: form.value.notes
      }
    })
  }

  // Ajouter OG si inclus et quantité > 0
  if (form.value.includeOG && form.value.quantityOG > 0) {
    items.push({
      type: 'custom',
      itemType: 'contact_lenses',
      description: `Lentilles OG: ${form.value.brand} ${form.value.model} (${form.value.lensType})`,
      quantity: form.value.quantityOG,
      price: form.value.unitPrice,
      details: {
        eye: 'OG',
        lensType: form.value.lensType,
        brand: form.value.brand,
        model: form.value.model,
        duration: form.value.duration,
        boxSize: form.value.boxSize,
        sphere: form.value.og.sphere,
        cylinder: form.value.og.cylinder,
        axis: form.value.og.axis,
        addition: form.value.og.addition,
        bc: form.value.og.bc,
        diameter: form.value.og.diameter,
        material: form.value.og.material,
        color: form.value.og.color,
        notes: form.value.notes
      }
    })
  }

  // Émettre les articles et fermer
  items.forEach(item => emit('add', item))
  emit('close')
}
// Méthodes pour le calcul automatique des prix
const calculatePrice = async () => {
  if (!form.value.lensType || !form.value.duration || !form.value.boxSize) {
    return
  }

  isCalculatingPrice.value = true
  priceCalculationError.value = null

  try {
    // Préparer les paramètres techniques
    const parameters = {}

    if (form.value.includeOD) {
      if (form.value.od.sphere !== null) parameters.sphere = form.value.od.sphere
      if (form.value.od.cylinder !== null) parameters.cylinder = form.value.od.cylinder
      if (form.value.od.addition !== null) parameters.addition = form.value.od.addition
    } else if (form.value.includeOG) {
      if (form.value.og.sphere !== null) parameters.sphere = form.value.og.sphere
      if (form.value.og.cylinder !== null) parameters.cylinder = form.value.og.cylinder
      if (form.value.og.addition !== null) parameters.addition = form.value.og.addition
    }

    const response = await axios.post('/api/contact-lens-prices/calculate', {
      lens_type: form.value.lensType,
      duration: form.value.duration,
      box_size: form.value.boxSize,
      brand: form.value.brand || null,
      parameters: parameters
    })

    if (response.data.success) {
      form.value.unitPrice = response.data.data.calculated_price
    } else {
      // Utiliser le prix suggéré si aucune règle n'est configurée
      if (response.data.data && response.data.data.suggested_price) {
        form.value.unitPrice = response.data.data.suggested_price
      }
      priceCalculationError.value = response.data.message
    }
  } catch (error) {
    console.error('Erreur lors du calcul du prix:', error)
    priceCalculationError.value = 'Erreur lors du calcul du prix'
  } finally {
    isCalculatingPrice.value = false
  }
}

// Méthodes pour l'autocomplétion des marques
const searchBrands = async () => {
  // Annuler la recherche précédente
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }

  // Débounce de 300ms
  searchTimeout.value = setTimeout(async () => {
    if (form.value.brand.length < 2) {
      brandSuggestions.value = []
      return
    }

    try {
      const response = await axios.get('/api/contact-lens-brands/search', {
        params: { q: form.value.brand }
      })

      if (response.data.success) {
        brandSuggestions.value = response.data.data
      }
    } catch (error) {
      console.error('Erreur lors de la recherche des marques:', error)
      brandSuggestions.value = []
    }
  }, 300)
}

const selectBrand = (brand) => {
  form.value.brand = brand
  showBrandSuggestions.value = false
  brandSuggestions.value = []

  // Recalculer le prix avec la nouvelle marque
  calculatePrice()
}

const hideBrandSuggestions = () => {
  // Délai pour permettre le clic sur une suggestion
  setTimeout(() => {
    showBrandSuggestions.value = false
  }, 200)
}

// Charger les marques populaires au montage
const loadPopularBrands = async () => {
  try {
    const response = await axios.get('/api/contact-lens-brands/popular', {
      params: { limit: 5 }
    })

    if (response.data.success && response.data.data.length > 0) {
      brandSuggestions.value = response.data.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des marques populaires:', error)
  }
}

// Watchers pour le calcul automatique des prix
watch([
  () => form.value.lensType,
  () => form.value.duration,
  () => form.value.boxSize,
  () => form.value.brand,
  () => form.value.od.sphere,
  () => form.value.od.cylinder,
  () => form.value.od.addition,
  () => form.value.og.sphere,
  () => form.value.og.cylinder,
  () => form.value.og.addition
], () => {
  calculatePrice()
}, { deep: true })

// Lifecycle
onMounted(() => {
  loadPopularBrands()
})
</script>
