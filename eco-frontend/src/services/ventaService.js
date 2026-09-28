import api from './api'

export const getProductos = async () => {
  const res = await api.get('/productos')
  return res.data
}

export const getVentas = async () => {
  const res = await api.get('/ventas')
  return res.data
}

export const createVenta = async (datosVenta) => {
  const res = await api.post('/ventas', datosVenta)
  return res.data
}