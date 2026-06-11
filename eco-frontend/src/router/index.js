import { createRouter, createWebHistory } from 'vue-router'

import Dashboard from '../views/Dashboard.vue'
import Productos from '../views/Productos.vue'
import Ventas from '../views/Ventas.vue'
import Compras from '@/views/Compras.vue'
import Inventario from '@/views/Inventario.vue'
import Usuarios from '@/views/Usuarios.vue'
import Reportes from '@/views/Reportes.vue'
import Login from '@/views/Login.vue'
import Categorias from '@/views/Categorias.vue'
import Proveedores from '@/views/Proveedores.vue'
import Imagenes from '../views/Imagenes.vue'

const routes = [
  {
    path: '/',
    component: Login
  },
  {
    path: '/login',
    component: Login
  },

  {
    path: '/dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },

  {
    path: '/productos',
    component: Productos,
    meta: { requiresAuth: true }
  },

  {
    path: '/categorias',
    component: Categorias,
    meta: { requiresAuth: true }
  },

  {
    path: '/ventas',
    component: Ventas,
    meta: { requiresAuth: true }
  },

  {
    path: '/compras',
    component: Compras,
    meta: { requiresAuth: true }
  },

  {
    path: '/inventario',
    component: Inventario,
    meta: {
      requiresAuth: true,
      adminOnly: true
    }
  },

  {
    path: '/usuarios',
    component: Usuarios,
    meta: {
      requiresAuth: true,
      adminOnly: true
    }
  },

  {
    path: '/reportes',
    component: Reportes,
    meta: {
      requiresAuth: true,
      adminOnly: true
    }
  },

  {
    path: '/proveedores',
    component: Proveedores,
    meta: { requiresAuth: true }
  },

  {
    path: '/imagenes',
    component: Imagenes,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user'))

  if (to.meta.requiresAuth && !token) {
    return '/login'
  }

  if (to.meta.adminOnly && user?.rol?.nombre === 'Cajero') {
    return '/dashboard'
  }

  return true
})

export default router