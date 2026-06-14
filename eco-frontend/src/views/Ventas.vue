<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Ventas</h1>
      </div>

      <button
        @click="abrirModalVenta"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
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
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto"
      >
        <div class="flex items-center justify-between p-6 border-b">
          <h2 class="text-2xl font-bold text-gray-800">
            Venta en Proceso
          </h2>

          <button
            @click="cerrarModal"
            class="text-gray-500 hover:text-gray-700 text-3xl"
          >
            &times;
          </button>
        </div>

        <div class="p-6">
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <div>
              <label class="block text-gray-700 font-medium mb-2">
                Nombre del Cliente
              </label>
              <input
                v-model="nombreCliente"
                type="text"
                placeholder="Consumidor Final"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">
                Código de Barras (Escáner)
              </label>
              <input
                v-model="codigoInput"
                @keyup.enter="agregarPorCodigo"
                type="text"
                placeholder="Escanee y presione Enter"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
                ref="inputCodigoBarras"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">
                Agregar Manualmente
              </label>
              <select
                v-model="productoManualSeleccionado"
                @change="agregarPorSeleccionManual"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A] bg-white"
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

          <div class="bg-white border rounded-2xl overflow-hidden mb-6">
            <div class="p-4 border-b bg-gray-50">
              <h3 class="text-xl font-bold text-gray-800">
                Productos Agregados
              </h3>
            </div>

            <table class="w-full">
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
                  <td class="px-4 py-3">{{ item.nombre }}</td>
                  <td class="px-4 py-3">
                    ${{ Number(item.precio_venta).toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">
                    <input 
                      type="number"
                      v-model.number="item.cantidad"
                      min="1"
                      :max="item.stock_maximo"
                      @id="validarCantidadInput(index)"
                      class="w-full px-2 py-1 border rounded-lg text-center focus:ring-2 focus:ring-[#46674A] outline-none"
                    />
                  </td>
                  <td class="px-4 py-3 font-semibold">
                    ${{ (item.precio_venta * item.cantidad).toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">
                    <button
                      @click="eliminarDelCarrito(item.producto_id)"
                      class="text-red-500 hover:text-red-700"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="carrito.length === 0">
                  <td
                    colspan="6"
                    class="text-center py-8 text-gray-500"
                  >
                    No hay productos agregados a esta venta.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end mb-6">
            <div class="bg-gray-100 rounded-xl px-6 py-4 min-w-[220px]">
              <p class="text-gray-600 text-sm">Total de la Venta</p>
              <p class="text-3xl font-bold text-[#46674A]">
                ${{ totalCalculado.toFixed(2) }}
              </p>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              @click="cerrarModal"
              class="px-5 py-3 border border-gray-300 rounded-xl hover:bg-gray-50"
              :disabled="guardandoVenta"
            >
              Cancelar
            </button>

            <button
              @click="finalizarVenta"
              class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl font-semibold flex items-center gap-2 disabled:opacity-50"
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
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'


const ventas = ref([])
const productosCatalogo = ref([])


const mostrarNuevaVenta = ref(false)
const guardandoVenta = ref(false)
const inputCodigoBarras = ref(null)


const nombreCliente = ref('Consumidor Final')
const codigoInput = ref('')
const productoManualSeleccionado = ref('')
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
    
    alert('Venta realizada')
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