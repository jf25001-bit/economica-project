<template>
  <div class="p-6 max-w-[1600px] mx-auto min-h-screen flex flex-col gap-5">
    <!-- Header principal del POS -->
    <div class="flex items-center justify-between bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-slate-900 text-white rounded-xl shadow-sm">
          <i class="bi bi-calculator-fill text-2xl"></i>
        </div>
        <div>
          <h1 class="text-2xl font-black text-slate-800 tracking-tight">Punto de Venta (POS)</h1>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Escaneo rápido y procesamiento de órdenes</p>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <button 
          @click="resetearVenta" 
          class="px-4 py-2.5 text-xs text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition-all font-bold border border-rose-200 flex items-center gap-2 cursor-pointer"
        >
          <i class="bi bi-trash text-sm"></i> 
          <span>Limpiar Carrito</span>
        </button>
      </div>
    </div>

    <!-- Grid principal -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      
      <!-- Panel de Controles (5 cols) -->
      <div class="lg:col-span-5 flex flex-col min-w-0">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-5 w-full box-border">
          
          <!-- 1. Cliente -->
          <div class="w-full box-border">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Cliente
            </label>
            <input
              v-model="nombreCliente"
              type="text"
              placeholder="Consumidor Final"
              class="w-full max-w-full box-border px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-sm text-slate-800 font-medium transition-all"
            />
          </div>

          <!-- 2. Escáner de Código de Barras -->
          <div class="w-full box-border">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Escáner de Código de Barras
            </label>
            <div class="relative w-full box-border">
              <input
                ref="inputCodigoBarras"
                v-model="codigoInput"
                @keyup.enter="agregarPorCodigo"
                type="text"
                placeholder="Escanee el producto aquí..."
                class="w-full max-w-full box-border pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-base font-mono text-slate-800 transition-all"
              />
              <i class="bi bi-qr-code-scan absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-xl"></i>
            </div>
          </div>

          <!-- 3. Búsqueda Manual -->
          <div class="w-full box-border pt-3 border-t border-slate-100">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Búsqueda Manual
            </label>
            <select
              v-model="productoManualSeleccionado"
              @change="agregarPorSeleccionManual"
              class="w-full max-w-full box-border px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-sm text-slate-800 font-medium transition-all"
            >
              <option value="">-- Buscar por catálogo --</option>
              <option 
                v-for="prod in productosCatalogo" 
                :key="prod.id" 
                :value="prod.id"
                :disabled="prod.stock <= 0"
              >
                {{ prod.nombre }} (${{ Number(prod.precio_venta).toFixed(2) }}) - Stock: {{ prod.stock }}
              </option>
            </select>
          </div>

        </div>
      </div>

      <!-- Tabla del Carrito y Cierre de Venta (7 cols) -->
      <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden min-w-0">
        
        <!-- Listado con altura FIJA -->
        <div class="h-[340px] overflow-y-auto p-4 custom-scrollbar bg-white flex flex-col">
          <table class="w-full border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 text-[11px] font-black uppercase tracking-wider text-slate-500 z-10">
              <tr>
                <th class="p-3.5 text-left rounded-l-lg">Producto</th>
                <th class="p-3.5 text-left">Precio</th>
                <th class="p-3.5 w-28 text-center">Cant.</th>
                <th class="p-3.5 text-left">Subtotal</th>
                <th class="p-3.5 text-center rounded-r-lg"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
              <tr v-for="(item, index) in carrito" :key="item.producto_id" class="hover:bg-slate-50/80 transition-colors">
                <td class="p-3.5 font-bold text-slate-800 align-middle">
                  <div>{{ item.nombre }}</div>
                  <span class="text-xs text-slate-500 font-mono font-medium">{{ item.codigo_barras || 'Sin código' }}</span>
                </td>
                <td class="p-3.5 text-slate-600 font-medium align-middle">${{ Number(item.precio_venta).toFixed(2) }}</td>
                <td class="p-3.5 align-middle">
                  <input 
                    type="number"
                    v-model.number="item.cantidad"
                    min="1"
                    :max="item.stock_maximo"
                    @input="validarCantidadInput(index)"
                    class="w-full max-w-full box-border px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 font-bold text-slate-800"
                  />
                </td>
                <td class="p-3.5 font-black text-slate-900 align-middle">
                  ${{ (item.precio_venta * item.cantidad).toFixed(2) }}
                </td>
                <td class="p-3.5 text-center align-middle">
                  <button @click="eliminarDelCarrito(item.producto_id)" class="text-slate-400 hover:text-rose-600 transition p-1 cursor-pointer">
                    <i class="bi bi-trash text-base"></i>
                  </button>
                </td>
              </tr>

              <!-- Estado vacío centrado -->
              <tr v-if="carrito.length === 0" class="h-[250px]">
                <td colspan="5" class="text-center text-slate-400 align-middle">
                  <div class="flex flex-col items-center justify-center h-full">
                    <i class="bi bi-cart-x text-5xl mb-2 text-slate-300"></i>
                    <span class="font-semibold text-slate-400 text-sm">Aún no hay productos agregados</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Panel de Total y Botón para Abrir Modal -->
        <div class="p-4 bg-slate-50/80 border-t border-slate-200 flex flex-col gap-4">
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total a Cobrar</span>
            <span class="text-3xl font-black text-slate-900">
              ${{ totalCalculado.toFixed(2) }}
            </span>
          </div>

          <button
            @click="abrirModalCobro"
            :disabled="carrito.length === 0"
            class="bg-slate-900 hover:bg-slate-800 active:bg-slate-950 disabled:opacity-50 text-white w-full py-4 rounded-xl font-bold text-base shadow-sm flex items-center justify-center gap-3 transition-all cursor-pointer"
          >
            <i class="bi bi-cash-stack text-xl"></i>
            <span>Ir a Cobrar</span>
          </button>
        </div>

      </div>

    </div>

    <!-- MODAL DE COBRO CORREGIDO -->
    <div 
      v-if="mostrarModalCobro" 
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col transition-all">
        
        <!-- Modal Header -->
        <div class="p-5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
          <div class="flex items-center gap-3">
            <i class="bi bi-wallet2 text-xl text-slate-200"></i>
            <h3 class="font-bold text-lg text-white tracking-tight">Procesar Cobro</h3>
          </div>
          <button 
            @click="cerrarModalCobro" 
            class="p-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition cursor-pointer flex items-center justify-center"
          >
            <i class="bi bi-x-lg text-lg"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 flex flex-col gap-5">
          
          <!-- Resumen de Total -->
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center shadow-inner">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Monto Total</span>
            <span class="text-4xl font-black text-slate-900">${{ totalCalculado.toFixed(2) }}</span>
          </div>

          <!-- Input Efectivo Recibido -->
          <div class="flex flex-col gap-2">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Efectivo Recibido
            </label>
            <div class="relative w-full">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400 text-xl pointer-events-none">$</span>
              <input
                ref="inputEfectivoModal"
                v-model.number="efectivoRecibido"
                @keyup.enter="confirmarYRegistrarVenta"
                type="number"
                min="0"
                step="0.01"
                placeholder="0.00"
                class="w-full box-border pl-9 pr-4 py-3 font-black text-2xl text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 transition-all"
              />
            </div>
          </div>

          <!-- Desglose de Cambio -->
          <div 
            class="p-4 rounded-xl border flex items-center justify-between transition-colors" 
            :class="cambioCalculado >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'"
          >
            <span class="text-xs font-extrabold uppercase tracking-wider">Cambio a Entregar</span>
            <span class="text-2xl font-black">${{ cambioCalculado.toFixed(2) }}</span>
          </div>

        </div>

        <!-- Modal Footer -->
        <div class="p-5 bg-slate-50 border-t border-slate-200 flex items-center gap-3">
          <button
            @click="cerrarModalCobro"
            class="w-1/3 py-3 px-4 rounded-xl font-bold text-sm text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition cursor-pointer shadow-sm"
          >
            Cancelar
          </button>
          
          <button
            @click="confirmarYRegistrarVenta"
            :disabled="guardandoVenta || efectivoRecibido < totalCalculado"
            class="w-2/3 py-3 px-4 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 disabled:opacity-40 text-white rounded-xl font-bold text-sm shadow-sm flex items-center justify-center gap-2 transition-all cursor-pointer"
          >
            <span v-if="guardandoVenta" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
            <i v-else class="bi bi-check-circle-fill"></i>
            <span>{{ guardandoVenta ? 'Procesando...' : 'Confirmar Venta' }}</span>
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const productosCatalogo = ref([])
const guardandoVenta = ref(false)
const inputCodigoBarras = ref(null)
const inputEfectivoModal = ref(null)

const nombreCliente = ref('Consumidor Final')
const codigoInput = ref('')
const productoManualSeleccionado = ref('')
const efectivoRecibido = ref(0)
const carrito = ref([])
const mostrarModalCobro = ref(false)

const cargarCatalogo = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/productos')
    productosCatalogo.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Error cargando catálogo:', error)
  }
}

const enfocarEscaner = () => {
  nextTick(() => {
    if (inputCodigoBarras.value) inputCodigoBarras.value.focus()
  })
}

const totalCalculado = computed(() => {
  return carrito.value.reduce((suma, item) => suma + (item.precio_venta * item.cantidad), 0)
})

const cambioCalculado = computed(() => {
  return (Number(efectivoRecibido.value) || 0) - totalCalculado.value
})

const abrirModalCobro = () => {
  if (carrito.value.length === 0) return
  efectivoRecibido.value = null
  mostrarModalCobro.value = true
  
  nextTick(() => {
    if (inputEfectivoModal.value) {
      inputEfectivoModal.value.focus()
      inputEfectivoModal.value.select()
    }
  })
}

const cerrarModalCobro = () => {
  mostrarModalCobro.value = false
  enfocarEscaner()
}

const manejarTeclasGlobales = (e) => {
  if (e.key === '`') {
    e.preventDefault()
    if (!mostrarModalCobro.value) {
      abrirModalCobro()
    }
  }
}

const agregarPorCodigo = () => {
  const cod = codigoInput.value.trim()
  if (!cod) return

  const producto = productosCatalogo.value.find(p => String(p.codigo_barras) === cod)

  if (!producto) {
    Swal.fire({
      icon: 'warning',
      title: 'Producto no encontrado',
      text: `El código "${cod}" no existe en el sistema.`,
      confirmButtonColor: '#0f172a'
    })
    codigoInput.value = ''
    return
  }

  inyectarProducto(producto)
  codigoInput.value = ''
}

const agregarPorSeleccionManual = () => {
  if (!productoManualSeleccionado.value) return
  const producto = productosCatalogo.value.find(p => p.id === Number(productoManualSeleccionado.value))
  if (producto) inyectarProducto(producto)
  productoManualSeleccionado.value = ''
}

const inyectarProducto = (producto) => {
  const stockDisponible = Number(producto.stock)

  if (stockDisponible <= 0) {
    Swal.fire({
      icon: 'error',
      title: 'Sin stock',
      text: `El producto "${producto.nombre}" está agotado.`,
      confirmButtonColor: '#0f172a'
    })
    return
  }

  const yaExiste = carrito.value.find(item => item.producto_id === producto.id)

  if (yaExiste) {
    if (yaExiste.cantidad >= stockDisponible) {
      Swal.fire({
        icon: 'info',
        title: 'Límite alcanzado',
        text: `Solo quedan ${stockDisponible} unidades disponibles de "${producto.nombre}".`,
        confirmButtonColor: '#0f172a'
      })
      return
    }
    yaExiste.cantidad++
  } else {
    carrito.value.push({
      producto_id: producto.id,
      nombre: producto.nombre,
      codigo_barras: producto.codigo_barras,
      precio_venta: Number(producto.precio_venta),
      cantidad: 1,
      stock_maximo: stockDisponible 
    })
  }
}

const validarCantidadInput = (index) => {
  const item = carrito.value[index]
  if (item.cantidad < 1 || isNaN(item.cantidad)) {
    item.cantidad = 1
  } else if (item.cantidad > item.stock_maximo) {
    Swal.fire({
      icon: 'warning',
      title: 'Exceso de stock',
      text: `Solo hay ${item.stock_maximo} unidades disponibles.`,
      confirmButtonColor: '#0f172a'
    })
    item.cantidad = item.stock_maximo
  }
}

const eliminarDelCarrito = (idProducto) => {
  carrito.value = carrito.value.filter(item => item.producto_id !== idProducto)
  enfocarEscaner()
}

const resetearVenta = () => {
  nombreCliente.value = 'Consumidor Final'
  codigoInput.value = ''
  productoManualSeleccionado.value = ''
  efectivoRecibido.value = 0
  carrito.value = []
  mostrarModalCobro.value = false
  enfocarEscaner()
}

const confirmarYRegistrarVenta = async () => {
  if (carrito.value.length === 0) return

  if (efectivoRecibido.value < totalCalculado.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Efectivo insuficiente',
      text: `Faltan $${(totalCalculado.value - efectivoRecibido.value).toFixed(2)} para completar el pago.`,
      confirmButtonColor: '#0f172a'
    })
    return
  }

  if (guardandoVenta.value) return
  guardandoVenta.value = true

  const datosVenta = {
    cliente: nombreCliente.value.trim() || 'Consumidor Final',
    total: totalCalculado.value,
    productos: carrito.value.map(item => ({
      producto_id: item.producto_id,
      cantidad: item.cantidad,
      precio_venta: item.precio_venta
    }))
  }

  try {
    await axios.post('http://127.0.0.1:8000/api/ventas', datosVenta)
    
    mostrarModalCobro.value = false

    await Swal.fire({
      icon: 'success',
      title: '¡Venta realizada!',
      text: `Cambio a entregar: $${cambioCalculado.value.toFixed(2)}`,
      showConfirmButton: true,
      confirmButtonColor: '#0f172a'
    })

    resetearVenta()
    await cargarCatalogo() 
  } catch (error) {
    console.error(error)
    const mensajeError = error.response?.data?.error || error.response?.data?.message || 'Error al procesar'
    Swal.fire({
      icon: 'error',
      title: 'Error en la venta',
      text: mensajeError,
      confirmButtonColor: '#0f172a'
    })
  } finally {
    guardandoVenta.value = false
    enfocarEscaner()
  }
}

onMounted(() => {
  cargarCatalogo()
  enfocarEscaner()
  window.addEventListener('keydown', manejarTeclasGlobales)
})

onUnmounted(() => {
  window.removeEventListener('keydown', manejarTeclasGlobales)
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>