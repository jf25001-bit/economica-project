<template>
  <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
      <div class="px-5 py-3.5 border-b border-slate-200 flex justify-between items-center bg-slate-900 text-white">
        <h3 class="font-bold text-xs uppercase tracking-wider m-0">Catálogo de Productos</h3>
        <button type="button" class="text-slate-300 hover:text-white cursor-pointer bg-transparent border-0" @click="$emit('cerrar')">
          <i class="bi bi-x-lg text-xs"></i>
        </button>
      </div>
      <div class="p-4 bg-white">
        <div class="relative mb-3">
          <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input
            v-model="busqueda"
            class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 outline-none focus:border-slate-900 box-border"
            placeholder="Buscar por nombre..."
          />
        </div>
        <div class="max-h-[260px] overflow-y-auto space-y-1.5 pr-1">
          <div
            v-for="p in productosFiltrados"
            :key="p.id"
            class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-slate-900 hover:bg-slate-100 transition flex justify-between items-center"
            @click="$emit('seleccionar', p)"
          >
            <div>
              <p class="font-bold text-slate-900 text-xs m-0">{{ p.nombre }}</p>
              <p class="text-[10px] text-slate-500 m-0">Unidad: {{ p.unidad_medida?.nombre || 'pieza' }}</p>
            </div>
            <span class="text-[10px] bg-white text-slate-700 font-semibold px-2 py-0.5 rounded-md border border-slate-200">
              Stock: {{ p.stock ?? 0 }}
            </span>
          </div>
          <p v-if="productosFiltrados.length === 0" class="text-center py-6 text-xs text-slate-400 italic">
            No hay productos que coincidan.
          </p>
        </div>
        <button
          type="button"
          class="mt-3 w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white text-[11px] font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition cursor-pointer"
          @click="$emit('crear-producto')"
        >
          <i class="bi bi-box-seam"></i>
          <span>¿No encuentras el producto? Agregar producto</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  productos: { type: Array, default: () => [] }
})

defineEmits(['cerrar', 'seleccionar', 'crear-producto'])

const busqueda = ref('')

const productosFiltrados = computed(() => {
  const query = busqueda.value.toLowerCase().trim()
  return props.productos
    .filter(p => !p.anulado)
    .filter(p => (p.nombre || '').toLowerCase().includes(query))
})
</script>