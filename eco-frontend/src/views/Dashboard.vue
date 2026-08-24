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

      <div class="inline-flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-slate-200/80 shadow-sm text-slate-600 text-xs font-bold self-start sm:self-auto">
        <i class="bi bi-calendar3 text-sky-500"></i>
        <span class="capitalize">{{ fechaActual }}</span>
      </div>
    </div>

    <!-- Tarjetas de Resumen (3 Columnas) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      
      <!-- Ventas del Día -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between transition-transform hover:-translate-y-1">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Ventas Hoy</span>
          <h3 class="text-2xl sm:text-3xl font-black text-slate-800 mt-1">${{ totalVentasHoy.toFixed(2) }}</h3>
          <span class="text-emerald-600 text-xs font-bold inline-flex items-center gap-1 mt-2">
            <i class="bi bi-graph-up-arrow"></i> Transacciones del día
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-2xl">
          <i class="bi bi-currency-dollar"></i>
        </div>
      </div>

      <!-- Productos Registrados -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between transition-transform hover:-translate-y-1">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Productos</span>
          <h3 class="text-3xl font-black text-slate-800 mt-1">{{ totalProductos }}</h3>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            Catálogo disponible
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-2xl">
          <i class="bi bi-box-seam-fill"></i>
        </div>
      </div>

      <!-- Usuarios del Sistema -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between transition-transform hover:-translate-y-1">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Usuarios</span>
          <h3 class="text-3xl font-black text-slate-800 mt-1">{{ totalUsuarios }}</h3>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            Cuentas activas
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-[#0b121e] text-sky-400 flex items-center justify-center text-2xl shadow-lg shadow-[#0b121e]/20 border border-slate-800">
          <i class="bi bi-people-fill"></i>
        </div>
      </div>

    </div>

    <!-- Sección de Bienvenida / Acceso Directo al POS -->
    <div class="bg-gradient-to-br from-[#0b121e] via-[#0e1626] to-[#111c30] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
      <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl"></div>
      
      <div class="relative z-10 max-w-xl">
        <h2 class="text-2xl font-black tracking-tight mb-2">
          ¡Hola de nuevo!
        </h2>
        <p class="text-sky-200/80 text-sm font-medium mb-6">
          Comienza a procesar ventas de forma rápida y sencilla desde el punto de venta.
        </p>

        <div class="flex flex-wrap gap-3">
          <router-link
            to="/pos"
            class="inline-flex items-center gap-2 bg-sky-400 hover:bg-sky-300 text-slate-950 font-extrabold px-6 py-3.5 rounded-2xl text-sm transition-all shadow-lg shadow-sky-400/20 active:scale-95"
          >
            <i class="bi bi-cart-check-fill text-lg"></i>
            <span>Ir al Punto de Venta</span>
          </router-link>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { getUsuarios } from '@/services/usuarioService'
import { getProductos } from '@/services/productoService'

const totalUsuarios = ref(0)
const totalProductos = ref(0)
const totalVentasHoy = ref(0.00)

const fechaActual = computed(() => {
  const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date().toLocaleDateString('es-ES', opciones)
})

onMounted(async () => {
  try {
    const usuarios = await getUsuarios()
    totalUsuarios.value = Array.isArray(usuarios) ? usuarios.length : (usuarios.data?.length || 0)

    if (typeof getProductos === 'function') {
      const productos = await getProductos()
      totalProductos.value = Array.isArray(productos) ? productos.length : (productos.data?.length || 0)
    }

    const token = localStorage.getItem('token')
    const resVentas = await axios.get('http://127.0.0.1:8000/api/ventas', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    const ventas = resVentas.data?.data || resVentas.data || []
    const hoy = new Date().toISOString().split('T')[0]
    
    totalVentasHoy.value = ventas
      .filter(v => v.created_at && v.created_at.startsWith(hoy))
      .reduce((sum, v) => sum + parseFloat(v.total || 0), 0)

  } catch (error) {
    console.error('Error cargando métricas:', error)
  }
})
</script>