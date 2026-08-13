import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/compras'

const config = {
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
}

export const getCompras = async () => {
  const res = await axios.get(API_URL, config)
  return res.data
}

export const createCompra = async (data) => {
  const res = await axios.post(API_URL, data, config)
  return res.data
}

export const updateCompra = async (id, data) => {
  const res = await axios.put(`${API_URL}/${id}`, data, config)
  return res.data
}