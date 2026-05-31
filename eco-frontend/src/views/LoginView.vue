<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 sm:px-6 lg:px-8">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2">

      <div class="bg-emerald-50 flex flex-col items-center justify-center p-8 border-b md:border-b-0 md:border-r border-gray-100">
        <div class="h-32 w-32 md:h-48 md:w-48 flex items-center justify-center">
          <img :src="rutaLogo" class="h-full w-full object-contain" alt="Logo La Económica" />
        </div>

        <p class="mt-2 text-sm text-emerald-600 font-medium text-center hidden md:block">
          Sistema de Control Integral para Minisúper
        </p>
      </div>

      <div class="p-8 md:p-12 flex flex-col justify-center">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
            Bienvenido de nuevo
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Ingresa tus credenciales para acceder al sistema.
          </p>
        </div>

        <form class="mt-8 space-y-5" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Correo Electrónico
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              class="block w-full px-3 py-2.5 bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors"
              placeholder="correo@minisuper.com"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
              Contraseña
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              class="block w-full px-3 py-2.5 bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors"
              placeholder="••••••••"
            />
          </div>

          <div class="pt-2">
            <button
              type="submit"
              class="w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-md transition-colors"
            >
              Iniciar Sesión
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// IMPORTACIÓN DE SEGURIDAD: Asegúrate de guardar tu archivo 'logo.png' en la carpeta 'src/assets/'
import logoFile from '../assets/login.png'

const email = ref('')
const password = ref('')

// Asignamos la importación procesada a una variable utilizable por el HTML
const rutaLogo = logoFile

const handleLogin = () => {
  console.log('Login solicitado para:', email.value, password.value)
}
</script>
