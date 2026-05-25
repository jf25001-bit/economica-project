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

       <!-- REEMPLAZA TODO EL <tbody> POR ESTO -->

<tbody>
  <tr
    v-for="producto in productos"
    :key="producto.id"
    class="border-t hover:bg-gray-50"
  >
    <!-- Código -->
    <td class="px-6 py-4">
      {{ producto.codigo }}
    </td>

    <!-- Imagen -->
    <td class="px-6 py-4">
      <img
        :src="producto.imagen"
        alt="Producto"
        class="w-16 h-16 object-cover rounded-xl border"
      />
    </td>

    <!-- Producto -->
    <td class="px-6 py-4 font-medium text-gray-800">
      {{ producto.nombre }}
    </td>

    <!-- Categoría -->
    <td class="px-6 py-4">
      <span
        class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm"
      >
        {{ producto.categoria }}
      </span>
    </td>

    <!-- Precio -->
    <td class="px-6 py-4">
      ${{ producto.precio }}
    </td>

    <!-- Stock -->
    <td class="px-6 py-4">
      {{ producto.stock }}
    </td>

    <!-- Lotes -->
    <td class="px-6 py-4">
      <button
        class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium hover:bg-indigo-200 transition"
      >
        <i class="bi bi-boxes mr-1"></i>
        {{ producto.lotes }}
      </button>
    </td>

    <!-- Estado -->
    <td class="px-6 py-4">
      <span
        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium"
      >
        Activo
      </span>
    </td>

    <!-- Acciones -->
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
const productos = [
  {
    id: 1,
    codigo: 'P001',
    nombre: 'Coca Cola 600ml',
    categoria: 'Bebidas',
    precio: '0.75',
    stock: 50,
    lotes: 2
  },
  {
    id: 2,
    codigo: 'P002',
    nombre: 'Leche Entera',
    categoria: 'Lácteos',
    precio: '1.35',
    stock: 35,
    lotes: 3
  },
  {
    id: 3,
    codigo: 'P003',
    nombre: 'Pan Bimbo',
    categoria: 'Panadería',
    precio: '2.50',
    stock: 20,
    lotes: 1
  },
  {
    id: 4,
    codigo: 'P004',
    nombre: 'Arroz 1 lb',
    categoria: 'Granos',
    precio: '0.90',
    stock: 80,
    lotes: 4
  }
]
</script>