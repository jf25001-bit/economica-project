<template>
  <aside
    :class="[
      'text-slate-400 fixed left-0 top-0 w-64 h-screen transition-all duration-300 flex flex-col z-40 bg-[#0F172A] border-r border-slate-800/80',
      isOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full shadow-none'
    ]"
  >
    <!-- Logo -->
    <div
      class="h-[73px] px-5 flex items-center justify-start transition-all duration-300 border-b border-slate-800/60 shrink-0"
    >
      <router-link :to="rutaInicio" class="no-underline flex items-center">
        <img
          src="/nuevo logo.svg"
          alt="Logo La Económica"
          :class="[
            'object-contain object-left transition-all duration-300',
            isOpen ? 'h-10 max-w-[85%]' : 'h-9 w-9'
          ]"
        />
      </router-link>
    </div>

    <!-- Menú por secciones desplegables -->
    <nav class="flex-1 py-3 px-3 overflow-y-auto custom-scrollbar space-y-2">
      <div v-for="seccion in menuFiltrado" :key="seccion.titulo">
        <!-- Título de sección clickeable -->
        <button
          v-if="seccion.titulo !== 'General'"
          @click="toggleSeccion(seccion.titulo)"
          class="w-full flex items-center justify-between px-4 py-1.5 mt-2 mb-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-600 hover:text-slate-300 transition-colors cursor-pointer bg-transparent border-0"
        >
          <span>{{ seccion.titulo }}</span>
          <i
            :class="[
              'bi bi-chevron-down text-[10px] transition-transform duration-300',
              seccionesAbiertas[seccion.titulo] ? 'rotate-180' : ''
            ]"
          ></i>
        </button>

        <!-- Items con animación de despliegue -->
        <div
          :class="[
            'grid transition-all duration-300 ease-in-out',
            seccion.titulo === 'General' || seccionesAbiertas[seccion.titulo]
              ? 'grid-rows-[1fr] opacity-100'
              : 'grid-rows-[0fr] opacity-0'
          ]"
        >
          <div class="overflow-hidden">
            <div class="space-y-1">
              <router-link
                v-for="item in seccion.items"
                :key="item.name"
                :to="item.route"
                class="no-underline flex items-center gap-3.5 px-4 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group font-medium text-sm"
                active-class="!bg-gradient-to-r !from-sky-500 !to-blue-600 !text-white !font-bold shadow-md shadow-sky-500/20"
              >
                <i
                  :class="[
                    item.icon,
                    'text-lg group-hover:scale-110 transition-transform shrink-0'
                  ]"
                ></i>

                <span class="truncate no-underline">
                  {{ item.name }}
                </span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Acciones Inferiores -->
    <div class="p-3 border-t border-slate-800/60 shrink-0">
      <button
        @click="cerrarSesion"
        class="flex items-center justify-center gap-3 w-full py-2.5 px-4 rounded-xl text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white transition-all duration-200 text-xs font-bold cursor-pointer border border-red-500/10 hover:border-transparent shadow-sm"
      >
        <i class="bi bi-box-arrow-left text-base shrink-0"></i>
        <span class="truncate">Cerrar sesión</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

defineProps({
  isOpen: Boolean
})

const router = useRouter()
const route = useRoute()

const menu = [
  {
    titulo: 'General',
    items: [
      { name: 'Inicio', route: '/dashboard', icon: 'bi bi-house-door-fill' }
    ]
  },
  {
    titulo: 'Operación',
    items: [
      { name: 'Apertura / Cierre', route: '/caja', icon: 'bi bi-wallet2' },
      { name: 'Punto de Venta', route: '/pos', icon: 'bi bi-calculator-fill' },
      { name: 'Ventas', route: '/ventas', icon: 'bi bi-cash-coin' },
      { name: 'Control de Cajas', route: '/control-cajas', icon: 'bi bi-shield-lock-fill' }
    ]
  },
  {
    titulo: 'Catálogo',
    items: [
      { name: 'Categorias', route: '/categorias', icon: 'bi bi-grid-3x3-gap-fill' },
      { name: 'Productos', route: '/productos', icon: 'bi bi-box-seam-fill' },
      { name: 'Inventario', route: '/inventario', icon: 'bi bi-archive-fill' }
    ]
  },
  {
    titulo: 'Compras',
    items: [
      { name: 'Proveedores', route: '/proveedores', icon: 'bi bi-building' },
      { name: 'Compras', route: '/compras', icon: 'bi bi-basket2-fill' }
    ]
  },
  {
    titulo: 'Administración',
    items: [
      { name: 'Usuarios', route: '/usuarios', icon: 'bi bi-person-badge-fill' },
      { name: 'Reportes', route: '/reportes', icon: 'bi bi-bar-chart-line-fill' }
    ]
  }
]

// Secciones desplegables: guarda cuáles están abiertas
const seccionesAbiertas = ref({})

const toggleSeccion = (titulo) => {
  seccionesAbiertas.value[titulo] = !seccionesAbiertas.value[titulo]
}

const usuarioActual = computed(() => {
  try {
    const usuario = localStorage.getItem('user')
    return usuario ? JSON.parse(usuario) : null
  } catch (error) {
    return null
  }
})

const rolActual = computed(() => {
  const rawRol = usuarioActual.value?.rol?.nombre || usuarioActual.value?.rol || ''
  return String(rawRol).trim().toLowerCase()
})

const rutasCajero = ['/pos', '/productos', '/inventario']

const menuFiltrado = computed(() => {
  if (rolActual.value === 'cajero') {
    return menu
      .map(seccion => ({
        ...seccion,
        items: seccion.items.filter(item => rutasCajero.includes(item.route))
      }))
      .filter(seccion => seccion.items.length > 0)
  }
  return menu
})

// Abre automáticamente la sección de la página actual
watch(
  () => route.path,
  (ruta) => {
    const seccion = menuFiltrado.value.find(s =>
      s.items.some(item => ruta.startsWith(item.route))
    )
    if (seccion) seccionesAbiertas.value[seccion.titulo] = true
  },
  { immediate: true }
)

// Ruta a la que va el logo: el cajero no tiene dashboard, va al punto de venta
const rutaInicio = computed(() => {
  return rolActual.value === 'cajero' ? '/pos' : '/dashboard'
})

const cerrarSesion = async () => {
  try {
    const confirmacion = await Swal.fire({
      title: '¿Cerrar Sesión?',
      text: '¿Estás seguro de que deseas salir del sistema?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, salir',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      background: '#1e293b',
      color: '#ffffff'
    })

    if (!confirmacion.isConfirmed) return

    const token = localStorage.getItem('token')

    await axios.post(
      'http://127.0.0.1:8000/api/auth/logout',
      {},
      { headers: { Authorization: `Bearer ${token}` } }
    )

  } catch (e) {
    console.error('Error al cerrar sesión en el servidor:', e)
  } finally {
    localStorage.clear()
    router.push('/login')
  }
}
</script>