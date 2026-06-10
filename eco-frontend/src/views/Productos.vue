<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex h-12 items-center justify-between rounded-lg border border-gray-200 bg-white px-4">
      <div class="relative w-full max-w-md">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="buscar"
          @input="cargarProductos"
          type="text"
          placeholder="Buscar producto..."
          class="h-9 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#46674A]/20"
        />
      </div>

      <div class="hidden items-center gap-2 md:flex">
        <button class="text-gray-500 transition hover:text-[#46674A]">
          <i class="bi bi-chat-left-text"></i>
        </button>
        <button class="relative text-gray-500 transition hover:text-[#46674A]">
          <i class="bi bi-bell"></i>
          <span class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-red-500"></span>
        </button>
      </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1fr_356px]">
      <main>
        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-xl font-bold text-gray-950">Gestion de Productos</h1>
            <p class="text-sm text-gray-500">
              Vista General y Listado ({{ total }} productos)
            </p>
          </div>

          <button
            @click="abrirModal"
            class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-[#46674A] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b5740]"
          >
            <i class="bi bi-plus-lg"></i>
            Nuevo Producto
          </button>
        </div>

        <div class="mb-3 rounded-lg border border-gray-200 bg-white p-3">
          <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Categoria</label>
              <select
                v-model="categoriaFiltro"
                @change="cargarProductos"
                class="h-8 rounded-md border border-gray-200 bg-gray-50 px-2 text-sm outline-none focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Todas</option>
                <option
                  v-for="cat in categorias"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.nombre }}
                </option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Estado</label>
              <select
                v-model="estado"
                @change="cargarProductos"
                class="h-8 rounded-md border border-gray-200 bg-gray-50 px-2 text-sm outline-none focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Todos</option>
                <option value="disponible">Disponible</option>
                <option value="bajo_stock">Bajo Stock</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Fecha</label>
              <select class="h-8 rounded-md border border-gray-200 bg-gray-50 px-2 text-sm outline-none">
                <option>All</option>
              </select>
            </div>

            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Ordenar</label>
              <select
                v-model="ordenar"
                @change="cargarProductos"
                class="h-8 rounded-md border border-gray-200 bg-gray-50 px-2 text-sm outline-none focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="recientes">Recientes</option>
                <option value="nombre">Nombre</option>
                <option value="precio">Precio</option>
              </select>
            </div>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] table-fixed">
              <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase text-gray-600">
                  <th class="w-[5%] px-4 py-3 text-center">
                    <input type="checkbox" class="h-4 w-4" />
                  </th>
                  <th class="w-[13%] px-4 py-3">SKU</th>
                  <th class="w-[28%] px-4 py-3">Nombre del Producto</th>
                  <th class="w-[16%] px-4 py-3">Categoria</th>
                  <th class="w-[12%] px-4 py-3">Estado</th>
                  <th class="w-[8%] px-4 py-3">Stock</th>
                  <th class="w-[12%] px-4 py-3">Precio Base</th>
                  <th class="w-[10%] px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-100">
                <tr
                  v-for="producto in productos"
                  :key="producto.id"
                  class="text-sm transition hover:bg-green-50"
                >
                  <td class="px-4 py-4 text-center">
                    <input type="checkbox" class="h-4 w-4" />
                  </td>

                  <td class="truncate px-4 py-4 font-medium text-gray-800">
                    {{ producto.codigo_barras || 'Sin codigo' }}
                  </td>

                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-gray-100 text-[#46674A]">
                        <i class="bi bi-box-seam"></i>
                      </div>
                      <div class="min-w-0">
                        <p class="truncate font-semibold text-blue-600">{{ producto.nombre }}</p>
                        <p class="truncate text-xs text-gray-500">
                          Codigo: {{ producto.codigo_barras || 'Sin codigo' }}
                        </p>
                      </div>
                    </div>
                  </td>

                  <td class="px-4 py-4 text-gray-700">
                    <p class="truncate">{{ nombreCategoriaProducto(producto) }}</p>
                    <p
                      v-if="producto.sub_categoria_id"
                      class="truncate text-xs text-gray-500"
                    >
                      {{ nombreSubcategoriaPorId(producto.sub_categoria_id) }}
                    </p>
                  </td>

                  <td class="px-4 py-4">
                    <span
                      class="rounded-full px-3 py-1 text-xs font-semibold"
                      :class="producto.stock <= producto.stock_minimo ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'"
                    >
                      {{ producto.stock <= producto.stock_minimo ? 'Bajo Stock' : 'Disponible' }}
                    </span>
                  </td>

                  <td class="px-4 py-4 font-semibold text-gray-800">
                    {{ producto.stock }}
                  </td>

                  <td class="px-4 py-4 font-semibold">
                    ${{ formatoPrecio(producto.precio_venta) }}
                  </td>

                  <td class="px-4 py-4 text-right">
                    <button class="mr-1 text-gray-500 hover:text-blue-600" title="Ver">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="mr-1 text-gray-500 hover:text-blue-600" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button
                      @click="eliminarProducto(producto.id)"
                      class="text-gray-500 hover:text-red-500"
                      title="Eliminar"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="productos.length === 0">
                  <td colspan="8" class="py-14 text-center text-gray-400">
                    <i class="bi bi-box text-4xl"></i>
                    <p class="mt-2">No hay productos registrados</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>

      <aside>
        <div class="rounded-lg border border-gray-200 bg-white p-3">
          <h2 class="mb-3 text-sm font-bold text-gray-950">Resumen de Inventario</h2>

          <div class="mb-2 flex items-center justify-between rounded-md border border-gray-100 px-3 py-3">
            <div>
              <span class="text-xs text-gray-500">Total Productos</span>
              <span class="ml-0.5 text-lg font-bold">{{ resumen.total }}</span>
            </div>
            <svg viewBox="0 0 50 20" class="h-5 w-11">
              <path d="M0,15 Q10,5 20,12 T40,4 L50,8" fill="none" stroke="#2563eb" stroke-width="2" />
            </svg>
          </div>

          <div class="mb-2 flex items-center justify-between rounded-md border border-gray-100 px-3 py-3">
            <div>
              <span class="text-xs text-gray-500">Disponible</span>
              <span class="ml-0.5 text-lg font-bold">{{ resumen.disponibles }}</span>
            </div>
            <svg viewBox="0 0 50 20" class="h-5 w-11">
              <path d="M0,10 Q12,14 25,6 T50,4" fill="none" stroke="#10b981" stroke-width="2" />
            </svg>
          </div>

          <div class="flex items-center justify-between rounded-md border border-gray-100 px-3 py-3">
            <div>
              <span class="text-xs text-gray-500">Bajo Stock</span>
              <span class="ml-0.5 text-lg font-bold">{{ resumen.bajo_stock }}</span>
            </div>
            <svg viewBox="0 0 50 20" class="h-5 w-11">
              <path d="M0,5 Q15,15 30,5 T50,15" fill="none" stroke="#f59e0b" stroke-width="2" />
            </svg>
          </div>
        </div>
      </aside>
    </div>

    <div
      v-if="mostrarModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
    >
      <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-[#46674A] px-6 py-4 text-white">
          <div>
            <h3 class="text-xl font-bold">Nuevo Producto</h3>
            <p class="text-xs text-white/75">Completa los datos principales del producto</p>
          </div>

          <button
            type="button"
            @click="cerrarModal"
            class="flex h-8 w-8 items-center justify-center rounded-full transition hover:bg-white/20"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form
          @submit.prevent="guardarProducto"
          class="max-h-[72vh] overflow-y-auto p-6"
        >
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Nombre del producto</label>
              <input
                v-model="nuevoProducto.nombre"
                required
                type="text"
                placeholder="Ej. Coca Cola 600ml"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Codigo de barras</label>
              <input
                v-model="nuevoProducto.codigo_barras"
                type="text"
                placeholder="Ej. 7501055300075"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Categoria</label>
              <select
                v-model="nuevoProducto.categoria_id"
                @change="alCambiarCategoriaProducto"
                required
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Seleccione una categoria</option>
                <option
                  v-for="cat in categorias"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.nombre }}
                </option>
              </select>
            </div>

            <div v-if="subcategoriasDeCategoria.length > 0">
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Subcategoria</label>
              <select
                v-model="nuevoProducto.sub_categoria_id"
                required
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Seleccione una subcategoria</option>
                <option
                  v-for="sub in subcategoriasDeCategoria"
                  :key="sub.id"
                  :value="sub.id"
                >
                  {{ sub.nombre }}
                </option>
              </select>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Proveedor</label>
              <select
                v-model="nuevoProducto.proveedor_id"
                required
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              >
                <option value="">Seleccione un proveedor</option>
                <option
                  v-for="proveedor in proveedores"
                  :key="proveedor.id"
                  :value="proveedor.id"
                >
                  {{ proveedor.nombre }}
                </option>
              </select>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Stock inicial</label>
              <input
                v-model.number="nuevoProducto.stock"
                required
                min="0"
                type="number"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Stock minimo</label>
              <input
                v-model.number="nuevoProducto.stock_minimo"
                required
                min="0"
                type="number"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Precio compra</label>
              <input
                v-model.number="nuevoProducto.precio_compra"
                required
                min="0"
                step="0.01"
                type="number"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold text-gray-700">Precio venta</label>
              <input
                v-model.number="nuevoProducto.precio_venta"
                required
                min="0"
                step="0.01"
                type="number"
                class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-[#46674A] focus:ring-2 focus:ring-[#46674A]/20"
              />
            </div>
          </div>

          <div class="mt-5 flex justify-end gap-3 border-t border-gray-100 pt-4">
            <button
              type="button"
              @click="cerrarModal"
              class="h-10 rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
              Cancelar
            </button>

            <button
              type="submit"
              :disabled="guardando"
              class="h-10 rounded-lg bg-[#46674A] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b5740] disabled:opacity-50"
            >
              {{ guardando ? 'Guardando...' : 'Guardar Producto' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import {
  createProducto,
  deleteProducto as borrarProducto,
  getProductos,
  getResumenProductos
} from '@/services/productoService'
import { getCategorias } from '@/services/categoriaService'
import { getProveedores } from '@/services/proveedorService'
import { getSubcategorias } from '@/services/subcategoriaService'

const productos = ref([])
const categorias = ref([])
const subcategorias = ref([])
const proveedores = ref([])
const resumen = ref({ total: 0, disponibles: 0, bajo_stock: 0 })

const buscar = ref('')
const categoriaFiltro = ref('')
const estado = ref('')
const ordenar = ref('recientes')
const total = ref(0)
const mostrarModal = ref(false)
const guardando = ref(false)

const modeloProductoLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  precio_compra: 0,
  precio_venta: 0,
  stock: 0,
  stock_minimo: 5,
  categoria_id: '',
  sub_categoria_id: '',
  proveedor_id: ''
})

const nuevoProducto = ref(modeloProductoLimpio())

const categoriasPorId = computed(() => {
  return Object.fromEntries(categorias.value.map(cat => [Number(cat.id), cat]))
})

const subcategoriasPorId = computed(() => {
  return Object.fromEntries(subcategorias.value.map(sub => [Number(sub.id), sub]))
})

const proveedoresPorId = computed(() => {
  return Object.fromEntries(proveedores.value.map(proveedor => [Number(proveedor.id), proveedor]))
})

const subcategoriasDeCategoria = computed(() => {
  if (!nuevoProducto.value.categoria_id) return []

  return subcategorias.value.filter(sub =>
    Number(sub.categoria_id) === Number(nuevoProducto.value.categoria_id)
  )
})

onMounted(async () => {
  await Promise.all([
    cargarCatalogos(),
    cargarProductos(),
    cargarResumen()
  ])
})

async function cargarCatalogos() {
  const [categoriasRes, subcategoriasRes, proveedoresRes] = await Promise.all([
    getCategorias(),
    getSubcategorias(),
    getProveedores()
  ])

  categorias.value = categoriasRes || []
  subcategorias.value = subcategoriasRes || []
  proveedores.value = proveedoresRes || []
}

async function cargarProductos() {
  try {
    const response = await getProductos({
      search: buscar.value,
      categoria: categoriaFiltro.value,
      estado: estado.value,
      ordenar: ordenar.value
    })

    productos.value = response.data || response || []
    total.value = response.total || productos.value.length
  } catch (error) {
    console.error(error)
  }
}

async function cargarResumen() {
  try {
    resumen.value = await getResumenProductos()
  } catch (error) {
    console.error(error)
  }
}

function abrirModal() {
  nuevoProducto.value = modeloProductoLimpio()
  mostrarModal.value = true
}

function cerrarModal() {
  mostrarModal.value = false
  nuevoProducto.value = modeloProductoLimpio()
}

function alCambiarCategoriaProducto() {
  nuevoProducto.value.sub_categoria_id = ''
}

async function guardarProducto() {
  if (guardando.value) return
  guardando.value = true

  try {
    await createProducto({
      ...nuevoProducto.value,
      categoria_id: Number(nuevoProducto.value.categoria_id),
      sub_categoria_id: nuevoProducto.value.sub_categoria_id
        ? Number(nuevoProducto.value.sub_categoria_id)
        : null,
      proveedor_id: Number(nuevoProducto.value.proveedor_id)
    })

    await Promise.all([
      cargarProductos(),
      cargarResumen()
    ])

    cerrarModal()

    Swal.fire({
      icon: 'success',
      title: 'Producto guardado',
      timer: 1400,
      showConfirmButton: false
    })
  } catch (error) {
    Swal.fire(
      'Error',
      error?.response?.data?.message || 'No se pudo guardar el producto',
      'error'
    )
  } finally {
    guardando.value = false
  }
}

async function eliminarProducto(id) {
  const result = await Swal.fire({
    title: 'Eliminar producto?',
    text: 'Esta accion no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#46674A',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, eliminar'
  })

  if (!result.isConfirmed) return

  try {
    await borrarProducto(id)
    await Promise.all([
      cargarProductos(),
      cargarResumen()
    ])
  } catch (error) {
    Swal.fire(
      'Error',
      error?.response?.data?.message || 'No se pudo eliminar el producto',
      'error'
    )
  }
}

function nombreCategoriaProducto(producto) {
  if (producto.categoria_id) {
    return categoriasPorId.value[Number(producto.categoria_id)]?.nombre || 'Sin categoria'
  }

  const sub = subcategoriasPorId.value[Number(producto.sub_categoria_id)]
  return sub ? categoriasPorId.value[Number(sub.categoria_id)]?.nombre || 'Sin categoria' : 'Sin categoria'
}

function nombreSubcategoriaPorId(id) {
  return subcategoriasPorId.value[Number(id)]?.nombre || 'Sin subcategoria'
}

function nombreProveedorPorId(id) {
  return proveedoresPorId.value[Number(id)]?.nombre || 'Sin proveedor'
}

function formatoPrecio(valor) {
  return Number(valor || 0).toFixed(2)
}
</script>
