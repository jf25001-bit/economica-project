import { computed, ref, watch } from 'vue'

export const OPCIONES_POR_PAGINA = [5, 10, 20, 50]

/**
 * Paginación reutilizable.
 *
 * @param {Ref<Array>} lista  
 * @param {Object} opciones
 * @param {number} opciones.porPaginaInicial  
 * @param {Array}  opciones.reiniciarCon      
 */
export const usePaginacion = (lista, { porPaginaInicial = 10, reiniciarCon = [] } = {}) => {
  const paginaActual = ref(1)
  const porPagina = ref(porPaginaInicial)

  const total = computed(() => lista.value.length)

  const totalPaginas = computed(() =>
    Math.max(1, Math.ceil(total.value / porPagina.value))
  )

  const paginados = computed(() => {
    const inicio = (paginaActual.value - 1) * porPagina.value
    return lista.value.slice(inicio, inicio + porPagina.value)
  })

  watch([porPagina, ...reiniciarCon], () => {
    paginaActual.value = 1
  })

  watch(totalPaginas, (n) => {
    if (paginaActual.value > n) paginaActual.value = n
  })

  return { paginaActual, porPagina, total, totalPaginas, paginados }
}