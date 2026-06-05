<template>
  <header
    class="bg-gradient-to-r from-green-900 to-green-700 text-white shadow-md h-16 flex items-center justify-between px-6"
  >
    <!-- Botón para abrir/cerrar sidebar -->
    <button
      @click="$emit('toggle-sidebar')"
      class="text-2xl hover:bg-white/10 p-2 rounded-lg transition"
    >
      ☰
    </button>

    <!-- Título -->
    <h1 class="text-xl font-semibold">
      La económica
    </h1>

    <!-- Usuario -->
    <div class="font-medium">
      Admin
    </div>
  </header>
</template>

<script setup>
defineEmits(['toggle-sidebar'])
</script>