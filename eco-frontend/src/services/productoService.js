import api from './api'

export const getProductos = () => {
  return api.get('/productos')
}

export const getAuxiliares = () => {
  return Promise.all([
    api.get('/categorias').catch(() => ({ data: [] })),
    api.get('/subcategorias').catch(() => ({ data: [] })),
    api.get('/unidades-medida').catch(() => ({ data: [] }))
  ])
}

export const guardarProductoAPI = (data, id = null) => {
  return id
    ? api.put(`/productos/${id}`, data)
    : api.post('/productos', data)
}

export const subirImagenAPI = (formData) => {
  return api.post('/imagenes', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

export const eliminarProductoAPI = (id) => {
  return api.delete(`/productos/${id}`)
}