<template>
  <div class="text-center py-12">
    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
      <slot name="icon">
        <!-- Icon par défaut -->
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-12 w-12">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" 
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </slot>
    </div>
    
    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ title }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ description }}</p>
    
    <div v-if="showAction" class="mt-6">
      <slot name="action">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          @click="$emit('action')"
        >
          {{ actionText }}
        </button>
      </slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  actionText: {
    type: String,
    default: 'Ajouter'
  },
  showAction: {
    type: Boolean,
    default: true
  }
})

defineEmits(['action'])
</script>
