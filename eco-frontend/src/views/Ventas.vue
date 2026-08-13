<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header de la Vista -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Historial de Ventas</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Registro histórico de transacciones realizadas</p>
      </div>

      <router-link
        to="/pos"
        class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl shadow-sm transition-all font-bold text-sm cursor-pointer no-underline"
      >
        <i class="bi bi-calculator-fill text-base text-sky-400"></i>
        <span>Ir al Punto de Venta</span>
      </router-link>
    </div>

    <!-- Contenedor Principal / Tabla sin tonos morados -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Encabezado de la Tarjeta (Ventas Registradas) -->
      <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-700 m-0">
          Ventas Registradas
        </h2>
        <button 
          @click="consultarVentas" 
          class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 hover:text-sky-700 transition-colors cursor-pointer bg-transparent border-0 p-0"
        >
          <i class="bi bi-arrow-clockwise text-sm"></i> 
          <span>Actualizar</span>
        </button>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[650px]">
          <thead>
            <tr class="bg-slate-100 border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
              <th class="px-6 py-3.5">Factura</th>
              <th class="px-6 py-3.5">Cliente</th>
              <th class="px-6 py-3.5">Fecha</th>
              <th class="px-6 py-3.5">Total</th>
              <th class="px-6 py-3.5 text-center">Estado</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100 text-sm">
            <tr
              v-for="venta in ventas"
              :key="venta.id"
              class="hover:bg-slate-50 transition-colors"
            >
              <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700">
                {{ venta.factura || `#${venta.id}` }}
              </td>
              <td class="px-6 py-4 font-bold text-slate-800">
                {{ venta.cliente }}
              </td>
              <td class="px-6 py-4 font-medium text-slate-500">
                {{ formatearFecha(venta.created_at || venta.fecha) }}
              </td>
              <td class="px-6 py-4 font-black text-slate-900">
                ${{ Number(venta.total).toFixed(2) }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Completada
                </span>
              </td>
            </tr>

            <!-- Estado Vacío -->
            <tr v-if="ventas.length === 0">
              <td colspan="5" class="text-center py-12 text-slate-400 font-medium italic">
                <i class="bi bi-receipt text-3xl block mb-2 text-slate-300"></i>
                No hay ventas registradas.
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

const ventas = ref([])

const consultarVentas = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/ventas')
    ventas.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Error cargando historial de ventas:', error)
  }
}

const formatearFecha = (fechaRaw) => {
  if (!fechaRaw) return 'N/A'
  const objFecha = new Date(fechaRaw)
  return isNaN(objFecha.getTime()) ? fechaRaw : objFecha.toLocaleDateString()
}

onMounted(() => {
  consultarVentas()
})
</script>