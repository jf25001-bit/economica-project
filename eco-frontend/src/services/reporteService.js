import api from './api'

export const getEmpleados = async () => {
  const { data } = await api.get('/usuarios')
  const lista = Array.isArray(data) ? data : data.data ?? []

  return lista
    .map((u) => ({
      id: u.id,
      nombre: `${u.name ?? ''} ${u.apellido ?? ''}`.trim()
    }))
    .sort((a, b) => a.nombre.localeCompare(b.nombre))
}

const leerError = async (error) => {
  const data = error.response?.data

  if (data instanceof Blob) {
    try {
      const json = JSON.parse(await data.text())
      const primerError = json.errors ? Object.values(json.errors)[0][0] : null
      return primerError || json.detalle || json.message || json.error
    } catch {
      return 'Error al procesar la respuesta del servidor'
    }
  }

  return data?.message || 'No se pudo generar el reporte'
}

export const generarReportePDF = async (params) => {
  const ventana = window.open('', '_blank')

  try {
    const { data } = await api.get('/reportes/general', {
      params,
      responseType: 'blob',
      headers: { Accept: 'application/json' }
    })

    const url = URL.createObjectURL(new Blob([data], { type: 'application/pdf' }))

    if (ventana) {
      ventana.location.href = url
    } else {
      window.location.href = url
    }
  } catch (error) {
    if (ventana) ventana.close()
    throw new Error(await leerError(error))
  }
}