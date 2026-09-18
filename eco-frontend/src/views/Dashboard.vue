<template>
  <div class="min-h-screen bg-slate-50/50 p-6 sm:p-8 relative">
    
    <!-- MODAL DE BLOQUEO OBLIGATORIO (Solo para Cajeros con caja cerrada) -->
    <div 
      v-if="esCajero && !cajaAbierta" 
      class="fixed inset-0 z-50 bg-slate-900/90 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in"
    >
      <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center shadow-2xl border border-slate-100 flex flex-col items-center">
        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-3xl flex items-center justify-center text-4xl mb-6 shadow-inner">
          <i class="bi bi-lock-fill"></i>
        </div>

        <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">
          Apertura de Caja Requerida
        </h2>
        
        <p class="text-slate-600 text-sm font-medium mb-8 leading-relaxed">
          No tienes una caja abierta para operar. Debes realizar la apertura inicial para acceder a las funciones del sistema.
        </p>

        <router-link 
          to="/caja" 
          class="w-full bg-amber-500 hover:bg-amber-600 text-white font-extrabold py-4 px-6 rounded-2xl text-base shadow-xl shadow-amber-500/30 transition-all active:scale-95 flex items-center justify-center gap-3 cursor-pointer"
        >
          <i class="bi bi-cash-stack text-xl"></i>
          <span>Ir a Abrir Caja</span>
        </router-link>
      </div>
    </div>

    <!-- BANNER INFORMATIVO (Solo para Administradores / Roles no-cajero) -->
    <div 
      v-if="!cajaAbierta && !esCajero" 
      class="mb-8 bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-3xl shadow-md border border-amber-200/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-fade-in"
    >
      <div class="flex items-center gap-4">
        <div class="p-3 bg-amber-100 text-amber-700 rounded-2xl flex-shrink-0">
          <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
        </div>
        <div>
          <h4 class="text-amber-950 font-black text-base sm:text-lg">Atención: La caja se encuentra cerrada</h4>
          <p class="text-amber-800 text-sm font-medium mt-0.5">
            Debes realizar la apertura de caja antes de comenzar a realizar ventas y operar en el sistema.
          </p>
        </div>
      </div>

      <router-link 
        to="/caja" 
        class="bg-amber-500 hover:bg-amber-600 text-white font-extrabold px-6 py-3 rounded-2xl text-sm transition-all shadow-lg shadow-amber-500/20 whitespace-nowrap flex items-center gap-2 active:scale-95 cursor-pointer self-end sm:self-auto"
      >
        <i class="bi bi-cash-stack text-lg"></i>
        <span>Abrir Caja Ahora</span>
      </router-link>
    </div>

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

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

    <!-- Sección de Bienvenida / Acceso Directo -->
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
            :to="cajaAbierta ? '/pos' : '/caja'"
            :class="cajaAbierta 
              ? 'bg-sky-400 hover:bg-sky-300 text-slate-950 shadow-sky-400/20' 
              : 'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20'"
            class="inline-flex items-center gap-2 font-extrabold px-6 py-3.5 rounded-2xl text-sm transition-all shadow-lg active:scale-95"
          >
            <i :class="cajaAbierta ? 'bi bi-cart-check-fill' : 'bi bi-cash-stack'" class="text-lg"></i>
            <span>{{ cajaAbierta ? 'Ir al Punto de Venta' : 'Abrir Caja para Vender' }}</span>
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
import { cajaService } from '@/services/cajaService'

const totalUsuarios = ref(0)
const totalProductos = ref(0)
const totalVentasHoy = ref(0.00)
const cajaAbierta = ref(true)
const esCajero = ref(false)

const fechaActual = computed(() => {
  const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  return new Date().toLocaleDateString('es-ES', opciones)
})

onMounted(async () => {
  try {
    const usuarioGuardado = localStorage.getItem('user')
    if (usuarioGuardado) {
      const user = JSON.parse(usuarioGuardado)
      const rol = user?.rol?.nombre || user?.rol || ''
      esCajero.value = (rol.toLowerCase() === 'cajero')
    }
  } catch (e) {
    console.error('Error al leer usuario:', e)
  }

  try {
    try {
      const resCaja = await cajaService.obtenerEstado()
      cajaAbierta.value = !!(resCaja && resCaja.caja)
    } catch (e) {
      cajaAbierta.value = false
    }

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