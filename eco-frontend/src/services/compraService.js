import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/compras'

const getHeaders = () => {
  const token = localStorage.getItem('token')

  return {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json'
    }
  }
}

export const getCompras = async () => {
  const response = await axios.get(API_URL, getHeaders())
  return response.data
}

export const createCompra = async (data) => {
  const response = await axios.post(API_URL, data, getHeaders())
  return response.data
}

export const updateCompra = async (id, data) => {
  const response = await axios.put(`${API_URL}/${id}`, data, getHeaders())
  return response.data
}

export const deleteCompra = async (id) => {
  const response = await axios.delete(`${API_URL}/${id}`, getHeaders())
  return response.data
}