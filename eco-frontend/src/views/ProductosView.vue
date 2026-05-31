<template>
  <div class="min-h-screen bg-gray-50 flex">

    <div class="w-64 bg-slate-900 text-white flex flex-col justify-between hidden md:flex">
      <div class="p-6">
        <div class="flex items-center space-x-3 mb-8">
          <img :src="'../../login.png'" class="h-9 w-9 object-contain" alt="Logo" />
          <span class="font-bold text-lg tracking-tight">La Económica</span>
        </div>
        <nav class="space-y-2">
          <router-link to="/categorias" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-2.5 rounded-lg font-medium transition-colors">
            <span>📁</span> <span>Categorías</span>
          </router-link>
          <router-link to="/proveedores" class="flex items-center space-x-3 text-slate-400 hover:bg-slate-800 hover:text-white px-4 py-2.5 rounded-lg font-medium transition-colors">
            <span>🤝</span> <span>Proveedores</span>
          </router-link>
          <router-link to="/productos" class="flex items-center space-x-3 bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-medium shadow-sm">
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

    <div class="flex-1 p-6 md:p-10 space-y-6 overflow-y-auto">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-200 pb-5 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Inventario de Productos</h1>
          <p class="text-sm text-gray-500 mt-1">Control general de artículos disponibles para la venta al público.</p>
        </div>
        <button class="bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm self-start md:self-auto">
          + Registrar Producto
        </button>
      </div>

      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200/80 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <input type="text" class="w-full sm:max-w-xs px-3 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg text-sm focus:outline-none" placeholder="Buscar por código o nombre..." />
        <select class="w-full sm:w-48 px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm focus:outline-none">
          <option>Todas las categorías</option>
          <option>Abarrotes</option>
          <option>Lácteos</option>
        </select>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <table class="w-full text-left text-sm text-gray-700">
          <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
            <tr>
              <th class="px-6 py-3.5">Código / Nombre</th>
              <th class="px-6 py-3.5">Categoría</th>
              <th class="px-6 py-3.5">Precio Venta</th>
              <th class="px-6 py-3.5">Stock</th>
              <th class="px-6 py-3.5 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-slate-50/60 transition-colors">
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">Arroz Blanco Precocido 1kg</div>
                <div class="text-xs text-gray-400 font-mono">7501020304051</div>
              </td>
              <td class="px-6 py-4 text-gray-600">Abarrotes</td>
              <td class="px-6 py-4 font-medium text-gray-900">$1.25</td>
              <td class="px-6 py-4"><div class="text-gray-900 font-semibold">45 unidades</div></td>
              <td class="px-6 py-4 text-right space-x-3 font-medium text-xs">
                <button class="text-emerald-600 hover:text-emerald-800">Editar</button>
                <button class="text-red-600 hover:text-red-800">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
