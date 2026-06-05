import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useComprasStore = defineStore('compras', () => {
  const proveedorSeleccionado = ref(null)
  const detalleProductos = ref([]) // Los ítems agregados a la factura

  return {
    proveedorSeleccionado,
    detalleProductos
  }
})