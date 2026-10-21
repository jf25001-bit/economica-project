<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    @mousedown="registrarInicio"
    @click="cerrarSiFondo"
  >
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-5 sm:p-6 box-border">
      <template v-if="paso === 'form'">
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="min-w-0">
            <h3 class="text-base font-bold text-slate-800 m-0">Disminuir stock del lote</h3>
            <p class="text-xs text-slate-400 m-0 mt-0.5 break-all">
              {{ lote.codigo_lote || 'Sin lote' }} · #{{ lote.id }}
            </p>
          </div>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-600 bg-transparent border-0 cursor-pointer text-lg leading-none shrink-0"
            @click="cerrar"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Cantidad actual</span>
            <span class="block text-lg font-black text-slate-900">{{ lote.cantidad_actual }}</span>
          </div>
          <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Quedaría</span>
            <span class="block text-lg font-black text-slate-900">{{ restante }}</span>
          </div>
        </div>

        <label class="block text-xs font-bold text-slate-600 mb-1.5">Cantidad a disminuir</label>
        <input
          ref="inputCantidad"
          v-model="cantidadInput"
          type="text"
          inputmode="numeric"
          pattern="[0-9]*"
          autocomplete="off"
          placeholder="Ej: 5"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#5B80B0] box-border"
          @keydown="bloquearTecla"
          @input="limpiarCantidad"
          @paste="pegarCantidad"
          @keyup.enter="continuar"
        />

        <p v-if="errorModal" class="text-xs text-red-600 mt-2 m-0">{{ errorModal }}</p>
        <p v-else-if="errorCantidad" class="text-xs text-red-600 mt-2 m-0">{{ errorCantidad }}</p>
        <p v-else class="text-[11px] text-slate-400 mt-2 m-0">
          Solo números enteros positivos. Máximo {{ lote.cantidad_actual }}.
        </p>

        <div class="flex gap-2 mt-5">
          <button
            type="button"
            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium cursor-pointer transition"
            @click="cerrar"
          >
            Cancelar
          </button>
          <button
            type="button"
            :disabled="!cantidadValida"
            class="flex-1 px-4 py-2.5 rounded-xl bg-[#2D3748] hover:bg-[#1A202C] text-white text-sm font-medium cursor-pointer transition disabled:opacity-40 disabled:cursor-not-allowed"
            @click="continuar"
          >
            Aceptar
          </button>
        </div>
      </template>

      <template v-else>
        <div class="text-center">
          <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-3 text-xl">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
          <h3 class="text-base font-bold text-slate-800 m-0">¿Confirmar disminución?</h3>
          <p class="text-sm text-slate-500 mt-2 mb-0">
            Se restarán <span class="font-bold text-slate-800">{{ cantidadNumero }}</span>
            {{ cantidadNumero === 1 ? 'unidad' : 'unidades' }} del lote
            <span class="font-mono font-bold text-slate-800 break-all">{{ lote.codigo_lote || '#' + lote.id }}</span>
            y del stock total de
            <span class="font-bold text-slate-800">{{ nombreProducto }}</span>.
            Esta acción no se puede deshacer.
          </p>
        </div>

        <div class="flex gap-2 mt-5">
          <button
            type="button"
            :disabled="guardando"
            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 text-sm font-medium cursor-pointer transition disabled:opacity-40 disabled:cursor-not-allowed"
            @click="paso = 'form'"
          >
            Volver
          </button>
          <button
            type="button"
            :disabled="guardando"
            class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium cursor-pointer transition disabled:opacity-40 disabled:cursor-not-allowed"
            @click="confirmar"
          >
            {{ guardando ? 'Guardando...' : 'Sí, disminuir' }}
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import api from '@/services/api'

const TECLAS_PERMITIDAS = ['Backspace', 'Delete', 'Tab', 'Enter', 'Escape', 'ArrowLeft', 'ArrowRight', 'Home', 'End']

const props = defineProps({
  lote: { type: Object, required: true },
  nombreProducto: { type: String, default: '' }
})

const emit = defineEmits(['cerrar', 'reducido'])

const paso = ref('form')
const cantidadInput = ref('')
const guardando = ref(false)
const errorModal = ref('')
const inputCantidad = ref(null)
const inicioEnFondo = ref(false)

const cantidadNumero = computed(() => cantidadInput.value === '' ? 0 : parseInt(cantidadInput.value, 10))

const restante = computed(() => {
  const resultado = Number(props.lote.cantidad_actual) - cantidadNumero.value
  return resultado < 0 ? 0 : resultado
})

const errorCantidad = computed(() => {
  if (cantidadInput.value === '') return ''
  if (cantidadNumero.value < 1) return 'La cantidad debe ser al menos 1.'
  if (cantidadNumero.value > Number(props.lote.cantidad_actual)) {
    return `No puedes disminuir más de ${props.lote.cantidad_actual}.`
  }
  return ''
})

const cantidadValida = computed(() => cantidadInput.value !== '' && !errorCantidad.value)

const bloquearTecla = (e) => {
  if (e.ctrlKey || e.metaKey) return
  if (TECLAS_PERMITIDAS.includes(e.key)) return
  if (!/^[0-9]$/.test(e.key)) e.preventDefault()
}

const limpiarCantidad = () => {
  cantidadInput.value = cantidadInput.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '')
}

const pegarCantidad = (e) => {
  e.preventDefault()
  const texto = (e.clipboardData?.getData('text') || '').replace(/\D/g, '')
  cantidadInput.value = (cantidadInput.value + texto).replace(/^0+(?=\d)/, '')
}

const registrarInicio = (e) => {
  inicioEnFondo.value = e.target === e.currentTarget
}

const cerrarSiFondo = (e) => {
  const cerrarModal = inicioEnFondo.value && e.target === e.currentTarget
  inicioEnFondo.value = false
  if (cerrarModal) cerrar()
}

const cerrar = () => {
  if (guardando.value) return
  emit('cerrar')
}

const continuar = () => {
  if (!cantidadValida.value) return
  errorModal.value = ''
  paso.value = 'confirmar'
}

const confirmar = async () => {
  if (!cantidadValida.value || guardando.value) return

  guardando.value = true
  errorModal.value = ''

  const cantidad = cantidadNumero.value

  try {
    await api.patch(`/lotes/${props.lote.id}/reducir`, { cantidad })

    guardando.value = false
    emit('reducido', {
      cantidad,
      codigo: props.lote.codigo_lote || `#${props.lote.id}`
    })
  } catch (err) {
    const data = err.response?.data
    errorModal.value = data?.errors?.cantidad?.[0] || data?.message || 'No se pudo disminuir el stock del lote.'
    paso.value = 'form'
    guardando.value = false
  }
}

onMounted(() => {
  nextTick(() => inputCantidad.value?.focus())
})
</script>