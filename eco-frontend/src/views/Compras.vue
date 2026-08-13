<template>
  <div class="main-interface-container p-4 lg:p-6 font-sans text-slate-800 bg-[#F1F5F9] min-h-screen">
    <div class="w-full max-w-none space-y-6">

      <!-- NAVBAR SUPERIOR -->
      <div class="top-strict-navbar flex items-center justify-between bg-white rounded-2xl shadow-md p-4 border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#47B5AC]/10 text-[#47B5AC]">
            <i class="bi bi-cart-check-fill text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-800">Módulo de Compras</h1>
            <p class="text-xs text-slate-500">Control de entradas, recepción de órdenes e inventario</p>
          </div>
        </div>
        
        <div class="top-right-actions flex items-center gap-4">
          <button 
            @click="abrirModal"
            class="bg-[#47B5AC] hover:bg-[#3ca098] text-white px-5 py-3 rounded-xl shadow-md transition font-medium text-sm flex items-center gap-2"
          >
            <i class="bi bi-plus-lg"></i>
            Nueva Compra
          </button>
        </div>
      </div>

      <!-- DISPOSICIÓN PRINCIPAL EN COLUMNAS -->
      <div class="content-layout-flex flex flex-col xl:flex-row gap-5 items-start w-full">
        
        <!-- PANEL IZQUIERDO: TABLA -->
        <div class="left-content-panel w-full xl:flex-1 xl:min-w-0 bg-white rounded-2xl shadow-md p-6 min-h-[calc(100vh-13rem)] flex flex-col border border-slate-100 overflow-hidden">
          <div class="section-header-row flex justify-between items-center mb-6">
            <div class="title-block">
              <h2 class="text-xl font-bold text-slate-800">Órdenes de Compra</h2>
              <p class="text-sm text-slate-500">Listado general ({{ compras.length }} registros)</p>
            </div>
          </div>

          <!-- WRAPPER DE LA TABLA CON OVERFLOW-HIDDEN -->
          <div class="table-card-wrapper border border-slate-200 rounded-xl overflow-hidden flex-1 bg-white">
            <div class="overflow-x-auto w-full">
              <table class="w-full text-left border-separate border-spacing-0">
                <thead class="bg-slate-100/70">
                  <tr class="text-slate-700 text-xs uppercase tracking-wider font-bold">
                    <th class="px-6 py-4 border-b border-slate-200">ID Orden</th>
                    <th class="px-6 py-4 border-b border-slate-200">Fecha</th>
                    <th class="px-6 py-4 border-b border-slate-200">Estado</th>
                    <th class="px-6 py-4 border-b border-slate-200">Total ($)</th>
                    <th class="px-6 py-4 border-b border-slate-200 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                  <tr v-for="c in compras" :key="c.id" class="hover:bg-slate-50/80 text-sm transition">
                    <td class="px-6 py-4 font-mono font-bold text-slate-800 border-b border-slate-100">#{{ c.id }}</td>
                    <td class="px-6 py-4 text-slate-600 text-xs border-b border-slate-100">{{ c.fecha_compra ?? '—' }}</td>
                    <td class="px-6 py-4 border-b border-slate-100">
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
                    <td class="px-6 py-4 font-bold text-slate-900 border-b border-slate-100">${{ Number(c.total ?? 0).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-right border-b border-slate-100">
                      <div class="flex gap-1.5 justify-end items-center">
                        <button 
                          @click="abrirCompletar(c)"
                          class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition shadow-xs shrink-0" 
                          title="Editar / Ver Detalle"
                        >
                          <i class="bi bi-pencil-fill text-xs text-slate-600"></i>
                        </button>
                        <button 
                          @click="eliminarCompra(c.id)"
                          class="w-8 h-8 inline-flex items-center justify-center rounded-xl border border-red-100 bg-red-50/50 text-red-500 hover:bg-red-100 hover:border-red-200 transition shadow-xs shrink-0" 
                          title="Eliminar"
                        >
                          <i class="bi bi-trash-fill text-xs text-red-500"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="compras.length === 0">
                    <td colspan="5" class="text-center py-28 text-slate-400 italic">
                      <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
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
          <div class="inventory-card-widget bg-white rounded-2xl shadow-md p-4 border border-slate-100 h-fit overflow-hidden">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Resumen de Compras</h2>
            
            <div class="widget-metric-row flex justify-between items-center p-3 bg-slate-50 rounded-xl mb-2.5">
              <div>
                <span class="block text-xs text-slate-500">Total Órdenes</span>
                <span class="text-xl font-bold text-slate-800">{{ compras.length }}</span>
              </div>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-3 bg-amber-50/60 rounded-xl mb-2.5 border border-amber-100/50">
              <div>
                <span class="block text-xs text-amber-600 font-medium">Órdenes Pendientes</span>
                <span class="text-xl font-bold text-amber-700">
                  {{ compras.filter(c => c.estado === 'pendiente').length }}
                </span>
              </div>
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700 shrink-0">
                <i class="bi bi-clock-history"></i>
              </span>
            </div>

            <div class="widget-metric-row flex justify-between items-center p-3 bg-emerald-50/60 rounded-xl border border-emerald-100/50">
              <div>
                <span class="block text-xs text-emerald-600 font-medium">Completadas</span>
                <span class="text-xl font-bold text-emerald-700">
                  {{ compras.filter(c => c.estado === 'completada').length }}
                </span>
              </div>
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 shrink-0">
                <i class="bi bi-check2-circle"></i>
              </span>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- MODAL 1: NUEVA COMPRA -->
    <div v-if="modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-40 p-4 lg:p-6">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh] border border-slate-100">
        
        <div class="px-6 py-4 flex justify-between items-center bg-[#2d3a4b] text-white shrink-0 overflow-hidden">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-700/60 text-emerald-400 shrink-0">
              <i class="bi bi-bag-plus-fill text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-white text-base">Registrar Nueva Orden de Compra</h3>
              <p class="text-xs text-slate-300">Agrega productos, define cantidades y sus costos de entrada</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="abrirModalProductoCompra"
              class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm shrink-0"
            >
              <i class="bi bi-plus-lg"></i>
              Nuevo Producto
            </button>
            <button class="w-8 h-8 rounded-full bg-slate-700/50 text-slate-300 hover:bg-slate-700 hover:text-white flex items-center justify-center transition shrink-0" @click="cerrar">
              <i class="bi bi-x-lg text-sm"></i>
            </button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto flex-1 space-y-5">
          
          <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-xs">
            <div class="overflow-x-auto w-full">
              <table class="w-full text-left text-xs border-separate border-spacing-0">
                <thead>
                  <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                    <th class="py-3 px-4 w-[45%] border-b border-slate-200">Producto</th>
                    <th class="py-3 px-4 w-[18%] border-b border-slate-200">Cantidad</th>
                    <th class="py-3 px-4 w-[20%] border-b border-slate-200">Precio Compra ($)</th>
                    <th class="py-3 px-4 w-[12%] text-right border-b border-slate-200">Subtotal</th>
                    <th class="py-3 px-2 w-[5%] text-center border-b border-slate-200"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(d, i) in detalles" :key="i" class="hover:bg-slate-50/50 transition">
                    <td class="p-3 border-b border-slate-100">
                      <button
                        type="button"
                        @click="abrirSelector(i)"
                        class="box-border w-full h-9 flex items-center justify-between gap-2 px-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-slate-400 hover:bg-white text-slate-700 transition text-left group overflow-hidden"
                      >
                        <span class="truncate font-medium text-xs text-slate-800">
                          <i class="bi bi-box-seam mr-1.5 text-slate-400 group-hover:text-slate-600"></i>
                          {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                        </span>
                        <i class="bi bi-search text-slate-400 text-xs shrink-0"></i>
                      </button>
                    </td>

                    <td class="p-3 border-b border-slate-100">
                      <input
                        v-model.number="d.cantidad"
                        type="number"
                        min="1"
                        placeholder="1"
                        class="box-border w-full h-9 px-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 bg-slate-50/50 outline-none focus:bg-white focus:border-slate-400 transition"
                      />
                    </td>

                    <td class="p-3 border-b border-slate-100">
                      <div class="relative w-full">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-medium">$</span>
                        <input
                          v-model.number="d.precio_compra"
                          type="number"
                          step="0.01"
                          min="0"
                          placeholder="0.00"
                          class="box-border w-full h-9 pl-6 pr-3 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 bg-slate-50/50 outline-none focus:bg-white focus:border-slate-400 transition"
                        />
                      </div>
                    </td>

                    <td class="p-3 text-right font-bold text-slate-800 text-xs border-b border-slate-100">
                      ${{ ((d.cantidad || 0) * (d.precio_compra || 0)).toFixed(2) }}
                    </td>

                    <td class="p-3 text-center border-b border-slate-100">
                      <button 
                        @click="remove(i)" 
                        class="h-8 w-8 inline-flex items-center justify-center rounded-xl border border-red-100 bg-red-50/50 text-red-500 hover:bg-red-100 hover:border-red-200 transition shrink-0"
                        title="Eliminar fila"
                      >
                        <i class="bi bi-trash-fill text-xs"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <button
            @click="add"
            type="button"
            class="box-border w-full py-2.5 rounded-xl border border-dashed border-slate-300 hover:border-slate-400 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition flex items-center justify-center gap-2"
          >
            <i class="bi bi-plus-lg"></i> Agregar otro producto
          </button>

        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0 overflow-hidden">
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total de la Orden:</span>
            <span class="text-xl font-black text-slate-900">${{ totalCompraNueva }}</span>
          </div>

          <div class="flex gap-3">
            <button
              @click="cerrar"
              :disabled="cargando"
              class="px-5 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 transition disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              @click="guardar"
              :disabled="cargando"
              class="px-6 py-2.5 rounded-xl bg-[#2d3a4b] hover:bg-slate-800 text-xs font-bold text-white shadow-md transition disabled:opacity-50 flex items-center gap-2"
            >
              <i v-if="!cargando" class="bi bi-check-lg"></i>
              {{ cargando ? 'Guardando...' : 'Guardar Compra' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- MODAL: NUEVO PRODUCTO DESDE COMPRA / NUEVA CATEGORÍA -->
    <div v-if="modalProductoCompra" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100">
        <div class="px-6 py-4 flex justify-between items-center bg-[#2d3a4b] text-white overflow-hidden">
          <div class="flex items-center gap-2.5">
            <i class="bi bi-box-seam-fill text-sky-400 text-lg"></i>
            <div>
              <h3 class="font-bold text-white text-base">Agregar Nuevo Producto</h3>
              <p class="text-xs text-slate-300">Gestión e inventario inicial</p>
            </div>
          </div>
          <button type="button" class="w-8 h-8 rounded-full bg-slate-700/50 text-slate-300 hover:bg-slate-700 hover:text-white flex items-center justify-center transition shrink-0" @click="cerrarModalProductoCompra">
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <form @submit.prevent="guardarProductoCompra" class="p-6 flex flex-col gap-4 max-h-[calc(100vh-8rem)] overflow-y-auto" enctype="multipart/form-data">
          <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Imagen del Producto</label>
            <div class="flex items-center gap-4">
              <div class="relative w-20 h-20 rounded-xl overflow-hidden bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0">
                <img v-if="imagenProductoPreview" :src="imagenProductoPreview" alt="Preview" class="w-full h-full object-cover" />
                <i v-else class="bi bi-image text-slate-300 text-2xl"></i>
                <button
                  v-if="imagenProductoPreview"
                  type="button"
                  @click="removerImagenProductoCompra"
                  class="absolute top-0 right-0 bg-red-500 text-white rounded-bl p-1 leading-none hover:bg-red-600 transition"
                  title="Quitar imagen"
                >
                  <i class="bi bi-x text-xs"></i>
                </button>
              </div>

              <div class="w-full">
                <label class="box-border w-full h-[72px] flex flex-col items-center justify-center px-4 bg-slate-50/50 text-slate-500 rounded-xl border border-slate-200 border-dashed cursor-pointer hover:bg-slate-100/60 transition text-center overflow-hidden">
                  <i class="bi bi-cloud-upload text-lg mb-1 text-slate-600"></i>
                  <span class="text-xs font-medium">Seleccionar imagen</span>
                  <input type="file" ref="fileInputProductoCompra" accept="image/*" class="hidden" @change="manejarCambioImagenProductoCompra" />
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nombre del Producto</label>
            <input type="text" v-model="nuevoProductoCompra.nombre" required placeholder="Ej. MacBook Pro M3" class="box-border w-full h-10 px-4 border border-slate-200 rounded-xl bg-slate-50/50 text-sm focus:bg-white focus:border-slate-400 outline-none transition" />
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Código de Barras / SKU</label>
            <input type="text" v-model="nuevoProductoCompra.codigo_barras" required placeholder="Ej. 7501055300075" class="box-border w-full h-10 px-4 border border-slate-200 rounded-xl bg-slate-50/50 text-sm focus:bg-white focus:border-slate-400 outline-none transition" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Categoría</label>
              <button type="button" @click="abrirBuscadorProductoCompra('subcategoria')" class="box-border w-full h-10 flex justify-between items-center px-4 border border-slate-200 rounded-xl text-left text-xs bg-slate-50/50 hover:bg-slate-100 transition truncate text-slate-700 font-medium overflow-hidden">
                <span class="truncate">{{ nombreSubcategoriaProductoCompra || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-slate-400 ml-1 shrink-0"></i>
              </button>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Unidad de Medida</label>
              <button type="button" @click="abrirBuscadorProductoCompra('unidad_medida')" class="box-border w-full h-10 flex justify-between items-center px-4 border border-slate-200 rounded-xl text-left text-xs bg-slate-50/50 hover:bg-slate-100 transition truncate text-slate-700 font-medium overflow-hidden">
                <span class="truncate">{{ nombreUnidadProductoCompra || 'Seleccionar...' }}</span>
                <i class="bi bi-search text-slate-400 ml-1 shrink-0"></i>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Precio Venta ($)</label>
            <input type="number" step="0.01" v-model="nuevoProductoCompra.precio_venta" required placeholder="0.00" class="box-border w-full h-10 px-4 border border-slate-200 rounded-xl bg-slate-50/50 text-sm focus:bg-white focus:border-slate-400 outline-none transition" />
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Stock Mínimo</label>
            <input type="number" v-model="nuevoProductoCompra.stock_minimo" required class="box-border w-full h-10 px-4 border border-slate-200 rounded-xl bg-slate-50/50 text-sm focus:bg-white focus:border-slate-400 outline-none transition" />
          </div>

          <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-slate-100">
            <button type="button" class="px-5 py-2.5 bg-white text-slate-600 font-bold rounded-xl border border-slate-200 hover:bg-slate-100 text-xs transition" :disabled="guardandoProductoCompra" @click="cerrarModalProductoCompra">Cancelar</button>
            <button type="submit" class="px-6 py-2.5 bg-[#2d3a4b] text-white font-bold rounded-xl hover:bg-slate-800 text-xs transition flex items-center gap-2 disabled:opacity-50" :disabled="guardandoProductoCompra">
              <span v-if="guardandoProductoCompra" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
              {{ guardandoProductoCompra ? 'Procesando...' : 'Guardar Producto' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL BUSCADOR PRODUCTO DESDE COMPRA -->
    <div v-if="modalBuscadorProductoCompra" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[60] p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 overflow-hidden">
          <h4 class="font-bold text-slate-800 text-sm">
            Buscar {{ tipoBuscadorProductoCompra === 'subcategoria' ? 'Categoría' : 'Unidad de Medida' }}
          </h4>
          <button type="button" class="text-slate-400 hover:text-slate-600 shrink-0" @click="cerrarBuscadorProductoCompra"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="p-4 flex flex-col gap-3">
          <div class="relative flex items-center w-full">
            <i class="bi bi-search absolute left-3 text-slate-400 text-sm"></i>
            <input
              v-model="filtroBuscadorProductoCompra"
              type="text"
              placeholder="Filtrar por nombre..."
              class="box-border w-full pl-9 pr-4 py-2 border border-slate-200 text-xs rounded-xl focus:outline-none focus:border-slate-400"
            />
          </div>

          <div v-if="tipoBuscadorProductoCompra === 'subcategoria'" class="max-h-[300px] overflow-y-auto border border-slate-200 rounded-xl bg-slate-50 flex flex-col divide-y divide-slate-100 overflow-hidden">
            <div
              v-for="cat in categoriasFiltradasBuscadorProductoCompra"
              :key="cat.id"
              class="bg-white"
            >
              <button
                type="button"
                @click="cat.subcategoriasFiltradas.length === 0 ? seleccionarCategoriaBuscadorProductoCompra(cat) : alternarCategoriaBuscadorProductoCompra(cat.id)"
                class="box-border w-full text-left px-4 py-3 text-xs text-slate-800 hover:bg-slate-50 font-bold transition flex justify-between items-center"
              >
                <span class="truncate">
                  <i class="bi bi-folder-fill mr-2 text-slate-600"></i>
                  {{ cat.nombre }}
                </span>
                <i
                  v-if="cat.subcategoriasFiltradas.length > 0"
                  :class="categoriaBuscadorProductoCompraAbierta === cat.id ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"
                  class="text-slate-400 text-xs shrink-0"
                ></i>
                <span v-else class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold shrink-0">
                  Usar categoría
                </span>
              </button>

              <div v-if="categoriaBuscadorProductoCompraAbierta === cat.id" class="bg-slate-50/70 border-t border-slate-100">
                <button
                  type="button"
                  v-for="sub in cat.subcategoriasFiltradas"
                  :key="sub.id"
                  @click="seleccionarItemBuscadorProductoCompra(sub)"
                  class="box-border w-full text-left pl-9 pr-4 py-2 text-xs text-slate-700 hover:bg-white font-medium transition flex justify-between items-center"
                >
                  <span class="truncate">{{ sub.nombre }}</span>
                  <span class="text-[10px] bg-slate-200 text-slate-500 px-2 py-0.5 rounded-md font-mono shrink-0">ID: {{ sub.id }}</span>
                </button>

                <div v-if="cat.subcategoriasFiltradas.length === 0" class="pl-9 pr-4 py-2.5 text-xs text-slate-400 italic">
                  Sin subcategorías.
                </div>
              </div>
            </div>

            <div v-if="categoriasFiltradasBuscadorProductoCompra.length === 0" class="text-center py-6 text-xs text-slate-400 italic">
              No hay coincidencias.
            </div>
          </div>

          <div v-else class="max-h-[250px] overflow-y-auto border border-slate-200 rounded-xl bg-slate-50 flex flex-col divide-y divide-slate-100 overflow-hidden">
            <button
              type="button"
              v-for="item in listaFiltradaBuscadorProductoCompra"
              :key="item.id"
              @click="seleccionarItemBuscadorProductoCompra(item)"
              class="box-border w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-white hover:text-slate-900 font-medium transition flex justify-between items-center"
            >
              <span class="truncate">{{ item.nombre }}</span>
              <span class="text-[10px] bg-slate-200 text-slate-500 px-2 py-0.5 rounded-md font-mono shrink-0">ID: {{ item.id }}</span>
            </button>
            <div v-if="listaFiltradaBuscadorProductoCompra.length === 0" class="text-center py-6 text-xs text-slate-400 italic">
              No hay coincidencias.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 2: SELECTOR DE PRODUCTOS -->
    <div v-if="modalProductos" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 overflow-hidden">
          <h3 class="font-bold text-slate-800 text-sm">Catálogo de Productos</h3>
          <button class="text-slate-400 hover:text-slate-600 shrink-0" @click="modalProductos = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="p-4 bg-white">
          <div class="relative mb-3 w-full">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input
              v-model="busqueda"
              class="box-border w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs outline-none focus:border-slate-400"
              placeholder="Escribe para buscar producto..."
            />
          </div>

          <div class="max-h-[260px] overflow-y-auto space-y-2 pr-1">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              @click="seleccionarProducto(p)"
              class="box-border p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-slate-400 hover:bg-slate-50/50 transition flex justify-between items-center overflow-hidden"
            >
              <div class="truncate mr-2">
                <p class="font-bold text-slate-800 text-xs truncate">{{ p.nombre }}</p>
                <p class="text-[11px] text-slate-500 truncate">Unidad: {{ p.unidad_medida?.nombre || p.unidad_medida || 'pieza' }}</p>
              </div>
              <span class="text-[11px] bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-full border border-slate-200 shrink-0">
                Stock: {{ p.stock ?? 0 }}
              </span>
            </div>

            <p v-if="productosFiltrados.length === 0" class="text-center text-slate-400 py-6 text-xs italic">
              No se encontraron coincidencias
            </p>
          </div>

          <div class="flex justify-end mt-4 pt-3 border-t border-slate-100">
            <button @click="modalProductos = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-50">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 3: DETALLE Y COMPLETAR -->
    <div v-if="modalCompletar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-40 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-slate-100">
        <div class="px-6 py-4 flex justify-between items-center bg-[#2d3a4b] text-white overflow-hidden">
          <div class="flex items-center gap-2.5">
            <i class="bi bi-file-earmark-text-fill text-sky-400 text-lg"></i>
            <h3 class="font-bold text-white text-base">Detalle de Compra #{{ compraSeleccionada?.id }}</h3>
          </div>
          <button class="w-8 h-8 rounded-full bg-slate-700/50 text-slate-300 hover:bg-slate-700 hover:text-white flex items-center justify-center transition shrink-0" @click="modalCompletar = false">
            <i class="bi bi-x-lg text-sm"></i>
          </button>
        </div>

        <div class="p-6 bg-white">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
              <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                Fecha Recepción
              </label>
              <input
                v-model="fechaLlegada"
                type="date"
                class="box-border w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-white outline-none focus:border-slate-400"
                :disabled="!esPendiente || cargando"
              />
            </div>

            <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50 flex flex-col justify-center">
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Monto Total</span>
              <span class="text-xl font-black text-slate-900">
                ${{ Number(compraSeleccionada?.total ?? 0).toFixed(2) }}
              </span>
            </div>
          </div>

          <!-- TABLA DE DETALLES Y LOTES -->
          <div class="max-h-[240px] overflow-y-auto border border-slate-200 rounded-xl bg-white mb-4 overflow-hidden">
            <div class="overflow-x-auto w-full">
              <table class="w-full text-left text-xs border-separate border-spacing-0">
                <thead class="bg-slate-100/70 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                  <tr>
                    <th class="p-3 border-b border-slate-200">Producto</th>
                    <th class="p-3 border-b border-slate-200">Cant.</th>
                    <th class="p-3 border-b border-slate-200">Precio</th>
                    <th class="p-3 border-b border-slate-200">Lote / Vencimiento</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(d, i) in lotes" :key="i">
                    <td class="p-3 font-bold text-slate-900 border-b border-slate-100">{{ d.producto_nombre }}</td>
                    <td class="p-3 text-slate-700 border-b border-slate-100">{{ d.cantidad }}</td>
                    <td class="p-3 text-slate-700 border-b border-slate-100">${{ Number(d.precio).toFixed(2) }}</td>
                    <td class="p-3 border-b border-slate-100">
                      <div class="flex flex-col gap-1.5">
                        <input
                          v-model="d.codigo_lote"
                          class="box-border w-full p-1.5 border border-slate-200 rounded-lg text-[11px] bg-slate-50/50 outline-none focus:bg-white focus:border-slate-400"
                          placeholder="Cód. Lote"
                          :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                        />
                        <input
                          v-model="d.fecha_expiracion"
                          type="date"
                          class="box-border w-full p-1.5 border border-slate-200 rounded-lg text-[11px] bg-slate-50/50 outline-none focus:bg-white focus:border-slate-400"
                          :disabled="compraSeleccionada?.estado === 'cancelada' || cargando"
                        />
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- BOTONES -->
          <div class="flex justify-between items-center border-t border-slate-100 pt-4">
            <button
              v-if="esPendiente"
              @click="cancelarCompra"
              :disabled="cargando"
              class="px-4 py-2 border border-red-100 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition"
            >
              Anular
            </button>
            <div v-else></div>

            <div class="flex gap-2">
              <button @click="modalCompletar = false" :disabled="cargando" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-50">
                Cerrar
              </button>

              <button
                v-if="esPendiente"
                :disabled="cargando"
                @click="completarCompra"
                class="px-5 py-2 bg-[#2d3a4b] hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-md transition disabled:opacity-50"
              >
                {{ cargando ? 'Procesando...' : 'Completar y Cargar Inventario' }}
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
