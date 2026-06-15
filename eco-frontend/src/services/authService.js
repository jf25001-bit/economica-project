import axios from 'axios'
// Ruta principal donde se encuentran las rutas de autenticación
const API_URL = 'http://127.0.0.1:8000/api/auth'

// Función para iniciar sesión
export const login = async (credentials) => {
  try {
      // Envía usuario y contraseña al backend
    const response = await axios.post(
      `${API_URL}/login`,
      credentials
    )

    // Retorna los datos recibidos (token y usuario)
    return response.data
  } catch (error) {

    // Cacha el  errores si las credenciales son incorrectas
    throw error.response?.data || {
      message: 'Error al iniciar sesión'
    }
  }
}