<template>
  <!-- Si la ruta es /login, mostrar SOLO el Login -->
  <router-view v-if="$route.path === '/' || $route.path === '/login'" />

  <!-- Para las demás rutas, mostrar Sidebar + Navbar + contenido -->
  <div v-else class="flex">
    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" />

    <!-- Contenido principal -->
    <div
      :class="[
        'flex-1 min-h-screen bg-gray-100 transition-all duration-300',
        sidebarOpen ? 'ml-60' : 'ml-20'
      ]"
    >
      <!-- Navbar -->
      <Navbar @toggle-sidebar="toggleSidebar" />

      <!-- Vista actual -->
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Navbar from './components/Navbar.vue'

const route = useRoute()
const sidebarOpen = ref(true)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>