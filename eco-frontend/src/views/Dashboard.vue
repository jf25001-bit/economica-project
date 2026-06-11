<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-5">
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-950">La Economica</h1>
          <p class="text-sm text-gray-500">Panel principal del sistema</p>
        </div>
        <button @click="cargarDatos" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">
          <i class="bi bi-arrow-repeat"></i>
          Actualizar
        </button>
      </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
      <router-link v-for="tarjeta in tarjetas" :key="tarjeta.titulo" :to="tarjeta.ruta" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase text-gray-500">{{ tarjeta.titulo }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-950">{{ tarjeta.valor }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ tarjeta.detalle }}</p>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-lg" :class="tarjeta.bg">
            <i :class="[tarjeta.icono, tarjeta.color, 'text-2xl']"></i>
          </div>
        </div>
      </router-link>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.2fr_.8fr]">
      <section class="rounded-lg border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
          <h2 class="font-bold text-gray-950">Actividad reciente</h2>
          <span class="text-xs text-gray-400">Ultimos movimientos</span>
        </div>
        <div class="divide-y divide-gray-100">
          <div v-for="movimiento in movimientos" :key="movimiento.key" class="flex items-center gap-3 px-4 py-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="movimiento.bg">
              <i :class="[movimiento.icono, movimiento.color]"></i>
            </div>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-semibold text-gray-800">{{ movimiento.titulo }}</p>
              <p class="text-xs text-gray-500">{{ movimiento.detalle }}</p>
            </div>
            <span class="text-xs text-gray-400">{{ movimiento.fecha }}</span>
          </div>
          <div v-if="movimientos.length === 0" class="py-10 text-center text-gray-400">Sin actividad registrada</div>
        </div>
      </section>

      <section class="rounded-lg border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-4 py-3">
          <h2 class="font-bold text-gray-950">Alertas de inventario</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <router-link v-for="producto in productosBajoStock" :key="producto.id" to="/inventario" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-red-50">
            <div class="min-w-0">
              <p class="truncate text-sm font-semibold text-gray-800">{{ producto.nombre }}</p>
              <p class="text-xs text-gray-500">Minimo {{ producto.stock_minimo }}</p>
            </div>
            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">{{ producto.stock }}</span>
          </router-link>
          <div v-if="productosBajoStock.length === 0" class="py-10 text-center text-gray-400">Inventario estable</div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getCategorias } from '../services/categoriaService'
import { getCompras } from '../services/compraService'
import { getProductos } from '../services/productoService'
import { getProveedores } from '../services/proveedorService'
import { getUsuarios } from '../services/userService'
import { getVentas } from '../services/ventaService'

const categorias = ref([])
const proveedores = ref([])
const productos = ref([])
const usuarios = ref([])
const ventas = ref([])
const compras = ref([])

const productosBajoStock = computed(() => productos.value.filter(producto => Number(producto.stock) <= Number(producto.stock_minimo)).slice(0, 6))
const totalVentas = computed(() => ventas.value.reduce((suma, venta) => suma + Number(venta.total || 0), 0))
const totalCompras = computed(() => compras.value.reduce((suma, compra) => suma + Number(compra.total || 0), 0))

const tarjetas = computed(() => [
  { titulo: 'Productos', valor: productos.value.length, detalle: `${productosBajoStock.value.length} en stock bajo`, ruta: '/productos', icono: 'bi bi-box-seam', bg: 'bg-[#9FCFCC]/30', color: 'text-gray-800' },
  { titulo: 'Ventas', valor: `$${formatoPrecio(totalVentas.value)}`, detalle: `${ventas.value.length} operaciones`, ruta: '/ventas', icono: 'bi bi-cash-coin', bg: 'bg-blue-100', color: 'text-blue-700' },
  { titulo: 'Compras', valor: `$${formatoPrecio(totalCompras.value)}`, detalle: `${compras.value.length} entradas`, ruta: '/compras', icono: 'bi bi-basket2', bg: 'bg-amber-100', color: 'text-amber-700' },
  { titulo: 'Usuarios', valor: usuarios.value.length, detalle: `${proveedores.value.length} proveedores`, ruta: '/usuarios', icono: 'bi bi-people', bg: 'bg-slate-100', color: 'text-slate-700' }
])

const movimientos = computed(() => {
  const ventasMov = ventas.value.slice(0, 3).map(venta => ({
    key: `v-${venta.id}`,
    titulo: `Venta F${String(venta.id).padStart(4, '0')}`,
    detalle: `${venta.cliente || 'Consumidor Final'} - $${formatoPrecio(venta.total)}`,
    fecha: formatoFecha(venta.created_at),
    icono: 'bi bi-receipt',
    bg: 'bg-blue-50',
    color: 'text-blue-600'
  }))

  const comprasMov = compras.value.slice(0, 3).map(compra => ({
    key: `c-${compra.id}`,
    titulo: `Compra C${String(compra.id).padStart(4, '0')}`,
    detalle: `${compra.estado || 'pendiente'} - $${formatoPrecio(compra.total)}`,
    fecha: formatoFecha(compra.fecha_compra || compra.created_at),
    icono: 'bi bi-bag-check',
    bg: 'bg-[#9FCFCC]/25',
    color: 'text-gray-700'
  }))

  return [...ventasMov, ...comprasMov].slice(0, 6)
})

onMounted(cargarDatos)

async function cargarDatos() {
  const [categoriasRes, proveedoresRes, productosRes, usuariosRes, ventasRes, comprasRes] = await Promise.all([
    getCategorias(),
    getProveedores(),
    getProductos({ per_page: 1000 }),
    getUsuarios(),
    getVentas(),
    getCompras()
  ])

  categorias.value = categoriasRes || []
  proveedores.value = proveedoresRes || []
  productos.value = productosRes.data || productosRes || []
  usuarios.value = usuariosRes || []
  ventas.value = ventasRes || []
  compras.value = comprasRes || []
}

function formatoFecha(fecha) {
  if (!fecha) return 'Sin fecha'
  return new Date(fecha).toLocaleDateString('es-SV')
}

function formatoPrecio(valor) {
  return Number(valor || 0).toFixed(2)
}
</script>
