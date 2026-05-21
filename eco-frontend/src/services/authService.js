import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/auth'

export const login = async (credentials) => {
  try {
    const response = await axios.post(
      `${API_URL}/login`,
      credentials
    )

    return response.data
  } catch (error) {
    throw error.response?.data || {
      message: 'Error al iniciar sesión'
    }
  }
}