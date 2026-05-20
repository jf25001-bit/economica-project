<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
        <p class="text-gray-600">
          Consulta el stock actual de todos los productos.
        </p>
      </div>

      <button
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-arrow-repeat mr-2"></i>
        Actualizar Stock
      </button>
    </div>

    <!-- Barra de búsqueda -->
    <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
      <div class="relative">
        <i
          class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        ></i>

        <input
          type="text"
          placeholder="Buscar en inventario..."
          class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        />
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
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
            v-for="item in inventario"
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
                    : 'bg-red-100 text-red-700'
                ]"
              >
                {{ item.stock > item.minimo ? 'Disponible' : 'Stock Bajo' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const inventario = [
  {
    id: 1,
    codigo: 'P001',
    nombre: 'Coca Cola 600ml',
    categoria: 'Bebidas',
    stock: 50,
    minimo: 10
  },
  {
    id: 2,
    codigo: 'P002',
    nombre: 'Pan Bimbo',
    categoria: 'Panadería',
    stock: 8,
    minimo: 10
  },
  {
    id: 3,
    codigo: 'P003',
    nombre: 'Leche Entera',
    categoria: 'Lácteos',
    stock: 35,
    minimo: 15
  },
  {
    id: 4,
    codigo: 'P004',
    nombre: 'Arroz 1 lb',
    categoria: 'Granos',
    stock: 5,
    minimo: 10
  }
]
</script>