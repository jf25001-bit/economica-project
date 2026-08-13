<template>
  <div class="min-h-screen bg-slate-50/50 p-6 sm:p-8">
    
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">
          Panel Principal
        </h1>
        <p class="text-slate-500 text-sm font-medium mt-1">
          Bienvenido al sistema de gestión de La Económica
        </p>
      </div>

      <div class="inline-flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-slate-200/80 shadow-sm text-slate-600 text-xs font-bold">
        <i class="bi bi-calendar3 text-sky-500"></i>
        <span>{{ fechaActual }}</span>
      </div>
    </div>

    <!-- Tarjetas de Resumen (KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
      
      <!-- Usuarios Registrados -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Usuarios</span>
          <h3 class="text-3xl font-black text-slate-800 mt-1">{{ totalUsuarios }}</h3>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            Cuentas en el sistema
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-[#2B3A4A] text-sky-400 flex items-center justify-center text-2xl shadow-lg shadow-[#2B3A4A]/20">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>

      <!-- Roles Configurados -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Roles</span>
          <h3 class="text-3xl font-black text-slate-800 mt-1">{{ totalRoles }}</h3>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            Niveles de acceso
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-2xl">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
      </div>

      <!-- Estado del Servidor / Sistema -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Estado</span>
          <h3 class="text-2xl font-black text-emerald-600 mt-1">Activo</h3>
          <span class="text-emerald-600 text-xs font-bold inline-flex items-center gap-1 mt-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sistema en línea
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-2xl">
          <i class="bi bi-check-circle-fill"></i>
        </div>
      </div>

    </div>

    <!-- Sección de Bienvenida / Accesos Rápidos -->
    <div class="bg-gradient-to-br from-[#2B3A4A] via-[#23303E] to-[#1F2B37] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
      <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl"></div>
      
      <div class="relative z-10 max-w-xl">
        <h2 class="text-2xl font-black tracking-tight mb-2">
          ¡Hola de nuevo!
        </h2>
        <p class="text-sky-200/80 text-sm font-medium mb-6">
          Selecciona una de las opciones para comenzar a gestionar los datos de la aplicación.
        </p>

        <div class="flex flex-wrap gap-3">
          <router-link
            to="/usuarios"
            class="inline-flex items-center gap-2 bg-sky-400 hover:bg-sky-300 text-slate-900 font-bold px-5 py-3 rounded-2xl text-sm transition shadow-lg shadow-sky-400/20"
          >
            <i class="bi bi-person-gear text-base"></i>
            <span>Gestionar Usuarios</span>
          </router-link>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getUsuarios } from '@/services/usuarioService'
import { getRoles } from '@/services/rolService'

const totalUsuarios = ref(0)
const totalRoles = ref(0)

const fechaActual = computed(() => {
  const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date().toLocaleDateString('es-ES', opciones)
})

onMounted(async () => {
  try {
    const usuarios = await getUsuarios()
    const roles = await getRoles()
    totalUsuarios.value = usuarios.length || 0
    totalRoles.value = roles.length || 0
  } catch (error) {
    console.error('Error al cargar datos del dashboard:', error)
  }
})
</script>