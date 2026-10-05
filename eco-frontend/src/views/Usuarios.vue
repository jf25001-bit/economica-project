<template>
  <div class="min-h-screen bg-slate-50/50 p-4 sm:p-6 md:p-8 w-full max-w-full overflow-x-hidden box-border">

    <!-- Encabezado de Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8 w-full">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight truncate">
          Usuarios
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">
          Gestión de accesos, cuentas de usuario y asignación de roles
        </p>
      </div>

      <button
        @click="abrirModal"
        class="inline-flex items-center justify-center gap-2 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold px-5 py-2.5 sm:px-6 sm:py-3 rounded-2xl shadow-lg shadow-[#2B3A4A]/20 transition-all active:scale-95 cursor-pointer text-sm sm:text-base w-full sm:w-auto shrink-0"
      >
        <i class="bi bi-person-plus-fill text-lg"></i>
        <span>Nuevo Usuario</span>
      </button>
    </div>

    <!-- Barra de Búsqueda y Filtro de Rol -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-3 sm:p-4 mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4 w-full max-w-full box-border">
      <div class="relative flex-1 min-w-0 w-full">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
          <i class="bi bi-search text-base"></i>
        </span>
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por ID, nombre, email o teléfono..."
          class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
        />
      </div>

      <div class="relative w-full sm:w-64 min-w-0 shrink-0">
        <select
          v-model="filtroRol"
          class="w-full max-w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium appearance-none cursor-pointer box-border truncate"
        >
          <option value="">Todos los roles</option>
          <option v-for="r in roles" :key="r.id" :value="r.id">
            {{ r.nombre }}
          </option>
        </select>
        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
          <i class="bi bi-chevron-down text-xs"></i>
        </span>
      </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden w-full max-w-full">
      <div class="overflow-x-auto w-full">
        <table class="w-full min-w-[700px] table-fixed">
          <thead>
            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider">
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left w-16 sm:w-20">ID</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left">Usuario / Nombre</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left">Email</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left">Teléfono</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left">Rol</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-center w-28 sm:w-32">Estado</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-right w-24 sm:w-28">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="u in usuariosPaginados"
              :key="u.id"
              class="transition-colors"
              :class="[
                esYo(u) 
                  ? 'bg-indigo-50/70 hover:bg-indigo-50/90 border-l-4 border-l-indigo-500 border-b-2 border-b-indigo-200/80 shadow-sm' 
                  : 'hover:bg-slate-50/80'
              ]"
            >
              <!-- ID -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 font-bold text-slate-800 truncate">
                #{{ u.id }}
              </td>

              <!-- Nombre y Apellido -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 font-bold text-slate-800 truncate">
                {{ u.name }} {{ u.apellido }}
                <span
                  v-if="esYo(u)"
                  class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-300/80 align-middle shadow-2xs"
                >
                  Tú
                </span>
              </td>

              <!-- Email -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 text-slate-600 text-sm truncate">
                {{ u.email || '—' }}
              </td>

              <!-- Teléfono -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 text-slate-600 text-sm truncate">
                {{ u.telefono || '—' }}
              </td>

              <!-- Rol -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 min-w-0">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/60 max-w-full truncate">
                  {{ u.rol?.nombre || 'Sin rol' }}
                </span>
              </td>

              <!-- Estado -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 text-center whitespace-nowrap">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                  :class="u.activo ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="u.activo ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ u.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>

              <!-- Acciones -->
              <td class="px-4 py-3 sm:px-6 sm:py-4">
                <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                  <button
                    @click="editar(u)"
                    class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-[#2B3A4A] hover:text-white transition cursor-pointer shrink-0"
                    title="Editar Usuario"
                  >
                    <i class="bi bi-pencil-fill text-xs sm:text-sm"></i>
                  </button>

                  <button
                    @click="toggleEstado(u)"
                    :disabled="protegido(u)"
                    class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl transition cursor-pointer shrink-0 disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="u.activo ? 'bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white'"
                    :title="protegido(u) ? 'No se puede cambiar el estado de un administrador ni el propio' : (u.activo ? 'Desactivar' : 'Activar')"
                  >
                    <i class="bi bi-power text-xs sm:text-sm"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Estado Vacío -->
            <tr v-if="usuariosFiltrados.length === 0">
              <td colspan="7" class="py-12 sm:py-16 text-center text-slate-400">
                <i class="bi bi-people text-3xl sm:text-4xl block mb-2 opacity-50"></i>
                <p class="font-medium text-xs sm:text-sm">No se encontraron usuarios registrados.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <Paginacion
        :current-page="paginaActual"
        :total-pages="totalPaginas"
        @update:current-page="paginaActual = $event"
      />
    </div>

    <!-- Modal Formulario -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-100 box-border my-auto">

        <!-- Modal Header -->
        <div class="bg-[#2B3A4A] text-white px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center shrink-0 w-full box-border">
          <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center text-sky-300 shrink-0">
              <i class="bi bi-person-fill text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
              <h2 class="text-base sm:text-lg font-extrabold leading-none truncate">
                {{ editando ? 'Editar Usuario' : 'Nuevo Usuario' }}
              </h2>
              <p class="text-sky-200/80 text-[11px] sm:text-xs mt-1 truncate">Asigna los datos, credenciales y el rol</p>
            </div>
          </div>

          <button
            @click="cerrar"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-slate-200 cursor-pointer shrink-0"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 w-full box-border">

          <!-- Campos de texto -->
          <div v-for="campo in camposForm" :key="campo.key" class="min-w-0 w-full">
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">{{ campo.label }}</label>
            <input
              v-model="form[campo.key]"
              :type="campo.type"
              :placeholder="campo.placeholder"
              @input="campo.filtro && filtrarCampo(campo.key, campo.filtro)"
              class="w-full max-w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
            />
            <p v-if="errores[campo.key]" class="text-red-500 text-xs font-semibold mt-1 break-words">{{ errores[campo.key] }}</p>
          </div>

          <!-- Campo Contraseña -->
          <div class="min-w-0 w-full">
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">Contraseña</label>
            <div class="relative w-full">
              <input
                v-model="form.password"
                :type="(!editando && mostrarPassword) ? 'text' : 'password'"
                :placeholder="editando ? 'Dejar en blanco para conservar actual' : '••••••••'"
                class="w-full max-w-full px-3.5 py-2.5 sm:px-4 sm:py-3 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
              />
              <button
                v-if="!editando"
                type="button"
                @click="mostrarPassword = !mostrarPassword"
                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                :title="mostrarPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
              >
                <i class="bi text-sm" :class="mostrarPassword ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
              </button>
            </div>
            <p v-if="errores.password" class="text-red-500 text-xs font-semibold mt-1 break-words">{{ errores.password }}</p>
          </div>

          <!-- Campo Rol de Usuario -->
          <div class="min-w-0 w-full relative">
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">Rol de Usuario</label>
            <div class="relative w-full">
              <select
                v-model="form.rol_id"
                class="w-full max-w-full px-3.5 py-2.5 sm:px-4 sm:py-3 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium cursor-pointer box-border appearance-none truncate"
              >
                <option value="">Selecciona un rol</option>
                <option v-for="r in roles" :key="r.id" :value="r.id">
                  {{ r.nombre }}
                </option>
              </select>
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                <i class="bi bi-chevron-down text-xs"></i>
              </span>
            </div>
            <p v-if="errores.rol_id" class="text-red-500 text-xs font-semibold mt-1 break-words">{{ errores.rol_id }}</p>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end gap-2 sm:gap-3 p-4 sm:p-5 bg-slate-50 border-t border-slate-100 shrink-0 w-full box-border">
          <button
            @click="cerrar"
            class="px-4 py-2 sm:px-5 sm:py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold text-xs sm:text-sm hover:bg-slate-100 transition cursor-pointer"
          >
            Cancelar
          </button>

          <button
            @click="guardar"
            :disabled="loading"
            class="px-5 py-2 sm:px-6 sm:py-2.5 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-[#2B3A4A]/20 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
          >
            <i v-if="loading" class="bi bi-arrow-clockwise animate-spin text-sm sm:text-base"></i>
            <span>{{ loading ? 'Guardando...' : 'Guardar' }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import Swal from 'sweetalert2'
import Paginacion from '@/components/Paginacion.vue'
import { getUsuarios, createUsuario, updateUsuario } from '@/services/usuarioService'
import { getRoles } from '@/services/rolService'
import { getMe } from '@/services/authService'

const ROL_ADMIN_KEYWORD = 'admin'

const usuarios = ref([])
const roles = ref([])
const currentUser = ref(null)
const search = ref('')
const filtroRol = ref('')
const modal = ref(false)
const editando = ref(false)
const loading = ref(false)
const mostrarPassword = ref(false)

const camposForm = [
  { key: 'name', label: 'Nombre', placeholder: 'Ej. Juan', type: 'text', filtro: 'letras' },
  { key: 'apellido', label: 'Apellido', placeholder: 'Ej. Pérez', type: 'text', filtro: 'letras' },
  { key: 'email', label: 'Correo Electrónico', placeholder: 'correo@ejemplo.com', type: 'email', filtro: null },
  { key: 'telefono', label: 'Teléfono', placeholder: 'Ej. 70000000', type: 'text', filtro: 'numeros' }
]

const formVacio = () => ({
  id: null,
  name: '',
  apellido: '',
  email: '',
  telefono: '',
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

const cargarMe = async () => {
  try {
    const data = await getMe()
    currentUser.value = data?.user ?? data
  } catch {
    currentUser.value = null
  }
}

onMounted(() => {
  cargar()
  cargarMe()
})

const filtrarCampo = (key, tipo) => {
  if (tipo === 'letras') {
    form.value[key] = form.value[key].replace(/[^a-zA-ZÀ-ÿ\s]/g, '')
  } else if (tipo === 'numeros') {
    form.value[key] = form.value[key].replace(/[^0-9]/g, '')
  }
}

const esYo = (u) => currentUser.value && Number(u.id) === Number(currentUser.value.id)
const esAdmin = (u) => u.rol?.nombre?.toLowerCase().includes(ROL_ADMIN_KEYWORD)

// Protege al usuario logueado y a cualquier administrador de ser desactivados
const protegido = (u) => esYo(u) || esAdmin(u)

const usuariosFiltrados = computed(() => {
  const query = search.value.toLowerCase().trim()

  const lista = usuarios.value.filter(u => {
    const matchId = u.id.toString().includes(query)
    const matchName = u.name ? u.name.toLowerCase().includes(query) : false
    const matchApellido = u.apellido ? u.apellido.toLowerCase().includes(query) : false
    const matchEmail = u.email ? u.email.toLowerCase().includes(query) : false
    const matchTelefono = u.telefono ? u.telefono.toLowerCase().includes(query) : false

    const matchQuery = !query || matchId || matchName || matchApellido || matchEmail || matchTelefono
    const matchRol = filtroRol.value === '' || u.rol_id == filtroRol.value

    return matchQuery && matchRol
  })

  return [...lista].sort((a, b) => {
    if (esYo(a)) return -1
    if (esYo(b)) return 1
    return 0
  })
})

const paginaActual = ref(1)
const porPagina = 8

const totalPaginas = computed(() =>
  Math.max(1, Math.ceil(usuariosFiltrados.value.length / porPagina))
)

const usuariosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina
  return usuariosFiltrados.value.slice(inicio, inicio + porPagina)
})

watch([search, filtroRol], () => {
  paginaActual.value = 1
})

const abrirModal = () => {
  modal.value = true
  editando.value = false
  form.value = formVacio()
  errores.value = {}
  mostrarPassword.value = false
}

const cerrar = () => {
  modal.value = false
}

const validar = () => {
  errores.value = {}
  let ok = true

  if (!form.value.name) {
    errores.value.name = 'El nombre es obligatorio'
    ok = false
  }

  if (!form.value.apellido) {
    errores.value.apellido = 'El apellido es obligatorio'
    ok = false
  }

  if (!form.value.email) {
    errores.value.email = 'El correo electrónico es obligatorio'
    ok = false
  }

  if (!editando.value && (!form.value.password || form.value.password.length < 8)) {
    errores.value.password = 'La contraseña debe tener mínimo 8 caracteres'
    ok = false
  }

  if (editando.value && form.value.password && form.value.password.length > 0 && form.value.password.length < 8) {
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
    const payload = {
      name: form.value.name,
      apellido: form.value.apellido,
      email: form.value.email,
      telefono: form.value.telefono || null,
      rol_id: form.value.rol_id,
      activo: Boolean(form.value.activo)
    }

    if (form.value.password) {
      payload.password = form.value.password
    }

    if (editando.value) {
      await updateUsuario(form.value.id, payload)
      await Swal.fire({ icon: 'success', title: 'Usuario actualizado', timer: 1500, showConfirmButton: false })
    } else {
      await createUsuario(payload)
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
    telefono: u.telefono || '',
    password: '',
    rol_id: u.rol_id || '',
    activo: Boolean(u.activo)
  }
  editando.value = true
  errores.value = {}
  mostrarPassword.value = false
  modal.value = true
}

const toggleEstado = async (u) => {
  if (protegido(u)) return

  try {
    const nuevoEstado = !u.activo

    const payload = {
      name: u.name,
      apellido: u.apellido,
      email: u.email,
      telefono: u.telefono || null,
      rol_id: u.rol_id,
      activo: nuevoEstado
    }

    await updateUsuario(u.id, payload)

    const mensaje = nuevoEstado ? 'Usuario activado' : 'Usuario desactivado'
    Swal.fire({
      icon: 'success',
      title: mensaje,
      timer: 1200,
      showConfirmButton: false
    })

    await cargar()
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'No se pudo cambiar el estado del usuario', 'error')
  }
}
</script>