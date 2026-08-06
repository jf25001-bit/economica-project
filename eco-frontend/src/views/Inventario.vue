<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
      </div>

      <button
        @click="cargarInventario"
        :disabled="cargando"
        class="bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-5 py-3 rounded-xl shadow-md transition font-medium disabled:opacity-60"
      >
        <i class="bi bi-arrow-repeat mr-2" :class="{ 'animate-spin': cargando }"></i>
        {{ cargando ? 'Actualizando...' : 'Actualizar Stock' }}
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
      <div class="relative">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar en inventario..."
          class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
        />
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[760px]">
          <thead class="bg-gray-100">
            <tr class="text-left text-gray-700">
              <th class="px-6 py-4 font-semibold">Código</th>
              <th class="px-6 py-4 font-semibold">Producto</th>
              <th class="px-6 py-4 font-semibold">Categoría</th>
              <th class="px-6 py-4 font-semibold">Stock</th>
              <th class="px-6 py-4 font-semibold">Stock Mínimo</th>
              <th class="px-6 py-4 font-semibold">Estado</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="item in inventarioFiltrado"
              :key="item.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-6 py-4">{{ item.codigo }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">
                {{ item.nombre }}
              </td>
              <td class="px-6 py-4">{{ item.categoria }}</td>
              <td class="px-6 py-4 font-semibold">{{ item.stock }}</td>
              <td class="px-6 py-4">{{ item.minimo }}</td>

              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-sm font-medium',
                    item.stock > item.minimo
                      ? 'bg-green-100 text-green-700'
                      : 'bg-amber-100 text-amber-700'
                  ]"
                >
                  {{ item.stock > item.minimo ? 'Disponible' : 'Stock Bajo' }}
                </span>
              </td>
            </tr>

            <tr v-if="cargando">
              <td colspan="6" class="text-center py-10 text-gray-400">
                Cargando inventario...
              </td>
            </tr>

            <tr v-else-if="inventarioFiltrado.length === 0">
              <td colspan="6" class="text-center py-10 text-gray-400 italic">
                No hay productos en inventario.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="error" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getProductos } from '@/services/productoService'

const productos = ref([])
const busqueda = ref('')
const cargando = ref(false)
const error = ref('')

const inventario = computed(() => {
  return productos.value.map(producto => ({
    id: producto.id,
    codigo: producto.codigo_barras || 'Sin código',
    nombre: producto.nombre || 'Sin nombre',
    categoria: producto.subcategoria?.categoria?.nombre || 'General',
    stock: Number(producto.stock) || 0,
    minimo: Number(producto.stock_minimo) || 0
  }))
})

const inventarioFiltrado = computed(() => {
  const query = busqueda.value.toLowerCase().trim()
  if (!query) return inventario.value

  return inventario.value.filter(item =>
    item.codigo.toLowerCase().includes(query) ||
    item.nombre.toLowerCase().includes(query) ||
    item.categoria.toLowerCase().includes(query)
  )
})

const cargarInventario = async () => {
  cargando.value = true
  error.value = ''

  try {
    const res = await getProductos()
    const data = res.data?.data || res.data || []
    productos.value = Array.isArray(data) ? data : []
  } catch (err) {
    console.error('Error cargando inventario:', err)
    productos.value = []
    error.value = 'No se pudo cargar el inventario desde la base de datos.'
  } finally {
    cargando.value = false
  }
}

onMounted(() => {
  cargarInventario()
})
</script>
