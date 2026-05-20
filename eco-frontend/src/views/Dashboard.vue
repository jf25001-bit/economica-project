<template>
  <div class="p-6">
    <!-- Título -->
    <h1 class="text-4xl font-bold text-gray-800 mb-2">
      Bienvenido al sistema La Económica
    </h1>

    <!-- Descripción -->
    <p class="text-gray-600 mb-8">
      Selecciona una opción del menú para comenzar.
    </p>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-4 gap-6">
      <!-- Productos -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-600">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Productos</p>
            <h2 class="text-3xl font-bold text-gray-800">120</h2>
          </div>
          <i class="bi bi-box-seam text-4xl text-green-700"></i>
        </div>
      </div>

      <!-- Ventas -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-blue-600">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Ventas Hoy</p>
            <h2 class="text-3xl font-bold text-gray-800">$245.50</h2>
          </div>
          <i class="bi bi-tag text-4xl text-blue-700"></i>
        </div>
      </div>

      <!-- Compras -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-orange-600">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Compras</p>
            <h2 class="text-3xl font-bold text-gray-800">18</h2>
          </div>
          <i class="bi bi-cart text-4xl text-orange-700"></i>
        </div>
      </div>

      <!-- Usuarios -->
      <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-purple-600">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Usuarios</p>
            <h2 class="text-3xl font-bold text-gray-800">4</h2>
          </div>
          <i class="bi bi-people text-4xl text-purple-700"></i>
        </div>
      </div>
    </div>

    <!-- Mensaje inferior -->
    <div class="mt-8 bg-white rounded-2xl shadow-md p-6">
      <h3 class="text-xl font-semibold text-gray-800 mb-2">
        Sistema Administrativo
      </h3>
      <p class="text-gray-600">
        Administra productos, ventas, compras, inventario, usuarios y reportes
        desde un solo lugar.
      </p>
    </div>
  </div>
</template>

<script setup>
</script>