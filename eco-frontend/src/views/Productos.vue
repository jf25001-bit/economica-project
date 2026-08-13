<template>
  <div class="main-interface-container box-border w-full max-w-full overflow-x-hidden p-3 sm:p-4 lg:p-6 bg-[#f8fafc] min-h-screen">
    
    <!-- BARRA SUPERIOR -->
    <div class="top-strict-navbar w-full box-border bg-white rounded-2xl shadow-sm p-4 border border-gray-100 mb-6">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
        <!-- Búsqueda -->
        <div class="search-wrapper relative flex items-center w-full sm:max-w-md">
          <i class="bi bi-search search-icon absolute left-4 text-gray-400"></i>
          <input
            v-model="buscar"
            type="text"
            placeholder="Buscar producto por nombre o SKU..."
            class="search-input-field w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm bg-gray-50/50 box-border"
          />
        </div>
        
        <!-- Botón Acción -->
        <div class="top-right-actions flex items-center w-full sm:w-auto shrink-0">
          <button class="w-full sm:w-auto bg-[#1a233a] hover:bg-[#111827] text-white px-5 py-2.5 rounded-xl shadow-sm transition-all font-medium text-sm flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer" @click="abrirModalForm">
            <i class="bi bi-plus-lg"></i>
            <span>Nuevo Producto</span>
          </button>
        </div>
      </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL FLEX -->
    <div class="flex flex-col xl:flex-row gap-5 w-full max-w-full min-w-0">
      
      <!-- PANEL IZQUIERDO (TABLA) -->
      <div class="left-content-panel flex-1 min-w-0 w-full bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 overflow-hidden">
        <div class="section-header-row flex flex-col 2xl:flex-row 2xl:justify-between 2xl:items-center gap-4 mb-6">
          <div class="title-block">
            <h1 class="text-xl sm:text-2xl font-bold text-[#1e293b]">Gestión de Productos</h1>
            <p class="text-xs font-medium text-gray-400 mt-1">Vista General y Listado ({{ total }} productos encontrados)</p>
          </div>

          <div class="filter-controls-left flex flex-wrap gap-2.5 2xl:justify-end">
            <select v-model="filtroCategoria" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 text-gray-600 outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
            </select>

            <select v-model="filtroEstado" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 text-gray-600 outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer">
              <option value="">Todos los estados</option>
              <option value="disponible">Disponible</option>
              <option value="bajo_stock">Bajo Stock</option>
            </select>

            <select v-model="filtroOrdenar" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-gray-50 text-gray-600 outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer">
              <option value="recientes">Recientes</option>
              <option value="nombre">Nombre</option>
              <option value="precio">Precio</option>
            </select>
          </div>
        </div>

        <div class="table-card-wrapper border border-gray-100 rounded-2xl overflow-hidden w-full">
          <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[700px] text-left border-collapse table-fixed">
              <thead class="bg-[#f8fafc] border-b border-gray-100">
                <tr class="text-[#64748b] text-[11px] font-bold uppercase tracking-wider">
                  <th class="px-4 py-3.5 whitespace-nowrap w-28">SKU / CÓDIGO</th>
                  <th class="px-4 py-3.5 whitespace-nowrap w-16">IMAGEN</th>
                  <th class="px-4 py-3.5 min-w-[140px]">PRODUCTO</th>
                  <th class="px-4 py-3.5 min-w-[140px]">CATEGORÍA / SUBCATEGORÍA</th>
                  <th class="px-4 py-3.5 whitespace-nowrap w-32">ESTADO</th>
                  <th class="px-4 py-3.5 whitespace-nowrap w-32">STOCK / UNIDAD</th>
                  <th class="px-4 py-3.5 whitespace-nowrap w-28">PRECIO VENTA</th>
                  <th class="px-4 py-3.5 text-center whitespace-nowrap w-24">ACCIONES</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="producto in productosFiltrados" :key="producto.id" class="hover:bg-slate-50/60 text-sm transition-colors">
                  <td class="px-4 py-3.5 font-mono text-xs text-gray-500 truncate">{{ producto.codigo_barras || 'Sin SKU' }}</td>
                  <td class="px-4 py-3.5">
                    <img
                      v-if="producto.imagenes && producto.imagenes.length"
                      :src="obtenerUrlImagen(producto.imagenes[0].ruta)"
                      alt="Producto"
                      class="w-9 h-9 object-cover rounded-lg border border-gray-200 shadow-xs"
                    />
                    <div v-else class="w-9 h-9 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-[9px] text-gray-400 bg-gray-50 shrink-0">
                      Sin foto
                    </div>
                  </td>
                  <td class="px-4 py-3.5 font-semibold text-[#1e293b] break-words text-xs sm:text-sm">{{ producto.nombre }}</td>
                  
                  <td class="px-4 py-3.5 break-words">
                    <div class="flex flex-col justify-center">
                      <span class="font-medium text-gray-700 text-xs">{{ producto.subcategoria?.categoria?.nombre || 'General' }}</span>
                      <span class="text-[11px] text-gray-400 mt-0.5">{{ producto.subcategoria?.nombre || 'Sin subcategoría' }}</span>
                    </div>
                  </td>

                  <td class="px-4 py-3.5 whitespace-nowrap">
                    <span 
                      class="rounded-full px-2.5 py-1 text-[11px] font-semibold inline-flex items-center gap-1.5"
                      :class="Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'bg-amber-500' : 'bg-emerald-500'"></span>
                      {{ Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'Bajo Stock' : 'Disponible' }}
                    </span>
                  </td>

                  <td class="px-4 py-3.5 whitespace-nowrap">
                    <span :class="Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'text-amber-600 font-bold' : 'text-gray-700 font-medium'">
                      {{ producto.stock }}
                    </span>
                    <span class="text-xs text-gray-400 ml-1">
                      ({{ producto.unidad_medida?.nombre || producto.unidad_medida_id || 'pza' }})
                    </span>
                  </td>

                  <td class="px-4 py-3.5 font-bold text-[#0f172a] whitespace-nowrap">${{ producto.precio_venta }}</td>
                  
                  <td class="px-4 py-3.5 text-center whitespace-nowrap">
                    <div class="flex gap-1.5 justify-center items-center">
                      <button class="p-1.5 text-gray-600 hover:text-blue-600 rounded-lg border border-gray-200 hover:bg-white shadow-xs transition cursor-pointer" @click="editarProducto(producto)" title="Editar producto">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="p-1.5 text-red-500 hover:text-red-700 rounded-lg border border-gray-200 hover:bg-white shadow-xs transition cursor-pointer" @click="eliminarProducto(producto.id)" title="Eliminar producto">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="productosFiltrados.length === 0">
                  <td colspan="8" class="text-center py-8 text-gray-400 italic text-xs">No se encontraron productos con los filtros seleccionados.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- PANEL DERECHO (RESUMEN) -->
      <div class="right-widgets-panel w-full xl:w-[260px] shrink-0 flex flex-col gap-4">
        <div class="inventory-card-widget bg-white rounded-2xl shadow-sm p-4 border border-gray-100 h-fit">
          <h2 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-3">Resumen de Inventario</h2>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-slate-50 rounded-xl mb-2">
            <div>
              <span class="block text-xs text-gray-500 font-medium">Total ítems</span>
              <span class="text-xl font-bold text-slate-800">{{ resumen.total }}</span>
            </div>
          </div>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-emerald-50/60 rounded-xl mb-2">
            <div>
              <span class="block text-xs text-emerald-600 font-medium">Disponibles</span>
              <span class="text-xl font-bold text-emerald-700">{{ resumen.disponibles }}</span>
            </div>
          </div>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-amber-50/60 rounded-xl">
            <div>
              <span class="block text-xs text-amber-600 font-medium">Bajo Stock</span>
              <span class="text-xl font-bold text-amber-700">{{ resumen.bajo_stock }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL REGISTRO / EDICIÓN -->
    <div v-if="mostrarModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 flex flex-col my-auto box-border">
        
        <div class="h-14 px-5 flex justify-between items-center bg-[#1a233a] shrink-0">
          <h3 class="font-bold text-white text-sm sm:text-base">
            {{ esEditando ? 'Modificar Producto Existente' : 'Agregar Nuevo Producto' }}
          </h3>
          <button type="button" class="w-7 h-7 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center transition cursor-pointer" @click="cerrarModal">
            <i class="bi bi-x-lg text-xs"></i>
          </button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="p-5 flex flex-col gap-3.5 max-h-[80vh] overflow-y-auto text-xs w-full box-border">
          <!-- Imagen -->
          <div>
            <label class="block font-semibold text-gray-700 mb-1">Imagen del Producto</label>
            <div class="flex items-center gap-3">
              <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center shrink-0 border border-gray-200">
                <img v-if="imagenPreview" :src="imagenPreview" alt="Preview" class="w-full h-full object-cover" />
                <i v-else class="bi bi-image text-gray-300 text-lg"></i>
                <button 
                  v-if="imagenPreview" 
                  type="button" 
                  @click="removerImagen" 
                  class="absolute top-0 right-0 bg-red-500 text-white rounded-bl p-0.5 leading-none shadow-md hover:bg-red-600 transition cursor-pointer"
                  title="Quitar imagen"
                >
                  <i class="bi bi-x text-xs"></i>
                </button>
              </div>

              <div class="flex-1 min-w-0">
                <label class="box-border w-full h-[56px] flex flex-col items-center justify-center px-3 bg-gray-50 text-gray-500 rounded-xl border border-gray-200 border-dashed cursor-pointer hover:bg-gray-100 transition text-center">
                  <i class="bi bi-cloud-upload text-sm text-blue-600 mb-0.5"></i>
                  <span class="text-[10px] font-medium truncate w-full">Seleccionar imagen archivo</span>
                  <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="manejarCambioImagen" />
                </label>
              </div>
            </div>
          </div>

          <!-- Nombre -->
          <div class="w-full min-w-0">
            <label class="block font-semibold text-gray-700 mb-1">Nombre del Producto</label>
            <input type="text" v-model="nuevoProducto.nombre" required placeholder="Ej. MacBook Pro M3" class="w-full h-9 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none box-border" />
          </div>

          <!-- Código -->
          <div class="w-full min-w-0">
            <label class="block font-semibold text-gray-700 mb-1">Código de Barras / SKU</label>
            <input type="text" v-model="nuevoProducto.codigo_barras" required placeholder="Ej. 7501055300075" class="w-full h-9 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none box-border" />
          </div>

          <!-- Grid Categoria y Unidad -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
            <div class="min-w-0 w-full">
              <label class="block font-semibold text-gray-700 mb-1">Categoría</label>
              <button type="button" @click="abrirBuscador('subcategoria')" class="w-full h-9 flex justify-between items-center px-3 border border-gray-200 rounded-xl text-left bg-gray-50 hover:bg-gray-100 transition truncate text-gray-700 cursor-pointer box-border">
                <span class="truncate">{{ nombreSubcategoriaSeleccionada || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1 shrink-0"></i>
              </button>
            </div>
            <div class="min-w-0 w-full">
              <label class="block font-semibold text-gray-700 mb-1">Unidad de Medida</label>
              <button type="button" @click="abrirBuscador('unidad_medida')" class="w-full h-9 flex justify-between items-center px-3 border border-gray-200 rounded-xl text-left bg-gray-50 hover:bg-gray-100 transition truncate text-gray-700 cursor-pointer box-border">
                <span class="truncate">{{ nombreUnidadMedidaSeleccionada || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1 shrink-0"></i>
              </button>
            </div>
          </div>

          <!-- Precio -->
          <div class="w-full min-w-0">
            <label class="block font-semibold text-gray-700 mb-1">Precio Venta ($)</label>
            <input type="number" step="0.01" v-model="nuevoProducto.precio_venta" required placeholder="0.00" class="w-full h-9 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none box-border" />
          </div>

          <!-- Stock -->
          <div :class="esEditando ? 'grid grid-cols-2 gap-3 w-full' : 'w-full min-w-0'">
            <div v-if="esEditando" class="min-w-0">
              <label class="block font-semibold text-gray-700 mb-1">Stock</label>
              <input type="number" v-model="nuevoProducto.stock" required class="w-full h-9 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none box-border" />
            </div>
            <div class="min-w-0">
              <label class="block font-semibold text-gray-700 mb-1">Stock Mínimo</label>
              <input type="number" v-model="nuevoProducto.stock_minimo" required class="w-full h-9 px-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-600 outline-none box-border" />
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-2 mt-2 pt-3 border-t border-gray-100 shrink-0">
            <button type="button" class="h-9 px-4 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-medium transition cursor-pointer" :disabled="guardando" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="h-9 px-4 bg-[#1a233a] text-white rounded-xl hover:bg-[#111827] font-medium flex items-center gap-2 transition disabled:opacity-50 cursor-pointer" :disabled="guardando">
              <span v-if="guardando" class="animate-spin inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full"></span>
              {{ guardando ? 'Procesando...' : (esEditando ? 'Actualizar Producto' : 'Guardar Producto') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL BUSCADOR SECUNDARIO -->
    <div v-if="mostrarBuscador" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-gray-100 flex flex-col my-auto box-border">
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
          <h4 class="font-bold text-gray-800 text-xs sm:text-sm">
            Buscar {{ tipoBuscador === 'subcategoria' ? 'Categoría' : 'Unidad de Medida' }}
          </h4>
          <button type="button" class="text-gray-400 hover:text-gray-600 cursor-pointer" @click="cerrarBuscador"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div class="p-4 flex flex-col gap-3 max-h-[75vh] overflow-y-auto">
          <div class="relative flex items-center w-full">
            <i class="bi bi-search absolute left-3 text-gray-400 text-xs"></i>
            <input
              v-model="filtroBuscadorInterno"
              type="text"
              placeholder="Filtrar por nombre..."
              class="w-full pl-8 pr-3 py-1.5 border border-gray-200 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 box-border"
            />
          </div>

          <div v-if="tipoBuscador === 'subcategoria'" class="max-h-[220px] overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 flex flex-col divide-y divide-gray-100">
            <div
              v-for="cat in categoriasFiltradasBuscador"
              :key="cat.id"
              class="bg-white"
            >
              <button
                type="button"
                @click="cat.subcategoriasFiltradas.length === 0 ? seleccionarCategoriaBuscador(cat) : alternarCategoriaBuscador(cat.id)"
                class="w-full text-left px-3 py-2 text-xs text-gray-800 hover:text-blue-600 hover:bg-blue-50/50 font-bold transition flex justify-between items-center cursor-pointer"
              >
                <span class="truncate">
                  <i class="bi bi-folder-fill mr-1.5 text-blue-600"></i>
                  {{ cat.nombre }}
                </span>
                <i
                  v-if="cat.subcategoriasFiltradas.length > 0"
                  :class="categoriaBuscadorAbierta === cat.id ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"
                  class="text-blue-600 ml-1"
                ></i>
                <span v-else class="text-[9px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-bold shrink-0">
                  Usar categoría
                </span>
              </button>

              <div v-if="categoriaBuscadorAbierta === cat.id" class="bg-gray-50 border-t border-gray-100">
                <button
                  type="button"
                  v-for="sub in cat.subcategoriasFiltradas"
                  :key="sub.id"
                  @click="seleccionarItemBuscador(sub)"
                  class="w-full text-left pl-8 pr-3 py-2 text-xs text-gray-600 hover:bg-white hover:text-blue-600 font-medium transition flex justify-between items-center cursor-pointer"
                >
                  <span class="truncate">{{ sub.nombre }}</span>
                  <span class="text-[9px] bg-gray-200/60 text-gray-500 px-1.5 py-0.5 rounded font-mono shrink-0 ml-1">ID: {{ sub.id }}</span>
                </button>

                <div v-if="cat.subcategoriasFiltradas.length === 0" class="pl-8 pr-3 py-2 text-xs text-gray-400 italic">
                  Sin subcategorías.
                </div>
              </div>
            </div>

            <div v-if="categoriasFiltradasBuscador.length === 0" class="text-center py-6 text-xs text-gray-400 italic">
              No hay coincidencias.
            </div>
          </div>

          <div v-else class="max-h-[200px] overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 flex flex-col divide-y divide-gray-100">
            <button
              type="button"
              v-for="item in listaFiltradaBuscador"
              :key="item.id"
              @click="seleccionarItemBuscador(item)"
              class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-white hover:text-blue-600 font-medium transition flex justify-between items-center cursor-pointer"
            >
              <span class="truncate">{{ item.nombre }}</span>
              <span class="text-[9px] bg-gray-200/60 text-gray-500 px-1.5 py-0.5 rounded font-mono shrink-0 ml-1">ID: {{ item.id }}</span>
            </button>
            <div v-if="listaFiltradaBuscador.length === 0" class="text-center py-6 text-xs text-gray-400 italic">
              No hay coincidencias.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  getProductos, 
  getAuxiliares, 
  guardarProductoAPI, 
  subirImagenAPI, 
  eliminarProductoAPI 
} from '@/services/productoService'

const productos = ref([]) 
const categorias = ref([]) 
const subcategorias = ref([]) 
const unidadesMedida = ref([]) 

const mostrarModal = ref(false)
const esEditando = ref(false)
const productoIdSeleccionado = ref(null)
const guardando = ref(false) 

const fileInput = ref(null)
const imagenSeleccionada = ref(null)
const imagenPreview = ref(null)

const mostrarBuscador = ref(false)
const tipoBuscador = ref('') 
const filtroBuscadorInterno = ref('')
const categoriaBuscadorAbierta = ref(null)
const nombreSubcategoriaSeleccionada = ref('')
const nombreUnidadMedidaSeleccionada = ref('')

const buscar = ref('')
const filtroCategoria = ref('')
const filtroEstado = ref('')
const filtroOrdenar = ref('recientes')

const modeloProductoLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  categoria_id: '',
  sub_categoria_id: '',
  unidad_medida_id: '',
  stock: 0,
  stock_minimo: 5,
  precio_venta: 0
})

const nuevoProducto = ref(modeloProductoLimpio())

const productosFiltrados = computed(() => {
  let resultado = [...productos.value]

  if (buscar.value.trim()) {
    const query = buscar.value.toLowerCase().trim()
    resultado = resultado.filter(p => 
      (p.nombre || '').toLowerCase().includes(query) || 
      (p.codigo_barras || '').toLowerCase().includes(query)
    )
  }

  if (filtroCategoria.value) {
    resultado = resultado.filter(p => {
      const idCat = p.subcategoria?.categoria?.id || p.subcategoria?.categoria_id || p.categoria_id
      return String(idCat) === String(filtroCategoria.value)
    })
  }

  if (filtroEstado.value) {
    resultado = resultado.filter(p => {
      const limiteMinimo = Number(p.stock_minimo) || 5
      const esBajoStock = Number(p.stock) <= limiteMinimo
      if (filtroEstado.value === 'bajo_stock') return esBajoStock
      if (filtroEstado.value === 'disponible') return !esBajoStock
      return true
    })
  }

  if (filtroOrdenar.value === 'nombre') {
    resultado.sort((a, b) => (a.nombre || '').localeCompare(b.nombre || ''))
  } else if (filtroOrdenar.value === 'precio') {
    resultado.sort((a, b) => Number(a.precio_venta) - Number(b.precio_venta))
  } else if (filtroOrdenar.value === 'recientes') {
    resultado.sort((a, b) => Number(b.id) - Number(a.id))
  }

  return resultado
})

const total = computed(() => productosFiltrados.value.length)

const resumen = computed(() => {
  const listaCompleta = productos.value
  return {
    total: listaCompleta.length,
    disponibles: listaCompleta.filter(p => Number(p.stock) > (Number(p.stock_minimo) || 5)).length,
    bajo_stock: listaCompleta.filter(p => Number(p.stock) <= (Number(p.stock_minimo) || 5)).length
  }
})

const obtenerUrlImagen = (ruta) => {
  if (!ruta) return ''
  const rutaLimpia = ruta.startsWith('/') ? ruta.substring(1) : ruta
  return `http://127.0.0.1:8000/storage/${rutaLimpia}`
}

const manejarCambioImagen = (e) => {
  const file = e.target.files[0]
  if (file) {
    imagenSeleccionada.value = file
    imagenPreview.value = URL.createObjectURL(file)
  }
}

const removerImagen = () => {
  imagenSeleccionada.value = null
  imagenPreview.value = null
  if (fileInput.value) fileInput.value.value = ''
}

const listaFiltradaBuscador = computed(() => {
  const query = filtroBuscadorInterno.value.toLowerCase().trim()
  if (tipoBuscador.value === 'unidad_medida') {
    return unidadesMedida.value.filter(um => (um.nombre || '').toLowerCase().includes(query))
  }
  return []
})

const categoriasFiltradasBuscador = computed(() => {
  const query = filtroBuscadorInterno.value.toLowerCase().trim()

  return categorias.value
    .map(cat => {
      const subcategoriasCategoria = Array.isArray(cat.subcategorias)
        ? cat.subcategorias
        : subcategorias.value.filter(sub => String(sub.categoria_id) === String(cat.id))

      const categoriaCoincide = (cat.nombre || '').toLowerCase().includes(query)
      const subcategoriasFiltradas = query && !categoriaCoincide
        ? subcategoriasCategoria.filter(sub => (sub.nombre || '').toLowerCase().includes(query))
        : subcategoriasCategoria

      return {
        ...cat,
        subcategoriasFiltradas
      }
    })
    .filter(cat => {
      if (!query) return true
      return (cat.nombre || '').toLowerCase().includes(query) || cat.subcategoriasFiltradas.length > 0
    })
})

const abrirBuscador = (tipo) => {
  tipoBuscador.value = tipo
  filtroBuscadorInterno.value = ''
  categoriaBuscadorAbierta.value = null
  mostrarBuscador.value = true
}

const cerrarBuscador = () => {
  mostrarBuscador.value = false
  tipoBuscador.value = ''
  categoriaBuscadorAbierta.value = null
}

const alternarCategoriaBuscador = (categoriaId) => {
  categoriaBuscadorAbierta.value = categoriaBuscadorAbierta.value === categoriaId ? null : categoriaId
}

const seleccionarCategoriaBuscador = (categoria) => {
  nuevoProducto.value.categoria_id = categoria.id
  nuevoProducto.value.sub_categoria_id = ''
  nombreSubcategoriaSeleccionada.value = categoria.nombre
  cerrarBuscador()
}

const seleccionarItemBuscador = (item) => {
  if (tipoBuscador.value === 'subcategoria') {
    nuevoProducto.value.categoria_id = item.categoria_id || item.categoria?.id || ''
    nuevoProducto.value.sub_categoria_id = item.id
    nombreSubcategoriaSeleccionada.value = item.nombre
  } else if (tipoBuscador.value === 'unidad_medida') {
    nuevoProducto.value.unidad_medida_id = item.id
    nombreUnidadMedidaSeleccionada.value = item.nombre
  }
  cerrarBuscador()
}

// --- CONEXIÓN AL SERVICIO ---

const cargarAuxiliaresFormulario = async () => {
  try {
    const [resCat, resSub, resUnidades] = await getAuxiliares()
    categorias.value = resCat.data.data || resCat.data
    subcategorias.value = resSub.data.data || resSub.data
    unidadesMedida.value = resUnidades.data.data || resUnidades.data
  } catch (err) {
    console.error('Error cargando catálogos:', err)
  }
}

const cargarProductos = async () => {
  try {
    const res = await getProductos()
    productos.value = res.data.data || res.data
  } catch (error) {
    console.error('Error cargando productos:', error)
  }
}

const guardarProducto = async () => {
  if (guardando.value) return 
  if ((!nuevoProducto.value.sub_categoria_id && !nuevoProducto.value.categoria_id) || !nuevoProducto.value.unidad_medida_id) {
    alert('Por favor selecciona Categoría y Unidad de Medida válidos.')
    return
  }

  guardando.value = true 

  try {
    const res = await guardarProductoAPI(nuevoProducto.value, esEditando.value ? productoIdSeleccionado.value : null)

    if (imagenSeleccionada.value) {
      const productoId = esEditando.value 
        ? productoIdSeleccionado.value 
        : (res.data.data?.id || res.data.id)

      if (productoId) {
        const formDataImagen = new FormData()
        formDataImagen.append('imagen', imagenSeleccionada.value)
        formDataImagen.append('producto_id', productoId)
        await subirImagenAPI(formDataImagen)
      }
    }

    cerrarModal()
    await cargarProductos()
  } catch (error) {
    console.error('Error al guardar:', error)
    alert('Ocurrió un error al procesar la solicitud.')
  } finally {
    guardando.value = false 
  }
}

const eliminarProducto = async (id) => {
  if (!confirm('¿Estás seguro de eliminar este producto?')) return
  try {
    await eliminarProductoAPI(id)
    cargarProductos()
  } catch (error) {
    console.error('Error al eliminar:', error)
  }
}

const abrirModalForm = () => { 
  esEditando.value = false
  nombreSubcategoriaSeleccionada.value = ''
  nombreUnidadMedidaSeleccionada.value = ''
  removerImagen()
  mostrarModal.value = true 
}

const cerrarModal = () => {
  mostrarModal.value = false
  esEditando.value = false
  productoIdSeleccionado.value = null
  nombreSubcategoriaSeleccionada.value = ''
  nombreUnidadMedidaSeleccionada.value = ''
  removerImagen()
  nuevoProducto.value = modeloProductoLimpio()
}

const editarProducto = (producto) => {
  esEditando.value = true
  productoIdSeleccionado.value = producto.id
  
  nombreSubcategoriaSeleccionada.value = producto.subcategoria?.nombre || (producto.sub_categoria_id ? 'ID: ' + producto.sub_categoria_id : '')
  nombreUnidadMedidaSeleccionada.value = producto.unidad_medida?.nombre || (producto.unidad_medida_id ? 'ID: ' + producto.unidad_medida_id : '')

  nuevoProducto.value = {
    codigo_barras: producto.codigo_barras || '',
    nombre: producto.nombre || '',
    categoria_id: producto.subcategoria?.categoria?.id || producto.categoria_id || '',
    sub_categoria_id: producto.sub_categoria_id || '',
    unidad_medida_id: producto.unidad_medida_id || '',
    stock: producto.stock || 0,
    stock_minimo: producto.stock_minimo || 5,
    precio_venta: producto.precio_venta || 0
  }

  if (producto.imagenes && producto.imagenes.length) {
    imagenPreview.value = obtenerUrlImagen(producto.imagenes[0].ruta)
  } else {
    imagenPreview.value = null
  }
  imagenSeleccionada.value = null
  
  mostrarModal.value = true
}

onMounted(() => {
  cargarProductos()
  cargarAuxiliaresFormulario()
})
</script>

<style scoped>
.main-interface-container {
  font-family: 'Inter', sans-serif;
  width: 100%;
}
</style>
