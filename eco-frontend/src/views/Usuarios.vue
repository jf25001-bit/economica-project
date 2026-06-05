<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          Usuarios
        </h1>
      </div>

      <!-- Botón -->
      <button
        @click="abrirModal"
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
            <th class="px-6 py-4 font-semibold">
              Nombre
            </th>

            <th class="px-6 py-4 font-semibold">
              Rol
            </th>

            <th class="px-6 py-4 font-semibold">
              Acciones
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="usuario in usuarios"
            :key="usuario.id"
            class="border-t hover:bg-gray-50"
          >
            <!-- Nombre -->
            <td class="px-6 py-4 font-medium text-gray-800">
              {{ usuario.nombre }}
            </td>

            <!-- Rol -->
            <td class="px-6 py-4">
              {{ usuario.rol }}
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4">
              <div class="flex gap-2">

                <!-- Editar -->
                <button
                  @click="editarUsuario(usuario)"
                  class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200"
                >
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Eliminar -->
                <button
                  @click="eliminarUsuario(usuario.id)"
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

    <!-- Modal -->
    <div
      v-if="mostrarModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6"
      >
        <!-- Encabezado -->
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-800">
            {{
              editandoId
                ? 'Editar Usuario'
                : 'Nuevo Usuario'
            }}
          </h2>

          <button
            @click="cerrarModal"
            class="text-3xl text-gray-500"
          >
            &times;
          </button>
        </div>

        <!-- Nombre -->
        <div class="mb-5">
          <label class="block text-gray-700 font-medium mb-2">
            Nombre
          </label>

          <input
            v-model="nombre"
            type="text"
            placeholder="Ingresa el nombre"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Rol -->
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">
            Rol
          </label>

          <select
            v-model="rol"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          >
            <option value="">
              Selecciona un rol
            </option>

            <option>
              Administrador
            </option>

            <option>
              Cajero
            </option>

            <option>
              Encargado
            </option>
          </select>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3">
          <button
            @click="cerrarModal"
            class="px-5 py-3 border border-gray-300 rounded-xl"
          >
            Cancelar
          </button>

          <button
            @click="guardarUsuario"
            class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl"
          >
            {{
              editandoId
                ? 'Actualizar'
                : 'Guardar Usuario'
            }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const mostrarModal = ref(false)

const nombre = ref('')
const rol = ref('')

const editandoId = ref(null)

const usuarios = ref([
  {
    id: 1,
    nombre: 'Administrador',
    rol: 'Administrador'
  },
  {
    id: 2,
    nombre: 'María López',
    rol: 'Cajero'
  },
  {
    id: 3,
    nombre: 'Juan Pérez',
    rol: 'Encargado'
  },
  {
    id: 4,
    nombre: 'Ana Martínez',
    rol: 'Cajero'
  }
])

/* Abrir modal */
function abrirModal() {
  mostrarModal.value = true
}

/* Guardar */
function guardarUsuario() {
  if (
    nombre.value.trim() === '' ||
    rol.value.trim() === ''
  ) {
    alert('Completa todos los campos')
    return
  }

  if (editandoId.value) {
    const index = usuarios.value.findIndex(
      usuario => usuario.id === editandoId.value
    )

    usuarios.value[index] = {
      ...usuarios.value[index],
      nombre: nombre.value,
      rol: rol.value
    }
  } else {
    usuarios.value.push({
      id: usuarios.value.length + 1,
      nombre: nombre.value,
      rol: rol.value
    })
  }

  cerrarModal()
}

/* Editar */
function editarUsuario(usuario) {
  mostrarModal.value = true

  editandoId.value = usuario.id

  nombre.value = usuario.nombre
  rol.value = usuario.rol
}

/* Eliminar */
function eliminarUsuario(id) {
  usuarios.value = usuarios.value.filter(
    usuario => usuario.id !== id
  )
}

/* Cerrar modal */
function cerrarModal() {
  mostrarModal.value = false

  editandoId.value = null

  nombre.value = ''
  rol.value = ''
}
</script>