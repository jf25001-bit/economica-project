import api from './api'

export const getUsuarios = async (params = {}) => {
  const res = await api.get('/usuarios', { params })
  return res.data
}

export const createUsuario = async (data) => {
  const res = await api.post('/usuarios', data)
  return res.data
}

export const updateUsuario = async (id, data) => {
  const res = await api.put(`/usuarios/${id}`, data)
  return res.data
}

export const deleteUsuario = async (id) => {
  const res = await api.delete(`/usuarios/${id}`)
  return res.data
}
