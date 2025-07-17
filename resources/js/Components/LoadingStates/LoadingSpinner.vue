<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <!-- Spinner -->
      <div 
        class="animate-spin rounded-full border-solid border-t-transparent"
        :class="spinnerClass"
      ></div>
      
      <!-- Message optionnel -->
      <div v-if="message" class="mt-3 text-center">
        <p :class="messageClass">{{ message }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md', // xs, sm, md, lg, xl
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'blue', // blue, gray, green, red
    validator: (value) => ['blue', 'gray', 'green', 'red'].includes(value)
  },
  message: {
    type: String,
    default: ''
  },
  fullHeight: {
    type: Boolean,
    default: false
  }
})

const containerClass = computed(() => {
  return props.fullHeight ? 'min-h-64 py-8' : 'py-4'
})

const spinnerClass = computed(() => {
  const sizes = {
    xs: 'h-4 w-4 border-2',
    sm: 'h-6 w-6 border-2',
    md: 'h-8 w-8 border-2',
    lg: 'h-12 w-12 border-3',
    xl: 'h-16 w-16 border-4'
  }
  
  const colors = {
    blue: 'border-blue-200 border-t-blue-600',
    gray: 'border-gray-200 border-t-gray-600',
    green: 'border-green-200 border-t-green-600',
    red: 'border-red-200 border-t-red-600'
  }
  
  return `${sizes[props.size]} ${colors[props.color]}`
})

const messageClass = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    gray: 'text-gray-600',
    green: 'text-green-600',
    red: 'text-red-600'
  }
  
  return `text-sm font-medium ${colors[props.color]}`
})
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
