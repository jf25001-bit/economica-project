<template>
  <div class="p-6">
   
    
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-800">
        Bienvenido a la económica
      </h1>

      <p class="text-gray-500 mt-2">
        Panel principal del sistema
      </p>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Productos -->
      <router-link
        to="/productos"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
 <div class="flex items-center justify-between">
          <div>
 <p class="text-gray-500 font-medium">
              Productos
            </p>

            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              120
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center">
            <i class="bi bi-box-seam text-2xl text-green-700"></i>
          </div>
          </div> 
 </router-link>
 <!-- Categorías -->
      <router-link
        to="/categorias"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
<p class="text-gray-500 font-medium">
              Categorías
            </p>

         <h2 class="text-3xl font-bold text-gray-800 mt-1">
          {{ categorias.length }}
        </h2> 
          </div>

          <div class="w-14 h-14 rounded-2xl bg-[#46674A]/10 flex items-center justify-center">
            <i class="bi bi-grid text-2xl text-[#46674A]"></i>
          </div>
          </div>
         </router-link>
<!-- Proveedores -->
      <router-link
        to="/proveedores"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
<p class="text-gray-500 font-medium">
              Proveedores
            </p>

            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              {{ proveedores.length }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
            <i class="bi bi-truck text-2xl text-blue-700"></i>
          </div>
</div>
 </router-link>
 <!-- Usuarios -->
   <router-link
        to="/usuarios"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
 <p class="text-gray-500 font-medium">
              Usuarios
            </p>

            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              4
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center">
            <i class="bi bi-people text-2xl text-purple-700"></i>
          </div>
        </div>
      </router-link>

    </div>

    <!-- Actividad reciente -->
    <div class="mt-8 bg-white rounded-3xl shadow-md p-6">

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
          Actividad reciente
        </h2>

        <span class="text-sm text-gray-400">
          Últimos movimientos
        </span>
      </div>

      <div class="space-y-4">

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-green-50">
          <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
            <i class="bi bi-plus-circle-fill text-green-600"></i>
          </div>

          <div>
            <p class="font-medium text-gray-700">
              Nueva categoría agregada
            </p>

            <p class="text-sm text-gray-500">
              Bebidas
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-blue-50">
          <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
            <i class="bi bi-person-plus-fill text-blue-600"></i>
          </div>

          <div>
            <p class="font-medium text-gray-700">
              Nuevo proveedor registrado
            </p>

            <p class="text-sm text-gray-500">
              Coca Cola
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-yellow-50">
          <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
            <i class="bi bi-pencil-square text-yellow-600"></i>
          </div>

          <div>
            <p class="font-medium text-gray-700">
              Categoría actualizada
            </p>

            <p class="text-sm text-gray-500">
              Lácteos
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-red-50">
          <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
            <i class="bi bi-trash-fill text-red-600"></i>
          </div>

          <div>
            <p class="font-medium text-gray-700">
              Categoría eliminada
            </p>

            <p class="text-sm text-gray-500">
              Dulces
            </p>
          </div>
</div>
</div>
</div>
     </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { getCategorias } from '../services/categoriaService'
import { getProveedores } from '../services/proveedorService'

const categorias = ref([])
const proveedores = ref([])

const cargarCategorias = async () => {
  try {
    categorias.value = await getCategorias()
  } catch (error) {
    console.error(error)
  }
}

const cargarProveedores = async () => {
  try {
    proveedores.value = await getProveedores()
  } catch (error) {
    console.error(error)
  }
}

onMounted(() => {
  cargarCategorias()
  cargarProveedores()
})
</script>