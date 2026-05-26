<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Reportes</h1>
        
      </div>

      <button
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-download mr-2"></i>
        Exportar PDF
      </button>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-4 gap-6 mb-6">
      <!-- Ventas -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-600">
        <p class="text-gray-500">Ventas del Mes</p>
        <h2 class="text-3xl font-bold text-gray-800">$4,250.00</h2>
      </div>

      <!-- Compras -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-blue-600">
        <p class="text-gray-500">Compras del Mes</p>
        <h2 class="text-3xl font-bold text-gray-800">$2,100.00</h2>
      </div>

      <!-- Productos -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-orange-600">
        <p class="text-gray-500">Productos Registrados</p>
        <h2 class="text-3xl font-bold text-gray-800">120</h2>
      </div>

      <!-- Stock Bajo -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-red-600">
        <p class="text-gray-500">Productos con Stock Bajo</p>
        <h2 class="text-3xl font-bold text-gray-800">8</h2>
      </div>
    </div>

    <!-- Tabla de reportes -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Reporte</th>
            <th class="px-6 py-4 font-semibold">Descripción</th>
            <th class="px-6 py-4 font-semibold">Acción</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="reporte in reportes"
            :key="reporte.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-medium text-gray-800">
              {{ reporte.nombre }}
            </td>

            <td class="px-6 py-4 text-gray-600">
              {{ reporte.descripcion }}
            </td>

            <td class="px-6 py-4">
              <button
                class="bg-[#46674A] hover:bg-[#3b5740] text-white px-4 py-2 rounded-lg transition"
              >
                <i class="bi bi-download mr-2"></i>
                Generar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const reportes = [
  {
    id: 1,
    nombre: 'Reporte de Ventas',
    descripcion: 'Muestra todas las ventas realizadas.'
  },
  {
    id: 2,
    nombre: 'Reporte de Compras',
    descripcion: 'Muestra todas las compras registradas.'
  },
  {
    id: 3,
    nombre: 'Reporte de Inventario',
    descripcion: 'Muestra el stock actual de productos.'
  },
  {
    id: 4,
    nombre: 'Reporte de Usuarios',
    descripcion: 'Muestra los usuarios del sistema.'
  }
]
</script>