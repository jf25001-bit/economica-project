<template>
  <div class="main-interface-container box-border w-full max-w-full overflow-x-hidden p-3 sm:p-4 lg:p-6 bg-[#f8fafc] min-h-screen">

    <div class="w-full box-border bg-white rounded-2xl shadow-sm p-4 border border-gray-100 mb-6">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">

        <div class="flex items-center gap-3 w-full sm:w-auto min-w-0">
          <button
            type="button"
            class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer shrink-0"
            title="Volver a productos"
            @click="volver"
          >
            <i class="bi bi-arrow-left"></i>
          </button>
          <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-bold text-[#1e293b] truncate">Productos anulados</h1>
            <p class="text-xs font-medium text-gray-400 mt-0.5">{{ total }} productos anulados</p>
          </div>
        </div>

        <div class="relative flex items-center w-full sm:max-w-md">
          <i class="bi bi-search absolute left-4 text-gray-400"></i>
          <input
            v-model="buscar"
            type="text"
            placeholder="Buscar producto por nombre o SKU..."
            class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm bg-gray-50/50 box-border"
          />
        </div>

      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
      <div class="overflow-x-auto w-full">
        <table class="w-full min-w-[900px] text-left border-collapse">

          <thead class="bg-[#f8fafc] border-b border-gray-100">
            <tr class="text-[#64748b] text-[11px] font-bold uppercase tracking-wider">
              <th class="px-4 py-3.5 whitespace-nowrap w-28">SKU / CÓDIGO</th>
              <th class="px-4 py-3.5 whitespace-nowrap w-16">IMAGEN</th>
              <th class="px-4 py-3.5 min-w-[140px]">PRODUCTO</th>
              <th class="px-4 py-3.5 min-w-[140px]">CATEGORÍA / SUBCATEGORÍA</th>
              <th class="px-4 py-3.5 whitespace-nowrap w-32">STOCK / UNIDAD</th>
              <th class="px-4 py-3.5 whitespace-nowrap min-w-[120px]">PRECIO VENTA</th>
              <th class="px-4 py-3.5 text-center whitespace-nowrap w-40">ACCIONES</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">

            <tr v-for="producto in paginados" :key="producto.id" class="bg-gray-50/70 text-sm text-gray-500">

              <td class="px-4 py-3.5 font-mono text-xs truncate">
                {{ producto.codigo_barras || 'Sin SKU' }}
              </td>

              <td class="px-4 py-3.5">
                <img
                  v-if="producto.imagenes && producto.imagenes.length"
                  :src="obtenerUrlImagen(producto.imagenes[0].ruta)"
                  alt="Producto"
                  class="w-9 h-9 object-cover rounded-lg border border-gray-200 grayscale opacity-70"
                />
                <div
                  v-else
                  class="w-9 h-9 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-[9px] text-gray-400 bg-gray-50 shrink-0"
                >
                  Sin foto
                </div>
              </td>

              <td class="px-4 py-3.5 font-semibold break-words text-xs sm:text-sm">
                <span class="line-through">{{ producto.nombre }}</span>
                <span class="ml-2 rounded-full bg-gray-800 text-white px-2 py-0.5 text-[10px] font-bold align-middle">Anulado</span>
              </td>

              <td class="px-4 py-3.5 break-words">
                <div class="flex flex-col justify-center">
                  <span class="font-medium text-xs">
                    {{ producto.subcategoria?.categoria?.nombre || 'General' }}
                  </span>
                  <span class="text-[11px] text-gray-400 mt-0.5">
                    {{ producto.subcategoria?.nombre || 'Sin subcategoría' }}
                  </span>
                </div>
              </td>

              <td class="px-4 py-3.5 whitespace-nowrap">
                <span class="font-medium">{{ formatearCantidad(producto.stock) }}</span>
                <span class="text-xs text-gray-400 ml-1">({{ producto.unidad_medida?.nombre || 'pza' }})</span>
              </td>

              <td class="px-4 py-3.5 font-bold whitespace-nowrap">
                {{ dinero(producto.precio_venta) }}
              </td>

              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <div class="flex gap-1.5 justify-center items-center">
                  <button
                    class="p-1.5 text-gray-600 hover:text-blue-600 rounded-lg border border-gray-200 hover:bg-white shadow-xs transition cursor-pointer"
                    title="Ver detalle"
                    @click="abrirDetalle(producto)"
                  >
                    <i class="bi bi-eye"></i>
                  </button>
                  <button
                    class="px-2.5 py-1.5 text-xs font-semibold text-emerald-700 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="reactivando"
                    @click="reactivar(producto)"
                  >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reactivar
                  </button>
                </div>
              </td>

            </tr>

            <tr v-if="filtrados.length === 0">
              <td colspan="7" class="text-center py-8 text-gray-400 italic text-xs">
                <span v-if="cargando">Cargando productos...</span>
                <span v-else>No hay productos anulados.</span>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <BarraPaginacion
        v-if="total > 0"
        v-model:pagina-actual="paginaActual"
        v-model:por-pagina="porPagina"
        :total="total"
      />
    </div>

    <div
      v-if="seleccionado"
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto"
      @click.self="cerrarDetalle"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 flex flex-col my-auto box-border">

        <div class="h-14 px-5 flex justify-between items-center bg-[#1a233a] shrink-0">
          <h3 class="font-bold text-white text-sm sm:text-base truncate">Producto anulado</h3>
          <button
            type="button"
            class="w-7 h-7 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition cursor-pointer"
            @click="cerrarDetalle"
          >
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>

        <div class="p-5 flex flex-col gap-3.5 max-h-[75vh] overflow-y-auto text-xs w-full box-border">

          <div class="flex items-center gap-3">
            <img
              v-if="seleccionado.imagenes && seleccionado.imagenes.length"
              :src="obtenerUrlImagen(seleccionado.imagenes[0].ruta)"
              alt="Producto"
              class="w-14 h-14 object-cover rounded-xl border border-gray-200 grayscale opacity-70"
            />
            <div
              v-else
              class="w-14 h-14 border border-dashed border-gray-300 rounded-xl flex items-center justify-center text-[10px] text-gray-400 bg-gray-50 shrink-0"
            >
              Sin foto
            </div>
            <span class="rounded-full bg-gray-800 text-white px-2.5 py-1 text-[11px] font-bold">Anulado</span>
          </div>

          <div v-for="campo in camposDetalle" :key="campo.etiqueta" class="w-full min-w-0">
            <label class="block font-semibold text-gray-700 mb-1">{{ campo.etiqueta }}</label>
            <input
              :value="campo.valor"
              type="text"
              readonly
              tabindex="-1"
              class="w-full h-9 px-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-600 cursor-not-allowed outline-none box-border"
            />
          </div>

        </div>

        <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-100 shrink-0">
          <button
            type="button"
            class="h-9 px-4 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-medium transition cursor-pointer"
            @click="cerrarDetalle"
          >
            Cerrar
          </button>
          <button
            type="button"
            class="h-9 px-4 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium flex items-center gap-2 transition disabled:opacity-50 cursor-pointer"
            :disabled="reactivando"
            @click="reactivar(seleccionado)"
          >
            <i class="bi bi-arrow-counterclockwise"></i>
            Reactivar producto
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getProductos, reactivarProductoAPI } from '@/services/productoService'
import BarraPaginacion from '@/components/BarraPaginacion.vue'
import { usePaginacion } from '@/utils/usePaginacion'
import { alertaError, alertaExito, confirmar, mensajesDeError } from '@/utils/alertas'

const router = useRouter()

const usuarioActual = computed(() => {
  try {
    const usuario = localStorage.getItem('user')
    return usuario ? JSON.parse(usuario) : null
  } catch (error) {
    console.error('Error leyendo usuario:', error)
    return null
  }
})

const esCajero = computed(() => (usuarioActual.value?.rol?.nombre || '') === 'Cajero')

const productos = ref([])
const buscar = ref('')
const cargando = ref(false)
const reactivando = ref(false)
const seleccionado = ref(null)

const filtrados = computed(() => {
  const query = buscar.value.toLowerCase().trim()

  return productos.value
    .filter(p => p.anulado)
    .filter(p => {
      if (!query) return true
      return (
        (p.nombre || '').toLowerCase().includes(query) ||
        (p.codigo_barras || '').toLowerCase().includes(query)
      )
    })
    .sort((a, b) => Number(b.id) - Number(a.id))
})

const { paginaActual, porPagina, total, paginados } = usePaginacion(filtrados, {
  reiniciarCon: [buscar]
})

const formatearCantidad = (valor) => {
  const n = Number(valor)
  if (!Number.isFinite(n)) return '0'
  return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.?0+$/, '')
}

const dinero = (valor) => (valor === null || valor === undefined ? '—' : `$${Number(valor).toFixed(2)}`)

const obtenerUrlImagen = (ruta) => {
  if (!ruta) return ''
  const rutaLimpia = ruta.startsWith('/') ? ruta.substring(1) : ruta
  return `http://127.0.0.1:8000/storage/${rutaLimpia}`
}

const camposDetalle = computed(() => {
  const p = seleccionado.value
  if (!p) return []

  const proveedores = (p.proveedores || [])
    .map(prov => prov.nombre_proveedor || prov.nombre)
    .filter(Boolean)
    .join(', ')

  return [
    { etiqueta: 'Nombre', valor: p.nombre },
    { etiqueta: 'Código de barras / SKU', valor: p.codigo_barras || 'Sin SKU' },
    { etiqueta: 'Categoría', valor: p.subcategoria?.categoria?.nombre || 'General' },
    { etiqueta: 'Subcategoría', valor: p.subcategoria?.nombre || 'Sin subcategoría' },
    { etiqueta: 'Unidad de medida', valor: p.unidad_medida?.nombre || 'Sin unidad' },
    { etiqueta: 'Precio de compra', valor: dinero(p.precio_compra) },
    { etiqueta: 'Precio de venta', valor: dinero(p.precio_venta) },
    {
      etiqueta: 'Modo de precio',
      valor: p.precio_automatico ? `Automático (${Number(p.margen_porcentaje)}%)` : 'Manual'
    },
    { etiqueta: 'Stock', valor: formatearCantidad(p.stock) },
    { etiqueta: 'Stock mínimo', valor: String(p.stock_minimo ?? 0) },
    { etiqueta: 'Proveedores', valor: proveedores || 'Sin proveedores' }
  ]
})

const cargarProductos = async () => {
  cargando.value = true

  try {
    const res = await getProductos()
    const data = res.data?.data || res.data || []
    productos.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Error cargando productos anulados:', error)
    alertaError('No se pudieron cargar los productos', mensajesDeError(error, 'Intenta de nuevo en unos segundos.'))
  } finally {
    cargando.value = false
  }
}

const abrirDetalle = (producto) => {
  seleccionado.value = producto
}

const cerrarDetalle = () => {
  seleccionado.value = null
}

const reactivar = async (producto) => {
  if (esCajero.value || reactivando.value) return

  const acepto = await confirmar({
    titulo: '¿Reactivar producto?',
    texto: `"${producto.nombre}" volverá a estar disponible en ventas, compras y productos.`,
    confirmarTexto: 'Sí, reactivar'
  })

  if (!acepto) return

  reactivando.value = true

  try {
    await reactivarProductoAPI(producto.id)
    cerrarDetalle()
    await cargarProductos()
    alertaExito('Producto reactivado')
  } catch (error) {
    console.error('Error al reactivar:', error)
    alertaError(
      'No se pudo reactivar el producto',
      mensajesDeError(error, 'Ocurrió un error al procesar la solicitud.')
    )
  } finally {
    reactivando.value = false
  }
}

const volver = () => router.push('/productos')

onMounted(() => {
  if (esCajero.value) {
    router.replace('/productos')
    return
  }

  cargarProductos()
})
</script>

<style scoped>
.main-interface-container {
  font-family: 'Inter', sans-serif;
  width: 100%;
}
</style>