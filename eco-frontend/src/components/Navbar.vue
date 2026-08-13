<template>
  <header class="bg-[#2B3A4A] text-white h-[73px] sticky top-0 z-30 shadow-md border-b border-slate-700/60 px-6 flex items-center justify-between">
    
    <!-- Lado Izquierdo: Botón Sidebar + Sucursal -->
    <div class="flex items-center gap-4">
      <button 
        @click="$emit('toggle-sidebar')" 
        class="w-10 h-10 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 flex items-center justify-center transition-all cursor-pointer"
        title="Menú"
      >
        <i class="bi bi-list text-2xl"></i>
      </button>

      <div class="hidden sm:flex flex-col">
        <span class="text-xs font-extrabold uppercase tracking-widest text-sky-400">Sucursal</span>
        <span class="text-sm font-black text-slate-200 leading-tight">Chalatenango</span>
      </div>
    </div>

    <!-- Lado Derecho: Usuario -->
    <div class="flex items-center gap-3 sm:gap-4">
      <div class="flex items-center gap-3 bg-slate-800/80 border border-slate-700/80 px-4 py-2 rounded-2xl shadow-inner">
        <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-sm border border-sky-500/30">
          <i class="bi bi-person-fill"></i>
        </div>
        <div class="flex flex-col text-left">
          <span class="text-xs font-bold text-slate-100 capitalize leading-tight">
            {{ usuarioNombre }}
          </span>
          <span class="text-[10px] font-semibold text-sky-400 uppercase tracking-wider">
            {{ usuarioRol }}
          </span>
        </div>
      </div>
    </div>

  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'

defineEmits(['toggle-sidebar'])

const usuarioNombre = ref('Usuario')
const usuarioRol = ref('Administrador')

onMounted(() => {
  try {
    const rawUser = localStorage.getItem('user')
    if (rawUser) {
      const user = JSON.parse(rawUser)

      // Obtener Nombre
      usuarioNombre.value = user.nombre || user.name || user.username || 'Usuario'

      // Extraer el texto del Rol correctamente sin renderizar el JSON
      if (typeof user.rol === 'object' && user.rol !== null) {
        usuarioRol.value = user.rol.NOMBRE || user.rol.nombre || 'Admin'
      } else if (typeof user.role === 'object' && user.role !== null) {
        usuarioRol.value = user.role.NOMBRE || user.role.nombre || 'Admin'
      } else if (user.rol || user.role) {
        usuarioRol.value = user.rol || user.role
      }
    }
  } catch (error) {
    console.error('Error parseando usuario:', error)
  }
})
</script>