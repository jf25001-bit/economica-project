// Solo letras (con tildes, ñ y espacios)
export function soloLetras(texto) {
  return /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(texto)
}

// Solo números enteros
export function soloNumeros(texto) {
  return /^[0-9]+$/.test(texto)
}

// Número con decimales (ej: 10 o 10.50)
export function esDecimal(texto) {
  return /^[0-9]+(\.[0-9]{1,2})?$/.test(texto)
}

// Que no esté vacío
export function noVacio(texto) {
  return String(texto).trim() !== ''
}