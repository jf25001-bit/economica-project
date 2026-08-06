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

    <div v-if="error" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <router-link
        to="/productos"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 font-medium">Productos</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              {{ cargando ? '...' : resumen.productos }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center">
            <i class="bi bi-box-seam text-2xl text-green-700"></i>
          </div>
        </div>
      </router-link>

      <router-link
        to="/categorias"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 font-medium">Categorías</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              {{ cargando ? '...' : resumen.categorias }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-[#47B5AC]/10 flex items-center justify-center">
            <i class="bi bi-grid text-2xl text-[#47B5AC]"></i>
          </div>
        </div>
      </router-link>

      <router-link
        to="/proveedores"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 font-medium">Proveedores</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              {{ cargando ? '...' : resumen.proveedores }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
            <i class="bi bi-truck text-2xl text-blue-700"></i>
          </div>
        </div>
      </router-link>

      <router-link
        to="/usuarios"
        class="bg-white rounded-3xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition-all block"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 font-medium">Usuarios</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-1">
              {{ cargando ? '...' : resumen.usuarios }}
            </h2>
          </div>

          <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center">
            <i class="bi bi-people text-2xl text-purple-700"></i>
          </div>
        </div>
      </router-link>
    </div>

    <div class="mt-8 bg-white rounded-3xl shadow-md p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
          Actividad reciente
        </h2>

        <button
          @click="cargarDashboard"
          :disabled="cargando"
          class="text-sm text-[#47B5AC] font-semibold disabled:opacity-60"
        >
          {{ cargando ? 'Actualizando...' : 'Actualizar' }}
        </button>
      </div>

      <div class="space-y-4">
        <div
          v-for="actividad in actividadReciente"
          :key="actividad.key"
          class="flex items-center gap-4 p-4 rounded-2xl"
          :class="actividad.bg"
        >
          <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="actividad.iconBg">
            <i class="bi" :class="[actividad.icon, actividad.iconColor]"></i>
          </div>

          <div>
            <p class="font-medium text-gray-700">
              {{ actividad.titulo }}
            </p>

            <p class="text-sm text-gray-500">
              {{ actividad.detalle }}
            </p>
          </div>
        </div>

        <div v-if="!cargando && actividadReciente.length === 0" class="text-center py-10 text-gray-400 italic">
          No hay actividad reciente registrada.
        </div>

        <div v-if="cargando" class="text-center py-10 text-gray-400">
          Cargando datos del inicio...
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getCategorias } from '../services/categoriaService'
import { getProveedores } from '../services/proveedorService'
import { getUsuarios } from '../services/usuarioService'
import { getProductos } from '../services/productoService'

const categorias = ref([])
const proveedores = ref([])
const usuarios = ref([])
const productos = ref([])
const cargando = ref(false)
const error = ref('')

const normalizarLista = (respuesta) => {
  const data = respuesta?.data?.data || respuesta?.data || respuesta
  return Array.isArray(data) ? data : []
}

const resumen = computed(() => ({
  productos: productos.value.length,
  categorias: categorias.value.length,
  proveedores: proveedores.value.length,
  usuarios: usuarios.value.length
}))

const fechaActividad = (item) => {
  const fecha = item.updated_at || item.created_at
  return fecha ? new Date(fecha).getTime() : 0
}

const actividadReciente = computed(() => {
  const actividades = [
    ...productos.value.map(item => ({
      key: `producto-${item.id}`,
      titulo: 'Producto registrado',
      detalle: item.nombre || 'Sin nombre',
      fecha: fechaActividad(item),
      bg: 'bg-green-50',
      iconBg: 'bg-green-100',
      icon: 'bi-box-seam',
      iconColor: 'text-green-600'
    })),
    ...categorias.value.map(item => ({
      key: `categoria-${item.id}`,
      titulo: 'Categoría registrada',
      detalle: item.nombre || 'Sin nombre',
      fecha: fechaActividad(item),
      bg: 'bg-[#47B5AC]/10',
      iconBg: 'bg-[#47B5AC]/15',
      icon: 'bi-grid',
      iconColor: 'text-[#47B5AC]'
    })),
    ...proveedores.value.map(item => ({
      key: `proveedor-${item.id}`,
      titulo: 'Proveedor registrado',
      detalle: item.nombre_proveedor || item.nombre || 'Sin nombre',
      fecha: fechaActividad(item),
      bg: 'bg-blue-50',
      iconBg: 'bg-blue-100',
      icon: 'bi-truck',
      iconColor: 'text-blue-600'
    })),
    ...usuarios.value.map(item => ({
      key: `usuario-${item.id}`,
      titulo: 'Usuario registrado',
      detalle: item.name || 'Sin nombre',
      fecha: fechaActividad(item),
      bg: 'bg-purple-50',
      iconBg: 'bg-purple-100',
      icon: 'bi-person-plus-fill',
      iconColor: 'text-purple-600'
    }))
  ]

  return actividades
    .sort((a, b) => b.fecha - a.fecha)
    .slice(0, 4)
})

const cargarDashboard = async () => {
  cargando.value = true
  error.value = ''

  const resultados = await Promise.allSettled([
    getProductos(),
    getCategorias(),
    getProveedores(),
    getUsuarios()
  ])

  productos.value = resultados[0].status === 'fulfilled' ? normalizarLista(resultados[0].value) : []
  categorias.value = resultados[1].status === 'fulfilled' ? normalizarLista(resultados[1].value) : []
  proveedores.value = resultados[2].status === 'fulfilled' ? normalizarLista(resultados[2].value) : []
  usuarios.value = resultados[3].status === 'fulfilled' ? normalizarLista(resultados[3].value) : []

  if (resultados.some(resultado => resultado.status === 'rejected')) {
    error.value = 'No se pudieron cargar todos los datos del inicio desde la base de datos.'
  }

  cargando.value = false
}

onMounted(() => {
  cargarDashboard()
})
</script>
