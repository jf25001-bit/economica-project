import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/proveedores'
const PRODUCTOS_URL = 'http://127.0.0.1:8000/api/productos'

const getHeaders = () => {
  const token = localStorage.getItem('token')

  return {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
}

// Obtener proveedores
export const getProveedores = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}

// Obtener catálogo de productos
export const getProductos = async () => {
  const response = await axios.get(
    PRODUCTOS_URL,
    getHeaders()
  )

  return response.data
}

// Crear proveedor
export const createProveedor = async (data) => {
  const response = await axios.post(
    API_URL,
    data,
    getHeaders()
  )

  return response.data
}

// Actualizar proveedor
export const updateProveedor = async (
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

// Eliminar proveedor
export const deleteProveedor = async (
  id
) => {
  const response = await axios.delete(
    `${API_URL}/${id}`,
    getHeaders()
  )

  return response.data
}