import { createRouter, createWebHistory } from 'vue-router'
import { cajaService } from '@/services/cajaService'

import Dashboard from '../views/Dashboard.vue'
import Productos from '../views/Productos.vue'
import Ventas from '../views/Ventas.vue'
import Compras from '@/views/Compras.vue'
import Inventario from '@/views/Inventario.vue'
import DetalleLotes from '@/views/DetalleLotes.vue'
import Usuarios from '@/views/Usuarios.vue'
import Reportes from '@/views/Reportes.vue'
import Login from '@/views/Login.vue'
import Categorias from '@/views/Categorias.vue'
import Proveedores from '@/views/Proveedores.vue'
import Imagenes from '../views/Imagenes.vue'
import Pos from '@/views/Pos.vue'
import AperturaCierreCaja from '@/views/AperturaCierreCaja.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },

  {
    path: '/dashboard',
    component: Dashboard,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/caja',
    component: AperturaCierreCaja,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/productos',
    component: Productos,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  },
  {
    path: '/productos/anulados',
    name: 'ProductosAnulados',
    component: () => import('@/views/ProductosAnulados.vue')
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

  {
    path: '/categorias',
    component: Categorias,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/ventas',
    component: Ventas,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/compras',
    component: Compras,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/inventario',
    component: Inventario,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  },
  {
    path: '/inventario/:id/lotes',
    name: 'DetalleLotes',
    component: DetalleLotes,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
    }
  },

  {
    path: '/usuarios',
    component: Usuarios,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/reportes',
    component: Reportes,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/proveedores',
    component: Proveedores,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

  {
    path: '/imagenes',
    component: Imagenes,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador']
    }
  },

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

  if (to.path === '/') {
    if (!token) return '/login'
    return rol === 'cajero' ? '/pos' : '/dashboard'
  }

  if (to.meta.requiresAuth && !token) {
    return to.path === '/login' ? true : '/login'
  }

  if (to.path === '/login' && token) {
    return rol === 'cajero' ? '/pos' : '/dashboard'
  }

  if (!to.meta.requiresAuth) {
    return true
  }

  let cajaAbierta = false
  try {
    const resCaja = await cajaService.obtenerEstado()
    cajaAbierta = !!(resCaja && resCaja.caja)
  } catch (e) {
    cajaAbierta = false
  }

  if (rol === 'cajero') {
    const rutasPermitidas = ['/pos', '/productos', '/inventario']
    const permitida = rutasPermitidas.some(r => to.path === r || to.path.startsWith(`${r}/`))
    if (!permitida) {
      return '/pos'
    }
  }

  if (to.meta.requiresCaja && !cajaAbierta && rol !== 'cajero') {
    return to.path === '/caja' ? true : '/caja'
  }

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