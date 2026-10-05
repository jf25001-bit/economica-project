import { createRouter, createWebHistory } from 'vue-router'
import { cajaService } from '@/services/cajaService'

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
import Pos from '@/views/Pos.vue'
import AperturaCierreCaja from '@/views/AperturaCierreCaja.vue'

const routes = [
  // LOGIN
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },

  // DASHBOARD
  {
    path: '/dashboard',
    component: Dashboard,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // APERTURA Y CIERRE DE CAJA (solo administrador)
  {
    path: '/caja',
    component: AperturaCierreCaja,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // PRODUCTOS
  {
    path: '/productos',
    component: Productos,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  },
  {
    path: '/control-cajas',
    name: 'ControlCajas',
    component: () => import('@/views/ControlCajas.vue'),
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // CATEGORÍAS
  {
    path: '/categorias',
    component: Categorias,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // VENTAS
  {
    path: '/ventas',
    component: Ventas,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // COMPRAS
  {
    path: '/compras',
    component: Compras,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // INVENTARIO
  {
    path: '/inventario',
    component: Inventario,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  },

  // USUARIOS
  {
    path: '/usuarios',
    component: Usuarios,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // REPORTES
  {
    path: '/reportes',
    component: Reportes,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // PROVEEDORES
  {
    path: '/proveedores',
    component: Proveedores,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // IMÁGENES
  {
    path: '/imagenes',
    component: Imagenes,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  // PUNTO DE VENTA (el admin necesita caja abierta; al cajero se le bloquea la venta dentro de Pos.vue)
  {
    path: '/pos',
    component: Pos,
    meta: {
      requiresAuth: true,
      requiresCaja: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const token = localStorage.getItem('token')
  let user = null

  try {
    const usuarioGuardado = localStorage.getItem('user')
    if (usuarioGuardado) {
      user = JSON.parse(usuarioGuardado)
    }
  } catch (error) {
    console.error('Error leyendo usuario:', error)
  }

  const rol = String(user?.rol?.nombre || user?.rol || '').trim().toLowerCase()

  // 1. Ruta raíz
  if (to.path === '/') {
    if (!token) return '/login'
    return rol === 'cajero' ? '/pos' : '/dashboard'
  }

  // 2. Sin sesión
  if (to.meta.requiresAuth && !token) {
    return to.path === '/login' ? true : '/login'
  }

  // 3. Con sesión e intenta ir a login
  if (to.path === '/login' && token) {
    return rol === 'cajero' ? '/pos' : '/dashboard'
  }

  if (!to.meta.requiresAuth) {
    return true
  }

  // 4. Estado de la caja
  let cajaAbierta = false
  try {
    const resCaja = await cajaService.obtenerEstado()
    cajaAbierta = !!(resCaja && resCaja.caja)
  } catch (e) {
    cajaAbierta = false
  }

  // CAJERO: solo estas rutas (el bloqueo de ventas se hace dentro de Pos.vue)
  if (rol === 'cajero') {
    const rutasPermitidas = ['/pos', '/productos', '/inventario']
    if (!rutasPermitidas.includes(to.path)) {
      return '/pos'
    }
  }

  // ADMIN: el punto de venta necesita caja abierta
  if (to.meta.requiresCaja && !cajaAbierta && rol !== 'cajero') {
    return to.path === '/caja' ? true : '/caja'
  }

  // Validar roles de cada ruta
  if (to.meta.allowedRoles) {
    const rolesPermitidos = to.meta.allowedRoles.map(r => r.toLowerCase())
    if (!rolesPermitidos.includes(rol)) {
      const destinoFallback = rol === 'cajero' ? '/pos' : '/dashboard'
      return to.path === destinoFallback ? true : destinoFallback
    }
  }

  return true
})

export default router