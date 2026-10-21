<template>
  <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden">
      <div class="px-6 py-4 flex justify-between items-center bg-slate-900 text-white shrink-0">
        <div class="flex items-center gap-3 min-w-0">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 border border-slate-700 shrink-0">
            <i class="bi bi-receipt text-lg"></i>
          </div>
          <div class="min-w-0">
            <h3 class="font-bold text-base m-0">Compra #{{ compra.id }}</h3>
            <p class="text-xs text-slate-300 m-0 mt-0.5 flex items-center gap-1.5">
              <i class="bi bi-calendar3"></i>
              {{ fechaCompra }}
            </p>
          </div>
        </div>
        <button
          type="button"
          class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition cursor-pointer shrink-0"
          @click="$emit('cerrar')"
        >
          <i class="bi bi-x-lg text-xs"></i>
        </button>
      </div>

      <div class="p-4 sm:p-5 overflow-y-auto flex-1 bg-slate-50/50 space-y-4">
        <div class="grid grid-cols-3 gap-3">
          <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-sm">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Productos</span>
            <span class="block text-lg sm:text-2xl font-black text-slate-900 mt-0.5">{{ detalles.length }}</span>
          </div>
          <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-sm">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Unidades</span>
            <span class="block text-lg sm:text-2xl font-black text-slate-900 mt-0.5">{{ totalUnidades }}</span>
          </div>
          <div class="bg-slate-900 rounded-xl p-3 sm:p-4 shadow-sm">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</span>
            <span class="block text-lg sm:text-2xl font-black text-white mt-0.5">${{ Number(compra.total ?? 0).toFixed(2) }}</span>
          </div>
        </div>

        <ul v-if="detalles.length" class="space-y-3 list-none p-0 m-0">
          <li
            v-for="(det, idx) in detalles"
            :key="det.id || idx"
            class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden"
          >
            <div class="p-4 flex items-start justify-between gap-3">
              <div class="flex items-start gap-3 min-w-0">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 shrink-0">
                  <i class="bi bi-box-seam"></i>
                </div>
                <div class="min-w-0">
                  <p class="font-bold text-slate-900 text-sm m-0 break-words">{{ nombreProducto(det) }}</p>
                  <p class="text-xs text-slate-500 m-0 mt-0.5 flex items-center gap-1.5 break-words">
                    <i class="bi bi-truck shrink-0"></i>
                    {{ nombreProveedor(det) }}
                  </p>
                </div>
              </div>
              <div class="text-right shrink-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Subtotal</span>
                <span class="block text-base font-black text-slate-900">${{ subtotal(det).toFixed(2) }}</span>
              </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-slate-100 border-y border-slate-100">
              <div v-for="stat in estadisticas(det)" :key="stat.label" class="bg-white px-4 py-2.5 min-w-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ stat.label }}</span>
                <span class="block text-sm font-bold text-slate-800">{{ stat.valor }}</span>
              </div>
            </div>

            <div class="px-4 py-3 bg-slate-50/60 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
              <div class="min-w-0">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Lote</span>
                <span class="block font-mono text-xs font-bold text-slate-800 break-all">
                  {{ loteDe(det)?.codigo_lote || '—' }}
                </span>
              </div>

              <div v-if="loteDe(det)">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Disponible</span>
                <span class="block text-xs font-bold text-slate-800">
                  {{ Number(loteDe(det).cantidad_actual) }}
                  <span class="font-medium text-slate-400">de {{ Number(loteDe(det).cantidad_inicial) }}</span>
                </span>
              </div>

              <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Expiración</span>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-xs font-bold text-slate-800">
                    {{ loteDe(det)?.fecha_expiracion ? formatearFecha(loteDe(det).fecha_expiracion) : 'Sin vencimiento' }}
                  </span>
                  <span
                    v-if="estadoExpiracion(det)"
                    :class="estadoExpiracion(det).clase"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold border"
                  >
                    <i :class="['bi', estadoExpiracion(det).icon]"></i>
                    {{ estadoExpiracion(det).texto }}
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <p v-else class="text-center py-10 text-sm text-slate-400 italic">Esta compra no tiene detalles.</p>
      </div>

      <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex items-center justify-end shrink-0">
        <button
          type="button"
          class="px-5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 transition cursor-pointer"
          @click="$emit('cerrar')"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ESTADOS_LOTE, clasificarLote, formatearFecha, textoEstadoLote } from '@/utils/lotes'

const props = defineProps({
  compra: { type: Object, required: true },
  productos: { type: Array, default: () => [] }
})

defineEmits(['cerrar'])

const detalles = computed(() => props.compra.detalles || [])

const fechaCompra = computed(() => {
  return props.compra.fecha_compra ? formatearFecha(props.compra.fecha_compra) : '—'
})

const unidadesDetalle = (det) => (Number(det.cantidad) || 0) * (Number(det.unidades_por_paquete) || 1)

const totalUnidades = computed(() => detalles.value.reduce((acc, det) => acc + unidadesDetalle(det), 0))

const subtotal = (det) => (Number(det.cantidad) || 0) * (Number(det.precio_compra) || 0)

const nombreProducto = (det) => {
  return det.producto_nombre
    || det.producto?.nombre
    || props.productos.find(p => String(p.id) === String(det.producto_id))?.nombre
    || '—'
}

const nombreProveedor = (det) => {
  return det.proveedor_nombre || det.proveedor?.nombre_proveedor || det.proveedor?.nombre || '—'
}

const loteDe = (det) => (det.lotes && det.lotes.length > 0 ? det.lotes[0] : null)

const estadisticas = (det) => [
  { label: 'Paquetes', valor: Number(det.cantidad) || 0 },
  { label: 'Unid. x paquete', valor: Number(det.unidades_por_paquete) || 1 },
  { label: 'Total unidades', valor: unidadesDetalle(det) },
  { label: 'Precio paquete', valor: `$${(Number(det.precio_compra) || 0).toFixed(2)}` }
]

const estadoExpiracion = (det) => {
  const lote = loteDe(det)
  if (!lote?.fecha_expiracion) return null

  const { dias, estadoVenc } = clasificarLote(lote.fecha_expiracion, lote.cantidad_actual ?? 1)
  const estado = ESTADOS_LOTE[estadoVenc]

  if (estado) {
    return {
      clase: estado.clase,
      icon: estado.icon,
      texto: textoEstadoLote({ estadoVenc, diasRestantes: dias })
    }
  }

  return {
    clase: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    icon: 'bi-check-circle',
    texto: 'Vigente'
  }
}
</script>