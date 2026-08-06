<template>
  <div class="main-interface-container p-4 lg:p-6 font-sans text-slate-800 bg-[#F1F5F9] min-h-screen">
    <div class="w-full max-w-none space-y-6">

      <!-- NAVBAR SUPERIOR -->
      <div class="top-strict-navbar flex items-center justify-between bg-white rounded-2xl shadow-md p-4">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#47B5AC]/10 text-[#47B5AC]">
            <i class="bi bi-cart-check-fill text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-800">Módulo de Compras</h1>
            <p class="text-xs text-gray-500">Control de entradas, recepción de órdenes e inventario</p>
          </div>
        </div>
        
        <div class="top-right-actions flex items-center gap-4">
          <button 
            @click="abrirModal"
            class="bg-[#47B5AC] hover:bg-[#47B5AC] text-white px-5 py-3 rounded-xl shadow-md transition font-medium text-sm flex items-center gap-2"
          >
            <i class="bi bi-plus-lg"></i>
            Nueva Compra
          </button>
        </div>
      </div>

      <!-- DISPOSICIÓN PRINCIPAL EN COLUMNAS -->
      <div class="content-layout-flex flex flex-col xl:flex-row gap-5 items-start w-full">
        
        <!-- PANEL IZQUIERDO: TABLA -->
        <div class="left-content-panel w-full xl:flex-1 xl:min-w-0 bg-white rounded-2xl shadow-md p-6 min-h-[calc(100vh-13rem)] flex flex-col">
          <div class="section-header-row flex justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-gray-800">Órdenes de Compra</h2>
              <p class="text-sm text-gray-500">Listado general ({{ compras.length }} registros)</p>
            </div>
          </div>

          <div class="table-card-wrapper border border-gray-200 rounded-xl overflow-hidden flex-1">
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                  <tr class="text-gray-700 text-sm font-semibold">
                    <th class="px-6 py-4">ID Orden</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Total ($)</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="c in compras" :key="c.id" class="border-t border-gray-200 hover:bg-gray-50 text-sm">
                    <td class="px-6 py-4 font-mono font-bold text-gray-800">#{{ c.id }}</td>
                    <td class="px-6 py-4 text-gray-600 text-xs">{{ c.fecha_compra ?? '—' }}</td>
                    <td class="px-6 py-4">
                      <span
                        class="rounded-full px-2.5 py-1 text-xs font-semibold inline-flex items-center gap-1.5"
                        :class="{
                          'bg-amber-100 text-amber-700': c.estado === 'pendiente',
                          'bg-green-100 text-green-700': c.estado === 'completada',
                          'bg-red-100 text-red-700': c.estado === 'cancelada'
                        }"
                      >
                        <i class="bi text-xs" :class="{
                          'bi-hourglass-split': c.estado === 'pendiente',
                          'bi-check-lg': c.estado === 'completada',
                          'bi-x-circle': c.estado === 'cancelada'
                        }"></i>
                        {{ c.estado ? c.estado.toUpperCase() : 'N/A' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">${{ Number(c.total ?? 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex gap-2 justify-end">
                        <button 
                          @click="abrirCompletar(c)"
                          class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition" 
                          title="Editar / Ver Detalle"
                        >
                          <i class="bi bi-pencil"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="compras.length === 0">
                    <td colspan="5" class="text-center py-28 text-gray-400 italic">
                      <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                      No hay compras registradas.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- PANEL DERECHO: WIDGET DE MÉTRICAS -->
        <div class="right-widgets-panel w-full xl:w-[240px] 2xl:w-[260px] xl:shrink-0 flex flex-col gap-4">
          <div class="inventory-card-widget bg-white rounded-2xl shadow-md p-3 border border-gray-100 h-fit">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Resumen de Compras</h2>
            
            <div class="widget-metric-row flex justify-between items-center p-2.5 bg-gray-50 rounded-xl mb-2">
              <div>
                <span class="block text-xs text-gray-500">Total Órdenes</span>
                <span class="text-xl font-bold text-gray-800">{{ compras.length }}</span>
              </div>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-2.5 bg-amber-50 rounded-xl mb-2">
              <div>
                <span class="block text-xs text-amber-600">Órdenes Pendientes</span>
                <span class="text-xl font-bold text-amber-700">
                  {{ compras.filter(c => c.estado === 'pendiente').length }}
                </span>
              </div>
              <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                <i class="bi bi-clock-history"></i>
              </span>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-2.5 bg-green-50 rounded-xl">
              <div>
                <span class="block text-xs text-green-600">Completadas</span>
                <span class="text-xl font-bold text-green-700">
                  {{ compras.filter(c => c.estado === 'completada').length }}
                </span>
              </div>
              <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-green-100 text-green-700">
                <i class="bi bi-check2-circle"></i>
              </span>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- MODAL 1: NUEVA COMPRA (REDISEÑADO & MINIMALISTA) -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-40 p-4 lg:p-6">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Encabezado -->
        <div class="px-6 py-4 flex justify-between items-center bg-[#47B5AC]">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white">
              <i class="bi bi-bag-plus-fill text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-white text-base">Registrar Nueva Orden de Compra</h3>
              <p class="text-xs text-white/80">Agrega productos, define cantidades y sus costos de entrada</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="abrirModalProductoCompra"
              class="px-4 py-2 rounded-xl bg-white text-[#47B5AC] text-xs font-bold shadow-md hover:bg-[#DDF3F1] transition flex items-center gap-2"
            >
              <i class="bi bi-plus-lg"></i>
              Nuevo Producto
            </button>
            <button class="w-11 h-11 rounded-full bg-[#DDF3F1] text-gray-800 shadow-md hover:bg-white flex items-center justify-center transition" @click="cerrar">
              <i class="bi bi-x-lg text-xl"></i>
            </button>
          </div>
        </div>

        <!-- Contenido principal con scroll -->
        <div class="p-6 overflow-y-auto flex-1 space-y-5">
          
          <!-- Tabla minimalista de ítems -->
          <div class="border border-gray-200/80 rounded-xl overflow-hidden bg-white shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200/80 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
                  <th class="py-3 px-4 w-[45%]">Producto</th>
                  <th class="py-3 px-4 w-[18%]">Cantidad</th>
                  <th class="py-3 px-4 w-[20%]">Precio Compra ($)</th>
                  <th class="py-3 px-4 w-[12%] text-right">Subtotal</th>
                  <th class="py-3 px-2 w-[5%] text-center"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(d, i) in detalles" :key="i" class="hover:bg-slate-50/50 transition">
                  
                  <!-- Selector de Producto -->
                  <td class="p-3">
                    <button
                      type="button"
                      @click="abrirSelector(i)"
                      class="box-border w-full h-9 flex items-center justify-between gap-2 px-3 rounded-lg border border-gray-400 bg-white hover:border-[#47B5AC] hover:bg-[#47B5AC]/5 text-gray-700 transition text-left group"
                    >
                      <span class="truncate font-medium text-xs text-gray-800">
                        <i class="bi bi-box-seam mr-1.5 text-gray-400 group-hover:text-[#47B5AC]"></i>
                        {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                      </span>
                      <i class="bi bi-search text-gray-400 group-hover:text-[#47B5AC] text-xs"></i>
                    </button>
                  </td>

                  <!-- Input Cantidad -->
                  <td class="p-3">
                    <input
                      v-model.number="d.cantidad"
                      type="number"
                      min="1"
                      placeholder="1"
                      class="box-border w-full h-9 px-3 border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 bg-white outline-none focus:border-[#47B5AC] focus:ring-1 focus:ring-[#47B5AC] transition"
                    />
                  </td>

                  <!-- Input Precio Compra -->
                  <td class="p-3">
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">$</span>
                      <input
                        v-model.number="d.precio_compra"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="box-border w-full h-9 pl-6 pr-3 border border-gray-400 rounded-lg text-xs font-bold text-[#47B5AC] bg-white outline-none focus:border-[#47B5AC] focus:ring-1 focus:ring-[#47B5AC] transition"
                      />
                    </div>
                  </td>

                  <!-- Subtotal -->
                  <td class="p-3 text-right font-bold text-gray-800 text-xs">
                    ${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}
                  </td>

                  <!-- Eliminar -->
                  <td class="p-3 text-center">
                    <button 
                      @click="remove(i)" 
                      class="h-8 w-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                      title="Eliminar fila"
                    >
                      <i class="bi bi-trash text-sm"></i>
                    </button>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>

          <!-- Botón de agregar más productos -->
          <button
            @click="add"
            type="button"
            class="w-full py-2.5 rounded-xl border border-dashed border-gray-300 hover:border-[#47B5AC] text-xs font-semibold text-gray-600 hover:text-[#47B5AC] hover:bg-[#47B5AC]/5 transition flex items-center justify-center gap-2"
          >
            <i class="bi bi-plus-lg"></i> Agregar otro producto
          </button>

        </div>

        <!-- Footer / Total General & Acciones -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 font-medium">Total de la Orden:</span>
            <span class="text-xl font-black text-gray-900">${{ totalCompraNueva }}</span>
          </div>

          <div class="flex gap-3">
            <button
              @click="cerrar"
              :disabled="cargando"
              class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 bg-white hover:bg-gray-100 transition disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              @click="guardar"
              :disabled="cargando"
              class="px-6 py-2 rounded-xl bg-[#47B5AC] hover:bg-[#47B5AC] text-xs font-bold text-white shadow-md transition disabled:opacity-50 flex items-center gap-2"
            >
              <i v-if="!cargando" class="bi bi-check-lg"></i>
              {{ cargando ? 'Guardando...' : 'Guardar Compra' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO DESDE COMPRA -->
    <div v-if="modalProductoCompra" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-[14px] shadow-xl max-w-md w-full overflow-hidden">
        <div class="h-24 px-6 flex justify-between items-center bg-[#47B5AC]">
          <h3 class="font-bold text-white text-lg">Agregar Nuevo Producto</h3>
          <button type="button" class="w-11 h-11 rounded-full bg-[#DDF3F1] text-gray-800 shadow-md hover:bg-white flex items-center justify-center transition" @click="cerrarModalProductoCompra">
            <i class="bi bi-x-lg text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="guardarProductoCompra" class="p-6 flex flex-col gap-4 max-h-[calc(100vh-8rem)] overflow-y-auto overflow-x-hidden" enctype="multipart/form-data">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Imagen del Producto</label>
            <div class="flex items-center gap-4 mt-1">
              <div class="relative w-20 h-20 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center flex-shrink-0">
                <img v-if="imagenProductoPreview" :src="imagenProductoPreview" alt="Preview" class="w-full h-full object-cover" />
                <i v-else class="bi bi-image text-gray-300 text-2xl"></i>
                <button
                  v-if="imagenProductoPreview"
                  type="button"
                  @click="removerImagenProductoCompra"
                  class="absolute top-0 right-0 bg-red-500 text-white rounded-bl p-1 leading-none shadow-md hover:bg-red-600 transition"
                  title="Quitar imagen"
                >
                  <i class="bi bi-x text-xs"></i>
                </button>
              </div>

              <div class="w-full">
                <label class="box-border w-full h-[72px] flex flex-col items-center justify-center px-4 bg-white text-gray-500 rounded-xl border border-gray-300 border-dashed cursor-pointer hover:bg-gray-50 hover:text-gray-700 transition text-center">
                  <i class="bi bi-cloud-upload text-lg mb-1 text-[#47B5AC]"></i>
                  <span class="text-xs font-medium">Seleccionar imagen archivo</span>
                  <input type="file" ref="fileInputProductoCompra" accept="image/*" class="hidden" @change="manejarCambioImagenProductoCompra" />
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre del Producto</label>
            <input type="text" v-model="nuevoProductoCompra.nombre" required placeholder="Ej. MacBook Pro M3" class="box-border w-full h-8 px-4 border border-gray-900 rounded-[14px] focus:ring-2 focus:ring-[#47B5AC] outline-none" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Código de Barras / SKU</label>
            <input type="text" v-model="nuevoProductoCompra.codigo_barras" required placeholder="Ej. 7501055300075" class="box-border w-full h-8 px-4 border border-gray-900 rounded-[14px] focus:ring-2 focus:ring-[#47B5AC] outline-none" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Categoría</label>
              <button type="button" @click="abrirBuscadorProductoCompra('subcategoria')" class="box-border w-full h-9 flex justify-between items-center px-4 border border-gray-900 rounded-[14px] text-left text-sm bg-gray-50 hover:bg-gray-100 transition truncate">
                <span class="truncate">{{ nombreSubcategoriaProductoCompra || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1"></i>
              </button>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
              <button type="button" @click="abrirBuscadorProductoCompra('unidad_medida')" class="box-border w-full h-9 flex justify-between items-center px-4 border border-gray-900 rounded-[14px] text-left text-sm bg-gray-50 hover:bg-gray-100 transition truncate">
                <span class="truncate">{{ nombreUnidadProductoCompra || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-gray-400 ml-1"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Venta ($)</label>
            <input type="number" step="0.01" v-model="nuevoProductoCompra.precio_venta" required placeholder="0.00" class="box-border w-full h-8 px-4 border border-gray-900 rounded-[14px] focus:ring-2 focus:ring-[#47B5AC] outline-none" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Stock Mínimo</label>
            <input type="number" v-model="nuevoProductoCompra.stock_minimo" required class="box-border w-full h-8 px-4 border border-gray-900 rounded-[14px] focus:ring-2 focus:ring-[#47B5AC] outline-none" />
          </div>

          <div class="flex justify-end gap-3 mt-8">
            <button type="button" class="h-9 px-5 bg-gray-100 text-gray-700 rounded-xl border border-gray-400 hover:bg-gray-200" :disabled="guardandoProductoCompra" @click="cerrarModalProductoCompra">Cancelar</button>
            <button type="submit" class="h-9 px-5 bg-[#47B5AC] text-white rounded-xl border border-gray-500 hover:bg-[#47B5AC] flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="guardandoProductoCompra">
              <span v-if="guardandoProductoCompra" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
              {{ guardandoProductoCompra ? 'Procesando...' : 'Guardar Producto' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL BUSCADOR PRODUCTO DESDE COMPRA -->
    <div v-if="modalBuscadorProductoCompra" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border">
        <div class="px-5 py-4 border-b flex justify-between items-center bg-gray-50">
          <h4 class="font-bold text-gray-800 text-base">
            Buscar {{ tipoBuscadorProductoCompra === 'subcategoria' ? 'Categoría' : 'Unidad de Medida' }}
          </h4>
          <button type="button" class="text-gray-400 hover:text-gray-600" @click="cerrarBuscadorProductoCompra"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="p-4 flex flex-col gap-3">
          <div class="relative flex items-center">
            <i class="bi bi-search absolute left-3 text-gray-400 text-sm"></i>
            <input
              v-model="filtroBuscadorProductoCompra"
              type="text"
              placeholder="Filtrar por nombre..."
              class="w-full pl-9 pr-4 py-2 border text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-[#47B5AC]"
            />
          </div>

          <div v-if="tipoBuscadorProductoCompra === 'subcategoria'" class="max-h-[300px] overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 flex flex-col divide-y">
            <div
              v-for="cat in categoriasFiltradasBuscadorProductoCompra"
              :key="cat.id"
              class="bg-white"
            >
              <button
                type="button"
                @click="cat.subcategoriasFiltradas.length === 0 ? seleccionarCategoriaBuscadorProductoCompra(cat) : alternarCategoriaBuscadorProductoCompra(cat.id)"
                class="w-full text-left px-4 py-3 text-sm text-gray-800 hover:text-[#47B5AC] hover:bg-[#47B5AC]/5 font-bold transition flex justify-between items-center"
              >
                <span class="truncate">
                  <i class="bi bi-folder-fill mr-2 text-[#47B5AC]"></i>
                  {{ cat.nombre }}
                </span>
                <i
                  v-if="cat.subcategoriasFiltradas.length > 0"
                  :class="categoriaBuscadorProductoCompraAbierta === cat.id ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"
                  class="text-[#47B5AC]"
                ></i>
                <span v-else class="text-[10px] bg-[#47B5AC]/10 text-[#47B5AC] px-2 py-0.5 rounded-md font-bold">
                  Usar categoría
                </span>
              </button>

              <div v-if="categoriaBuscadorProductoCompraAbierta === cat.id" class="bg-gray-50 border-t border-gray-100">
                <button
                  type="button"
                  v-for="sub in cat.subcategoriasFiltradas"
                  :key="sub.id"
                  @click="seleccionarItemBuscadorProductoCompra(sub)"
                  class="w-full text-left pl-10 pr-4 py-2.5 text-sm text-gray-700 hover:bg-white hover:text-[#47B5AC] font-medium transition flex justify-between items-center"
                >
                  <span class="truncate">{{ sub.nombre }}</span>
                  <span class="text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded-md font-mono">ID: {{ sub.id }}</span>
                </button>

                <div v-if="cat.subcategoriasFiltradas.length === 0" class="pl-10 pr-4 py-3 text-xs text-gray-400 italic">
                  Sin subcategorías.
                </div>
              </div>
            </div>

            <div v-if="categoriasFiltradasBuscadorProductoCompra.length === 0" class="text-center py-6 text-xs text-gray-400 italic">
              No hay coincidencias.
            </div>
          </div>

          <div v-else class="max-h-[250px] overflow-y-auto border border-gray-100 rounded-xl bg-gray-50 flex flex-col divide-y">
            <button
              type="button"
              v-for="item in listaFiltradaBuscadorProductoCompra"
              :key="item.id"
              @click="seleccionarItemBuscadorProductoCompra(item)"
              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-white hover:text-[#47B5AC] font-medium transition flex justify-between items-center"
            >
              <span class="truncate">{{ item.nombre }}</span>
              <span class="text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded-md font-mono">ID: {{ item.id }}</span>
            </button>
            <div v-if="listaFiltradaBuscadorProductoCompra.length === 0" class="text-center py-6 text-xs text-gray-400 italic">
              No hay coincidencias.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 2: SELECTOR DE PRODUCTOS -->
    <div v-if="modalProductos" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-sm">Catálogo de Productos</h3>
          <button class="text-gray-400 hover:text-gray-600" @click="modalProductos = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-4 bg-white">
          <div class="relative mb-3">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              v-model="busqueda"
              class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#47B5AC]/20"
              placeholder="Escribe para buscar producto..."
            />
          </div>

          <div class="max-h-[260px] overflow-y-auto space-y-2 pr-1">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              @click="seleccionarProducto(p)"
              class="p-3 rounded-xl border border-gray-200 bg-white cursor-pointer hover:border-[#47B5AC] hover:bg-[#47B5AC]/5 transition flex justify-between items-center"
            >
              <div>
                <p class="font-bold text-gray-800 text-xs">{{ p.nombre }}</p>
                <p class="text-[11px] text-gray-500">Unidad: {{ p.unidad_medida?.nombre || p.unidad_medida || 'pieza' }}</p>
              </div>
              <span class="text-[11px] bg-gray-100 text-gray-700 font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                Stock: {{ p.stock ?? 0 }}
              </span>
            </div>

            <p v-if="productosFiltrados.length === 0" class="text-center text-gray-400 py-6 text-xs italic">
              No se encontraron coincidencias
            </p>
          </div>

          <div class="flex justify-end mt-4 pt-3 border-t border-gray-200">
            <button @click="modalProductos = false" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 3: DETALLE Y COMPLETAR -->
    <div v-if="modalCompletar" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-40 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
          <h3 class="font-bold text-gray-800 text-lg">Detalle de Compra #{{ compraSeleccionada?.id }}</h3>
          <button class="text-gray-400 hover:text-gray-600" @click="modalCompletar = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-6 bg-white">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="p-3 border border-gray-200 rounded-xl bg-gray-50">
              <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">
                Fecha Recepción
              </label>
              <input
                v-model="fechaLlegada"
                type="date"
                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white outline-none focus:ring-2 focus:ring-[#47B5AC]/20"
                :disabled="!esPendiente || cargando"
              />
            </div>

            <div class="p-3 border border-gray-200 rounded-xl bg-gray-50 flex flex-col justify-center">
              <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Monto Total</span>
              <span class="text-xl font-black text-gray-900">
                ${{ Number(compraSeleccionada?.total ?? 0).toFixed(2) }}
              </span>
            </div>
          </div>

          <!-- TABLA DE DETALLES Y LOTES -->
          <div class="max-h-[240px] overflow-y-auto border border-gray-200 rounded-xl bg-white mb-4">
            <table class="w-full text-left text-xs">
              <thead class="bg-gray-100 border-b border-gray-200 text-gray-700 font-semibold uppercase">
                <tr>
                  <th class="p-3">Producto</th>
                  <th class="p-3">Cant.</th>
                  <th class="p-3">Precio</th>
                  <th class="p-3">Lote / Vencimiento</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(d, i) in lotes" :key="i">
                  <td class="p-3 font-bold text-gray-900">{{ d.producto_nombre }}</td>
                  <td class="p-3 text-gray-700">{{ d.cantidad }}</td>
                  <td class="p-3 text-gray-700">${{ Number(d.precio).toFixed(2) }}</td>
                  <td class="p-3">
                    <div class="flex flex-col gap-1.5">
                      <input
                        v-model="d.codigo_lote"
                        class="w-full p-1.5 border border-gray-300 rounded-lg text-[11px] bg-white outline-none focus:ring-2 focus:ring-[#47B5AC]/20"
                        placeholder="Cód. Lote"
                        :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                      />
                      <input
                        v-model="d.fecha_expiracion"
                        type="date"
                        class="w-full p-1.5 border border-gray-300 rounded-lg text-[11px] bg-white outline-none focus:ring-2 focus:ring-[#47B5AC]/20"
                        :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- BOTONES -->
          <div class="flex justify-between items-center border-t border-gray-200 pt-4">
            <button
              v-if="esPendiente"
              @click="cancelarCompra"
              :disabled="cargando"
              class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition"
            >
              Anular
            </button>
            <div v-else></div>

            <div class="flex gap-2">
              <button @click="modalCompletar = false" :disabled="cargando" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50">
                Cerrar
              </button>

              <button
                v-if="esPendiente"
                :disabled="cargando"
                @click="completarCompra"
                class="px-5 py-2 bg-[#47B5AC] hover:bg-[#47B5AC] text-white rounded-xl text-xs font-bold shadow-md transition disabled:opacity-50"
              >
                {{ cargando ? 'Procesando...' : 'Completar y Cargar Inventario' }}
              </button>

              <button
                v-else-if="compraSeleccionada?.estado !== 'cancelada'"
                :disabled="cargando"
                @click="guardarCambios"
                class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition"
              >
                {{ cargando ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { getCompras, createCompra, updateCompra } from '../services/compraService'
import { getProductos, getAuxiliares, guardarProductoAPI, subirImagenAPI } from '../services/productoService'

const compras = ref([])
const productos = ref([])
const categorias = ref([])
const subcategorias = ref([])
const unidadesMedida = ref([])

const modal = ref(false)
const modalProductos = ref(false)
const modalCompletar = ref(false)
const modalProductoCompra = ref(false)
const modalBuscadorProductoCompra = ref(false)
const cargando = ref(false)
const guardandoProductoCompra = ref(false)

const detalles = ref([])
const lotes = ref([])

const compraSeleccionada = ref(null)
const fechaLlegada = ref('')
const indexProducto = ref(null)
const busqueda = ref('')
const fileInputProductoCompra = ref(null)
const imagenProductoSeleccionada = ref(null)
const imagenProductoPreview = ref(null)
const tipoBuscadorProductoCompra = ref('')
const filtroBuscadorProductoCompra = ref('')
const categoriaBuscadorProductoCompraAbierta = ref(null)
const nombreSubcategoriaProductoCompra = ref('')
const nombreUnidadProductoCompra = ref('')

const modeloProductoCompraLimpio = () => ({
  codigo_barras: '',
  nombre: '',
  categoria_id: '',
  sub_categoria_id: '',
  unidad_medida_id: '',
  stock: 0,
  stock_minimo: 5,
  precio_venta: 0
})

const nuevoProductoCompra = ref(modeloProductoCompraLimpio())

const cargar = async () => {
  try {
    const res = await getCompras()
    const dataExtraida = res.data?.data || res.data || res
    compras.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar compras:', err)
    compras.value = []
  }
}

const cargarProductos = async () => {
  try {
    const res = await getProductos()
    const dataExtraida = res.data?.data || res.data || res
    productos.value = Array.isArray(dataExtraida) ? dataExtraida : []
  } catch (err) {
    console.error('Error al cargar productos:', err)
    productos.value = []
  }
}

const cargarAuxiliaresProductoCompra = async () => {
  try {
    const [resCat, resSub, resUnidades] = await getAuxiliares()
    const dataCat = resCat.data?.data || resCat.data || []
    const dataSub = resSub.data?.data || resSub.data || []
    const dataUnidades = resUnidades.data?.data || resUnidades.data || []
    categorias.value = Array.isArray(dataCat) ? dataCat : []
    subcategorias.value = Array.isArray(dataSub) ? dataSub : []
    unidadesMedida.value = Array.isArray(dataUnidades) ? dataUnidades : []
  } catch (err) {
    console.error('Error cargando catálogos de producto:', err)
    categorias.value = []
    subcategorias.value = []
    unidadesMedida.value = []
  }
}

onMounted(() => {
  cargar()
  cargarProductos()
  cargarAuxiliaresProductoCompra()
})

// MODAL NUEVA COMPRA
function abrirModal() {
  modal.value = true
  detalles.value = [{ producto_id: '', cantidad: 1, precio_compra: null }]
}

function add() {
  detalles.value.push({ producto_id: '', cantidad: 1, precio_compra: null })
}

function remove(i) {
  detalles.value.splice(i, 1)
}

function cerrar() {
  modal.value = false
}

// Calculo dinámico del Total General de la Nueva Compra
const totalCompraNueva = computed(() => {
  return detalles.value.reduce((acc, d) => {
    return acc + ((d.cantidad || 0) * (d.precio_compra || 0))
  }, 0).toFixed(2)
})

// SELECTOR DE PRODUCTOS
function abrirSelector(i) {
  indexProducto.value = i
  modalProductos.value = true
}

function seleccionarProducto(p) {
  detalles.value[indexProducto.value].producto_id = p.id
  detalles.value[indexProducto.value].precio_compra = obtenerUltimoPrecioCompra(p.id)
  modalProductos.value = false
}

function obtenerUltimoPrecioCompra(productoId) {
  for (const compra of compras.value) {
    const detalle = (compra.detalles || []).find(d => String(d.producto_id) === String(productoId))
    const precio = Number(detalle?.precio_compra)

    if (detalle && !Number.isNaN(precio)) {
      return precio
    }
  }

  return null
}

const productosFiltrados = computed(() => {
  if (!Array.isArray(productos.value)) return []
  const query = busqueda.value.toLowerCase().trim()
  return productos.value.filter(p =>
    (p.nombre || '').toLowerCase().includes(query)
  )
})

const listaFiltradaBuscadorProductoCompra = computed(() => {
  const query = filtroBuscadorProductoCompra.value.toLowerCase().trim()
  if (tipoBuscadorProductoCompra.value === 'unidad_medida') {
    return unidadesMedida.value.filter(item => (item.nombre || '').toLowerCase().includes(query))
  }
  return []
})

const categoriasFiltradasBuscadorProductoCompra = computed(() => {
  const query = filtroBuscadorProductoCompra.value.toLowerCase().trim()

  return categorias.value
    .map(cat => {
      const subcategoriasCategoria = Array.isArray(cat.subcategorias)
        ? cat.subcategorias
        : subcategorias.value.filter(sub => String(sub.categoria_id) === String(cat.id))

      const categoriaCoincide = (cat.nombre || '').toLowerCase().includes(query)
      const subcategoriasFiltradas = query && !categoriaCoincide
        ? subcategoriasCategoria.filter(sub => (sub.nombre || '').toLowerCase().includes(query))
        : subcategoriasCategoria

      return {
        ...cat,
        subcategoriasFiltradas
      }
    })
    .filter(cat => {
      if (!query) return true
      return (cat.nombre || '').toLowerCase().includes(query) || cat.subcategoriasFiltradas.length > 0
    })
})

function getProductoNombre(id) {
  if (!Array.isArray(productos.value)) return ''
  const prod = productos.value.find(p => String(p.id) === String(id))
  return prod ? prod.nombre : ''
}

function abrirModalProductoCompra() {
  nuevoProductoCompra.value = modeloProductoCompraLimpio()
  nombreSubcategoriaProductoCompra.value = ''
  nombreUnidadProductoCompra.value = ''
  removerImagenProductoCompra()
  modalProductoCompra.value = true
}

function cerrarModalProductoCompra() {
  modalProductoCompra.value = false
  modalBuscadorProductoCompra.value = false
  tipoBuscadorProductoCompra.value = ''
  filtroBuscadorProductoCompra.value = ''
  categoriaBuscadorProductoCompraAbierta.value = null
  nombreSubcategoriaProductoCompra.value = ''
  nombreUnidadProductoCompra.value = ''
  removerImagenProductoCompra()
  nuevoProductoCompra.value = modeloProductoCompraLimpio()
}

function manejarCambioImagenProductoCompra(e) {
  const file = e.target.files[0]
  if (file) {
    imagenProductoSeleccionada.value = file
    imagenProductoPreview.value = URL.createObjectURL(file)
  }
}

function removerImagenProductoCompra() {
  imagenProductoSeleccionada.value = null
  imagenProductoPreview.value = null
  if (fileInputProductoCompra.value) fileInputProductoCompra.value.value = ''
}

function abrirBuscadorProductoCompra(tipo) {
  tipoBuscadorProductoCompra.value = tipo
  filtroBuscadorProductoCompra.value = ''
  categoriaBuscadorProductoCompraAbierta.value = null
  modalBuscadorProductoCompra.value = true
}

function cerrarBuscadorProductoCompra() {
  modalBuscadorProductoCompra.value = false
  tipoBuscadorProductoCompra.value = ''
  categoriaBuscadorProductoCompraAbierta.value = null
}

function alternarCategoriaBuscadorProductoCompra(categoriaId) {
  categoriaBuscadorProductoCompraAbierta.value = categoriaBuscadorProductoCompraAbierta.value === categoriaId ? null : categoriaId
}

function seleccionarCategoriaBuscadorProductoCompra(categoria) {
  nuevoProductoCompra.value.categoria_id = categoria.id
  nuevoProductoCompra.value.sub_categoria_id = ''
  nombreSubcategoriaProductoCompra.value = categoria.nombre
  cerrarBuscadorProductoCompra()
}

function seleccionarItemBuscadorProductoCompra(item) {
  if (tipoBuscadorProductoCompra.value === 'subcategoria') {
    nuevoProductoCompra.value.categoria_id = item.categoria_id || item.categoria?.id || ''
    nuevoProductoCompra.value.sub_categoria_id = item.id
    nombreSubcategoriaProductoCompra.value = item.nombre
  } else if (tipoBuscadorProductoCompra.value === 'unidad_medida') {
    nuevoProductoCompra.value.unidad_medida_id = item.id
    nombreUnidadProductoCompra.value = item.nombre
  }

  cerrarBuscadorProductoCompra()
}

async function guardarProductoCompra() {
  if (guardandoProductoCompra.value) return

  if ((!nuevoProductoCompra.value.sub_categoria_id && !nuevoProductoCompra.value.categoria_id) || !nuevoProductoCompra.value.unidad_medida_id) {
    alert('Por favor selecciona Categoría y Unidad de Medida válidos.')
    return
  }

  guardandoProductoCompra.value = true

  try {
    const res = await guardarProductoAPI(nuevoProductoCompra.value)
    const productoId = res.data?.data?.id || res.data?.id

    if (imagenProductoSeleccionada.value && productoId) {
      const formDataImagen = new FormData()
      formDataImagen.append('imagen', imagenProductoSeleccionada.value)
      formDataImagen.append('producto_id', productoId)
      await subirImagenAPI(formDataImagen)
    }

    await cargarProductos()
    cerrarModalProductoCompra()
  } catch (error) {
    console.error('Error al guardar producto desde compra:', error)
    alert('Ocurrió un error al guardar el producto.')
  } finally {
    guardandoProductoCompra.value = false
  }
}

// GUARDAR NUEVA COMPRA
async function guardar() {
  if (detalles.value.length === 0) return alert('Debes agregar al menos un producto')
  
  const incomp = detalles.value.some(d => !d.producto_id || !d.cantidad || d.precio_compra === null)
  if (incomp) return alert('Por favor llena el producto, cantidad y precio de compra de cada fila.')

  if (cargando.value) return
  cargando.value = true

  try {
    await createCompra({
      estado: 'pendiente',
      detalles: detalles.value
    })
    await cargar()
    cerrar()
  } catch (error) {
    alert('Error al crear la compra.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

// EDITAR / COMPLETAR
function abrirCompletar(c) {
  compraSeleccionada.value = c

  if (c.fecha_llegada) {
    fechaLlegada.value = c.fecha_llegada.split(' ')[0]
  } else {
    fechaLlegada.value = new Date().toISOString().split('T')[0]
  }

  lotes.value = (c.detalles || []).map(d => {
    const prodLocal = Array.isArray(productos.value) ? productos.value.find(p => String(p.id) === String(d.producto_id)) : null
    const loteExistente = d.lotes && d.lotes.length > 0 ? d.lotes[0] : null

    return {
      detalle_id: d.id,
      producto_id: d.producto_id,
      producto_nombre: d.producto?.nombre || prodLocal?.nombre || 'Producto Desconocido',
      cantidad: d.cantidad,
      precio: d.precio_compra ?? (d.subtotal / d.cantidad),
      codigo_lote: loteExistente ? loteExistente.codigo_lote : '',
      fecha_expiracion: loteExistente ? loteExistente.fecha_expiracion : ''
    }
  })

  modalCompletar.value = true
}

const esPendiente = computed(() => compraSeleccionada.value?.estado === 'pendiente')

function mapearDetallesParaBackend() {
  return lotes.value.map(l => ({
    detalle_id: l.detalle_id,
    codigo_lote: l.codigo_lote || null,
    fecha_expiracion: l.fecha_expiracion || null
  }))
}

async function completarCompra() {
  if (!confirm('¿Finalizar compra y cargar las unidades al inventario?')) return
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: 'completada',
      fecha_llegada: fechaLlegada.value,
      detalles: mapearDetallesParaBackend()
    })
    await cargar()
    await cargarProductos()
    modalCompletar.value = false
  } catch (error) {
    alert('Error al procesar la compra.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

async function guardarCambios() {
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: compraSeleccionada.value.estado,
      fecha_llegada: fechaLlegada.value,
      detalles: mapearDetallesParaBackend()
    })
    await cargar()
    modalCompletar.value = false
  } catch (error) {
    alert('Error al guardar cambios.')
    console.error(error)
  } finally {
    cargando.value = false
  }
}

async function cancelarCompra() {
  if (!confirm('¿Seguro que deseas cancelar esta compra?')) return
  if (cargando.value) return

  cargando.value = true
  try {
    await updateCompra(compraSeleccionada.value.id, {
      estado: 'cancelada'
    })
    await cargar()
    modalCompletar.value = false
  } catch (error) {
    console.error(error)
  } finally {
    cargando.value = false
  }
}
</script>
