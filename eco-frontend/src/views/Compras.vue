<template>
  <div class="p-6">

    <div class="flex justify-between mb-6">
      <h1 class="text-3xl font-bold">Compras</h1>

      <button
        @click="abrirModal"
        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 font-medium"
      >
        + Nueva Compra
      </button>
    </div>

    <!-- TABLA DE COMPRAS -->
    <table class="w-full bg-white shadow rounded">
      <thead class="bg-gray-100 border-b">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Total</th>
          <th class="p-3 text-left">Estado</th>
          <th class="p-3 text-left">Fecha Compra</th>
          <th class="p-3 text-left">Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="c in compras" :key="c.id" class="border-b hover:bg-gray-50">
          <td class="p-3 font-semibold">#{{ c.id }}</td>
          <td class="p-3 font-bold text-gray-800">${{ Number(c.total ?? 0).toFixed(2) }}</td>

          <td class="p-3">
            <span
              :class="{
                'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold': c.estado === 'pendiente',
                'bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold': c.estado === 'completada',
                'bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold': c.estado === 'cancelada'
              }"
            >
              {{ (c.estado || '').toUpperCase() }}
            </span>
          </td>

          <td class="p-3 text-gray-600">{{ c.fecha_compra ?? '---' }}</td>

          <td class="p-3">
            <button
              @click="abrirCompletar(c)"
              class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm font-medium"
            >
              Ver / Editar
            </button>
          </td>
        </tr>

        <tr v-if="compras.length === 0">
          <td colspan="5" class="text-center py-6 text-gray-500">
            No hay compras registradas
          </td>
        </tr>
      </tbody>
    </table>

    <!-- MODAL 1: NUEVA COMPRA -->
    <div v-if="modal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white p-6 w-[650px] rounded-lg shadow-xl">
        <h2 class="text-xl font-bold mb-4">Nueva Orden de Compra</h2>

        <div class="max-h-[380px] overflow-y-auto mb-4 pr-1">
          <div
            v-for="(d, i) in detalles"
            :key="i"
            class="mb-3 border p-3 rounded-lg relative bg-gray-50 shadow-sm"
          >
            <!-- Selector de producto -->
            <div
              class="border p-2 mb-3 bg-white rounded cursor-pointer hover:bg-gray-100 font-medium text-gray-800 flex justify-between items-center"
              @click="abrirSelector(i)"
            >
              <span>{{ getProductoNombre(d.producto_id) || 'Seleccionar producto 🔍' }}</span>
              <span class="text-xs text-blue-600 font-semibold">Cambiar</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <!-- Cantidad -->
              <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Cantidad:</label>
                <input
                  v-model.number="d.cantidad"
                  type="number"
                  min="1"
                  class="w-full border p-2 rounded bg-white"
                />
              </div>

              <!-- Precio de Compra Editable -->
              <div>
                <label class="text-xs font-semibold text-gray-600 mb-1 block">Precio Compra ($):</label>
                <input
                  v-model.number="d.precio_compra"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="w-full border p-2 rounded bg-white font-medium text-green-700"
                />
              </div>
            </div>

            <div class="flex justify-between items-center mt-3 pt-2 border-t">
              <span class="text-xs text-gray-500">
                Subtotal: <strong>${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}</strong>
              </span>
              <button @click="remove(i)" class="text-red-600 text-xs font-semibold hover:underline">
                Quitar producto
              </button>
            </div>
          </div>
        </div>

        <button @click="add" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded text-sm font-medium w-full mb-4">
          + Agregar producto
        </button>

        <div class="flex justify-end gap-2 border-t pt-3">
          <button @click="cerrar" :disabled="cargando" class="px-4 py-2 border rounded hover:bg-gray-50 text-sm">
            Cancelar
          </button>
          <button 
            @click="guardar" 
            :disabled="cargando"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium disabled:opacity-50"
          >
            {{ cargando ? 'Guardando...' : 'Guardar Compra' }}
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL 2: SELECTOR DE PRODUCTOS -->
    <div v-if="modalProductos" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white p-6 w-[550px] rounded-lg shadow-xl">
        <h2 class="text-xl font-bold mb-4">Seleccionar Producto</h2>

        <input
          v-model="busqueda"
          class="w-full border p-2 mb-4 rounded focus:ring-2 focus:ring-blue-500 outline-none"
          placeholder="Buscar por nombre..."
        />

        <div class="max-h-[300px] overflow-y-auto">
          <div
            v-for="p in productosFiltrados"
            :key="p.id"
            @click="seleccionarProducto(p)"
            class="p-3 border mb-2 cursor-pointer hover:bg-blue-50 rounded transition flex justify-between items-center"
          >
            <div>
              <div class="font-semibold text-gray-800">{{ p.nombre }}</div>
              <div class="text-xs text-gray-500">Unidad: {{ p.unidad_medida?.nombre || p.unidad_medida || 'pieza' }}</div>
            </div>
            <span class="text-xs bg-gray-200 px-2 py-1 rounded">Stock: {{ p.stock ?? 0 }}</span>
          </div>

          <p v-if="productosFiltrados.length === 0" class="text-center text-gray-500 py-4">
            No se encontraron productos.
          </p>
        </div>

        <div class="flex justify-end mt-4">
          <button @click="modalProductos = false" class="px-4 py-2 border rounded hover:bg-gray-50 text-sm">
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL 3: COMPLETAR / VER DETALLES -->
    <div v-if="modalCompletar" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white p-6 w-[750px] rounded-lg shadow-xl">
        <h2 class="text-xl font-bold mb-3">Detalles de Compra #{{ compraSeleccionada?.id }}</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div class="p-3 border rounded bg-blue-50/50 flex flex-col gap-1">
            <label class="text-xs font-bold text-gray-700 uppercase">
              Fecha de Recepción:
            </label>
            <input
              v-model="fechaLlegada"
              type="date"
              class="border p-2 rounded text-sm bg-white"
              :disabled="!esPendiente || cargando"
            />
          </div>

          <div class="p-3 border rounded bg-gray-50 flex flex-col justify-center">
            <span class="text-xs text-gray-500">Total de esta compra:</span>
            <span class="text-xl font-bold text-gray-800">
              ${{ Number(compraSeleccionada?.total ?? 0).toFixed(2) }}
            </span>
          </div>
        </div>

        <!-- TABLA DE DETALLES Y LOTES -->
        <div class="max-h-[300px] overflow-y-auto border rounded mb-4">
          <div class="grid grid-cols-4 gap-2 p-2 bg-gray-100 font-semibold text-xs border-b sticky top-0">
            <div>Producto</div>
            <div>Cantidad</div>
            <div>Precio Compra</div>
            <div>Lote / Vencimiento (Opcional)</div>
          </div>

          <div
            v-for="(d, i) in lotes"
            :key="i"
            class="grid grid-cols-4 gap-2 p-3 border-b text-sm items-center"
          >
            <div class="font-semibold text-gray-800">{{ d.producto_nombre }}</div>
            <div class="text-gray-600">{{ d.cantidad }}</div>
            <div class="text-gray-600">${{ Number(d.precio).toFixed(2) }}</div>

            <div class="flex flex-col gap-1">
              <input
                v-model="d.codigo_lote"
                class="border p-1 text-xs w-full rounded"
                placeholder="Código Lote (Opcional)"
                :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
              />
              <input
                v-model="d.fecha_expiracion"
                type="date"
                class="border p-1 text-xs w-full rounded"
                :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
              />
            </div>
          </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="flex justify-between mt-4 border-t pt-3">
          <button
            v-if="esPendiente"
            @click="cancelarCompra"
            :disabled="cargando"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium"
          >
            Cancelar Compra
          </button>
          <div v-else></div>

          <div class="flex gap-2">
            <button @click="modalCompletar = false" :disabled="cargando" class="px-4 py-2 border rounded text-sm">
              Cerrar
            </button>

            <button
              v-if="esPendiente"
              :disabled="cargando"
              @click="completarCompra"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium"
            >
              {{ cargando ? 'Procesando...' : 'Finalizar y Cargar Stock' }}
            </button>

            <button
              v-else-if="compraSeleccionada?.estado !== 'cancelada'"
              :disabled="cargando"
              @click="guardarCambios"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium"
            >
              {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
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

// SELECTOR DE PRODUCTOS
function abrirSelector(i) {
  indexProducto.value = i
  modalProductos.value = true
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  // Si el objeto tiene un precio sugerido lo asigna, sino lo deja libre
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
    await cargarProductos() // Actualiza inventario local
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