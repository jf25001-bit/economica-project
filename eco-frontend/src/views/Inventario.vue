<template>
  <div class="p-4 sm:p-6 w-full max-w-full overflow-x-hidden box-border">
    
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Inventario</h1>
      </div>

      <button
        @click="cargarInventario"
        :disabled="cargando"
        class="bg-[#2D3748] hover:bg-[#1A202C] text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-2xl shadow-md transition font-medium disabled:opacity-60 flex items-center justify-center cursor-pointer shrink-0"
      >
        <i class="bi bi-arrow-repeat mr-2 text-lg" :class="{ 'animate-spin': cargando }"></i>
        <span>{{ cargando ? 'Actualizando...' : 'Actualizar Stock' }}</span>
      </button>
    </div>

    <!-- Buscador -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-6 w-full box-border">
      <div class="relative w-full">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar en inventario..."
          class="w-full max-w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5B80B0] box-border"
        />
      </div>
    </div>

    <!-- Tabla principal -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-full border border-gray-100">
      <div class="overflow-x-auto w-full">
        <table class="w-full min-w-[700px] table-fixed">
          <thead class="bg-gray-100">
            <tr class="text-left text-gray-700 text-xs sm:text-sm">
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-28 whitespace-nowrap">Código</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold min-w-[150px]">Producto</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-32">Categoría</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-28 text-center whitespace-nowrap">Stock Total</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-24 text-center whitespace-nowrap">Lotes</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-28 text-center whitespace-nowrap">Stock Mínimo</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-32 text-center whitespace-nowrap">Estado</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 font-semibold w-28 text-right whitespace-nowrap">Detalle</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            <template
              v-for="item in inventarioFiltrado"
              :key="item.id"
            >
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-4 py-3 sm:px-6 sm:py-4 font-mono text-xs sm:text-sm text-gray-600 truncate">{{ item.codigo }}</td>
                <td class="px-4 py-3 sm:px-6 sm:py-4 font-medium text-gray-800 break-words text-sm">{{ item.nombre }}</td>
                <td class="px-4 py-3 sm:px-6 sm:py-4 text-gray-600 break-words text-sm">{{ item.categoria }}</td>
                <td class="px-4 py-3 sm:px-6 sm:py-4 font-semibold text-center whitespace-nowrap">{{ item.stock }}</td>
                <td class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">
                  <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                    {{ item.lotes.length }}
                  </span>
                </td>
                <td class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap text-sm">{{ item.minimo }}</td>

                <td class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-xs font-medium inline-block',
                      item.stock > item.minimo
                        ? 'bg-[#5B80B0]/10 text-[#5B80B0]'
                        : 'bg-amber-100 text-amber-700'
                    ]"
                  >
                    {{ item.stock > item.minimo ? 'Disponible' : 'Stock Bajo' }}
                  </span>
                </td>

                <td class="px-4 py-3 sm:px-6 sm:py-4 text-right whitespace-nowrap">
                  <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-100 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="item.lotes.length === 0"
                    @click="alternarDetalle(item.id)"
                  >
                    <i :class="detalleAbierto === item.id ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                    Ver lotes
                  </button>
                </td>
              </tr>

              <!-- Subtabla Lotes -->
              <tr
                v-if="detalleAbierto === item.id"
                class="bg-gray-50/60"
              >
                <td colspan="8" class="p-3 sm:p-4">
                  <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 px-4 py-3 bg-gray-50/50">
                      <div>
                        <p class="text-xs sm:text-sm font-semibold text-gray-700">Detalle de lotes en existencia</p>
                        <p class="text-[11px] sm:text-xs text-gray-500">Salida FIFO: primero se descuenta el lote con vencimiento más cercano.</p>
                      </div>
                      <span class="text-xs sm:text-sm font-semibold text-[#5B80B0] shrink-0">
                        Total: {{ item.stock }}
                      </span>
                    </div>

                    <div class="overflow-x-auto w-full">
                      <table class="w-full min-w-[500px]">
                        <thead class="bg-gray-50">
                          <tr class="text-left text-[11px] sm:text-xs uppercase text-gray-500">
                            <th class="px-4 py-2.5 font-semibold whitespace-nowrap w-24">Orden FIFO</th>
                            <th class="px-4 py-2.5 font-semibold whitespace-nowrap">Lote</th>
                            <th class="px-4 py-2.5 font-semibold whitespace-nowrap text-center">Cantidad Inicial</th>
                            <th class="px-4 py-2.5 font-semibold whitespace-nowrap text-center">Cantidad Actual</th>
                            <th class="px-4 py-2.5 font-semibold whitespace-nowrap text-right">Vencimiento</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                          <tr
                            v-for="(lote, index) in item.lotes"
                            :key="lote.id"
                            class="text-xs sm:text-sm text-gray-700 hover:bg-gray-50"
                          >
                            <td class="px-4 py-2.5 whitespace-nowrap">
                              <span class="rounded-full bg-[#5B80B0]/10 px-2 py-0.5 text-xs font-semibold text-[#5B80B0]">
                                #{{ index + 1 }}
                              </span>
                            </td>
                            <td class="px-4 py-2.5 font-mono text-xs font-medium whitespace-nowrap">{{ lote.codigo_lote || 'Sin lote' }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center">{{ lote.cantidad_inicial }}</td>
                            <td class="px-4 py-2.5 font-semibold whitespace-nowrap text-center">{{ lote.cantidad_actual }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right text-gray-500">{{ formatearFecha(lote.fecha_expiracion) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </td>
              </tr>
            </template>

            <!-- Estados -->
            <tr v-if="cargando">
              <td colspan="8" class="text-center py-10 text-gray-400 text-sm">
                Cargando inventario...
              </td>
            </tr>

            <tr v-else-if="inventarioFiltrado.length === 0">
              <td colspan="8" class="text-center py-10 text-gray-400 italic text-sm">
                No hay productos en inventario.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Error -->
    <div v-if="error" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getProductos } from '@/services/productoService'
import api from '@/services/api'

const productos = ref([])
const lotes = ref([])
const busqueda = ref('')
const cargando = ref(false)
const error = ref('')
const detalleAbierto = ref(null)

const lotesPorProducto = computed(() => {
  return lotes.value.reduce((grupos, lote) => {
    const productoId = lote.producto_id
    const cantidadActual = Number(lote.cantidad_actual) || 0

    if (!productoId || cantidadActual <= 0) return grupos
    if (!grupos[productoId]) grupos[productoId] = []

    grupos[productoId].push({
      ...lote,
      cantidad_inicial: Number(lote.cantidad_inicial) || 0,
      cantidad_actual: cantidadActual
    })

    return grupos
  }, {})
})

const inventario = computed(() => {
  return productos.value.map(producto => {
    const lotesProducto = ordenarLotesFifo(lotesPorProducto.value[producto.id] || [])
    const stockLotes = lotesProducto.reduce((total, lote) => total + lote.cantidad_actual, 0)

    return {
      id: producto.id,
      codigo: producto.codigo_barras || 'Sin codigo',
      nombre: producto.nombre || 'Sin nombre',
      categoria: producto.subcategoria?.categoria?.nombre || 'General',
      stock: lotesProducto.length > 0 ? stockLotes : Number(producto.stock) || 0,
      minimo: Number(producto.stock_minimo) || 0,
      lotes: lotesProducto
    }
  })
})

const inventarioFiltrado = computed(() => {
  const query = busqueda.value.toLowerCase().trim()
  if (!query) return inventario.value

  return inventario.value.filter(item =>
    item.codigo.toLowerCase().includes(query) ||
    item.nombre.toLowerCase().includes(query) ||
    item.categoria.toLowerCase().includes(query) ||
    item.lotes.some(lote => (lote.codigo_lote || '').toLowerCase().includes(query))
  )
})

const cargarInventario = async () => {
  cargando.value = true
  error.value = ''

  try {
    const [resProductos, resLotes] = await Promise.all([
      getProductos(),
      api.get('/lotes')
    ])

    const dataProductos = resProductos.data?.data || resProductos.data || []
    const dataLotes = resLotes.data?.data || resLotes.data || []

    productos.value = Array.isArray(dataProductos) ? dataProductos : []
    lotes.value = Array.isArray(dataLotes) ? dataLotes : []
  } catch (err) {
    console.error('Error cargando inventario:', err)
    productos.value = []
    lotes.value = []
    error.value = 'No se pudo cargar el inventario desde la base de datos.'
  } finally {
    cargando.value = false
  }
}

const ordenarLotesFifo = (lista) => {
  return [...lista].sort((a, b) => {
    const fechaA = a.fecha_expiracion ? new Date(a.fecha_expiracion).getTime() : Number.MAX_SAFE_INTEGER
    const fechaB = b.fecha_expiracion ? new Date(b.fecha_expiracion).getTime() : Number.MAX_SAFE_INTEGER

    if (fechaA !== fechaB) return fechaA - fechaB
    return Number(a.id) - Number(b.id)
  })
}

const formatearFecha = (fecha) => {
  if (!fecha) return 'Sin vencimiento'

  const fechaObj = new Date(`${fecha}T00:00:00`)
  return isNaN(fechaObj.getTime())
    ? fecha
    : fechaObj.toLocaleDateString()
}

const alternarDetalle = (id) => {
  detalleAbierto.value = detalleAbierto.value === id ? null : id
}

onMounted(() => {
  cargarInventario()
})
</script>