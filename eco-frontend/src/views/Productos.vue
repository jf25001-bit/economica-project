<template>
  <div class="main-interface-container">
    
    <div class="top-strict-navbar">
      <div class="search-wrapper">
        <i class="bi bi-search search-icon"></i>
        <input
          v-model="buscar"
          @keyup="cargarProductos"
          type="text"
          placeholder="Buscar producto..."
        class="search-input-field"
      />
      </div>
      
      <div class="top-right-actions">
        <button class="action-icon-btn">
          <i class="bi bi-chat-left-text"></i>
        </button>
        <button class="action-icon-btn position-relative">
          <i class="bi bi-bell"></i>
          <span class="notification-dot-badge"></span>
        </button>
      </div>
    </div>

    <div class="content-layout-flex">
      
      <div class="left-content-panel">
        
        <div class="section-header-row">
          <div class="title-block">
            <h1 class="main-title-text">Gestión de Productos</h1>
            <p class="subtitle-text">
  Vista General y Listado ({{ total }} productos)
</p>
          </div>
          <div class="header-buttons-group">
           <button class="btn-action-primary">
  <i class="bi bi-plus-lg"></i> Nuevo Producto
</button>
          </div>
        </div>

        <div class="filter-strip-container">
          <div class="filter-controls-left">
            <div class="filter-select-group">
              <label class="filter-label">Categoría</label>
              <select
  v-model="categoria"
  @change="cargarProductos"
  class="filter-dropdown"
>
  <option value="">Todas</option>
  <option
    v-for="cat in categorias"
    :key="cat.id"
    :value="cat.id"
  >
    {{ cat.nombre }}
  </option>
</select>
            </div>
            <div class="filter-select-group">
              <label class="filter-label">Estado</label>
              <select
  v-model="estado"
  @change="cargarProductos"
  class="filter-dropdown"
>
  <option value="">Todos</option>
  <option value="disponible">Disponible</option>
  <option value="bajo_stock">Bajo Stock</option>
</select>
            </div>
            <div class="filter-select-group">
              <label class="filter-label">Fecha</label>
              <select class="filter-dropdown"><option>All</option></select>
            </div>
            <div class="filter-select-group">
              <label class="filter-label">Ordenar</label>
              <select
  v-model="ordenar"
  @change="cargarProductos"
  class="filter-dropdown-sort"
>
  <option value="recientes">Recientes</option>
  <option value="nombre">Nombre</option>
  <option value="precio">Precio</option>
</select>
            </div>
          </div>
        </div>

        <div class="table-card-wrapper">
          <table class="strict-data-table">
            <thead>
              <tr>
                <th style="width: 5%; text-align: center;"><input type="checkbox" class="strict-checkbox-input" /></th>
                <th style="width: 12%;">SKU</th>
                <th style="width: 35%;">Nombre del Producto</th>
                <th style="width: 13%;">Categoría</th>
                <th style="width: 12%;">Estado</th>
                <th style="width: 8%;">Stock</th>
                <th style="width: 15%;">Precio Base</th>
                <th style="width: 10%; text-align: right;">Acciones</th>
              </tr>
            </thead>
            <tbody>
  <tr
    v-for="producto in productos"
    :key="producto.id"
  >
    <td style="text-align:center">
      <input
        type="checkbox"
        class="strict-checkbox-input"
      />
    </td>

    <td class="sku-cell-text">
      {{ producto.codigo_barras }}
    </td>

    <td>
      <div class="product-info-cell">

        <img
          src="https://via.placeholder.com/40"
          class="product-thumb-img"
          alt=""
        />

        <div class="product-meta-text">

          <span class="product-title-link">
            {{ producto.nombre }}
          </span>

          <span class="product-desc-truncate">
            Código: {{ producto.codigo_barras }}
          </span>

        </div>

      </div>
    </td>

    <td>
      {{ producto.sub_categoria_id }}
    </td>

    <td>
      <span
        class="status-badge"
        :class="
          producto.stock <= producto.stock_minimo
            ? 'status-low-stock'
            : 'status-available'
        "
      >
        {{
          producto.stock <= producto.stock_minimo
            ? 'Bajo Stock'
            : 'Disponible'
        }}
      </span>
    </td>

    <td>
      {{ producto.stock }}
    </td>

    <td>
      ${{ producto.precio_venta }}
    </td>

    <td class="actions-cell-right">

      <button class="row-action-btn">
        <i class="bi bi-eye"></i>
      </button>

      <button class="row-action-btn">
        <i class="bi bi-pencil"></i>
      </button>

      <button class="row-action-btn">
        <i class="bi bi-trash"></i>
      </button>
    </td>

  </tr>
</tbody>
          </table>
        </div>
      </div>

      <div class="right-widgets-panel">
        <div class="inventory-card-widget">
          <h2 class="widget-title-text">Resumen de Inventario</h2>
          
          <div class="widget-metric-row">
            <div class="metric-info">
              <span class="metric-label">Total Productos</span>
              <span class="metric-number">
                  {{ resumen.total }}
              </span>
            </div>
            <div class="metric-sparkline">
              <svg viewBox="0 0 50 20" class="sparkline-svg"><path d="M0,15 Q10,5 20,12 T40,4 L50,8" fill="none" stroke="#2563eb" stroke-width="2"/></svg>
            </div>
          </div>

          <div class="widget-metric-row">
            <div class="metric-info">
              <span class="metric-label">Disponible</span>
              <span class="metric-number">
  {{ resumen.disponibles }}
</span>
            </div>
            <div class="metric-sparkline">
              <svg viewBox="0 0 50 20" class="sparkline-svg"><path d="M0,10 Q12,14 25,6 T50,4" fill="none" stroke="#10b981" stroke-width="2"/></svg>
            </div>
          </div>

          <div class="widget-metric-row">
            <div class="metric-info">
              <span class="metric-label">Bajo Stock</span>
              <span class="metric-number">
  {{ resumen.bajo_stock }}
</span>
            </div>
            <div class="metric-sparkline">
              <svg viewBox="0 0 50 20" class="sparkline-svg"><path d="M0,5 Q15,15 30,5 T50,15" fill="none" stroke="#f59e0b" stroke-width="2"/></svg>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div v-if="mostrarModal" class="modal-overlay-backdrop">
      <div class="modal-surface-card">
        <div class="modal-header-container">
          <h3 class="modal-title">Agregar Nuevo Producto</h3>
          <button class="modal-close-x" @click="cerrarModal"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="modal-form-body">
          <div class="form-row-grid">
            <div class="form-field-group">
              <label>Nombre del Producto</label>
              <input type="text" v-model="nuevoProducto.nombre" required placeholder="Ej. iPhone 15 Pro" />
            </div>
            <div class="form-field-group">
              <label>SKU Único</label>
              <input type="text" v-model="nuevoProducto.sku" required placeholder="Ej. APP-IPH15" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Descripción Corta</label>
            <input type="text" v-model="nuevoProducto.descripcion" placeholder="Breve detalle..." />
          </div>

          <div class="form-row-grid rows-three">
            <div class="form-field-group">
              <label>Categoría</label>
              <input type="text" v-model="nuevoProducto.categoria" required placeholder="Ej. Tecnología" />
            </div>
            <div class="form-field-group">
              <label>Precio Base ($)</label>
              <input type="number" step="0.01" v-model="nuevoProducto.precio" required placeholder="0.00" />
            </div>
            <div class="form-field-group">
              <label>Stock Inicial</label>
              <input type="number" v-model="nuevoProducto.stock" required placeholder="0" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Estado Inicial</label>
            <select v-model="nuevoProducto.estado">
              <option value="Disponible">Disponible</option>
              <option value="Bajo Stock">Bajo Stock</option>
              <option value="Agotado">Agotado</option>
            </select>
          </div>

          <div class="modal-actions-footer">
            <button type="button" class="btn-secondary" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="btn-primary-submit">Guardar Producto</button>
          </div>
        </form>
      </div>
    </div>
    <div v-if="mostrarModal" class="modal-overlay-backdrop">
      <div class="modal-surface-card">
        <div class="modal-header-container">
          <h3 class="modal-title">Agregar Nuevo Producto</h3>
          <button class="modal-close-x" @click="cerrarModal"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="modal-form-body">
          <div class="form-row-grid">
            <div class="form-field-group">
              <label>Nombre del Producto</label>
              <input type="text" v-model="nuevoProducto.nombre" required placeholder="Ej. Coca Cola 600ml" />
            </div>
            <div class="form-field-group">
              <label>SKU (Código de Barras)</label>
              <input type="text" v-model="nuevoProducto.codigo_barras" required placeholder="Ej. 7501055300075" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Descripción Corta</label>
            <input type="text" v-model="nuevoProducto.descripcion" placeholder="Breve detalle..." />
          </div>

          <div class="form-row-grid rows-three">
            <div class="form-field-group">
              <label>Categoría (ID)</label>
              <input type="text" v-model="nuevoProducto.sub_categoria_id" required placeholder="Ej. 1" />
            </div>
            <div class="form-field-group">
              <label>Precio Venta ($)</label>
              <input type="number" step="0.01" v-model="nuevoProducto.precio_venta" required placeholder="0.00" />
            </div>
            <div class="form-field-group">
              <label>Stock Inicial</label>
              <input type="number" v-model="nuevoProducto.stock" required placeholder="0" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Stock Mínimo (Alerta)</label>
            <input type="number" v-model="nuevoProducto.stock_minimo" required placeholder="Ej. 10" />
          </div>

          <div class="modal-actions-footer">
            <button type="button" class="btn-secondary" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="btn-primary-submit">Guardar Producto</button>
          </div>
        </form>
      </div>
    </div>
    <div v-if="mostrarModal" class="modal-overlay-backdrop">
      <div class="modal-surface-card">
        <div class="modal-header-container">
          <h3 class="modal-title">Agregar Nuevo Producto</h3>
          <button type="button" class="modal-close-x" @click="cerrarModal">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <form @submit.prevent="guardarProducto" class="modal-form-body">
          <div class="form-row-grid">
            <div class="form-field-group">
              <label>Nombre del Producto</label>
              <input type="text" v-model="nuevoProducto.nombre" required placeholder="Ej. MacBook Pro" />
            </div>
            <div class="form-field-group">
              <label>SKU (Código de Barras)</label>
              <input type="text" v-model="nuevoProducto.codigo_barras" required placeholder="Ej. M3 M3" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Descripción Corta</label>
            <input type="text" v-model="nuevoProducto.descripcion" placeholder="Detalles..." />
          </div>

          <div class="form-row-grid rows-three">
            <div class="form-field-group">
              <label>Categoría (ID)</label>
              <input type="text" v-model="nuevoProducto.sub_categoria_id" required placeholder="Ej. 1" />
            </div>
            <div class="form-field-group">
              <label>Precio Venta ($)</label>
              <input type="number" step="0.01" v-model="nuevoProducto.precio_venta" required placeholder="0.00" />
            </div>
            <div class="form-field-group">
              <label>Stock Inicial</label>
              <input type="number" v-model="nuevoProducto.stock" required placeholder="0" />
            </div>
          </div>

          <div class="form-field-group">
            <label>Stock Mínimo</label>
            <input type="number" v-model="nuevoProducto.stock_minimo" required placeholder="10" />
          </div>

          <div class="modal-actions-footer">
            <button type="button" class="btn-secondary" @click="cerrarModal">Cancelar</button>
            <button type="submit" class="btn-primary-submit">Guardar Producto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// --- ESTADOS REACTIVOS DEL BACKEND ---
const productos = ref([])
const resumen = ref({ total: 0, disponibles: 0, bajo_stock: 0, agotado: 0 })

// --- ESTADOS REACTIVOS DE LOS FILTROS (Faltaban y causaban errores) ---
const buscar = ref('')
const categoria = ref('')
const estado = ref('')
const ordenar = ref('recientes')
const total = ref(0)
const categorias = ref([]) // Para el v-for de categorías en tu HTML

// --- CONTROL DE INVENTARIO (BACKEND) ---
const cargarProductos = async () => {
    try {
        const response = await axios.get(
            'http://localhost:8000/api/productos',
            {
                params: {
                    search: buscar.value
                }
            }
        )
        productos.value = response.data.data
        total.value = productos.value.length
    } catch (error) {
        console.error(error)
    }
}

const cargarResumen = async () => {
    try {
        const response = await axios.get(
            'http://localhost:8000/api/productos/resumen'
        )
        resumen.value = response.data
    } catch (error) {
        console.error(error)
    }
}

const eliminarProducto = async (id) => {
    if (!confirm('¿Eliminar producto?')) return
    try {
        await axios.delete(
            `http://localhost:8000/api/productos/${id}`
        )
        cargarProductos()
        cargarResumen()
    } catch (error) {
        console.error(error)
    }
}

onMounted(() => {
    cargarProductos()
    cargarResumen()
})

// --- LÓGICA DEL MODAL (CORREGIDA SIN IMPORTS DUPLICADOS) ---
const mostrarModal = ref(false)

const modeloProductoLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  descripcion: '',
  sub_categoria_id: '',
  stock: 0,
  stock_minimo: 10,
  precio_venta: 0
})

const nuevoProducto = ref(modeloProductoLimpio())

const cerrarModal = () => {
  mostrarModal.value = false
  nuevoProducto.value = modeloProductoLimpio()
}

// Guarda temporalmente en la tabla y actualiza contadores
const guardarProducto = () => {
  productos.value.unshift({
    id: Date.now(),
    ...nuevoProducto.value
  })
  
  if (resumen.value) {
    resumen.value.total = productos.value.length
    resumen.value.disponibles = productos.value.filter(p => p.stock > p.stock_minimo).length
    resumen.value.bajo_stock = productos.value.filter(p => p.stock <= p.stock_minimo).length
  }
  total.value = productos.value.length

  cerrarModal()
}
</script>
<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

/* CONTENEDOR ELÁSTICO SIN ANCHOS ESTÁTICOS */
.main-interface-container {
  box-sizing: border-box !important;
  font-family: 'Inter', sans-serif !important;
  background-color: #f3f4f6 !important;
  width: 100% !important; 
  min-height: 100% !important;
  padding: 16px !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 16px !important;
}

/* NAVBAR FLUIDO */
.top-strict-navbar {
  box-sizing: border-box !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 8px !important;
  padding: 0 16px !important;
  height: 50px !important;
  width: 100% !important;
}

.search-wrapper {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  flex: 1 !important;
  max-width: 400px !important;
}

.search-icon {
  position: absolute !important;
  left: 12px !important;
  color: #9ca3af !important;
}

.search-input-field {
  width: 100% !important;
  height: 34px !important;
  background-color: #f3f4f6 !important;
  border: none !important;
  border-radius: 6px !important;
  padding-left: 36px !important;
  font-size: 0.85rem !important;
  outline: none !important;
}

.top-right-actions {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

.action-icon-btn {
  background: none !important;
  border: none !important;
  color: #4b5563 !important;
  font-size: 1.1rem !important;
  cursor: pointer !important;
}

.notification-dot-badge {
  position: absolute !important;
  top: 2px !important;
  right: 2px !important;
  width: 6px !important;
  height: 6px !important;
  background-color: #ef4444 !important;
  border-radius: 50% !important;
}

/* DISTRIBUCIÓN EN FILA FLEXIBLE */
.content-layout-flex {
  display: flex !important;
  gap: 16px !important;
  width: 100% !important;
  align-items: start !important;
}

.left-content-panel {
  flex: 0 0 78% !important; /* Asegura un control total del espacio izquierdo */
  width: 78% !important;
}

.right-widgets-panel {
  flex: 0 0 22% !important; /* Rellena perfectamente el costado derecho */
  width: 22% !important;
}

/* FILTROS Y ENCABEZADO */
.section-header-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 12px !important;
  width: 100% !important;
}

.main-title-text {
  font-size: 1.25rem !important;
  font-weight: 700 !important;
  margin: 0 !important;
}

.subtitle-text {
  font-size: 0.8rem !important;
  color: #6b7280 !important;
  margin: 0 !important;
}

.header-buttons-group {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

.btn-action-primary {
  background-color: #2563eb !important;
  color: #ffffff !important;
  border: none !important;
  padding: 0 12px !important;
  height: 34px !important;
  font-size: 0.82rem !important;
  border-radius: 6px !important;
  cursor: pointer !important;
}

.time-display-widget {
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  font-size: 0.8rem !important;
  padding: 0 10px !important;
  height: 34px !important;
  border-radius: 6px !important;
  display: inline-flex !important;
  align-items: center !important;
}

.filter-strip-container {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 8px !important;
  padding: 0 12px !important;
  height: 48px !important;
  margin-bottom: 12px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.filter-controls-left {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
}

.filter-select-group {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
}

.filter-label {
  font-size: 0.8rem !important;
  color: #4b5563 !important;
}

.filter-dropdown, .filter-dropdown-sort {
  background-color: #f9fafb !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 6px !important;
  height: 28px !important;
  font-size: 0.8rem !important;
}

/* PAGINADOR */
.pagination-right-block {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

.pagination-counter-text {
  font-size: 0.8rem !important;
  color: #6b7280 !important;
}

.pagination-box-controls {
  display: flex !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 6px !important;
  height: 28px !important;
}

.pag-arrow-btn, .pag-number-active, .pag-number-inactive {
  width: 28px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 0.8rem !important;
  background: none !important;
  border: none !important;
}

.pag-number-active {
  background-color: #eff6ff !important;
  color: #2563eb !important;
  font-weight: 700 !important;
}

/* TABLA DINÁMICA PERFECTA */
.table-card-wrapper {
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 8px !important;
  width: 100% !important;
  overflow: hidden !important;
}

.strict-data-table {
  width: 100% !important;
  border-collapse: collapse !important;
  table-layout: fixed !important; /* Reparte los % de las columnas de forma matemática */
}

.strict-data-table thead th {
  background-color: #f9fafb !important;
  padding: 10px 12px !important;
  font-size: 0.78rem !important;
  font-weight: 600;
  color: #4b5563 !important;
  border-bottom: 1px solid #e5e7eb !important;
}

.strict-data-table tbody td {
  padding: 10px 12px !important;
  font-size: 0.82rem !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

.strict-checkbox-input {
  width: 14px !important;
  height: 14px !important;
  cursor: pointer !important;
}

.product-info-cell {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

.product-thumb-img {
  width: 32px !important;
  height: 32px !important;
  border-radius: 4px !important;
  object-fit: cover !important;
}

.product-meta-text {
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
}

.product-title-link {
  font-weight: 600 !important;
  color: #2563eb !important;
}

.product-desc-truncate {
  font-size: 0.72rem !important;
  color: #6b7280 !important;
}

.custom-pill-tag, .status-badge {
  padding: 2px 8px !important;
  border-radius: 9999px !important;
  font-size: 0.75rem !important;
}

.status-available { background-color: #dcfce7 !important; color: #15803d !important; }
.status-low-stock { background-color: #fef3c7 !important; color: #d97706 !important; }

.actions-cell-right { text-align: right !important; }
.row-action-btn { background: none !important; border: none !important; color: #6b7280 !important; cursor: pointer !important; font-size: 0.9rem !important; margin-left: 4px !important;}

/* WIDGETS RESUMEN DERECHO */
.inventory-card-widget {
  background-color: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 8px !important;
  padding: 12px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.widget-title-text {
  font-size: 0.82rem !important;
  font-weight: 700 !important;
  margin: 0 0 12px 0 !important;
}

.widget-metric-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  border: 1px solid #f3f4f6 !important;
  border-radius: 6px !important;
  padding: 8px 10px !important;
  margin-bottom: 8px !important;
}

.metric-label { font-size: 0.7rem !important; color: #6b7280 !important; }
.metric-number { font-size: 1.1rem !important; font-weight: 700 !important; }
.metric-sparkline { width: 40px !important; height: 18px !important; }
.sparkline-svg { width: 100% !important; height: 100% !important; }
</style>