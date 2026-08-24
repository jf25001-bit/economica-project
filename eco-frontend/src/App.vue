<template>
  <router-view v-if="$route.path === '/' || $route.path === '/login'" />

  <div v-else class="relative min-h-screen w-full bg-[#0d1424] m-0 p-0 overflow-x-hidden">
    <!-- Sidebar -->
    <Sidebar :isOpen="sidebarOpen" />

    <!-- Contenido principal -->
    <div
      :class="[
        'min-h-screen flex flex-col bg-slate-50 transition-all duration-300 m-0 p-0',
        sidebarOpen ? 'md:ml-60 ml-0' : 'md:ml-20 ml-0'
      ]"
    >
      <!-- Navbar -->
      <Navbar class="top-0 left-0 w-full" @toggle-sidebar="toggleSidebar" />

      <!-- Vista actual -->
      <main class="flex-1 p-0 m-0">
        <router-view />
      </main>
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

<style>
html, body, #app {
  margin: 0 !important;
  padding: 0 !important;
  border: none !important;
  background-color: #0d1424 !important;
}
</style>