<template>
  <div class="bg-white overflow-hidden shadow rounded-lg animate-pulse">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <!-- Icon skeleton -->
          <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              <!-- Title skeleton -->
              <div class="h-4 bg-gray-200 rounded w-24"></div>
            </dt>
            <dd class="mt-1">
              <!-- Value skeleton -->
              <div class="h-8 bg-gray-200 rounded w-16"></div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Pas de logique nécessaire pour un skeleton
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
