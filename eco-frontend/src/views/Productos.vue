<template>
  <div class="main-interface-container p-6">
    <div class="top-strict-navbar flex items-center justify-between mb-6 bg-white rounded-2xl shadow-md p-4">
      <div class="search-wrapper relative flex items-center max-w-md w-full">
        <i class="bi bi-search search-icon absolute left-4 text-gray-400"></i>
        <input
          v-model="buscar"
          type="text"
          placeholder="Buscar producto por nombre o SKU..."
          class="search-input-field w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
        />
      </div>
      
      <div class="top-right-actions flex items-center gap-4">
        <button class="bg-[#46674A] hover:bg-[#3b5740] text-white px-5 py-3 rounded-xl shadow-md transition font-medium" @click="abrirModalForm">
          <i class="bi bi-plus-lg mr-2"></i> Nuevo Producto
        </button>
      </div>
    </div>

    <div class="content-layout-flex flex gap-6 items-start w-full">
      <div class="left-content-panel w-3/4 bg-white rounded-2xl shadow-md p-6">
        <div class="section-header-row flex justify-between items-center mb-6">
          <div class="title-block">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
            <p class="text-sm text-gray-500">Vista General y Listado ({{ total }} productos encontrados)</p>
          </div>

          <div class="filter-controls-left flex flex-wrap gap-3">
            <select v-model="filtroCategoria" class="px-4 py-2 border border-gray-300 rounded-xl text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-[#46674A]/20">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
            </select>

            <select v-model="filtroEstado" class="px-4 py-2 border border-gray-300 rounded-xl text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-[#46674A]/20">
              <option value="">Todos los estados</option>
              <option value="disponible">Disponible</option>
              <option value="bajo_stock">Bajo Stock</option>
            </select>

            <select v-model="filtroOrdenar" class="px-4 py-2 border border-gray-300 rounded-xl text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-[#46674A]/20">
              <option value="recientes">Recientes</option>
              <option value="nombre">Nombre</option>
              <option value="precio">Precio</option>
            </select>
          </div>
        </div>

        <div class="table-card-wrapper border border-gray-200 rounded-xl overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b border-gray-200">
              <tr class="text-gray-700 text-sm font-semibold">
                <th class="px-6 py-4" style="width: 14%;">SKU / Código</th>
                <th class="px-6 py-4" style="width: 10%;">Imagen</th>
                <th class="px-6 py-4" style="width: 20%;">Producto</th>
                <th class="px-6 py-4" style="width: 18%;">Categoría / Subcategoría</th>
                <th class="px-6 py-4" style="width: 12%;">Estado</th>
                <th class="px-6 py-4" style="width: 12%;">Stock / Unidad</th>
                <th class="px-6 py-4" style="width: 10%;">Precio Venta</th>
                <th class="px-6 py-4 text-right" style="width: 10%;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="producto in productosFiltrados" :key="producto.id" class="border-t border-gray-200 hover:bg-gray-50 text-sm">
                <td class="px-6 py-4 font-mono text-gray-600">{{ producto.codigo_barras || 'Sin SKU' }}</td>
                <td class="px-6 py-4">
                  <img
                    v-if="producto.imagenes && producto.imagenes.length"
                    :src="obtenerUrlImagen(producto.imagenes[0].ruta)"
                    alt="Producto"
                    class="w-12 h-12 object-cover rounded-xl border shadow-sm"
                  />
                  <div v-else class="w-12 h-12 border border-dashed rounded-xl flex items-center justify-center text-[10px] text-gray-400 bg-gray-50">
                    Sin foto
                  </div>
                </td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ producto.nombre }}</td>
                
                <td class="px-6 py-4 flex flex-col justify-center">
                  <span class="font-medium text-gray-800">{{ producto.subcategoria?.categoria?.nombre || 'General' }}</span>
                  <span class="text-xs text-gray-400 mt-0.5">{{ producto.subcategoria?.nombre || 'Sin subcategoría' }}</span>
                </td>

                <td class="px-6 py-4">
                  <span 
                    class="rounded-full px-2.5 py-1 text-xs font-semibold inline-block"
                    :class="Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'"
                  >
                    {{ Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'Bajo Stock' : 'Disponible' }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span :class="Number(producto.stock) <= (Number(producto.stock_minimo) || 5) ? 'text-amber-600 font-bold' : 'text-gray-700'">
                    {{ producto.stock }}
                  </span>
                  <span class="text-xs text-gray-400 ml-1">
                    ({{ producto.unidad_medida?.nombre || producto.unidad_medida_id || 'pza' }})
                  </span>
                </td>

                <td class="px-6 py-4 font-medium text-gray-900">${{ producto.precio_venta }}</td>
                
                <td class="px-6 py-4 text-right">
                  <div class="flex gap-2 justify-end">
                    <button class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" @click="editarProducto(producto)" title="Editar producto">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition" @click="eliminarProducto(producto.id)" title="Eliminar producto">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="productosFiltrados.length === 0">
                <td colspan="8" class="text-center py-8 text-gray-400 italic">No se encontraron productos con los filtros seleccionados.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="right-widgets-panel w-1/4 flex flex-col gap-4">
        <div class="inventory-card-widget bg-white rounded-2xl shadow-md p-4 border border-gray-100">
          <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Resumen de Inventario</h2>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-gray-50 rounded-xl mb-2">
            <div>
              <span class="block text-xs text-gray-500">Total ítems</span>
              <span class="text-xl font-bold text-gray-800">{{ resumen.total }}</span>
            </div>
          </div>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-green-50 rounded-xl mb-2">
            <div>
              <span class="block text-xs text-green-600">Disponibles</span>
              <span class="text-xl font-bold text-green-700">{{ resumen.disponibles }}</span>
            </div>
          </div>
          <div class="widget-metric-row flex justify-between items-center p-3 bg-amber-50 rounded-xl">
            <div>
              <span class="block text-xs text-amber-600">Bajo Stock</span>
              <span class="text-xl font-bold text-amber-700">{{ resumen.bajo_stock }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL REGISTRO / EDICIÓN -->
    <div v-if="mostrarModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-40 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-lg">
            {{ esEditando ? 'Modificar Producto Existente' : 'Agregar Nuevo Producto' }}
          </h3>
          <button class="text-gray-400 hover:text-gray-600" @click="cerrarModal"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="p-6 flex flex-col gap-4 max-h-[80vh] overflow-y-auto" enctype="multipart/form-data">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Imagen del Producto</label>
            <div class="flex items-center gap-4 mt-1">
              <div class="relative w-20 h-20 border rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center flex-shrink-0">
                <img v-if="imagenPreview" :src="imagenPreview" alt="Preview" class="w-full h-full object-cover" />
                <i v-else class="bi bi-image text-gray-300 text-2xl"></i>
                <button 
                  v-if="imagenPreview" 
                  type="button" 
                  @click="removerImagen" 
                  class="absolute top-0 right-0 bg-red-500 text-white rounded-bl p-1 leading-none shadow-md hover:bg-red-600 transition"
                  title="Quitar imagen"
                >
                  <i class="bi bi-x text-xs"></i>
                </button>
              </div>

              <div class="w-full">
                <label class="w-full flex flex-col items-center justify-center px-4 py-3 bg-white text-gray-500 rounded-xl border border-gray-300 border-dashed cursor-pointer hover:bg-gray-50 hover:text-gray-700 transition text-center">
                  <i class="bi bi-cloud-upload text-lg mb-0.5 text-[#46674A]"></i>
                  <span class="text-xs font-medium">Seleccionar imagen archivo</span>
                  <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="manejarCambioImagen" />
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre del Producto</label>
            <input type="text" v-model="nuevoProducto.nombre" required placeholder="Ej. MacBook Pro M3" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Código de Barras / SKU</label>
            <input type="text" v-model="nuevoProducto.codigo_barras" required placeholder="Ej. 7501055300075" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Subcategoría</label>
              <button type="button" @click="abrirBuscador('subcategoria')" class="w-full flex justify-between items-center px-4 py-2 border rounded-xl text-left text-sm bg-gray-50 hover:bg-gray-100 transition truncate">
                <span class="truncate">{{ nombreSubcategoriaSeleccionada || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1"></i>
              </button>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Proveedor</label>
              <button type="button" @click="abrirBuscador('proveedor')" class="w-full flex justify-between items-center px-4 py-2 border rounded-xl text-left text-sm bg-gray-50 hover:bg-gray-100 transition truncate">
                <span class="truncate">{{ nombreProveedorSeleccionado || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1"></i>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
              <button type="button" @click="abrirBuscador('unidad_medida')" class="w-full flex justify-between items-center px-4 py-2 border rounded-xl text-left text-sm bg-gray-50 hover:bg-gray-100 transition truncate">
                <span class="truncate">{{ nombreUnidadMedidaSeleccionada || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1"></i>
              </button>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Venta ($)</label>
              <input type="number" step="0.01" v-model="nuevoProducto.precio_venta" required placeholder="0.00" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">
                {{ esEditando ? 'Stock' : 'Stock Inicial' }}
              </label>
              <input type="number" v-model="nuevoProducto.stock" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Stock Mínimo</label>
              <input type="number" v-model="nuevoProducto.stock_minimo" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-4 border-t pt-4">
            <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200" :disabled="guardando" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-[#46674A] text-white rounded-xl hover:bg-[#3b5740] flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="guardando">
              <span v-if="guardando" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
              {{ guardando ? 'Procesando...' : (esEditando ? 'Actualizar Producto' : 'Guardar Producto') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL BUSCADOR SECUNDARIO (Subcategoría / Proveedor / Unidad de Medida) -->
    <div v-if="mostrarBuscador" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border">
        <div class="px-5 py-4 border-b flex justify-between items-center bg-gray-50">
          <h4 class="font-bold text-gray-800 text-base">
            Buscar {{ tipoBuscador === 'subcategoria' ? 'Subcategoría' : (tipoBuscador === 'proveedor' ? 'Proveedor' : 'Unidad de Medida') }}
          </h4>
          <button type="button" class="text-gray-400 hover:text-gray-600" @click="cerrarBuscador"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div class="p-4 flex flex-col gap-3">
          <div class="relative flex items-center">
            <i class="bi bi-search absolute left-3 text-gray-400 text-sm"></i>
            <input
              v-model="filtroBuscadorInterno"
              type="text"
              placeholder="Filtrar por nombre..."
              class="w-full pl-9 pr-4 py-2 border text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-[#46674A]"
            />
          </div>

          <div class="max-h-[250px] overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 flex flex-col divide-y">
            <button
              type="button"
              v-for="item in listaFiltradaBuscador"
              :key="item.id"
              @click="seleccionarItemBuscador(item)"
              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-white hover:text-[#46674A] font-medium transition flex justify-between items-center"
            >
              <span class="truncate">{{ tipoBuscador === 'proveedor' ? item.nombre_proveedor : item.nombre }}</span>
              <span class="text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded-md font-mono">ID: {{ item.id }}</span>
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
const proveedores = ref([]) 
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
const nombreSubcategoriaSeleccionada = ref('')
const nombreProveedorSeleccionado = ref('')
const nombreUnidadMedidaSeleccionada = ref('')

const buscar = ref('')
const filtroCategoria = ref('')
const filtroEstado = ref('')
const filtroOrdenar = ref('recientes')

const modeloProductoLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  sub_categoria_id: '',
  proveedor_id: '',
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
  if (tipoBuscador.value === 'subcategoria') {
    return subcategorias.value.filter(sc => (sc.nombre || '').toLowerCase().includes(query))
  } else if (tipoBuscador.value === 'proveedor') {
    return proveedores.value.filter(p => (p.nombre_proveedor || '').toLowerCase().includes(query))
  } else if (tipoBuscador.value === 'unidad_medida') {
    return unidadesMedida.value.filter(um => (um.nombre || '').toLowerCase().includes(query))
  }
  return []
})

const abrirBuscador = (tipo) => {
  tipoBuscador.value = tipo
  filtroBuscadorInterno.value = ''
  mostrarBuscador.value = true
}

const cerrarBuscador = () => {
  mostrarBuscador.value = false
  tipoBuscador.value = ''
}

const seleccionarItemBuscador = (item) => {
  if (tipoBuscador.value === 'subcategoria') {
    nuevoProducto.value.sub_categoria_id = item.id
    nombreSubcategoriaSeleccionada.value = item.nombre
  } else if (tipoBuscador.value === 'proveedor') {
    nuevoProducto.value.proveedor_id = item.id
    nombreProveedorSeleccionado.value = item.nombre_proveedor
  } else if (tipoBuscador.value === 'unidad_medida') {
    nuevoProducto.value.unidad_medida_id = item.id
    nombreUnidadMedidaSeleccionada.value = item.nombre
  }
  cerrarBuscador()
}

// --- CONEXIÓN AL SERVICIO ---

const cargarAuxiliaresFormulario = async () => {
  try {
    const [resCat, resSub, resProv, resUnidades] = await getAuxiliares()
    categorias.value = resCat.data.data || resCat.data
    subcategorias.value = resSub.data.data || resSub.data
    proveedores.value = resProv.data.data || resProv.data
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
  if (!nuevoProducto.value.sub_categoria_id || !nuevoProducto.value.proveedor_id || !nuevoProducto.value.unidad_medida_id) {
    alert('Por favor selecciona Subcategoría, Proveedor y Unidad de Medida válidos.')
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
  nombreProveedorSeleccionado.value = ''
  nombreUnidadMedidaSeleccionada.value = ''
  removerImagen()
  mostrarModal.value = true 
}

const cerrarModal = () => {
  mostrarModal.value = false
  esEditando.value = false
  productoIdSeleccionado.value = null
  nombreSubcategoriaSeleccionada.value = ''
  nombreProveedorSeleccionado.value = ''
  nombreUnidadMedidaSeleccionada.value = ''
  removerImagen()
  nuevoProducto.value = modeloProductoLimpio()
}

const editarProducto = (producto) => {
  esEditando.value = true
  productoIdSeleccionado.value = producto.id
  
  nombreSubcategoriaSeleccionada.value = producto.subcategoria?.nombre || (producto.sub_categoria_id ? 'ID: ' + producto.sub_categoria_id : '')
  nombreProveedorSeleccionado.value = producto.proveedor?.nombre_proveedor || (producto.proveedor_id ? 'ID: ' + producto.proveedor_id : '')
  nombreUnidadMedidaSeleccionada.value = producto.unidad_medida?.nombre || (producto.unidad_medida_id ? 'ID: ' + producto.unidad_medida_id : '')

  nuevoProducto.value = {
    codigo_barras: producto.codigo_barras || '',
    nombre: producto.nombre || '',
    sub_categoria_id: producto.sub_categoria_id || '',
    proveedor_id: producto.proveedor_id || '',
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