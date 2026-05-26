<template>
  <div class="p-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          Categorías
        </h1>

      </div>

      <!-- Botón -->
      <button
        @click="abrirNuevaCategoria"
        class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Nueva Categoría
      </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
      <table class="w-full">
        <thead class="bg-blue-100">
          <tr class="text-left text-black-700">
            <th class="px-6 py-4 font-semibold">Categoría</th>
            <th class="px-6 py-4 font-semibold">Subcategorías</th>
            <th class="px-6 py-4 font-semibold">Estado</th>
            <th class="px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="categoria in categorias"
            :key="categoria.id"
            class="border-t hover:bg-gray-50"
          >
            <!-- Nombre -->
            <td class="px-6 py-4 font-semibold">
              {{ categoria.nombre }}
            </td>

            <!-- Subcategorías -->
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="sub in categoria.subcategorias"
                  :key="sub"
                  class="bg-[#46674A]/10 text-[#46674A] px-8 py-1 rounded-full text-sm"
                >
                  {{ sub }}
                </span>
              </div>
            </td>

            <!-- Estado -->
            <td class="px-6 py-4">
              <span
                class="bg-black-100 text-green-700 px-8 py-1 rounded-full text-sm"
              >
                Activa
              </span>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4">
              <div class="flex gap-8 text-lg">

                <!-- Editar -->
                <button
                  @click="editarCategoria(categoria)"
                  class="text-blue-500 hover:text-blue-700"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>

                <!-- Eliminar -->
                <button
                  @click="eliminarCategoria(categoria.id)"
                  class="text-red-500 hover:text-red-700"
                >
                  <i class="bi bi-trash"></i>
                </button>

              </div>
            </td>
          </tr>

          <!-- Vacío -->
          <tr v-if="categorias.length === 0">
            <td
              colspan="4"
              class="text-center py-8 text-gray-500"
            >
              No hay categorías registradas.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div
      v-if="mostrarModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6"
      >
        <!-- Encabezado -->
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-black-800">
            {{
              editandoId
                ? 'Editar Categoría'
                : 'Nueva Categoría'
            }}
          </h2>

          <button
            @click="cerrarModal"
            class="text-3xl text-red-500"
          >
            &times;
          </button>
        </div>

        <!-- Nombre -->
        <div class="mb-5">
          <label class="block text-black-700 font-medium mb-8">
            Nombre de la categoría
          </label>

          <input
            v-model="nombreCategoria"
            type="text"
            placeholder="Ejemplo: Bebidas"
            class="w-full px-2 py-3 border borderblack-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Subcategorías -->
        <div class="mb-5">
          <label class="block text-black-700 font-medium mb-2">
            Subcategorías
          </label>

          <div class="flex gap-3">
            <input
              v-model="nuevaSubcategoria"
              type="text"
              placeholder="Ejemplo: Gaseosas"
              class="flex-1 px-2 py-3 border border-black-300 rounded-xl focus:outline-none focus:ring-8 focus:ring-[#46674A]"
            />

            <button
              @click="agregarSubcategoria"
              class="bg-[#46674A] text-white px-4 rounded-xl"
            >
              Agregar
            </button>
          </div>
        </div>

        <!-- Lista -->
        <div class="flex flex-wrap gap-2 mb-6">
          <span
            v-for="(sub, index) in subcategorias"
            :key="index"
            class="bg-[#46674A]/10 text-[#46674A] px-3 py-2 rounded-full flex items-center gap-2"
          >
            {{ sub }}

            <button
              @click="eliminarSubcategoria(index)"
              class="text-red-500"
            >
              <i class="bi bi-x-lg"></i>
            </button>
          </span>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3">
          <button
            @click="cerrarModal"
            class="px-5 py-3 border border-gray-300 rounded-xl"
          >
            Cancelar
          </button>

          <button
            @click="guardarCategoria"
            class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl"
          >
            {{
              editandoId
                ? 'Actualizar'
                : 'Guardar Categoría'
            }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const mostrarModal = ref(false)

const nombreCategoria = ref('')
const nuevaSubcategoria = ref('')
const subcategorias = ref([])

const editandoId = ref(null)

const categorias = ref([
  {
    id: 1,
    nombre: 'Bebidas',
    subcategorias: ['Gaseosas', 'Jugos', 'Agua']
  },
  {
    id: 2,
    nombre: 'Snacks',
    subcategorias: ['Papas', 'Galletas']
  }
])

/* Abrir modal */
function abrirNuevaCategoria() {
  mostrarModal.value = true
}

/* Agregar subcategoría */
function agregarSubcategoria() {
  if (nuevaSubcategoria.value.trim() === '') return

  subcategorias.value.push(nuevaSubcategoria.value)

  nuevaSubcategoria.value = ''
}

/* Eliminar subcategoría */
function eliminarSubcategoria(index) {
  subcategorias.value.splice(index, 1)
}

/* Guardar */
function guardarCategoria() {
  if (nombreCategoria.value.trim() === '') {
    alert('Ingresa un nombre de categoría')
    return
  }

  if (editandoId.value) {
    const index = categorias.value.findIndex(
      categoria => categoria.id === editandoId.value
    )

    categorias.value[index] = {
      ...categorias.value[index],
      nombre: nombreCategoria.value,
      subcategorias: [...subcategorias.value]
    }
  } else {
    categorias.value.push({
      id: Date.now(),
      nombre: nombreCategoria.value,
      subcategorias: [...subcategorias.value]
    })
  }

  cerrarModal()
}

/* Editar */
function editarCategoria(categoria) {
  mostrarModal.value = true

  editandoId.value = categoria.id

  nombreCategoria.value = categoria.nombre

  subcategorias.value = [
    ...categoria.subcategorias
  ]
}

/* Eliminar */
function eliminarCategoria(id) {
  categorias.value = categorias.value.filter(
    categoria => categoria.id !== id
  )
}

/* Cerrar */
function cerrarModal() {
  mostrarModal.value = false

  editandoId.value = null
  nombreCategoria.value = ''
  nuevaSubcategoria.value = ''
  subcategorias.value = []
}
</script>