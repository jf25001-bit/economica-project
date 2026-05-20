<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden w-full max-w-5xl grid grid-cols-2">
      
      <!-- Lado izquierdo -->
      <div class="bg-[#46674A] text-white flex flex-col items-center justify-center p-12">
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

      <!-- Formulario -->
      <div class="p-12 flex flex-col justify-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
          Iniciar Sesión
        </h2>

        <p class="text-gray-500 mb-8">
          Ingresa tu usuario para continuar.
        </p>

        <!-- Correo -->
        <div class="mb-5">
          <label class="block text-gray-700 font-medium mb-2">
            Usuario
          </label>
          <input
            type="usuario"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Contraseña -->
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">
            Contraseña
          </label>
          <input
            type="password"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#46674A]"
          />
        </div>

        <!-- Botón -->
        <button
          class="w-full bg-[#46674A] hover:bg-[#3b5740] text-white py-3 rounded-xl font-semibold shadow-md transition"
        >
          Ingresar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>