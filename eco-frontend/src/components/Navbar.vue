<template>
  <header
    class="bg-[#46674A] text-white shadow-lg h-16 flex items-center justify-between px-6 border-b border-[#3b5740]"
  >
    <!-- Botón menú -->
    <button
      @click="$emit('toggle-sidebar')"
      class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-all"
    >
      <i class="bi bi-list text-2xl"></i>
    </button>

    <!-- Logo / Título -->
    <div class="flex items-center gap-200">
      <div
        class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center"
      >
        <i class="bi bi-shop text-lg"></i>
      </div>

      <h1 class="text-lg font-bold leading-none">
        La economica
      </h1>
    </div>

    <!-- Usuario -->
    <div
      class="flex items-center gap-3 bg-white/10 px-6 py-2 rounded-xl"
    >
      <div
        class="relative cursor-pointer user-box"
        @click="toggleInfo"
      >
        <p class="text-sm font-semibold">
          {{ user?.name || 'Usuario' }}
        </p>

        <p class="text-xs text-white/70">
          {{ user?.rol?.nombre || 'Sin rol' }}
        </p>

        <!-- DROPDOWN -->
        <div
          v-if="mostrarInfo"
          class="absolute right-0 mt-2 w-48 bg-white text-black rounded-xl shadow-lg p-3 z-50"
        >
          <p class="text-xs text-gray-500">Usuario</p>
          <p class="font-bold">{{ user?.name }}</p>

          <p class="text-xs text-gray-500 mt-2">Rol</p>
          <p class="font-bold">{{ user?.rol?.nombre }}</p>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'

defineEmits(['toggle-sidebar'])

const user = ref(null)
const mostrarInfo = ref(false)

onMounted(() => {
  user.value = JSON.parse(localStorage.getItem('user'))

  // cerrar si se hace click fuera
  window.addEventListener('click', (e) => {
    const el = document.querySelector('.user-box')
    if (el && !el.contains(e.target)) {
      mostrarInfo.value = false
    }
  })
})

const toggleInfo = () => {
  mostrarInfo.value = !mostrarInfo.value
}
</script>