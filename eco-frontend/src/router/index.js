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

  // APERTURA Y CIERRE DE CAJA
  {
    path: '/caja',
    component: AperturaCierreCaja,
    meta: {
      requiresAuth: true,
      allowedRoles: ['Administrador', 'Cajero']
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
  component: () => import('@/views/ControlCajas.vue')
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

  // PUNTO DE VENTA (Requiere Caja Abierta)
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

  // 1. Si NO está autenticado y la ruta requiere Auth, mandar a Login
  if (to.meta.requiresAuth && !token) {
    return '/login'
  }

  // 2. Si SI está autenticado e intenta ir a Login o a la raíz, redirigir según su estado/rol
  if (to.path === '/login' && token) {
    let cajaAbierta = false
    try {
      const resCaja = await cajaService.obtenerEstado()
      cajaAbierta = !!(resCaja && resCaja.caja)
    } catch (e) {
      cajaAbierta = false
    }

    const rol = user?.rol?.nombre || user?.rol || ''
    
    if (rol === 'Cajero') {
      return cajaAbierta ? '/pos' : '/caja'
    }
    return '/dashboard'
  }

  // Si la ruta no requiere autenticación y no es el login con token, permitir
  if (!to.meta.requiresAuth) {
    return true
  }

  const rol = user?.rol?.nombre || user?.rol || ''

  // 3. Verificar estado de la caja desde el servidor
  let cajaAbierta = false
  try {
    const resCaja = await cajaService.obtenerEstado()
    cajaAbierta = !!(resCaja && resCaja.caja)
  } catch (e) {
    cajaAbierta = false
  }

  // ==========================================
  // RESTRICCIÓN PARA CAJERO
  // ==========================================
  if (rol === 'Cajero') {
    // Si la caja está cerrada, el Cajero SOLO puede estar en '/caja'
    if (!cajaAbierta && to.path !== '/caja') {
      return '/caja'
    }

    const rutasPermitidas = ['/pos', '/productos', '/inventario', '/caja']
    if (!rutasPermitidas.includes(to.path)) {
      return cajaAbierta ? '/pos' : '/caja'
    }
  }

  // ==========================================
  // RESTRICCIÓN DE PUNTO DE VENTA (ADMIN Y CAJERO)
  // ==========================================
  if (to.meta.requiresCaja && !cajaAbierta) {
    return '/caja'
  }

  // ==========================================
  // VALIDAR ROLES DE CADA RUTA
  // ==========================================
  if (to.meta.allowedRoles && !to.meta.allowedRoles.includes(rol)) {
    return rol === 'Cajero' ? (cajaAbierta ? '/pos' : '/caja') : '/dashboard'
  }

  return true
})

export default router 