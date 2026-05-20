import { createRouter, createWebHistory } from 'vue-router'

import Dashboard from '../views/Dashboard.vue'
import Productos from '../views/Productos.vue'
import Ventas from '../views/Ventas.vue'
import Compras from '@/views/Compras.vue'
import Inventario from '@/views/Inventario.vue'
import Usuarios from '@/views/Usuarios.vue'
import Reportes from '@/views/Reportes.vue'

const routes = [
  {
    path: '/',
    component: Dashboard
  },
  {
    path: '/productos',
    component: Productos
  },
  {
    path: '/ventas',
    component:Ventas
  },
  {
    path: '/compras',
    component:Compras
  },
  {
    path: '/inventario',
    component: Inventario
  },
  {
    path: '/usuarios',
    component:Usuarios
  },
  {
    path: '/reportes',
    component: Reportes
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router