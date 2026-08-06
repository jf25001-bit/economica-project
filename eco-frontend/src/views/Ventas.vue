<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Ventas</h1>
      </div>

      <button
        @click="abrirModalVenta"
        class="bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nueva Venta
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
      <div class="p-6 border-b">
        <h2 class="text-2xl font-bold text-gray-800">
          Ventas Realizadas
        </h2>
      </div>

      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Factura</th>
            <th class="px-6 py-4 font-semibold">Cliente</th>
            <th class="px-6 py-4 font-semibold">Fecha</th>
            <th class="px-6 py-4 font-semibold">Total</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="venta in ventas"
            :key="venta.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-medium">
              {{ venta.factura }}
            </td>
            <td class="px-6 py-4">
              {{ venta.cliente }}
            </td>
            <td class="px-6 py-4">
              {{ formatearFecha(venta.created_at || venta.fecha) }}
            </td>
            <td class="px-6 py-4 font-semibold">
              ${{ Number(venta.total).toFixed(2) }}
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium"
              >
                Completada
              </span>
            </td>
          </tr>

          <tr v-if="ventas.length === 0">
            <td colspan="5" class="text-center py-6 text-gray-500">
              No hay ventas registradas.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-if="mostrarNuevaVenta"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-[18px] shadow-2xl w-full max-w-6xl max-h-[92vh] overflow-hidden flex flex-col border border-gray-200"
      >
        <div class="bg-[#47B5AC] text-white h-[112px] px-6 flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold leading-none">
              Venta en Proceso
            </h2>
            <p class="text-white/80 text-sm mt-3">
              Agregue productos por código de barras o selección manual
            </p>
          </div>

          <button
            @click="cerrarModal"
            class="w-11 h-11 rounded-full bg-[#DDF3F1] text-gray-800 shadow-md hover:bg-white flex items-center justify-center transition"
          >
            <i class="bi bi-x-lg text-xl"></i>
          </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 bg-white">
          
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            
            <div>
              <label class="block text-sm text-gray-700 font-semibold mb-2">
                Nombre del Cliente
              </label>
              <input
                v-model="nombreCliente"
                type="text"
                placeholder="Consumidor Final"
                class="box-border w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
              />
            </div>

            <div>
              <label class="block text-sm text-gray-700 font-semibold mb-2">
                Código de Barras (Escáner)
              </label>
              <input
                v-model="codigoInput"
                @keyup.enter="agregarPorCodigo"
                type="text"
                placeholder="Escanee y presione Enter"
                class="box-border w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
                ref="inputCodigoBarras"
              />
            </div>

            <div>
              <label class="block text-sm text-gray-700 font-semibold mb-2">
                Agregar Manualmente
              </label>
              <select
                v-model="productoManualSeleccionado"
                @change="agregarPorSeleccionManual"
                class="box-border w-full h-11 px-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC] bg-white"
              >
                <option value="">-- Busque o seleccione un producto --</option>
                <option 
                  v-for="prod in productosCatalogo" 
                  :key="prod.id" 
                  :value="prod.id"
                  :disabled="prod.stock <= 0"
                >
                  {{ prod.nombre }} (${{ Number(prod.precio_venta).toFixed(2) }}) - Stock: {{ prod.stock }}
                </option>
              </select>
            </div>

          </div>

          <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mb-6 shadow-sm">
            <div class="p-4 border-b bg-gray-50">
              <h3 class="text-lg font-bold text-gray-800">
                Productos Agregados
              </h3>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] table-fixed">
              <colgroup>
                <col class="w-[16%]" />
                <col class="w-[26%]" />
                <col class="w-[14%]" />
                <col class="w-[14%]" />
                <col class="w-[16%]" />
                <col class="w-[14%]" />
              </colgroup>
              <thead class="bg-gray-100">
                <tr class="text-left text-gray-700">
                  <th class="px-4 py-3 font-semibold">Código</th>
                  <th class="px-4 py-3 font-semibold">Producto</th>
                  <th class="px-4 py-3 font-semibold">Precio</th>
                  <th class="px-4 py-3 font-semibold" style="width: 120px;">Cantidad</th>
                  <th class="px-4 py-3 font-semibold">Subtotal</th>
                  <th class="px-4 py-3 font-semibold">Acciones</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(item, index) in carrito"
                  :key="item.producto_id"
                  class="border-t hover:bg-gray-50"
                >
                  <td class="px-4 py-3 font-mono text-sm text-gray-500">
                    {{ item.codigo_barras || 'N/A' }}
                  </td>
                  <td class="px-4 py-3 truncate">{{ item.nombre }}</td>
                  <td class="px-4 py-3">
                    ${{ Number(item.precio_venta).toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="number"
                      v-model.number="item.cantidad"
                      min="1"
                      :max="item.stock_maximo"
                      @input="validarCantidadInput(index)"
                      class="box-border w-full h-9 px-2 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-[#47B5AC] outline-none"
                    />
                  </td>
                  <td class="px-4 py-3 font-semibold">
                    ${{ (item.precio_venta * item.cantidad).toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">
                    <button
                      @click="eliminarDelCarrito(item.producto_id)"
                    class="w-9 h-9 rounded-lg bg-red-50 text-red-500 hover:text-red-700 hover:bg-red-100 transition"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="carrito.length === 0">
                  <td
                    colspan="6"
                    class="text-center py-16 text-gray-500"
                  >
                    No hay productos agregados a esta venta.
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>

          <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:max-w-2xl">
              <div class="bg-gray-100 rounded-xl px-5 py-4">
                <p class="text-gray-600 text-sm">Total de la Venta</p>
                <p class="text-3xl font-bold text-[#47B5AC]">
                  ${{ totalCalculado.toFixed(2) }}
                </p>
              </div>

              <div class="bg-gray-100 rounded-xl px-5 py-4">
                <label class="block text-gray-600 text-sm mb-2">
                  Efectivo recibido
                </label>
                <input
                  v-model.number="efectivoRecibido"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="0.00"
                  class="box-border w-full h-10 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
                />
              </div>

              <div class="bg-gray-100 rounded-xl px-5 py-4">
                <p class="text-gray-600 text-sm">Cambio a dar</p>
                <p
                  class="text-3xl font-bold"
                  :class="cambioCalculado >= 0 ? 'text-[#47B5AC]' : 'text-red-500'"
                >
                  ${{ cambioCalculado.toFixed(2) }}
                </p>
              </div>
            </div>

          <div class="flex justify-end gap-3">
            <button
              @click="cerrarModal"
              class="h-11 px-6 border border-gray-300 rounded-xl hover:bg-gray-50"
              :disabled="guardandoVenta"
            >
              Cancelar
            </button>

            <button
              @click="finalizarVenta"
              class="h-11 bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-6 rounded-xl font-semibold flex items-center gap-2 disabled:opacity-50"
              :disabled="guardandoVenta || carrito.length === 0"
            >
              <span v-if="guardandoVenta" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
              {{ guardandoVenta ? 'Guardando...' : 'Finalizar Venta' }}
            </button>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'


const ventas = ref([])
const productosCatalogo = ref([])


const mostrarNuevaVenta = ref(false)
const guardandoVenta = ref(false)
const inputCodigoBarras = ref(null)


const nombreCliente = ref('Consumidor Final')
const codigoInput = ref('')
const productoManualSeleccionado = ref('')
const efectivoRecibido = ref(0)
const carrito = ref([])


const consultarBaseDatos = async () => {
  try {
    const [resVentas, resProductos] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/ventas'),
      axios.get('http://127.0.0.1:8000/api/productos')
    ])
    
    ventas.value = resVentas.data.data || resVentas.data || []
    productosCatalogo.value = resProductos.data.data || resProductos.data || []
  } catch (error) {
    console.error('Error cargando los datos del servidor:', error)
  }
}


const abrirModalVenta = () => {
  nombreCliente.value = 'Consumidor Final'
  codigoInput.value = ''
  productoManualSeleccionado.value = ''
  efectivoRecibido.value = 0
  carrito.value = []
  mostrarNuevaVenta.value = true

  
  nextTick(() => {
    if (inputCodigoBarras.value) inputCodigoBarras.value.focus()
  })
}


const totalCalculado = computed(() => {
  return carrito.value.reduce(
    (suma, item) => suma + (item.precio_venta * item.cantidad), 
    0
  )
})

const cambioCalculado = computed(() => {
  return (Number(efectivoRecibido.value) || 0) - totalCalculado.value
})

// Metodos para meter los productos al carrito

const agregarPorCodigo = () => {
  const cod = codigoInput.value.trim()
  if (!cod) return

  
  const producto = productosCatalogo.value.find(p => String(p.codigo_barras) === cod)

  if (!producto) {
    alert(`El código de barras "${cod}" no existe en el sistema.`)
    codigoInput.value = ''
    return
  }

  inyectarProducto(producto)
  codigoInput.value = ''
}


const agregarPorSeleccionManual = () => {
  if (!productoManualSeleccionado.value) return

  const producto = productosCatalogo.value.find(p => p.id === Number(productoManualSeleccionado.value))
  if (producto) {
    inyectarProducto(producto)
  }

 
  productoManualSeleccionado.value = ''
}


const inyectarProducto = (producto) => {
  const stockDisponible = Number(producto.stock)

  if (stockDisponible <= 0) {
    alert(`El producto "${producto.nombre}" está totalmente agotado en el inventario.`)
    return
  }

  const yaExiste = carrito.value.find(item => item.producto_id === producto.id)

  if (yaExiste) {
    if (yaExiste.cantidad >= stockDisponible) {
      alert(`No puedes agregar más. Solo quedan ${stockDisponible} unidades disponibles de "${producto.nombre}".`)
      return
    }
    yaExiste.cantidad++
  } else {
    
    carrito.value.push({
      producto_id: producto.id,
      nombre: producto.nombre,
      codigo_barras: producto.codigo_barras,
      precio_venta: Number(producto.precio_venta),
      cantidad: 1,
      stock_maximo: stockDisponible 
    })
  }
}

// Validar que el usuario no escriba manualmente un número negativo o superior al stock real
const validarCantidadInput = (index) => {
  const item = carrito.value[index]
  if (item.cantidad < 1 || isNaN(item.cantidad)) {
    item.cantidad = 1
  } else if (item.cantidad > item.stock_maximo) {
    alert(`Acción denegada: Has excedido el stock disponible (${item.stock_maximo} unidades).`)
    item.cantidad = item.stock_maximo
  }
}

// Quitar un artículo del carrito
const eliminarDelCarrito = (idProducto) => {
  carrito.value = carrito.value.filter(item => item.producto_id !== idProducto)
}


const cerrarModal = () => {
  mostrarNuevaVenta.value = false
  codigoInput.value = ''
  productoManualSeleccionado.value = ''
  efectivoRecibido.value = 0
  carrito.value = []
}


const finalizarVenta = async () => {
  if (carrito.value.length === 0) {
    alert('Agrega por lo menos un producto para proceder.')
    return
  }

  if (guardandoVenta.value) return
  guardandoVenta.value = true

 
  const datosVenta = {
    cliente: nombreCliente.value.trim() || 'Consumidor Final',
    total: totalCalculado.value,
    productos: carrito.value.map(item => ({
      producto_id: item.producto_id,
      cantidad: item.cantidad,
      precio_venta: item.precio_venta
    }))
  }

  try {
    
    await axios.post('http://127.0.0.1:8000/api/ventas', datosVenta)
    
    await Swal.fire({
      icon: 'success',
      title: 'Venta realizada',
      showConfirmButton: false,
      timer: 1400,
      confirmButtonColor: '#47B5AC'
    })
    cerrarModal()
    await consultarBaseDatos() 
  } catch (error) {
    console.error(error)
    const mensajeError = error.response?.data?.error || error.response?.data?.message || 'Error desconocido'
    alert('Hubo un inconveniente al procesar la venta: ' + mensajeError)
  } finally {
    guardandoVenta.value = false
  }
}


const formatearFecha = (fechaRaw) => {
  if (!fechaRaw) return 'N/A'
  const objFecha = new Date(fechaRaw)
  return isNaN(objFecha.getTime()) ? fechaRaw : objFecha.toLocaleDateString()
}


onMounted(() => {
  consultarBaseDatos()
})
</script>
