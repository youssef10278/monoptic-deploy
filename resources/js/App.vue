<template>
  <component :is="currentLayout">
    <router-view />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AuthenticatedLayout from './Layouts/AuthenticatedLayout.vue'
import SuperAdminLayout from './Layouts/SuperAdminLayout.vue'

const route = useRoute()

// Mapping des layouts
const layouts = {
  AuthenticatedLayout,
  SuperAdminLayout
}

// Computed property pour déterminer le layout actuel
const currentLayout = computed(() => {
  const layoutName = route.meta?.layout
  
  // Si aucun layout spécifié dans les meta, utiliser un div simple
  if (!layoutName) {
    return 'div'
  }
  
  // Retourner le composant layout correspondant
  return layouts[layoutName] || 'div'
})
</script>
