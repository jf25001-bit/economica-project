<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 lg:p-6">
    <!-- Header -->
    <div class="w-full max-w-none">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-800">
            Proveedores
          </h1>
          <p class="text-gray-500 mt-1">Sección de administración de proveedores y sus productos</p>
        </div>

        <button
          @click="abrirNuevoProveedor"
          class="group relative overflow-hidden bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-7 py-3 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 flex items-center gap-3 font-semibold"
        >
          <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
          <i class="bi bi-plus-circle-fill text-lg"></i>
          <span>Nuevo proveedor</span>
        </button>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden w-full">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1040px] table-fixed">
            <colgroup>
              <col class="w-[8%]" />
              <col class="w-[18%]" />
              <col class="w-[14%]" />
              <col class="w-[24%]" />
              <col class="w-[24%]" />
              <col class="w-[12%]" />
            </colgroup>
            <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
              <tr class="text-gray-600 text-sm uppercase">
                <th class="px-6 py-5 text-left">ID</th>
                <th class="px-6 py-5 text-left">Nombre</th>
                <th class="px-6 py-5 text-left">Teléfono</th>
                <th class="px-6 py-5 text-left">Dirección</th>
                <th class="px-6 py-5 text-left">Productos Suministrados</th>
                <th class="px-6 py-5 text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="proveedor in proveedores"
                :key="proveedor.id"
                class="border-b border-gray-100 hover:bg-green-50/50 transition-all duration-200"
              >
                <td class="px-6 py-5">
                  <span class="bg-[#47B5AC]/10 text-[#47B5AC] px-3 py-1 rounded-full text-xs font-bold">
                    #{{ proveedor.id }}
                  </span>
                </td>

                <td class="px-6 py-5 font-medium text-gray-800 truncate">
                  {{ proveedor.nombre_proveedor }}
                </td>

                <td class="px-6 py-5 text-gray-600 truncate">
                  {{ proveedor.telefono }}
                </td>

                <td class="px-6 py-5 text-gray-600 truncate">
                  {{ proveedor.direccion }}
                </td>

                <!-- Columna de productos asociados -->
                <td class="px-6 py-5">
                  <div v-if="proveedor.productos && proveedor.productos.length > 0" class="flex flex-wrap gap-1 max-h-16 overflow-y-auto pr-1">
                    <span
                      v-for="prod in proveedor.productos"
                      :key="prod.id"
                      class="bg-gray-100 border border-gray-200 text-gray-700 px-2.5 py-0.5 rounded-lg text-xs font-medium"
                    >
                      {{ prod.nombre || prod.nombre_producto }}
                    </span>
                  </div>
                  <span v-else class="text-xs text-gray-400 italic">Sin productos</span>
                </td>

                <td class="px-6 py-5">
                  <div class="flex justify-center gap-3">
                    <button
                      @click="editarProveedor(proveedor)"
                      class="w-10 h-10 rounded-full border border-blue-300 bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all flex items-center justify-center"
                      title="Editar"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>

                    <button
                      @click="eliminarProveedor(proveedor.id)"
                      class="w-10 h-10 rounded-full border border-red-300 bg-red-50 text-red-500 hover:bg-red-100 transition-all flex items-center justify-center"
                      title="Eliminar"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="proveedores.length === 0">
                <td colspan="6" class="py-28 text-center">
                  <div class="flex flex-col items-center">
                    <i class="bi bi-folder-x text-5xl text-gray-300"></i>
                    <p class="mt-3 text-gray-400">
                      No hay proveedores registrados
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Formulario Principal -->
    <div
      v-if="mostrarModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-40 p-4"
    >
      <div class="bg-white rounded-[22px] shadow-2xl w-full max-w-[675px] overflow-hidden animate-fadeIn my-8 border border-gray-200">
        
        <!-- Header Modal -->
        <div class="bg-[#47B5AC] text-white h-[112px] px-8 flex justify-between items-start pt-8">
          <div class="space-y-3">
            <h2 class="text-2xl font-bold leading-none">
              {{ editandoId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
            </h2>
            <p class="text-white/85 text-sm">
              Complete los datos del proveedor y asigne sus productos
            </p>
          </div>

          <button
            @click="cerrarModal"
            class="w-11 h-11 rounded-full bg-[#DDF3F1] text-gray-800 shadow-md hover:bg-white transition flex items-center justify-center"
          >
            <i class="bi bi-x-lg text-xl"></i>
          </button>
        </div>

        <!-- Contenido Modal -->
        <div class="px-8 pt-8 pb-7 space-y-5 max-h-[75vh] overflow-y-auto overflow-x-hidden">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre -->
            <div>
              <label class="flex items-center text-sm font-bold text-gray-800 mb-2">
                <i class="bi bi-person-badge mr-2 text-[#47B5AC]"></i>
                Nombre del proveedor
              </label>

              <input
                v-model="nombre"
                type="text"
                placeholder="Ingrese el nombre"
                class="box-border block w-full h-10 px-4 rounded-[14px] border border-gray-300 focus:ring-2 focus:ring-[#47B5AC] focus:border-[#47B5AC] outline-none transition"
              />
            </div>

            <!-- Teléfono -->
            <div>
              <label class="flex items-center text-sm font-bold text-gray-800 mb-2">
                <i class="bi bi-telephone mr-2 text-[#47B5AC]"></i>
                Teléfono
              </label>

              <input
                v-model="telefono"
                type="text"
                maxlength="8"
                placeholder="Ej: 77778888"
                @input="telefono = telefono.replace(/[^0-9]/g, '')"
                class="box-border block w-full h-10 px-4 rounded-[14px] border border-gray-300 focus:ring-2 focus:ring-[#47B5AC] focus:border-[#47B5AC] outline-none transition"
              />
            </div>
          </div>

          <!-- Dirección -->
          <div>
            <label class="flex items-center text-sm font-bold text-gray-800 mb-2">
              <i class="bi bi-geo-alt mr-2 text-[#47B5AC]"></i>
              Dirección
            </label>

            <textarea
              v-model="direccion"
              rows="2"
              placeholder="Ingrese la dirección"
              class="box-border block w-full min-h-[56px] px-4 py-3 rounded-[14px] border border-gray-300 focus:ring-2 focus:ring-[#47B5AC] focus:border-[#47B5AC] outline-none resize-none transition"
            ></textarea>
          </div>

          <!-- Botón Disparador del Modal de Productos Suministrados -->
          <div class="pt-4">
            <label class="flex items-center text-sm font-bold text-gray-800 mb-2">
              <i class="bi bi-box-seam mr-2 text-[#47B5AC]"></i>
              Productos Suministrados
            </label>

            <button
              type="button"
              @click="abrirBuscadorProductos"
              class="box-border w-full min-h-[94px] flex items-center justify-between px-5 py-4 bg-gray-50 hover:bg-gray-100 border border-gray-300 rounded-[14px] text-left transition group shadow-sm"
            >
              <div class="flex items-center gap-3 min-w-0">
                <i class="bi bi-card-checklist text-xl text-[#47B5AC]"></i>
                <div class="min-w-0">
                  <p class="text-sm font-bold text-gray-800 truncate">
                    {{ productosSeleccionados.length > 0 
                      ? `${productosSeleccionados.length} producto(s) seleccionado(s)` 
                      : 'Seleccionar productos suministrados...' }}
                  </p>
                  <p class="text-xs text-gray-400 mt-2">
                    Haga clic para abrir el catálogo y gestionar la selección
                  </p>
                </div>
              </div>
              <i class="bi bi-chevron-right text-gray-400 group-hover:translate-x-1 transition-transform"></i>
            </button>
          </div>

        </div>

        <!-- Footer Modal -->
        <div class="bg-gray-50 px-8 h-[86px] flex items-center justify-end gap-3">
          <button
            @click="cerrarModal"
            class="h-11 px-7 rounded-[10px] border border-gray-300 bg-gray-100 hover:bg-gray-200 font-medium transition text-gray-700"
          >
            Cancelar
          </button>

          <button
            @click="guardarProveedor"
            class="h-11 px-7 rounded-[10px] bg-[#47B5AC] text-white font-semibold hover:bg-[#47B5AC] shadow-lg hover:shadow-xl transition flex items-center"
          >
            <i class="bi bi-check-circle me-2"></i>
            {{ editandoId ? 'Actualizar' : 'Guardar Proveedor' }}
          </button>
        </div>

      </div>
    </div>

    <!-- MODAL SECUNDARIO: Buscador Amplio de Productos Suministrados -->
    <div
      v-if="mostrarBuscadorProductos"
      class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fadeIn border border-gray-100">
        
        <!-- Header Buscador -->
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <div>
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
              <i class="bi bi-boxes text-[#47B5AC]"></i>
              Asignar Productos Suministrados
            </h3>
            <p class="text-xs text-gray-500">Marque o desmarque los productos que provee este distribuidor</p>
          </div>
          <button
            type="button"
            class="w-8 h-8 rounded-full hover:bg-gray-200 transition flex items-center justify-center text-gray-500"
            @click="cerrarBuscadorProductos"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <!-- Cuerpo del Buscador -->
        <div class="p-6 space-y-4">
          <!-- Input de búsqueda -->
          <div class="relative flex items-center">
            <i class="bi bi-search absolute left-4 text-gray-400"></i>
            <input
              v-model="filtroProducto"
              type="text"
              placeholder="Buscar producto por nombre..."
              class="w-full pl-11 pr-4 py-3 border border-gray-300 text-sm rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
            />
          </div>

          <!-- Contenedor amplio de checkboxes con scroll -->
          <div class="max-h-[350px] overflow-y-auto border border-gray-200 rounded-2xl p-4 bg-gray-50/50">
            <div v-if="productosFiltrados.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <label
                v-for="producto in productosFiltrados"
                :key="producto.id"
                class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-xl hover:border-[#47B5AC]/50 cursor-pointer transition select-none shadow-xs"
              >
                <input
                  type="checkbox"
                  :value="producto.id"
                  v-model="productosSeleccionados"
                  class="w-5 h-5 text-[#47B5AC] focus:ring-[#47B5AC] rounded border-gray-300 accent-[#47B5AC]"
                />
                <div class="truncate">
                  <p class="text-sm font-medium text-gray-800 truncate">
                    {{ producto.nombre || producto.nombre_producto }}
                  </p>
                  <p v-if="producto.codigo_barras" class="text-[11px] text-gray-400 font-mono">
                    SKU: {{ producto.codigo_barras }}
                  </p>
                </div>
              </label>
            </div>

            <p v-else class="text-center text-sm text-gray-400 py-10 italic">
              No se encontraron productos coincidentes
            </p>
          </div>
        </div>

        <!-- Footer Buscador -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t">
          <span class="text-xs font-semibold text-gray-500">
            Marcados: <span class="text-[#47B5AC] text-sm font-bold">{{ productosSeleccionados.length }}</span>
          </span>

          <button
            type="button"
            @click="cerrarBuscadorProductos"
            class="px-6 py-2.5 bg-[#47B5AC] text-white rounded-xl font-medium hover:bg-[#47B5AC] transition shadow-md"
          >
            Aceptar
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Swal from 'sweetalert2'
import {
  getProveedores,
  getProductos,
  createProveedor,
  updateProveedor,
  deleteProveedor
} from '../services/proveedorService'

const mostrarModal = ref(false)
const mostrarBuscadorProductos = ref(false)

const nombre = ref('')
const telefono = ref('')
const direccion = ref('')
const productosSeleccionados = ref([])

const editandoId = ref(null)

const proveedores = ref([])
const catalogoProductos = ref([])
const filtroProducto = ref('')

// Cargar catálogo de productos
const cargarProductos = async () => {
  try {
    const res = await getProductos()
    catalogoProductos.value = Array.isArray(res) ? res : (res.data || [])
  } catch (error) {
    console.error('Error al cargar productos:', error)
  }
}

// Cargar proveedores
const cargarProveedores = async () => {
  try {
    const res = await getProveedores()
    proveedores.value = Array.isArray(res) ? res : (res.data || [])
  } catch (error) {
    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al cargar proveedores'
    })
  }
}

// Filtro computado de productos para el modal secundario
const productosFiltrados = computed(() => {
  if (!filtroProducto.value.trim()) return catalogoProductos.value
  const query = filtroProducto.value.toLowerCase().trim()
  return catalogoProductos.value.filter(p => {
    const nombreProd = (p.nombre || p.nombre_producto || '').toLowerCase()
    const sku = (p.codigo_barras || '').toLowerCase()
    return nombreProd.includes(query) || sku.includes(query)
  })
})

onMounted(() => {
  cargarProveedores()
  cargarProductos()
})

// Abrir/cerrar buscador secundario
function abrirBuscadorProductos() {
  filtroProducto.value = ''
  mostrarBuscadorProductos.value = true
}

function cerrarBuscadorProductos() {
  mostrarBuscadorProductos.value = false
}

// Abrir modal nuevo
function abrirNuevoProveedor() {
  editandoId.value = null

  nombre.value = ''
  telefono.value = ''
  direccion.value = ''
  productosSeleccionados.value = []
  filtroProducto.value = ''

  mostrarModal.value = true
}

// Guardar y actualizar
async function guardarProveedor() {

  // Nombre
  if (!nombre.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese el nombre del proveedor',
      'warning'
    )
    return
  }

  if (nombre.value.trim().length < 3) {
    Swal.fire(
      'Nombre inválido',
      'El nombre debe tener al menos 3 caracteres',
      'warning'
    )
    return
  }

  // Teléfono
  if (!telefono.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese el teléfono',
      'warning'
    )
    return
  }

  if (!/^\d+$/.test(telefono.value)) {
    Swal.fire(
      'Teléfono inválido',
      'El teléfono solo debe contener números',
      'warning'
    )
    return
  }

  if (telefono.value.length !== 8) {
    Swal.fire(
      'Teléfono inválido',
      'El teléfono debe tener 8 dígitos',
      'warning'
    )
    return
  }

  // Dirección
  if (!direccion.value.trim()) {
    Swal.fire(
      'Campo requerido',
      'Ingrese la dirección',
      'warning'
    )
    return
  }

  const data = {
    nombre_proveedor: nombre.value,
    telefono: telefono.value,
    direccion: direccion.value,
    productos: productosSeleccionados.value
  }

  try {

    if (editandoId.value) {

      await updateProveedor(
        editandoId.value,
        data
      )

      Swal.fire({
        icon: 'success',
        title: 'Proveedor actualizado',
        timer: 1500,
        showConfirmButton: false
      })

    } else {

      await createProveedor(data)

      Swal.fire({
        icon: 'success',
        title: 'Proveedor agregado correctamente',
        timer: 1500,
        showConfirmButton: false
      })
    }

    await cargarProveedores()
    cerrarModal()

  } catch (error) {

    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text:
        error.response?.data?.message ||
        'No se pudo guardar el proveedor'
    })
  }
}

// Editar
function editarProveedor(proveedor) {

  mostrarModal.value = true

  editandoId.value = proveedor.id

  nombre.value = proveedor.nombre_proveedor
  telefono.value = proveedor.telefono
  direccion.value = proveedor.direccion
  
  // Asignar los IDs de los productos ya seleccionados
  if (proveedor.productos && Array.isArray(proveedor.productos)) {
    productosSeleccionados.value = proveedor.productos.map(p => p.id)
  } else {
    productosSeleccionados.value = []
  }

  filtroProducto.value = ''
}

// Eliminar
async function eliminarProveedor(id) {

  const result = await Swal.fire({
    title: '¿Eliminar proveedor?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#47B5AC',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  })

  if (!result.isConfirmed) return

  try {

    await deleteProveedor(id)

    await cargarProveedores()

    Swal.fire({
      icon: 'success',
      title: 'Proveedor eliminado',
      text: 'El proveedor fue eliminado correctamente',
      timer: 1500,
      showConfirmButton: false
    })

  } catch (error) {

    console.error(error)

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo eliminar el proveedor'
    })
  }
}

function cerrarModal() {

  mostrarModal.value = false
  mostrarBuscadorProductos.value = false

  editandoId.value = null

  nombre.value = ''
  telefono.value = ''
  direccion.value = ''
  productosSeleccionados.value = []
  filtroProducto.value = ''
}
</script>
