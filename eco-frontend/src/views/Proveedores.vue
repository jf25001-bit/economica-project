<template>
  <div class="min-h-screen bg-slate-50/50 p-4 sm:p-6 md:p-8 w-full max-w-full overflow-x-hidden box-border">

    <!-- Encabezado de Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8 w-full">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-800 tracking-tight truncate">
          Proveedores
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">
          Gestión de proveedores y asignación de catálogo de productos
        </p>
      </div>

      <button
        @click="abrirNuevoProveedor"
        class="inline-flex items-center justify-center gap-2 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold px-5 py-2.5 sm:px-6 sm:py-3 rounded-2xl shadow-lg shadow-[#2B3A4A]/20 transition-all active:scale-95 cursor-pointer text-sm sm:text-base w-full sm:w-auto shrink-0"
      >
        <i class="bi bi-person-plus-fill text-lg"></i>
        <span>Nuevo Proveedor</span>
      </button>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-3 sm:p-4 mb-6 w-full max-w-full box-border">
      <div class="relative w-full">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
          <i class="bi bi-search text-base"></i>
        </span>
        <input
          v-model="filtroProveedor"
          type="text"
          placeholder="Buscar proveedor por nombre, teléfono o dirección..."
          class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
        />
      </div>
    </div>

    <!-- Tabla de Proveedores -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden w-full max-w-full">
      <div class="overflow-x-auto w-full">
        <table class="w-full min-w-[650px] table-fixed">
          <thead>
            <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider">
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left w-16">ID</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left w-1/4">Nombre</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left w-28">Teléfono</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left w-1/4">Dirección</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-left">Productos Suministrados</th>
              <th class="px-4 py-3 sm:px-6 sm:py-4 text-right w-24">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="proveedor in proveedoresFiltrados"
              :key="proveedor.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <!-- ID -->
              <td class="px-4 py-3 sm:px-6 sm:py-4">
                <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                  #{{ proveedor.id }}
                </span>
              </td>

              <!-- Nombre -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 font-bold text-slate-800">
                <div class="flex items-center gap-2 min-w-0">
                  <i class="bi bi-truck text-slate-400 text-base shrink-0"></i>
                  <span class="truncate block" :title="proveedor.nombre_proveedor">{{ proveedor.nombre_proveedor }}</span>
                </div>
              </td>

              <!-- Teléfono -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 text-slate-600 text-sm font-medium whitespace-nowrap">
                {{ proveedor.telefono }}
              </td>

              <!-- Dirección -->
              <td class="px-4 py-3 sm:px-6 sm:py-4 text-slate-600 text-sm font-medium">
                <p class="line-clamp-2 break-words" :title="proveedor.direccion">
                  {{ proveedor.direccion }}
                </p>
              </td>

              <!-- Productos Suministrados -->
              <td class="px-4 py-3 sm:px-6 sm:py-4">
                <div class="flex flex-wrap gap-1.5 max-h-20 overflow-y-auto pr-1">
                  <span
                    v-for="prod in proveedor.productos"
                    :key="prod.id"
                    class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/60 max-w-full truncate"
                  >
                    <i class="bi bi-box-seam-fill mr-1 text-[10px] shrink-0"></i>
                    <span class="truncate">{{ prod.nombre || prod.nombre_producto }}</span>
                  </span>
                  <span v-if="!proveedor.productos || proveedor.productos.length === 0" class="text-xs text-slate-400 italic">
                    Sin productos
                  </span>
                </div>
              </td>

              <!-- Acciones -->
              <td class="px-4 py-3 sm:px-6 sm:py-4">
                <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                  <button
                    @click="editarProveedor(proveedor)"
                    class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-[#2B3A4A] hover:text-white transition cursor-pointer shrink-0"
                    title="Editar Proveedor"
                  >
                    <i class="bi bi-pencil-fill text-xs sm:text-sm"></i>
                  </button>

                  <button
                    @click="eliminarProveedor(proveedor.id)"
                    class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition cursor-pointer shrink-0"
                    title="Eliminar Proveedor"
                  >
                    <i class="bi bi-trash-fill text-xs sm:text-sm"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Estado Vacío -->
            <tr v-if="proveedoresFiltrados.length === 0">
              <td colspan="6" class="py-12 sm:py-16 text-center text-slate-400">
                <i class="bi bi-folder-x text-3xl sm:text-4xl block mb-2 opacity-50"></i>
                <p class="font-medium text-xs sm:text-sm">No se encontraron proveedores registrados.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Formulario Principal -->
    <div v-if="mostrarModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-40 p-3 sm:p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-100 box-border my-auto">
        
        <!-- Modal Header -->
        <div class="bg-[#2B3A4A] text-white px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center shrink-0 w-full box-border">
          <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center text-sky-300 shrink-0">
              <i class="bi bi-truck text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
              <h2 class="text-base sm:text-lg font-extrabold leading-none truncate">
                {{ editandoId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
              </h2>
              <p class="text-sky-200/80 text-[11px] sm:text-xs mt-1 truncate">Complete los datos y gestione sus productos</p>
            </div>
          </div>

          <button
            @click="cerrarModal"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-slate-200 cursor-pointer shrink-0"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 w-full box-border">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
            <!-- Nombre -->
            <div class="min-w-0 w-full">
              <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">
                Nombre del proveedor
              </label>
              <input
                v-model="nombre"
                type="text"
                placeholder="Ingrese el nombre"
                class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
              />
            </div>

            <!-- Teléfono -->
            <div class="min-w-0 w-full">
              <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">
                Teléfono
              </label>
              <input
                v-model="telefono"
                type="text"
                maxlength="8"
                placeholder="Ej: 77778888"
                @input="telefono = telefono.replace(/[^0-9]/g, '')"
                class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
              />
            </div>
          </div>

          <!-- Dirección -->
          <div class="w-full">
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">
              Dirección
            </label>
            <textarea
              v-model="direccion"
              rows="2"
              placeholder="Ingrese la dirección completa"
              class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium resize-none box-border"
            ></textarea>
          </div>

          <!-- Disparador Modal de Productos -->
          <div class="w-full">
            <label class="block text-slate-700 text-xs font-bold uppercase tracking-wider mb-1.5">
              Productos Suministrados
            </label>
            <button
              type="button"
              @click="abrirBuscadorProductos"
              class="w-full p-3 sm:p-4 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl flex items-center justify-between transition cursor-pointer group gap-2 box-border"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center font-bold shrink-0">
                  <i class="bi bi-boxes text-sm sm:text-base"></i>
                </div>
                <div class="text-left min-w-0">
                  <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                    {{ productosSeleccionados.length > 0 
                      ? `${productosSeleccionados.length} producto(s) seleccionado(s)` 
                      : 'Seleccionar productos...' }}
                  </p>
                  <p class="text-[11px] sm:text-xs text-slate-400 truncate">Haga clic para abrir y gestionar el catálogo</p>
                </div>
              </div>
              <i class="bi bi-chevron-right text-slate-400 group-hover:translate-x-1 transition-transform shrink-0 text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end gap-2 sm:gap-3 p-4 sm:p-5 bg-slate-50 border-t border-slate-100 shrink-0 w-full box-border">
          <button
            @click="cerrarModal"
            class="px-4 py-2 sm:px-5 sm:py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold text-xs sm:text-sm hover:bg-slate-100 transition cursor-pointer"
          >
            Cancelar
          </button>

          <button
            @click="guardarProveedor"
            :disabled="guardando"
            class="px-5 py-2 sm:px-6 sm:py-2.5 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-[#2B3A4A]/20 disabled:opacity-50 transition cursor-pointer flex items-center gap-2"
          >
            <i v-if="guardando" class="bi bi-arrow-clockwise animate-spin text-sm sm:text-base"></i>
            <span>{{ guardando ? 'Guardando...' : (editandoId ? 'Actualizar' : 'Guardar Proveedor') }}</span>
          </button>
        </div>

      </div>
    </div>

    <!-- MODAL SECUNDARIO: Buscador de Productos -->
    <div v-if="mostrarBuscadorProductos" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl sm:rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-slate-100 box-border my-auto">
        
        <!-- Header Buscador -->
        <div class="bg-[#2B3A4A] text-white px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center shrink-0 w-full box-border">
          <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center text-sky-300 shrink-0">
              <i class="bi bi-boxes text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
              <h3 class="text-base sm:text-lg font-extrabold leading-none truncate">
                Asignar Productos
              </h3>
              <p class="text-sky-200/80 text-[11px] sm:text-xs mt-1 truncate">Marque los productos suministrados por este proveedor</p>
            </div>
          </div>

          <button
            type="button"
            @click="cerrarBuscadorProductos"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-slate-200 cursor-pointer shrink-0"
          >
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <!-- Cuerpo del Buscador -->
        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 w-full box-border">
          <!-- Input Búsqueda -->
          <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
              <i class="bi bi-search text-base"></i>
            </span>
            <input
              v-model="filtroProducto"
              type="text"
              placeholder="Buscar por nombre o código..."
              class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium box-border"
            />
          </div>

          <!-- Contenedor de Checkboxes -->
          <div class="max-h-[280px] sm:max-h-[320px] overflow-y-auto border border-slate-200 rounded-2xl p-2 sm:p-3 bg-slate-50/50 w-full box-border">
            <div v-if="productosFiltrados.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full">
              <label
                v-for="producto in productosFiltrados"
                :key="producto.id"
                class="flex items-center gap-3 p-2.5 sm:p-3 bg-white border border-slate-200 rounded-xl hover:border-slate-300 cursor-pointer transition select-none shadow-xs min-w-0 box-border"
              >
                <input
                  type="checkbox"
                  :value="producto.id"
                  v-model="productosSeleccionados"
                  class="w-4 h-4 rounded border-slate-300 text-[#2B3A4A] focus:ring-[#2B3A4A]/20 accent-[#2B3A4A] shrink-0"
                />
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-bold text-slate-800 truncate" :title="producto.nombre || producto.nombre_producto">
                    {{ producto.nombre || producto.nombre_producto }}
                  </p>
                  <p v-if="producto.codigo_barras" class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">
                    SKU: {{ producto.codigo_barras }}
                  </p>
                </div>
              </label>
            </div>

            <div v-else class="text-center py-10 sm:py-12 text-slate-400">
              <i class="bi bi-box-seam text-2xl sm:text-3xl block mb-1 opacity-50"></i>
              <p class="text-xs italic">No se encontraron productos coincidentes</p>
            </div>
          </div>
        </div>

        <!-- Footer Buscador -->
        <div class="flex justify-between items-center p-4 sm:p-5 bg-slate-50 border-t border-slate-100 shrink-0 w-full box-border">
          <span class="text-xs font-bold text-slate-500">
            Seleccionados: <span class="text-[#2B3A4A] text-xs sm:text-sm font-extrabold">{{ productosSeleccionados.length }}</span>
          </span>

          <button
            type="button"
            @click="cerrarBuscadorProductos"
            class="px-5 py-2 sm:px-6 sm:py-2.5 bg-[#2B3A4A] hover:bg-[#1F2B37] text-white font-bold text-xs sm:text-sm rounded-xl shadow transition cursor-pointer"
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
const guardando = ref(false)

const nombre = ref('')
const telefono = ref('')
const direccion = ref('')
const productosSeleccionados = ref([])

const editandoId = ref(null)

const proveedores = ref([])
const catalogoProductos = ref([])
const filtroProveedor = ref('')
const filtroProducto = ref('')

const proveedoresFiltrados = computed(() => {
  if (!filtroProveedor.value.trim()) return proveedores.value
  const query = filtroProveedor.value.toLowerCase().trim()
  return proveedores.value.filter(p => {
    const nom = (p.nombre_proveedor || '').toLowerCase()
    const tel = (p.telefono || '').toLowerCase()
    const dir = (p.direccion || '').toLowerCase()
    return nom.includes(query) || tel.includes(query) || dir.includes(query)
  })
})

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

const cargarProductos = async () => {
  try {
    const res = await getProductos()
    catalogoProductos.value = Array.isArray(res) ? res : (res.data || [])
  } catch (error) {
    console.error('Error al cargar productos:', error)
  }
}

const cargarProveedores = async () => {
  try {
    const res = await getProveedores()
    proveedores.value = Array.isArray(res) ? res : (res.data || [])
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al cargar proveedores',
      confirmButtonColor: '#2B3A4A'
    })
  }
}

function abrirBuscadorProductos() {
  filtroProducto.value = ''
  mostrarBuscadorProductos.value = true
}

function cerrarBuscadorProductos() {
  mostrarBuscadorProductos.value = false
}

function abrirNuevoProveedor() {
  editandoId.value = null
  nombre.value = ''
  telefono.value = ''
  direccion.value = ''
  productosSeleccionados.value = []
  filtroProducto.value = ''
  mostrarModal.value = true
}

async function guardarProveedor() {
  if (guardando.value) return

  if (!nombre.value.trim()) {
    Swal.fire({
      icon: 'warning',
      title: 'Campo requerido',
      text: 'Ingrese el nombre del proveedor',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  if (nombre.value.trim().length < 3) {
    Swal.fire({
      icon: 'warning',
      title: 'Nombre inválido',
      text: 'El nombre debe tener al menos 3 caracteres',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  if (!telefono.value.trim()) {
    Swal.fire({
      icon: 'warning',
      title: 'Campo requerido',
      text: 'Ingrese el teléfono',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  if (!/^\d+$/.test(telefono.value)) {
    Swal.fire({
      icon: 'warning',
      title: 'Teléfono inválido',
      text: 'El teléfono solo debe contener números',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  if (telefono.value.length !== 8) {
    Swal.fire({
      icon: 'warning',
      title: 'Teléfono inválido',
      text: 'El teléfono debe tener 8 dígitos',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  if (!direccion.value.trim()) {
    Swal.fire({
      icon: 'warning',
      title: 'Campo requerido',
      text: 'Ingrese la dirección',
      confirmButtonColor: '#2B3A4A'
    })
    return
  }

  guardando.value = true

  const data = {
    nombre_proveedor: nombre.value,
    telefono: telefono.value,
    direccion: direccion.value,
    productos: productosSeleccionados.value
  }

  try {
    if (editandoId.value) {
      await updateProveedor(editandoId.value, data)
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
      text: error.response?.data?.message || 'No se pudo guardar el proveedor',
      confirmButtonColor: '#2B3A4A'
    })
  } finally {
    guardando.value = false
  }
}

function editarProveedor(proveedor) {
  mostrarModal.value = true
  editandoId.value = proveedor.id
  nombre.value = proveedor.nombre_proveedor
  telefono.value = proveedor.telefono
  direccion.value = proveedor.direccion
  
  if (proveedor.productos && Array.isArray(proveedor.productos)) {
    productosSeleccionados.value = proveedor.productos.map(p => p.id)
  } else {
    productosSeleccionados.value = []
  }

  filtroProducto.value = ''
}

async function eliminarProveedor(id) {
  const result = await Swal.fire({
    title: '¿Eliminar proveedor?',
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
    await deleteProveedor(id)
    await cargarProveedores()

    Swal.fire({
      icon: 'success',
      title: 'Proveedor eliminado',
      timer: 1500,
      showConfirmButton: false
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo eliminar el proveedor',
      confirmButtonColor: '#2B3A4A'
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