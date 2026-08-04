<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-800">
            Proveedores
          </h1>
          <p>Sección de proveedores</p>
          
        </div>

       <button
        @click="abrirNuevoProveedor"
        class="group relative overflow-hidden bg-[#5B80B0] hover:bg-[#5B80B0] text-white px-7 py-3 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 flex items-center gap-3 font-semibold"
      >
        <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>

        <i class="bi bi-plus-circle-fill text-lg"></i>

        <span>Nuevo proveedor</span>
      </button>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
              <tr class="text-gray-600 text-sm uppercase">
                <th class="px-6 py-5 text-left">ID</th>
                <th class="px-6 py-5 text-left">Nombre</th>
                <th class="px-6 py-5 text-left">Teléfono</th>
                <th class="px-6 py-5 text-left">Dirección</th>
                <th class="px-6 py-5 text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="proveedor in proveedores"
                :key="proveedor.id"
                class="border-b border-gray-100 hover:bg-[#5B80B0]/10 transition-all duration-200"
              >
                <td class="px-6 py-5">
                  <span
                    class="bg-[#5B80B0]/10 text-[#5B80B0] px-3 py-1 rounded-full text-xs font-bold"
                  >
                    #{{ proveedor.id }}
                  </span>
                </td>

                <td class="px-6 py-5 font-medium text-gray-800">
                  {{ proveedor.nombre_proveedor }}
                </td>

                <td class="px-6 py-5 text-gray-600">
                  {{ proveedor.telefono }}
                </td>

                <td class="px-6 py-5 text-gray-600">
                  {{ proveedor.direccion }}
                </td>

                <td class="px-6 py-5">
                  <div class="flex justify-center gap-3">
                    <button
                      @click="editarProveedor(proveedor)"
                      class="w-10 h-10 rounded-full border border-blue-300 bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all flex items-center justify-center"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>

                    <button
                      @click="eliminarProveedor(proveedor.id)"
                      class="w-10 h-10 rounded-full border border-red-300 bg-red-50 text-red-500 hover:bg-red-100 transition-all flex items-center justify-center"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="proveedores.length === 0">
                <td colspan="5" class="py-16 text-center">
                  <div class="flex flex-col items-center">
                    <i class="bi bi-folder-x text-5xl text-gray-300"></i>
                    <p class="mt-3 text-gray-400">
                      No hay proveedores registrados
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

   <!-- Modal -->
<div
  v-if="mostrarModal"
  class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
>
  <div
    class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fadeIn"
  >

    
    <div class="bg-[#5B80B0] text-white px-8 py-5 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold">
          {{ editandoId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
        </h2>
        <p class="text-white/70 text-sm">
          Complete la información del proveedor
        </p>
      </div>

      <button
        @click="cerrarModal"
        class="w-10 h-10 rounded-full hover:bg-white/20 transition flex items-center justify-center"
      >
        <i class="bi bi-x-lg text-xl"></i>
      </button>
    </div>

    <!-- Contenido -->
    <div class="p-8 space-y-6">

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="bi bi-person-badge mr-2 text-[#5B80B0]"></i>
          Nombre del proveedor
        </label>

        <input
          v-model="nombre"
          type="text"
          placeholder="Ingrese el nombre"
          class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#5B80B0] focus:border-[#5B80B0] outline-none transition"
        />
      </div>

      <!-- Teléfono -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="bi bi-telephone mr-2 text-[#5B80B0]"></i>
          Teléfono
        </label>

        <input
          v-model="telefono"
          type="text"
          maxlength="8"
          placeholder="Ej: 77778888"
          @input="telefono = telefono.replace(/[^0-9]/g, '')"
          class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#5B80B0] focus:border-[#5B80B0] outline-none transition"
        />
      </div>

      <!-- Dirección -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="bi bi-geo-alt mr-2 text-[#5B80B0]"></i>
          Dirección
        </label>

        <textarea
          v-model="direccion"
          rows="3"
          placeholder="Ingrese la dirección"
          class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#5B80B0] focus:border-[#5B80B0] outline-none resize-none transition"
        ></textarea>
      </div>

    </div>

    
    <div class="bg-gray-50 px-8 py-5 flex justify-end gap-3 border-t">

      <button
        @click="cerrarModal"
        class="px-6 py-3 rounded-xl border border-gray-300 hover:bg-gray-100 font-medium transition"
      >
        Cancelar
      </button>

      <button
        @click="guardarProveedor"
        class="px-6 py-3 rounded-xl bg-[#5B80B0] text-white font-semibold hover:bg-[#5B80B0] shadow-lg hover:shadow-xl transition"
      >
        <i class="bi bi-check-circle me-2"></i>
        {{ editandoId ? 'Actualizar' : 'Guardar Proveedor' }}
      </button>

    </div>

  </div>
</div>
  </div>
</template>

<script setup>

import { ref, onMounted } from 'vue'
import Swal from 'sweetalert2'
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

// Cargar proveedores
const cargarProveedores = async () => {
  try {
    proveedores.value = await getProveedores()
  } catch (error) {
    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al cargar proveedores'
    })
  }
}

onMounted(() => {
  cargarProveedores()
})

// Abrir modal nuevo
function abrirNuevoProveedor() {
  editandoId.value = null

  nombre.value = ''
  telefono.value = ''
  direccion.value = ''

  mostrarModal.value = true
}

// Guardar y actualizar
async function guardarProveedor() {

  // Nombre
  if (!nombre.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese el nombre del proveedor',
      'warning'
    )
    return
  }

  if (nombre.value.trim().length < 3) {
    Swal.fire(
      'Nombre inválido',
      'El nombre debe tener al menos 3 caracteres',
      'warning'
    )
    return
  }

  // Teléfono
  if (!telefono.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese el teléfono',
      'warning'
    )
    return
  }

  if (!/^\d+$/.test(telefono.value)) {
    Swal.fire(
      'Teléfono inválido',
      'El teléfono solo debe contener números',
      'warning'
    )
    return
  }

  if (telefono.value.length !== 8) {
    Swal.fire(
      'Teléfono inválido',
      'El teléfono debe tener 8 dígitos',
      'warning'
    )
    return
  }

  // Dirección
  if (!direccion.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese la dirección',
      'warning'
    )
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

      Swal.fire({
        icon: 'success',
        title: 'Proveedor actualizado',
        timer: 1500,
        showConfirmButton: false
      })

    } else {

      await createProveedor(data)

      Swal.fire({
        icon: 'success',
        title: 'Proveedor agregado correctamente',
        timer: 1500,
        showConfirmButton: false
      })
    }

    await cargarProveedores()
    cerrarModal()

  } catch (error) {

    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text:
        error.response?.data?.message ||
        'No se pudo guardar el proveedor'
    })
  }
}

// Editar
function editarProveedor(proveedor) {

  mostrarModal.value = true

  editandoId.value = proveedor.id

  nombre.value = proveedor.nombre_proveedor
  telefono.value = proveedor.telefono
  direccion.value = proveedor.direccion
}

// Eliminar
async function eliminarProveedor(id) {

  const result = await Swal.fire({
    title: '¿Eliminar proveedor?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#5B80B0',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  })

  if (!result.isConfirmed) return

  try {

    await deleteProveedor(id)

    await cargarProveedores()

    Swal.fire({
      icon: 'success',
      title: 'Proveedor eliminado',
      text: 'El proveedor fue eliminado correctamente',
      timer: 1500,
      showConfirmButton: false
    })

  } catch (error) {

    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo eliminar el proveedor'
    })
  }
}


function cerrarModal() {

  mostrarModal.value = false

  editandoId.value = null

  nombre.value = ''
  telefono.value = ''
  direccion.value = ''
}

</script>