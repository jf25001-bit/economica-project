import axios from 'axios'

// Ruta de la API de usuarios
const API_URL = 'http://127.0.0.1:8000/api/usuarios'

// Obtiene el token guardado para enviarlo en cada petición
const getHeaders = () => ({
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})
// Obtiene la lista de usuarios
export const getUsuarios = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}
// Crea un nuevo usuario
export const createUsuario = async (data) => {
  const response = await axios.post(
    API_URL,
    data,
    getHeaders()
  )

  return response.data
}
// Actualiza la información de un usuario
export const updateUsuario = async (id, data) => {
  const response = await axios.put(
    `${API_URL}/${id}`,
    data,
    getHeaders()
  )

  return response.data
}

// Elimina un usuario por su id
export const deleteUsuario = async (id) => {
  const response = await axios.delete(
    `${API_URL}/${id}`,
    getHeaders()
  )

  return response.data
}