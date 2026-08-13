<template>
  <div class="min-h-screen bg-slate-50/50 p-6 sm:p-8">

    <!-- Encabezado de Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">
          Categorías
        </h1>
        <p class="text-slate-500 text-sm font-medium mt-1">
          Gestión de categorías y organización de subcategorías
        </p>
      </div>

      <button
        @click="abrirNuevaCategoria"
        class="inline-flex items-center justify-center gap-2 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold px-6 py-3 rounded-2xl shadow-lg shadow-[#2B3A4A]/20 transition-all active:scale-95 cursor-pointer"
      >
        <i class="bi bi-folder-plus text-lg"></i>
        <span>Nueva Categoría</span>
      </button>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 mb-6 flex flex-wrap gap-4">
      <div class="relative flex-1 min-w-[240px]">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
          <i class="bi bi-search text-base"></i>
        </span>
        <input
          v-model="filtro"
          type="text"
          placeholder="Buscar categoría por nombre..."
          class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium"
        />
      </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] table-fixed">
          <thead>
            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider">
              <th class="px-6 py-4 text-left w-1/3">Nombre</th>
              <th class="px-6 py-4 text-left">Subcategorías</th>
              <th class="px-6 py-4 text-right w-32">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="cat in categoriasFiltradas"
              :key="cat.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- Nombre -->
              <td class="px-6 py-4 font-bold text-slate-800 truncate">
                <div class="flex items-center gap-2">
                  <i class="bi bi-folder-fill text-slate-400 text-base"></i>
                  <span>{{ cat.nombre }}</span>
                </div>
              </td>

              <!-- Subcategorías -->
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="sub in cat.subcategorias"
                    :key="sub.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/60"
                  >
                    <i class="bi bi-tag-fill mr-1 text-[10px]"></i>
                    {{ sub.nombre }}
                  </span>
                  <span v-if="!cat.subcategorias || cat.subcategorias.length === 0" class="text-xs text-slate-400 italic">
                    Sin subcategorías
                  </span>
                </div>
              </td>

              <!-- Acciones -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="editarCategoria(cat)"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-[#2B3A4A] hover:text-white transition cursor-pointer"
                    title="Editar Categoría"
                  >
                    <i class="bi bi-pencil-fill text-sm"></i>
                  </button>

                  <button
                    @click="eliminarCategoria(cat.id)"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition cursor-pointer"
                    title="Eliminar Categoría"
                  >
                    <i class="bi bi-trash-fill text-sm"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Estado Vacío -->
            <tr v-if="categoriasFiltradas.length === 0">
              <td colspan="3" class="py-16 text-center text-slate-400">
                <i class="bi bi-folder-x text-4xl block mb-2 opacity-50"></i>
                <p class="font-medium text-sm">No se encontraron categorías registradas.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Formulario -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-3xl w-full max-w-xl flex flex-col overflow-hidden shadow-2xl border border-slate-100">
        
        <!-- Modal Header -->
        <div class="bg-[#2B3A4A] text-white px-6 py-5 flex justify-between items-center">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-sky-300">
              <i class="bi bi-folder-fill text-xl"></i>
            </div>
            <div>
              <h2 class="text-lg font-extrabold leading-none">
                {{ editando ? 'Editar Categoría' : 'Nueva Categoría' }}
              </h2>
              <p class="text-sky-200/80 text-xs mt-1">Administración de categorías y subcategorías</p>
            </div>
          </div>

          <button
            @click="cerrar"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-slate-200 cursor-pointer"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">
              Nombre de la categoría
            </label>
            <input
              v-model="nombre"
              type="text"
              placeholder="Ej. Bebidas, Alimentos, etc."
              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium"
            />
          </div>

          <!-- Acordeón Desplegable Subcategorías -->
          <div class="border border-slate-200 rounded-xl overflow-hidden">
            <button
              @click="alternarSubcategorias"
              class="w-full px-4 py-3 flex items-center justify-between text-left bg-slate-50 hover:bg-slate-100/80 transition cursor-pointer"
            >
              <div class="flex items-center gap-2 text-slate-700 font-bold text-sm">
                <i class="bi bi-tags-fill text-[#2B3A4A]"></i>
                <span>Gestionar subcategorías</span>
              </div>
              <i :class="mostrarSubcategorias ? 'bi bi-chevron-up' : 'bi bi-chevron-down'" class="text-slate-400 text-xs"></i>
            </button>

            <div v-if="mostrarSubcategorias" class="p-4 bg-white space-y-3 border-t border-slate-200">
              <!-- Subcategorías ya almacenadas -->
              <div v-if="editando && subExistentes.length > 0" class="flex flex-wrap gap-2">
                <span
                  v-for="(sub, i) in subExistentes"
                  :key="sub.id"
                  class="bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2"
                >
                  {{ sub.nombre }}
                  <button
                    @click="eliminarSubBD(sub.id, i)"
                    class="text-red-500 hover:text-red-700 cursor-pointer"
                  >
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </span>
              </div>

              <!-- Input para agregar nuevas subcategorías -->
              <div class="flex gap-2">
                <input
                  v-model="nuevaSub"
                  @keyup.enter="agregarSub"
                  placeholder="Nombre de subcategoría..."
                  class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium"
                />
                <button
                  @click="agregarSub"
                  class="bg-[#2B3A4A] hover:bg-[#1F2B37] text-white px-4 py-2.5 rounded-xl shadow transition cursor-pointer flex items-center gap-1 font-bold text-sm"
                >
                  <i class="bi bi-plus-lg"></i>
                  <span>Añadir</span>
                </button>
              </div>

              <!-- Badges de Subcategorías creadas temporalmente -->
              <div v-if="subNuevas.length > 0" class="flex flex-wrap gap-2 pt-1">
                <span
                  v-for="(sub, i) in subNuevas"
                  :key="i"
                  class="bg-sky-50 text-sky-700 border border-sky-200 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-2"
                >
                  {{ sub.nombre }}
                  <button
                    @click="subNuevas.splice(i, 1)"
                    class="text-red-500 hover:text-red-700 cursor-pointer"
                  >
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end gap-3 p-5 bg-slate-50 border-t border-slate-100">
          <button
            @click="cerrar"
            class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-100 transition cursor-pointer"
          >
            Cancelar
          </button>

          <button
            @click="guardar"
            :disabled="guardando"
            class="px-6 py-2.5 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold text-sm rounded-xl shadow-lg shadow-[#2B3A4A]/20 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
          >
            <i v-if="guardando" class="bi bi-arrow-clockwise animate-spin text-base"></i>
            <span>{{ guardando ? 'Guardando...' : 'Guardar' }}</span>
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import Swal from 'sweetalert2'
import {
  getCategorias,
  createCategoria,
  updateCategoria,
  deleteCategoria
} from '@/services/categoriaService'

import {
  createSubcategoria,
  deleteSubcategoria
} from '@/services/subcategoriaService'

const categorias = ref([])
const modal = ref(false)
const editando = ref(false)
const id = ref(null)
const nombre = ref('')
const subExistentes = ref([])
const subNuevas = ref([])
const nuevaSub = ref('')
const mostrarSubcategorias = ref(false)
const guardando = ref(false)
const filtro = ref('')

const categoriasFiltradas = computed(() => {
  return categorias.value.filter(cat =>
    cat.nombre.toLowerCase().includes(filtro.value.toLowerCase().trim())
  )
})

onMounted(() => cargar())

async function cargar() {
  categorias.value = await getCategorias()
}

function abrirNuevaCategoria() {
  modal.value = true
  editando.value = false
  id.value = null
  nombre.value = ''
  subExistentes.value = []
  subNuevas.value = []
  nuevaSub.value = ''
  mostrarSubcategorias.value = false
}

function editarCategoria(cat) {
  modal.value = true
  editando.value = true
  id.value = cat.id
  nombre.value = cat.nombre
  subExistentes.value = [...(cat.subcategorias || [])]
  subNuevas.value = []
  nuevaSub.value = ''
  mostrarSubcategorias.value = true
}

function alternarSubcategorias() {
  mostrarSubcategorias.value = !mostrarSubcategorias.value
  if (!mostrarSubcategorias.value && !editando.value) {
    subNuevas.value = []
    nuevaSub.value = ''
  }
}

function agregarSub() {
  if (!nuevaSub.value.trim()) return
  subNuevas.value.push({ nombre: nuevaSub.value.trim() })
  nuevaSub.value = ''
}

async function eliminarSubBD(subId, index) {
  try {
    await deleteSubcategoria(subId)
    subExistentes.value.splice(index, 1)
  } catch (error) {
    console.error(error)
  }
}

async function guardar() {
  if (guardando.value) return
  if (!nombre.value.trim()) {
    Swal.fire({
      icon: 'warning',
      title: 'Campo requerido',
      text: 'El nombre de la categoría es obligatorio',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  guardando.value = true

  try {
    let res
    const subcategoriasPorCrear = [...subNuevas.value]
    
    if (mostrarSubcategorias.value && nuevaSub.value.trim()) {
      subcategoriasPorCrear.push({ nombre: nuevaSub.value.trim() })
      nuevaSub.value = ''
    }

    if (editando.value) {
      res = await updateCategoria(id.value, {
        nombre: nombre.value.trim()
      })
    } else {
      res = await createCategoria({
        nombre: nombre.value.trim()
      })
    }

    const catId = res?.data?.data?.id || res?.data?.id || res?.id || id.value

    const creadas = []
    for (const s of subcategoriasPorCrear) {
      const subRes = await createSubcategoria({
        nombre: s.nombre,
        categoria_id: catId
      })
      creadas.push(subRes?.data || subRes || { nombre: s.nombre, id: Date.now() })
    }

    await cargar()

    const actual = categorias.value.find(c => c.id === catId)
    if (actual) {
      if (!actual.subcategorias) {
        actual.subcategorias = []
      }
      
      if (editando.value) {
        actual.subcategorias = [...subExistentes.value, ...creadas]
      } else {
        actual.subcategorias = creadas
      }
    }

    await Swal.fire({
      icon: 'success',
      title: editando.value ? 'Categoría actualizada' : 'Categoría creada correctamente',
      timer: 1500,
      showConfirmButton: false
    })

    cerrar()
  } catch (error) {
    console.error(error)
    Swal.fire({
      title: 'Error',
      text: error?.response?.data?.message || 'No se pudo guardar la categoría o sus subcategorías',
      icon: 'error',
      confirmButtonColor: '#2B3A4A'
    })
  } finally {
    guardando.value = false
  }
}

async function eliminarCategoria(catId) {
  const result = await Swal.fire({
    title: '¿Eliminar categoría?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#2B3A4A',
    cancelButtonColor: '#ef4444',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  })

  if (!result.isConfirmed) return

  try {
    await deleteCategoria(catId)
    await cargar()
    await Swal.fire({
      icon: 'success',
      title: 'Categoría eliminada',
      timer: 1500,
      showConfirmButton: false
    })
  } catch (error) {
    Swal.fire({
      title: 'Error',
      text: 'No se pudo eliminar la categoría',
      icon: 'error',
      confirmButtonColor: '#2B3A4A'
    })
  }
}

function cerrar() {
  modal.value = false
  editando.value = false
  nombre.value = ''
  id.value = null
  subExistentes.value = []
  subNuevas.value = []
  nuevaSub.value = ''
  mostrarSubcategorias.value = false
}
</script>