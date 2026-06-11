<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-950">Reportes</h1>
        <p class="text-sm text-gray-500">Generacion de PDF por rango de fecha</p>
      </div>
      <button @click="generarPdf" :disabled="cargando" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#9FCFCC] px-4 text-sm font-semibold text-gray-900 shadow-sm hover:bg-[#8bc0bd] disabled:opacity-50">
        <i class="bi bi-file-earmark-pdf"></i>
        Generar PDF
      </button>
    </div>

    <div class="mb-4 grid gap-3 rounded-lg border border-gray-200 bg-white p-3 md:grid-cols-[180px_180px_auto]">
      <input v-model="fechaInicio" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <input v-model="fechaFin" type="date" class="h-10 rounded-md border border-gray-300 px-3 text-sm outline-none focus:border-[#9FCFCC] focus:ring-2 focus:ring-[#9FCFCC]/40" />
      <button @click="cargarReporte" class="h-10 rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">Aplicar filtro</button>
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-4">
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Ventas</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">${{ formatoPrecio(totalVentas) }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Compras</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">${{ formatoPrecio(totalCompras) }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Productos</p>
        <p class="mt-1 text-2xl font-bold text-gray-950">{{ productos.length }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="text-xs font-semibold uppercase text-gray-500">Stock bajo</p>
        <p class="mt-1 text-2xl font-bold text-red-600">{{ stockBajo }}</p>
      </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
      <section class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-4 py-3 font-bold text-gray-900">Ventas filtradas</div>
        <table class="w-full">
          <tbody class="divide-y divide-gray-100 text-sm">
            <tr v-for="venta in ventas.slice(0, 8)" :key="venta.id">
              <td class="px-4 py-3">F{{ String(venta.id).padStart(4, '0') }}</td>
              <td class="px-4 py-3">{{ venta.cliente || 'Consumidor Final' }}</td>
              <td class="px-4 py-3 text-right font-semibold">${{ formatoPrecio(venta.total) }}</td>
            </tr>
            <tr v-if="ventas.length === 0"><td colspan="3" class="py-8 text-center text-gray-400">Sin ventas</td></tr>
          </tbody>
        </table>
      </section>

      <section class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-4 py-3 font-bold text-gray-900">Compras filtradas</div>
        <table class="w-full">
          <tbody class="divide-y divide-gray-100 text-sm">
            <tr v-for="compra in compras.slice(0, 8)" :key="compra.id">
              <td class="px-4 py-3">C{{ String(compra.id).padStart(4, '0') }}</td>
              <td class="px-4 py-3">{{ formatoFecha(compra.fecha_compra || compra.created_at) }}</td>
              <td class="px-4 py-3 text-right font-semibold">${{ formatoPrecio(compra.total) }}</td>
            </tr>
            <tr v-if="compras.length === 0"><td colspan="3" class="py-8 text-center text-gray-400">Sin compras</td></tr>
          </tbody>
        </table>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import { getCompras } from '../services/compraService'
import { getProductos } from '../services/productoService'
import { getProveedores } from '../services/proveedorService'
import { getUsuarios } from '../services/userService'
import { getVentas } from '../services/ventaService'

const hoy = new Date().toISOString().slice(0, 10)
const fechaInicio = ref(hoy)
const fechaFin = ref(hoy)
const ventas = ref([])
const compras = ref([])
const productos = ref([])
const proveedores = ref([])
const usuarios = ref([])
const cargando = ref(false)

const totalVentas = computed(() => ventas.value.reduce((suma, item) => suma + Number(item.total || 0), 0))
const totalCompras = computed(() => compras.value.reduce((suma, item) => suma + Number(item.total || 0), 0))
const stockBajo = computed(() => productos.value.filter(item => Number(item.stock) <= Number(item.stock_minimo)).length)

onMounted(cargarReporte)

async function cargarReporte() {
  if (!fechaInicio.value || !fechaFin.value) {
    Swal.fire('Fechas requeridas', 'Ingresa fecha inicial y final', 'warning')
    return
  }
  cargando.value = true
  try {
    const params = { fecha_inicio: fechaInicio.value, fecha_fin: fechaFin.value }
    const [ventasRes, comprasRes, productosRes, proveedoresRes, usuariosRes] = await Promise.all([
      getVentas(params),
      getCompras(params),
      getProductos({ ...params, per_page: 1000 }),
      getProveedores(params),
      getUsuarios()
    ])
    ventas.value = ventasRes || []
    compras.value = comprasRes || []
    productos.value = productosRes.data || productosRes || []
    proveedores.value = proveedoresRes || []
    usuarios.value = usuariosRes || []
  } catch (error) {
    Swal.fire('Error', error?.response?.data?.message || 'No se pudo cargar el reporte', 'error')
  } finally {
    cargando.value = false
  }
}

async function generarPdf() {
  await cargarReporte()
  if (cargando.value) return
  const ventana = window.open('', '_blank', 'width=1000,height=800')
  if (!ventana) {
    Swal.fire('Ventana bloqueada', 'Permite ventanas emergentes para generar el PDF', 'warning')
    return
  }

  ventana.document.write(`
    <html>
      <head>
        <title>Reporte ${fechaInicio.value} a ${fechaFin.value}</title>
        <style>
          body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
          h1 { margin: 0; font-size: 24px; }
          h2 { margin-top: 28px; font-size: 16px; }
          .meta { color: #6b7280; margin-top: 6px; }
          .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 24px 0; }
          .card { border: 1px solid #e5e7eb; border-left: 5px solid #9FCFCC; padding: 12px; border-radius: 8px; }
          .label { color: #6b7280; font-size: 12px; text-transform: uppercase; }
          .value { font-size: 22px; font-weight: 700; margin-top: 4px; }
          table { border-collapse: collapse; width: 100%; margin-top: 10px; }
          th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; font-size: 12px; text-align: left; }
          th { background: #f9fafb; text-transform: uppercase; color: #4b5563; }
          .right { text-align: right; }
        </style>
      </head>
      <body>
        <h1>Reporte La Economica</h1>
        <p class="meta">Filtro: ${fechaInicio.value} al ${fechaFin.value}</p>
        <div class="cards">
          <div class="card"><div class="label">Ventas</div><div class="value">$${formatoPrecio(totalVentas.value)}</div></div>
          <div class="card"><div class="label">Compras</div><div class="value">$${formatoPrecio(totalCompras.value)}</div></div>
          <div class="card"><div class="label">Productos</div><div class="value">${productos.value.length}</div></div>
          <div class="card"><div class="label">Stock bajo</div><div class="value">${stockBajo.value}</div></div>
        </div>
        ${tablaVentas()}
        ${tablaCompras()}
        ${tablaInventario()}
        <h2>Proveedores registrados en el rango</h2>
        <p>${proveedores.value.length} proveedores</p>
        <h2>Usuarios del sistema</h2>
        <p>${usuarios.value.length} usuarios</p>
      </body>
    </html>
  `)
  ventana.document.close()
  ventana.focus()
  setTimeout(() => ventana.print(), 250)
}

function tablaVentas() {
  return `<h2>Ventas</h2><table><thead><tr><th>Factura</th><th>Cliente</th><th>Fecha</th><th class="right">Total</th></tr></thead><tbody>${ventas.value.map(venta => `<tr><td>F${String(venta.id).padStart(4, '0')}</td><td>${venta.cliente || 'Consumidor Final'}</td><td>${formatoFecha(venta.created_at)}</td><td class="right">$${formatoPrecio(venta.total)}</td></tr>`).join('') || '<tr><td colspan="4">Sin ventas</td></tr>'}</tbody></table>`
}

function tablaCompras() {
  return `<h2>Compras</h2><table><thead><tr><th>Compra</th><th>Fecha</th><th>Estado</th><th class="right">Total</th></tr></thead><tbody>${compras.value.map(compra => `<tr><td>C${String(compra.id).padStart(4, '0')}</td><td>${formatoFecha(compra.fecha_compra || compra.created_at)}</td><td>${compra.estado || ''}</td><td class="right">$${formatoPrecio(compra.total)}</td></tr>`).join('') || '<tr><td colspan="4">Sin compras</td></tr>'}</tbody></table>`
}

function tablaInventario() {
  return `<h2>Inventario</h2><table><thead><tr><th>Producto</th><th>Categoria</th><th class="right">Stock</th><th class="right">Minimo</th></tr></thead><tbody>${productos.value.map(producto => `<tr><td>${producto.nombre}</td><td>${producto.categoria?.nombre || 'Sin categoria'}</td><td class="right">${producto.stock}</td><td class="right">${producto.stock_minimo}</td></tr>`).join('') || '<tr><td colspan="4">Sin productos</td></tr>'}</tbody></table>`
}

function formatoFecha(fecha) {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-SV')
}

function formatoPrecio(valor) {
  return Number(valor || 0).toFixed(2)
}
</script>
