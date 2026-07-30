<template>
  <div class="main-interface-container p-6 font-sans text-slate-800 bg-[#F1F5F9] min-h-screen">
    <div class="mx-auto max-w-7xl space-y-6">

      <!-- NAVBAR SUPERIOR -->
      <div class="top-strict-navbar flex items-center justify-between bg-white rounded-2xl shadow-md p-4">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#46674A]/10 text-[#46674A]">
            <i class="bi bi-cart-check-fill text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-800">Módulo de Compras</h1>
            <p class="text-xs text-gray-500">Control de entradas, recepción de órdenes e inventario</p>
          </div>
        </div>
        
        <div class="top-right-actions flex items-center gap-4">
          <button 
            @click="abrirModal"
            class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium text-sm flex items-center gap-2"
          >
            <i class="bi bi-plus-lg"></i>
            Nueva Compra
          </button>
        </div>
      </div>

      <!-- DISPOSICIÓN PRINCIPAL EN COLUMNAS -->
      <div class="content-layout-flex flex flex-col lg:flex-row gap-6 items-start w-full">
        
        <!-- PANEL IZQUIERDO: TABLA -->
        <div class="left-content-panel w-full lg:w-3/4 bg-white rounded-2xl shadow-md p-6">
          <div class="section-header-row flex justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-gray-800">Órdenes de Compra</h2>
              <p class="text-sm text-gray-500">Listado general ({{ compras.length }} registros)</p>
            </div>
          </div>

          <div class="table-card-wrapper border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                  <tr class="text-gray-700 text-sm font-semibold">
                    <th class="px-6 py-4">ID Orden</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Total ($)</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="c in compras" :key="c.id" class="border-t border-gray-200 hover:bg-gray-50 text-sm">
                    <td class="px-6 py-4 font-mono font-bold text-gray-800">#{{ c.id }}</td>
                    <td class="px-6 py-4 text-gray-600 text-xs">{{ c.fecha_compra ?? '—' }}</td>
                    <td class="px-6 py-4">
                      <span
                        class="rounded-full px-2.5 py-1 text-xs font-semibold inline-flex items-center gap-1.5"
                        :class="{
                          'bg-amber-100 text-amber-700': c.estado === 'pendiente',
                          'bg-green-100 text-green-700': c.estado === 'completada',
                          'bg-red-100 text-red-700': c.estado === 'cancelada'
                        }"
                      >
                        <i class="bi text-xs" :class="{
                          'bi-hourglass-split': c.estado === 'pendiente',
                          'bi-check-lg': c.estado === 'completada',
                          'bi-x-circle': c.estado === 'cancelada'
                        }"></i>
                        {{ c.estado ? c.estado.toUpperCase() : 'N/A' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">${{ Number(c.total ?? 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex gap-2 justify-end">
                        <button 
                          @click="abrirCompletar(c)"
                          class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" 
                          title="Editar / Ver Detalle"
                        >
                          <i class="bi bi-pencil"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="compras.length === 0">
                    <td colspan="5" class="text-center py-12 text-gray-400 italic">
                      <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                      No hay compras registradas.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: WIDGET DE MÉTRICAS -->
        <div class="right-widgets-panel w-full lg:w-1/4 flex flex-col gap-4">
          <div class="inventory-card-widget bg-white rounded-2xl shadow-md p-4 border border-gray-100">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Resumen de Compras</h2>
            
            <div class="widget-metric-row flex justify-between items-center p-3 bg-gray-50 rounded-xl mb-2">
              <div>
                <span class="block text-xs text-gray-500">Total Órdenes</span>
                <span class="text-xl font-bold text-gray-800">{{ compras.length }}</span>
              </div>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-3 bg-amber-50 rounded-xl mb-2">
              <div>
                <span class="block text-xs text-amber-600">Órdenes Pendientes</span>
                <span class="text-xl font-bold text-amber-700">
                  {{ compras.filter(c => c.estado === 'pendiente').length }}
                </span>
              </div>
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                <i class="bi bi-clock-history"></i>
              </span>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-3 bg-green-50 rounded-xl">
              <div>
                <span class="block text-xs text-green-600">Completadas</span>
                <span class="text-xl font-bold text-green-700">
                  {{ compras.filter(c => c.estado === 'completada').length }}
                </span>
              </div>
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-700">
                <i class="bi bi-check2-circle"></i>
              </span>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- MODAL 1: NUEVA COMPRA (REDISEÑADO & MINIMALISTA) -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-40 p-4 lg:p-6">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Encabezado -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#46674A]/10 text-[#46674A]">
              <i class="bi bi-bag-plus-fill text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-gray-800 text-base">Registrar Nueva Orden de Compra</h3>
              <p class="text-xs text-gray-400">Agrega productos, define cantidades y sus costos de entrada</p>
            </div>
          </div>
          <button class="text-gray-400 hover:text-gray-600 transition p-1.5 rounded-lg hover:bg-gray-100" @click="cerrar">
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Contenido principal con scroll -->
        <div class="p-6 overflow-y-auto flex-1 space-y-5">
          
          <!-- Tabla minimalista de ítems -->
          <div class="border border-gray-200/80 rounded-xl overflow-hidden bg-white shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200/80 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
                  <th class="py-3 px-4 w-[45%]">Producto</th>
                  <th class="py-3 px-4 w-[18%]">Cantidad</th>
                  <th class="py-3 px-4 w-[20%]">Precio Compra ($)</th>
                  <th class="py-3 px-4 w-[12%] text-right">Subtotal</th>
                  <th class="py-3 px-2 w-[5%] text-center"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(d, i) in detalles" :key="i" class="hover:bg-slate-50/50 transition">
                  
                  <!-- Selector de Producto -->
                  <td class="p-3">
                    <button
                      type="button"
                      @click="abrirSelector(i)"
                      class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:border-[#46674A] hover:bg-[#46674A]/5 text-gray-700 transition text-left group"
                    >
                      <span class="truncate font-medium text-xs text-gray-800">
                        <i class="bi bi-box-seam mr-1.5 text-gray-400 group-hover:text-[#46674A]"></i>
                        {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                      </span>
                      <i class="bi bi-search text-gray-400 group-hover:text-[#46674A] text-xs"></i>
                    </button>
                  </td>

                  <!-- Input Cantidad -->
                  <td class="p-3">
                    <input
                      v-model.number="d.cantidad"
                      type="number"
                      min="1"
                      placeholder="1"
                      class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-800 bg-white outline-none focus:border-[#46674A] focus:ring-1 focus:ring-[#46674A] transition"
                    />
                  </td>

                  <!-- Input Precio Compra -->
                  <td class="p-3">
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">$</span>
                      <input
                        v-model.number="d.precio_compra"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="w-full pl-6 pr-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-[#46674A] bg-white outline-none focus:border-[#46674A] focus:ring-1 focus:ring-[#46674A] transition"
                      />
                    </div>
                  </td>

                  <!-- Subtotal -->
                  <td class="p-3 text-right font-bold text-gray-800 text-xs">
                    ${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}
                  </td>

                  <!-- Eliminar -->
                  <td class="p-3 text-center">
                    <button 
                      @click="remove(i)" 
                      class="h-8 w-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                      title="Eliminar fila"
                    >
                      <i class="bi bi-trash text-sm"></i>
                    </button>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>

          <!-- Botón de agregar más productos -->
          <button
            @click="add"
            type="button"
            class="w-full py-2.5 rounded-xl border border-dashed border-gray-300 hover:border-[#46674A] text-xs font-semibold text-gray-600 hover:text-[#46674A] hover:bg-[#46674A]/5 transition flex items-center justify-center gap-2"
          >
            <i class="bi bi-plus-lg"></i> Agregar otro producto
          </button>

        </div>

        <!-- Footer / Total General & Acciones -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 font-medium">Total de la Orden:</span>
            <span class="text-xl font-black text-gray-900">${{ totalCompraNueva }}</span>
          </div>

          <div class="flex gap-3">
            <button
              @click="cerrar"
              :disabled="cargando"
              class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 bg-white hover:bg-gray-100 transition disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              @click="guardar"
              :disabled="cargando"
              class="px-6 py-2 rounded-xl bg-[#46674A] hover:bg-[#3b5740] text-xs font-bold text-white shadow-md transition disabled:opacity-50 flex items-center gap-2"
            >
              <i v-if="!cargando" class="bi bi-check-lg"></i>
              {{ cargando ? 'Guardando...' : 'Guardar Compra' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MODAL 2: SELECTOR DE PRODUCTOS -->
    <div v-if="modalProductos" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-sm">Catálogo de Productos</h3>
          <button class="text-gray-400 hover:text-gray-600" @click="modalProductos = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-4 bg-white">
          <div class="relative mb-3">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              v-model="busqueda"
              class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#46674A]/20"
              placeholder="Escribe para buscar producto..."
            />
          </div>

          <div class="max-h-[260px] overflow-y-auto space-y-2 pr-1">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              @click="seleccionarProducto(p)"
              class="p-3 rounded-xl border border-gray-200 bg-white cursor-pointer hover:border-[#46674A] hover:bg-[#46674A]/5 transition flex justify-between items-center"
            >
              <div>
                <p class="font-bold text-gray-800 text-xs">{{ p.nombre }}</p>
                <p class="text-[11px] text-gray-500">Unidad: {{ p.unidad_medida?.nombre || p.unidad_medida || 'pieza' }}</p>
              </div>
              <span class="text-[11px] bg-gray-100 text-gray-700 font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                Stock: {{ p.stock ?? 0 }}
              </span>
            </div>

            <p v-if="productosFiltrados.length === 0" class="text-center text-gray-400 py-6 text-xs italic">
              No se encontraron coincidencias
            </p>
          </div>

          <div class="flex justify-end mt-4 pt-3 border-t border-gray-200">
            <button @click="modalProductos = false" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 3: DETALLE Y COMPLETAR -->
    <div v-if="modalCompletar" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-40 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-lg">Detalle de Compra #{{ compraSeleccionada?.id }}</h3>
          <button class="text-gray-400 hover:text-gray-600" @click="modalCompletar = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-6 bg-white">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="p-3 border border-gray-200 rounded-xl bg-gray-50">
              <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">
                Fecha Recepción
              </label>
              <input
                v-model="fechaLlegada"
                type="date"
                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white outline-none focus:ring-2 focus:ring-[#46674A]/20"
                :disabled="!esPendiente || cargando"
              />
            </div>

            <div class="p-3 border border-gray-200 rounded-xl bg-gray-50 flex flex-col justify-center">
              <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Monto Total</span>
              <span class="text-xl font-black text-gray-900">
                ${{ Number(compraSeleccionada?.total ?? 0).toFixed(2) }}
              </span>
            </div>
          </div>

          <!-- TABLA DE DETALLES Y LOTES -->
          <div class="max-h-[240px] overflow-y-auto border border-gray-200 rounded-xl bg-white mb-4">
            <table class="w-full text-left text-xs">
              <thead class="bg-gray-100 border-b border-gray-200 text-gray-700 font-semibold uppercase">
                <tr>
                  <th class="p-3">Producto</th>
                  <th class="p-3">Cant.</th>
                  <th class="p-3">Precio</th>
                  <th class="p-3">Lote / Vencimiento</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(d, i) in lotes" :key="i">
                  <td class="p-3 font-bold text-gray-900">{{ d.producto_nombre }}</td>
                  <td class="p-3 text-gray-700">{{ d.cantidad }}</td>
                  <td class="p-3 text-gray-700">${{ Number(d.precio).toFixed(2) }}</td>
                  <td class="p-3">
                    <div class="flex flex-col gap-1.5">
                      <input
                        v-model="d.codigo_lote"
                        class="w-full p-1.5 border border-gray-300 rounded-lg text-[11px] bg-white outline-none focus:ring-2 focus:ring-[#46674A]/20"
                        placeholder="Cód. Lote"
                        :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                      />
                      <input
                        v-model="d.fecha_expiracion"
                        type="date"
                        class="w-full p-1.5 border border-gray-300 rounded-lg text-[11px] bg-white outline-none focus:ring-2 focus:ring-[#46674A]/20"
                        :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- BOTONES -->
          <div class="flex justify-between items-center border-t border-gray-200 pt-4">
            <button
              v-if="esPendiente"
              @click="cancelarCompra"
              :disabled="cargando"
              class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition"
            >
              Anular
            </button>
            <div v-else></div>

            <div class="flex gap-2">
              <button @click="modalCompletar = false" :disabled="cargando" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50">
                Cerrar
              </button>

              <button
                v-if="esPendiente"
                :disabled="cargando"
                @click="completarCompra"
                class="px-5 py-2 bg-[#46674A] hover:bg-[#3b5740] text-white rounded-xl text-xs font-bold shadow-md transition disabled:opacity-50"
              >
                {{ cargando ? 'Procesando...' : 'Completar y Cargar Inventario' }}
              </button>

              <button
                v-else-if="compraSeleccionada?.estado !== 'cancelada'"
                :disabled="cargando"
                @click="guardarCambios"
                class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition"
              >
                {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { getCompras, createCompra, updateCompra } from '../services/compraService'
import { getProductos } from '../services/productoService'

const compras = ref([])
const productos = ref([])

const modal = ref(false)
const modalProductos = ref(false)
const modalCompletar = ref(false)
const cargando = ref(false)

const detalles = ref([])
const lotes = ref([])

const compraSeleccionada = ref(null)
const fechaLlegada = ref('')
const indexProducto = ref(null)
const busqueda = ref('')

const cargar = async () => {
  try {
    const res = await getCompras()
    const dataExtraida = res.data?.data || res.data || res
    compras.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar compras:', err)
    compras.value = []
  }
}

const cargarProductos = async () => {
  try {
    const res = await getProductos()
    const dataExtraida = res.data?.data || res.data || res
    productos.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar productos:', err)
    productos.value = []
  }
}

onMounted(() => {
  cargar()
  cargarProductos()
})

// MODAL NUEVA COMPRA
function abrirModal() {
  modal.value = true
  detalles.value = [{ producto_id: '', cantidad: 1, precio_compra: null }]
}

function add() {
  detalles.value.push({ producto_id: '', cantidad: 1, precio_compra: null })
}

function remove(i) {
  detalles.value.splice(i, 1)
}

function cerrar() {
  modal.value = false
}

// Calculo dinámico del Total General de la Nueva Compra
const totalCompraNueva = computed(() => {
  return detalles.value.reduce((acc, d) => {
    return acc + ((d.cantidad || 0) * (d.precio_compra || 0))
  }, 0).toFixed(2)
})

// SELECTOR DE PRODUCTOS
function abrirSelector(i) {
  indexProducto.value = i
  modalProductos.value = true
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  detalles.value[indexProducto.value].precio_compra = p.precio_compra || p.precio_venta || null
  modalProductos.value = false
}

const productosFiltrados = computed(() => {
  if (!Array.isArray(productos.value)) return []
  const query = busqueda.value.toLowerCase().trim()
  return productos.value.filter(p =>
    (p.nombre || '').toLowerCase().includes(query)
  )
})

function getProductoNombre(id) {
  if (!Array.isArray(productos.value)) return ''
  const prod = productos.value.find(p => String(p.id) === String(id))
  return prod ? prod.nombre : ''
}

// GUARDAR NUEVA COMPRA
async function guardar() {
  if (detalles.value.length === 0) return alert('Debes agregar al menos un producto')
  
  const incomp = detalles.value.some(d => !d.producto_id || !d.cantidad || d.precio_compra === null)
  if (incomp) return alert('Por favor llena el producto, cantidad y precio de compra de cada fila.')

  if (cargando.value) return
  cargando.value = true

  try {
    await createCompra({
      estado: 'pendiente',
      detalles: detalles.value
    })
    await cargar()
    cerrar()
  } catch (error) {
    alert('Error al crear la compra.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

// EDITAR / COMPLETAR
function abrirCompletar(c) {
  compraSeleccionada.value = c

  if (c.fecha_llegada) {
    fechaLlegada.value = c.fecha_llegada.split(' ')[0]
  } else {
    fechaLlegada.value = new Date().toISOString().split('T')[0]
  }

  lotes.value = (c.detalles || []).map(d => {
    const prodLocal = Array.isArray(productos.value) ? productos.value.find(p => String(p.id) === String(d.producto_id)) : null
    const loteExistente = d.lotes && d.lotes.length > 0 ? d.lotes[0] : null

    return {
      detalle_id: d.id,
      producto_id: d.producto_id,
      producto_nombre: d.producto?.nombre || prodLocal?.nombre || 'Producto Desconocido',
      cantidad: d.cantidad,
      precio: d.precio_compra ?? (d.subtotal / d.cantidad),
      codigo_lote: loteExistente ? loteExistente.codigo_lote : '',
      fecha_expiracion: loteExistente ? loteExistente.fecha_expiracion : ''
    }
  })

  modalCompletar.value = true
}

const esPendiente = computed(() => compraSeleccionada.value?.estado === 'pendiente')

function mapearDetallesParaBackend() {
  return lotes.value.map(l => ({
    detalle_id: l.detalle_id,
    codigo_lote: l.codigo_lote || null,
    fecha_expiracion: l.fecha_expiracion || null
  }))
}

async function completarCompra() {
  if (!confirm('¿Finalizar compra y cargar las unidades al inventario?')) return
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: 'completada',
      fecha_llegada: fechaLlegada.value,
      detalles: mapearDetallesParaBackend()
    })
    await cargar()
    await cargarProductos()
    modalCompletar.value = false
  } catch (error) {
    alert('Error al procesar la compra.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

async function guardarCambios() {
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: compraSeleccionada.value.estado,
      fecha_llegada: fechaLlegada.value,
      detalles: mapearDetallesParaBackend()
    })
    await cargar()
    modalCompletar.value = false
  } catch (error) {
    alert('Error al guardar cambios.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

async function cancelarCompra() {
  if (!confirm('¿Seguro que deseas cancelar esta compra?')) return
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: 'cancelada'
    })
    await cargar()
    modalCompletar.value = false
  } catch (error) {
    console.error(error)
  } finally {
    cargando.value = false
  }
}
</script>