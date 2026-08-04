<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden w-full max-w-5xl grid grid-cols-2">
      
     
      <div class="bg-[#5B80B0] text-white flex flex-col items-center justify-center p-12">
        <img
          src="/logo.jpeg"
          alt="Logo"
          class="w-32 h-32 object-contain mb-6"
        />

        <h1 class="text-4xl font-bold mb-2">La Económica</h1>
        <p class="text-white/80 text-center">
          Sistema de gestión para minisúper
        </p>
      </div>

    
      <div class="p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
          Iniciar Sesión
        </h2>

        <p class="text-gray-500 mb-8">
          Ingresa tu usuario para continuar.
        </p>
        <div
              v-if="error"
              class="bg-red-100 text-red-700 px-4 py-3 rounded-xl mb-5">
              {{ error }}
        </div>

        <!-- nombre de usuario -->
        <div class="mb-5">
          <label class="block text-gray-700 font-medium mb-2">
            Usuario
          </label>
          <input v-model="name" type="text" placeholder="Ingrese usuario" class="w-full px-4 py-3 border
           border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5B80B0]" />
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
          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#5B80B0]"
        />
        </div>

       
        <button
          @click="handleLogin"
          :disabled="loading"
          class="w-full bg-[#5B80B0] hover:bg-[#5B80B0] text-white py-3 rounded-xl font-semibold shadow-md transition disabled:opacity-50"
        >
          {{ loading ? 'Ingresando...' : 'Ingresar' }}
      </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// importa las funciones necesarias de vue y vue router
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../services/authService'

// Router para redirigir páginas
const router = useRouter()

// variables que almacenan los datos ingresados en el formulario
const name = ref('')
const password = ref('')

// muestra errores y controla el estado de carga
const error = ref('')
const loading = ref(false)

// Función para iniciar sesión
const handleLogin = async () => {
  
  error.value = ''

  // validar campos vacíos
  if (!name.value || !password.value) {
    error.value = 'Completa todos los campos'
    return
  }

  loading.value = true

  try {
    // enviar datos al backend
    const response = await login({
      name: name.value,
      password: password.value
    })

    
    localStorage.setItem(
      'token',
      response.access_token
    )

    // guardar usuario
    localStorage.setItem(
      'user',
      JSON.stringify(response.user)
    )

   
    router.push('/dashboard')

  } catch (err) {
    error.value =
      err.message || 'Credenciales inválidas'
  } finally {
    loading.value = false
  }
}
</script>