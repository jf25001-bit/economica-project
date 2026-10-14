<template>
  <div class="p-3 sm:p-6 w-full max-w-full overflow-x-hidden box-border">

    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Inventario</h1>
      <p class="mt-1 text-xs text-gray-400">
        Se actualiza automáticamente<span v-if="horaActualizacion"> · Última actualización {{ horaActualizacion }}</span>
      </p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-3 sm:p-4 mb-6 w-full box-border flex flex-col lg:flex-row gap-3 lg:items-center">
      <div class="relative w-full lg:flex-1 min-w-0">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar en inventario..."
          class="w-full max-w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5B80B0] box-border text-sm"
        />
      </div>

      <div class="relative w-full lg:w-64 shrink-0">
        <i class="bi bi-funnel absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
        <select
          v-model="filtroVencimiento"
          class="w-full appearance-none pl-11 pr-10 py-3 border border-gray-300 rounded-xl bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#5B80B0] cursor-pointer box-border"
        >
          <option v-for="f in filtrosVencimiento" :key="f.value" :value="f.value">{{ f.label }}</option>
        </select>
        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
      </div>
    </div>

    <div
      v-if="filtroVencimiento === '' && avisoVencidos.lotes > 0"
      class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-sm text-gray-800"
    >
      <span class="flex items-center gap-2 font-medium min-w-0">
        <i class="bi bi-x-octagon-fill shrink-0"></i>
        <span class="min-w-0 break-words">{{ textoAvisoVencidos }}</span>
      </span>
      <button
        type="button"
        class="text-xs font-semibold underline cursor-pointer bg-transparent border-0 text-gray-700 hover:text-gray-900 self-start sm:self-auto shrink-0"
        @click="filtroVencimiento = 'vencidos'"
      >
        Ver vencidos
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-full border border-gray-100">

      <template v-if="filas.length">
        <div class="hidden xl:block overflow-x-auto w-full">
          <table class="w-full min-w-[1000px]">
            <thead class="bg-gray-100">
              <tr class="text-left text-gray-700 text-sm">
                <th class="px-4 py-4 font-semibold whitespace-nowrap">Código</th>
                <th class="px-4 py-4 font-semibold">Producto</th>
                <th class="px-4 py-4 font-semibold">Categoría</th>
                <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Stock Disponible</th>
                <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Lotes</th>
                <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Stock Mínimo</th>
                <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Estado</th>
                <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Vencimiento</th>
                <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Detalle</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
              <tr
                v-for="item in filas"
                :key="item.id"
                :class="['transition-colors', item.anulado ? 'bg-gray-200/70 text-gray-500' : 'hover:bg-gray-50/80']"
              >
                <td class="px-4 py-3 font-mono text-sm whitespace-nowrap" :class="item.anulado ? 'text-gray-500' : 'text-gray-600'">{{ item.codigo }}</td>
                <td class="px-4 py-3 font-medium break-words text-sm" :class="item.anulado ? 'text-gray-600 line-through' : 'text-gray-800'">
                  {{ item.nombre }}
                  <span v-if="item.anulado" class="ml-2 rounded-full bg-gray-800 text-white px-2 py-0.5 text-[10px] font-bold align-middle no-underline inline-block">Anulado</span>
                </td>
                <td class="px-4 py-3 break-words text-sm" :class="item.anulado ? 'text-gray-500' : 'text-gray-600'">{{ item.categoria }}</td>

                <td class="px-4 py-3 font-semibold text-center whitespace-nowrap">
                  <span>{{ item.stock }}</span>
                  <span
                    v-if="filtroVencimiento === 'vencidos'"
                    class="block text-[11px] font-medium text-gray-500"
                  >
                    {{ item.cantidadVencida }} vencidas
                  </span>
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap">
                  <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                    {{ filtroVencimiento === 'vencidos' ? item.vencidos : item.lotesVigentes.length }}
                  </span>
                </td>

                <td class="px-4 py-3 text-center whitespace-nowrap text-sm">{{ item.minimo }}</td>

                <td class="px-4 py-3 text-center whitespace-nowrap">
                  <span
                    v-if="item.anulado"
                    class="px-2.5 py-1 rounded-full text-xs font-medium inline-block bg-gray-300 text-gray-700"
                  >
                    Anulado
                  </span>
                  <span
                    v-else
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

                <td class="px-4 py-3 text-center">
                  <span v-if="item.badge" :class="[CLASE_BADGE, item.badge.clase]">
                    <i :class="['bi', item.badge.icon]"></i>
                    {{ item.badge.texto }}
                  </span>
                  <span v-else class="text-xs text-gray-400">
                    {{ item.lotesVigentes.length ? 'Vigente' : '—' }}
                  </span>
                </td>

                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                    :class="item.anulado ? 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' : 'border-gray-200 text-gray-600 hover:bg-gray-100'"
                    :disabled="item.lotes.length === 0"
                    @click="verLotes(item)"
                  >
                    <i class="bi bi-eye"></i>
                    Ver lotes
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="xl:hidden grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 sm:p-4">
          <div
            v-for="item in filas"
            :key="item.id"
            :class="['min-w-0 rounded-xl border p-4 shadow-sm flex flex-col gap-3', item.anulado ? 'bg-gray-200/70 border-gray-300' : 'border-gray-100 bg-white']"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <p class="font-semibold text-sm break-words m-0" :class="item.anulado ? 'text-gray-700 line-through' : 'text-gray-800'">
                  {{ item.nombre }}
                  <span v-if="item.anulado" class="ml-1 rounded-full bg-gray-800 text-white px-2 py-0.5 text-[10px] font-bold align-middle no-underline inline-block">Anulado</span>
                </p>
                <p class="font-mono text-[11px] truncate m-0 mt-0.5" :class="item.anulado ? 'text-gray-500' : 'text-gray-400'">{{ item.codigo }}</p>
                <p class="text-xs truncate m-0 mt-0.5" :class="item.anulado ? 'text-gray-500' : 'text-gray-500'">{{ item.categoria }}</p>
              </div>
              <span
                v-if="item.anulado"
                class="px-2.5 py-1 rounded-full text-[11px] font-medium shrink-0 bg-gray-300 text-gray-700"
              >
                Anulado
              </span>
              <span
                v-else
                :class="[
                  'px-2.5 py-1 rounded-full text-[11px] font-medium shrink-0',
                  item.stock > item.minimo
                    ? 'bg-[#5B80B0]/10 text-[#5B80B0]'
                    : 'bg-amber-100 text-amber-700'
                ]"
              >
                {{ item.stock > item.minimo ? 'Disponible' : 'Stock Bajo' }}
              </span>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center rounded-lg p-2" :class="item.anulado ? 'bg-gray-300/60' : 'bg-gray-50'">
              <div class="min-w-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Stock</span>
                <span class="block text-sm font-bold text-gray-800">{{ item.stock }}</span>
                <span
                  v-if="filtroVencimiento === 'vencidos'"
                  class="block text-[10px] font-medium text-gray-500"
                >
                  {{ item.cantidadVencida }} vencidas
                </span>
              </div>
              <div class="min-w-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Lotes</span>
                <span class="block text-sm font-bold text-gray-800">
                  {{ filtroVencimiento === 'vencidos' ? item.vencidos : item.lotesVigentes.length }}
                </span>
              </div>
              <div class="min-w-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Mínimo</span>
                <span class="block text-sm font-bold text-gray-800">{{ item.minimo }}</span>
              </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
              <span v-if="item.badge" :class="[CLASE_BADGE, item.badge.clase]">
                <i :class="['bi', item.badge.icon]"></i>
                {{ item.badge.texto }}
              </span>
              <span v-else class="text-xs text-gray-500">
                {{ item.lotesVigentes.length ? 'Vigente' : '—' }}
              </span>

              <button
                type="button"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                :class="item.anulado ? 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' : 'border-gray-200 text-gray-600 hover:bg-gray-100'"
                :disabled="item.lotes.length === 0"
                @click="verLotes(item)"
              >
                <i class="bi bi-eye"></i>
                Ver lotes
              </button>
            </div>
          </div>
        </div>
      </template>

      <div v-else class="py-10 px-4 text-center text-sm text-gray-400">
        <span v-if="cargando">Cargando inventario...</span>
        <span v-else class="italic">No hay productos que coincidan.</span>
      </div>

      <BarraPaginacion
        v-if="total > 0"
        v-model:pagina-actual="paginaActual"
        v-model:por-pagina="porPagina"
        :total="total"
      />
    </div>

    <div v-if="error" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import { getProductos } from '@/services/productoService'
import api from '@/services/api'
import BarraPaginacion from '@/components/BarraPaginacion.vue'
import { usePaginacion } from '@/utils/usePaginacion'
import {
  DIAS_ALERTA,
  ESTADOS_LOTE,
  FILTROS_VENCIMIENTO,
  construirInventario
} from '@/utils/lotes'

const INTERVALO_REFRESCO = 30000
const CLASE_BADGE = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border'

const router = useRouter()

const productos = ref([])
const lotes = ref([])
const busqueda = ref('')
const cargando = ref(false)
const error = ref('')
const filtroVencimiento = ref('')
const horaActualizacion = ref('')

let temporizador = null

const inventario = computed(() => construirInventario(productos.value, lotes.value))

const esAnulado = (item) => {
  return Boolean(
    item.anulado ||
    item.estado === 'anulado' ||
    item.deleted_at ||
    item.is_active === false ||
    item.is_active === 0
  )
}

const filtrosVencimiento = computed(() => {
  const porVencer = inventario.value.filter(i => !esAnulado(i) && i.porVencer > 0).length
  const vencidos = inventario.value.filter(i => !esAnulado(i) && i.vencidos > 0).length

  return [
    { value: '', label: 'Todos' },
    { value: 'anulados', label: 'Anulados' },
    ...FILTROS_VENCIMIENTO.slice(1).map(f => {
      if (f.value === 'por-vencer') return { ...f, label: `${f.label} (${porVencer})` }
      if (f.value === 'vencidos') return { ...f, label: `${f.label} (${vencidos})` }
      return f
    })
  ]
})

const avisoVencidos = computed(() => ({
  lotes: inventario.value.reduce((total, i) => total + (esAnulado(i) ? 0 : i.vencidosRecientes), 0),
  productos: inventario.value.filter(i => !esAnulado(i) && i.vencidosRecientes > 0).length
}))

const textoAvisoVencidos = computed(() => {
  const { lotes: l, productos: p } = avisoVencidos.value
  return `${l} ${l === 1 ? 'lote' : 'lotes'} de ${p} ${p === 1 ? 'producto' : 'productos'} ${l === 1 ? 'ha' : 'han'} vencido`
})

const inventarioFiltrado = computed(() => {
  const query = busqueda.value.toLowerCase().trim()

  const lista = inventario.value.filter(item => {
    const anulado = esAnulado(item)

    if (filtroVencimiento.value === 'anulados') {
      if (!anulado) return false
    } else {
      if (anulado) return false
      if (filtroVencimiento.value === 'por-vencer' && item.porVencer === 0) return false
      if (filtroVencimiento.value === 'vencidos' && item.vencidos === 0) return false
    }

    if (!query) return true

    return (
      item.codigo.toLowerCase().includes(query) ||
      item.nombre.toLowerCase().includes(query) ||
      item.categoria.toLowerCase().includes(query) ||
      item.lotes.some(lote =>
        (lote.codigo_lote || '').toLowerCase().includes(query) ||
        (lote.producto_nombre || '').toLowerCase().includes(query) ||
        (lote.producto_codigo_barras || '').toLowerCase().includes(query)
      )
    )
  })

  if (filtroVencimiento.value === 'vencidos') {
    lista.sort((a, b) => b.diasUltimoVencido - a.diasUltimoVencido || a.nombre.localeCompare(b.nombre))
  }

  return lista
})

const { paginaActual, porPagina, total, paginados } = usePaginacion(inventarioFiltrado, {
  reiniciarCon: [busqueda, filtroVencimiento]
})

const badgeVencimiento = (item) => {
  if (filtroVencimiento.value === 'vencidos') {
    return {
      clase: ESTADOS_LOTE.vencido.clase,
      icon: ESTADOS_LOTE.vencido.icon,
      texto: `${item.vencidos} ${item.vencidos === 1 ? 'lote vencido' : 'lotes vencidos'}`
    }
  }

  if (item.porVencer > 0) {
    const estado = ESTADOS_LOTE[item.severidad]
    return {
      clase: estado.clase,
      icon: estado.icon,
      texto: `${item.porVencer} ${item.porVencer === 1 ? 'lote por vencer' : 'lotes por vencer'}`
    }
  }

  return null
}

const filas = computed(() => paginados.value.map(item => ({
  ...item,
  anulado: esAnulado(item),
  badge: badgeVencimiento(item)
})))

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  showCloseButton: true,
  timer: 7000,
  timerProgressBar: true
})

const notificarVencimientos = () => {
  const porVencer = inventario.value.filter(i => !esAnulado(i) && i.porVencer > 0).length
  const urgentes = inventario.value.filter(i => !esAnulado(i) && i.severidad === 'urgente').length
  if (!porVencer) return

  const lineas = [
    `<b>${porVencer}</b> ${porVencer === 1 ? 'producto tiene' : 'productos tienen'} lotes por vencer (≤ ${DIAS_ALERTA} días)`
  ]
  if (urgentes) {
    lineas.push(`<b>${urgentes}</b> ${urgentes === 1 ? 'con lote' : 'con lotes'} que vencen en 3 días o menos`)
  }

  Toast.fire({
    icon: urgentes ? 'error' : 'warning',
    title: 'Alerta de vencimientos',
    html: `<div style="font-size:13px;text-align:left">${lineas.join('<br>')}</div>`
  })
}

const cargarInventario = async ({ silencioso = false } = {}) => {
  if (!silencioso) cargando.value = true

  try {
    const [resProductos, resLotes] = await Promise.all([
      getProductos(),
      api.get('/lotes')
    ])

    const dataProductos = resProductos.data?.data || resProductos.data || resProductos || []
    const dataLotes = resLotes.data?.data || resLotes.data || resLotes || []

    productos.value = Array.isArray(dataProductos) ? dataProductos : []
    lotes.value = Array.isArray(dataLotes) ? dataLotes : []
    error.value = ''
    horaActualizacion.value = new Date().toLocaleTimeString('es-SV', { hour: '2-digit', minute: '2-digit' })
  } catch (err) {
    console.error('Error cargando inventario:', err)
    if (!silencioso) {
      productos.value = []
      lotes.value = []
      error.value = 'No se pudo cargar el inventario desde la base de datos.'
    }
  } finally {
    if (!silencioso) cargando.value = false
  }
}

const refrescarSiVisible = () => {
  if (document.visibilityState === 'visible') cargarInventario({ silencioso: true })
}

const verLotes = (item) => {
  router.push({
    name: 'DetalleLotes',
    params: { id: item.id },
    query: filtroVencimiento.value ? { filtro: filtroVencimiento.value } : {}
  })
}

onMounted(async () => {
  await cargarInventario()
  notificarVencimientos()
  temporizador = setInterval(refrescarSiVisible, INTERVALO_REFRESCO)
  document.addEventListener('visibilitychange', refrescarSiVisible)
})

onUnmounted(() => {
  clearInterval(temporizador)
  document.removeEventListener('visibilitychange', refrescarSiVisible)
})
</script>