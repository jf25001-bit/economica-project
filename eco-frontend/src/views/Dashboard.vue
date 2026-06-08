<template>
  <div class="p-6">

    <!-- Bienvenida -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-800">
        Bienvenido a La Económica
      </h1>

      <p class="text-gray-500 mt-2">
        Panel principal del sistema
      </p>
    </div>

    <!-- Tarjetas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Productos -->
      <router-link
        to="/productos"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 font-medium">Productos</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">120</h2>
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
            <p class="text-gray-500 font-medium">Categorías</p>
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
            <p class="text-gray-500 font-medium">Proveedores</p>
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
            <p class="text-gray-500 font-medium">Usuarios</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">4</h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center">
            <i class="bi bi-people text-2xl text-purple-700"></i>
          </div>
        </div>
      </router-link>

    </div>

    <!-- FILTROS -->
    <div class="flex gap-3 mt-8">
      <button @click="filtro = '3dias'" class="px-4 py-2 bg-gray-100 rounded-xl">
        Últimos 3 días
      </button>

      <button @click="filtro = 'semana'" class="px-4 py-2 bg-gray-100 rounded-xl">
        Última semana
      </button>
    </div>

    <!-- GRÁFICO -->
    <div class="mt-10 bg-white rounded-3xl shadow-md p-6 w-full md:w-1/2 mx-auto">

      <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Resumen general del sistema
      </h2>

      <canvas ref="chartRef" height="120"></canvas>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

import { getCategorias } from '../services/categoriaService'
import { getProveedores } from '../services/proveedorService'

const categorias = ref([])
const proveedores = ref([])
const chartRef = ref(null)

const filtro = ref('3dias')

let chartInstance = null

const cargarCategorias = async () => {
  categorias.value = await getCategorias()
}

const cargarProveedores = async () => {
  proveedores.value = await getProveedores()
}

/**
 * Simulación por tiempo
 * (luego lo conectas a backend real)
 */
const obtenerDatosPorFiltro = () => {
  if (filtro.value === '3dias') {
    return {
      productos: [12, 18, 25],
      categorias: [3, 5, 7],
      proveedores: [2, 4, 6]
    }
  }

  return {
    productos: [40, 55, 70, 90, 100, 110, 120],
    categorias: [10, 12, 15, 18, 20, 22, 25],
    proveedores: [5, 6, 8, 9, 10, 12, 14]
  }
}

const crearGrafico = () => {
  const data = obtenerDatosPorFiltro()

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(chartRef.value, {
    type: 'doughnut',
    data: {
      labels: ['Productos', 'Categorías', 'Proveedores'],
      datasets: [
        {
          data: [
            data.productos[data.productos.length - 1],
            data.categorias[data.categorias.length - 1],
            data.proveedores[data.proveedores.length - 1]
          ],
          backgroundColor: [
            '#16a34a',
            '#46674A',
            '#2563eb'
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  })
}

onMounted(async () => {
  await Promise.all([
    cargarCategorias(),
    cargarProveedores()
  ])

  crearGrafico()
})

watch(filtro, () => {
  crearGrafico()
})
</script>