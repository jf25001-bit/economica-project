import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/productos'

// Obtener productos
export const getProductos = async () => {
  const res = await axios.get(API_URL)
  return res.data
}