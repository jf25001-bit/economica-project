<template>
  <div class="p-6 max-w-[1600px] mx-auto min-h-screen flex flex-col gap-5">
    <!-- Header principal -->
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

    <!-- Alerta de caja cerrada -->
    <div
      v-if="!cajaAbierta"
      class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl"
    >
      <i class="bi bi-exclamation-triangle-fill text-xl"></i>
      <div>
        <p class="font-bold text-sm">La caja está cerrada</p>
        <p class="text-xs">No se pueden realizar ventas hasta que el administrador abra la caja.</p>
      </div>
    </div>

    <!-- Contenido POS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- Panel de Controles -->
      <div class="lg:col-span-5 flex flex-col min-w-0">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-5 w-full box-border">

          <!-- Cliente -->
          <div class="w-full box-border">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Cliente
            </label>
            <input
              v-model="nombreCliente"
              :disabled="!cajaAbierta"
              type="text"
              placeholder="Consumidor Final"
              class="w-full max-w-full box-border px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-sm text-slate-800 font-medium transition-all disabled:opacity-50"
            />
          </div>

          <!-- Escáner -->
          <div class="w-full box-border">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Escáner de Código de Barras
            </label>
            <div class="relative w-full box-border">
              <input
                ref="inputCodigoBarras"
                v-model="codigoInput"
                :disabled="!cajaAbierta"
                @keyup.enter="agregarPorCodigo"
                type="text"
                inputmode="numeric"
                placeholder="Escanee el producto aquí..."
                class="w-full max-w-full box-border pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-base font-mono text-slate-800 transition-all disabled:opacity-50"
              />
              <i class="bi bi-qr-code-scan absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 text-xl"></i>
            </div>
          </div>

          <!-- Búsqueda manual -->
          <div class="w-full box-border pt-3 border-t border-slate-100">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2">
              Búsqueda Manual
            </label>
            <select
              v-model="productoManualSeleccionado"
              :disabled="!cajaAbierta"
              @change="agregarPorSeleccionManual"
              class="w-full max-w-full box-border px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 text-sm text-slate-800 font-medium transition-all disabled:opacity-50"
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

      <!-- Carrito de compras -->
      <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden min-w-0">

        <!-- Tabla -->
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
                    type="text"
                    inputmode="numeric"
                    :value="item.cantidad"
                    @input="validarCantidadInput(index, $event)"
                    @blur="restaurarCantidad(index, $event)"
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

        <!-- Total y Acción -->
        <div class="p-4 bg-slate-50/80 border-t border-slate-200 flex flex-col gap-4">
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total a Cobrar</span>
            <span class="text-3xl font-black text-slate-900">
              ${{ totalCalculado.toFixed(2) }}
            </span>
          </div>

          <button
            @click="abrirModalCobro"
            :disabled="carrito.length === 0 || !cajaAbierta"
            class="bg-slate-900 hover:bg-slate-800 active:bg-slate-950 disabled:opacity-50 text-white w-full py-4 rounded-xl font-bold text-base shadow-sm flex items-center justify-center gap-3 transition-all cursor-pointer"
          >
            <i class="bi bi-cash-stack text-xl"></i>
            <span>Ir a Cobrar</span>
          </button>
        </div>

      </div>

    </div>

    <!-- Modal Cobro -->
    <div
      v-if="mostrarModalCobro"
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col transition-all">

        <!-- Header Modal -->
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

        <!-- Body Modal -->
        <div class="p-6 flex flex-col gap-5">

          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-center shadow-inner">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Monto Total</span>
            <span class="text-4xl font-black text-slate-900">${{ totalCalculado.toFixed(2) }}</span>
          </div>

          <div class="flex flex-col gap-2">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Efectivo Recibido
            </label>
            <div class="relative w-full">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400 text-xl pointer-events-none">$</span>
              <input
                ref="inputEfectivoModal"
                :value="efectivoRecibido"
                @input="validarEfectivoInput"
                @keyup.enter="confirmarYRegistrarVenta"
                type="text"
                inputmode="decimal"
                placeholder="0.00"
                class="w-full box-border pl-9 pr-4 py-3 font-black text-2xl text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:bg-white focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 transition-all"
              />
            </div>
          </div>

          <div
            class="p-4 rounded-xl border flex items-center justify-between transition-colors"
            :class="cambioCalculado >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'"
          >
            <span class="text-xs font-extrabold uppercase tracking-wider">Cambio a Entregar</span>
            <span class="text-2xl font-black">${{ cambioCalculado.toFixed(2) }}</span>
          </div>

        </div>

        <!-- Footer Modal -->
        <div class="p-5 bg-slate-50 border-t border-slate-200 flex items-center gap-3">
          <button
            @click="cerrarModalCobro"
            class="w-1/3 py-3 px-4 rounded-xl font-bold text-sm text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition cursor-pointer shadow-sm"
          >
            Cancelar
          </button>

          <button
            @click="confirmarYRegistrarVenta"
            :disabled="guardandoVenta || (Number(efectivoRecibido) || 0) < totalCalculado"
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
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import Swal from 'sweetalert2'
import { getProductos, createVenta } from '@/services/ventaService'
import { cajaService } from '@/services/cajaService'
import { nombreCompleto } from '@/utils/ticketVenta'

const INTERVALO_REFRESCO = 30000

const productosCatalogo = ref([])
const guardandoVenta = ref(false)
const inputCodigoBarras = ref(null)
const inputEfectivoModal = ref(null)

const nombreCliente = ref('Consumidor Final')
const codigoInput = ref('')
const productoManualSeleccionado = ref('')
const efectivoRecibido = ref('')
const carrito = ref([])
const mostrarModalCobro = ref(false)
const cajaAbierta = ref(false)

let temporizador = null

const nombreNegocio = 'La Económica'

watch(codigoInput, (valor) => {
  const limpio = String(valor ?? '').replace(/\D/g, '')
  if (limpio !== valor) codigoInput.value = limpio
})

const sincronizarCarrito = () => {
  const ajustes = []

  carrito.value = carrito.value.filter(item => {
    const prod = productosCatalogo.value.find(p => p.id === item.producto_id)
    const stock = prod ? Number(prod.stock) : 0

    if (stock <= 0) {
      ajustes.push(`${item.nombre}: sin stock disponible, se quitó del carrito`)
      return false
    }

    item.stock_maximo = stock

    if (item.cantidad > stock) {
      ajustes.push(`${item.nombre}: cantidad ajustada a${stock}`)
      item.cantidad = stock
    }

    return true
  })

  return ajustes
}

const cargarCatalogo = async () => {
  try {
    const res = await getProductos()
    productosCatalogo.value = res.data || res || []
    return sincronizarCarrito()
  } catch (error) {
    console.error('Error cargando catálogo:', error)
    return []
  }
}

const avisarAjustes = (ajustes) => {
  Swal.fire({
    icon: 'info',
    title: 'Se actualizó el stock',
    html: `<div style="font-size:13px;text-align:left">${ajustes.map(limpiarTexto).join('<br>')}</div>`,
    confirmButtonColor: '#0f172a'
  }).then(() => enfocarEscaner())
}

const cargarEstadoCaja = async () => {
  try {
    const res = await cajaService.obtenerEstado()
    cajaAbierta.value = !!(res && res.caja)
  } catch (error) {
    cajaAbierta.value = false
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

const validarEfectivoInput = (event) => {
  let val = event.target.value

  val = val.replace(/,/g, '.')
  val = val.replace(/[^0-9.]/g, '')

  const partes = val.split('.')
  if (partes.length > 2) {
    val = partes[0] + '.' + partes.slice(1).join('')
  }

  if (partes.length >= 2) {
    val = partes[0] + '.' + partes[1].slice(0, 2)
  }

  efectivoRecibido.value = val
  event.target.value = val
}

const avisarCajaCerrada = () => {
  Swal.fire({
    icon: 'warning',
    title: 'Caja cerrada',
    text: 'No se pueden realizar ventas porque la caja está cerrada.',
    confirmButtonColor: '#0f172a'
  })
}

const abrirModalCobro = async () => {
  if (carrito.value.length === 0) return

  await cargarEstadoCaja()
  if (!cajaAbierta.value) {
    avisarCajaCerrada()
    return
  }

  const ajustes = await cargarCatalogo()
  if (ajustes.length) {
    avisarAjustes(ajustes)
    return
  }
  if (carrito.value.length === 0) return

  efectivoRecibido.value = ''
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

const refrescarSiVisible = async () => {
  if (document.visibilityState !== 'visible') return
  if (mostrarModalCobro.value || guardandoVenta.value) return

  const ajustes = await cargarCatalogo()
  if (ajustes.length) avisarAjustes(ajustes)
}

const agregarPorCodigo = () => {
  if (!cajaAbierta.value) return

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
  if (!cajaAbierta.value) return
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

const validarCantidadInput = (index, event) => {
  const item = carrito.value[index]
  const limpio = event.target.value.replace(/\D/g, '')
  const cantidad = parseInt(limpio, 10)

  if (!cantidad) {
    event.target.value = limpio
    return
  }

  if (cantidad > item.stock_maximo) {
    Swal.fire({
      icon: 'warning',
      title: 'Exceso de stock',
      text: `Solo hay ${item.stock_maximo} unidades disponibles.`,
      confirmButtonColor: '#0f172a'
    })
    item.cantidad = item.stock_maximo
  } else {
    item.cantidad = cantidad
  }

  event.target.value = item.cantidad
}

const restaurarCantidad = (index, event) => {
  event.target.value = carrito.value[index].cantidad
}

const eliminarDelCarrito = (idProducto) => {
  carrito.value = carrito.value.filter(item => item.producto_id !== idProducto)
  enfocarEscaner()
}

const resetearVenta = () => {
  nombreCliente.value = 'Consumidor Final'
  codigoInput.value = ''
  productoManualSeleccionado.value = ''
  efectivoRecibido.value = ''
  carrito.value = []
  mostrarModalCobro.value = false
  enfocarEscaner()
}

const limpiarTexto = (texto) => {
  return String(texto ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
}

const imprimirTicket = (datos) => {
  const fecha = new Date(datos.fecha)
  const fechaTexto = isNaN(fecha.getTime()) ? '' : fecha.toLocaleDateString()
  const horaTexto = isNaN(fecha.getTime())
    ? ''
    : fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true })

  let filas = ''
  datos.items.forEach((item) => {
    filas += `
      <div class="producto">${limpiarTexto(item.nombre)}</div>
      <div class="linea">
        <span>${item.cantidad} x $${Number(item.precio_venta).toFixed(2)}</span>
        <span>$${(item.precio_venta * item.cantidad).toFixed(2)}</span>
      </div>
    `
  })

  const lineaFactura = datos.factura ? `<div>Factura: ${limpiarTexto(datos.factura)}</div>` : ''
  const lineaVendedor = datos.vendedor ? `<div>Atendió: ${limpiarTexto(datos.vendedor)}</div>` : ''

  const html = `
    <html>
      <head>
        <title>Ticket</title>
        <style>
          @page { size: 80mm auto; margin: 0; }
          body {
            width: 72mm;
            margin: 0 auto;
            padding: 4mm 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
          }
          .centro { text-align: center; }
          .negocio { font-size: 16px; font-weight: bold; }
          .separador { border-top: 1px dashed #000; margin: 6px 0; }
          .linea { display: flex; justify-content: space-between; }
          .producto { font-weight: bold; margin-top: 4px; }
          .total { font-size: 15px; font-weight: bold; }
        </style>
      </head>
      <body>
        <div class="centro negocio">${limpiarTexto(nombreNegocio)}</div>
        <div class="centro">Ticket de venta</div>
        <div class="separador"></div>

        ${lineaFactura}
        <div>Fecha: ${fechaTexto}</div>
        <div>Hora: ${horaTexto}</div>
        <div>Cliente: ${limpiarTexto(datos.cliente)}</div>
        ${lineaVendedor}
        <div class="separador"></div>

        ${filas}

        <div class="separador"></div>
        <div class="linea total"><span>TOTAL</span><span>$${Number(datos.total).toFixed(2)}</span></div>
        <div class="linea"><span>Recibido</span><span>$${Number(datos.recibido).toFixed(2)}</span></div>
        <div class="linea"><span>Cambio</span><span>$${Number(datos.vuelto).toFixed(2)}</span></div>
        <div class="separador"></div>

        <div class="centro">¡Gracias por su compra!</div>
      </body>
    </html>
  `

  const ventana = window.open('', '_blank', 'width=400,height=600')
  if (!ventana) {
    Swal.fire({
      icon: 'warning',
      title: 'Ventana bloqueada',
      text: 'El navegador bloqueó la ventana del ticket. Permite las ventanas emergentes e intenta de nuevo.',
      confirmButtonColor: '#0f172a'
    })
    return
  }
  ventana.document.write(html)
  ventana.document.close()
  ventana.focus()
  setTimeout(() => {
    ventana.print()
    ventana.close()
  }, 300)
}

const confirmarYRegistrarVenta = async () => {
  if (carrito.value.length === 0) return

  if (!cajaAbierta.value) {
    avisarCajaCerrada()
    return
  }

  const montoRecibidoNum = Number(efectivoRecibido.value) || 0

  if (montoRecibidoNum < totalCalculado.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Efectivo insuficiente',
      text: `Faltan $${(totalCalculado.value - montoRecibidoNum).toFixed(2)} para completar el pago.`,
      confirmButtonColor: '#0f172a'
    })
    return
  }

  if (guardandoVenta.value) return
  guardandoVenta.value = true

  const datosVenta = {
    cliente: nombreCliente.value.trim() || 'Consumidor Final',
    total: totalCalculado.value,
    dinero_recibido: montoRecibidoNum,
    productos: carrito.value.map(item => ({
      producto_id: item.producto_id,
      cantidad: item.cantidad,
      precio_venta: item.precio_venta
    }))
  }

  const copiaItems = carrito.value.map(item => ({
    nombre: item.nombre,
    cantidad: item.cantidad,
    precio_venta: item.precio_venta
  }))
  const cambioDeEstaVenta = cambioCalculado.value

  try {
    const res = await createVenta(datosVenta)
    const ventaGuardada = res?.data?.venta || res?.data || res || {}

    mostrarModalCobro.value = false

    const datosTicket = {
      factura: ventaGuardada.factura || (ventaGuardada.id ? `#${ventaGuardada.id}` : ''),
      fecha: ventaGuardada.fecha_venta || ventaGuardada.created_at || new Date(),
      cliente: datosVenta.cliente,
      vendedor: ventaGuardada.vendedor_nombre || nombreCompleto(ventaGuardada.usuario),
      items: copiaItems,
      total: datosVenta.total,
      recibido: datosVenta.dinero_recibido,
      vuelto: ventaGuardada.vuelto ?? cambioDeEstaVenta
    }

    const respuesta = await Swal.fire({
      icon: 'success',
      title: '¡Venta realizada!',
      text: `Cambio a entregar: $${cambioDeEstaVenta.toFixed(2)}`,
      showConfirmButton: true,
      confirmButtonText: 'Listo',
      confirmButtonColor: '#0f172a',
      showDenyButton: true,
      denyButtonText: 'Imprimir ticket',
      denyButtonColor: '#475569'
    })

    if (respuesta.isDenied) {
      imprimirTicket(datosTicket)
    }

    resetearVenta()
    await cargarCatalogo()
  } catch (error) {
    console.error(error)

    if (error.response?.status === 403) {
      cajaAbierta.value = false
      mostrarModalCobro.value = false
    }

    if (error.response?.status === 400) {
      mostrarModalCobro.value = false
      cargarCatalogo()
    }

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
  cargarEstadoCaja()
  enfocarEscaner()
  window.addEventListener('keydown', manejarTeclasGlobales)
  temporizador = setInterval(refrescarSiVisible, INTERVALO_REFRESCO)
  document.addEventListener('visibilitychange', refrescarSiVisible)
})

onUnmounted(() => {
  window.removeEventListener('keydown', manejarTeclasGlobales)
  clearInterval(temporizador)
  document.removeEventListener('visibilitychange', refrescarSiVisible)
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