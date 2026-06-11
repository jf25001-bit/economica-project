<template>
  <aside
    :class="[
       'bg-[#9FCFCC] text-gray-900 h-screen fixed left-0 top-0 transition-all duration-300 shadow-xl flex flex-col z-40 border-r border-white/5',
      isOpen ? 'w-60' : 'w-20'
    ]"
  >
    <!-- Encabezado -->
     <div 
      :class="[
        'p-4 border-b border-white/10 flex items-center gap-3 transition-all duration-300',
        isOpen ? 'justify-start' : 'justify-center'
      ]"
    >
      <img
        src="/logo.jpeg"
        alt="Logo"
        class="w-12 h-12 rounded-xl object-cover shadow-md shrink-0 border border-white/10"
      />

     <div v-if="isOpen" class="overflow-hidden whitespace-nowrap transition-all duration-300">
        <h1 class="font-bold text-base tracking-wide text-gray-950 leading-tight">La Económica</h1>
      </div>
    </div>

    <!-- Menú -->
     <nav class="flex-1 py-0 space-y-1 overflow-y-auto custom-scrollbar">
      <router-link
        v-for="item in menu"
        :key="item.name"
        :to="item.route"
         class="flex items-center gap-8 px-3 py-3 mx-2 rounded-xl text-gray-700 hover:text-gray-950 hover:bg-white/35 transition-all duration-200 group"
        active-class="bg-white/60 text-gray-950 font-semibold shadow-inner border-1-4 border-white !rounded-l-none"
        :title="!isOpen ? item.name : ''"
      >
        <i :class="[item.icon, 'text-xl transition-transform group-hover:scale-110 shrink-0']"></i>
        <span 
          v-if="isOpen" 
          class="text-sm tracking-wide transition-all duration-300"
        >
          {{ item.name }}
        </span>

      </router-link>
    </nav>

    <!-- Cerrar sesión -->
      <div class="p-3 border-t border-white/30 bg-white/20">
      <button
        @click="cerrarSesion"
        class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-800 hover:text-red-700 hover:bg-red-100/70 transition-all duration-200 group"
        :title="!isOpen ? 'Cerrar sesión' : ''"
      >
         <i class="bi bi-box-arrow-left text-xl transition-transform group-hover:-translate-x-0.5 shrink-0"></i>
        <span 
          v-if="isOpen" 
          class="text-sm font-medium tracking-wide"
        >
          Cerrar sesión
        </span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'
defineProps({
  isOpen: {
    type: Boolean,
    default: true
  }
})

const router = useRouter()

const menu = [
 { name: 'Inicio', route: '/dashboard', icon: 'bi bi-house-door-fill' },
  { name: 'Categorias', route: '/categorias', icon: 'bi bi-grid-3x3-gap-fill' },
  { name: 'Productos', route: '/productos', icon: 'bi bi-box-seam-fill' },
  { name: 'Inventario', route: '/inventario', icon: 'bi bi-archive-fill' },
  { name: 'Compras', route: '/compras', icon: 'bi bi-basket2-fill' },
  { name: 'Proveedores', route: '/proveedores', icon: 'bi bi-building' },
  { name: 'Ventas', route: '/ventas', icon: 'bi bi-cash-coin' },
  { name: 'Usuarios', route: '/usuarios', icon: 'bi bi-person-badge-fill' },
  { name: 'Reportes', route: '/reportes', icon: 'bi bi-bar-chart-line-fill' }
]


const cerrarSesion = async () => {
  try {
    const token = localStorage.getItem('token')

    await axios.post(
      'http://127.0.0.1:8000/api/auth/logout',
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
  } catch (error) {
    console.log('Logout error:', error)
  }

  // eliminar datos locales
  localStorage.removeItem('token')
  localStorage.removeItem('user')

  // regresar al login
  router.push('/')
}
</script>
<style scoped>
/* Opcional: Estilo sutil para la barra de scroll interna del menú si hay muchas opciones */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
