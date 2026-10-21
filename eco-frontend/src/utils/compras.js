const CLAVE_BORRADOR = 'borrador_compra'

export const fechaHoy = () => {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

export const nuevoDetalle = () => ({
  producto_id: '',
  proveedor_id: '',
  cantidad: 1,
  unidades_por_paquete: 1,
  precio_compra: '',
  codigo_lote: '',
  fecha_expiracion: ''
})

export const proveedoresDelProducto = (productoId, productos, proveedores) => {
  if (!productoId) return []

  const prod = productos.find(p => String(p.id) === String(productoId))
  if (prod && Array.isArray(prod.proveedores) && prod.proveedores.length > 0) {
    return prod.proveedores
  }

  return proveedores.filter(p => {
    if (!Array.isArray(p.productos)) return false
    return p.productos.some(item => String(item.id) === String(productoId))
  })
}

export const guardarBorrador = (detalles) => {
  try {
    sessionStorage.setItem(CLAVE_BORRADOR, JSON.stringify({ detalles }))
  } catch (error) {
    console.error('No se pudo guardar el borrador:', error)
  }
}

export const recuperarBorrador = () => {
  const raw = sessionStorage.getItem(CLAVE_BORRADOR)
  if (!raw) return null

  try {
    const borrador = JSON.parse(raw)
    if (!Array.isArray(borrador.detalles) || borrador.detalles.length === 0) return null
    return borrador.detalles
  } catch (error) {
    console.error('No se pudo restaurar el borrador:', error)
    return null
  } finally {
    sessionStorage.removeItem(CLAVE_BORRADOR)
  }
}

export const limpiarBorrador = () => {
  sessionStorage.removeItem(CLAVE_BORRADOR)
}