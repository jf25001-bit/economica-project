<template>
  <div class="p-6 w-full max-w-full space-y-6 text-slate-200">
    
    <!-- Header Banner -->
    <div class="bg-[#0f172a] rounded-2xl p-6 border border-slate-800/80 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-2xl border border-sky-500/30">
          <i class="bi bi-wallet2"></i>
        </div>
        <div>
          <h1 class="text-2xl font-black text-white tracking-tight">Apertura y Cierre de Caja</h1>
          <p class="text-xs text-slate-400 mt-0.5">Módulo de gestión de turnos y arqueo de dinero</p>
        </div>
      </div>
    </div>

    <!-- State Loader -->
    <div v-if="cargandoEstado" class="bg-[#0f172a] rounded-2xl border border-slate-800/80 p-16 flex flex-col items-center justify-center text-slate-400">
      <i class="bi bi-arrow-repeat animate-spin text-4xl text-sky-400 mb-3"></i>
      <span class="text-xs font-bold uppercase tracking-wider">Cargando datos...</span>
    </div>

    <!-- Main Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
      
      <!-- Left Card: Status -->
      <div class="lg:col-span-4 bg-[#0f172a] rounded-2xl border border-slate-800/80 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden shadow-lg min-h-[400px]">
        
        <div class="relative z-10 mb-6">
          <div 
            :class="[
              'w-24 h-24 rounded-2xl flex items-center justify-center text-4xl border transition-all duration-300',
              !cajaAbierta 
                ? 'bg-rose-950/20 border-rose-500/30 text-rose-500' 
                : 'bg-emerald-950/20 border-emerald-500/30 text-emerald-400'
            ]"
          >
            <i :class="!cajaAbierta ? 'bi bi-lock-fill' : 'bi bi-unlock-fill'"></i>
          </div>
        </div>

        <div class="relative z-10 space-y-2">
          <span 
            :class="[
              'px-3 py-1 rounded-md text-[11px] font-black tracking-widest uppercase border inline-block',
              !cajaAbierta 
                ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' 
                : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
            ]"
          >
            {{ !cajaAbierta ? 'CAJA CERRADA' : 'CAJA ABIERTA' }}
          </span>
          
          <h3 class="text-2xl font-black text-white pt-2">
            {{ !cajaAbierta ? 'Turno Inactivo' : 'Turno Activo' }}
          </h3>
          
          <p class="text-xs text-slate-400 font-medium max-w-xs leading-relaxed">
            {{ !cajaAbierta 
                ? 'Ingresa el monto base para habilitar las operaciones de venta en el sistema.' 
                : `Aperturado el ${formatoFecha(cajaInfo?.fecha_apertura)}` 
            }}
          </p>
        </div>
      </div>

      <!-- Right Card: Forms -->
      <div class="lg:col-span-8 bg-[#0f172a] rounded-2xl border border-slate-800/80 p-8 flex flex-col justify-center shadow-lg">
        
        <!-- OPEN BOX FORM -->
        <form v-if="!cajaAbierta" @submit.prevent="ejecutarApertura" class="space-y-6">
          <div>
            <h2 class="text-xl font-black text-white">Abrir Turno de Caja</h2>
            <p class="text-xs text-slate-400 mt-1">Configuración de apertura e ingreso de fondo base</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">CAJA ASIGNADA</label>
              <div class="relative">
                <select v-model="formApertura.caja_id" class="w-full p-3.5 pr-10 bg-slate-900/90 border border-slate-800 rounded-xl text-xs font-bold text-white focus:outline-none focus:border-sky-500 appearance-none cursor-pointer">
                  <option value="1">Caja Principal</option>
                </select>
                <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              </div>
            </div>

            <div>
              <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">TURNO LABORAL</label>
              <div class="relative">
                <select v-model="formApertura.turno" class="w-full p-3.5 pr-10 bg-slate-900/90 border border-slate-800 rounded-xl text-xs font-bold text-white focus:outline-none focus:border-sky-500 appearance-none cursor-pointer">
                  <option value="Turno Único">Turno Único</option>
                  <option value="Turno Mañana">Turno Mañana</option>
                  <option value="Turno Tarde">Turno Tarde</option>
                </select>
                <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">
              MONTO BASE DE APERTURA
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 font-bold text-base">$</span>
              <input
                v-model.number="formApertura.monto_apertura"
                type="number"
                step="0.01"
                min="0"
                required
                placeholder="0.00"
                class="w-full p-3.5 pl-8 pr-20 bg-slate-900/90 border border-sky-500/80 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all text-base placeholder-slate-600"
              />
              <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 tracking-wider">
                USD
              </span>
            </div>
          </div>

          <button
            type="submit"
            :disabled="procesando"
            class="w-full py-4 bg-sky-600 hover:bg-sky-500 text-white font-black rounded-xl transition-all uppercase tracking-wider text-xs shadow-lg shadow-sky-600/30 active:scale-[0.99] disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
          >
            <i v-if="procesando" class="bi bi-arrow-repeat animate-spin text-base"></i>
            <i v-else class="bi bi-box-arrow-in-right text-base"></i>
            <span>{{ procesando ? 'ABRIENDO...' : 'ABRIR CAJA' }}</span>
          </button>
        </form>

        <!-- CLOSE BOX FORM -->
        <form v-else @submit.prevent="ejecutarCierre" class="space-y-6">
          <div>
            <h2 class="text-xl font-black text-white">Cierre y Arqueo de Caja</h2>
            <p class="text-xs text-slate-400 mt-1">Conteo y confirmación de dinero total en efectivo</p>
          </div>

          <div>
            <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">
              MONTO TOTAL EN EFECTIVO
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400 font-bold text-base">$</span>
              <input
                v-model.number="formCierre.monto_cierre"
                type="number"
                step="0.01"
                min="0"
                required
                placeholder="0.00"
                class="w-full p-3.5 pl-8 pr-20 bg-slate-900/90 border border-rose-500/80 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-all text-base placeholder-slate-600"
              />
              <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 tracking-wider">
                USD
              </span>
            </div>
          </div>

          <button
            type="submit"
            :disabled="procesando"
            class="w-full py-4 bg-rose-600 hover:bg-rose-500 text-white font-black rounded-xl transition-all uppercase tracking-wider text-xs shadow-lg shadow-rose-600/30 active:scale-[0.99] disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
          >
            <i v-if="procesando" class="bi bi-arrow-repeat animate-spin text-base"></i>
            <i v-else class="bi bi-lock-fill text-base"></i>
            <span>{{ procesando ? 'CERRANDO...' : 'CERRAR CAJA' }}</span>
          </button>
        </form>

      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import Swal from 'sweetalert2'
import { cajaService } from '@/services/cajaService'

const cajaAbierta = ref(false)
const cajaInfo = ref(null)
const cargandoEstado = ref(true)
const procesando = ref(false)

const formApertura = ref({
  monto_apertura: 0,
  caja_id: '1',
  turno: 'Turno Único'
})

const formCierre = ref({
  monto_cierre: 0
})

const formatoFecha = (fechaStr) => {
  if (!fechaStr) return 'Hoy'
  try {
    const fecha = new Date(fechaStr)
    return fecha.toLocaleString('es-ES', {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    })
  } catch (e) {
    return fechaStr
  }
}

const consultarEstado = async () => {
  cargandoEstado.value = true
  try {
    const res = await cajaService.obtenerEstado()
    // Desempaquetado seguro contra respuestas directas o envueltas por Axios (res.data)
    const data = res?.data ? res.data : res

    if (data && data.caja) {
      cajaAbierta.value = true
      cajaInfo.value = data.caja
    } else {
      cajaAbierta.value = false
      cajaInfo.value = null
      
      if (data && data.monto_anterior !== undefined) {
        formApertura.value.monto_apertura = parseFloat(data.monto_anterior)
      }
    }
  } catch (e) {
    console.error("Error al consultar caja:", e)
  } finally {
    cargandoEstado.value = false
  }
}

const ejecutarApertura = async () => {
  procesando.value = true
  try {
    let userId = 1
    const userStr = localStorage.getItem('user') || localStorage.getItem('usuario')
    if (userStr) {
      try {
        const parsed = JSON.parse(userStr)
        if (parsed && parsed.id) userId = parsed.id
      } catch (err) {
        console.warn("No se pudo parsear el usuario en storage", err)
      }
    }

    const datosEnvio = {
      ...formApertura.value,
      user_id: userId,
      observacion: 'Apertura de turno'
    }

    const res = await cajaService.abrirCaja(datosEnvio)
    const data = res?.data ? res.data : res

    if (data && data.caja) {
      cajaAbierta.value = true
      cajaInfo.value = data.caja
    }
    
    Swal.fire({
      icon: 'success',
      title: '¡Caja Abierta!',
      text: 'La caja se abrió correctamente.',
      timer: 1500,
      showConfirmButton: false,
      background: '#0f172a',
      color: '#fff'
    })
    
    await consultarEstado()
  } catch (e) {
    const msg = e.response?.data?.message || e.message || 'Ocurrió un error al intentar abrir la caja.'
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: msg,
      background: '#0f172a',
      color: '#fff'
    })
    await consultarEstado()
  } finally {
    procesando.value = false
  }
}

const ejecutarCierre = async () => {
  procesando.value = true
  try {
    const datosCierre = {
      ...formCierre.value,
      observacion: 'Cierre de turno habitual'
    }

    await cajaService.cerrarCaja(datosCierre)
    
    cajaAbierta.value = false
    cajaInfo.value = null
    formCierre.value.monto_cierre = 0

    Swal.fire({
      icon: 'success',
      title: '¡Caja Cerrada!',
      text: 'Se guardó el cierre de caja correctamente.',
      timer: 1500,
      showConfirmButton: false,
      background: '#0f172a',
      color: '#fff'
    })
    
    await consultarEstado()
  } catch (e) {
    const msg = e.response?.data?.message || 'Error al cerrar la caja.'
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: msg,
      background: '#0f172a',
      color: '#fff'
    })
  } finally {
    procesando.value = false
  }
}

onMounted(() => {
  consultarEstado()
})
</script>