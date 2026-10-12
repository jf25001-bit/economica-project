import Swal from 'sweetalert2'

const COLOR_PRIMARIO = '#2B3A4A'
const COLOR_PELIGRO = '#ef4444'
const COLOR_SECUNDARIO = '#64748b'

const escaparHtml = (texto) =>
  String(texto)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')

// Aviso de éxito que se cierra solo
export const alertaExito = (titulo, texto = '') =>
  Swal.fire({
    icon: 'success',
    title: titulo,
    text: texto,
    timer: 1800,
    showConfirmButton: false
  })

// Advertencia para validaciones del formulario (falta un dato, valor inválido, etc.)
export const alertaAdvertencia = (titulo, texto = '') =>
  Swal.fire({
    icon: 'warning',
    title: titulo,
    text: texto,
    confirmButtonText: 'Entendido',
    confirmButtonColor: COLOR_PRIMARIO
  })

// Error con una lista de mensajes (por ejemplo los que devuelve Laravel)
export const alertaError = (titulo, mensajes = []) => {
  const lista = (Array.isArray(mensajes) ? mensajes : [mensajes]).filter(Boolean)

  return Swal.fire({
    icon: 'error',
    title: titulo,
    html: lista.length
      ? `<ul style="text-align:left;margin:0;padding-left:1.1rem;font-size:0.9rem">${lista
          .map((m) => `<li style="margin-bottom:4px">${escaparHtml(m)}</li>`)
          .join('')}</ul>`
      : undefined,
    confirmButtonText: 'Entendido',
    confirmButtonColor: COLOR_PRIMARIO
  })
}

// Confirmación. Devuelve true si el usuario acepta.
export const confirmar = async ({
  titulo,
  texto = '',
  confirmarTexto = 'Sí, continuar',
  peligro = false
}) => {
  const resultado = await Swal.fire({
    icon: peligro ? 'warning' : 'question',
    title: titulo,
    text: texto,
    showCancelButton: true,
    confirmButtonText: confirmarTexto,
    cancelButtonText: 'Cancelar',
    confirmButtonColor: peligro ? COLOR_PELIGRO : COLOR_PRIMARIO,
    cancelButtonColor: COLOR_SECUNDARIO,
    reverseButtons: true
  })

  return resultado.isConfirmed
}

// Saca los mensajes de un error de axios (validación 422 de Laravel o mensaje general)
export const mensajesDeError = (error, respaldo = 'Ocurrió un error inesperado. Intenta de nuevo.') => {
  const errores = error?.response?.data?.errors

  if (errores && typeof errores === 'object') {
    return Object.values(errores).flat()
  }

  return [error?.response?.data?.message || respaldo]
}