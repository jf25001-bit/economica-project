import axios from 'axios'

// La URL apunta a /api/reportes (NO /api/auth/reportes)
const API_URL = 'http://127.0.0.1:8000/api/reportes'

// Obtiene los headers con el Token de autenticación
const getHeaders = () => {
  const token = localStorage.getItem('token') || sessionStorage.getItem('token')
  return {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json'
    }
  }
}

/**
 * Obtiene las tarjetas/métricas según el período ('dia', 'semana', 'mes')
 */
export const getTarjetasReporte = async (periodo = 'mes') => {
  try {
    const response = await axios.get(
      `${API_URL}/tarjetas?periodo=${periodo}`, 
      getHeaders()
    )
    return response.data
  } catch (error) {
    throw error.response?.data || { message: 'Error al obtener estadísticas del reporte' }
  }
}

/**
 * Descarga y abre el PDF generado
 */
export const descargarPDFReporte = async (tipo = 'general', periodo = 'mes') => {
  try {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token')
    
    // Petición de tipo blob para que Axios pueda recibir el PDF binario con el Token Bearer
    const response = await axios.get(
      `${API_URL}/general?tipo=${tipo}&periodo=${periodo}`, 
      {
        headers: {
          Authorization: `Bearer ${token}`
        },
        responseType: 'blob'
      }
    )

    // Convierte los datos binarios a una URL descargable y abre el PDF en una pestaña
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    window.open(url, '_blank')
  } catch (error) {
    throw error.response?.data || { message: 'Error al descargar el PDF' }
  }
}