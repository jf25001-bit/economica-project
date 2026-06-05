<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          Proveedores
        </h1>
      </div>

      <button
        @click="abrirNuevoProveedor"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nuevo Proveedor
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100">
          <tr class="text-left text-gray-700">
            <th class="px-6 py-4 font-semibold">ID</th>
            <th class="px-6 py-4 font-semibold">Nombre</th>
            <th class="px-6 py-4 font-semibold">Teléfono</th>
            <th class="px-6 py-4 font-semibold">Dirección</th>
            <th class="px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="proveedor in proveedores"
            :key="proveedor.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-6 py-4 font-semibold">
              {{ proveedor.id }}
            </td>

            <td class="px-6 py-4">
              {{ proveedor.nombre_proveedor }}
            </td>

            <td class="px-6 py-4">
              {{ proveedor.telefono }}
            </td>

            <td class="px-6 py-4">
              {{ proveedor.direccion }}
            </td>

            <td class="px-6 py-4">
              <div class="flex gap-4 text-lg">

                <!-- Editar -->
                <button
                  @click="editarProveedor(proveedor)"
                  class="text-blue-500 hover:text-blue-700"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>

                <!-- Eliminar -->
                <button
                  @click="eliminarProveedor(proveedor.id)"
                  class="text-red-500 hover:text-red-700"
                >
                  <i class="bi bi-trash"></i>
                </button>

              </div>
            </td>
          </tr>

          <!-- Vacío -->
          <tr v-if="proveedores.length === 0">
            <td
              colspan="5"
              class="text-center py-8 text-gray-500"
            >
              No hay proveedores registrados.
            </td>
          </tr>
        </tbody>
      </table>
    </div

    <!-- Modal -->
    <div
      v-if="mostrarModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-10"
      >
        <!-- Encabezado -->
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-2xl font-bold text-gray-800">
            {{
              editandoId
                ? 'Editar Proveedor'
                : 'Nuevo Proveedor'
            }}
          </h2>

          <button
            @click="cerrarModal"
            class="text-3xl text-red-500"
          >
            &times;
          </button>
        </div>

        <!-- Nombre -->
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-3">
            Nombre del proveedor
          </label>

          <input
            v-model="nombre"
            type="text"
            placeholder="Distribuidora Central"
            class="w-full px-1 py-3 border border-black-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Teléfono -->
        <div class="mb-5">
          <label class="block text-black-700 font-medium mb-2">
            Teléfono
          </label>

          <input
            v-model="telefono"
            type="text"
            placeholder="7777-7777"
            class="w-full px-1 py-3 border border-black-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Dirección -->
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">
            Dirección
          </label>

          <input
            v-model="direccion"
            type="text"
            placeholder="San Salvador"
            class="w-full px-1 py-3 border border-black-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
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
            @click="guardarProveedor"
            class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl"
          >
            {{
              editandoId
                ? 'Actualizar'
                : 'Guardar Proveedor'
            }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  getProveedores,
  createProveedor,
  updateProveedor,
  deleteProveedor
} from '../services/proveedorService'

const mostrarModal = ref(false)

const nombre = ref('')
const telefono = ref('')
const direccion = ref('')

const editandoId = ref(null)

const proveedores = ref([])

// Cargar proveedores al abrir pantalla
const cargarProveedores = async () => {
  try {
    proveedores.value =
      await getProveedores()
  } catch (error) {
    console.error(error)
    alert('Error al cargar proveedores')
  }
}

onMounted(() => {
  cargarProveedores()
})

// Abrir modal nuevo
function abrirNuevoProveedor() {
  editandoId.value = null
  mostrarModal.value = true
}

// Guardar / actualizar
async function guardarProveedor() {
  if (
    !nombre.value.trim() ||
    !telefono.value.trim() ||
    !direccion.value.trim()
  ) {
    alert('Completa todos los campos')
    return
  }

  const data = {
    nombre_proveedor: nombre.value,
    telefono: telefono.value,
    direccion: direccion.value
  }

  try {
    if (editandoId.value) {
      await updateProveedor(
        editandoId.value,
        data
      )
    } else {
      await createProveedor(data)
    }

    await cargarProveedores()
    cerrarModal()

  } catch (error) {
    console.error(error)

    alert(
      error.response?.data?.message ||
      'Error al guardar proveedor'
    )
  }
}

// Editar
function editarProveedor(proveedor) {
  mostrarModal.value = true

  editandoId.value = proveedor.id

  nombre.value =
    proveedor.nombre_proveedor

  telefono.value =
    proveedor.telefono

  direccion.value =
    proveedor.direccion
}

// Eliminar
async function eliminarProveedor(id) {
  const confirmar = confirm(
    '¿Eliminar este proveedor?'
  )

  if (!confirmar) return

  try {
    await deleteProveedor(id)
    await cargarProveedores()
  } catch (error) {
    console.error(error)
    alert('Error al eliminar')
  }
}

// Cerrar modal
function cerrarModal() {
  mostrarModal.value = false

  editandoId.value = null

  nombre.value = ''
  telefono.value = ''
  direccion.value = ''
}
</script>