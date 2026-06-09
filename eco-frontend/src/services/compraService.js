import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/compras'

// Obtener compras
export const getCompras = async () => {
  const res = await axios.get(API_URL)
  return res.data
}

// Crear compra
export const createCompra = async (data) => {
  const res = await axios.post(API_URL, data)
  return res.data
}

export const updateCompra = async (id, data) => {
  const res = await axios.put(
    `http://127.0.0.1:8000/api/compras/${id}`,
    data
  )

  return res.data
}