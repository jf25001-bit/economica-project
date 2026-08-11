<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Historial de Ventas</h1>
        <p class="text-sm text-gray-500">Registro histórico de transacciones realizadas</p>
      </div>

      <router-link
        to="/pos"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium flex items-center gap-2"
      >
        <i class="bi bi-calculator-fill"></i>
        Ir al Punto de Venta
      </router-link>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
      <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">
          Ventas Registradas
        </h2>
        <button 
          @click="consultarVentas" 
          class="text-sm text-[#46674A] hover:underline font-medium"
        >
          <i class="bi bi-arrow-clockwise mr-1"></i> Actualizar
        </button>
      </div>

      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Factura</th>
            <th class="px-6 py-4 font-semibold">Cliente</th>
            <th class="px-6 py-4 font-semibold">Fecha</th>
            <th class="px-6 py-4 font-semibold">Total</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="venta in ventas"
            :key="venta.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-medium font-mono text-sm">
              {{ venta.factura || `#${venta.id}` }}
            </td>
            <td class="px-6 py-4">
              {{ venta.cliente }}
            </td>
            <td class="px-6 py-4">
              {{ formatearFecha(venta.created_at || venta.fecha) }}
            </td>
            <td class="px-6 py-4 font-semibold">
              ${{ Number(venta.total).toFixed(2) }}
            </td>
            <td class="px-6 py-4">
              <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                Completada
              </span>
            </td>
          </tr>

          <tr v-if="ventas.length === 0">
            <td colspan="5" class="text-center py-8 text-gray-500">
              No hay ventas registradas.
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