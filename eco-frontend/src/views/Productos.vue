<template>
  <div class="main-interface-container p-6">
    <div class="top-strict-navbar flex items-center justify-between mb-6 bg-white rounded-2xl shadow-md p-4">
      <div class="search-wrapper relative flex items-center max-w-md w-full">
        <i class="bi bi-search search-icon absolute left-4 text-gray-400"></i>
        <input
          v-model="buscar"
          @input="cargarProductos"
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
            <p class="text-sm text-gray-500">Vista General y Listado ({{ total }} productos)</p>
          </div>

          <div class="filter-controls-left flex gap-4">
            <select v-model="filtroCategoria" @change="cargarProductos" class="px-4 py-2 border border-gray-300 rounded-xl text-sm">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
            </select>

            <select v-model="filtroEstado" @change="cargarProductos" class="px-4 py-2 border border-gray-300 rounded-xl text-sm">
              <option value="">Todos los estados</option>
              <option value="disponible">Disponible</option>
              <option value="bajo_stock">Bajo Stock</option>
            </select>
          </div>
        </div>

        <div class="table-card-wrapper border border-gray-200 rounded-xl overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b border-gray-200">
              <tr class="text-gray-700 text-sm font-semibold">
                <th class="px-6 py-4" style="width: 15%;">SKU / Código</th>
                <th class="px-6 py-4" style="width: 15%;">Imagen</th>
                <th class="px-6 py-4" style="width: 30%;">Producto</th>
                <th class="px-6 py-4" style="width: 15%;">Categoría</th>
                <th class="px-6 py-4" style="width: 10%;">Stock</th>
                <th class="px-6 py-4" style="width: 15%;">Precio Venta</th>
                <th class="px-6 py-4 text-right" style="width: 10%;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="producto in productos" :key="producto.id" class="border-t border-gray-200 hover:bg-gray-50 text-sm">
                <td class="px-6 py-4 font-mono text-gray-600">
                  {{ producto.codigo_barras || 'Sin SKU' }}
                </td>

                <td class="px-6 py-4">
                  <img
                    v-if="producto.imagenes && producto.imagenes.length"
                    :src="`http://127.0.0.1:8000/storage/${producto.imagenes[0].ruta}`"
                    alt="Producto"
                    class="w-12 h-12 object-cover rounded-xl border shadow-sm"
                  />
                  <div v-else class="w-12 h-12 border border-dashed rounded-xl flex items-center justify-center text-[10px] text-gray-400 bg-gray-50">
                    Sin foto
                  </div>
                </td>

                <td class="px-6 py-4 font-medium text-gray-800">
                  {{ producto.nombre }}
                </td>

                <td class="px-6 py-4 text-gray-600">
                  {{ producto.subcategoria?.categoria?.nombre || 'General' }}
                </td>

                <td class="px-6 py-4">
                  <span :class="producto.stock <= (producto.stock_minimo || 5) ? 'text-red-600 font-bold' : 'text-gray-700'">
                    {{ producto.stock }}
                  </span>
                </td>

                <td class="px-6 py-4 font-medium text-gray-900">
                  ${{ producto.precio_venta }}
                </td>

                <td class="px-6 py-4 text-right">
                  <div class="flex gap-2 justify-end">
                    <button class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" @click="editarProducto(producto)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition" @click="eliminarProducto(producto.id)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="productos.length === 0">
                <td colspan="7" class="text-center py-8 text-gray-400 italic">No se encontraron productos en el sistema.</td>
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

    <div v-if="mostrarModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-lg">
            {{ esEditando ? 'Modificar Producto Existente' : 'Agregar Nuevo Producto' }}
          </h3>
          <button class="text-gray-400 hover:text-gray-600" @click="cerrarModal"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="p-6 flex flex-col gap-4 max-h-[80vh] overflow-y-auto">
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
              <label class="block text-xs font-semibold text-gray-600 mb-1">Subcategoría (ID)</label>
              <input type="number" v-model="nuevoProducto.sub_categoria_id" required placeholder="Ej. 1" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Proveedor (ID)</label>
              <input type="number" v-model="nuevoProducto.proveedor_id" required placeholder="Ej. 1" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Compra ($)</label>
              <input type="number" step="0.01" v-model="nuevoProducto.precio_compra" required placeholder="0.00" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-[#46674A] outline-none" />
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
            <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-[#46674A] text-white rounded-xl hover:bg-[#3b5740]">
              {{ esEditando ? 'Actualizar Producto' : 'Guardar Producto' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// --- ESTADOS REACTIVOS ---
const productos = ref([])
const categorias = ref([])
const total = ref(0)
const mostrarModal = ref(false)
const esEditando = ref(false) // Define si el modal guarda nuevo o edita existente
const productoIdSeleccionado = ref(null) // Rastrea qué ID de producto estamos editando

const resumen = ref({
  total: 0,
  disponibles: 0,
  bajo_stock: 0
})

const buscar = ref('')
const filtroCategoria = ref('')
const filtroEstado = ref('')

const modeloProductoLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  sub_categoria_id: '',
  proveedor_id: '',
  stock: 0,
  stock_minimo: 5,
  precio_compra: 0,
  precio_venta: 0
})

const nuevoProducto = ref(modeloProductoLimpio())

// --- FUNCIONES API ---
const cargarProductos = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos', {
      params: {
        search: buscar.value,
        categoria: filtroCategoria.value,
        estado: filtroEstado.value
      }
    })
    productos.value = response.data.data || response.data
    total.value = productos.value.length
    calcularResumenLocal()
  } catch (error) {
    console.error('Error cargando productos:', error)
  }
}

const guardarProducto = async () => {
  try {
    if (esEditando.value) {
      // Petición PUT para actualizar los cambios
      await axios.put(`http://127.0.0.1:8000/api/productos/${productoIdSeleccionado.value}`, nuevoProducto.value)
    } else {
      // Petición POST para guardar un producto nuevo
      await axios.post('http://127.0.0.1:8000/api/productos', nuevoProducto.value)
    }
    cerrarModal()
    cargarProductos()
  } catch (error) {
    console.error('Error al procesar producto:', error)
    if (error.response && error.response.status === 422) {
      alert('Error de validación: Asegúrate de que los campos sean únicos y que los IDs de Proveedor o Subcategoría existan en el sistema.');
      console.log('Detalles de validación del backend:', error.response.data.errors);
    }
  }
}

const eliminarProducto = async (id) => {
  if (!confirm('¿Estás completamente seguro de eliminar este producto?')) return
  try {
    await axios.delete(`http://127.0.0.1:8000/api/productos/${id}`)
    cargarProductos()
  } catch (error) {
    console.error('Error al eliminar:', error)
  }
}

const calcularResumenLocal = () => {
  resumen.value.total = productos.value.length
  resumen.value.disponibles = productos.value.filter(p => p.stock > (p.stock_minimo || 5)).length
  resumen.value.bajo_stock = productos.value.filter(p => p.stock <= (p.stock_minimo || 5)).length
}

// --- ACCIONES MODAL ---
const abrirModalForm = () => { 
  esEditando.value = false
  mostrarModal.value = true 
}

const cerrarModal = () => {
  mostrarModal.value = false
  esEditando.value = false
  productoIdSeleccionado.value = null
  nuevoProducto.value = modeloProductoLimpio()
}

// NUEVO: Método para rellenar los datos preexistentes en el modal completo
const editarProducto = (producto) => {
  esEditando.value = true
  productoIdSeleccionado.value = producto.id
  
  // Clonamos el objeto para evitar modificar la tabla directamente antes de guardar
  nuevoProducto.value = {
    codigo_barras: producto.codigo_barras || '',
    nombre: producto.nombre || '',
    sub_categoria_id: producto.sub_categoria_id || '',
    proveedor_id: producto.proveedor_id || '',
    stock: producto.stock || 0,
    stock_minimo: producto.stock_minimo || 5,
    precio_compra: producto.precio_compra || 0,
    precio_venta: producto.precio_venta || 0
  }
  
  mostrarModal.value = true
}

onMounted(() => {
  cargarProductos()
})
</script>

<style scoped>
.main-interface-container {
  font-family: 'Inter', sans-serif;
  width: 100%;
}
</style>