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

    <!-- Contenedor Principal -->
    <div 
      class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden isolate relative"
      style="clip-path: inset(0 rounded 1rem);"
    >
      <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-700 m-0">
          Ventas Registradas
        </h2>
      </div>

      <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-separate border-spacing-0 min-w-[650px]">
          <thead>
            <tr class="bg-slate-100/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
              <th class="px-6 py-3.5 border-b border-slate-200">Factura</th>
              <th class="px-6 py-3.5 border-b border-slate-200">Cliente</th>
              <th class="px-6 py-3.5 border-b border-slate-200">Fecha</th>
              <th class="px-6 py-3.5 border-b border-slate-200">Total</th>
              <th class="px-6 py-3.5 text-center border-b border-slate-200">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100 text-sm">
            <tr
              v-for="venta in ventas"
              :key="venta.id"
              class="hover:bg-slate-50 transition-colors"
            >
              <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700 border-b border-slate-100">
                {{ venta.factura || `#${venta.id}` }}
              </td>
              <td class="px-6 py-4 font-bold text-slate-800 border-b border-slate-100">
                {{ venta.cliente }}
              </td>
              <td class="px-6 py-4 font-medium text-slate-500 border-b border-slate-100">
                {{ formatearFecha(venta.fecha_venta || venta.created_at) }}
              </td>
              <td class="px-6 py-4 font-black text-slate-900 border-b border-slate-100">
                ${{ Number(venta.total).toFixed(2) }}
              </td>
              <td class="px-6 py-4 border-b border-slate-100">
                <div class="flex items-center justify-center">
                  <button
                    @click="verDetalleVenta(venta)"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-[#2B3A4A] hover:text-white transition cursor-pointer"
                    title="Ver Detalle de Venta"
                  >
                    <i class="bi bi-eye-fill text-sm"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="ventas.length === 0">
              <td colspan="5" class="text-center py-12 text-slate-400 font-medium italic border-b border-slate-100">
                <i class="bi bi-receipt text-3xl block mb-2 text-slate-300"></i>
                No hay ventas registradas.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Detalle de Venta -->
    <div
      v-if="mostrarModalDetalle"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
      @click.self="cerrarModal"
    >
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Header Modal -->
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-sky-400">
              <i class="bi bi-receipt-cutoff text-lg"></i>
            </div>
            <div>
              <h3 class="text-base font-bold text-white m-0">
                Detalle de Venta {{ ventaSeleccionada?.factura || `#${ventaSeleccionada?.id}` }}
              </h3>
              <p class="text-[11px] text-slate-400 font-medium m-0">Información completa de la transacción</p>
            </div>
          </div>
          <button
            @click="cerrarModal"
            class="text-slate-400 hover:text-white hover:bg-slate-800 w-8 h-8 rounded-lg flex items-center justify-center transition cursor-pointer border-0 bg-transparent"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Cuerpo Modal -->
        <div class="p-6 space-y-4">
          <!-- Metadatos de la Venta -->
          <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
            <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Cliente</span>
              <span class="text-sm font-bold text-slate-800 block mt-0.5">
                {{ ventaSeleccionada?.cliente || 'Consumidor Final' }}
              </span>
            </div>
            <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Fecha</span>
              <span class="text-sm font-bold text-slate-800 block mt-0.5">
                {{ formatearFecha(ventaSeleccionada?.fecha_venta || ventaSeleccionada?.created_at) }}
              </span>
            </div>
          </div>

          <!-- Tabla con Scroll Controlado -->
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2">
              Productos Comprados
            </h4>
            <div class="border border-slate-200 rounded-xl overflow-y-auto max-h-60">
              <table class="w-full text-left border-collapse text-xs">
                <thead class="sticky top-0 bg-slate-100 border-b border-slate-200 z-10">
                  <tr class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-2.5">Producto</th>
                    <th class="px-4 py-2.5 text-center">Cant.</th>
                    <th class="px-4 py-2.5 text-right">Precio U.</th>
                    <th class="px-4 py-2.5 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="det in ventaSeleccionada?.detalles"
                    :key="det.id"
                    class="hover:bg-slate-50/50"
                  >
                    <td class="px-4 py-2.5 font-bold text-slate-800">
                      {{ det.producto?.nombre || 'Producto Desconocido' }}
                    </td>
                    <td class="px-4 py-2.5 text-center font-bold text-slate-600">
                      {{ det.cantidad }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-medium text-slate-600">
                      ${{ Number(det.precio_unitario).toFixed(2) }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-bold text-slate-900">
                      ${{ Number(det.subtotal).toFixed(2) }}
                    </td>
                  </tr>
                  <tr v-if="!ventaSeleccionada?.detalles || ventaSeleccionada.detalles.length === 0">
                    <td colspan="4" class="text-center py-6 text-slate-400 italic">
                      Sin detalles disponibles.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Total Final -->
          <div class="flex justify-between items-center pt-2 border-t border-slate-100">
            <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total de la Venta</span>
            <span class="text-xl font-black text-slate-900">
              ${{ Number(ventaSeleccionada?.total || 0).toFixed(2) }}
            </span>
          </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
          <button
            @click="cerrarModal"
            class="px-4 py-2 bg-slate-200 hover:bg-slate-300 active:bg-slate-400 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer border-0"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const ventas = ref([])
const mostrarModalDetalle = ref(false)
const ventaSeleccionada = ref(null)

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

const verDetalleVenta = (venta) => {
  ventaSeleccionada.value = venta
  mostrarModalDetalle.value = true
}

const cerrarModal = () => {
  mostrarModalDetalle.value = false
  ventaSeleccionada.value = null
}

onMounted(() => {
  consultarVentas()
})
</script>