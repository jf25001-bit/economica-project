<template>
  <div class="p-6 bg-slate-50 min-h-screen text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">

      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <i class="bi bi-shield-lock-fill text-sky-500"></i> Control y Arqueo de Cajas
          </h1>
          <p class="text-xs text-slate-500 mt-1">Supervisión en tiempo real de turnos activos e historial de cierres</p>
        </div>

        <div class="flex gap-2">
          <button 
            @click="vista = 'activas'" 
            :class="[
              'px-4 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer flex items-center gap-2 shadow-md',
              vista === 'activas' 
                ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-sky-500/20' 
                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
            ]"
          >
            <i class="bi bi-exclamation-triangle-fill"></i> Cajas Abiertas
            <span v-if="cajasActivas.length > 0" class="px-1.5 py-0.5 text-[10px] bg-white/20 rounded-full font-black text-white">
              {{ cajasActivas.length }}
            </span>
          </button>

          <button 
            @click="vista = 'historial'" 
            :class="[
              'px-4 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer flex items-center gap-2 shadow-md',
              vista === 'historial' 
                ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-sky-500/20' 
                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
            ]"
          >
            <i class="bi bi-clock-history"></i> Historial
          </button>
        </div>
      </div>

      <!-- VISTA 1: CAJAS ACTIVAS -->
      <div v-if="vista === 'activas'" class="bg-[#0d1424] border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4 text-white">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-amber-400 flex items-center gap-2">
            <i class="bi bi-circle-fill text-[8px] animate-pulse text-amber-400"></i> Cajas Abiertas Actualmente
          </h2>
        </div>

        <div v-if="cajasActivas.length === 0" class="text-slate-400 text-center py-12 text-sm">
          No hay ninguna caja abierta en este momento.
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="caja in cajasActivas" :key="caja.id" class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex flex-col justify-between space-y-4">
            <div>
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-sky-400 uppercase tracking-widest">Caja #{{ caja.id }}</span>
                <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-md">Abierta</span>
              </div>
              
              <h3 class="text-base font-black text-white mt-3">{{ nombreUsuario(caja) }}</h3>
              <p class="text-xs text-slate-400 mt-1">Apertura: {{ caja.fecha_apertura }}</p>
              
              <div class="mt-4 pt-3 border-t border-slate-800 space-y-1.5">
                <div class="flex justify-between items-center text-xs">
                  <span class="text-slate-400">Monto Base:</span>
                  <span class="font-extrabold text-white">${{ Number(caja.monto_apertura || 0).toFixed(2) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                  <span class="text-slate-400">Ventas Registradas:</span>
                  <span class="font-bold text-emerald-400">+${{ Number(caja.total_ventas || 0).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <button 
              @click="forzarCierre(caja)"
              class="w-full py-2.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-2"
            >
              <i class="bi bi-lock-fill"></i> Forzar Cierre de Caja
            </button>
          </div>
        </div>
      </div>

      <!-- VISTA 2: HISTORIAL DE CIERRES -->
      <div v-if="vista === 'historial'" class="bg-[#0d1424] border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4 text-white">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="bi bi-clock-history text-sky-400"></i> Historial de Aperturas y Cierres
          </h2>
        </div>

        <div v-if="historial.length === 0" class="text-slate-400 text-center py-12 text-sm">
          No se encontraron registros de cierres en el historial.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
              <tr>
                <th class="p-3">ID Caja / Usuario</th>
                <th class="p-3">Apertura</th>
                <th class="p-3">Cierre</th>
                <th class="p-3">Monto Inicial</th>
                <th class="p-3">Monto Final</th>
                <th class="p-3">Estado / Tipo</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="item in historial" :key="item.id" class="hover:bg-slate-900/60 transition-colors">
                <td class="p-3 font-extrabold text-white">Caja #{{ item.id }} ({{ nombreUsuario(item) }})</td>
                <td class="p-3 text-slate-400">{{ item.fecha_apertura }}</td>
                <td class="p-3 text-slate-400">{{ item.fecha_cierre || 'En curso' }}</td>
                <td class="p-3 font-semibold text-slate-200">${{ Number(item.monto_apertura || 0).toFixed(2) }}</td>
                <td class="p-3 font-bold text-emerald-400">${{ Number(item.monto_cierre || 0).toFixed(2) }}</td>
                <td class="p-3">
                  <!-- Validación ampliada para cierre forzado -->
                  <div v-if="item.cerrado_por_id || (item.observacion && (item.observacion.includes('Forzado') || item.observacion.includes('forzado') || item.observacion.includes('Admin')))" class="space-y-1">
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[10px] font-bold rounded-md inline-flex items-center gap-1">
                      <i class="bi bi-shield-exclamation"></i> Cierre Forzado por Admin
                    </span>
                    <p v-if="item.observacion" class="text-[10px] text-slate-400 leading-tight">
                      {{ item.observacion }}
                    </p>
                  </div>

                  <span v-else-if="item.estado === 'cerrada'" class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-md inline-block">
                    Cierre Normal
                  </span>

                  <span v-else class="px-2.5 py-1 bg-sky-500/10 text-sky-400 border border-sky-500/20 text-[10px] font-bold rounded-md inline-block">
                    En Curso
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { cajaService } from '@/services/cajaService'
import Swal from 'sweetalert2'

const vista = ref('activas')
const cajasActivas = ref([])
const historial = ref([])

const nombreUsuario = (item) => {
  return item.usuario?.nombre || item.usuario?.name || `Usuario #${item.user_id}`
}

const cargarCajasActivas = async () => {
  try {
    const res = await cajaService.obtenerCajasActivasGlobales()
    cajasActivas.value = res.data || res || []
  } catch (e) {
    console.error('Error al obtener cajas activas:', e)
  }
}

const cargarHistorial = async () => {
  try {
    const res = await cajaService.obtenerHistorialCierres()
    historial.value = res.data || res || []
  } catch (e) {
    console.error('Error al obtener historial:', e)
  }
}

const forzarCierre = async (caja) => {
  const montoInicial = Number(caja.monto_apertura || 0).toFixed(2)
  const totalVentas = Number(caja.total_ventas || 0).toFixed(2)
  const totalEsperado = (Number(caja.monto_apertura || 0) + Number(caja.total_ventas || 0)).toFixed(2)
  const usuarioNom = nombreUsuario(caja)

  const { value: observacion, isConfirmed } = await Swal.fire({
    title: '¿Forzar Cierre de Caja?',
    html: `
      <p class="text-xs text-slate-300 mb-3">Vas a cerrar la <strong>CAJA #${caja.id}</strong> asignada a <strong>${usuarioNom}</strong></p>
      
      <div style="background-color: #0f172a; border: 1px solid #334155; padding: 12px; border-radius: 12px; margin-bottom: 15px; text-align: left;">
        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; color: #94a3b8;">
          <span>Monto Inicial:</span>
          <strong style="color: #f8fafc;">$${montoInicial}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; color: #94a3b8;">
          <span>Total Vendido:</span>
          <strong style="color: #34d399;">+$${totalVentas}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; padding-top: 6px; border-top: 1px dashed #334155; color: #38bdf8;">
          <span>Esperado en Caja:</span>
          <span>$${totalEsperado}</span>
        </div>
      </div>

      <p class="text-xs text-slate-300 mb-2 font-medium">Ingresa el motivo del cierre:</p>
    `,
    input: 'text',
    inputPlaceholder: 'Ej. El cajero dejó la sesión abierta',
    showCancelButton: true,
    confirmButtonText: 'Sí, cerrar caja',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    background: '#1e293b',
    color: '#ffffff'
  })

  if (isConfirmed) {
    try {
      await cajaService.forzarCierreAdmin(caja.id, observacion || 'Cierre forzado por el Administrador')
      await Swal.fire({
        title: 'Caja Cerrada',
        text: 'Se forzó el cierre de la caja correctamente.',
        icon: 'success',
        background: '#1e293b',
        color: '#ffffff'
      })
      cargarCajasActivas()
      cargarHistorial()
    } catch (e) {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo forzar el cierre de la caja.',
        icon: 'error',
        background: '#1e293b',
        color: '#ffffff'
      })
    }
  }
}

onMounted(() => {
  cargarCajasActivas()
  cargarHistorial()
})
</script>