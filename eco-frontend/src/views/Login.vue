<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100 p-4 sm:p-8">
    <!-- Contenedor principal -->
    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden w-full max-w-6xl grid grid-cols-1 md:grid-cols-12 border border-slate-200/80 min-h-[600px]">
      
      <!-- Panel Izquierdo (Marca) -->
      <div class="md:col-span-7 bg-gradient-to-br from-[#2B3A4A] via-[#23303E] to-[#1F2B37] text-white flex flex-col items-center justify-center p-8 sm:p-12 md:p-16 relative overflow-hidden">
        <!-- Luces decorativas de fondo -->
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-sky-500/15 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col items-center text-center w-full max-w-md">
          <!-- Logo con tamaño equilibrado -->
          <div class="w-40 h-40 sm:w-48 sm:h-48 md:w-52 md:h-52 rounded-3xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-center p-5 mb-6 shadow-2xl transition-transform hover:scale-105 duration-300">
            <img
              src="/nuevo logo.svg"
              alt="Logo La Económica"
              class="w-full h-full object-contain filter drop-shadow-xl"
            />
          </div>

          <h1 class="text-4xl sm:text-5xl font-black tracking-tight mb-3 text-white drop-shadow-sm">
            La Económica
          </h1>
          <p class="text-sky-200/90 text-base sm:text-lg font-medium max-w-sm">
            Sistema de gestión integral para minisúper
          </p>
          <div class="mt-6 inline-flex items-center gap-2 bg-sky-500/10 px-4 py-1.5 rounded-full border border-sky-400/30">
            <i class="bi bi-geo-alt-fill text-sky-400 text-xs"></i>
            <span class="text-xs uppercase tracking-widest text-sky-300 font-bold">
              Chalatenango
            </span>
          </div>
        </div>
      </div>

      <!-- Panel Derecho (Formulario) -->
      <div class="md:col-span-5 p-8 sm:p-12 md:p-14 flex flex-col justify-center bg-white">
        <div class="mb-8">
          <h2 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">
            Iniciar Sesión
          </h2>
          <p class="text-slate-500 text-sm sm:text-base mt-2 font-medium">
            Ingresa tu usuario y contraseña para continuar al sistema.
          </p>
        </div>

        <!-- Alerta de Error -->
        <div
          v-if="error"
          class="bg-red-50 border border-red-200 text-red-600 px-4 py-3.5 rounded-2xl text-sm font-medium mb-6 flex items-center gap-3 animate-shake"
        >
          <i class="bi bi-exclamation-circle-fill text-lg flex-shrink-0"></i>
          <span>{{ error }}</span>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Campo: Usuario -->
          <div>
            <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
              Usuario
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <i class="bi bi-person-fill text-xl"></i>
              </span>
              <input
                v-model="name"
                type="text"
                placeholder="Ej. juan.perez"
                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 text-base focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium"
              />
            </div>
          </div>

          <!-- Campo: Contraseña -->
          <div>
            <label class="block text-slate-700 text-xs font-extrabold uppercase tracking-wider mb-2">
              Contraseña
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <i class="bi bi-lock-fill text-xl"></i>
              </span>
              <input
                v-model="password"
                type="password"
                placeholder="••••••••"
                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 text-base focus:outline-none focus:border-[#2B3A4A] focus:ring-2 focus:ring-[#2B3A4A]/20 transition-all font-medium"
              />
            </div>
          </div>

          <!-- Botón de Ingreso -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-[#2B3A4A] hover:bg-[#1F2B37] text-white py-4 rounded-2xl font-bold text-base shadow-xl shadow-[#2B3A4A]/30 transition-all active:scale-[0.98] disabled:opacity-60 flex items-center justify-center gap-3 mt-4 cursor-pointer"
          >
            <i v-if="loading" class="bi bi-arrow-clockwise animate-spin text-xl"></i>
            <span>{{ loading ? 'Ingresando...' : 'Ingresar' }}</span>
          </button>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../services/authService'

const router = useRouter()

const name = ref('')
const password = ref('')

const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''

  if (!name.value || !password.value) {
    error.value = 'Por favor completa todos los campos'
    return
  }

  loading.value = true

  try {
    const response = await login({
      name: name.value,
      password: password.value
    })

    localStorage.setItem('token', response.access_token)
    localStorage.setItem('user', JSON.stringify(response.user))

    router.push('/dashboard')
  } catch (err) {
    error.value = err.message || 'Credenciales inválidas, intenta de nuevo'
  } finally {
    loading.value = false
  }
}
</script>