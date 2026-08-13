<template>
  <div class="min-h-screen bg-slate-50/50 p-6 sm:p-8">
    
    <!-- Encabezado de Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">
          Reportes
        </h1>
        <p class="text-slate-500 text-sm font-medium mt-1">
          La Económica — Panel de Monitoreo
        </p>
      </div>

      <button
        @click="generarPDFGeneral"
        class="inline-flex items-center justify-center gap-2 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold px-6 py-3 rounded-2xl shadow-lg shadow-[#2B3A4A]/20 transition-all active:scale-95 cursor-pointer"
      >
        <i class="bi bi-file-earmark-pdf-fill text-sky-400 text-lg"></i>
        <span>Exportar PDF General</span>
      </button>
    </div>

    <!-- Filtro de Periodo -->
    <div class="flex justify-end mb-6">
      <div class="relative w-full sm:w-64">
        <select 
          v-model="periodoSeleccionado" 
          @change="obtenerEstadisticas"
          class="w-full pl-4 pr-10 py-2.5 bg-white border border-slate-200/80 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium appearance-none cursor-pointer shadow-sm"
        >
          <option value="dia">Reporte de Hoy (Diario)</option>
          <option value="semana">Reporte de la Semana</option>
          <option value="mes">Reporte del Mes</option>
        </select>
        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
          <i class="bi bi-chevron-down text-xs"></i>
        </span>
      </div>
    </div>

    <!-- Tarjetas de Métricas del Periodo (KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      
      <!-- Ventas del Periodo -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Ventas del Periodo</span>
          <div v-if="cargandoTarjetas" class="h-8 bg-slate-200/70 rounded-xl w-24 animate-pulse mt-2"></div>
          <h2 v-else class="text-2xl sm:text-3xl font-black text-slate-800 mt-1">${{ (tarjetas.ventas_mes || 0).toFixed(2) }}</h2>
          <span class="text-emerald-600 text-xs font-bold inline-flex items-center gap-1 mt-2">
            <i class="bi bi-arrow-up-right text-xs"></i> Ingresos acumulados
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-2xl">
          <i class="bi bi-cash-stack"></i>
        </div>
      </div>

      <!-- Compras del Periodo -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Compras del Periodo</span>
          <div v-if="cargandoTarjetas" class="h-8 bg-slate-200/70 rounded-xl w-24 animate-pulse mt-2"></div>
          <h2 v-else class="text-2xl sm:text-3xl font-black text-slate-800 mt-1">${{ (tarjetas.compras_mes || 0).toFixed(2) }}</h2>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            Egresos registrados
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-2xl">
          <i class="bi bi-cart-check-fill"></i>
        </div>
      </div>

      <!-- Productos Registrados -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Productos Registrados</span>
          <div v-if="cargandoTarjetas" class="h-8 bg-slate-200/70 rounded-xl w-16 animate-pulse mt-2"></div>
          <h2 v-else class="text-2xl sm:text-3xl font-black text-slate-800 mt-1">{{ tarjetas.productos_totales || 0 }}</h2>
          <span class="text-slate-400 text-xs font-medium inline-flex items-center gap-1 mt-2">
            En catálogo
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-[#2B3A4A] text-sky-400 flex items-center justify-center text-2xl shadow-lg shadow-[#2B3A4A]/20">
          <i class="bi bi-box-seam-fill"></i>
        </div>
      </div>

      <!-- Productos con Stock Bajo -->
      <div class="bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 flex items-center justify-between">
        <div>
          <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Stock Bajo</span>
          <div v-if="cargandoTarjetas" class="h-8 bg-slate-200/70 rounded-xl w-16 animate-pulse mt-2"></div>
          <h2 v-else class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ tarjetas.stock_bajo || 0 }}</h2>
          <span class="text-amber-600 text-xs font-bold inline-flex items-center gap-1 mt-2">
            <i class="bi bi-exclamation-triangle-fill"></i> Requiere atención
          </span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-2xl">
          <i class="bi bi-graph-down-arrow"></i>
        </div>
      </div>

    </div>

    <!-- Alerta de Error -->
    <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200/80 text-red-700 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
      <i class="bi bi-exclamation-triangle-fill text-lg text-red-500"></i>
      <span>{{ error }}</span>
    </div>

    <!-- Tabla de Reportes -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h2 class="text-lg font-black text-slate-800">Reportes Disponibles</h2>
        <p class="text-slate-500 text-xs font-medium mt-0.5">Selecciona y descarga el documento en PDF</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider">
              <th class="px-6 py-4">Reporte</th>
              <th class="px-6 py-4">Descripción</th>
              <th class="px-6 py-4 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="reporte in reportes" :key="reporte.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-800">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-700 border border-sky-100 flex items-center justify-center text-sm">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                  </div>
                  <span>{{ reporte.nombre }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-600 text-sm font-medium">
                {{ reporte.descripcion }}
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  @click="generarPDFColumna(reporte.id)"
                  class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-[#2B3A4A] hover:text-white text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition cursor-pointer"
                >
                  <i class="bi bi-download"></i>
                  <span>Generar PDF</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API_BASE = 'http://localhost:8000/api/auth'

const periodoSeleccionado = ref('mes')
const cargandoTarjetas = ref(true)
const error = ref(null)

const tarjetas = ref({
  ventas_mes: 0,
  compras_mes: 0,
  productos_totales: 0,
  stock_bajo: 0
})

const reportes = ref([
  { id: 1, nombre: 'Reporte de Ventas', descripcion: 'Muestra todas las ventas realizadas en el periodo seleccionado.' },
  { id: 2, nombre: 'Reporte de Compras', descripcion: 'Muestra todas las compras registradas a proveedores en el periodo seleccionado.' }
])

async function obtenerEstadisticas() {
  cargandoTarjetas.value = true
  error.value = null
  try {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token')
    const config = {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    }
    const { data } = await axios.get(`${API_BASE}/reportes/tarjetas?periodo=${periodoSeleccionado.value}`, config)
    tarjetas.value = data
  } catch (e) {
    console.error(e)
    if (e.response && e.response.status === 401) {
      error.value = 'Sesión expirada o no autorizada. Por favor, vuelve a iniciar sesión.'
    } else {
      error.value = 'No se pudieron sincronizar los datos de las tarjetas con el servidor.'
    }
  } finally {
    gatherStatsCompleted()
  }
}

function gatherStatsCompleted() {
  cargandoTarjetas.value = false
}

const generarPDFGeneral = () => {
  window.open(`http://localhost:8000/api/reportes/general?tipo=general&periodo=${periodoSeleccionado.value}`, '_blank')
}

const generarPDFColumna = (id) => {
  if (id === 1) {
    window.open(`http://localhost:8000/api/reportes/general?tipo=ventas&periodo=${periodoSeleccionado.value}`, '_blank')
  } else if (id === 2) {
    window.open(`http://localhost:8000/api/reportes/general?tipo=compras&periodo=${periodoSeleccionado.value}`, '_blank')
  }
}

onMounted(() => {
  obtenerEstadisticas()
})
</script>