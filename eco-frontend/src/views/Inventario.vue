<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
      </div>

      <button
        @click="cargarInventario"
        :disabled="cargando"
        class="bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-5 py-3 rounded-xl shadow-md transition font-medium disabled:opacity-60"
      >
        <i class="bi bi-arrow-repeat mr-2" :class="{ 'animate-spin': cargando }"></i>
        {{ cargando ? 'Actualizando...' : 'Actualizar Stock' }}
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
      <div class="relative">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar en inventario..."
          class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5B80B0]"
        />
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[940px]">
          <thead class="bg-gray-100">
            <tr class="text-left text-gray-700">
              <th class="px-6 py-4 font-semibold">Codigo</th>
              <th class="px-6 py-4 font-semibold">Producto</th>
              <th class="px-6 py-4 font-semibold">Categoria</th>
              <th class="px-6 py-4 font-semibold">Stock Total</th>
              <th class="px-6 py-4 font-semibold">Lotes</th>
              <th class="px-6 py-4 font-semibold">Stock Minimo</th>
              <th class="px-6 py-4 font-semibold">Estado</th>
              <th class="px-6 py-4 font-semibold text-right">Detalle</th>
            </tr>
          </thead>

          <tbody>
            <template
              v-for="item in inventarioFiltrado"
              :key="item.id"
            >
              <tr class="border-t hover:bg-gray-50">
                <td class="px-6 py-4">{{ item.codigo }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">
                  {{ item.nombre }}
                </td>
                <td class="px-6 py-4">{{ item.categoria }}</td>
                <td class="px-6 py-4 font-semibold">{{ item.stock }}</td>
                <td class="px-6 py-4">
                  <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">
                    {{ item.lotes.length }}
                  </span>
                </td>
                <td class="px-6 py-4">{{ item.minimo }}</td>

                <td class="px-6 py-4">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-sm font-medium',
                      item.stock > item.minimo
                        ? 'bg-[#5B80B0]/10 text-[#5B80B0]'
                        : 'bg-amber-100 text-amber-700'
                    ]"
                  >
                    {{ item.stock > item.minimo ? 'Disponible' : 'Stock Bajo' }}
                  </span>
                </td>

                <td class="px-6 py-4 text-right">
                  <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="item.lotes.length === 0"
                    @click="alternarDetalle(item.id)"
                  >
                    <i :class="detalleAbierto === item.id ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                    Ver lotes
                  </button>
                </td>
              </tr>

              <tr
                v-if="detalleAbierto === item.id"
                class="bg-gray-50"
              >
                <td colspan="8" class="px-6 py-4">
                  <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                      <div>
                        <p class="text-sm font-semibold text-gray-700">Detalle de lotes en existencia</p>
                        <p class="text-xs text-gray-500">Salida FIFO: primero se descuenta el lote con vencimiento mas cercano.</p>
                      </div>
                      <span class="text-sm font-semibold text-[#5B80B0]">
                        Total: {{ item.stock }}
                      </span>
                    </div>

                    <table class="w-full">
                      <thead class="bg-gray-50">
                        <tr class="text-left text-xs uppercase text-gray-500">
                          <th class="px-4 py-3 font-semibold">Orden FIFO</th>
                          <th class="px-4 py-3 font-semibold">Lote</th>
                          <th class="px-4 py-3 font-semibold">Cantidad Inicial</th>
                          <th class="px-4 py-3 font-semibold">Cantidad Actual</th>
                          <th class="px-4 py-3 font-semibold">Vencimiento</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(lote, index) in item.lotes"
                          :key="lote.id"
                          class="border-t border-gray-100 text-sm text-gray-700"
                        >
                          <td class="px-4 py-3">
                            <span class="rounded-full bg-[#5B80B0]/10 px-2.5 py-1 text-xs font-semibold text-[#5B80B0]">
                              {{ index + 1 }}
                            </span>
                          </td>
                          <td class="px-4 py-3 font-medium">{{ lote.codigo_lote || 'Sin lote' }}</td>
                          <td class="px-4 py-3">{{ lote.cantidad_inicial }}</td>
                          <td class="px-4 py-3 font-semibold">{{ lote.cantidad_actual }}</td>
                          <td class="px-4 py-3">{{ formatearFecha(lote.fecha_expiracion) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
            </template>

            <tr v-if="cargando">
              <td colspan="8" class="text-center py-10 text-gray-400">
                Cargando inventario...
              </td>
            </tr>

            <tr v-else-if="inventarioFiltrado.length === 0">
              <td colspan="8" class="text-center py-10 text-gray-400 italic">
                No hay productos en inventario.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

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
