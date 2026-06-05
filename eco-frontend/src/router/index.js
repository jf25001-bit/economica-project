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


const routes = [
  {
    path: '/',
    component: Login
  },
  {
    path: '/productos',
    component: Productos
  },
  {
     path: '/categorias',
    component: Categorias
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
  },
   {
    path: '/login',
    component: Login
  },
  {
    path: '/proveedores',
    component: Proveedores
  },
  {
    path: '/dashboard',
    component: Dashboard
  }
  
  
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router