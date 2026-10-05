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

      <div class="content-layout-flex flex flex-col xl:flex-row gap-5 items-start w-full">
        <div class="left-content-panel w-full xl:flex-1 xl:min-w-0 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 min-h-[calc(100vh-13rem)] flex flex-col">
          <div class="section-header-row flex justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-slate-900">Órdenes de Compra</h2>
              <p class="text-sm text-slate-500">Listado general ({{ compras.length }} registros)</p>
            </div>
          </div>

          <div class="table-card-wrapper border border-slate-200 rounded-xl overflow-hidden flex-1 bg-white flex flex-col justify-between">
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                  <tr class="text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-6 py-4">ID Orden</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Total ($)</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="c in comprasPaginadas" :key="c.id" class="hover:bg-slate-50/80 text-sm transition">
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">#{{ c.id }}</td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ c.fecha_compra ?? '—' }}</td>
                    <td class="px-6 py-4 font-bold text-slate-900">${{ Number(c.total ?? 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-right">
                      <button
                        @click="abrirEditar(c)"
                        class="bg-slate-100 text-slate-700 p-2 rounded-lg hover:bg-slate-900 hover:text-white transition cursor-pointer inline-flex items-center justify-center border border-slate-300"
                        title="Editar Compra"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="compras.length === 0">
                    <td colspan="4" class="text-center py-20 text-slate-400 italic">No hay compras registradas.</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="totalPaginasCompras > 1" class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end shrink-0">
              <div class="flex items-center gap-1.5">
                <button
                  @click="paginaActualCompras--"
                  :disabled="paginaActualCompras === 1"
                  class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition cursor-pointer shadow-sm"
                  title="Anterior"
                >
                  <i class="bi bi-chevron-left text-xs"></i>
                </button>

                <button
                  v-for="pagina in paginasVisibles"
                  :key="pagina"
                  @click="paginaActualCompras = pagina"
                  :class="[
                    'w-9 h-9 rounded-xl text-xs font-bold transition cursor-pointer flex items-center justify-center shadow-sm',
                    paginaActualCompras === pagina
                      ? 'bg-slate-800 text-white border border-slate-800'
                      : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200'
                  ]"
                >
                  {{ pagina }}
                </button>

                <button
                  @click="paginaActualCompras++"
                  :disabled="paginaActualCompras === totalPaginasCompras"
                  class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition cursor-pointer shadow-sm"
                  title="Siguiente"
                >
                  <i class="bi bi-chevron-right text-xs"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="right-widgets-panel w-full xl:w-[260px] xl:shrink-0">
          <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-200">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Resumen</h2>
            <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <span class="text-xs text-slate-600">Total Compras</span>
              <span class="text-lg font-bold text-slate-900">{{ compras.length }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4">
      <div class="modal-card-box bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden">

        <div class="px-5 py-4 flex justify-between items-center border-b border-slate-200 bg-slate-900 text-white shrink-0">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-white border border-slate-700 shrink-0">
              <i :class="modoEdicion ? 'bi bi-pencil-square' : 'bi bi-bag-plus-fill'" class="text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-white text-base">
                {{ modoEdicion ? `Editar Orden de Compra #${compraIdEdicion}` : 'Registrar Nueva Orden de Compra' }}
              </h3>
              <p class="text-xs text-slate-300">Detalla los ítems, precios y datos de lote correspondientes</p>
            </div>
          </div>
          <button
            class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition cursor-pointer shrink-0"
            @click="cerrar"
          >
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>

        <div class="modal-body-scroll p-4 sm:p-5 overflow-y-auto overflow-x-hidden flex-1 space-y-5 bg-slate-50/50">

          <div class="form-row-single space-y-3">
            <div class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm">
              <label class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-1.5">Fecha de Compra</label>
              <input
                v-model="fechaCompraNueva"
                type="date"
                :readonly="modoEdicion"
                :class="[
                  'form-force-input px-3 py-2 border rounded-lg text-sm font-medium outline-none',
                  modoEdicion ? 'bg-slate-100 text-slate-500 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-800 border-slate-300 focus:border-slate-900'
                ]"
              />
            </div>
            <div class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm flex items-center justify-between">
              <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Monto Total Estimado</span>
              <span class="text-2xl font-black text-slate-900">${{ totalCompraNueva }}</span>
            </div>
          </div>

          <div class="form-stack-container">
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-3">Productos en la Orden</label>

            <div
              v-for="(d, i) in detalles"
              :key="i"
              class="producto-card-item p-4 border border-slate-200 rounded-xl bg-white shadow-sm mb-4 space-y-3.5"
            >
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ítem #{{ i + 1 }}</span>
                <span v-if="d.detalle_id" class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">ID Registro: #{{ d.detalle_id }}</span>
              </div>

              <div class="field-block">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Producto</label>
                <button
                  type="button"
                  @click="abrirSelector(i)"
                  :disabled="modoEdicion"
                  class="form-force-button h-10 flex items-center justify-between px-3 rounded-lg border border-slate-300 bg-white hover:border-slate-900 text-slate-800 text-left cursor-pointer shadow-sm transition disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed disabled:hover:border-slate-300"
                >
                  <span class="truncate text-sm font-medium">
                    {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                  </span>
                  <i class="bi bi-search text-slate-400 text-xs shrink-0 ml-2"></i>
                </button>
              </div>

              <div class="field-block">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Proveedor Suministrador</label>
                <select
                  v-model="d.proveedor_id"
                  :disabled="!d.producto_id"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-800 outline-none focus:border-slate-900 disabled:bg-slate-100 disabled:text-slate-400"
                >
                  <option value="">-- Seleccionar Proveedor --</option>
                  <option
                    v-for="prov in obtenerProveedoresDelProducto(d.producto_id)"
                    :key="prov.id"
                    :value="prov.id"
                  >
                    {{ prov.nombre_proveedor || prov.nombre }}
                  </option>
                </select>
                <p v-if="d.producto_id && obtenerProveedoresDelProducto(d.producto_id).length === 0" class="text-[11px] text-amber-600 mt-1">
                  * Este producto no tiene proveedores vinculados aún.
                </p>
                <button
                  type="button"
                  @click="irAProveedores(i)"
                  class="mt-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-900 underline underline-offset-2 cursor-pointer"
                >
                  ¿No aparece el proveedor? Agregar o vincular proveedor
                </button>
              </div>

              <div class="field-grid-2">
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Cant. Paquetes</label>
                  <input
                    v-model.number="d.cantidad"
                    type="number"
                    min="1"
                    step="1"
                    placeholder="1"
                    @keydown="soloEnteros"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-slate-900"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Unid. por Paquete</label>
                  <input
                    v-model.number="d.unidades_por_paquete"
                    type="number"
                    min="1"
                    step="1"
                    placeholder="1"
                    @keydown="soloEnteros"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-slate-900"
                  />
                </div>
              </div>

              <div class="field-block">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Precio Paquete ($)</label>
                <div class="relative w-full">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                  <input
                    v-model.number="d.precio_compra"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    class="form-force-input h-10 pl-7 pr-3 border border-slate-300 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-slate-900"
                  />
                </div>
              </div>

              <div class="field-grid-2 pt-2 border-t border-slate-100">
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Código de Lote</label>
                  <input
                    v-model="d.codigo_lote"
                    type="text"
                    :readonly="modoEdicion"
                    placeholder="Ej: LOTE-123"
                    :class="[
                      'form-force-input h-10 px-3 border rounded-lg text-sm outline-none',
                      modoEdicion ? 'bg-slate-100 text-slate-500 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-800 border-slate-300 focus:border-slate-900'
                    ]"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Fecha Expiración</label>
                  <input
                    v-model="d.fecha_expiracion"
                    type="date"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-700 outline-none focus:border-slate-900"
                  />
                </div>
              </div>

              <div class="pt-3 border-t border-slate-200 flex items-center justify-between bg-slate-50 -mx-4 -mb-4 p-3.5 rounded-b-xl">
                <div>
                  <span class="text-[10px] text-slate-400 font-bold uppercase block tracking-wider">Subtotal Ítem</span>
                  <span class="text-lg font-black text-slate-900">
                    ${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}
                  </span>
                </div>

                <button
                  v-if="!modoEdicion"
                  type="button"
                  @click="remove(i)"
                  class="h-9 px-3.5 inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition cursor-pointer text-xs font-bold"
                >
                  <i class="bi bi-trash text-sm"></i>
                  <span>Eliminar</span>
                </button>
              </div>

            </div>
          </div>

          <button
            v-if="!modoEdicion"
            @click="add"
            type="button"
            class="w-full py-3.5 rounded-xl border-2 border-dashed border-slate-300 bg-white hover:bg-slate-100 hover:border-slate-800 text-xs font-bold text-slate-800 transition flex items-center justify-center gap-2 cursor-pointer shadow-sm"
          >
            <i class="bi bi-plus-circle-fill text-base text-slate-900"></i> Agregar otro producto
          </button>
        </div>

        <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Total Final:</span>
            <span class="text-xl font-black text-slate-900">${{ totalCompraNueva }}</span>
          </div>
          <div class="flex gap-2">
            <button
              @click="cerrar"
              :disabled="cargando"
              class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 transition cursor-pointer"
            >
              Cancelar
            </button>
            <button
              @click="guardar"
              :disabled="cargando"
              class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-bold text-white shadow-md transition flex items-center gap-1.5 cursor-pointer active:bg-slate-950"
            >
              <i v-if="!cargando" class="bi bi-check-lg text-sm"></i>
              {{ cargando ? 'Guardando...' : (modoEdicion ? 'Actualizar Compra' : 'Guardar Compra') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="modalProductos" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-200 flex justify-between items-center bg-slate-900 text-white">
          <h3 class="font-bold text-xs uppercase tracking-wider">Catálogo de Productos</h3>
          <button class="text-slate-300 hover:text-white cursor-pointer" @click="modalProductos = false">
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>
        <div class="p-4 bg-white">
          <div class="relative mb-3">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
              v-model="busqueda"
              class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 outline-none focus:border-slate-900"
              placeholder="Buscar por nombre..."
            />
          </div>
          <div class="max-h-[260px] overflow-y-auto space-y-1.5 pr-1">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              @click="seleccionarProducto(p)"
              class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-slate-900 hover:bg-slate-100 transition flex justify-between items-center"
            >
              <div>
                <p class="font-bold text-slate-900 text-xs">{{ p.nombre }}</p>
                <p class="text-[10px] text-slate-500">Unidad: {{ p.unidad_medida?.nombre || 'pieza' }}</p>
              </div>
              <span class="text-[10px] bg-white text-slate-700 font-semibold px-2 py-0.5 rounded-md border border-slate-200">
                Stock: {{ p.stock ?? 0 }}
              </span>
            </div>
            <p v-if="productosFiltrados.length === 0" class="text-center py-6 text-xs text-slate-400 italic">
              No hay productos que coincidan.
            </p>
          </div>
          <button
            type="button"
            @click="irAProductos"
            class="mt-3 w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white text-[11px] font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition cursor-pointer"
          >
            <i class="bi bi-box-seam"></i>
            <span>¿No encuentras el producto? Agregar producto</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCompras, createCompra, updateCompra } from '../services/compraService'
import { getProductos } from '../services/productoService'
import { getProveedores } from '../services/proveedorService'
import { avisoConAccion } from '@/utils/avisos'

const route = useRoute()
const router = useRouter()

const compras = ref([])
const productos = ref([])
const proveedores = ref([])
const modal = ref(false)
const modalProductos = ref(false)
const cargando = ref(false)
const detalles = ref([])
const indexProducto = ref(null)
const busqueda = ref('')
const fechaCompraNueva = ref(new Date().toISOString().split('T')[0])

const modoEdicion = ref(false)
const compraIdEdicion = ref(null)

const paginaActualCompras = ref(1)
const porPaginaCompras = ref(8)

const comprasPaginadas = computed(() => {
  const inicio = (paginaActualCompras.value - 1) * porPaginaCompras.value
  return compras.value.slice(inicio, inicio + porPaginaCompras.value)
})

const totalPaginasCompras = computed(() => {
  return Math.ceil(compras.value.length / porPaginaCompras.value) || 1
})

const paginasVisibles = computed(() => {
  const paginas = []
  for (let i = 1; i <= totalPaginasCompras.value; i++) {
    paginas.push(i)
  }
  return paginas
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

// ---------- Borrador de la compra (se conserva al ir a crear proveedor/producto) ----------
const CLAVE_BORRADOR = 'borrador_compra'

const guardarBorrador = () => {
  try {
    sessionStorage.setItem(CLAVE_BORRADOR, JSON.stringify({
      fecha: fechaCompraNueva.value,
      detalles: detalles.value
    }))
  } catch (error) {
    console.error('No se pudo guardar el borrador:', error)
  }
}

const restaurarBorrador = () => {
  const raw = sessionStorage.getItem(CLAVE_BORRADOR)
  if (!raw) return false

  try {
    const borrador = JSON.parse(raw)
    if (!Array.isArray(borrador.detalles) || borrador.detalles.length === 0) return false

    modoEdicion.value = false
    compraIdEdicion.value = null
    fechaCompraNueva.value = borrador.fecha || new Date().toISOString().split('T')[0]
    detalles.value = borrador.detalles
    modal.value = true
    return true
  } catch (error) {
    console.error('No se pudo restaurar el borrador:', error)
    return false
  } finally {
    sessionStorage.removeItem(CLAVE_BORRADOR)
  }
}

let cargaInicial = null

onMounted(async () => {
  cargaInicial = Promise.all([cargar(), cargarProductos(), cargarProveedores()])
  await cargaInicial

  const restaurado = restaurarBorrador()

  if (route.query.nuevo) {
    router.replace({ query: {} })
    if (!restaurado) iniciarNuevaCompra()
  }
})

function obtenerProveedoresDelProducto(productoId) {
  if (!productoId) return []

  const prod = productos.value.find(p => String(p.id) === String(productoId))
  if (prod && Array.isArray(prod.proveedores) && prod.proveedores.length > 0) {
    return prod.proveedores
  }

  return proveedores.value.filter(p => {
    if (!Array.isArray(p.productos)) return false
    return p.productos.some(prodItem => String(prodItem.id) === String(productoId))
  })
}

const irAProductos = () => {
  if (modal.value && !modoEdicion.value) guardarBorrador()
  router.push({ path: '/productos', query: { nuevo: 1 } })
}

const irAProveedores = (i) => {
  if (modal.value && !modoEdicion.value) guardarBorrador()

  const productoId = detalles.value[i]?.producto_id
  router.push({
    path: '/proveedores',
    query: { nuevo: 1, ...(productoId ? { producto: productoId } : {}) }
  })
}

async function iniciarNuevaCompra() {
  if (cargaInicial) await cargaInicial

  if (productos.value.length === 0) {
    const ir = await avisoConAccion({
      icon: 'warning',
      title: 'Aún no hay productos',
      text: 'Para registrar una compra primero necesitas crear al menos un producto.',
      boton: 'Crear producto'
    })
    if (ir) irAProductos()
    return
  }

  const hayProductoAfiliado = productos.value.some(p => obtenerProveedoresDelProducto(p.id).length > 0)

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

  abrirModalCrear()
}

function abrirModalCrear() {
  modoEdicion.value = false
  compraIdEdicion.value = null
  fechaCompraNueva.value = new Date().toISOString().split('T')[0]
  detalles.value = [{
    producto_id: '',
    proveedor_id: '',
    cantidad: 1,
    unidades_por_paquete: 1,
    precio_compra: 0,
    codigo_lote: '',
    fecha_expiracion: ''
  }]
  modal.value = true
}

function abrirEditar(compra) {
  modoEdicion.value = true
  compraIdEdicion.value = compra.id
  fechaCompraNueva.value = compra.fecha_compra ? compra.fecha_compra.substring(0, 10) : new Date().toISOString().split('T')[0]

  if (compra.detalles && compra.detalles.length > 0) {
    detalles.value = compra.detalles.map(det => {
      const lote = det.lotes && det.lotes.length > 0 ? det.lotes[0] : null
      return {
        id: det.id,
        detalle_id: det.id,
        producto_id: det.producto_id,
        proveedor_id: det.proveedor_id || '',
        cantidad: det.cantidad || 1,
        unidades_por_paquete: det.unidades_por_paquete || 1,
        precio_compra: Number(det.precio_compra || 0),
        codigo_lote: lote ? lote.codigo_lote : (det.codigo_lote || ''),
        fecha_expiracion: lote && lote.fecha_expiracion ? lote.fecha_expiracion.substring(0, 10) : (det.fecha_expiracion ? det.fecha_expiracion.substring(0, 10) : '')
      }
    })
  } else {
    detalles.value = [{
      producto_id: '',
      proveedor_id: '',
      cantidad: 1,
      unidades_por_paquete: 1,
      precio_compra: 0,
      codigo_lote: '',
      fecha_expiracion: ''
    }]
  }

  modal.value = true
}

function add() {
  detalles.value.push({
    producto_id: '',
    proveedor_id: '',
    cantidad: 1,
    unidades_por_paquete: 1,
    precio_compra: 0,
    codigo_lote: '',
    fecha_expiracion: ''
  })
}

function remove(i) {
  detalles.value.splice(i, 1)
}

function cerrar() {
  sessionStorage.removeItem(CLAVE_BORRADOR)
  modal.value = false
}

const totalCompraNueva = computed(() => {
  return detalles.value.reduce((acc, d) => acc + ((d.cantidad || 0) * (d.precio_compra || 0)), 0).toFixed(2)
})

function abrirSelector(i) {
  if (modoEdicion.value) return
  indexProducto.value = i
  modalProductos.value = true
}

async function avisarProductoSinProveedor(p) {
  const ir = await avisoConAccion({
    icon: 'info',
    title: 'Producto sin proveedor',
    text: `"${p.nombre}" no está afiliado a ningún proveedor.`,
    boton: 'Ir a proveedores'
  })
  if (ir) router.push({ path: '/proveedores' })
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  detalles.value[indexProducto.value].proveedor_id = ''
  modalProductos.value = false

  if (obtenerProveedoresDelProducto(p.id).length === 0) {
    avisarProductoSinProveedor(p)
  }
}

const productosFiltrados = computed(() => {
  if (!Array.isArray(productos.value)) return []
  const query = busqueda.value.toLowerCase().trim()
  return productos.value.filter(p => (p.nombre || '').toLowerCase().includes(query))
})

function getProductoNombre(id) {
  if (!Array.isArray(productos.value)) return ''
  const prod = productos.value.find(p => String(p.id) === String(id))
  return prod ? prod.nombre : ''
}

// Bloquea teclas que generan decimales o signos en los campos de cantidad
const soloEnteros = (e) => {
  if (['.', ',', 'e', 'E', '+', '-'].includes(e.key)) e.preventDefault()
}

async function guardar() {
  if (detalles.value.length === 0) {
    return alert('Debes agregar al menos un producto')
  }

  for (const [i, d] of detalles.value.entries()) {
    const n = i + 1

    if (!d.producto_id || !d.proveedor_id) {
      return alert(`Ítem #${n}: selecciona el producto y su proveedor.`)
    }
    if (!Number.isInteger(Number(d.cantidad)) || Number(d.cantidad) < 1) {
      return alert(`Ítem #${n}: la cantidad de paquetes debe ser un número entero de 1 en adelante (no se permiten fracciones como 1.5).`)
    }
    if (!Number.isInteger(Number(d.unidades_por_paquete)) || Number(d.unidades_por_paquete) < 1) {
      return alert(`Ítem #${n}: las unidades por paquete deben ser un número entero de 1 en adelante.`)
    }
    if (!(Number(d.precio_compra) > 0)) {
      return alert(`Ítem #${n}: el precio del paquete debe ser mayor a 0.`)
    }
  }

  cargando.value = true
  try {
    const payload = {
      fecha_compra: fechaCompraNueva.value,
      detalles: detalles.value.map(d => ({
        id: d.detalle_id || d.id || undefined,
        detalle_id: d.detalle_id || d.id || undefined,
        producto_id: Number(d.producto_id),
        proveedor_id: Number(d.proveedor_id),
        cantidad: Number(d.cantidad),
        unidades_por_paquete: Number(d.unidades_por_paquete || 1),
        precio_compra: Number(d.precio_compra),
        codigo_lote: d.codigo_lote || null,
        fecha_expiracion: d.fecha_expiracion || null
      }))
    }

    if (modoEdicion.value) {
      await updateCompra(compraIdEdicion.value, payload)
    } else {
      await createCompra(payload)
    }

    await cargar()
    await cargarProductos()
    cerrar()
  } catch (error) {
    const apiErrors = error.response?.data?.errors
    if (apiErrors) {
      const msg = Object.values(apiErrors).flat().join('\n')
      alert(`Error de validación:\n${msg}`)
    } else {
      alert(error.response?.data?.message || 'Error al procesar la solicitud.')
    }
    console.error(error)
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.modal-card-box {
  width: 100% !important;
  max-width: 580px !important;
  box-sizing: border-box !important;
}

.modal-body-scroll {
  width: 100% !important;
  box-sizing: border-box !important;
}

.form-row-single {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.form-stack-container {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.producto-card-item {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.field-block {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.field-grid-2 {
  display: grid !important;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
  gap: 12px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.form-force-input,
.form-force-button {
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
  min-width: 0 !important;
  box-sizing: border-box !important;
}
</style>