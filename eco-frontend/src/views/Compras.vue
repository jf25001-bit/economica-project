<template>
  <div class="p-6">

    <div class="flex justify-between mb-6">
      <h1 class="text-3xl font-bold">Compras</h1>

      <button
        @click="abrirModal"
        class="bg-green-700 text-white px-4 py-2 rounded"
      >
        Nueva Compra
      </button>
    </div>

    <table class="w-full bg-white shadow rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Total</th>
          <th class="p-3 text-left">Estado</th>
          <th class="p-3 text-left">Fecha</th>
          <th class="p-3 text-left">Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="c in compras" :key="c.id" class="border-b">

          <td class="p-3">{{ c.id }}</td>
          <td class="p-3">${{ Number(c.total ?? 0).toFixed(2) }}</td>

          <td class="p-3">
            <span
              :class="{
                'text-yellow-600 font-bold': c.estado === 'pendiente',
                'text-green-600 font-bold': c.estado === 'completada',
                'text-red-600 font-bold': c.estado === 'cancelada'
              }"
            >
              {{ c.estado }}
            </span>
          </td>

          <td class="p-3">{{ c.fecha_compra ?? '---' }}</td>

          <td class="p-3">
            <button
              @click="abrirCompletar(c)"
              class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
            >
              Ver / Editar
            </button>
          </td>

        </tr>

        <tr v-if="compras.length === 0">
          <td colspan="5" class="text-center py-4">
            No hay compras
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="modal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

      <div class="bg-white p-6 w-[650px] rounded shadow-lg">

        <h2 class="text-xl font-bold mb-4">Nueva Compra</h2>

        <div class="max-h-[350px] overflow-y-auto mb-4">

          <div
            v-for="(d, i) in detalles"
            :key="i"
            class="mb-4 border p-3 rounded relative bg-white shadow-sm"
          >

            <div
              class="border p-2 mb-2 bg-gray-50 rounded cursor-pointer hover:bg-gray-100"
              @click="abrirSelector(i)"
            >
              {{ getProductoNombre(d.producto_id) || 'Seleccionar producto 🔍' }}
            </div>

            <p class="text-sm text-gray-600 mb-2">
              Precio Compra: ${{ getProductoPrecio(d.producto_id) || '0.00' }}
            </p>

            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold">Cantidad:</label>
              <input
                v-model="d.cantidad"
                type="number"
                min="1"
                class="w-full border p-2 rounded"
              />
            </div>

            <button @click="remove(i)" class="text-red-600 text-sm mt-2 block font-medium">
              Quitar producto
            </button>

          </div>

        </div>

        <button @click="add" class="bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded text-sm font-medium">
          + Agregar producto
        </button>

        <div class="flex justify-end gap-2 mt-4">
          <button @click="cerrar" :disabled="cargando" class="px-4 py-2 border rounded hover:bg-gray-50 disabled:opacity-50">
            Cancelar
          </button>
          <button 
            @click="guardar" 
            :disabled="cargando"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ cargando ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>

      </div>

    </div>

    <div v-if="modalProductos" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

      <div class="bg-white p-6 w-[600px] rounded shadow-lg">

        <h2 class="text-xl font-bold mb-4">Seleccionar producto</h2>

        <input
          v-model="busqueda"
          class="w-full border p-2 mb-4 rounded"
          placeholder="Buscar producto por nombre..."
        />

        <div class="max-h-[300px] overflow-y-auto">

          <div
            v-for="p in productosFiltrados"
            :key="p.id"
            @click="seleccionarProducto(p)"
            class="p-3 border mb-2 cursor-pointer hover:bg-gray-100 rounded"
          >
            <div class="font-semibold">{{ p.nombre }}</div>
            <div class="text-sm text-gray-500">Precio Compra: ${{ p.precio_compra }}</div>
          </div>

        </div>

        <div class="flex justify-end mt-4">
          <button @click="modalProductos=false" class="px-4 py-2 border rounded hover:bg-gray-50">
            Cerrar
          </button>
        </div>

      </div>

    </div>

    <div v-if="modalCompletar" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

      <div class="bg-white p-6 w-[750px] rounded shadow-lg">

        <h2 class="text-xl font-bold mb-2">Detalles de compra #{{ compraSeleccionada?.id }}</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div class="p-3 border rounded bg-blue-50/50 flex flex-col gap-1 w-full">
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wide">
              Fecha de llegada de la compra:
            </label>
            <input
              v-model="fechaLlegada"
              type="date"
              class="border p-2 rounded text-sm bg-white text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-75 disabled:bg-gray-100"
              :disabled="!esPendiente || cargando"
            />
          </div>

          <div 
            v-if="compraSeleccionada?.estado !== 'cancelada'" 
            class="p-3 border rounded bg-gray-50 flex flex-col gap-1 w-full"
          >
            <label class="text-xs font-bold text-gray-700 uppercase tracking-wide">
              Documento de facturación
            </label>
            <input 
              type="file" 
              class="border p-1.5 rounded text-sm bg-white text-gray-800 shadow-sm focus:outline-none file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300" 
              :disabled="cargando"
            />
          </div>
        </div>

        <div class="max-h-[300px] overflow-y-auto border rounded mb-4">
          
          <div class="grid grid-cols-4 gap-2 p-2 bg-gray-100 font-semibold text-xs border-b sticky top-0">
            <div>Producto</div>
            <div>Cantidad en unidades</div>
            <div>Precio Compra.</div>
            <div>Información de Lote / Vencimiento</div>
          </div>

          <div
            v-for="(d, i) in lotes"
            :key="i"
            class="grid grid-cols-4 gap-2 p-3 border-b text-sm items-center"
          >

            <div class="font-semibold text-gray-800">
              {{ d.producto_nombre }}
            </div>

            <div class="text-gray-600">
              {{ d.cantidad }}
            </div>

            <div class="text-gray-600">
              ${{ Number(d.precio).toFixed(2) }}
            </div>

            <div class="flex flex-col gap-1">
              <input
                v-model="d.codigo_lote"
                class="border p-1 text-xs w-full rounded text-gray-700 disabled:opacity-75 disabled:bg-gray-100"
                placeholder="Código de Lote"
                :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
              />

              <input
                v-model="d.fecha_expiracion"
                type="date"
                class="border p-1 text-xs w-full rounded text-gray-700 disabled:opacity-75 disabled:bg-gray-100"
                :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
              />
            </div>

          </div>

        </div>

        <div class="flex justify-between mt-4">

          <button
            v-if="esPendiente"
            @click="cancelarCompra"
            :disabled="cargando"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded font-medium disabled:opacity-50"
          >
            Cancelar compra
          </button>
          <div v-else></div>

          <div class="flex gap-2">

            <button @click="modalCompletar=false" :disabled="cargando" class="px-4 py-2 border rounded hover:bg-gray-50 font-medium disabled:opacity-50">
              Cerrar
            </button>

            <button
              v-if="esPendiente"
              :disabled="!puedeFinalizar || cargando"
              @click="completarCompra"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed font-medium"
            >
              {{ cargando ? 'Procesando...' : 'Finalizar compra' }}
            </button>

            <button
              v-else-if="compraSeleccionada?.estado !== 'cancelada'"
              :disabled="cargando"
              @click="guardarCambios"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ cargando ? 'Guardando...' : 'Guardar cambios' }}
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

// Estado para bloquear dobles envíos
const cargando = ref(false)

const detalles = ref([])
const lotes = ref([])

const compraSeleccionada = ref(null)
const fechaLlegada = ref('')
const indexProducto = ref(null)
const busqueda = ref('')

/* LOAD */
const cargar = async () => {
  compras.value = await getCompras()
}

const cargarProductos = async () => {
  productos.value = await getProductos()
}

onMounted(() => {
  cargar()
  cargarProductos()
})

/* NUEVA */
function abrirModal() {
  modal.value = true
  detalles.value = []
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

/* PRODUCTOS */
function abrirSelector(i) {
  indexProducto.value = i
  modalProductos.value = true
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  detalles.value[indexProducto.value].precio_compra = p.precio_compra
  modalProductos.value = false
}

const productosFiltrados = computed(() =>
  productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
)

function getProductoNombre(id) {
  return productos.value.find(p => p.id === id)?.nombre
}

function getProductoPrecio(id) {
  return productos.value.find(p => p.id === id)?.precio_compra
}

/* GUARDAR NUEVA COMPRA */
async function guardar() {
  if (detalles.value.length === 0) return alert('Debes agregar al menos un producto')
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

/* EDITAR / VER DETALLES */
function abrirCompletar(c) {
  compraSeleccionada.value = c

  if (c.fecha_llegada) {
    fechaLlegada.value = c.fecha_llegada.split(' ')[0]
  } else {
    fechaLlegada.value = new Date().toISOString().split('T')[0]
  }

  lotes.value = (c.detalles || []).map(d => {
    const prodLocal = productos.value.find(p => p.id === d.producto_id)
    const loteExistente = d.lotes && d.lotes.length > 0 ? d.lotes[0] : null

    return {
      detalle_id: d.id, 
      producto_id: d.producto_id,
      producto_nombre: d.producto?.nombre || prodLocal?.nombre || 'Producto Desconocido',
      cantidad: d.cantidad,
      precio: d.precio_compra ?? d.subtotal / d.cantidad,
      codigo_lote: loteExistente ? loteExistente.codigo_lote : '',
      fecha_expiracion: loteExistente ? loteExistente.fecha_expiracion : ''
    }
  })

  modalCompletar.value = true
}

const esPendiente = computed(() =>
  compraSeleccionada.value?.estado === 'pendiente'
)

const puedeFinalizar = computed(() =>
  lotes.value.length > 0 && 
  fechaLlegada.value !== '' && 
  lotes.value.every(l => l.codigo_lote.trim() !== '' && l.fecha_expiracion !== '')
)

/**
 * Función auxiliar para formatear los lotes.
 */
function mapearDetallesParaBackend() {
  return lotes.value.map(l => ({
    detalle_id: l.detalle_id,
    codigo_lote: l.codigo_lote || null,
    fecha_expiracion: l.fecha_expiracion || null
  }))
}

/* PEDIR CONFIRMACIÓN PARA FINALIZAR */
async function completarCompra() {
  const confirmar = confirm('¿Finalizar compra y cargar stock?.')
  if (!confirmar) return
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: 'completada',
      fecha_llegada: fechaLlegada.value,
      detalles: mapearDetallesParaBackend()
    })
    await cargar()
    modalCompletar.value = false
  } catch (error) {
    alert('Error al guardar en el servidor. Inténtalo de nuevo.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

/* GUARDAR CAMBIOS */
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
    alert('Error al guardar en el servidor. Inténtalo de nuevo.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

/* CANCELAR COMPRA */
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