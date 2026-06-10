import api from './api'

export const getCompras = async () => {
  const res = await api.get('/compras')
  return res.data
}

export const createCompra = async (data) => {
  const res = await api.post('/compras', data)
  return res.data
}

export const updateCompra = async (id, data) => {
  const res = await api.put(`/compras/${id}`, data)
  return res.data
}
