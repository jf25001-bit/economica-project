import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/productos'

const getHeaders = () => {
  const token = localStorage.getItem('token')

  return {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
}

export const getProductos = async (params = {}) => {
  const res = await axios.get(API_URL, {
    ...getHeaders(),
    params
  })

  return res.data
}

export const getResumenProductos = async () => {
  const res = await axios.get(`${API_URL}/resumen`, getHeaders())
  return res.data
}

export const createProducto = async (data) => {
  const res = await axios.post(API_URL, data, getHeaders())
  return res.data
}

export const updateProducto = async (id, data) => {
  if (data instanceof FormData) {
    data.append('_method', 'PUT')
    const res = await axios.post(`${API_URL}/${id}`, data, getHeaders())
    return res.data
  }

  const res = await axios.put(`${API_URL}/${id}`, data, getHeaders())
  return res.data
}

export const deleteProducto = async (id) => {
  const res = await axios.delete(`${API_URL}/${id}`, getHeaders())
  return res.data
}
