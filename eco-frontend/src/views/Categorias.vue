<template>
  <div class="p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Categorías</h1>

      <button
        @click="abrirNuevaCategoria"
        class="bg-green-700 text-white px-4 py-2 rounded"
      >
        Nueva Categoría
      </button>
    </div>

    <!-- LISTA -->
    <table class="w-full bg-white shadow rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-3 text-left">Nombre</th>
          <th class="p-3 text-left">Subcategorías</th>
          <th class="p-3 text-left">Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="cat in categorias" :key="cat.id" class="border-t">

          <td class="p-3 font-semibold">
            {{ cat.nombre }}
          </td>

          <td class="p-3">
            <span
              v-for="sub in cat.subcategorias"
              :key="sub.id"
              class="bg-gray-200 px-2 py-1 rounded mr-1"
            >
              {{ sub.nombre }}
            </span>
          </td>

          <td class="p-3">
            <button
              @click="editarCategoria(cat)"
              class="text-blue-600 mr-3"
            >
              Editar
            </button>

            <button
              @click="eliminarCategoria(cat.id)"
              class="text-red-600"
            >
              Eliminar
            </button>
          </td>

        </tr>
      </tbody>
    </table>

    <!-- MODAL -->
    <div v-if="modal" class="fixed inset-0 bg-black/40 flex items-center justify-center">

      <div class="bg-white p-5 rounded w-[450px]">

        <h2 class="text-xl font-bold mb-3">
          {{ editando ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h2>

        <!-- NOMBRE -->
        <input
          v-model="nombre"
          class="w-full border p-2 mb-4"
          placeholder="Nombre categoría"
        />

        <!-- SOLO EN EDICIÓN -->
        <div v-if="editando">

          <p class="font-semibold mb-2">Subcategorías</p>

          <!-- EXISTENTES -->
          <div class="mb-3">
            <span
              v-for="(sub, i) in subExistentes"
              :key="sub.id"
              class="bg-gray-200 px-2 py-1 rounded mr-1"
            >
              {{ sub.nombre }}

              <button
                @click="eliminarSubBD(sub.id, i)"
                class="text-red-600 ml-1"
              >
                ✕
              </button>
            </span>
          </div>

          <!-- AGREGAR NUEVAS -->
          <div class="flex gap-2 mb-3">
            <input
              v-model="nuevaSub"
              class="border p-2 flex-1"
              placeholder="Nueva subcategoría"
            />

            <button
              @click="agregarSub"
              class="bg-green-600 text-white px-3"
            >
              +
            </button>
          </div>

          <!-- NUEVAS -->
          <div>
            <span
              v-for="(sub, i) in subNuevas"
              :key="i"
              class="bg-blue-100 px-2 py-1 rounded mr-1"
            >
              {{ sub.nombre }}

              <button
                @click="subNuevas.splice(i,1)"
                class="text-red-600 ml-1"
              >
                ✕
              </button>
            </span>
          </div>

        </div>

        <!-- BOTONES -->
        <div class="flex justify-end gap-2 mt-4">

          <button @click="cerrar" class="px-3 py-1 border">
            Cancelar
          </button>

              <button
        @click="guardar"
        :disabled="guardando"
        class="bg-green-700 text-white px-3 py-1 disabled:opacity-50"
      >
        {{ guardando ? 'Guardando...' : 'Guardar' }}
      </button>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

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
const guardando = ref(false)

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
}

/* =====================
   AGREGAR SUB NUEVA
===================== */
function agregarSub() {
  if (!nuevaSub.value.trim()) return

  subNuevas.value.push({ nombre: nuevaSub.value })
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

  guardando.value = true

  try {

    if (!nombre.value.trim()) return

    let res

    if (editando.value) {
      res = await updateCategoria(id.value, {
        nombre: nombre.value
      })
    } else {
      res = await createCategoria({
        nombre: nombre.value
      })
    }

    const catId =
      res.data?.data?.id || res.data?.id

    for (const s of subNuevas.value) {
      await createSubcategoria({
        nombre: s.nombre,
        categoria_id: catId
      })
    }

    await cargar()
    cerrar()

  } catch (error) {
    console.error(error)
  } finally {
    guardando.value = false
  }
}

/* =====================
   ELIMINAR CATEGORIA
===================== */
async function eliminarCategoria(catId) {
  if (!confirm('¿Eliminar categoría?')) return

  await deleteCategoria(catId)
  await cargar()
  
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
}
</script>