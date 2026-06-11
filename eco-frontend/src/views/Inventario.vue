<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Inventario</h1>
        <p class="text-sm text-gray-500">Existencias reales por producto</p>
      </div>
      <button @click="cargarInventario" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm hover:bg-[#8bc0bd]">
        <i class="bi bi-arrow-repeat"></i>
        Actualizar
      </button>
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-3">
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Productos</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ inventario.length }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Unidades</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ totalUnidades }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Stock bajo</p>
        <p class="mt-1 text-2xl font-bold text-red-600">{{ stockBajo }}</p>
      </div>
    </div>

    <div class="mb-4 grid gap-3 rounded-lg border border-gray-200 bg-white p-3 md:grid-cols-[1fr_180px_180px_auto]">
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input v-model="busqueda" type="text" placeholder="Buscar por codigo, producto, categoria o proveedor..." class="h-10 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#9FCFCC]/50" />
      </div>
      <input v-model="fechaInicio" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <input v-model="fechaFin" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <button @click="cargarInventario" class="h-10 rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filtrar</button>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] table-fixed">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase text-gray-600">
              <th class="w-[14%] px-4 py-3">Codigo</th>
              <th class="w-[24%] px-4 py-3">Producto</th>
              <th class="w-[16%] px-4 py-3">Categoria</th>
              <th class="w-[16%] px-4 py-3">Proveedor</th>
              <th class="w-[10%] px-4 py-3">Stock</th>
              <th class="w-[10%] px-4 py-3">Minimo</th>
              <th class="w-[10%] px-4 py-3">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in inventarioFiltrado" :key="item.id" class="text-sm transition hover:bg-[#9FCFCC]/10">
              <td class="px-4 py-4 text-gray-700">{{ item.codigo_barras || `P${String(item.id).padStart(4, '0')}` }}</td>
              <td class="px-4 py-4 font-medium text-gray-900">{{ item.nombre }}</td>
              <td class="px-4 py-4 text-gray-700">{{ item.categoria?.nombre || 'Sin categoria' }}</td>
              <td class="px-4 py-4 text-gray-700">{{ item.proveedor?.nombre || 'Sin proveedor' }}</td>
              <td class="px-4 py-4 font-semibold text-gray-950">{{ item.stock }}</td>
              <td class="px-4 py-4 text-gray-700">{{ item.stock_minimo }}</td>
              <td class="px-4 py-4">
                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="Number(item.stock) <= Number(item.stock_minimo) ? 'bg-red-100 text-red-700' : 'bg-[#9FCFCC]/40 text-gray-800'">
                  {{ Number(item.stock) <= Number(item.stock_minimo) ? 'Stock bajo' : 'Disponible' }}
                </span>
              </td>
            </tr>
            <tr v-if="!cargando && inventarioFiltrado.length === 0">
              <td colspan="7" class="py-12 text-center text-gray-400">No hay productos para mostrar</td>
            </tr>
            <tr v-if="cargando">
              <td colspan="7" class="py-10 text-center text-gray-500">Cargando inventario...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import { getProductos } from '../services/productoService'

const inventario = ref([])
const busqueda = ref('')
const fechaInicio = ref('')
const fechaFin = ref('')
const cargando = ref(false)

const inventarioFiltrado = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()
  if (!texto) return inventario.value
  return inventario.value.filter(item => `${item.codigo_barras || ''} ${item.nombre} ${item.categoria?.nombre || ''} ${item.proveedor?.nombre || ''}`.toLowerCase().includes(texto))
})

const totalUnidades = computed(() => inventario.value.reduce((suma, item) => suma + Number(item.stock || 0), 0))
const stockBajo = computed(() => inventario.value.filter(item => Number(item.stock) <= Number(item.stock_minimo)).length)

onMounted(cargarInventario)

async function cargarInventario() {
  cargando.value = true
  try {
    const response = await getProductos({
      per_page: 1000,
      fecha_inicio: fechaInicio.value || undefined,
      fecha_fin: fechaFin.value || undefined
    })
    inventario.value = response.data || response || []
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo cargar el inventario', 'error')
  } finally {
    cargando.value = false
  }
}
</script>
