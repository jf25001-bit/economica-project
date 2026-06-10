import api from './api'

export const getVentas = async () => {
  const res = await api.get('/ventas')
  return res.data
}

export const createVenta = async (data) => {
  const res = await api.post('/ventas', data)
  return res.data
}
