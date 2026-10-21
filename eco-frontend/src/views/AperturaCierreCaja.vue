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

    <!-- Banner: caja abierta por otro usuario -->
    <div
      v-if="!cargandoEstado && !cajaAbierta && cajaOtroUsuario"
      class="bg-[#0f172a] border border-sky-500/40 rounded-2xl p-4 flex items-center gap-4 text-sky-400 shadow-md"
    >
      <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center shrink-0">
        <i class="bi bi-person-lock text-xl text-sky-400"></i>
      </div>
      <div>
        <h4 class="font-bold text-sm text-sky-400">Caja abierta por {{ cajaOtroUsuario.usuario }}</h4>
        <p class="text-xs text-slate-400 mt-0.5">
          Solo su dueño puede cerrarla de forma normal. Como administrador, puedes hacer un Cierre Forzado desde Control de Cajas.
        </p>
      </div>
    </div>

    <!-- Banner Alerta Caja Cerrada -->
    <div 
      v-else-if="!cargandoEstado && !cajaAbierta" 
      class="bg-[#0f172a] border border-amber-500/40 rounded-2xl p-4 flex items-center gap-4 text-amber-400 shadow-md"
    >
      <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
        <i class="bi bi-exclamation-triangle-fill text-xl text-amber-400 animate-pulse"></i>
      </div>
      <div>
        <h4 class="font-bold text-sm text-amber-400">Caja Cerrada</h4>
        <p class="text-xs text-slate-400 mt-0.5">
          Debes realizar la apertura ingresando el monto base inicial antes de operar ventas.
        </p>
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

          <p v-if="cajaAbierta" class="text-[11px] font-bold text-sky-400">
            {{ etiquetaTurno(cajaInfo?.turno) }}
          </p>
          
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
                  <option value="unico" :disabled="esTarde">Turno Único (jornada completa)</option>
                  <option value="manana" :disabled="esTarde">Turno Mañana </option>
                  <option value="tarde" :disabled="!esTarde">Turno Tarde </option>
                </select>
                <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              </div>
              <p v-if="esTarde" class="text-[10px] text-amber-400 font-bold mt-2">
                Desde las 12:00 p.m. solo está disponible el turno tarde.
              </p>
              <p v-else class="text-[10px] text-amber-400 font-bold mt-2">
                El turno tarde estará disponible desde las 12:00 p.m.
              </p>
            </div>
          </div>

          <div>
            <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">
              MONTO BASE DE APERTURA
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sky-400 font-bold text-base">$</span>
              <input
                v-model="formApertura.monto_apertura"
                @input="formApertura.monto_apertura = recortarDecimales(formApertura.monto_apertura)"
                @blur="formApertura.monto_apertura = aDosDecimales(formApertura.monto_apertura)"
                type="text"
                inputmode="decimal"
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

          <!-- Resumen del turno -->
          <div class="grid grid-cols-2 gap-4 bg-slate-900/60 border border-slate-800 rounded-xl p-4">
            <div>
              <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Monto Apertura</p>
              <p class="text-lg font-black text-white mt-1">${{ formatoMonto(cajaInfo?.monto_apertura) }}</p>
            </div>
            <div>
              <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">Ventas del Turno</p>
              <p class="text-lg font-black text-white mt-1">${{ formatoMonto(cajaInfo?.total_ventas) }}</p>
            </div>
          </div>

          <div>
            <label class="block text-slate-400 text-[11px] font-black uppercase tracking-wider mb-2">
              MONTO TOTAL EN EFECTIVO
            </label>
            <p class="text-[11px] text-sky-400 font-bold mb-2">
              <i class="bi bi-magic"></i> Calculado automáticamente: ${{ formatoMonto(montoEsperado) }} — puedes ajustarlo si el conteo físico difiere.
            </p>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400 font-bold text-base">$</span>
              <input
                v-model="formCierre.monto_cierre"
                @input="formCierre.monto_cierre = recortarDecimales(formCierre.monto_cierre)"
                @blur="formCierre.monto_cierre = aDosDecimales(formCierre.monto_cierre)"
                type="text"
                inputmode="decimal"
                required
                placeholder="0.00"
                class="w-full p-3.5 pl-8 pr-20 bg-slate-900/90 border border-rose-500/80 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-all text-base placeholder-slate-600"
              />
              <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 tracking-wider">
                USD
              </span>
            </div>

            <!-- Diferencia en vivo -->
            <p
              v-if="diferencia !== 0"
              :class="[
                'text-xs font-bold mt-2',
                diferencia > 0 ? 'text-emerald-400' : 'text-rose-400'
              ]"
            >
              <i :class="diferencia > 0 ? 'bi bi-arrow-up-circle' : 'bi bi-arrow-down-circle'"></i>
              {{ diferencia > 0 ? 'Sobrante' : 'Faltante' }} de ${{ formatoMonto(Math.abs(diferencia)) }} respecto al monto esperado
            </p>
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
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import Swal from 'sweetalert2'
import { cajaService } from '@/services/cajaService'

const cajaAbierta = ref(false)
const cajaInfo = ref(null)
const cajaOtroUsuario = ref(null)
const cargandoEstado = ref(true)
const procesando = ref(false)

const formApertura = ref({
  monto_apertura: '0.00',
  caja_id: '1',
  turno: 'unico'
})

const formCierre = ref({
  monto_cierre: '0.00'
})

const horaSV = () =>
  Number(new Intl.DateTimeFormat('en-US', { timeZone: 'America/El_Salvador', hour: 'numeric', hour12: false }).format(new Date())) % 24

const hora = ref(horaSV())
const esTarde = computed(() => hora.value >= 12)

watch(esTarde, (tarde) => {
  formApertura.value.turno = tarde ? 'tarde' : 'unico'
}, { immediate: true })

const etiquetaTurno = (t) =>
  ({ unico: 'Turno Único', manana: 'Turno Mañana', tarde: 'Turno Tarde' }[t] || 'Turno Único')

const montoEsperado = computed(() => {
  const apertura = parseFloat(cajaInfo.value?.monto_apertura || 0)
  const ventas = parseFloat(cajaInfo.value?.total_ventas || 0)
  return Number((apertura + ventas).toFixed(2))
})

const diferencia = computed(() => {
  return Number((parseFloat(formCierre.value.monto_cierre || 0) - montoEsperado.value).toFixed(2))
})

const formatoMonto = (valor) => {
  const num = parseFloat(valor || 0)
  return num.toFixed(2)
}

const recortarDecimales = (valor) => {
  const limpio = String(valor).replace(/[^\d.]/g, '')
  const [entero, ...resto] = limpio.split('.')
  if (!resto.length) return entero
  return `${entero}.${resto.join('').slice(0, 2)}`
}

const aDosDecimales = (valor) => (Number(valor) || 0).toFixed(2)

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

// ---------- Cierre automático al terminar el día ----------
let reloj = null
let cerrandoAuto = false

const fechaDe = (valor) => {
  const d = valor ? new Date(valor) : new Date()
  return `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()}`
}

const esDeOtroDia = (fechaApertura) => fechaDe(fechaApertura) !== fechaDe()

const cerrarPorFinDeDia = async () => {
  if (cerrandoAuto) return
  cerrandoAuto = true
  try {
    await cajaService.cerrarCaja({ monto_cierre: montoEsperado.value, automatico: true })
    await consultarEstado()
    Swal.fire({
      icon: 'info',
      title: 'Caja cerrada automáticamente',
      text: 'La caja se cerró porque cambió el día.',
      confirmButtonText: 'Entendido',
      confirmButtonColor: '#0284c7',
      background: '#0f172a',
      color: '#fff'
    })
  } catch (e) {
    console.error('No se pudo cerrar la caja automáticamente:', e)
  } finally {
    cerrandoAuto = false
  }
}

const revisarFinDeDia = () => {
  if (cajaAbierta.value && cajaInfo.value && esDeOtroDia(cajaInfo.value.fecha_apertura)) {
    cerrarPorFinDeDia()
  }
}

const consultarEstado = async () => {
  cargandoEstado.value = true
  try {
    const data = await cajaService.obtenerEstado()
    
    if (data && data.caja) {
      cajaAbierta.value = true
      cajaInfo.value = data.caja
      cajaOtroUsuario.value = null

      const apertura = parseFloat(data.caja.monto_apertura || 0)
      const ventas = parseFloat(data.caja.total_ventas || 0)
      formCierre.value.monto_cierre = (apertura + ventas).toFixed(2)

      if (esDeOtroDia(data.caja.fecha_apertura)) {
        cerrarPorFinDeDia()
        return
      }
    } else {
      cajaAbierta.value = false
      cajaInfo.value = null
      cajaOtroUsuario.value = data?.caja_otro_usuario || null
      
      if (data && data.monto_anterior !== undefined) {
        formApertura.value.monto_apertura = parseFloat(data.monto_anterior).toFixed(2)
      }

      if (cajaOtroUsuario.value) {
        Swal.fire({
          title: 'Caja abierta por otro usuario',
          text: `La caja activa pertenece a "${cajaOtroUsuario.value.usuario}". Solo su dueño puede cerrarla de forma normal. Puedes hacer un Cierre Forzado desde Control de Cajas.`,
          icon: 'info',
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#0284c7',
          background: '#0f172a',
          color: '#fff'
        })
      } else {
        Swal.fire({
          title: '¡Caja Cerrada!',
          text: 'Debes aperturar un turno de caja para habilitar las operaciones en el sistema.',
          icon: 'warning',
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#0284c7',
          background: '#0f172a',
          color: '#fff'
        })
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
    const activas = await cajaService.obtenerCajasActivasGlobales()

    if (activas && activas.length > 0) {
      const cajaExistente = activas[0]
      const u = cajaExistente.usuario
      const responsable = [u?.nombre || u?.name, u?.apellido].filter(Boolean).join(' ') || 'otro usuario'
      
      await Swal.fire({
        icon: 'warning',
        title: 'Apertura Bloqueada',
        text: `Ya existe una caja abierta en el sistema iniciada por: "${responsable}". Se debe cerrar la caja activa antes de abrir un nuevo turno.`,
        background: '#0f172a',
        color: '#fff',
        confirmButtonColor: '#38bdf8'
      })
      procesando.value = false
      return
    }

    let userId = null
    const posiblesKeys = ['user', 'usuario', 'authUser', 'auth']

    for (const key of posiblesKeys) {
      const item = localStorage.getItem(key)
      if (item) {
        try {
          const parsed = JSON.parse(item)
          userId = parsed.id || parsed.user?.id || parsed.usuario?.id
          if (userId) break
        } catch (err) {
          console.warn(`No se pudo parsear la key "${key}" de localStorage`, err)
        }
      }
    }

    const datosEnvio = {
      ...formApertura.value,
      monto_apertura: Number(formApertura.value.monto_apertura),
      ...(userId ? { user_id: userId } : {})
    }

    await cajaService.abrirCaja(datosEnvio)
    
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
    const msg = e.response?.data?.message || 'Ocurrió un error al intentar abrir la caja.'
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

const ejecutarCierre = async () => {
  procesando.value = true
  try {
    await cajaService.cerrarCaja({ monto_cierre: Number(formCierre.value.monto_cierre) })
    
    Swal.fire({
      icon: 'success',
      title: '¡Caja Cerrada!',
      text: 'Se guardó el cierre de caja.',
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
  reloj = setInterval(() => {
    hora.value = horaSV()
    revisarFinDeDia()
  }, 30000)
})

onUnmounted(() => clearInterval(reloj))
</script>