<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Productos</h1>
      </div>

      <button
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Agregar Producto
      </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
      <div class="grid grid-cols-3 gap-20">
        <!-- Buscar -->
        <div class="relative">
          <i
            class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
          ></i>

          <input
            type="text"
            placeholder="Buscar productos..."
            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-3 focus:ring-[#46674A]"
          />
        </div>

        <!-- Categoría -->
        <select
          class="px- py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        >
          <option>Todas las categorías</option>
          <option>Bebidas</option>
          <option>Lácteos</option>
          <option>Panadería</option>
          <option>Granos</option>
          <option>Dulces</option>
          <option>Limpieza</option>
        </select>

        <!-- Estado -->
        <select
          class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        >
          <option>Todos los estados</option>
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Código</th>
            <th class="px-6 py-4 font-semibold">Images</th>
            <th class="px-6 py-4 font-semibold">Producto</th>
            <th class="px-6 py-4 font-semibold">Categoría</th>
            <th class="px-6 py-4 font-semibold">Precio</th>
            <th class="px-6 py-4 font-semibold">Stock</th>
            <th class="px-6 py-4 font-semibold">Lotes</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
            <th class="px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>


<tbody>
  <tr
    v-for="producto in productos"
    :key="producto.id"
    class="border-t hover:bg-gray-50"
  >
    <td class="px-6 py-4">
      {{ producto.codigo_barras }}
    </td>

    <td class="px-6 py-4">
      <img
        v-if="producto.imagenes && producto.imagenes.length"
        :src="`http://127.0.0.1:8000/storage/${producto.imagenes[0].ruta}`"
        alt="Producto"
        class="w-16 h-16 object-cover rounded-xl border"
      />

      <div
        v-else
        class="w-16 h-16 border rounded-xl flex items-center justify-center text-xs text-gray-400"
      >
        Sin imagen
      </div>
    </td>

    <td class="px-6 py-4 font-medium text-gray-800">
      {{ producto.nombre }}
    </td>

    <td class="px-6 py-4">
      {{ producto.subcategoria?.categoria?.nombre || 'Sin categoría' }}
    </td>

    <td class="px-6 py-4">
      ${{ producto.precio_venta }}
    </td>

    <td class="px-6 py-4">
      {{ producto.stock }}
    </td>

    <td class="px-6 py-4">
      -
    </td>

    <td class="px-6 py-4">
      <span
        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium"
      >
        Activo
      </span>
    </td>

    <td class="px-6 py-4">
      <div class="flex gap-2">
        <button
          class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200"
        >
          <i class="bi bi-pencil"></i>
        </button>

        <button
          class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200"
        >
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </td>
  </tr>
</tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const productos = ref([])

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = response.data

    console.log('Productos:', response.data)
  } catch (error) {
    console.error('Error al cargar productos:', error)
  }
})
</script>