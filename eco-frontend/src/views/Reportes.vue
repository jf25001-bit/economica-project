<template>
  <div class="min-h-screen bg-slate-50/50 p-6 sm:p-8">
    <div class="mb-8">
      <h1 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">Reportes</h1>
      <p class="text-slate-500 text-sm font-medium mt-1">La Económica — Documentos en PDF</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <button
        v-for="reporte in reportes"
        :key="reporte.id"
        @click="abrir(reporte)"
        class="text-left bg-white p-6 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 hover:-translate-y-1 hover:shadow-2xl transition-all cursor-pointer"
      >
        <div :class="['w-14 h-14 rounded-2xl border flex items-center justify-center text-2xl mb-4', reporte.estilo]">
          <i :class="['bi', reporte.icono]"></i>
        </div>
        <h2 class="text-lg font-black text-slate-800">{{ reporte.titulo }}</h2>
        <p class="text-sm text-slate-500 font-medium mt-1">{{ reporte.descripcion }}</p>
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#2B3A4A] mt-4">
          <i class="bi bi-file-earmark-pdf"></i> Generar reporte
        </span>
      </button>
    </div>

    <div
      v-if="activo"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
      @click.self="cerrar"
    >
      <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-black text-slate-800">{{ activo.titulo }}</h2>
            <p class="text-slate-500 text-xs font-medium mt-0.5">{{ activo.descripcion }}</p>
          </div>
          <button @click="cerrar" class="text-slate-400 hover:text-slate-700 transition cursor-pointer">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-6 space-y-5">
          <div v-if="activo.id === 'empleado'">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Empleado</label>
            <div class="relative">
              <select
                v-model="empleadoId"
                :disabled="cargandoEmpleados"
                class="w-full pl-4 pr-10 py-2.5 bg-white border border-slate-200/80 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium appearance-none cursor-pointer shadow-sm disabled:opacity-60"
              >
                <option :value="null" disabled>
                  {{ cargandoEmpleados ? 'Cargando empleados...' : 'Selecciona un empleado' }}
                </option>
                <option v-for="e in empleados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
              </select>
              <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                <i class="bi bi-chevron-down text-xs"></i>
              </span>
            </div>
          </div>

          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Periodo</label>
            <div class="grid grid-cols-4 gap-2">
              <button
                v-for="p in periodos"
                :key="p.valor"
                @click="periodo = p.valor"
                :class="[
                  'py-2 rounded-xl text-xs font-bold border transition cursor-pointer',
                  periodo === p.valor
                    ? 'bg-[#2B3A4A] text-white border-[#2B3A4A] shadow-lg shadow-[#2B3A4A]/20'
                    : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
                ]"
              >
                {{ p.texto }}
              </button>
            </div>
          </div>

          <div v-if="periodo === 'rango'" class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Desde</label>
              <input
                type="date"
                v-model="fechaInicio"
                class="w-full px-3 py-2.5 bg-white border border-slate-200/80 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20"
              />
            </div>
            <div>
              <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">Hasta</label>
              <input
                type="date"
                v-model="fechaFin"
                :min="fechaInicio"
                class="w-full px-3 py-2.5 bg-white border border-slate-200/80 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20"
              />
            </div>
          </div>

          <div
            v-if="error"
            class="p-3 bg-red-50 border border-red-200/80 text-red-700 rounded-2xl flex items-center gap-3 text-sm font-semibold"
          >
            <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
            <span>{{ error }}</span>
          </div>
        </div>

        <div class="p-6 pt-0 flex gap-3">
          <button
            @click="cerrar"
            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            @click="generar"
            :disabled="!puedeGenerar || cargando"
            class="flex-1 inline-flex items-center justify-center gap-2 bg-[#2B3A4A] hover:bg-[#1f2b38] text-white font-bold px-4 py-2.5 rounded-xl text-sm transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i :class="cargando ? 'bi bi-arrow-repeat animate-spin' : 'bi bi-download'"></i>
            <span>{{ cargando ? 'Generando...' : 'Generar PDF' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { getEmpleados, generarReportePDF } from '@/services/reporteService'

const reportes = [
  {
    id: 'ventas',
    titulo: 'Ventas Generales',
    descripcion: 'Todas las ventas del periodo con vendedor, productos, pago y vuelto.',
    icono: 'bi-receipt',
    estilo: 'bg-emerald-50 text-emerald-600 border-emerald-100'
  },
  {
    id: 'compras',
    titulo: 'Compras Generales',
    descripcion: 'Todas las compras a proveedores del periodo con sus productos.',
    icono: 'bi-cart-check-fill',
    estilo: 'bg-sky-50 text-sky-600 border-sky-100'
  },
  {
    id: 'empleado',
    titulo: 'Ventas por Empleado',
    descripcion: 'Ventas realizadas por un empleado específico en el periodo.',
    icono: 'bi-person-badge',
    estilo: 'bg-amber-50 text-amber-600 border-amber-100'
  }
]

const periodos = [
  { valor: 'semana', texto: 'Semana' },
  { valor: 'mes', texto: 'Mes' },
  { valor: 'anio', texto: 'Año' },
  { valor: 'rango', texto: 'Rango' }
]

const activo = ref(null)
const periodo = ref('mes')
const fechaInicio = ref('')
const fechaFin = ref('')
const empleadoId = ref(null)
const empleados = ref([])
const cargando = ref(false)
const cargandoEmpleados = ref(false)
const error = ref('')

const puedeGenerar = computed(() => {
  if (!activo.value) return false
  if (activo.value.id === 'empleado' && !empleadoId.value) return false
  if (periodo.value === 'rango' && (!fechaInicio.value || !fechaFin.value)) return false
  return true
})

const cargarEmpleados = async () => {
  if (empleados.value.length) return
  cargandoEmpleados.value = true
  try {
    empleados.value = await getEmpleados()
  } catch (e) {
    error.value = 'No se pudo cargar la lista de empleados.'
  } finally {
    cargandoEmpleados.value = false
  }
}

const abrir = (reporte) => {
  activo.value = reporte
  error.value = ''
  periodo.value = 'mes'
  fechaInicio.value = ''
  fechaFin.value = ''
  empleadoId.value = null
  if (reporte.id === 'empleado') cargarEmpleados()
}

const cerrar = () => {
  activo.value = null
}

const generar = async () => {
  error.value = ''
  cargando.value = true
  try {
    await generarReportePDF({
      tipo: activo.value.id,
      periodo: periodo.value,
      empleado_id: activo.value.id === 'empleado' ? empleadoId.value : undefined,
      fecha_inicio: periodo.value === 'rango' ? fechaInicio.value : undefined,
      fecha_fin: periodo.value === 'rango' ? fechaFin.value : undefined
    })
    cerrar()
  } catch (e) {
    error.value = e.message
  } finally {
    cargando.value = false
  }
}
</script>