<template>
  <div class="min-h-screen bg-gray-50 flex">

    <div class="w-64 bg-slate-900 text-white flex flex-col justify-between hidden md:flex">
      <div class="p-6">
        <div class="flex items-center space-x-3 mb-8">
          <img :src="'/logo.png'" class="h-9 w-9 object-contain" alt="Logo" />
          <span class="font-bold text-lg tracking-tight">La Económica</span>
        </div>
        <nav class="space-y-2">
          <router-link to="/categorias" class="flex items-center space-x-3 bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-medium shadow-sm">
            <span>📁</span> <span>Categorías</span>
          </router-link>
          <router-link to="/proveedores" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-2.5 rounded-lg font-medium transition-colors">
            <span>🤝</span> <span>Proveedores</span>
          </router-link>
          <router-link to="/productos" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-2.5 rounded-lg font-medium transition-colors">
            <span>📦</span> <span>Productos</span>
          </router-link>
        </nav>
      </div>
      <div class="p-6 border-t border-slate-800">
        <router-link to="/" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-medium">
          <span>🚪</span> <span>Cerrar Sesión</span>
        </router-link>
      </div>
    </div>

    <div class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto">
      <div class="border-b border-gray-200 pb-5">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Estructura de Productos</h1>
        <p class="text-sm text-gray-500 mt-1">Organiza tus productos por departamentos y sub-grupos para facilitar las ventas.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80 space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">1. Categorías Principales</h2>
            <button class="text-xs font-semibold bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors">
              + Nueva Categoría
            </button>
          </div>
          <div class="border border-gray-100 rounded-lg overflow-hidden">
            <table class="w-full text-left text-sm text-gray-700">
              <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                  <th class="px-4 py-3">Nombre</th>
                  <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="bg-emerald-50/50">
                  <td class="px-4 py-3 font-medium text-gray-900 flex items-center justify-between">
                    Abarrotes <span class="text-xs text-gray-400 font-normal">(3 subs)</span>
                  </td>
                  <td class="px-4 py-3 text-right space-x-2">
                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                  </td>
                </tr>
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 font-medium text-gray-900 flex items-center justify-between">
                    Lácteos <span class="text-xs text-gray-400 font-normal">(3 subs)</span>
                  </td>
                  <td class="px-4 py-3 text-right space-x-2">
                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-bold text-gray-900">2. Subcategorías</h2>
              <p class="text-xs text-gray-400 mt-0.5">Viendo las subcategorías de: <span class="font-semibold text-emerald-600">Abarrotes</span></p>
            </div>
            <button class="text-xs font-semibold bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-all">
              + Nueva Subcategoría
            </button>
          </div>
          <div class="border border-gray-100 rounded-lg overflow-hidden">
            <table class="w-full text-left text-sm text-gray-700">
              <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                <tr>
                  <th class="px-4 py-3">Subcategoría</th>
                  <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 font-medium text-gray-900">Arroz y Frijoles</td>
                  <td class="px-4 py-3 text-right">
                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                  </td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 font-medium text-gray-900">Aceites</td>
                  <td class="px-4 py-3 text-right">
                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
