<template>
  <div class="p-6 space-y-4">

    <h1 class="text-3xl font-bold"> Ventas (POS)</h1>
<p>sesión de ventas</p>
    <!-- FORMULARIO -->
    <div class="bg-white p-4 rounded-2xl shadow space-y-4">

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <input v-model="cliente"
          placeholder="Cliente"
          class="border p-3 rounded-xl"
        />

        <input v-model="codigo"
          placeholder="Código"
          @keyup.enter="buscarProducto"
          class="border p-3 rounded-xl"
        />

        <input v-model="producto.nombre"
          placeholder="Producto"
          disabled
          class="border p-3 rounded-xl bg-gray-100"
        />

        <input v-model="producto.precio"
          placeholder="Precio"
          disabled
          class="border p-3 rounded-xl bg-gray-100"
        />

      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        <input v-model.number="cantidad"
          type="number"
          min="1"
          class="border p-3 rounded-xl"
          placeholder="Cantidad"
        />

        <button
          @click="agregarProducto"
          class="bg-[#46674A] text-white rounded-xl py-3"
        >
          Agregar
        </button>

        <button
          @click="limpiar"
          class="border rounded-xl py-3"
        >
          Limpiar
        </button>

      </div>

    </div>

    <!-- TABLA -->
    <div class="bg-white p-4 rounded-2xl shadow">

      <table class="w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2">Producto</th>
            <th class="p-2">Precio</th>
            <th class="p-2">Cantidad</th>
            <th class="p-2">Subtotal</th>
            <th class="p-2">Acción</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="item in carrito" :key="item.codigo" class="border-t">
            <td class="p-2">{{ item.nombre }}</td>
            <td class="p-2">${{ item.precio }}</td>
            <td class="p-2">{{ item.cantidad }}</td>
            <td class="p-2 font-bold">
              ${{ (item.precio * item.cantidad).toFixed(2) }}
            </td>
            <td class="p-2">
              <button @click="eliminar(item.codigo)" class="text-red-500">
                🗑
              </button>
            </td>
          </tr>

          <tr v-if="carrito.length === 0">
            <td colspan="5" class="text-center py-4 text-gray-500">
              Sin productos
            </td>
          </tr>
        </tbody>
      </table>

    </div>

    <!-- TOTAL + ACCIONES -->
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow">

      <div>
        <p>Total</p>
        <p class="text-3xl font-bold text-green-600">
          ${{ total.toFixed(2) }}
        </p>
      </div>

      <div class="flex gap-3">

        <button
          @click="finalizarVenta"
          class="bg-green-600 text-white px-4 py-2 rounded-xl"
        >
          Guardar Venta
        </button>

        <button
          @click="limpiar"
          class="bg-red-500 text-white px-4 py-2 rounded-xl"
        >
          Cancelar
        </button>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const cliente = ref('')
const codigo = ref('')
const cantidad = ref(1)

const producto = ref({
  codigo: '',
  nombre: '',
  precio: 0
})

const carrito = ref([])

const API = "http://127.0.0.1:8000/api"

/* BUSCAR EN BASE DE DATOS */
async function buscarProducto() {
  try {
    const res = await axios.get(`${API}/productos/${codigo.value}`)
    producto.value = res.data
  } catch (error) {
    alert('Producto no encontrado')
    producto.value = { codigo: '', nombre: '', precio: 0 }
  }
}

/* AGREGAR */
function agregarProducto() {
  if (!producto.value.codigo) {
    alert('Producto no encontrado')
    return
  }

  const item = carrito.value.find(i => i.codigo === producto.value.codigo)

  if (item) {
    item.cantidad += cantidad.value
  } else {
    carrito.value.push({
      ...producto.value,
      cantidad: cantidad.value
    })
  }

  codigo.value = ''
  producto.value = { codigo: '', nombre: '', precio: 0 }
  cantidad.value = 1
}

/* ELIMINAR */
function eliminar(codigo) {
  carrito.value = carrito.value.filter(i => i.codigo !== codigo)
}

/* LIMPIAR */
function limpiar() {
  carrito.value = []
  codigo.value = ''
  producto.value = { codigo: '', nombre: '', precio: 0 }
  cantidad.value = 1
}

/* TOTAL */
const total = computed(() =>
  carrito.value.reduce((sum, i) => sum + i.precio * i.cantidad, 0)
)

/* GUARDAR VENTA EN LARAVEL */
async function finalizarVenta() {
  if (carrito.value.length === 0) {
    alert('No hay productos')
    return
  }

  try {
    await axios.post(`${API}/ventas`, {
      cliente: cliente.value,
      productos: carrito.value,
      total: total.value
    })

    alert('Venta guardada ✔')
    limpiar()

  } catch (error) {
    console.log(error)
    alert('Error al guardar venta')
  }
}
</script>