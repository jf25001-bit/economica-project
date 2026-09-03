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
                    @click="abrirDetalleLotes(item)"
                  >
                    <i class="bi bi-eye"></i>
                    Ver lotes
                  </button>
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

    <!-- Modal de Detalle de Lotes -->
    <div
      v-if="productoSeleccionado"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
      @click.self="cerrarDetalleLotes"
    >
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-3xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-sky-400 shrink-0">
              <i class="bi bi-box-seam text-lg"></i>
            </div>
            <div class="min-w-0">
              <h3 class="text-base font-bold text-white m-0 truncate">
                Detalle de lotes
              </h3>
              <p class="text-[11px] text-slate-400 font-medium m-0 truncate">
                {{ productoSeleccionado.nombre }} - {{ productoSeleccionado.codigo }}
              </p>
            </div>
          </div>
          <button
            @click="cerrarDetalleLotes"
            class="text-slate-400 hover:text-white hover:bg-slate-800 w-8 h-8 rounded-lg flex items-center justify-center transition cursor-pointer border-0 bg-transparent shrink-0"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
            <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Producto</span>
              <span class="text-sm font-bold text-slate-800 block mt-0.5 truncate">
                {{ productoSeleccionado.nombre }}
              </span>
            </div>
            <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Categoría</span>
              <span class="text-sm font-bold text-slate-800 block mt-0.5 truncate">
                {{ productoSeleccionado.categoria }}
              </span>
            </div>
            <div>
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Stock total</span>
              <span class="text-sm font-black text-slate-900 block mt-0.5">
                {{ productoSeleccionado.stock }}
              </span>
            </div>
          </div>

          <div>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-2">
              <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 m-0">
                  Lotes en existencia
                </h4>
                <p class="text-[11px] text-slate-500 font-medium m-0 mt-1">
                  Salida FIFO: primero se descuenta el lote con vencimiento más cercano.
                </p>
              </div>
              <span class="text-xs sm:text-sm font-bold text-[#5B80B0] shrink-0">
                Total: {{ productoSeleccionado.stock }}
              </span>
            </div>

            <div class="border border-slate-200 rounded-xl overflow-auto max-h-72">
              <table class="w-full min-w-[560px] text-left border-collapse text-xs">
                <thead class="sticky top-0 bg-slate-100 border-b border-slate-200 z-10">
                  <tr class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-2.5 whitespace-nowrap">Orden FIFO</th>
                    <th class="px-4 py-2.5 whitespace-nowrap">Lote</th>
                    <th class="px-4 py-2.5 whitespace-nowrap text-center">Cantidad Inicial</th>
                    <th class="px-4 py-2.5 whitespace-nowrap text-center">Cantidad Actual</th>
                    <th class="px-4 py-2.5 whitespace-nowrap text-right">Vencimiento</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="(lote, index) in productoSeleccionado.lotes"
                    :key="lote.id"
                    class="text-slate-700 hover:bg-slate-50/70"
                  >
                    <td class="px-4 py-2.5 whitespace-nowrap">
                      <span class="rounded-full bg-[#5B80B0]/10 px-2 py-0.5 text-xs font-semibold text-[#5B80B0]">
                        #{{ index + 1 }}
                      </span>
                    </td>
                    <td class="px-4 py-2.5 font-mono text-xs font-bold whitespace-nowrap text-slate-800">{{ lote.codigo_lote || 'Sin lote' }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-center font-medium text-slate-600">{{ lote.cantidad_inicial }}</td>
                    <td class="px-4 py-2.5 font-black whitespace-nowrap text-center text-slate-900">{{ lote.cantidad_actual }}</td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-right text-slate-500">{{ formatearFecha(lote.fecha_expiracion) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
          <button
            @click="cerrarDetalleLotes"
            class="px-4 py-2 bg-slate-200 hover:bg-slate-300 active:bg-slate-400 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer border-0"
          >
            Cerrar
          </button>
        </div>
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
const productoSeleccionado = ref(null)

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

const abrirDetalleLotes = (item) => {
  productoSeleccionado.value = item
}

const cerrarDetalleLotes = () => {
  productoSeleccionado.value = null
}

onMounted(() => {
  cargarInventario()
})
</script>
