import axios from 'axios'
// Ruta de la API de roles
const API_URL = 'http://127.0.0.1:8000/api/roles'
// Obtiene el token guardado para autorizar la petición
const getHeaders = () => ({
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

// Obtiene todos los roles registrados
export const getRoles = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}