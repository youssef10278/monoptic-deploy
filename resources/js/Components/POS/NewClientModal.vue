<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-start mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Nouveau Client
            </h3>
            <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <form @submit.prevent="createClient" class="space-y-4">
            <!-- Nom complet -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Nom complet *
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                placeholder="Prénom Nom"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.name }"
              >
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>

            <!-- Téléphone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Téléphone
              </label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                placeholder="06 12 34 56 78"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.phone }"
              >
              <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="client@email.com"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.email }"
              >
              <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
            </div>

            <!-- Adresse -->
            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                Adresse
              </label>
              <textarea
                id="address"
                v-model="form.address"
                rows="2"
                placeholder="Adresse complète"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'border-red-300': errors.address }"
              ></textarea>
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
            </div>

            <!-- Informations supplémentaires -->
            <div class="bg-blue-50 rounded-lg p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-blue-800">
                    Création rapide
                  </h3>
                  <div class="mt-2 text-sm text-blue-700">
                    <p>Seul le nom est obligatoire. Vous pourrez compléter les informations plus tard depuis la fiche client.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Résumé -->
            <div v-if="form.name" class="bg-gray-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Aperçu</h4>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Nom :</strong> {{ form.name }}</p>
                <p v-if="form.phone"><strong>Téléphone :</strong> {{ form.phone }}</p>
                <p v-if="form.email"><strong>Email :</strong> {{ form.email }}</p>
                <p v-if="form.address"><strong>Adresse :</strong> {{ form.address }}</p>
              </div>
            </div>
          </form>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="createClient"
            :disabled="!canCreate || isCreating"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="isCreating" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isCreating ? 'Création...' : 'Créer et Continuer' }}
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
import axios from 'axios'

// Emits
const emit = defineEmits(['close', 'created'])

// État réactif
const isCreating = ref(false)
const errors = ref({})

const form = ref({
  name: '',
  phone: '',
  email: '',
  address: ''
})

// Computed
const canCreate = computed(() => {
  return form.value.name.trim().length > 0
})

// Méthodes
const createClient = async () => {
  if (!canCreate.value || isCreating.value) return

  isCreating.value = true
  errors.value = {}

  try {
    const clientData = {
      name: form.value.name.trim(),
      phone: form.value.phone.trim() || null,
      email: form.value.email.trim() || null,
      address: form.value.address.trim() || null
    }

    const response = await axios.post('/api/clients', clientData)

    if (response.data.success) {
      emit('created', response.data.data)
    } else {
      alert('Erreur lors de la création du client')
    }
  } catch (error) {
    console.error('Erreur lors de la création du client:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      alert('Erreur lors de la création du client')
    }
  } finally {
    isCreating.value = false
  }
}
</script>
