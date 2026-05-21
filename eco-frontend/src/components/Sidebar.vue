<template>
  <aside
    :class="[
      'bg-[#405c44]  text-white h-screen fixed left-0 top-0 transition-all duration-300 shadow-lg flex flex-col',
      isOpen ? 'w-60' : 'w-20'
    ]"
  >
    <!-- Encabezado -->
    <div class="p-1 border-b border-white/20 flex items-center gap-3">
      <img
        src="/logo.jpeg"
        alt="Logo"
        class="w-15 h-16 rounded"
      />

      <div v-if="isOpen">
        <h1 class="font-bold text-lg">La Económica</h1>
        <p class="text-sm text-white/80">Admin</p>
      </div>
    </div>

    <!-- Menú -->
    <nav class="flex-1 py-4">
      <router-link
        v-for="item in menu"
        :key="item.name"
        :to="item.route"
        class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg hover:bg-white/10 transition"
        active-class="bg-white/20"
      >
       <i :class="item.icon" class="text-xl text-black"></i>
       <span v-if="isOpen" class="text-black font-medium">
  {{ item.name }}
</span>
      </router-link>
    </nav>

    <!-- Cerrar sesión -->
    <div class="p-4 border-t border-white/20">
      <button
        @click="cerrarSesion"
        class="flex items-center gap-3 w-full px-4 py-2 rounded-lg hover:bg-white/10 transition"
      >
        <span class="text-xl">↩</span>
        <span v-if="isOpen">Cerrar sesión</span>
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
  { name: 'Inicio', route: '/dashboard', icon: 'bi bi-house' },
  { name: 'Productos', route: '/productos', icon: 'bi bi-box-seam' },
  { name: 'Ventas', route: '/ventas', icon: 'bi bi-tag' },
  { name: 'Compras', route: '/compras', icon: 'bi bi-cart' },
  { name: 'Inventario', route: '/inventario', icon: 'bi bi-archive' },
  { name: 'Usuarios', route: '/usuarios', icon: 'bi bi-people' },
  { name: 'Reportes', route: '/reportes', icon: 'bi bi-bar-chart' }
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