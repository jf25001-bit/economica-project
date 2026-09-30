import Swal from 'sweetalert2'

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  width: 420,
  showConfirmButton: true,
  showCancelButton: true,
  cancelButtonText: 'Ahora no',
  confirmButtonColor: '#2B3A4A',
  cancelButtonColor: '#94a3b8',
  timer: 12000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

export const avisoConAccion = async ({ icon = 'info', title, text, boton }) => {
  const resultado = await Toast.fire({ icon, title, text, confirmButtonText: boton })
  return resultado.isConfirmed
}