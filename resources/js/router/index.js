import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import TenantDashboard from '../views/TenantDashboard.vue'
import ClientsIndex from '../views/Clients/Index.vue'
import ClientsShow from '../views/Clients/Show.vue'
import StockIndex from '../views/Stock/Index.vue'
import SalesIndex from '../views/Sales/Index.vue'
import POSIndex from '../views/POS/Index.vue'
import CreditsIndex from '../views/Credits/Index.vue'
import ReportsIndex from '../views/Reports/Index.vue'
import SuperAdminDashboard from '../views/SuperAdmin/Dashboard.vue'
import OpticianConfig from '../views/Settings/OpticianConfig.vue'



const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: TenantDashboard,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/clients',
    name: 'ClientsIndex',
    component: ClientsIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/clients/:id',
    name: 'ClientsShow',
    component: ClientsShow,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },

  {
    path: '/stock',
    name: 'StockIndex',
    component: StockIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/sales',
    name: 'SalesIndex',
    component: SalesIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/pos',
    name: 'POSIndex',
    component: POSIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/credits',
    name: 'CreditsIndex',
    component: CreditsIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },
  {
    path: '/reports',
    name: 'ReportsIndex',
    component: ReportsIndex,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  },

  {
    path: '/super-admin/dashboard',
    name: 'SuperAdminDashboard',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, requiresSuperAdmin: true, layout: 'SuperAdminLayout' }
  },
  {
    path: '/settings/optician',
    name: 'OpticianConfig',
    component: OpticianConfig,
    meta: { requiresAuth: true, layout: 'AuthenticatedLayout' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  const isAuthenticated = !!token

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Rediriger vers login si l'utilisateur n'est pas authentifié
    next('/login')
  } else if (to.meta.requiresGuest && isAuthenticated) {
    // Rediriger vers dashboard si l'utilisateur est déjà authentifié
    next('/dashboard')
  } else if (to.meta.requiresSuperAdmin && isAuthenticated) {
    // Vérifier si l'utilisateur est Super Admin
    const userData = localStorage.getItem('user_data')
    if (userData) {
      const user = JSON.parse(userData)
      if (user.role !== 'super_admin') {
        // Rediriger vers le dashboard normal si pas Super Admin
        next('/dashboard')
        return
      }
    }
    next()
  } else {
    next()
  }
})

export default router
