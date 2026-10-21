<template>
  <div class="main-interface-container p-4 lg:p-6 font-sans text-slate-800 bg-slate-100 min-h-screen">
    <div class="w-full max-w-5xl mx-auto space-y-6">

      <div class="top-strict-navbar flex items-center justify-between bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm">
            <i class="bi bi-cart-check-fill text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-900">Módulo de Compras</h1>
            <p class="text-xs text-slate-500">Control de entradas, recepción de órdenes e inventario</p>
          </div>
        </div>

        <div class="top-right-actions flex items-center gap-4">
          <button
            @click="iniciarNuevaCompra"
            class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl shadow-md transition font-semibold text-sm flex items-center gap-2 cursor-pointer active:bg-slate-950"
          >
            <i class="bi bi-plus-lg"></i>
            Nueva Compra
          </button>
        </div>
      </div>

      <div class="w-full">
        <div class="w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-6 min-h-[calc(100vh-13rem)] flex flex-col">
          <div class="section-header-row flex flex-wrap gap-3 justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-slate-900">Órdenes de Compra</h2>
              <p class="text-sm text-slate-500">Listado general ({{ comprasVisibles.length }} registros)</p>
            </div>

            <button
              type="button"
              role="switch"
              :aria-checked="mostrarAnuladas"
              @click="mostrarAnuladas = !mostrarAnuladas"
              class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer"
            >
              <span
                :class="mostrarAnuladas ? 'bg-slate-900' : 'bg-slate-300'"
                class="relative inline-block h-5 w-9 rounded-full transition-colors"
              >
                <span
                  :class="mostrarAnuladas ? 'translate-x-4' : 'translate-x-0'"
                  class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform"
                ></span>
              </span>
              Mostrar anuladas ({{ totalAnuladas }})
            </button>
          </div>

          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-5 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
              <i class="bi bi-funnel-fill text-slate-500 text-base"></i>
              <span>Filtrar fecha:</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-medium text-slate-600">Desde</span>
              <input
                v-model="fechaInicio"
                type="date"
                class="h-10 px-3 border border-slate-300 rounded-xl text-sm bg-white text-slate-800 outline-none focus:border-slate-900 shadow-sm"
              />
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-medium text-slate-600">Hasta</span>
              <input
                v-model="fechaFin"
                type="date"
                class="h-10 px-3 border border-slate-300 rounded-xl text-sm bg-white text-slate-800 outline-none focus:border-slate-900 shadow-sm"
              />
            </div>
            <button
              v-if="fechaInicio || fechaFin"
              @click="limpiarFiltroFecha"
              class="h-10 px-4 rounded-xl border border-rose-200 bg-rose-50 text-xs text-rose-700 hover:bg-rose-100 font-bold transition cursor-pointer flex items-center gap-1.5 shadow-sm"
            >
              <i class="bi bi-x-circle-fill text-sm"></i>
              Limpiar filtro
            </button>
          </div>

          <div class="table-card-wrapper border border-slate-200 rounded-xl overflow-hidden flex-1 bg-white flex flex-col justify-between">
            <div class="overflow-x-auto w-full">
              <table class="w-full text-left border-collapse min-w-[500px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                  <tr class="text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-4 lg:px-6 py-4">ID Orden</th>
                    <th class="px-4 lg:px-6 py-4">Fecha</th>
                    <th class="px-4 lg:px-6 py-4">Total ($)</th>
                    <th class="px-4 lg:px-6 py-4">Estado</th>
                    <th class="px-4 lg:px-6 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr
                    v-for="c in comprasPaginadas"
                    :key="c.id"
                    :class="c.anulado ? 'bg-slate-100' : 'hover:bg-slate-50/80'"
                    class="text-sm transition"
                  >
                    <td
                      :class="c.anulado ? 'text-slate-300 line-through' : 'text-slate-900'"
                      class="px-4 lg:px-6 py-4 font-mono font-bold whitespace-nowrap"
                    >
                      #{{ c.id }}
                    </td>
                    <td
                      :class="c.anulado ? 'text-slate-300 line-through' : 'text-slate-500'"
                      class="px-4 lg:px-6 py-4 text-xs whitespace-nowrap"
                    >
                      {{ c.fecha_compra ?? '—' }}
                    </td>
                    <td
                      :class="c.anulado ? 'text-slate-300 line-through' : 'text-slate-900'"
                      class="px-4 lg:px-6 py-4 font-bold whitespace-nowrap"
                    >
                      ${{ Number(c.total ?? 0).toFixed(2) }}
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                      <span
                        :class="c.anulado
                          ? 'bg-slate-200 text-slate-400 border-slate-300 line-through'
                          : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                        class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md border inline-block"
                      >
                        {{ c.anulado ? 'Anulada' : 'Activa' }}
                      </span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-right whitespace-nowrap">
                      <div class="inline-flex items-center gap-2">
                        <button
                          @click="verDetalles(c)"
                          :disabled="c.anulado"
                          class="bg-slate-100 text-slate-700 p-2 rounded-lg hover:bg-slate-900 hover:text-white transition cursor-pointer inline-flex items-center justify-center border border-slate-300 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-slate-100 disabled:hover:text-slate-700"
                          title="Ver detalles"
                        >
                          <i class="bi bi-eye"></i>
                        </button>
                        <button
                          v-if="!c.anulado"
                          @click="anular(c)"
                          class="bg-rose-50 text-rose-700 p-2 rounded-lg hover:bg-rose-600 hover:text-white transition cursor-pointer inline-flex items-center justify-center border border-rose-200"
                          title="Anular compra"
                        >
                          <i class="bi bi-slash-circle"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="comprasVisibles.length === 0">
                    <td colspan="5" class="text-center py-20 text-slate-400 italic">No hay compras para mostrar.</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <BarraPaginacion
              v-if="totalCompras > 0"
              v-model:pagina-actual="paginaActualCompras"
              v-model:por-pagina="porPaginaCompras"
              :total="totalCompras"
            />
          </div>
        </div>
      </div>
    </div>

    <ModalNuevaCompra
      v-if="modalNueva"
      :productos="productos"
      :proveedores="proveedores"
      :detalles-iniciales="detallesIniciales"
      @cerrar="modalNueva = false"
      @guardado="alGuardar"
    />

    <ModalDetalleCompra
      v-if="modalDetalle && compraDetalle"
      :compra="compraDetalle"
      :productos="productos"
      @cerrar="modalDetalle = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCompras, anularCompra } from '../services/compraService'
import { getProductos } from '../services/productoService'
import { getProveedores } from '../services/proveedorService'
import { avisoConAccion } from '@/utils/avisos'
import { alertaError, alertaExito, mensajesDeError } from '@/utils/alertas'
import { proveedoresDelProducto, recuperarBorrador } from '@/utils/compras'
import BarraPaginacion from '@/components/BarraPaginacion.vue'
import ModalNuevaCompra from '@/components/ModalNuevaCompra.vue'
import ModalDetalleCompra from '@/components/ModalDetalleCompra.vue'
import { usePaginacion } from '@/utils/usePaginacion'

const route = useRoute()
const router = useRouter()

const compras = ref([])
const productos = ref([])
const proveedores = ref([])
const modalNueva = ref(false)
const detallesIniciales = ref(null)

const fechaInicio = ref('')
const fechaFin = ref('')

const modalDetalle = ref(false)
const compraDetalle = ref(null)

const mostrarAnuladas = ref(false)

function limpiarFiltroFecha() {
  fechaInicio.value = ''
  fechaFin.value = ''
}

const comprasVisibles = computed(() => {
  let resultado = mostrarAnuladas.value ? compras.value : compras.value.filter(c => !c.anulado)

  if (fechaInicio.value) {
    resultado = resultado.filter(c => {
      if (!c.fecha_compra) return false
      const fecha = String(c.fecha_compra).substring(0, 10)
      return fecha >= fechaInicio.value
    })
  }

  if (fechaFin.value) {
    resultado = resultado.filter(c => {
      if (!c.fecha_compra) return false
      const fecha = String(c.fecha_compra).substring(0, 10)
      return fecha <= fechaFin.value
    })
  }

  return resultado
})

const totalAnuladas = computed(() => compras.value.filter(c => c.anulado).length)

const {
  paginaActual: paginaActualCompras,
  porPagina: porPaginaCompras,
  total: totalCompras,
  paginados: comprasPaginadas
} = usePaginacion(comprasVisibles, {
  reiniciarCon: [mostrarAnuladas, fechaInicio, fechaFin]
})

const cargar = async () => {
  try {
    const res = await getCompras()
    const dataExtraida = res.data?.data || res.data || res
    compras.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar compras:', err)
  }
}

const cargarProductos = async () => {
  try {
    const res = await getProductos()
    const dataExtraida = res.data?.data || res.data || res
    productos.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar productos:', err)
  }
}

const cargarProveedores = async () => {
  try {
    const res = await getProveedores()
    const dataExtraida = res.data?.data || res.data || res
    proveedores.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar proveedores:', err)
  }
}

let cargaInicial = null

onMounted(async () => {
  cargaInicial = Promise.all([cargar(), cargarProductos(), cargarProveedores()])
  await cargaInicial

  const borrador = recuperarBorrador()

  if (borrador) {
    detallesIniciales.value = borrador
    modalNueva.value = true
  }

  if (route.query.nuevo) {
    router.replace({ query: {} })
    if (!borrador) iniciarNuevaCompra()
  }
})

const irAProductos = () => {
  router.push({ path: '/productos', query: { nuevo: 1 } })
}

async function iniciarNuevaCompra() {
  if (cargaInicial) await cargaInicial

  const activos = productos.value.filter(p => !p.anulado)

  if (activos.length === 0) {
    const ir = await avisoConAccion({
      icon: 'warning',
      title: 'Aún no hay productos',
      text: 'Para registrar una compra primero necesitas crear al menos un producto.',
      boton: 'Crear producto'
    })
    if (ir) irAProductos()
    return
  }

  const hayProductoAfiliado = activos.some(
    p => proveedoresDelProducto(p.id, productos.value, proveedores.value).length > 0
  )

  if (!hayProductoAfiliado) {
    const ir = await avisoConAccion({
      icon: 'warning',
      title: 'Ningún producto tiene proveedor',
      text: 'Afilia al menos un producto a un proveedor para poder registrar compras.',
      boton: 'Ir a proveedores'
    })
    if (ir) router.push({ path: '/proveedores' })
    return
  }

  detallesIniciales.value = null
  modalNueva.value = true
}

async function alGuardar() {
  modalNueva.value = false
  await cargar()
  await cargarProductos()
  alertaExito('Compra registrada', 'El inventario se actualizó correctamente.')
}

function verDetalles(compra) {
  if (compra.anulado) return
  compraDetalle.value = compra
  modalDetalle.value = true
}

async function anular(compra) {
  const confirmado = await avisoConAccion({
    icon: 'warning',
    title: `¿Anular la compra #${compra.id}?`,
    text: 'Se eliminarán los lotes de esta compra y se descontará su stock. Esta acción no se puede deshacer.',
    boton: 'Sí, anular'
  })
  if (!confirmado) return

  try {
    await anularCompra(compra.id)
    await cargar()
    await cargarProductos()
    alertaExito('Compra anulada', 'El inventario se actualizó correctamente.')
  } catch (error) {
    console.error(error)
    alertaError(
      'No se pudo anular la compra',
      mensajesDeError(error, 'Error al procesar la solicitud.')
    )
  }
}
</script>