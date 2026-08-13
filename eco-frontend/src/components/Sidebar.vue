<template>
  <aside
    :class="[
      'bg-[#0F172A] text-slate-400 h-screen fixed left-0 top-0 transition-all duration-300 shadow-2xl flex flex-col z-40 border-r border-slate-800/80',
      isOpen ? 'w-64' : 'w-20'
    ]"
  >
    <!-- Logo y Título -->
    <div 
      :class="[
        'h-[73px] px-5 flex items-center gap-3.5 transition-all duration-300 border-b border-slate-800/60',
        isOpen ? 'justify-start' : 'justify-center'
      ]"
    >
      <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-emerald-400 p-0.5 shrink-0 shadow-lg shadow-sky-500/20">
        <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center p-1.5">
          <img src="/nuevo logo.svg" alt="Logo" class="w-full h-full object-contain" />
        </div>
      </div>

      <div v-if="isOpen" class="overflow-hidden whitespace-nowrap">
        <h1 class="font-extrabold text-sm text-white tracking-tight leading-none">La Económica</h1>
        <span class="text-[10px] text-sky-400 font-bold uppercase tracking-widest block mt-1">POS System</span>
      </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="flex-1 py-6 px-3 space-y-1.5 overflow-y-auto custom-scrollbar">
      <router-link
        v-for="item in menu"
        :key="item.name"
        :to="item.route"
        class="no-underline flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all duration-200 group font-medium text-sm"
        active-class="!bg-gradient-to-r !from-sky-500 !to-blue-600 !text-white !font-bold shadow-md shadow-sky-500/20"
        :title="!isOpen ? item.name : ''"
      >
        <i :class="[item.icon, 'text-lg group-hover:scale-110 transition-transform shrink-0']"></i>
        <span v-if="isOpen" class="truncate no-underline">{{ item.name }}</span>
      </router-link>
    </nav>

    <!-- Cerrar Sesión -->
    <div class="p-3 border-t border-slate-800/60">
      <button
        @click="cerrarSesion"
        class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl text-red-400 bg-red-500/10 hover:bg-red-500 hover:text-white transition-all duration-200 text-sm font-bold cursor-pointer border border-red-500/10 hover:border-transparent shadow-sm"
        :title="!isOpen ? 'Cerrar sesión' : ''"
      >
        <i class="bi bi-box-arrow-left text-lg shrink-0"></i>
        <span v-if="isOpen">Cerrar sesión</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'

defineProps({ isOpen: Boolean })
const router = useRouter()

const menu = [
  { name: 'Inicio', route: '/dashboard', icon: 'bi bi-house-door-fill' },
  { name: 'Punto de Venta', route: '/pos', icon: 'bi bi-calculator-fill' },
  { name: 'Ventas', route: '/ventas', icon: 'bi bi-cash-coin' },
  { name: 'Categorias', route: '/categorias', icon: 'bi bi-grid-3x3-gap-fill' },
  { name: 'Productos', route: '/productos', icon: 'bi bi-box-seam-fill' },
  { name: 'Inventario', route: '/inventario', icon: 'bi bi-archive-fill' },
  { name: 'Compras', route: '/compras', icon: 'bi bi-basket2-fill' },
  { name: 'Proveedores', route: '/proveedores', icon: 'bi bi-building' },
  { name: 'Usuarios', route: '/usuarios', icon: 'bi bi-person-badge-fill' },
  { name: 'Reportes', route: '/reportes', icon: 'bi bi-bar-chart-line-fill' }
]

const cerrarSesion = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.post('http://127.0.0.1:8000/api/auth/logout', {}, { headers: { Authorization: `Bearer ${token}` } })
  } catch (e) {}
  localStorage.clear()
  router.push('/')
}
</script>

<style scoped>
/* Elimina cualquier subrayado de enlaces por defecto */
a {
  text-decoration: none !important;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.08);
  border-radius: 4px;
}
</style>
