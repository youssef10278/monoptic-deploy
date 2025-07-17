<template>
  <div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:p-6">
      <!-- Header skeleton -->
      <div class="mb-4">
        <div class="h-6 bg-gray-200 rounded w-48 animate-pulse"></div>
      </div>
      
      <!-- Table skeleton -->
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th v-for="n in columns" :key="n" class="px-6 py-3">
                <div class="h-4 bg-gray-200 rounded animate-pulse" :class="getColumnWidth(n)"></div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="row in rows" :key="row" class="hover:bg-gray-50">
              <td v-for="col in columns" :key="col" class="px-6 py-4 whitespace-nowrap">
                <div class="h-4 bg-gray-200 rounded animate-pulse" :class="getColumnWidth(col)"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  rows: {
    type: Number,
    default: 5
  },
  columns: {
    type: Number,
    default: 4
  }
})

const getColumnWidth = (index) => {
  const widths = ['w-32', 'w-24', 'w-20', 'w-16', 'w-28', 'w-20']
  return widths[index % widths.length] || 'w-24'
}
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
