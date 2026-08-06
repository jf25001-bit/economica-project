<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-4xl font-bold text-gray-800">Usuarios</h1>
        <p class="text-gray-500">Gestión de usuarios y roles</p>
      </div>

      <button
        @click="abrirModal"
        class="bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-6 py-3 rounded-2xl shadow-lg transition"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nuevo Usuario
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-5 mb-6 flex flex-wrap gap-4">
      <input
        v-model="search"
        type="text"
        placeholder="Buscar usuario..."
        class="flex-1 px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#47B5AC]"
      />

      <select
        v-model="filtroRol"
        class="px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#47B5AC]"
      >
        <option value="">Todos los roles</option>
        <option v-for="r in roles" :key="r.id" :value="r.id">
          {{ r.nombre }}
        </option>
      </select>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] table-fixed">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-4 text-left">Nombre</th>
              <th class="px-6 py-4 text-left">Apellidos</th>
              <th class="px-6 py-4 text-left">Correo</th>
              <th class="px-6 py-4 text-left">Rol</th>
              <th class="px-6 py-4 text-left">Estado</th>
              <th class="px-6 py-4 text-left">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="u in usuariosFiltrados"
              :key="u.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-6 py-4 font-medium truncate">{{ u.name }}</td>
              <td class="px-6 py-4 truncate">{{ u.apellido || 'Sin apellidos' }}</td>
              <td class="px-6 py-4 truncate">{{ u.email || 'Sin correo' }}</td>
              <td class="px-6 py-4 truncate">{{ u.rol?.nombre || 'Sin rol' }}</td>
              <td class="px-6 py-4">
                <span :class="u.activo ? 'text-green-600' : 'text-red-600'">
                  {{ u.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button
                    @click="editar(u)"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                    title="Editar"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>

                  <button
                    @click="abrirEliminar(u)"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition"
                    title="Eliminar"
                  >
                    <i class="bi bi-trash"></i>
                  </button>

                  <button
                    @click="toggleEstado(u)"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition"
                    title="Cambiar estado"
                  >
                    <i class="bi bi-power"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="usuariosFiltrados.length === 0">
              <td colspan="6" class="py-16 text-center text-gray-400 italic">
                No hay usuarios registrados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="modal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-3xl w-full max-w-2xl flex flex-col overflow-hidden">
        <div class="bg-[#47B5AC] text-white px-6 py-5 flex justify-between items-center">
          <h2 class="text-xl font-bold">
            {{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h2>

          <button
            @click="cerrar"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition text-white"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <input
                v-model="form.name"
                placeholder="Nombre"
                class="box-border w-full h-10 px-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
              />
              <p v-if="errores.name" class="text-red-500 text-sm mt-1">{{ errores.name }}</p>
            </div>

            <div>
              <input
                v-model="form.apellido"
                placeholder="Apellidos"
                class="box-border w-full h-10 px-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
              />
              <p v-if="errores.apellido" class="text-red-500 text-sm mt-1">{{ errores.apellido }}</p>
            </div>
          </div>

          <div>
            <input
              v-model="form.email"
              type="email"
              placeholder="Correo electrónico"
              class="box-border w-full h-10 px-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
            />
            <p v-if="errores.email" class="text-red-500 text-sm mt-1">{{ errores.email }}</p>
          </div>

          <div>
            <input
              v-model="form.password"
              type="password"
              :placeholder="editando ? 'Contraseña nueva (opcional)' : 'Contraseña'"
              class="box-border w-full h-10 px-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
            />
            <p v-if="errores.password" class="text-red-500 text-sm mt-1">{{ errores.password }}</p>
          </div>

          <div>
            <select
              v-model="form.rol_id"
              class="box-border w-full h-10 px-4 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
            >
              <option value="">Selecciona rol</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">
                {{ r.nombre }}
              </option>
            </select>
            <p v-if="errores.rol_id" class="text-red-500 text-sm mt-1">{{ errores.rol_id }}</p>
          </div>
        </div>

        <div class="flex justify-end gap-3 p-4 bg-gray-50">
          <button @click="cerrar" class="px-4 py-2 border rounded-xl">
            Cancelar
          </button>

          <button
            @click="guardar"
            :disabled="loading"
            class="px-4 py-2 bg-[#47B5AC] text-white rounded-xl disabled:opacity-50"
          >
            {{ loading ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Swal from 'sweetalert2'
import { getUsuarios, createUsuario, updateUsuario, deleteUsuario } from '@/services/usuarioService'
import { getRoles } from '@/services/rolService'

const usuarios = ref([])
const roles = ref([])
const search = ref('')
const filtroRol = ref('')
const modal = ref(false)
const editando = ref(false)
const loading = ref(false)

const formVacio = () => ({
  id: null,
  name: '',
  apellido: '',
  email: '',
  password: '',
  rol_id: '',
  activo: true
})

const form = ref(formVacio())
const errores = ref({})

const cargar = async () => {
  usuarios.value = await getUsuarios()
  roles.value = await getRoles()
}

onMounted(cargar)

const usuariosFiltrados = computed(() => {
  const query = search.value.toLowerCase().trim()

  return usuarios.value.filter(u => {
    const texto = [
      u.name,
      u.apellido,
      u.email
    ].join(' ').toLowerCase()
    const matchName = !query || texto.includes(query)
    const matchRol = filtroRol.value === '' || u.rol_id == filtroRol.value
    return matchName && matchRol
  })
})

const abrirModal = () => {
  modal.value = true
  editando.value = false
  form.value = formVacio()
  errores.value = {}
}

const cerrar = () => {
  modal.value = false
}

const validar = () => {
  errores.value = {}
  let ok = true
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

  if (!form.value.name.trim()) {
    errores.value.name = 'El nombre es obligatorio'
    ok = false
  }

  if (!form.value.apellido.trim()) {
    errores.value.apellido = 'Los apellidos son obligatorios'
    ok = false
  }

  if (!form.value.email.trim()) {
    errores.value.email = 'El correo electrónico es obligatorio'
    ok = false
  } else if (!emailRegex.test(form.value.email)) {
    errores.value.email = 'Ingrese un correo electrónico válido'
    ok = false
  }

  if (!editando.value && (!form.value.password || form.value.password.length < 8)) {
    errores.value.password = 'La contraseña debe tener mínimo 8 caracteres'
    ok = false
  }

  if (editando.value && form.value.password && form.value.password.length < 8) {
    errores.value.password = 'La contraseña debe tener mínimo 8 caracteres'
    ok = false
  }

  if (!form.value.rol_id) {
    errores.value.rol_id = 'Selecciona un rol'
    ok = false
  }

  return ok
}

const guardar = async () => {
  if (!validar()) return

  loading.value = true

  try {
    if (editando.value) {
      await updateUsuario(form.value.id, form.value)
      await Swal.fire({ icon: 'success', title: 'Usuario actualizado', timer: 1500, showConfirmButton: false })
    } else {
      await createUsuario(form.value)
      await Swal.fire({ icon: 'success', title: 'Usuario creado correctamente', timer: 1500, showConfirmButton: false })
    }

    cerrar()
    await cargar()
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'No se pudo guardar el usuario', 'error')
  } finally {
    loading.value = false
  }
}

const editar = (u) => {
  form.value = {
    id: u.id,
    name: u.name || '',
    apellido: u.apellido || '',
    email: u.email || '',
    password: '',
    rol_id: u.rol_id || '',
    activo: Boolean(u.activo)
  }
  editando.value = true
  errores.value = {}
  modal.value = true
}

const abrirEliminar = async (u) => {
  const result = await Swal.fire({
    title: '¿Eliminar usuario?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#47B5AC',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar'
  })

  if (!result.isConfirmed) return

  try {
    await deleteUsuario(u.id)
    await cargar()
    await Swal.fire({ icon: 'success', title: 'Usuario eliminado', timer: 1500, showConfirmButton: false })
  } catch (error) {
    Swal.fire('Error', 'No se pudo eliminar el usuario', 'error')
  }
}

const toggleEstado = async (u) => {
  await updateUsuario(u.id, {
    ...u,
    apellido: u.apellido || '',
    email: u.email || '',
    activo: !u.activo,
    password: ''
  })
  cargar()
}
</script>
