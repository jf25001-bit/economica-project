import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api'

// Función helper para obtener las cabeceras con el Token JWT / Sanctum
const getAuthHeaders = () => {
  const token = localStorage.getItem('token') || sessionStorage.getItem('token')
  return {
    headers: {
      'Authorization': token ? `Bearer ${token}` : '',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  }
}

export const cajaService = {
  // Obtener estado actual (Desempaqueta directo res.data)
  async obtenerEstado() {
    const res = await axios.get(`${API_URL}/caja/estado-actual`, getAuthHeaders())
    return res.data
  },

  async obtenerEstadoActual() {
    const res = await axios.get(`${API_URL}/caja/estado-actual`, getAuthHeaders())
    return res
  },

  // Abrir caja (Desempaqueta directo res.data)
  async abrirCaja(data) {
    const res = await axios.post(`${API_URL}/caja/abrir`, data, getAuthHeaders())
    return res.data
  },

  // Cerrar caja normal (Desempaqueta directo res.data)
  async cerrarCaja(data) {
    const res = await axios.post(`${API_URL}/caja/cerrar`, data, getAuthHeaders())
    return res.data
  },

  // Obtener cajas activas globales
  async obtenerCajasActivasGlobales() {
    const res = await axios.get(`${API_URL}/caja/activas`, getAuthHeaders())
    return res.data
  },

  // Obtener historial de cierres
  async obtenerHistorialCierres() {
    const res = await axios.get(`${API_URL}/caja/historial`, getAuthHeaders())
    return res.data
  },

  // Forzar cierre de caja
  async forzarCierreAdmin(cajaId, observacion) {
    const res = await axios.post(
      `${API_URL}/caja/forzar-cierre/${cajaId}`,
      {
        observacion: observacion || 'Cierre Forzado x Admin'
      },
      getAuthHeaders()
    )
    return res.data
  }
}