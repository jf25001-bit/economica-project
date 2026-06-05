<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-2">
      
      <div class="bg-[#8ecae6] text-slate-800 flex flex-col items-center justify-center p-12">
        <img
          src="/logo.jpeg"
          alt="Logo"
          class="w-32 h-32 object-contain mb-6"
        />
        <h1 class="text-4xl font-bold mb-2 text-sky-950">La Económica</h1>
        <p class="text-sky-900/80 text-center font-medium">
          Sistema de Control Integral para Minisúper
        </p>
      </div>

      <div class="p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
          Bienvenido de nuevo
        </h2>
        <p class="text-gray-500 mb-8">
          Ingresa tus credenciales para acceder al sistema.
        </p>

        <div
          v-if="error"
          class="bg-red-50 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm border border-red-100">
          {{ error }}
        </div>

        <div class="mb-5">
          <label class="block text-gray-700 font-medium mb-2">
            Usuario
          </label>
          <input 
            v-model="name" 
            type="text" 
            placeholder="Ingrese usuario" 
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8ecae6]" 
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">
            Contraseña
          </label>
          <input
            v-model="password"
            type="password"
            placeholder="••••••••"
            @keyup.enter="handleLogin"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8ecae6]"
          />
        </div>

        <button
          @click="handleLogin"
          :disabled="loading"
          class="w-full bg-[#8ecae6] hover:bg-[#219ebc] hover:text-white text-sky-950 py-3 rounded-xl font-bold shadow-md transition disabled:opacity-50"
        >
          {{ loading ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../services/authService'

// Router para redirigir páginas
const router = useRouter()

// Variables reactivas del formulario vinculadas a los v-model
const name = ref('')
const password = ref('')

// Estados de control para la interfaz
const error = ref('')
const loading = ref(false)

// Función para procesar el inicio de sesión
const handleLogin = async () => {
  // Limpiar estados de errores anteriores
  error.value = ''

  // Validar campos vacíos en el lado del cliente
  if (!name.value || !password.value) {
    error.value = 'Completa todos los campos'
    return
  }

  loading.value = true

  try {
    // Mandamos el objeto mapeando 'name.value' a la propiedad 'usuario' que tu API espera
    const response = await login({
      usuario: name.value,
      password: password.value
    })

    // Si el backend responde OK (200), guardamos los estados de sesión
    if (response.access_token) {
      localStorage.setItem('token', response.access_token)
    }

    if (response.user) {
      localStorage.setItem('user', JSON.stringify(response.user))
    }

    // Redirección limpia hacia el panel de reportes/control
    router.push('/dashboard')

  } catch (err) {
    console.error('Detalle del error atrapado:', err)
    // Mostramos el mensaje exacto que configuramos en tu controlador de Laravel
    error.value = err.response?.data?.message || err.message || 'Credenciales inválidas'
  } finally {
    loading.value = false
  }
}
</script>