<template>
  <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4">
    <div class="modal-card-box bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-h-[92vh] flex flex-col overflow-hidden">

      <div class="px-6 py-4 flex justify-between items-center border-b border-slate-200 bg-slate-900 text-white shrink-0">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-white border border-slate-700 shrink-0">
            <i class="bi bi-bag-plus-fill text-lg"></i>
          </div>
          <div>
            <h3 class="font-bold text-white text-base">Registrar Nueva Orden de Compra</h3>
            <p class="text-xs text-slate-300">Detalla los ítems, precios y datos de lote correspondientes</p>
          </div>
        </div>
        <button
          class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition cursor-pointer shrink-0"
          @click="cerrar"
        >
          <i class="bi bi-x-lg text-xs"></i>
        </button>
      </div>

      <div class="modal-body-scroll p-5 sm:p-6 overflow-y-auto overflow-x-hidden flex-1 space-y-5 bg-slate-50/50">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="p-4 border border-slate-200 rounded-xl bg-white shadow-sm">
            <label class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-1.5">Fecha de Compra (automática)</label>
            <input
              v-model="fechaCompraNueva"
              type="date"
              readonly
              tabindex="-1"
              class="form-force-input h-10 px-3 py-2 border rounded-lg text-sm font-medium outline-none bg-slate-100 text-slate-500 border-slate-200 cursor-not-allowed"
            />
          </div>
          <div class="p-4 border border-slate-200 rounded-xl bg-white shadow-sm flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Monto Total Estimado</span>
            <span class="text-3xl font-black text-slate-900">${{ totalCompraNueva }}</span>
          </div>
        </div>

        <div class="form-stack-container">
          <label class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-3">Productos en la Orden</label>

          <div
            v-for="(d, i) in detalles"
            :key="i"
            class="producto-card-item p-5 border border-slate-200 rounded-xl bg-white shadow-sm mb-4 space-y-4"
          >
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
              <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ítem #{{ i + 1 }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Producto</label>
                <button
                  type="button"
                  @click="abrirSelector(i)"
                  class="form-force-button h-10 flex items-center justify-between px-3 rounded-lg border border-slate-300 bg-white hover:border-slate-900 text-slate-800 text-left cursor-pointer shadow-sm transition"
                >
                  <span class="truncate text-sm font-medium">
                    {{ getProductoNombre(d.producto_id) || 'Seleccionar producto...' }}
                  </span>
                  <i class="bi bi-search text-slate-400 text-xs shrink-0 ml-2"></i>
                </button>
              </div>

              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Proveedor Suministrador</label>
                <select
                  v-model="d.proveedor_id"
                  :disabled="!d.producto_id"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-800 outline-none focus:border-slate-900 disabled:bg-slate-100 disabled:text-slate-400"
                >
                  <option value="">-- Seleccionar Proveedor --</option>
                  <option
                    v-for="prov in obtenerProveedoresDelProducto(d.producto_id)"
                    :key="prov.id"
                    :value="prov.id"
                  >
                    {{ prov.nombre_proveedor || prov.nombre }}
                  </option>
                </select>
                <p
                  v-if="d.producto_id && !d.proveedor_id && obtenerProveedoresDelProducto(d.producto_id).length === 0"
                  class="text-[11px] text-amber-600 mt-1"
                >
                  * Este producto no tiene proveedores vinculados aún.
                </p>
                <button
                  type="button"
                  @click="irAProveedores(i)"
                  class="mt-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-900 underline underline-offset-2 cursor-pointer"
                >
                  ¿No aparece el proveedor? Agregar o vincular proveedor
                </button>
              </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Cant. Paquetes</label>
                <input
                  :value="d.cantidad"
                  type="text"
                  inputmode="numeric"
                  maxlength="6"
                  placeholder="1"
                  @input="actualizarEntero(d, 'cantidad', $event)"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-slate-900"
                />
              </div>
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Unid. por Paquete</label>
                <input
                  :value="d.unidades_por_paquete"
                  type="text"
                  inputmode="numeric"
                  maxlength="6"
                  placeholder="1"
                  @input="actualizarEntero(d, 'unidades_por_paquete', $event)"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 outline-none focus:border-slate-900"
                />
              </div>
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Precio Paquete ($)</label>
                <div class="relative w-full">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                  <input
                    :value="d.precio_compra"
                    type="text"
                    inputmode="decimal"
                    maxlength="9"
                    placeholder="0.00"
                    @input="actualizarPrecio(d, $event)"
                    class="form-force-input h-10 pl-7 pr-3 border border-slate-300 rounded-lg text-sm font-bold text-slate-900 outline-none focus:border-slate-900"
                  />
                </div>
              </div>
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Fecha Expiración</label>
                <input
                  v-model="d.fecha_expiracion"
                  type="date"
                  :min="fechaMinimaExpiracion"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-700 outline-none focus:border-slate-900"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 pt-2">
              <div class="min-w-0">
                <label class="text-xs font-semibold text-slate-600 block mb-1">Código de Lote</label>
                <input
                  v-model="d.codigo_lote"
                  type="text"
                  maxlength="20"
                  placeholder="Ej: LOTE-123"
                  class="form-force-input h-10 px-3 border border-slate-300 rounded-lg text-sm bg-white text-slate-800 outline-none focus:border-slate-900"
                />
              </div>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-between bg-slate-50 -mx-5 -mb-5 px-5 py-3.5 rounded-b-xl">
              <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase block tracking-wider">Subtotal Ítem</span>
                <span class="text-lg font-black text-slate-900">
                  ${{ subtotalItem(d).toFixed(2) }}
                </span>
              </div>

              <button
                type="button"
                @click="remove(i)"
                class="h-9 px-3.5 inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition cursor-pointer text-xs font-bold"
              >
                <i class="bi bi-trash text-sm"></i>
                <span>Eliminar</span>
              </button>
            </div>

          </div>
        </div>

        <button
          @click="add"
          type="button"
          class="w-full py-3.5 rounded-xl border-2 border-dashed border-slate-300 bg-white hover:bg-slate-100 hover:border-slate-800 text-xs font-bold text-slate-800 transition flex items-center justify-center gap-2 cursor-pointer shadow-sm"
        >
          <i class="bi bi-plus-circle-fill text-base text-slate-900"></i> Agregar otro producto
        </button>
      </div>

      <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 font-medium">Total Final:</span>
          <span class="text-xl font-black text-slate-900">${{ totalCompraNueva }}</span>
        </div>
        <div class="flex gap-2">
          <button
            @click="cerrar"
            :disabled="cargando"
            class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            @click="guardar"
            :disabled="cargando"
            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-bold text-white shadow-md transition flex items-center gap-1.5 cursor-pointer active:bg-slate-950"
          >
            <i v-if="!cargando" class="bi bi-check-lg text-sm"></i>
            {{ cargando ? 'Guardando...' : 'Guardar Compra' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <ModalSelectorProducto
    v-if="modalProductos"
    :productos="productos"
    @cerrar="modalProductos = false"
    @seleccionar="seleccionarProducto"
    @crear-producto="irAProductos"
  />
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { createCompra } from '@/services/compraService'
import { avisoConAccion } from '@/utils/avisos'
import { alertaError, alertaAdvertencia, mensajesDeError } from '@/utils/alertas'
import {
  fechaHoy,
  guardarBorrador,
  limpiarBorrador,
  nuevoDetalle,
  proveedoresDelProducto
} from '@/utils/compras'
import ModalSelectorProducto from '@/components/ModalSelectorProducto.vue'

const props = defineProps({
  productos: { type: Array, default: () => [] },
  proveedores: { type: Array, default: () => [] },
  detallesIniciales: { type: Array, default: null }
})

const emit = defineEmits(['cerrar', 'guardado'])

const router = useRouter()

const fechaMinimaExpiracion = fechaHoy()
const fechaCompraNueva = ref(fechaHoy())
const detalles = ref(props.detallesIniciales ?? [nuevoDetalle()])
const cargando = ref(false)
const modalProductos = ref(false)
const indexProducto = ref(null)

const obtenerProveedoresDelProducto = (productoId) => {
  return proveedoresDelProducto(productoId, props.productos, props.proveedores)
}

const subtotalItem = (d) => (Number(d.cantidad) || 0) * (Number(d.precio_compra) || 0)

const totalCompraNueva = computed(() => {
  return detalles.value.reduce((acc, d) => acc + subtotalItem(d), 0).toFixed(2)
})

const getProductoNombre = (id) => {
  const prod = props.productos.find(p => String(p.id) === String(id))
  return prod ? prod.nombre : ''
}

const add = () => {
  detalles.value.push(nuevoDetalle())
}

const remove = (i) => {
  detalles.value.splice(i, 1)
}

const cerrar = () => {
  limpiarBorrador()
  emit('cerrar')
}

const irAProductos = () => {
  guardarBorrador(detalles.value)
  router.push({ path: '/productos', query: { nuevo: 1 } })
}

const irAProveedores = (i) => {
  guardarBorrador(detalles.value)

  const productoId = detalles.value[i]?.producto_id
  router.push({
    path: '/proveedores',
    query: { nuevo: 1, ...(productoId ? { producto: productoId } : {}) }
  })
}

const abrirSelector = (i) => {
  indexProducto.value = i
  modalProductos.value = true
}

const avisarProductoSinProveedor = async (p) => {
  const ir = await avisoConAccion({
    icon: 'info',
    title: 'Producto sin proveedor',
    text: `"${p.nombre}" no está afiliado a ningún proveedor.`,
    boton: 'Ir a proveedores'
  })
  if (ir) router.push({ path: '/proveedores' })
}

const seleccionarProducto = (p) => {
  detalles.value[indexProducto.value].producto_id = p.id
  detalles.value[indexProducto.value].proveedor_id = ''
  modalProductos.value = false

  if (obtenerProveedoresDelProducto(p.id).length === 0) {
    avisarProductoSinProveedor(p)
  }
}

const actualizarEntero = (d, campo, e) => {
  const limpio = e.target.value.replace(/\D/g, '')
  d[campo] = limpio === '' ? '' : parseInt(limpio, 10)
  e.target.value = d[campo]
}

const actualizarPrecio = (d, e) => {
  const [entero, ...resto] = e.target.value.replace(/[^\d.]/g, '').split('.')
  d.precio_compra = resto.length ? `${entero}.${resto.join('').slice(0, 2)}` : entero
  e.target.value = d.precio_compra
}

const guardar = async () => {
  if (detalles.value.length === 0) {
    return alertaAdvertencia('Falta un producto', 'Debes agregar al menos un producto a la orden.')
  }

  const hoy = fechaHoy()

  for (const [i, d] of detalles.value.entries()) {
    const n = i + 1

    if (!d.producto_id || !d.proveedor_id) {
      return alertaAdvertencia(`Revisa el ítem #${n}`, 'Selecciona el producto y su proveedor.')
    }
    if (!Number.isInteger(Number(d.cantidad)) || Number(d.cantidad) < 1) {
      return alertaAdvertencia(
        `Revisa el ítem #${n}`,
        'La cantidad de paquetes debe ser un número entero de 1 en adelante (no se permiten fracciones como 1.5).'
      )
    }
    if (!Number.isInteger(Number(d.unidades_por_paquete)) || Number(d.unidades_por_paquete) < 1) {
      return alertaAdvertencia(
        `Revisa el ítem #${n}`,
        'Las unidades por paquete deben ser un número entero de 1 en adelante.'
      )
    }
    if (!(Number(d.precio_compra) > 0)) {
      return alertaAdvertencia(`Revisa el ítem #${n}`, 'El precio del paquete debe ser mayor a 0.')
    }
    if (d.fecha_expiracion && d.fecha_expiracion < hoy) {
      return alertaAdvertencia(
        `Revisa el ítem #${n}`,
        'La fecha de expiración no puede ser anterior a hoy. No se pueden comprar productos caducados.'
      )
    }
  }

  cargando.value = true
  try {
    const payload = {
      fecha_compra: fechaHoy(),
      detalles: detalles.value.map(d => ({
        producto_id: Number(d.producto_id),
        proveedor_id: Number(d.proveedor_id),
        cantidad: Number(d.cantidad),
        unidades_por_paquete: Number(d.unidades_por_paquete || 1),
        precio_compra: Number(d.precio_compra),
        codigo_lote: d.codigo_lote || null,
        fecha_expiracion: d.fecha_expiracion || null
      }))
    }

    await createCompra(payload)

    limpiarBorrador()
    emit('guardado')
  } catch (error) {
    console.error(error)
    alertaError(
      'No se pudo guardar la compra',
      mensajesDeError(error, 'Error al procesar la solicitud.')
    )
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.modal-card-box {
  width: 100% !important;
  max-width: 1100px !important;
  box-sizing: border-box !important;
}

.modal-body-scroll {
  width: 100% !important;
  box-sizing: border-box !important;
}

.form-stack-container {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.producto-card-item {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.form-force-input,
.form-force-button {
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
  min-width: 0 !important;
  box-sizing: border-box !important;
}
</style>