<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Usuarios</h1>
        <p class="text-gray-600">
          Administra los usuarios que tienen acceso al sistema.
        </p>
      </div>

      <button
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nuevo Usuario
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
          placeholder="Buscar usuarios..."
          class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        />
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">Nombre</th>
            <th class="px-6 py-4 font-semibold">Correo</th>
            <th class="px-6 py-4 font-semibold">Rol</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
            <th class="px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="usuario in usuarios"
            :key="usuario.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-medium text-gray-800">
              {{ usuario.nombre }}
            </td>
            <td class="px-6 py-4">{{ usuario.correo }}</td>
            <td class="px-6 py-4">{{ usuario.rol }}</td>

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
const usuarios = [
  {
    id: 1,
    nombre: 'Administrador',
    correo: 'admin@laeconomica.com',
    rol: 'Administrador'
  },
  {
    id: 2,
    nombre: 'María López',
    correo: 'maria@laeconomica.com',
    rol: 'Cajero'
  },
  {
    id: 3,
    nombre: 'Juan Pérez',
    correo: 'juan@laeconomica.com',
    rol: 'Encargado'
  },
  {
    id: 4,
    nombre: 'Ana Martínez',
    correo: 'ana@laeconomica.com',
    rol: 'Cajero'
  }
]
</script>