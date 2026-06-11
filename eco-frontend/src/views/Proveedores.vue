<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Proveedores</h1>
        <p class="text-sm text-gray-500">Directorio y altas de proveedores</p>
      </div>
      <button @click="abrirNuevoProveedor" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-[#8bc0bd]">
        <i class="bi bi-plus-lg"></i>
        Nuevo Proveedor
      </button>
    </div>

    <div class="mb-4 grid gap-3 rounded-lg border border-gray-200 bg-white p-3 md:grid-cols-[1fr_180px_180px_auto]">
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input v-model="busqueda" type="text" placeholder="Buscar por nombre, telefono o correo..." class="h-10 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#9FCFCC]/50" />
      </div>
      <input v-model="fechaInicio" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <input v-model="fechaFin" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <button @click="cargarProveedores" class="h-10 rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">
        Filtrar
      </button>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] table-fixed">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase text-gray-600">
              <th class="w-[8%] px-4 py-3">ID</th>
              <th class="w-[24%] px-4 py-3">Nombre</th>
              <th class="w-[17%] px-4 py-3">Telefono</th>
              <th class="w-[22%] px-4 py-3">Correo</th>
              <th class="w-[17%] px-4 py-3">Registro</th>
              <th class="w-[12%] px-4 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="proveedor in proveedoresFiltrados" :key="proveedor.id" class="text-sm transition hover:bg-[#9FCFCC]/10">
              <td class="px-4 py-4 font-semibold text-gray-900">{{ proveedor.id }}</td>
              <td class="px-4 py-4 font-medium text-gray-900">{{ proveedor.nombre }}</td>
              <td class="px-4 py-4 text-gray-700">{{ proveedor.telefono || 'Sin telefono' }}</td>
              <td class="px-4 py-4 text-gray-700">{{ proveedor.email || 'Sin correo' }}</td>
              <td class="px-4 py-4 text-gray-700">{{ formatoFecha(proveedor.created_at) }}</td>
              <td class="px-4 py-4 text-right">
                <button @click="editarProveedor(proveedor)" class="mr-3 text-blue-600 hover:text-blue-800" title="Editar"><i class="bi bi-pencil-square"></i></button>
                <button @click="eliminarProveedor(proveedor.id)" class="text-red-500 hover:text-red-700" title="Desactivar"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr v-if="!cargando && proveedoresFiltrados.length === 0">
              <td colspan="6" class="py-12 text-center text-gray-400">No hay proveedores registrados</td>
            </tr>
            <tr v-if="cargando">
              <td colspan="6" class="py-10 text-center text-gray-500">Cargando proveedores...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="mostrarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 p-4 backdrop-blur-sm">
      <div class="w-full max-w-2xl rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <h2 class="text-lg font-bold text-gray-950">{{ editandoId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</h2>
          <button @click="cerrarModal" class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100"><i class="bi bi-x-lg"></i></button>
        </div>
        <form @submit.prevent="guardarProveedor" class="grid gap-4 p-5 md:grid-cols-2">
          <label class="md:col-span-2">
            <span class="mb-1 block text-sm font-semibold text-gray-700">Nombre</span>
            <input v-model="form.nombre" type="text" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Telefono</span>
            <input v-model="form.telefono" type="text" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Correo</span>
            <input v-model="form.email" type="email" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label class="md:col-span-2">
            <span class="mb-1 block text-sm font-semibold text-gray-700">Direccion</span>
            <input v-model="form.direccion" type="text" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <div class="flex justify-end gap-3 md:col-span-2">
            <button type="button" @click="cerrarModal" class="h-10 rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="submit" :disabled="guardando" class="h-10 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm hover:bg-[#8bc0bd] disabled:opacity-50">{{ guardando ? 'Guardando...' : 'Guardar' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import { createProveedor, deleteProveedor, getProveedores, updateProveedor } from '../services/proveedorService'

const proveedores = ref([])
const busqueda = ref('')
const fechaInicio = ref('')
const fechaFin = ref('')
const cargando = ref(false)
const guardando = ref(false)
const mostrarModal = ref(false)
const editandoId = ref(null)
const form = ref(modeloFormulario())

const proveedoresFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()
  if (!texto) return proveedores.value
  return proveedores.value.filter(proveedor => `${proveedor.nombre} ${proveedor.telefono || ''} ${proveedor.email || ''}`.toLowerCase().includes(texto))
})

onMounted(cargarProveedores)

function modeloFormulario() {
  return { nombre: '', telefono: '', email: '', direccion: '' }
}

async function cargarProveedores() {
  cargando.value = true
  try {
    proveedores.value = await getProveedores({
      fecha_inicio: fechaInicio.value || undefined,
      fecha_fin: fechaFin.value || undefined
    })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudieron cargar los proveedores', 'error')
  } finally {
    cargando.value = false
  }
}

function abrirNuevoProveedor() {
  editandoId.value = null
  form.value = modeloFormulario()
  mostrarModal.value = true
}

function editarProveedor(proveedor) {
  editandoId.value = proveedor.id
  form.value = {
    nombre: proveedor.nombre || '',
    telefono: proveedor.telefono || '',
    email: proveedor.email || '',
    direccion: proveedor.direccion || ''
  }
  mostrarModal.value = true
}

async function guardarProveedor() {
  if (!form.value.nombre.trim()) {
    Swal.fire('Nombre requerido', 'Ingresa el nombre del proveedor', 'warning')
    return
  }
  guardando.value = true
  try {
    if (editandoId.value) await updateProveedor(editandoId.value, form.value)
    else await createProveedor(form.value)
    await cargarProveedores()
    cerrarModal()
    Swal.fire({ icon: 'success', title: 'Proveedor guardado', timer: 1200, showConfirmButton: false })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo guardar el proveedor', 'error')
  } finally {
    guardando.value = false
  }
}

async function eliminarProveedor(id) {
  const result = await Swal.fire({ title: 'Eliminar proveedor', text: 'Esta accion no se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar' })
  if (!result.isConfirmed) return
  await deleteProveedor(id)
  await cargarProveedores()
}

function cerrarModal() {
  mostrarModal.value = false
  editandoId.value = null
  form.value = modeloFormulario()
}

function formatoFecha(fecha) {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-SV')
}
</script>
