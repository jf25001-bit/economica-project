<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Usuarios</h1>
        <p class="text-sm text-gray-500">Cuentas, roles y accesos por apartado</p>
      </div>
      <button @click="abrirModal" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm hover:bg-[#8bc0bd]">
        <i class="bi bi-person-plus"></i>
        Nuevo Usuario
      </button>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3">
      <div class="relative">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input v-model="busqueda" @keyup.enter="cargarUsuarios" type="text" placeholder="Filtrar por nombre..." class="h-10 w-full rounded-md bg-gray-100 pl-9 pr-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-[#9FCFCC]/50" />
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase text-gray-600">
              <th class="w-[22%] px-4 py-3">Nombre</th>
              <th class="w-[24%] px-4 py-3">Correo</th>
              <th class="w-[16%] px-4 py-3">Rol</th>
              <th class="w-[14%] px-4 py-3">Estado</th>
              <th class="w-[14%] px-4 py-3">Accesos</th>
              <th class="w-[10%] px-4 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="usuario in usuarios" :key="usuario.id" class="text-sm transition hover:bg-[#9FCFCC]/10">
              <td class="px-4 py-4 font-medium text-gray-900">{{ usuario.name }}</td>
              <td class="px-4 py-4 text-gray-700">{{ usuario.email }}</td>
              <td class="px-4 py-4 text-gray-700">{{ usuario.rol?.nombre || 'Sin rol' }}</td>
              <td class="px-4 py-4">
                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="usuario.activo ? 'bg-[#9FCFCC]/40 text-gray-800' : 'bg-red-100 text-red-700'">
                  {{ usuario.activo ? 'Activo' : 'Bloqueado' }}
                </span>
              </td>
              <td class="px-4 py-4 text-gray-700">{{ accesosResumen(usuario.permisos) }}</td>
              <td class="px-4 py-4 text-right">
                <button @click="editarUsuario(usuario)" class="mr-3 text-blue-600 hover:text-blue-800" title="Editar"><i class="bi bi-pencil-square"></i></button>
                <button @click="eliminarUsuario(usuario.id)" class="text-red-500 hover:text-red-700" title="Bloquear"><i class="bi bi-lock"></i></button>
              </td>
            </tr>
            <tr v-if="!cargando && usuarios.length === 0">
              <td colspan="6" class="py-12 text-center text-gray-400">No hay usuarios registrados</td>
            </tr>
            <tr v-if="cargando">
              <td colspan="6" class="py-10 text-center text-gray-500">Cargando usuarios...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="mostrarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 p-4 backdrop-blur-sm">
      <div class="w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <div>
            <h2 class="text-lg font-bold text-gray-950">{{ editandoId ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
            <p class="text-xs text-gray-500">El administrador decide que apartados puede ver</p>
          </div>
          <button @click="cerrarModal" class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100"><i class="bi bi-x-lg"></i></button>
        </div>

        <form @submit.prevent="guardarUsuario" class="grid max-h-[78vh] gap-4 overflow-y-auto p-5 md:grid-cols-2">
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Nombre</span>
            <input v-model="form.name" type="text" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Correo</span>
            <input v-model="form.email" type="email" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Contraseña {{ editandoId ? '(opcional)' : '' }}</span>
            <input v-model="form.password" type="password" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
          </label>
          <label>
            <span class="mb-1 block text-sm font-semibold text-gray-700">Rol</span>
            <select v-model="form.rol_id" class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40">
              <option value="">Sin rol</option>
              <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.nombre }}</option>
            </select>
          </label>
          <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 md:col-span-2">
            <input v-model="form.activo" type="checkbox" class="h-4 w-4 accent-[#9FCFCC]" />
            <span class="text-sm font-semibold text-gray-700">Usuario activo</span>
          </label>

          <div class="rounded-lg border border-gray-200 md:col-span-2">
            <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
              <p class="text-sm font-bold text-gray-800">Permisos de vista</p>
            </div>
            <div class="grid gap-2 p-4 md:grid-cols-3">
              <label v-for="apartado in apartados" :key="apartado.key" class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 text-sm">
                <span class="font-medium text-gray-700"><i :class="[apartado.icon, 'mr-2 text-gray-500']"></i>{{ apartado.label }}</span>
                <input v-model="form.permisos" :value="apartado.key" type="checkbox" class="h-4 w-4 accent-[#9FCFCC]" />
              </label>
            </div>
          </div>

          <div class="flex justify-end gap-3 md:col-span-2">
            <button type="button" @click="cerrarModal" class="h-10 rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="submit" :disabled="guardando" class="h-10 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm hover:bg-[#8bc0bd] disabled:opacity-50">{{ guardando ? 'Guardando...' : 'Guardar Usuario' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import Swal from 'sweetalert2'
import { getRoles } from '../services/rolService'
import { createUsuario, deleteUsuario, getUsuarios, updateUsuario } from '../services/userService'

const apartados = [
  { key: 'dashboard', label: 'Inicio', icon: 'bi bi-house-door' },
  { key: 'categorias', label: 'Categorias', icon: 'bi bi-grid' },
  { key: 'productos', label: 'Productos', icon: 'bi bi-box-seam' },
  { key: 'inventario', label: 'Inventario', icon: 'bi bi-archive' },
  { key: 'compras', label: 'Compras', icon: 'bi bi-basket2' },
  { key: 'proveedores', label: 'Proveedores', icon: 'bi bi-building' },
  { key: 'ventas', label: 'Ventas', icon: 'bi bi-cash-coin' },
  { key: 'usuarios', label: 'Usuarios', icon: 'bi bi-person-badge' },
  { key: 'reportes', label: 'Reportes', icon: 'bi bi-bar-chart-line' }
]

const usuarios = ref([])
const roles = ref([])
const busqueda = ref('')
const cargando = ref(false)
const guardando = ref(false)
const mostrarModal = ref(false)
const editandoId = ref(null)
const form = ref(modeloFormulario())
let filtroTimer

watch(busqueda, () => {
  clearTimeout(filtroTimer)
  filtroTimer = setTimeout(cargarUsuarios, 350)
})

onMounted(async () => {
  await Promise.all([cargarUsuarios(), cargarRoles()])
})

function modeloFormulario() {
  return {
    name: '',
    email: '',
    password: '',
    rol_id: '',
    activo: true,
    permisos: apartados.map(apartado => apartado.key)
  }
}

async function cargarUsuarios() {
  cargando.value = true
  try {
    usuarios.value = await getUsuarios({ search: busqueda.value || undefined })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudieron cargar los usuarios', 'error')
  } finally {
    cargando.value = false
  }
}

async function cargarRoles() {
  try {
    roles.value = await getRoles()
  } catch {
    roles.value = []
  }
}

function abrirModal() {
  editandoId.value = null
  form.value = modeloFormulario()
  mostrarModal.value = true
}

function editarUsuario(usuario) {
  editandoId.value = usuario.id
  form.value = {
    name: usuario.name || '',
    email: usuario.email || '',
    password: '',
    rol_id: usuario.rol_id || '',
    activo: usuario.activo !== false,
    permisos: Array.isArray(usuario.permisos) ? usuario.permisos : []
  }
  mostrarModal.value = true
}

async function guardarUsuario() {
  if (!form.value.name.trim() || !form.value.email.trim() || (!editandoId.value && !form.value.password.trim())) {
    Swal.fire('Campos requeridos', 'Completa nombre, correo y contraseña', 'warning')
    return
  }

  guardando.value = true
  try {
    const payload = { ...form.value, rol_id: form.value.rol_id || null }
    if (editandoId.value && !payload.password) delete payload.password
    if (editandoId.value) await updateUsuario(editandoId.value, payload)
    else await createUsuario(payload)
    await cargarUsuarios()
    cerrarModal()
    Swal.fire({ icon: 'success', title: 'Usuario guardado', timer: 1200, showConfirmButton: false })
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo guardar el usuario', 'error')
  } finally {
    guardando.value = false
  }
}

async function eliminarUsuario(id) {
  const result = await Swal.fire({ title: 'Bloquear usuario', text: 'El usuario ya no quedara activo', icon: 'warning', showCancelButton: true, confirmButtonText: 'Bloquear', cancelButtonText: 'Cancelar' })
  if (!result.isConfirmed) return
  await deleteUsuario(id)
  await cargarUsuarios()
}

function cerrarModal() {
  mostrarModal.value = false
  editandoId.value = null
  form.value = modeloFormulario()
}

function accesosResumen(permisos) {
  if (!Array.isArray(permisos) || permisos.length === 0) return 'Sin accesos'
  if (permisos.length === apartados.length) return 'Todos'
  return `${permisos.length} apartados`
}
</script>
