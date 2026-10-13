<template>
  <div class="flex flex-col md:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-gray-100">
    <div class="flex flex-wrap items-center justify-center gap-3 text-xs text-gray-500">
      <span>
        Mostrando
        <b class="text-gray-700">{{ desde }}</b>–<b class="text-gray-700">{{ hasta }}</b>
        de
        <b class="text-gray-700">{{ total }}</b>
      </span>

      <select
        :value="porPagina"
        class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 text-gray-600 outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer"
        @change="emit('update:porPagina', Number($event.target.value))"
      >
        <option v-for="n in opciones" :key="n" :value="n">{{ n }} por página</option>
      </select>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-1">
      <button
        type="button"
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
        :disabled="paginaActual === 1"
        @click="ir(paginaActual - 1)"
      >
        <i class="bi bi-chevron-left text-xs"></i>
      </button>

      <template v-for="(p, i) in paginasVisibles" :key="i">
        <span v-if="p === '...'" class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">…</span>
        <button
          v-else
          type="button"
          class="w-8 h-8 flex items-center justify-center rounded-lg border text-xs font-medium transition cursor-pointer"
          :class="p === paginaActual ? 'bg-[#1a233a] text-white border-[#1a233a]' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
          @click="ir(p)"
        >
          {{ p }}
        </button>
      </template>

      <button
        type="button"
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
        :disabled="paginaActual === totalPaginas"
        @click="ir(paginaActual + 1)"
      >
        <i class="bi bi-chevron-right text-xs"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { OPCIONES_POR_PAGINA } from '@/utils/usePaginacion'

const props = defineProps({
  paginaActual: { type: Number, required: true },
  porPagina: { type: Number, required: true },
  total: { type: Number, required: true },
  opciones: { type: Array, default: () => OPCIONES_POR_PAGINA }
})

const emit = defineEmits(['update:paginaActual', 'update:porPagina'])

const totalPaginas = computed(() => Math.max(1, Math.ceil(props.total / props.porPagina)))

const desde = computed(() => (props.total === 0 ? 0 : (props.paginaActual - 1) * props.porPagina + 1))
const hasta = computed(() => Math.min(props.paginaActual * props.porPagina, props.total))

const paginasVisibles = computed(() => {
  const totalP = totalPaginas.value
  const actual = props.paginaActual

  if (totalP <= 7) return Array.from({ length: totalP }, (_, i) => i + 1)

  const paginas = [1]
  const inicio = Math.max(2, actual - 1)
  const fin = Math.min(totalP - 1, actual + 1)

  if (inicio > 2) paginas.push('...')
  for (let i = inicio; i <= fin; i++) paginas.push(i)
  if (fin < totalP - 1) paginas.push('...')

  paginas.push(totalP)
  return paginas
})

const ir = (n) => {
  if (n >= 1 && n <= totalPaginas.value) emit('update:paginaActual', n)
}
</script>