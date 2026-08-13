<template>
  <header class="bg-[#2B3A4A] border-b border-slate-700/60 sticky top-0 z-50 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Marca y Logo Agrandado -->
      <div class="flex items-center gap-3.5">
        <!-- Contenedor blanco para resaltar el logo -->
        <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center p-2 shadow-inner">
          <img 
            src="/nuevo logo.svg" 
            alt="Logo La Económica" 
            class="w-full h-full object-contain filter drop-shadow" 
          />
        </div>
        <div>
          <span class="text-xl font-black tracking-tight text-white block leading-none">
            La Económica
          </span>
          <span class="text-[10px] font-bold text-sky-300 uppercase tracking-widest mt-1 block">
            Chalatenango
          </span>
        </div>
      </div>

      <!-- Usuario y Salir -->
      <div class="flex items-center gap-4">
        <span class="text-sm font-bold text-sky-100 hidden sm:inline">
          {{ userName }}
        </span>

        <button 
          @click="handleLogout" 
          title="Cerrar sesión"
          class="flex items-center gap-2 bg-white/10 hover:bg-red-500/20 hover:text-red-300 text-slate-100 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer border border-white/10"
        >
          <i class="bi bi-box-arrow-right text-base"></i>
          <span>Salir</span>
        </button>
      </div>

    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const userName = ref('Usuario')

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      const user = JSON.parse(storedUser)
      userName.value = user.name || user.username || 'Usuario'
    } catch {
      userName.value = 'Usuario'
    }
  }
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>