<template>
  <div class="p-3 sm:p-6 w-full max-w-full overflow-x-hidden box-border">

    <div class="flex items-center gap-3 mb-6 min-w-0">
      <button
        type="button"
        class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer shrink-0"
        title="Volver al inventario"
        @click="volver"
      >
        <i class="bi bi-arrow-left"></i>
      </button>
      <div class="min-w-0">
        <h1 class="text-xl sm:text-3xl font-bold text-gray-800 truncate">Detalle de lotes</h1>
        <p v-if="producto" class="text-xs text-gray-400 mt-0.5 truncate">
          {{ producto.nombre }} · {{ producto.codigo }}
        </p>
      </div>
    </div>

    <div v-if="cargando && !producto" class="bg-white rounded-2xl shadow-md p-10 text-center text-sm text-gray-400">
      Cargando lotes...
    </div>

    <div v-else-if="!producto" class="bg-white rounded-2xl shadow-md p-10 text-center">
      <p class="text-sm text-gray-500 mb-4">No se encontró el producto.</p>
      <button
        type="button"
        class="bg-[#2D3748] hover:bg-[#1A202C] text-white px-5 py-2.5 rounded-2xl text-sm font-medium cursor-pointer"
        @click="volver"
      >
        Volver al inventario
      </button>
    </div>

    <template v-else>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 mb-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="min-w-0">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Producto</span>
            <span class="text-sm font-bold text-slate-800 block mt-0.5 break-words">{{ producto.nombre }}</span>
          </div>
          <div class="min-w-0">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Categoría</span>
            <span class="text-sm font-bold text-slate-800 block mt-0.5 break-words">{{ producto.categoria }}</span>
          </div>
          <div class="min-w-0">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Stock disponible</span>
            <span class="text-sm font-black text-slate-900 block mt-0.5">{{ producto.stock }}</span>
          </div>
          <div class="min-w-0">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Stock mínimo</span>
            <span class="text-sm font-bold text-slate-800 block mt-0.5">{{ producto.minimo }}</span>
          </div>
        </div>
      </div>

      <div
        v-if="exito"
        class="mb-4 flex items-start gap-2.5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
      >
        <i class="bi bi-check-circle-fill mt-0.5 shrink-0"></i>
        <span class="min-w-0 break-words">{{ exito }}</span>
      </div>

      <div
        v-if="producto.porVencer > 0"
        class="mb-4 flex items-start gap-2.5 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800"
      >
        <i class="bi bi-exclamation-triangle-fill mt-0.5 shrink-0"></i>
        <div class="min-w-0">
          <p class="font-bold m-0">Atención con la fecha de vencimiento</p>
          <p class="m-0 mt-0.5 text-xs">
            {{ producto.porVencer }} {{ producto.porVencer === 1 ? 'lote vence' : 'lotes vencen' }} en los próximos {{ DIAS_ALERTA }} días.
          </p>
        </div>
      </div>

      <div
        v-if="filtro === '' && producto.vencidosRecientes > 0"
        class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-sm text-gray-800"
      >
        <span class="flex items-center gap-2 font-medium min-w-0">
          <i class="bi bi-x-octagon-fill shrink-0"></i>
          <span class="min-w-0 break-words">
            {{ producto.vencidosRecientes }} {{ producto.vencidosRecientes === 1 ? 'lote de este producto ha vencido' : 'lotes de este producto han vencido' }}
          </span>
        </span>
        <button
          type="button"
          class="text-xs font-semibold underline cursor-pointer bg-transparent border-0 text-gray-700 hover:text-gray-900 self-start sm:self-auto shrink-0"
          @click="filtro = 'vencidos'"
        >
          Ver vencidos
        </button>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100">
          <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 m-0">Lotes en existencia</h2>

          <div class="relative w-full sm:w-52">
            <select
              v-model="filtro"
              class="w-full appearance-none pl-4 pr-9 py-2 border border-gray-300 rounded-xl bg-white text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#5B80B0] cursor-pointer box-border"
            >
              <option v-for="f in FILTROS_VENCIMIENTO" :key="f.value" :value="f.value">{{ f.label }}</option>
            </select>
            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
          </div>
        </div>

        <template v-if="lotesPaginados.length">
          <div class="hidden lg:block overflow-x-auto">
            <table class="w-full min-w-[760px] text-left border-collapse text-xs">
              <thead class="bg-slate-100 border-b border-slate-200">
                <tr class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                  <th class="px-4 py-2.5 whitespace-nowrap">ID</th>
                  <th class="px-4 py-2.5 whitespace-nowrap">Lote</th>
                  <th class="px-4 py-2.5 whitespace-nowrap text-center">Cantidad Inicial</th>
                  <th class="px-4 py-2.5 whitespace-nowrap text-center">Cantidad Actual</th>
                  <th class="px-4 py-2.5 whitespace-nowrap text-center">Estado</th>
                  <th class="px-4 py-2.5 whitespace-nowrap text-right">Vencimiento</th>
                  <th v-if="esAdmin" class="px-4 py-2.5 whitespace-nowrap text-center">Acción</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="lote in lotesPaginados"
                  :key="lote.id"
                  :class="esVencido(lote) ? 'bg-slate-100' : ['text-slate-700 hover:bg-slate-50/70', ESTADOS_LOTE[lote.estadoVenc]?.fila]"
                >
                  <td class="px-4 py-2.5 whitespace-nowrap">
                    <span
                      :class="esVencido(lote)
                        ? 'bg-slate-200 text-slate-300 line-through'
                        : 'bg-[#5B80B0]/10 text-[#5B80B0]'"
                      class="rounded-full px-2 py-0.5 text-xs font-semibold"
                    >
                      #{{ lote.id }}
                    </span>
                  </td>
                  <td
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-800'"
                    class="px-4 py-2.5 font-mono text-xs font-bold whitespace-nowrap"
                  >
                    {{ lote.codigo_lote || 'Sin lote' }}
                    <span
                      v-if="nombreDistinto(lote)"
                      :class="esVencido(lote) ? 'text-slate-300' : 'text-slate-400'"
                      class="block font-sans text-[10px] font-medium"
                    >
                      Registrado como: {{ lote.producto_nombre }}
                    </span>
                  </td>
                  <td
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-600'"
                    class="px-4 py-2.5 whitespace-nowrap text-center font-medium"
                  >
                    {{ lote.cantidad_inicial }}
                  </td>
                  <td
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-900'"
                    class="px-4 py-2.5 font-black whitespace-nowrap text-center"
                  >
                    {{ lote.cantidad_actual }}
                  </td>
                  <td class="px-4 py-2.5 whitespace-nowrap text-center">
                    <span
                      v-if="ESTADOS_LOTE[lote.estadoVenc]"
                      :class="[CLASE_BADGE, ESTADOS_LOTE[lote.estadoVenc].clase]"
                    >
                      <i :class="['bi', ESTADOS_LOTE[lote.estadoVenc].icon]"></i>
                      {{ textoEstadoLote(lote) }}
                    </span>
                    <span v-else class="text-slate-400">{{ lote.estadoVenc === 'vigente' ? 'Vigente' : '—' }}</span>
                  </td>
                  <td
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-500'"
                    class="px-4 py-2.5 whitespace-nowrap text-right"
                  >
                    {{ formatearFecha(lote.fecha_expiracion) }}
                  </td>
                  <td v-if="esAdmin" class="px-4 py-2.5 whitespace-nowrap text-center">
                    <button
                      v-if="puedeReducir(lote)"
                      type="button"
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-[11px] font-semibold cursor-pointer transition"
                      @click="abrirModal(lote)"
                    >
                      <i class="bi bi-dash-circle"></i>
                      Disminuir
                    </button>
                    <span v-else class="text-slate-300">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 sm:p-4">
            <div
              v-for="lote in lotesPaginados"
              :key="lote.id"
              :class="esVencido(lote) ? 'bg-slate-100' : (ESTADOS_LOTE[lote.estadoVenc]?.fila || 'bg-white')"
              class="min-w-0 rounded-xl border border-slate-200 p-4 flex flex-col gap-3"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <p
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-800'"
                    class="font-mono text-xs font-bold break-all m-0"
                  >
                    {{ lote.codigo_lote || 'Sin lote' }}
                  </p>
                  <p
                    v-if="nombreDistinto(lote)"
                    :class="esVencido(lote) ? 'text-slate-300' : 'text-slate-400'"
                    class="text-[10px] font-medium break-words m-0 mt-0.5"
                  >
                    Registrado como: {{ lote.producto_nombre }}
                  </p>
                  <span
                    :class="esVencido(lote)
                      ? 'bg-slate-200 text-slate-300 line-through'
                      : 'bg-[#5B80B0]/10 text-[#5B80B0]'"
                    class="inline-block mt-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                  >
                    #{{ lote.id }}
                  </span>
                </div>
                <span
                  v-if="ESTADOS_LOTE[lote.estadoVenc]"
                  :class="[CLASE_BADGE, 'shrink-0', ESTADOS_LOTE[lote.estadoVenc].clase]"
                >
                  <i :class="['bi', ESTADOS_LOTE[lote.estadoVenc].icon]"></i>
                  {{ textoEstadoLote(lote) }}
                </span>
                <span v-else class="text-xs text-slate-400 shrink-0">{{ lote.estadoVenc === 'vigente' ? 'Vigente' : '—' }}</span>
              </div>

              <div class="grid grid-cols-3 gap-2 text-center bg-white/70 rounded-lg p-2 border border-slate-100">
                <div class="min-w-0">
                  <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Inicial</span>
                  <span
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-700'"
                    class="block text-sm font-bold"
                  >
                    {{ lote.cantidad_inicial }}
                  </span>
                </div>
                <div class="min-w-0">
                  <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Actual</span>
                  <span
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-900'"
                    class="block text-sm font-black"
                  >
                    {{ lote.cantidad_actual }}
                  </span>
                </div>
                <div class="min-w-0">
                  <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Vence</span>
                  <span
                    :class="esVencido(lote) ? 'text-slate-300 line-through' : 'text-slate-600'"
                    class="block text-xs font-semibold"
                  >
                    {{ formatearFecha(lote.fecha_expiracion) }}
                  </span>
                </div>
              </div>

              <button
                v-if="puedeReducir(lote)"
                type="button"
                class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold cursor-pointer transition"
                @click="abrirModal(lote)"
              >
                <i class="bi bi-dash-circle"></i>
                Disminuir stock
              </button>
            </div>
          </div>
        </template>

        <div v-else class="px-4 py-10 text-center text-sm text-slate-400 italic">
          {{ mensajeVacio }}
        </div>

        <BarraPaginacion
          v-if="total > 0"
          v-model:pagina-actual="paginaActual"
          v-model:por-pagina="porPagina"
          :total="total"
        />
      </div>
    </template>

    <div v-if="error" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </div>

    <ModalReducirLote
      v-if="loteModal"
      :lote="loteModal"
      :nombre-producto="producto?.nombre || ''"
      @cerrar="cerrarModal"
      @reducido="alReducir"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getProductos } from '@/services/productoService'
import api from '@/services/api'
import BarraPaginacion from '@/components/BarraPaginacion.vue'
import ModalReducirLote from '@/components/ModalReducirLote.vue'
import { usePaginacion } from '@/utils/usePaginacion'
import {
  DIAS_ALERTA,
  ESTADOS_LOTE,
  ESTADOS_POR_VENCER,
  FILTROS_VENCIMIENTO,
  construirInventario,
  formatearFecha,
  textoEstadoLote
} from '@/utils/lotes'

const INTERVALO_REFRESCO = 30000
const CLASE_BADGE = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border'

const route = useRoute()
const router = useRouter()

const productos = ref([])
const lotes = ref([])
const cargando = ref(false)
const error = ref('')
const exito = ref('')
const loteModal = ref(null)

const obtenerRol = () => {
  try {
    const usuario = JSON.parse(localStorage.getItem('user'))
    return String(usuario?.rol?.nombre || usuario?.rol || '').trim().toLowerCase()
  } catch {
    return ''
  }
}

const esAdmin = obtenerRol() === 'administrador'

const filtro = ref(['por-vencer', 'vencidos'].includes(route.query.filtro) ? route.query.filtro : '')

let temporizador = null
let temporizadorExito = null

const producto = computed(() => {
  return construirInventario(productos.value, lotes.value)
    .find(p => String(p.id) === String(route.params.id)) || null
})

const nombreDistinto = (lote) => {
  return Boolean(lote.producto_nombre) && lote.producto_nombre !== producto.value?.nombre
}

const esVencido = (lote) => lote.estadoVenc === 'vencido'

const puedeReducir = (lote) => esAdmin && Number(lote.cantidad_actual) > 0

const lotesVisibles = computed(() => {
  if (!producto.value) return []
  const lista = producto.value.lotes

  if (filtro.value === 'vencidos') {
    return lista
      .filter(l => l.estadoVenc === 'vencido')
      .sort((a, b) => b.diasRestantes - a.diasRestantes || Number(a.id) - Number(b.id))
  }
  if (filtro.value === 'por-vencer') return lista.filter(l => ESTADOS_POR_VENCER.includes(l.estadoVenc))
  return lista.filter(l => l.estadoVenc !== 'vencido')
})

const { paginaActual, porPagina, total, paginados: lotesPaginados } = usePaginacion(lotesVisibles, {
  reiniciarCon: [filtro, () => route.params.id]
})

const mensajeVacio = computed(() => {
  if (filtro.value === 'vencidos') return 'Ningún lote vencido.'
  if (filtro.value === 'por-vencer') return 'Ningún lote por vencer.'
  return 'Este producto no tiene lotes vigentes.'
})

const abrirModal = (lote) => {
  if (!esAdmin) return
  loteModal.value = lote
}

const cerrarModal = () => {
  loteModal.value = null
}

const mostrarExito = (mensaje) => {
  exito.value = mensaje
  clearTimeout(temporizadorExito)
  temporizadorExito = setTimeout(() => { exito.value = '' }, 4000)
}

const alReducir = async ({ cantidad, codigo }) => {
  loteModal.value = null
  await cargarDatos({ silencioso: true })
  mostrarExito(`Se disminuyeron ${cantidad} ${cantidad === 1 ? 'unidad' : 'unidades'} del lote ${codigo}.`)
}

const cargarDatos = async ({ silencioso = false } = {}) => {
  if (!silencioso) cargando.value = true

  try {
    const [resProductos, resLotes] = await Promise.all([
      getProductos(),
      api.get('/lotes')
    ])

    const dataProductos = resProductos.data?.data || resProductos.data || []
    const dataLotes = resLotes.data?.data || resLotes.data || []

    productos.value = Array.isArray(dataProductos) ? dataProductos : []
    lotes.value = Array.isArray(dataLotes) ? dataLotes : []
    error.value = ''
  } catch (err) {
    console.error('Error cargando lotes:', err)
    if (!silencioso) error.value = 'No se pudieron cargar los lotes desde la base de datos.'
  } finally {
    if (!silencioso) cargando.value = false
  }
}

const refrescarSiVisible = () => {
  if (document.visibilityState === 'visible' && !loteModal.value) cargarDatos({ silencioso: true })
}

const volver = () => router.push('/inventario')

onMounted(() => {
  cargarDatos()
  temporizador = setInterval(refrescarSiVisible, INTERVALO_REFRESCO)
  document.addEventListener('visibilitychange', refrescarSiVisible)
})

onUnmounted(() => {
  clearInterval(temporizador)
  clearTimeout(temporizadorExito)
  document.removeEventListener('visibilitychange', refrescarSiVisible)
})
</script>