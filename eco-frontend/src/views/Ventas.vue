<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Ventas</h1>
        <p class="text-gray-600">
          Consulta y administra las ventas realizadas.
        </p>
      </div>

      <button
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nueva Venta
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
          placeholder="Buscar ventas..."
          class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        />
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Factura</th>
            <th class="px-6 py-4 font-semibold">Cliente</th>
            <th class="px-6 py-4 font-semibold">Fecha</th>
            <th class="px-6 py-4 font-semibold">Total</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
            <th class="px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="venta in ventas"
            :key="venta.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-medium">{{ venta.factura }}</td>
            <td class="px-6 py-4">{{ venta.cliente }}</td>
            <td class="px-6 py-4">{{ venta.fecha }}</td>
            <td class="px-6 py-4 font-semibold">${{ venta.total }}</td>

            <td class="px-6 py-4">
              <span
                class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium"
              >
                Completada
              </span>
            </td>

            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button
                  class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200"
                >
                  <i class="bi bi-eye"></i>
                </button>

                <button
                  class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200"
                >
                  <i class="bi bi-printer"></i>
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
const ventas = [
  {
    id: 1,
    factura: 'F001',
    cliente: 'Consumidor Final',
    fecha: '13/05/2026',
    total: '12.50'
  },
  {
    id: 2,
    factura: 'F002',
    cliente: 'Juan Pérez',
    fecha: '13/05/2026',
    total: '8.75'
  },
  {
    id: 3,
    factura: 'F003',
    cliente: 'María López',
    fecha: '12/05/2026',
    total: '25.00'
  },
  {
    id: 4,
    factura: 'F004',
    cliente: 'Carlos Martínez',
    fecha: '12/05/2026',
    total: '5.40'
  }
]
</script>