import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/categorias'


//comprueba si esta el token para mostrar todo esto
const getHeaders = () => {
  const token = localStorage.getItem('token')

  return {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
}

export const getCategorias = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}

export const createCategoria = async (data) => {
  const response = await axios.post(
    API_URL,
    data,
    getHeaders()
  )

  return response.data
}

export const updateCategoria = async (
  id,
  data
) => {
  const response = await axios.put(
    `${API_URL}/${id}`,
    data,
    getHeaders()
  )

  return response.data
}

export const deleteCategoria = async (
  id
) => {
  const response = await axios.delete(
    `${API_URL}/${id}`,
    getHeaders()
  )

  return response.data
}