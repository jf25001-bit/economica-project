import api from './api'

export const getCompras = async () => {
  const response = await api.get('/compras')
  return response.data
}

export const createCompra = async (data) => {
  const response = await api.post('/compras', data)
  return response.data
}

export const anularCompra = async (id) => {
  const response = await api.patch(`/compras/${id}/anular`)
  return response.data
}