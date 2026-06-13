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

      <div>
        <h1 class="text-lg font-bold leading-none">
        La economica
        </h1>
        
      </div>
    </div>

    <!-- Usuario -->
    <div
      class="flex items-center gap-3 bg-white/10 px-6 py-2 rounded-xl"
    >
      <div
       
      >
        
      </div>

      <div class="hidden sm:block">
        <p class="text-sm font-semibold">
          Admin
        </p>
        
      </div>
    </div>
  </header>
</template>

<script setup>
defineEmits(['toggle-sidebar'])
</script>