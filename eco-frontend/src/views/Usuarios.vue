<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-8">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-4xl font-bold text-gray-800">Usuarios</h1>
        <p class="text-gray-500">Gestión de usuarios y roles</p>
      </div>

      <button
        @click="abrirModal"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-6 py-3 rounded-2xl shadow-lg transition"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nuevo Usuario
      </button>
    </div>

    <!-- FILTROS -->
    <div class="bg-white rounded-2xl shadow-md p-5 mb-6 flex flex-wrap gap-4">

      <input
        v-model="search"
        type="text"
        placeholder="Buscar usuario..."
        class="flex-1 px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#46674A]"
      />

      <select
        v-model="filtroRol"
        class="px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#46674A]"
      >
        <option value="">Todos los roles</option>
        <option v-for="r in roles" :key="r.id" :value="r.id">
          {{ r.nombre }}
        </option>
      </select>

    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-3xl shadow-xl overflow-x-hidden">

      <table class="w-full table-fixed">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-4 text-left">Nombre</th>
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
            <td class="px-6 py-4 font-medium">
              {{ u.name }}
            </td>

            <td class="px-6 py-4">
              {{ u.rol?.nombre || 'Sin rol' }}
            </td>

            <td class="px-6 py-4">
              <span :class="u.activo ? 'text-green-600' : 'text-red-600'">
                {{ u.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>

            <!-- ACCIONES REDONDAS -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">

                <button
                  @click="editar(u)"
                  class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                >
                  <i class="bi bi-pencil"></i>
                </button>

                <button
                  @click="eliminar(u.id)"
                  class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition"
                >
                  <i class="bi bi-trash"></i>
                </button>

                <button
                  @click="toggleEstado(u)"
                  class="w-9 h-9 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition"
                >
                  <i class="bi bi-power"></i>
                </button>

              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL USUARIO -->
   <div v-if="modal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 overflow-hidden">

     <div class="bg-white rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- HEADER -->
        <div class="bg-[#46674A] text-white px-6 py-5 flex justify-between items-center">
          <h2 class="text-xl font-bold">
            {{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h2>

          <button
            @click="cerrar"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition"
          >
            ✖
          </button>
        </div>

        <!-- BODY -->
        <div class="p-6 space-y-4 overflow-y-auto flex-1">

          <input
            v-model="form.name"
            placeholder="Nombre"
            class="w-full px-1 py-3 border rounded-2xl"
          />

          <input
            v-model="form.password"
            type="password"
            placeholder="Contraseña"
            class="w-full px-1 py-3 border rounded-2xl"
          />

          <select
            v-model="form.rol_id"
            class="w-full px-3 py-3 border rounded-2xl"
          >
            <option value="">Selecciona rol</option>
            <option v-for="r in roles" :key="r.id" :value="r.id">
              {{ r.nombre }}
            </option>
          </select>

        </div>

        <!-- FOOTER -->
      <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 flex-wrap">

          <button
            @click="cerrar"
            class="px-5 py-2 border rounded-xl"
          >
            Cancelar
          </button>

          <button
            @click="guardar"
            class="px-5 py-2 bg-[#46674A] text-white rounded-xl"
          >
            Guardar
          </button>

        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getUsuarios, createUsuario, updateUsuario, deleteUsuario } from '@/services/usuarioService'
import { getRoles } from '@/services/rolService'

const usuarios = ref([])
const roles = ref([])

const search = ref('')
const filtroRol = ref('')

const modal = ref(false)
const editando = ref(false)

const form = ref({
  id: null,
  name: '',
  password: '',
  rol_id: ''
})

const cargar = async () => {
  usuarios.value = await getUsuarios()
  roles.value = await getRoles()
}

onMounted(cargar)

const usuariosFiltrados = computed(() => {
  return usuarios.value.filter(u => {
    const nombre = u.name?.toLowerCase() || ''
    const matchName = nombre.includes(search.value.toLowerCase())
    const matchRol = filtroRol.value === '' || u.rol_id == filtroRol.value
    return matchName && matchRol
  })
})

const abrirModal = () => {
  modal.value = true
  editando.value = false
  form.value = { id: null, name: '', password: '', rol_id: '' }
}

const cerrar = () => modal.value = false

const guardar = async () => {
  if (editando.value) {
    await updateUsuario(form.value.id, form.value)
  } else {
    await createUsuario(form.value)
  }
  cerrar()
  cargar()
}

const editar = (u) => {
  form.value = { ...u, password: '' }
  editando.value = true
  modal.value = true
}

const eliminar = async (id) => {
  await deleteUsuario(id)
  cargar()
}

const toggleEstado = async (u) => {
  await updateUsuario(u.id, {
    ...u,
    activo: !u.activo,
    password: ''
  })
  cargar()
}
</script>