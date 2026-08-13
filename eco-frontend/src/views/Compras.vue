<template>
  <div class="main-interface-container p-4 lg:p-6 font-sans text-slate-800 bg-slate-100 min-h-screen">
    <div class="w-full max-w-5xl mx-auto space-y-6">
      
      <!-- NAVBAR SUPERIOR -->
      <div class="top-strict-navbar flex items-center justify-between bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200">
            <i class="bi bi-cart-check-fill text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-800">Módulo de Compras</h1>
            <p class="text-xs text-slate-500">Control de entradas, recepción de órdenes e inventario</p>
          </div>
        </div>
        
        <div class="top-right-actions flex items-center gap-4">
          <!-- BOTÓN CAMBIADO A AZUL/INDIGO -->
          <button 
            @click="abrirModalCrear"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow transition font-semibold text-sm flex items-center gap-2 cursor-pointer"
          >
            <i class="bi bi-plus-lg"></i>
            Nueva Compra
          </button>
        </div>
      </div>

      <!-- TABLA PRINCIPAL DE COMPRAS -->
      <div class="content-layout-flex flex flex-col xl:flex-row gap-5 items-start w-full">
        <div class="left-content-panel w-full xl:flex-1 xl:min-w-0 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 min-h-[calc(100vh-13rem)] flex flex-col">
          <div class="section-header-row flex justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-slate-800">Órdenes de Compra</h2>
              <p class="text-sm text-slate-500">Listado general ({{ compras.length }} registros)</p>
            </div>
          </div>
          <div class="table-card-wrapper border border-slate-200 rounded-xl overflow-hidden flex-1 bg-white">
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
                  <tr v-for="c in compras" :key="c.id" class="hover:bg-slate-50/80 text-sm transition">
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">#{{ c.id }}</td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ c.fecha_compra ?? '—' }}</td>
                    <td class="px-6 py-4 font-bold text-emerald-600">${{ Number(c.total ?? 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-right">
                      <button 
                        @click="abrirEditar(c)"
                        class="bg-slate-100 text-slate-700 p-2 rounded-lg hover:bg-slate-200 transition cursor-pointer inline-flex items-center justify-center border border-slate-300" 
                        title="Editar Compra"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="compras.length === 0">
                    <td colspan="4" class="text-center py-20 text-slate-400 italic">
                      No hay compras registradas.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- WIDGET LATERAL -->
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

    <!-- MODAL REGISTRAR / EDITAR COMPRA -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4">
      <div class="modal-card-box bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden">
        
        <!-- ENCABEZADO MODAL -->
        <div class="px-5 py-4 flex justify-between items-center border-b border-slate-200 bg-slate-50 shrink-0">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0">
              <i :class="modoEdicion ? 'bi bi-pencil-square' : 'bi bi-bag-plus-fill'" class="text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-slate-800 text-base">
                {{ modoEdicion ? `Editar Orden de Compra #${compraIdEdicion}` : 'Registrar Nueva Orden de Compra' }}
              </h3>
              <p class="text-xs text-slate-500">Detalla los ítems, precios y datos de lote correspondientes</p>
            </div>
          </div>
          <button class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer shrink-0" @click="cerrar">
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>

        <!-- CUERPO DEL MODAL -->
        <div class="modal-body-scroll p-4 sm:p-5 overflow-y-auto overflow-x-hidden flex-1 space-y-5 bg-slate-50/50">
          
          <!-- FECHA Y MONTO TOTAL ESTIMADO -->
          <div class="form-row-single space-y-3">
            <div class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm">
              <label class="text-xs font-bold text-slate-600 uppercase tracking-wide block mb-1.5">Fecha de Compra</label>
              <input
                v-model="fechaCompraNueva"
                type="date"
                class="form-force-input px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white text-slate-800 outline-none focus:border-emerald-500 font-medium"
              />
            </div>
            <div class="p-3.5 border border-slate-200 rounded-xl bg-white shadow-sm flex items-center justify-between">
              <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Monto Total Estimado</span>
              <span class="text-2xl font-black text-emerald-600">${{ totalCompraNueva }}</span>
            </div>
          </div>

          <!-- LISTA DE PRODUCTOS -->
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

              <!-- SELECCIONAR PRODUCTO -->
              <div class="field-block">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Producto</label>
                <button
                  type="button"
                  @click="abrirSelector(i)"
                  class="form-force-button h-10 flex items-center justify-between px-3 rounded-lg border border-slate-300 bg-white hover:border-emerald-500 text-slate-800 text-left cursor-pointer shadow-sm"
                >
                  <span class="truncate text-sm font-medium">
                    {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                  </span>
                  <i class="bi bi-search text-slate-400 text-xs shrink-0 ml-2"></i>
                </button>
              </div>

              <!-- CANTIDAD Y UNIDADES POR PAQUETE -->
              <div class="field-grid-2">
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Cant. Paquetes</label>
                  <input
                    v-model.number="d.cantidad"
                    type="number"
                    min="1"
                    placeholder="1"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Unid. por Paquete</label>
                  <input
                    v-model.number="d.unidades_por_paquete"
                    type="number"
                    min="1"
                    placeholder="1"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500"
                  />
                </div>
              </div>

              <!-- PRECIO PAQUETE -->
              <div class="field-block">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Precio Paquete ($)</label>
                <div class="relative w-full">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                  <input
                    v-model.number="d.precio_compra"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="form-force-input h-10 pl-7 pr-3 border border-slate-300 rounded-lg text-sm font-bold text-emerald-600 outline-none focus:border-emerald-500"
                  />
                </div>
              </div>

              <!-- CÓDIGO DE LOTE Y EXPIRACIÓN -->
              <div class="field-grid-2 pt-2 border-t border-slate-100">
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Código de Lote</label>
                  <input
                    v-model="d.codigo_lote"
                    type="text"
                    placeholder="Ej: LOTE-123"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-800 outline-none focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 block mb-1">Fecha Expiración</label>
                  <input
                    v-model="d.fecha_expiracion"
                    type="date"
                    class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-700 outline-none focus:border-emerald-500"
                  />
                </div>
              </div>

              <!-- SUBTOTAL Y ELIMINAR -->
              <div class="pt-3 border-t border-slate-200 flex items-center justify-between bg-slate-50 -mx-4 -mb-4 p-3.5 rounded-b-xl">
                <div>
                  <span class="text-[10px] text-slate-400 font-bold uppercase block tracking-wider">Subtotal Ítem</span>
                  <span class="text-lg font-black text-slate-800">
                    ${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}
                  </span>
                </div>

                <button 
                  type="button"
                  @click="remove(i)" 
                  class="h-9 px-3.5 inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition cursor-pointer text-xs font-bold"
                >
                  <i class="bi bi-trash text-sm"></i>
                  <span>Eliminar</span>
                </button>
              </div>

            </div>
          </div>

          <!-- BOTÓN AGREGAR OTRO PRODUCTO -->
          <button
            @click="add"
            type="button"
            class="w-full py-3.5 rounded-xl border-2 border-dashed border-slate-300 bg-white hover:bg-emerald-50/50 hover:border-emerald-400 text-xs font-bold text-slate-700 transition flex items-center justify-center gap-2 cursor-pointer shadow-sm"
          >
            <i class="bi bi-plus-circle-fill text-base text-emerald-600"></i> Agregar otro producto
          </button>
        </div>

        <!-- FOOTER MODAL -->
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
              class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow transition flex items-center gap-1.5 cursor-pointer"
            >
              <i v-if="!cargando" class="bi bi-check-lg text-sm"></i>
              {{ cargando ? 'Guardando...' : (modoEdicion ? 'Actualizar Compra' : 'Guardar Compra') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL SELECTOR DE PRODUCTO -->
    <div v-if="modalProductos" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 flex justify-between items-center bg-slate-50">
          <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Catálogo de Productos</h3>
          <button class="text-slate-400 hover:text-slate-700 cursor-pointer" @click="modalProductos = false">
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>
        <div class="p-4 bg-white">
          <div class="relative mb-3">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
              v-model="busqueda"
              class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 outline-none focus:border-emerald-500"
              placeholder="Buscar por nombre..."
            />
          </div>
          <div class="max-h-[260px] overflow-y-auto space-y-1.5 pr-1">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              @click="seleccionarProducto(p)"
              class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/50 transition flex justify-between items-center"
            >
              <div>
                <p class="font-bold text-slate-800 text-xs">{{ p.nombre }}</p>
                <p class="text-[10px] text-slate-500">Unidad: {{ p.unidad_medida?.nombre || 'pieza' }}</p>
              </div>
              <span class="text-[10px] bg-white text-slate-700 font-semibold px-2 py-0.5 rounded-md border border-slate-200">
                Stock: {{ p.stock ?? 0 }}
              </span>
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
const cargando = ref(false)
const detalles = ref([])
const indexProducto = ref(null)
const busqueda = ref('')
const fechaCompraNueva = ref(new Date().toISOString().split('T')[0])

const modoEdicion = ref(false)
const compraIdEdicion = ref(null)

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

onMounted(() => {
  cargar()
  cargarProductos()
})

function abrirModalCrear() {
  modoEdicion.value = false
  compraIdEdicion.value = null
  fechaCompraNueva.value = new Date().toISOString().split('T')[0]
  detalles.value = [{ 
    producto_id: '', 
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
  modal.value = false
}

const totalCompraNueva = computed(() => {
  return detalles.value.reduce((acc, d) => {
    return acc + ((d.cantidad || 0) * (d.precio_compra || 0))
  }, 0).toFixed(2)
})

function abrirSelector(i) {
  indexProducto.value = i
  modalProductos.value = true
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  modalProductos.value = false
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

async function guardar() {
  if (detalles.value.length === 0) {
    return alert('Debes agregar al menos un producto')
  }

  const incompleto = detalles.value.some(
    d => !d.producto_id || !d.cantidad || d.precio_compra === null || d.precio_compra < 0
  )
  if (incompleto) {
    return alert('Por favor selecciona un producto, asigna la cantidad y un precio válido.')
  }

  cargando.value = true
  try {
    const payload = {
      fecha_compra: fechaCompraNueva.value,
      detalles: detalles.value.map(d => ({
        id: d.detalle_id || d.id || undefined,
        detalle_id: d.detalle_id || d.id || undefined,
        producto_id: Number(d.producto_id),
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