<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Compras</h1>
        <p class="text-sm text-gray-500">Registro de entradas e inventario</p>
      </div>

      <button
        @click="abrirModal"
        class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#46674A] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b5740]"
      >
        <i class="bi bi-plus-lg"></i>
        Nueva Compra
      </button>
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-3">
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Compras</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ compras.length }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Total invertido</p>
        <p class="mt-1 text-2xl font-bold text-[#46674A]">${{ formatoPrecio(totalCompras) }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Productos recibidos</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ unidadesCompradas }}</p>
      </div>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3">
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por proveedor, producto o estado..."
          class="h-10 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#46674A]/20"
        />
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] table-fixed">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase text-gray-600">
              <th class="w-[12%] px-4 py-3">Compra</th>
              <th class="w-[20%] px-4 py-3">Proveedor</th>
              <th class="w-[24%] px-4 py-3">Productos</th>
              <th class="w-[13%] px-4 py-3">Fecha</th>
              <th class="w-[12%] px-4 py-3">Estado</th>
              <th class="w-[12%] px-4 py-3 text-right">Total</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="compra in comprasFiltradas"
              :key="compra.id"
              class="text-sm transition hover:bg-green-50"
            >
              <td class="px-4 py-4 font-semibold text-gray-900">C{{ String(compra.id).padStart(4, '0') }}</td>
              <td class="px-4 py-4 text-gray-700">{{ proveedorCompra(compra) }}</td>
              <td class="px-4 py-4">
                <p class="truncate font-medium text-gray-900">{{ resumenProductos(compra) }}</p>
                <p class="text-xs text-gray-500">{{ cantidadDetalles(compra) }} lineas</p>
              </td>
              <td class="px-4 py-4 text-gray-700">{{ formatoFecha(compra.fecha_compra || compra.created_at) }}</td>
              <td class="px-4 py-4">
                <span
                  class="rounded-full px-3 py-1 text-xs font-semibold"
                  :class="claseEstado(compra.estado)"
                >
                  {{ etiquetaEstado(compra.estado) }}
                </span>
              </td>
              <td class="px-4 py-4 text-right font-bold text-gray-950">${{ formatoPrecio(compra.total) }}</td>
            </tr>

            <tr v-if="!cargando && comprasFiltradas.length === 0">
              <td colspan="6" class="py-14 text-center text-gray-400">
                <i class="bi bi-basket2 text-4xl"></i>
                <p class="mt-2">No hay compras para mostrar</p>
              </td>
            </tr>

            <tr v-if="cargando">
              <td colspan="6" class="py-10 text-center text-gray-500">Cargando compras...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-if="mostrarModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
    >
      <div class="w-full max-w-5xl overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <div>
            <h2 class="text-lg font-bold text-gray-950">Nueva Compra</h2>
            <p class="text-xs text-gray-500">Al guardar como completada se suma al inventario</p>
          </div>
          <button
            @click="cerrarModal"
            class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form @submit.prevent="guardarCompra" class="max-h-[78vh] overflow-y-auto p-5">
          <div class="mb-4 grid gap-3 md:grid-cols-3">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Fecha de compra</label>
              <input
                v-model="form.fecha_compra"
                type="date"
                class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Estado</label>
              <select
                v-model="form.estado"
                class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="completada">Completada</option>
                <option value="pendiente">Pendiente</option>
              </select>
            </div>
            <div class="rounded-md bg-gray-100 px-4 py-3">
              <p class="text-xs text-gray-500">Total</p>
              <p class="text-2xl font-bold text-[#46674A]">${{ formatoPrecio(totalFormulario) }}</p>
            </div>
          </div>

          <div class="mb-4 rounded-lg border border-gray-200">
            <div class="grid gap-3 border-b border-gray-100 bg-gray-50 p-3 md:grid-cols-[1.3fr_.45fr_.45fr_.7fr_.55fr_auto]">
              <select
                v-model="linea.producto_id"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Producto</option>
                <option
                  v-for="producto in productos"
                  :key="producto.id"
                  :value="producto.id"
                >
                  {{ producto.nombre }} - stock {{ producto.stock }}
                </option>
              </select>
              <input
                v-model.number="linea.cantidad"
                min="1"
                type="number"
                placeholder="Cantidad"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
              <input
                v-model.number="linea.precio_compra"
                min="0"
                step="0.01"
                type="number"
                placeholder="Precio"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
              <input
                v-model="linea.codigo_lote"
                type="text"
                placeholder="Lote"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
              <input
                v-model="linea.fecha_expiracion"
                type="date"
                class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
              <button
                type="button"
                @click="agregarDetalle"
                class="h-10 rounded-md bg-[#46674A] px-4 text-sm font-semibold text-white transition hover:bg-[#3b5740]"
              >
                Agregar
              </button>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full min-w-[760px] table-fixed">
                <thead class="bg-white">
                  <tr class="text-left text-xs font-semibold uppercase text-gray-500">
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3">Cantidad</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Lote</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                    <th class="w-14 px-4 py-3"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="item in form.detalles" :key="item.uid" class="text-sm">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ item.nombre }}</td>
                    <td class="px-4 py-3">{{ item.cantidad }}</td>
                    <td class="px-4 py-3">${{ formatoPrecio(item.precio_compra) }}</td>
                    <td class="px-4 py-3">{{ item.codigo_lote || 'Sin lote' }}</td>
                    <td class="px-4 py-3 text-right font-semibold">${{ formatoPrecio(item.cantidad * item.precio_compra) }}</td>
                    <td class="px-4 py-3 text-right">
                      <button type="button" @click="quitarDetalle(item.uid)" class="text-red-500 hover:text-red-700">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="form.detalles.length === 0">
                    <td colspan="6" class="py-8 text-center text-gray-400">Agrega al menos un producto</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              type="button"
              @click="cerrarModal"
              class="h-10 rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="guardando"
              class="h-10 rounded-md bg-[#46674A] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b5740] disabled:opacity-50"
            >
              {{ guardando ? 'Guardando...' : 'Guardar Compra' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import Swal from 'sweetalert2'
import { createCompra, getCompras } from '@/services/compraService'
import { getProductos } from '@/services/productoService'

const compras = ref([])
const productos = ref([])
const busqueda = ref('')
const cargando = ref(false)
const guardando = ref(false)
const mostrarModal = ref(false)

const form = ref(modeloFormulario())
const linea = ref(modeloLinea())

const comprasFiltradas = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()
  if (!texto) return compras.value

  return compras.value.filter(compra => {
    const proveedor = proveedorCompra(compra).toLowerCase()
    const productosTexto = resumenProductos(compra).toLowerCase()
    return `${proveedor} ${productosTexto} ${compra.estado}`.includes(texto)
  })
})

const totalCompras = computed(() => compras.value.reduce((suma, compra) => suma + Number(compra.total || 0), 0))
const unidadesCompradas = computed(() => compras.value.reduce((suma, compra) => {
  return suma + (compra.detalles || []).reduce((acc, detalle) => acc + Number(detalle.cantidad || 0), 0)
}, 0))
const totalFormulario = computed(() => form.value.detalles.reduce((suma, item) => suma + (Number(item.cantidad) * Number(item.precio_compra)), 0))

watch(() => linea.value.producto_id, (id) => {
  const producto = productos.value.find(item => Number(item.id) === Number(id))
  if (!producto) return
  linea.value.precio_compra = Number(producto.precio_compra || 0)
})

onMounted(async () => {
  await Promise.all([
    cargarCompras(),
    cargarProductos()
  ])
})

function modeloFormulario() {
  return {
    fecha_compra: new Date().toISOString().slice(0, 10),
    estado: 'completada',
    detalles: []
  }
}

function modeloLinea() {
  return {
    producto_id: '',
    cantidad: 1,
    precio_compra: 0,
    codigo_lote: '',
    fecha_expiracion: ''
  }
}

async function cargarCompras() {
  cargando.value = true
  try {
    compras.value = await getCompras()
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudieron cargar las compras', 'error')
  } finally {
    cargando.value = false
  }
}

async function cargarProductos() {
  const response = await getProductos({ per_page: 1000 })
  productos.value = response.data || response || []
}

function abrirModal() {
  form.value = modeloFormulario()
  linea.value = modeloLinea()
  mostrarModal.value = true
}

function cerrarModal() {
  mostrarModal.value = false
}

function agregarDetalle() {
  const producto = productos.value.find(item => Number(item.id) === Number(linea.value.producto_id))
  if (!producto || Number(linea.value.cantidad) < 1) {
    Swal.fire('Revisa el producto', 'Selecciona un producto y una cantidad valida', 'warning')
    return
  }

  form.value.detalles.push({
    uid: crypto.randomUUID(),
    producto_id: Number(producto.id),
    nombre: producto.nombre,
    cantidad: Number(linea.value.cantidad),
    precio_compra: Number(linea.value.precio_compra || producto.precio_compra || 0),
    codigo_lote: linea.value.codigo_lote,
    fecha_expiracion: linea.value.fecha_expiracion || null
  })

  linea.value = modeloLinea()
}

function quitarDetalle(uid) {
  form.value.detalles = form.value.detalles.filter(item => item.uid !== uid)
}

async function guardarCompra() {
  if (form.value.detalles.length === 0) {
    Swal.fire('Compra vacia', 'Agrega al menos un producto', 'warning')
    return
  }

  guardando.value = true
  try {
    await createCompra({
      fecha_compra: form.value.fecha_compra,
      estado: form.value.estado,
      detalles: form.value.detalles.map(({ uid, nombre, ...item }) => item)
    })

    await Promise.all([
      cargarCompras(),
      cargarProductos()
    ])

    cerrarModal()
    Swal.fire({ icon: 'success', title: 'Compra guardada', timer: 1400, showConfirmButton: false })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo guardar la compra', 'error')
  } finally {
    guardando.value = false
  }
}

function proveedorCompra(compra) {
  const detalle = (compra.detalles || []).find(item => item.producto?.proveedor)
  return detalle?.producto?.proveedor?.nombre || 'Sin proveedor'
}

function resumenProductos(compra) {
  const nombres = (compra.detalles || []).map(item => item.producto?.nombre).filter(Boolean)
  return nombres.length ? nombres.join(', ') : 'Sin productos'
}

function cantidadDetalles(compra) {
  return (compra.detalles || []).length
}

function formatoFecha(fecha) {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-SV')
}

function formatoPrecio(valor) {
  return Number(valor || 0).toFixed(2)
}

function etiquetaEstado(estado) {
  return {
    completada: 'Completada',
    pendiente: 'Pendiente',
    cancelada: 'Cancelada'
  }[estado] || 'Pendiente'
}

function claseEstado(estado) {
  return {
    completada: 'bg-green-100 text-green-700',
    pendiente: 'bg-amber-100 text-amber-700',
    cancelada: 'bg-red-100 text-red-700'
  }[estado] || 'bg-gray-100 text-gray-700'
}
</script>
