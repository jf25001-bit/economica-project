import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/caja'

const getHeaders = () => {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` }
  }
}

const getUserId = () => {
  try {
    const userStr = localStorage.getItem('user')
    const user = userStr ? JSON.parse(userStr) : null
    return user?.id || null
  } catch (e) {
    return null
  }
}

export const cajaService = {
  // Estado de caja del usuario
  async obtenerEstado() {
    const userId = getUserId()
    const response = await axios.get(`${API_URL}/estado`, {
      ...getHeaders(),
      params: { user_id: userId }
    })
    return response.data
  },

  // Abrir caja
  async abrirCaja(datos) {
    const userId = getUserId()
    const payload = {
      ...datos,
      user_id: userId
    }
    const response = await axios.post(`${API_URL}/abrir`, payload, getHeaders())
    return response.data
  },

  // Cerrar caja
  async cerrarCaja(datos) {
    const userId = getUserId()
    const payload = {
      ...datos,
      user_id: userId
    }
    const response = await axios.post(`${API_URL}/cerrar`, payload, getHeaders())
    return response.data
  },

  // Obtener cajas activas globales (Para Control de Cajas)
  async obtenerCajasActivasGlobales() {
    const response = await axios.get(`${API_URL}/activas`, getHeaders())
    return response.data
  },

  // Obtener historial completo de cajas (Para Control de Cajas)
  async obtenerHistorialCierres() {
    const response = await axios.get(`${API_URL}/historial`, getHeaders())
    return response.data
  },

  // Forzar cierre por Admin
  async forzarCierreAdmin(cajaId, observacion) {
    const payload = {
      observacion: observacion
    }
    const response = await axios.post(`${API_URL}/forzar-cierre/${cajaId}`, payload, getHeaders())
    return response.data
  }
}