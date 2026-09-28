import api from './api'

// Función para iniciar sesión
export const login = async (credentials) => {
  try {
    // Usa 'api' centralizado en lugar de axios directo
    const response = await api.post('/auth/login', credentials)

    // Guardar token en localStorage si viene en la respuesta
    if (response.data.access_token) {
      localStorage.setItem('token', response.data.access_token)
    }

    return response.data
  } catch (error) {
    throw error.response?.data || {
      message: 'Error al iniciar sesión'
    }
  }
}

// Función para obtener datos del usuario autenticado
export const getMe = async () => {
  const response = await api.get('/auth/me')
  return response.data
}

// Función para cerrar sesión
export const logout = async () => {
  try {
    await api.post('/auth/logout')
  } finally {
    localStorage.removeItem('token')
  }
}