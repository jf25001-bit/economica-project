<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-4xl font-bold text-gray-800">
          Categorias
        </h1>
        <p> Sección de categorias y subcategorias</p>
      </div>

      <button
        @click="abrirNuevaCategoria"
        class="bg-[#9FCFCC] hover:bg-[#8bc0bd] text-gray-900 px-6 py-3 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex items-center gap-2"
      >
        <i class="bi bi-plus-lg"></i>
        Nueva Categoría
      </button>
    </div>

    <!-- BUSCADOR -->
    <div class="mb-6">
      <input
        v-model="filtro"
        type="text"
        placeholder="Buscar categoría..."
        class="w-full px-4 py-3 border border-gray-300 rounded-2xl shadow-sm focus:ring-2 focus:ring-[#9FCFCC] focus:border-transparent outline-none"
      />
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">

        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
            <tr class="text-gray-600 text-sm uppercase">
              <th class="px-6 py-5 text-left">
                Nombre
              </th>

              <th class="px-6 py-5 text-left">
                Subcategorías
              </th>

              <th class="px-6 py-5 text-center">
                Acciones
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="cat in categoriasFiltradas"
              :key="cat.id"
              class="border-b border-gray-100 hover:bg-green-50 transition-all duration-200"
            >
              <td class="px-6 py-5 font-semibold text-gray-800">
                {{ cat.nombre }}
              </td>

              <td class="px-6 py-5">
                <span
                  v-for="sub in cat.subcategorias"
                  :key="sub.id"
                  class="inline-block bg-[#9FCFCC]/10 text-[#9FCFCC] px-3 py-1 rounded-full text-sm mr-2 mb-1"
                >
                  {{ sub.nombre }}
                </span>
              </td>

              <td class="px-6 py-5">
                <div class="flex justify-center gap-3">

                  <button
                    @click="editarCategoria(cat)"
                    class="w-10 h-10 rounded-full border border-blue-300 bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all flex items-center justify-center"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>

                  <button
                    @click="eliminarCategoria(cat.id)"
                    class="w-10 h-10 rounded-full border border-red-300 bg-red-50 text-red-500 hover:bg-red-100 transition-all flex items-center justify-center"
                  >
                    <i class="bi bi-trash"></i>
                  </button>

                </div>
              </td>
            </tr>

            <tr v-if="categoriasFiltradas.length === 0">
              <td colspan="3" class="py-16 text-center">
                <div class="flex flex-col items-center">
                  <i class="bi bi-folder-x text-5xl text-gray-300"></i>
                  <p class="mt-3 text-gray-400">
                    No hay categorías registradas
                  </p>
                </div>
              </td>
            </tr>

          </tbody>
        </table>

      </div>
    </div>

    <!-- MODAL -->
<div
  v-if="modal"
  class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
>
  <div
    class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden"
  >

    <!-- Header -->
    <div class="bg-[#9FCFCC] text-gray-900 px-8 py-5 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold">
          {{ editando ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h2>

        <p class="text-white/70 text-sm">
          Administración de categorías y subcategorías
        </p>
      </div>

      <button
        @click="cerrar"
        class="w-10 h-10 rounded-full hover:bg-white/20 transition flex items-center justify-center"
      >
        <i class="bi bi-x-lg text-xl"></i>
      </button>
    </div>

    <!-- Contenido -->
    <div class="p-8 space-y-6">

      <!-- Nombre categoría -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="bi bi-folder-fill mr-2 text-[#9FCFCC]"></i>
          Nombre de la categoría
        </label>

        <input
          v-model="nombre"
          type="text"
          placeholder="Ingrese el nombre de la categoría"
          class="w-full px-4 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#9FCFCC] focus:border-[#9FCFCC] outline-none transition"
        />
      </div>

      <!-- Desplegable subcategorías -->
      <div class="border border-gray-200 rounded-2xl overflow-hidden">
        <button
          @click="alternarSubcategorias"
          class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition"
        >
          <div>
            <p class="font-semibold text-gray-700">
              <i class="bi bi-tags-fill mr-2 text-[#9FCFCC]"></i>
              Agregar subcategorías
            </p>

            <p class="text-sm text-gray-500">
              Opcional para categorías que necesitan una división interna
            </p>
          </div>

          <i
            :class="mostrarSubcategorias ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"
            class="text-[#9FCFCC] text-xl"
          ></i>
        </button>
      </div>

      <!-- Subcategorías -->
      <div v-if="mostrarSubcategorias">

        <label class="block text-sm font-semibold text-gray-700 mb-3">
          <i class="bi bi-tags-fill mr-2 text-[#9FCFCC]"></i>
          Subcategorías
        </label>

        <!-- Existentes -->
        <div class="flex flex-wrap gap-2 mb-4">
          <span
            v-for="(sub, i) in subExistentes"
            :key="sub.id"
            class="bg-green-100 text-[#9FCFCC] px-3 py-2 rounded-xl flex items-center gap-2"
          >
            {{ sub.nombre }}

            <button
              @click="eliminarSubBD(sub.id, i)"
              class="text-red-500 hover:text-red-700"
            >
              <i class="bi bi-x-circle-fill"></i>
            </button>
          </span>
        </div>

        <!-- Nueva Subcategoría -->
        <div class="flex gap-3 mb-4">
          <input
            v-model="nuevaSub"
            @keyup.enter="agregarSub"
            placeholder="Nueva subcategoría"
            class="flex-1 px-4 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#9FCFCC] focus:border-[#9FCFCC] outline-none"
          />

          <button
            @click="agregarSub"
            class="bg-[#9FCFCC] hover:bg-[#8bc0bd] text-gray-900 px-5 rounded-2xl shadow-md"
          >
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>

        <!-- Nuevas -->
        <div class="flex flex-wrap gap-2">
          <span
            v-for="(sub, i) in subNuevas"
            :key="i"
            class="bg-blue-100 text-blue-700 px-3 py-2 rounded-xl flex items-center gap-2"
          >
            {{ sub.nombre }}

            <button
              @click="subNuevas.splice(i,1)"
              class="text-red-500 hover:text-red-700"
            >
              <i class="bi bi-x-circle-fill"></i>
            </button>
          </span>
        </div>

      </div>

    </div>

    <!-- Footer -->
    <div class="bg-gray-50 px-8 py-5 flex justify-end gap-3 border-t">

      <button
        @click="cerrar"
        class="px-6 py-3 rounded-xl border border-gray-300 hover:bg-gray-100 font-medium transition"
      >
        Cancelar
      </button>

      <button
        @click="guardar"
        :disabled="guardando"
        class="px-6 py-3 rounded-xl bg-[#9FCFCC] text-gray-900 font-semibold hover:bg-[#8bc0bd] shadow-lg transition disabled:opacity-50"
      >
        <i class="bi bi-check-circle me-2"></i>
        {{ guardando ? 'Guardando...' : 'Guardar' }}
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

// FILTRO
const filtro = ref('')

const categoriasFiltradas = computed(() => {
  return categorias.value.filter(cat =>
    cat.nombre.toLowerCase().includes(filtro.value.toLowerCase())
  )
})

onMounted(() => cargar())

async function cargar() {
  categorias.value = await getCategorias()
}

/* =====================
   NUEVA CATEGORIA
===================== */
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

/* =====================
   EDITAR
===================== */
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

/* =====================
   DESPLEGAR SUBCATEGORIAS
===================== */
function alternarSubcategorias() {
  mostrarSubcategorias.value = !mostrarSubcategorias.value

  if (!mostrarSubcategorias.value && !editando.value) {
    subNuevas.value = []
    nuevaSub.value = ''
  }
}

/* =====================
   AGREGAR SUB NUEVA
===================== */
function agregarSub() {
  if (!nuevaSub.value.trim()) return

  subNuevas.value.push({ nombre: nuevaSub.value.trim() })
  nuevaSub.value = ''
}

/* =====================
   BORRAR SUB BD
===================== */
async function eliminarSubBD(subId, index) {
  await deleteSubcategoria(subId)
  subExistentes.value.splice(index, 1)
}

/* =====================
   GUARDAR
===================== */
async function guardar() {

  if (guardando.value) return
  if (!nombre.value.trim()) return

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

    const catId = res?.data?.id || res?.data?.data?.id || res?.id || id.value

    if (!catId) {
      throw new Error('No se pudo obtener el id de la categoría guardada')
    }

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

    cerrar()

  } catch (error) {
    console.error(error)
    Swal.fire(
      'Error',
      error?.response?.data?.message || 'No se pudo guardar la categoría o sus subcategorías',
      'error'
    )
  } finally {
    guardando.value = false
  }
}
/* =====================
   ELIMINAR CATEGORIA
===================== */
async function eliminarCategoria(catId) {
  const result = await Swal.fire({
    title: '¿Eliminar categoría?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#9FCFCC',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar'
  })

  if (!result.isConfirmed) return

  try {
    await deleteCategoria(catId)
    await cargar()

    Swal.fire({
      icon: 'success',
      title: 'Categoría eliminada',
      timer: 1500,
      showConfirmButton: false
    })

  } catch (error) {
    Swal.fire(
      'Error',
      'No se pudo eliminar la categoría',
      'error'
    )
  }
}
/* =====================
   CERRAR
===================== */
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
