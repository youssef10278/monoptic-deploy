<template>
  <div class="w-full h-full">
    <Bar
      :data="chartData"
      :options="chartOptions"
      class="w-full h-full"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Bar } from 'vue-chartjs'

// Enregistrer les composants Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
)

// Props
const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  period: {
    type: String,
    default: 'day'
  }
})

// Données du graphique
const chartData = computed(() => {
  if (!props.data || props.data.length === 0) {
    return {
      labels: [],
      datasets: []
    }
  }

  // Formater les labels selon la période
  const labels = props.data.map(item => {
    if (props.period === 'day') {
      return new Date(item.date).toLocaleDateString('fr-FR', {
        month: 'short',
        day: 'numeric'
      })
    } else if (props.period === 'week') {
      return `Semaine ${item.week}`
    } else if (props.period === 'month') {
      return new Date(item.month + '-01').toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short'
      })
    }
    return item.date || item.week || item.month
  })

  // Données des revenus
  const revenues = props.data.map(item => parseFloat(item.revenue || 0))

  return {
    labels,
    datasets: [
      {
        label: 'Chiffre d\'affaires (€)',
        data: revenues,
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 1,
        borderRadius: 4,
        borderSkipped: false,
      }
    ]
  }
})

// Options du graphique
const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    title: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const value = context.parsed.y
          return `CA: ${new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'MAD'
          }).format(value).replace('€', 'MAD')}`
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        maxRotation: 45,
        minRotation: 0
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)'
      },
      ticks: {
        callback: function(value) {
          return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'MAD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }).format(value).replace('€', 'MAD')
        }
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}))
</script>
