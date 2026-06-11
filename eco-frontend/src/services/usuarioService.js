import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/usuarios'

const getHeaders = () => ({
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

export const getUsuarios = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}

export const createUsuario = async (data) => {
  const response = await axios.post(
    API_URL,
    data,
    getHeaders()
  )

  return response.data
}

export const updateUsuario = async (id, data) => {
  const response = await axios.put(
    `${API_URL}/${id}`,
    data,
    getHeaders()
  )

  return response.data
}

export const deleteUsuario = async (id) => {
  const response = await axios.delete(
    `${API_URL}/${id}`,
    getHeaders()
  )

  return response.data
}