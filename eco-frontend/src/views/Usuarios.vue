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
        class="bg-[#5B80B0] hover:bg-[#5B80B0] text-white px-6 py-3 rounded-2xl shadow-lg transition"
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
        class="flex-1 px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#5B80B0]"
      />

      <select
        v-model="filtroRol"
        class="px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-[#5B80B0]"
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
              <span :class="u.activo ? 'text-[#5B80B0]' : 'text-red-600'">
                {{ u.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>

            <!-- ACCIONES -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">

                <button
                  @click="editar(u)"
                  class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                >
                  <i class="bi bi-pencil"></i>
                </button>

                <button
                  @click="abrirEliminar(u)"
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
    <div v-if="modal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">

      <div class="bg-white rounded-3xl w-full max-w-xl flex flex-col">

        <!-- HEADER -->
        <div class="bg-[#5B80B0] text-white px-6 py-5 flex justify-between items-center">
          <h2 class="text-xl font-bold">
            {{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h2>

          <button
  @click="cerrar"
  class="w-9 h-9 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition text-white"
>
  ✖
</button>
        </div>

        <!-- BODY -->
        <div class="p-6 space-y-2">

          <input
            v-model="form.name"
            placeholder="Nombre"
            class="w-full px-1 py-2 border rounded-xl"
          />
          <p v-if="errores.name" class="text-red-500 text-sm">{{ errores.name }}</p>

          <input
            v-model="form.password"
            type="password"
            placeholder="Contraseña"
            class="w-full px-1 py-2 border rounded-xl"
          />
          <p v-if="errores.password" class="text-red-500 text-sm">{{ errores.password }}</p>

          <select
            v-model="form.rol_id"
            class="w-full px-3 py-2 border rounded-xl"
          >
            <option value="">Selecciona rol</option>
            <option v-for="r in roles" :key="r.id" :value="r.id">
              {{ r.nombre }}
            </option>
          </select>
          <p v-if="errores.rol_id" class="text-red-500 text-sm">{{ errores.rol_id }}</p>

        </div>

        <!-- FOOTER -->
        <div class="flex justify-end gap-3 p-4">

          <button @click="cerrar" class="px-4 py-2 border rounded-xl">
            Cancelar
          </button>

          <button
            @click="guardar"
            :disabled="loading"
            class="px-4 py-2 bg-[#5B80B0] text-white rounded-xl disabled:opacity-50"
          >
            {{ loading ? 'Guardando...' : 'Guardar' }}
          </button>

        </div>

      </div>
    </div>

    <!-- MODAL ELIMINAR -->
    <div v-if="modalEliminar" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

      <div class="bg-white p-6 w-[420px] rounded shadow-lg">

        <h2 class="text-xl font-bold mb-3">Eliminar usuario</h2>

        <p class="text-gray-600 mb-6">
          ¿Seguro que deseas eliminar este usuario?
        </p>

        <div class="flex justify-end gap-3">

          <button
            @click="modalEliminar = false"
            class="px-4 py-2 border rounded hover:bg-gray-50"
          >
            Cancelar
          </button>

          <button
            @click="confirmarEliminar"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
          >
            Eliminar
          </button>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
// Importación de funciones de Vue, alertas y servicios del backend
import { ref, computed, onMounted } from 'vue'
import Swal from 'sweetalert2'
import { getUsuarios, createUsuario, updateUsuario, deleteUsuario } from '@/services/usuarioService'
import { getRoles } from '@/services/rolService'
// Aquí se guardan los usuarios y roles
const usuarios = ref([])
const roles = ref([])
// Variables para buscar y filtrar
const search = ref('')
const filtroRol = ref('')
// Controla si el modal está abierto
const modal = ref(false)
// Sirve para saber si estamos editando o creando
const editando = ref(false)
// Muestra estado de carga
const loading = ref(false)

// Datos que se ingresan en el formulario
const form = ref({
  id: null,
  name: '',
  password: '',
  rol_id: ''
})

// Guarda los errores de validación
const errores = ref({
  name: '',
  password: '',
  rol_id: ''
})

// Carga usuarios y roles desde la base de datos 
const cargar = async () => {
  usuarios.value = await getUsuarios()
  roles.value = await getRoles()
}

onMounted(cargar)

// Filtra usuarios según la búsqueda realizada
const usuariosFiltrados = computed(() => {
  return usuarios.value.filter(u => {
    const nombre = u.name?.toLowerCase() || ''
    const matchName = nombre.includes(search.value.toLowerCase())
    const matchRol = filtroRol.value === '' || u.rol_id == filtroRol.value
    return matchName && matchRol
  })
})

// Abre el formulario para agregar un usuario 
const abrirModal = () => {
  modal.value = true
  editando.value = false
  form.value = { id: null, name: '', password: '', rol_id: '' }
  errores.value = { name: '', password: '', rol_id: '' }
}
// Cierra el formulario
const cerrar = () => {
  modal.value = false
}

// Verifica que los datos ingresados sean correctos
const validar = () => {
  errores.value = { name: '', password: '', rol_id: '' }
  let ok = true

  if (!form.value.name.trim()) {
    errores.value.name = 'El nombre es obligatorio'
    ok = false
  }

  if (!editando.value) {
    if (!form.value.password || form.value.password.length < 8) {
      errores.value.password = 'La contraseña debe tener mínimo 8 caracteres'
      ok = false
    }
  } else {
    if (form.value.password && form.value.password.length < 8) {
      errores.value.password = 'La contraseña debe tener mínimo 8 caracteres'
      ok = false
    }
  }

  if (!form.value.rol_id) {
    errores.value.rol_id = 'Selecciona un rol'
    ok = false
  }

  return ok
}

// Guarda o actualiza la información del usuario
const guardar = async () => {
  if (!validar()) return

  loading.value = true

  Swal.fire({
    title: editando.value ? 'Actualizando usuario...' : 'Creando usuario...',
    text: 'Por favor espera',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading()
    }
  })

  try {
    if (editando.value) {
      await updateUsuario(form.value.id, form.value)

      Swal.fire({
        icon: 'success',
        title: 'Usuario actualizado',
        timer: 1500,
        showConfirmButton: false
      })

    } else {
      await createUsuario(form.value)

      Swal.fire({
        icon: 'success',
        title: 'Usuario creado correctamente',
        timer: 1500,
        showConfirmButton: false
      })
    }

    cerrar()
    await cargar()

  } catch (error) {
    Swal.fire('Error', 'No se pudo guardar el usuario', 'error')
  } finally {
    loading.value = false
  }
}

// Carga los datos del usuario para editarlo
const editar = (u) => {
  form.value = { ...u, password: '' }
  editando.value = true
  modal.value = true
}

// Elimina un usuario después de confirmar la acción
const abrirEliminar = async (u) => {
  const result = await Swal.fire({
    title: '¿Eliminar usuario?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#5B80B0',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar'
  })

  if (!result.isConfirmed) return

  try {
    await deleteUsuario(u.id)
    await cargar()

    Swal.fire({
      icon: 'success',
      title: 'Usuario eliminado',
      timer: 1500,
      showConfirmButton: false
    })

  } catch (error) {
    Swal.fire('Error', 'No se pudo eliminar el usuario', 'error')
  }
}

// Cambia el estado del usuario entre activo e inactivo
const toggleEstado = async (u) => {
  await updateUsuario(u.id, {
    ...u,
    activo: !u.activo,
    password: ''
  })
  cargar()
}
</script>