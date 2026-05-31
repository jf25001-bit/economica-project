import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import CategoriasView from '../views/CategoriasView.vue'
import ProveedoresView from '../views/ProveedoresView.vue'
import ProductosView from '../views/ProductosView.vue'

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginView
  },
  {
    path: '/categorias',
    name: 'categorias',
    component: CategoriasView
  },
  {
    path: '/proveedores',
    name: 'proveedores',
    component: ProveedoresView
  },
  {
    path: '/productos',
    name: 'productos',
    component: ProductosView
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
