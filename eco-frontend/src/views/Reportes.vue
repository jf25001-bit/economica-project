<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Reportes</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">La Económica - Panel de Monitoreo</p>
      </div>

      <button
        @click="generarPDFGeneral"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium cursor-pointer"
      >
        <i class="bi bi-file-pdf mr-2"></i>
        Exportar PDF General
      </button>
    </div>

    <!-- Filtro Temporal -->
    <div class="flex justify-end mb-4">
      <select 
        v-model="periodoSeleccionado" 
        @change="obtenerEstadisticas"
        class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-sm font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#46674A]"
      >
        <option value="dia">Reporte de Hoy (Diario)</option>
        <option value="semana">Reporte de la Semana</option>
        <option value="mes">Reporte del Mes</option>
      </select>
    </div>

    <!-- Tarjetas Informativas -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-600">
        <p class="text-gray-500 text-sm font-medium">Ventas del Periodo</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-24 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-gray-800">${{ (tarjetas.ventas_mes || 0).toFixed(2) }}</h2>
      </div>

      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-blue-600">
        <p class="text-gray-500 text-sm font-medium">Compras del Periodo</p>
        <div v-if="cargandoTarjetas" class="h-8 bg-gray-200 rounded w-24 animate-pulse mt-1"></div>
        <h2 v-else class="text-3xl font-bold text-gray-800">${{ (tarjetas.compras_mes || 0).toFixed(2) }}</h2>
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

    <!-- Alerta de Errores de Conexión -->
    <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-sm font-medium">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <span>{{ error }}</span>
    </div>

    <!-- Tabla Dinámica de Descargas -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Reporte</th>
            <th class="px-6 py-4 font-semibold">Descripción</th>
            <th class="px-6 py-4 font-semibold">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="reporte in reportes" :key="reporte.id" class="border-t hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 font-medium text-gray-800">{{ reporte.nombre }}</td>
            <td class="px-6 py-4 text-gray-600">{{ reporte.descripcion }}</td>
            <td class="px-6 py-4">
              <button
                @click="generarPDFColumna(reporte.id)"
                class="bg-[#46674A] hover:bg-[#3b5740] text-white px-4 py-2 rounded-lg transition text-sm cursor-pointer inline-flex items-center"
              >
                <i class="bi bi-download mr-2"></i>
                Generar
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

// URL base de la API
const API_BASE = 'http://localhost:8000/api/auth'

// Variables reactivas organizadas
const periodoSeleccionado = ref('mes')
const cargandoTarjetas = ref(true)
const error = ref(null)

const tarjetas = ref({
  ventas_mes: 0,
  compras_mes: 0,
  productos_totales: 0,
  stock_bajo: 0
})

const reportes = [
  { id: 1, nombre: 'Reporte de Ventas', descripcion: 'Muestra todas las ventas realizadas en el periodo seleccionado.' },
  { id: 2, nombre: 'Reporte de Compras', descripcion: 'Muestra todas las compras registradas a proveedores en el periodo seleccionado.' },
  { id: 3, nombre: 'Reporte de Inventario', descripcion: 'Muestra el stock actual de productos y alertas de reposición.' }
]


async function obtenerEstadisticas() {
  cargandoTarjetas.value = true
  error.value = null
  try {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token')
    const config = {
      headers: {
        'Authorization': `Bearer ${token}`, // Le enviamos el pase de entrada a Laravel
        'Accept': 'application/json'
      }
    }
    const { data } = await axios.get(`${API_BASE}/reportes/tarjetas?periodo=${periodoSeleccionado.value}`, config)
    tarjetas.value = data
  } catch (e) {
    console.error(e)
    // Si Laravel responde 401, le avisamos de forma amigable al usuario
    if (e.response && e.response.status === 401) {
      error.value = 'Sesión expirada o no autorizada. Por favor, vuelve a iniciar sesión.'
    } else {
      error.value = 'No se pudieron sincronizar los datos de las tarjetas con el servidor.'
    }
  } finally {
    cargandoTarjetas.value = false
  }
}

const generarPDFGeneral = () => {
  // Apuntamos a /api/reportes/general directamente sin pasar por /auth/
  window.open(`http://localhost:8000/api/reportes/general?periodo=${periodoSeleccionado.value}`, '_blank')
}

const generarPDFColumna = (id) => {
  if (id === 1) {
    window.open(`http://localhost:8000/api/reportes/general?tipo=ventas&periodo=${periodoSeleccionado.value}`, '_blank')
  } else if (id === 2) {
    window.open(`http://localhost:8000/api/reportes/general?tipo=compras&periodo=${periodoSeleccionado.value}`, '_blank')
  } else if (id === 3) {
    window.open(`http://localhost:8000/api/reportes/general?tipo=inventario`, '_blank')
  }
}


onMounted(() => {
  obtenerEstadisticas()
})
</script>