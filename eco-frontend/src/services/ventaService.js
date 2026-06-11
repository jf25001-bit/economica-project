import api from './api'

export const getVentas = async (params = {}) => {
  const res = await api.get('/ventas', { params })
  return res.data
}

export const createVenta = async (data) => {
  const res = await api.post('/ventas', data)
  return res.data
}
