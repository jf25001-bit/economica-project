export const DIAS_ALERTA = 15
export const DIAS_CRITICO = 7
export const DIAS_URGENTE = 3
export const DIAS_AVISO_VENCIDOS = 10

export const ESTADOS_LOTE = {
  vencido: {
    clase: 'bg-gray-800 text-white border-gray-800',
    icon: 'bi-x-octagon-fill',
    fila: 'bg-gray-100/70'
  },
  urgente: {
    clase: 'bg-red-50 text-red-700 border-red-200',
    icon: 'bi-exclamation-octagon-fill',
    fila: 'bg-red-50/60'
  },
  critico: {
    clase: 'bg-orange-50 text-orange-700 border-orange-200',
    icon: 'bi-exclamation-triangle-fill',
    fila: 'bg-orange-50/60'
  },
  proximo: {
    clase: 'bg-amber-50 text-amber-700 border-amber-200',
    icon: 'bi-clock-history',
    fila: 'bg-amber-50/50'
  },
  agotado: {
    clase: 'bg-gray-100 text-gray-500 border-gray-200',
    icon: 'bi-dash-circle',
    fila: 'bg-gray-50/50'
  }
}

export const ESTADOS_POR_VENCER = ['urgente', 'critico', 'proximo']

export const FILTROS_VENCIMIENTO = [
  { value: '', label: 'Todos' },
  { value: 'por-vencer', label: 'Por vencer' },
  { value: 'vencidos', label: 'Vencidos' }
]

export const parseFecha = (fecha) => {
  if (!fecha) return null
  const d = new Date(`${String(fecha).slice(0, 10)}T00:00:00`)
  return isNaN(d.getTime()) ? null : d
}

export const diasParaVencer = (fecha) => {
  const f = parseFecha(fecha)
  if (!f) return null
  const hoy = new Date()
  hoy.setHours(0, 0, 0, 0)
  return Math.round((f - hoy) / 86400000)
}

export const clasificarLote = (fecha, cantidadActual = 1) => {
  if (cantidadActual <= 0) return { dias: diasParaVencer(fecha), estadoVenc: 'agotado' }
  const dias = diasParaVencer(fecha)
  if (dias === null) return { dias: null, estadoVenc: 'sin-fecha' }
  if (dias < 0) return { dias, estadoVenc: 'vencido' }
  if (dias <= DIAS_URGENTE) return { dias, estadoVenc: 'urgente' }
  if (dias <= DIAS_CRITICO) return { dias, estadoVenc: 'critico' }
  if (dias <= DIAS_ALERTA) return { dias, estadoVenc: 'proximo' }
  return { dias, estadoVenc: 'vigente' }
}

export const textoEstadoLote = (lote) => {
  if (lote.estadoVenc === 'agotado') return 'Agotado'
  const d = lote.diasRestantes
  if (lote.estadoVenc === 'vencido') {
    const n = Math.abs(d)
    return `Vencido hace ${n} ${n === 1 ? 'día' : 'días'}`
  }
  if (d === 0) return 'Vence hoy'
  return `Vence en ${d} ${d === 1 ? 'día' : 'días'}`
}

export const formatearFecha = (fecha) => {
  if (!fecha) return 'Sin vencimiento'
  const fechaObj = parseFecha(fecha)
  return fechaObj ? fechaObj.toLocaleDateString('es-SV') : fecha
}

export const ordenarLotesFifo = (lista) => {
  return [...lista].sort((a, b) => {
    const fechaA = parseFecha(a.fecha_expiracion)?.getTime() ?? Number.MAX_SAFE_INTEGER
    const fechaB = parseFecha(b.fecha_expiracion)?.getTime() ?? Number.MAX_SAFE_INTEGER

    if (fechaA !== fechaB) return fechaA - fechaB
    return Number(a.id) - Number(b.id)
  })
}

const sumarCantidad = (lista) => lista.reduce((total, l) => total + l.cantidad_actual, 0)

export const construirInventario = (productos, lotes) => {
  const anulados = new Set(productos.filter(p => p.anulado).map(p => Number(p.id)))
  const grupos = {}

  for (const lote of lotes) {
    const cantidadActual = Number(lote.cantidad_actual) || 0
    if (!lote.producto_id) continue
    if (cantidadActual <= 0 && !anulados.has(Number(lote.producto_id))) continue
    if (!grupos[lote.producto_id]) grupos[lote.producto_id] = []

    grupos[lote.producto_id].push({
      ...lote,
      cantidad_inicial: Number(lote.cantidad_inicial) || 0,
      cantidad_actual: cantidadActual
    })
  }

  return productos.map(producto => {
    let orden = 0

    const lotesProducto = ordenarLotesFifo(grupos[producto.id] || []).map(lote => {
      const { dias, estadoVenc } = clasificarLote(lote.fecha_expiracion, lote.cantidad_actual)
      return {
        ...lote,
        diasRestantes: dias,
        estadoVenc,
        ordenFifo: (estadoVenc === 'vencido' || estadoVenc === 'agotado') ? null : ++orden
      }
    })

    const lotesVigentes = lotesProducto.filter(l => l.estadoVenc !== 'vencido' && l.estadoVenc !== 'agotado')
    const lotesVencidos = lotesProducto.filter(l => l.estadoVenc === 'vencido')
    const lotesPorVencer = lotesVigentes.filter(l => ESTADOS_POR_VENCER.includes(l.estadoVenc))

    let severidad = null
    if (lotesPorVencer.some(l => l.estadoVenc === 'urgente')) severidad = 'urgente'
    else if (lotesPorVencer.some(l => l.estadoVenc === 'critico')) severidad = 'critico'
    else if (lotesPorVencer.length) severidad = 'proximo'

    return {
      id: producto.id,
      codigo: producto.codigo_barras || 'Sin codigo',
      nombre: producto.nombre || 'Sin nombre',
      categoria: producto.subcategoria?.categoria?.nombre || 'General',
      anulado: !!producto.anulado,
      stock: lotesProducto.length > 0 ? sumarCantidad(lotesVigentes) : Number(producto.stock) || 0,
      minimo: Number(producto.stock_minimo) || 0,
      lotes: lotesProducto,
      lotesVigentes,
      lotesVencidos,
      vencidos: lotesVencidos.length,
      vencidosRecientes: lotesVencidos.filter(l => l.diasRestantes >= -DIAS_AVISO_VENCIDOS).length,
      diasUltimoVencido: lotesVencidos.length
        ? Math.max(...lotesVencidos.map(l => l.diasRestantes))
        : null,
      cantidadVencida: sumarCantidad(lotesVencidos),
      porVencer: lotesPorVencer.length,
      severidad
    }
  })
}