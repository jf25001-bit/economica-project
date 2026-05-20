<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Ventas</h1>
        <p class="text-gray-600">
          Consulta las ventas realizadas y registra nuevas ventas.
        </p>
      </div>

      <!-- Botón Nueva Venta -->
      <button
        @click="mostrarNuevaVenta = true"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nueva Venta
      </button>
    </div>

    <!-- Tabla de Ventas Realizadas -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
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
              {{ venta.fecha }}
            </td>
            <td class="px-6 py-4 font-semibold">
              ${{ venta.total }}
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

    <!-- MODAL: VENTA QUE SE ESTÁ REALIZANDO -->
    <div
      v-if="mostrarNuevaVenta"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto"
      >
        <!-- Encabezado del modal -->
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
          <!-- Código de barras -->
          <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">
              Código de Barras
            </label>

            <input
              v-model="codigo"
              @keyup.enter="agregarProducto"
              type="text"
              placeholder="Escanee o escriba el código de barras"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
              autofocus
            />
          </div>

          <!-- Productos de la venta actual -->
          <div class="bg-white border rounded-2xl overflow-hidden mb-6">
            <div class="p-4 border-b bg-gray-50">
              <h3 class="text-xl font-bold text-gray-800">
                Productos Agregados
              </h3>
            </div>

            <table class="w-full">
              <thead class="bg-gray-100">
                <tr class="text-left text-gray-700">
                  <th class="px-4 py-3 font-semibold">Producto</th>
                  <th class="px-4 py-3 font-semibold">Precio</th>
                  <th class="px-4 py-3 font-semibold">Cantidad</th>
                  <th class="px-4 py-3 font-semibold">Subtotal</th>
                  <th class="px-4 py-3 font-semibold">Acciones</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="item in carrito"
                  :key="item.codigo"
                  class="border-t hover:bg-gray-50"
                >
                  <td class="px-4 py-3">{{ item.nombre }}</td>
                  <td class="px-4 py-3">
                    ${{ item.precio.toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">{{ item.cantidad }}</td>
                  <td class="px-4 py-3 font-semibold">
                    ${{ (item.precio * item.cantidad).toFixed(2) }}
                  </td>
                  <td class="px-4 py-3">
                    <button
                      @click="eliminarProducto(item.codigo)"
                      class="text-red-500 hover:text-red-700"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="carrito.length === 0">
                  <td
                    colspan="5"
                    class="text-center py-8 text-gray-500"
                  >
                    No hay productos agregados a esta venta.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Total -->
          <div class="flex justify-end mb-6">
            <div class="bg-gray-100 rounded-xl px-6 py-4 min-w-[220px]">
              <p class="text-gray-600 text-sm">Total de la Venta</p>
              <p class="text-3xl font-bold text-[#46674A]">
                ${{ total.toFixed(2) }}
              </p>
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-3">
            <button
              @click="cerrarModal"
              class="px-5 py-3 border border-gray-300 rounded-xl hover:bg-gray-50"
            >
              Cancelar
            </button>

            <button
              @click="finalizarVenta"
              class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl font-semibold"
            >
              Finalizar Venta
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const mostrarNuevaVenta = ref(false)
const codigo = ref('')
const carrito = ref([])

/* Productos de ejemplo */
const productos = [
  {
    codigo: '7501000123456',
    nombre: 'Coca Cola 600ml',
    precio: 0.75
  },
  {
    codigo: '7501000123457',
    nombre: 'Leche Entera',
    precio: 1.35
  },
  {
    codigo: '7501000123458',
    nombre: 'Pan Bimbo',
    precio: 2.50
  },
  {
    codigo: '7501000123459',
    nombre: 'Arroz 1 lb',
    precio: 0.90
  }
]

/* Ventas realizadas */
const ventas = ref([
  {
    id: 1,
    factura: 'F001',
    cliente: 'Consumidor Final',
    fecha: '20/05/2026',
    total: '12.50'
  },
  {
    id: 2,
    factura: 'F002',
    cliente: 'Juan Pérez',
    fecha: '20/05/2026',
    total: '8.75'
  }
])

function agregarProducto() {
  const producto = productos.find(
    p => p.codigo === codigo.value.trim()
  )

  if (!producto) {
    alert('Producto no encontrado')
    codigo.value = ''
    return
  }

  const existente = carrito.value.find(
    item => item.codigo === producto.codigo
  )

  if (existente) {
    existente.cantidad++
  } else {
    carrito.value.push({
      ...producto,
      cantidad: 1
    })
  }

  codigo.value = ''
}

function eliminarProducto(codigoProducto) {
  carrito.value = carrito.value.filter(
    item => item.codigo !== codigoProducto
  )
}

const total = computed(() => {
  return carrito.value.reduce(
    (suma, item) => suma + item.precio * item.cantidad,
    0
  )
})

function cerrarModal() {
  mostrarNuevaVenta.value = false
  codigo.value = ''
  carrito.value = []
}

function finalizarVenta() {
  if (carrito.value.length === 0) {
    alert('Agrega al menos un producto')
    return
  }

  const numero = ventas.value.length + 1

  ventas.value.unshift({
    id: Date.now(),
    factura: `F${String(numero).padStart(3, '0')}`,
    cliente: 'Consumidor Final',
    fecha: new Date().toLocaleDateString(),
    total: total.value.toFixed(2)
  })

  cerrarModal()
}
</script>