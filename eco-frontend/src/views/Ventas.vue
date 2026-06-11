<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Ventas</h1>
        <p class="text-sm text-gray-500">Caja, historial y salida de inventario</p>
      </div>

      <button
        @click="abrirModal"
        class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-[#8bc0bd]"
      >
        <i class="bi bi-plus-lg"></i>
        Nueva Venta
      </button>
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-3">
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Ventas</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ ventas.length }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Ingresos</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">${{ formatoPrecio(totalVentas) }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Unidades vendidas</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ unidadesVendidas }}</p>
      </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3">
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por cliente, producto o factura..."
          class="h-10 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#9FCFCC]/50"
        />
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase text-gray-600">
              <th class="w-[12%] px-4 py-3">Factura</th>
              <th class="w-[18%] px-4 py-3">Cliente</th>
              <th class="w-[28%] px-4 py-3">Productos</th>
              <th class="w-[14%] px-4 py-3">Atendio</th>
              <th class="w-[13%] px-4 py-3">Fecha</th>
              <th class="w-[12%] px-4 py-3 text-right">Total</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="venta in ventasFiltradas"
              :key="venta.id"
              class="text-sm transition hover:bg-[#9FCFCC]/10"
            >
              <td class="px-4 py-4 font-semibold text-gray-900">F{{ String(venta.id).padStart(4, '0') }}</td>
              <td class="px-4 py-4 text-gray-700">{{ venta.cliente || 'Consumidor Final' }}</td>
              <td class="px-4 py-4">
                <p class="truncate font-medium text-gray-900">{{ resumenProductos(venta) }}</p>
                <p class="text-xs text-gray-500">{{ cantidadDetalles(venta) }} lineas</p>
              </td>
              <td class="px-4 py-4 text-gray-700">{{ venta.usuario?.name || 'Caja' }}</td>
              <td class="px-4 py-4 text-gray-700">{{ formatoFecha(venta.created_at) }}</td>
              <td class="px-4 py-4 text-right font-bold text-gray-950">${{ formatoPrecio(venta.total) }}</td>
            </tr>

            <tr v-if="!cargando && ventasFiltradas.length === 0">
              <td colspan="6" class="py-14 text-center text-gray-400">
                <i class="bi bi-receipt text-4xl"></i>
                <p class="mt-2">No hay ventas registradas</p>
              </td>
            </tr>

            <tr v-if="cargando">
              <td colspan="6" class="py-10 text-center text-gray-500">Cargando ventas...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-if="mostrarModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
    >
      <div class="w-full max-w-6xl overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <div>
            <h2 class="text-lg font-bold text-gray-950">Nueva Venta</h2>
            <p class="text-xs text-gray-500">Al finalizar se descuenta del inventario</p>
          </div>
          <button
            @click="cerrarModal"
            class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form @submit.prevent="finalizarVenta" class="grid max-h-[78vh] gap-0 overflow-y-auto lg:grid-cols-[1fr_340px]">
          <section class="p-5">
            <div class="mb-4 grid gap-3 md:grid-cols-[1fr_220px]">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Cliente</label>
                <input
                  v-model="cliente"
                  type="text"
                  placeholder="Consumidor Final"
                  class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40"
                />
              </div>
              <div class="rounded-md bg-gray-100 px-4 py-3">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-950">${{ formatoPrecio(totalCarrito) }}</p>
              </div>
            </div>

            <div class="mb-4 grid gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 md:grid-cols-[1fr_110px_auto]">
              <div class="relative">
                <i class="bi bi-upc-scan absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input
                  v-model="busquedaProducto"
                  @keyup.enter.prevent="agregarPrimeroFiltrado"
                  type="text"
                  placeholder="Codigo de barras o nombre del producto"
                  class="h-10 w-full rounded-md border border-gray-300 pl-9 pr-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40"
                  autofocus
                />
              </div>
              <input
                v-model.number="cantidad"
                min="1"
                type="number"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40"
              />
              <button
                type="button"
                @click="agregarPrimeroFiltrado"
                class="h-10 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 transition hover:bg-[#8bc0bd]"
              >
                Agregar
              </button>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200">
              <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] table-fixed">
                  <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase text-gray-500">
                      <th class="px-4 py-3">Producto</th>
                      <th class="px-4 py-3">Precio</th>
                      <th class="px-4 py-3">Cantidad</th>
                      <th class="px-4 py-3">Stock</th>
                      <th class="px-4 py-3 text-right">Subtotal</th>
                      <th class="w-14 px-4 py-3"></th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-for="item in carrito" :key="item.producto_id" class="text-sm">
                      <td class="px-4 py-3 font-medium text-gray-900">{{ item.nombre }}</td>
                      <td class="px-4 py-3">${{ formatoPrecio(item.precio_venta) }}</td>
                      <td class="px-4 py-3">
                        <input
                          v-model.number="item.cantidad"
                          min="1"
                          :max="item.stock"
                          type="number"
                          class="h-8 w-20 rounded-md border border-gray-300 px-2 text-sm outline-none focus:border-[#9FCFCC]"
                        />
                      </td>
                      <td class="px-4 py-3">{{ item.stock }}</td>
                      <td class="px-4 py-3 text-right font-semibold">${{ formatoPrecio(item.precio_venta * item.cantidad) }}</td>
                      <td class="px-4 py-3 text-right">
                        <button type="button" @click="quitarProducto(item.producto_id)" class="text-red-500 hover:text-red-700">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="carrito.length === 0">
                      <td colspan="6" class="py-10 text-center text-gray-400">Agrega productos para finalizar la venta</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <aside class="border-t border-gray-100 bg-gray-50 p-5 lg:border-l lg:border-t-0">
            <h3 class="mb-3 text-sm font-bold uppercase text-gray-600">Productos disponibles</h3>
            <div class="max-h-[420px] space-y-2 overflow-y-auto pr-1">
              <button
                v-for="producto in productosFiltrados"
                :key="producto.id"
                type="button"
                @click="agregarProducto(producto)"
                class="w-full rounded-md border border-gray-200 bg-white p-3 text-left transition hover:border-[#9FCFCC] hover:bg-[#9FCFCC]/10 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="producto.stock <= 0"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-gray-950">{{ producto.nombre }}</p>
                    <p class="truncate text-xs text-gray-500">{{ producto.codigo_barras || 'Sin codigo' }}</p>
                  </div>
                  <p class="text-sm font-bold text-gray-900">${{ formatoPrecio(producto.precio_venta) }}</p>
                </div>
                <p class="mt-2 text-xs text-gray-500">Stock: {{ producto.stock }}</p>
              </button>
            </div>

            <div class="mt-5 space-y-3 rounded-lg border border-gray-200 bg-white p-3">
              <label>
                <span class="mb-1 block text-sm font-semibold text-gray-700">Efectivo recibido</span>
                <input
                  v-model.number="efectivoRecibido"
                  min="0"
                  step="0.01"
                  type="number"
                  class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40"
                  placeholder="0.00"
                />
              </label>
              <div class="grid grid-cols-2 gap-3">
                <div class="rounded-md bg-gray-100 px-3 py-2">
                  <p class="text-xs text-gray-500">Total</p>
                  <p class="text-lg font-bold text-gray-950">${{ formatoPrecio(totalCarrito) }}</p>
                </div>
                <div class="rounded-md px-3 py-2" :class="cambio < 0 ? 'bg-red-50' : 'bg-[#9FCFCC]/25'">
                  <p class="text-xs text-gray-500">Cambio</p>
                  <p class="text-lg font-bold" :class="cambio < 0 ? 'text-red-600' : 'text-gray-950'">${{ formatoPrecio(Math.max(cambio, 0)) }}</p>
                </div>
              </div>
            </div>

            <div class="mt-5 flex gap-3">
              <button
                type="button"
                @click="cerrarModal"
                class="h-10 flex-1 rounded-md border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="guardando"
                class="h-10 flex-1 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-[#8bc0bd] disabled:opacity-50"
              >
                {{ guardando ? 'Procesando...' : 'Finalizar' }}
              </button>
            </div>
          </aside>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import { getProductos } from '@/services/productoService'
import { createVenta, getVentas } from '@/services/ventaService'

const ventas = ref([])
const productos = ref([])
const carrito = ref([])
const busqueda = ref('')
const busquedaProducto = ref('')
const cliente = ref('Consumidor Final')
const cantidad = ref(1)
const efectivoRecibido = ref('')
const cargando = ref(false)
const guardando = ref(false)
const mostrarModal = ref(false)

const ventasFiltradas = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()
  if (!texto) return ventas.value

  return ventas.value.filter(venta => {
    const factura = `f${String(venta.id).padStart(4, '0')}`
    return `${factura} ${venta.cliente} ${resumenProductos(venta)}`.toLowerCase().includes(texto)
  })
})

const productosFiltrados = computed(() => {
  const texto = busquedaProducto.value.trim().toLowerCase()
  const base = productos.value.filter(producto => Number(producto.stock || 0) > 0)
  if (!texto) return base.slice(0, 20)

  return base.filter(producto => {
    return `${producto.nombre} ${producto.codigo_barras || ''}`.toLowerCase().includes(texto)
  }).slice(0, 20)
})

const totalCarrito = computed(() => carrito.value.reduce((suma, item) => suma + Number(item.precio_venta) * Number(item.cantidad), 0))
const cambio = computed(() => Number(efectivoRecibido.value || 0) - totalCarrito.value)
const totalVentas = computed(() => ventas.value.reduce((suma, venta) => suma + Number(venta.total || 0), 0))
const unidadesVendidas = computed(() => ventas.value.reduce((suma, venta) => {
  return suma + (venta.detalles || []).reduce((acc, detalle) => acc + Number(detalle.cantidad || 0), 0)
}, 0))

onMounted(async () => {
  await Promise.all([
    cargarVentas(),
    cargarProductos()
  ])
})

async function cargarVentas() {
  cargando.value = true
  try {
    ventas.value = await getVentas()
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudieron cargar las ventas', 'error')
  } finally {
    cargando.value = false
  }
}

async function cargarProductos() {
  const response = await getProductos({ per_page: 1000 })
  productos.value = response.data || response || []
}

function abrirModal() {
  carrito.value = []
  cliente.value = 'Consumidor Final'
  busquedaProducto.value = ''
  cantidad.value = 1
  efectivoRecibido.value = ''
  mostrarModal.value = true
}

function cerrarModal() {
  mostrarModal.value = false
}

function agregarPrimeroFiltrado() {
  const producto = productosFiltrados.value[0]
  if (!producto) {
    Swal.fire('Producto no encontrado', 'Busca por nombre o codigo de barras', 'warning')
    return
  }
  agregarProducto(producto)
}

function agregarProducto(producto) {
  const cantidadAgregar = Math.max(1, Number(cantidad.value || 1))
  const existente = carrito.value.find(item => Number(item.producto_id) === Number(producto.id))

  if (existente) {
    const nuevaCantidad = existente.cantidad + cantidadAgregar
    if (nuevaCantidad > producto.stock) {
      Swal.fire('Stock insuficiente', `Solo hay ${producto.stock} unidades disponibles`, 'warning')
      return
    }
    existente.cantidad = nuevaCantidad
  } else {
    if (cantidadAgregar > producto.stock) {
      Swal.fire('Stock insuficiente', `Solo hay ${producto.stock} unidades disponibles`, 'warning')
      return
    }
    carrito.value.push({
      producto_id: Number(producto.id),
      nombre: producto.nombre,
      precio_venta: Number(producto.precio_venta || 0),
      cantidad: cantidadAgregar,
      stock: Number(producto.stock || 0)
    })
  }

  busquedaProducto.value = ''
  cantidad.value = 1
}

function quitarProducto(productoId) {
  carrito.value = carrito.value.filter(item => Number(item.producto_id) !== Number(productoId))
}

async function finalizarVenta() {
  if (carrito.value.length === 0) {
    Swal.fire('Venta vacia', 'Agrega al menos un producto', 'warning')
    return
  }

  const itemSinStock = carrito.value.find(item => Number(item.cantidad) > Number(item.stock))
  if (itemSinStock) {
    Swal.fire('Stock insuficiente', `${itemSinStock.nombre} solo tiene ${itemSinStock.stock} unidades`, 'warning')
    return
  }

  if (efectivoRecibido.value === '' || efectivoRecibido.value === null || Number(efectivoRecibido.value) < totalCarrito.value) {
    Swal.fire('Efectivo insuficiente', 'Ingresa el efectivo recibido para calcular el cambio y cubrir el total', 'warning')
    return
  }

  guardando.value = true
  try {
    await createVenta({
      cliente: cliente.value || 'Consumidor Final',
      productos: carrito.value.map(item => ({
        producto_id: item.producto_id,
        cantidad: Number(item.cantidad)
      })),
      efectivo_recibido: Number(efectivoRecibido.value)
    })

    await Promise.all([
      cargarVentas(),
      cargarProductos()
    ])

    cerrarModal()
    Swal.fire({ icon: 'success', title: 'Venta guardada', timer: 1400, showConfirmButton: false })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo finalizar la venta', 'error')
  } finally {
    guardando.value = false
  }
}

function resumenProductos(venta) {
  const nombres = (venta.detalles || []).map(item => item.producto?.nombre).filter(Boolean)
  return nombres.length ? nombres.join(', ') : 'Sin productos'
}

function cantidadDetalles(venta) {
  return (venta.detalles || []).length
}

function formatoFecha(fecha) {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-SV')
}

function formatoPrecio(valor) {
  return Number(valor || 0).toFixed(2)
}
</script>
