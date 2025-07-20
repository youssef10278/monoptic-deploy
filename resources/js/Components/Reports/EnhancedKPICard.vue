<template>
  <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <component :is="iconComponent" :class="iconClass" />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 truncate">
              {{ title }}
            </dt>
            <dd class="flex items-baseline">
              <div class="text-2xl font-semibold text-gray-900">
                {{ formattedValue }}
              </div>
              <div v-if="showGrowth && growth !== null" class="ml-2 flex items-baseline text-sm font-semibold">
                <component 
                  :is="growthIcon" 
                  :class="growthIconClass"
                  class="self-center flex-shrink-0 h-4 w-4"
                />
                <span :class="growthTextClass" class="ml-1">
                  {{ Math.abs(growth).toFixed(1) }}%
                </span>
              </div>
            </dd>
            <div v-if="subtitle" class="text-xs text-gray-400 mt-1">
              {{ subtitle }}
            </div>
          </dl>
        </div>
      </div>
      
      <!-- Mini sparkline si des données sont fournies -->
      <div v-if="sparklineData && sparklineData.length > 0" class="mt-4">
        <div class="flex items-end space-x-1 h-8">
          <div 
            v-for="(value, index) in normalizedSparklineData" 
            :key="index"
            :class="sparklineBarClass"
            :style="{ height: `${value}%` }"
            class="flex-1 rounded-sm opacity-70"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatPrice } from '../../utils/currency.js'

// Icônes SVG
const TrendingUpIcon = {
  template: `
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
    </svg>
  `
}

const TrendingDownIcon = {
  template: `
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
    </svg>
  `
}

const CurrencyIcon = {
  template: `
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
    </svg>
  `
}

const ShoppingCartIcon = {
  template: `
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H3m4 10v6a1 1 0 001 1h1m0 0h4m-5 0a1 1 0 001 1h1m0 0a1 1 0 001-1v-1a1 1 0 00-1-1H9a1 1 0 00-1 1v1z"></path>
    </svg>
  `
}

const CalculatorIcon = {
  template: `
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
    </svg>
  `
}

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  type: {
    type: String,
    default: 'currency', // 'currency', 'number', 'text'
    validator: value => ['currency', 'number', 'text'].includes(value)
  },
  icon: {
    type: String,
    default: 'currency',
    validator: value => ['currency', 'cart', 'calculator', 'trending-up', 'trending-down'].includes(value)
  },
  color: {
    type: String,
    default: 'blue',
    validator: value => ['blue', 'green', 'purple', 'yellow', 'red', 'indigo'].includes(value)
  },
  growth: {
    type: Number,
    default: null
  },
  showGrowth: {
    type: Boolean,
    default: true
  },
  subtitle: {
    type: String,
    default: null
  },
  sparklineData: {
    type: Array,
    default: () => []
  }
})

// Computed properties
const formattedValue = computed(() => {
  if (props.type === 'currency') {
    return formatPrice(props.value)
  } else if (props.type === 'number') {
    return new Intl.NumberFormat('fr-FR').format(props.value)
  }
  return props.value
})

const iconComponent = computed(() => {
  const icons = {
    'currency': CurrencyIcon,
    'cart': ShoppingCartIcon,
    'calculator': CalculatorIcon,
    'trending-up': TrendingUpIcon,
    'trending-down': TrendingDownIcon
  }
  return icons[props.icon] || CurrencyIcon
})

const iconClass = computed(() => {
  const colors = {
    blue: 'text-blue-400',
    green: 'text-green-400',
    purple: 'text-purple-400',
    yellow: 'text-yellow-400',
    red: 'text-red-400',
    indigo: 'text-indigo-400'
  }
  return `h-6 w-6 ${colors[props.color] || colors.blue}`
})

const growthIcon = computed(() => {
  return props.growth >= 0 ? TrendingUpIcon : TrendingDownIcon
})

const growthIconClass = computed(() => {
  return props.growth >= 0 ? 'text-green-500' : 'text-red-500'
})

const growthTextClass = computed(() => {
  return props.growth >= 0 ? 'text-green-600' : 'text-red-600'
})

const sparklineBarClass = computed(() => {
  const colors = {
    blue: 'bg-blue-200',
    green: 'bg-green-200',
    purple: 'bg-purple-200',
    yellow: 'bg-yellow-200',
    red: 'bg-red-200',
    indigo: 'bg-indigo-200'
  }
  return colors[props.color] || colors.blue
})

const normalizedSparklineData = computed(() => {
  if (!props.sparklineData || props.sparklineData.length === 0) return []
  
  const max = Math.max(...props.sparklineData)
  const min = Math.min(...props.sparklineData)
  const range = max - min
  
  if (range === 0) return props.sparklineData.map(() => 50)
  
  return props.sparklineData.map(value => {
    return ((value - min) / range) * 80 + 10 // 10-90% range
  })
})
</script>
