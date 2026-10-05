<template>
  <div v-if="totalPages > 1" class="flex items-center justify-end px-4 py-3 sm:px-6 border-t border-slate-100 bg-white">
    <div class="flex items-center gap-1.5">
      <button
        @click="$emit('update:currentPage', currentPage - 1)"
        :disabled="currentPage === 1"
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer"
      >
        <i class="bi bi-chevron-left text-xs"></i>
      </button>

      <button
        v-for="p in paginasVisibles"
        :key="p"
        @click="$emit('update:currentPage', p)"
        :class="[
          'w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition cursor-pointer',
          p === currentPage
            ? 'bg-[#2B3A4A] text-white'
            : 'border border-slate-200 text-slate-600 hover:bg-slate-100'
        ]"
      >
        {{ p }}
      </button>

      <button
        @click="$emit('update:currentPage', currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer"
      >
        <i class="bi bi-chevron-right text-xs"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalPages: { type: Number, required: true }
})

defineEmits(['update:currentPage'])

const paginasVisibles = computed(() => {
  const rango = 1
  const paginas = []
  for (let i = Math.max(1, props.currentPage - rango); i <= Math.min(props.totalPages, props.currentPage + rango); i++) {
    paginas.push(i)
  }
  return paginas
})
</script>