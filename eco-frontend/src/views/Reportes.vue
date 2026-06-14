<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Reportes</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">La Económica - Panel de Monitoreo</p>
      </div>

      <button
        @click="generarPDFGeneral"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium cursor-pointer inline-flex items-center"
      >
        <i class="bi bi-file-earmark-pdf-fill mr-2"></i>
        Exportar Balance General
      </button>
    </div>

    <div class="flex justify-end mb-4">
      <select 
        v-model="periodoSeleccionado" 
        @change="obtenerEstadisticas"
        class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-sm font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#46674A] cursor-pointer"
      >
        <option value="dia">Reporte de Hoy (Diario)</option>
        <option value="semana">Reporte de la Semana</option>
        <option value="mes">Reporte del Mes</option>
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-600">
        <p class="text-gray-500 text-sm font-medium">Ventas del Periodo</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-24 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-gray-800">${{ (tarjetas.ventas_periodo || 0).toFixed(2) }}</h2>
      </div>

      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-blue-600">
        <p class="text-gray-500 text-sm font-medium">Compras del Periodo</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-24 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-gray-800">${{ (tarjetas.compras_periodo || 0).toFixed(2) }}</h2>
      </div>

      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-orange-600">
        <p class="text-gray-500 text-sm font-medium">Productos Registrados</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-16 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-gray-800">{{ tarjetas.productos_totales || 0 }}</h2>
      </div>

      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-red-600">
        <p class="text-gray-500 text-sm font-medium">Productos con Stock Bajo</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-16 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-red-600">{{ tarjetas.stock_bajo || 0 }}</h2>
      </div>
    </div>

    <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-sm font-medium">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <span>{{ error }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr class="text-left text-gray-700 border-b border-gray-100">
            <th class="px-6 py-4 font-semibold text-sm">Módulo / Reporte</th>
            <th class="px-6 py-4 font-semibold text-sm">Descripción Operativa</th>
            <th class="px-6 py-4 font-semibold text-sm text-center">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="reporte in reportes" :key="reporte.id" class="border-b last:border-none hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 font-semibold text-gray-800 text-sm">{{ reporte.nombre }}</td>
            <td class="px-6 py-4 text-gray-600 text-sm">{{ reporte.descripcion }}</td>
            <td class="px-6 py-4 text-center">
              <button
                @click="generarPDFColumna(reporte.id)"
                class="bg-[#46674A] hover:bg-[#3b5740] text-white px-4 py-2 rounded-lg transition text-xs font-medium cursor-pointer inline-flex items-center shadow-sm"
              >
                <i class="bi bi-printer-fill mr-1.5"></i>
                Generar PDF
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Configuración de Endpoints e Inyección de URL Base
const API_BASE = 'http://localhost:8000/api'

// Estado Reactivo Controlado
const periodoSeleccionado = ref('mes')
const cargandoTarjetas = ref(true)
const error = ref(null)

const tarjetas = ref({
  ventas_periodo: 0,
  compras_periodo: 0,
  productos_totales: 0,
  stock_bajo: 0
})

// Catálogo Maestro de Reportes Específicos
const reportes = ref([
  { id: 1, nombre: 'Reporte Analítico de Ventas', descripcion: 'Muestra el desglose detallado de ventas, clientes, métodos de pago y artículos despachados.' },
  { id: 2, nombre: 'Reporte General de Compras', descripcion: 'Auditoría integral de compras registradas a proveedores con número de factura física.' }
])

// Recuperación Segura de Token de Sesión Activa
const obtenerToken = () => localStorage.getItem('token') || sessionStorage.getItem('token')

async function obtenerEstadisticas() {
  cargandoTarjetas.value = true
  error.value = null
  try {
    const token = obtenerToken()
    const config = {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    }
    
    // CORRECCIÓN DE RUTA: Quitamos el "/auth" para que busque bien el endpoint público
    const { data } = await axios.get(`${API_BASE}/reportes/tarjetas?periodo=${periodoSeleccionado.value}`, config)
    
    // MAPEO DE DATOS: Aseguramos la compatibilidad con lo que devuelve el backend
    if (data.status === 'success') {
      tarjetas.value = {
        ventas_periodo: data.tarjetas.ventas_periodo,
        compras_periodo: data.tarjetas.compras_periodo,
        productos_totales: data.tarjetas.productos_registrados, // Mapeado de productos_registrados
        stock_bajo: data.tarjetas.productos_bajo_stock          // Mapeado de productos_bajo_stock
      }
    }
  } catch (e) {
    console.error(e)
    if (e.response && e.response.status === 401) {
      error.value = 'Su sesión ha expirado. Por favor, vuelva a ingresar al sistema.'
    } else {
      error.value = 'Fallo de enlace: No se pudieron sincronizar los KPIs globales.'
    }
  } finally {
    cargandoTarjetas.value = false
  }
}

// Balance General Unificado (Ventas vs Compras)
const generarPDFGeneral = () => {
  const token = obtenerToken()
  const url = `${API_BASE}/reportes/general?tipo=general&periodo=${periodoSeleccionado.value}&token=${token}`
  window.open(url, '_blank')
}

// Reportes Individuales Filtrados por Fila
const generarPDFColumna = (id) => {
  const token = obtenerToken()
  const tipoReporte = id === 1 ? 'ventas' : 'compras'
  const url = `${API_BASE}/reportes/general?tipo=${tipoReporte}&periodo=${periodoSeleccionado.value}&token=${token}`
  window.open(url, '_blank')
}

onMounted(() => {
  obtenerEstadisticas()
})
</script>