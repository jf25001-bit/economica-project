import api from './api' 

export const getProductos = () => api.get('/productos')

export const getAuxiliares = () => Promise.all([
  api.get('/categorias').catch(() => ({ data: [] })),
  api.get('/subcategorias').catch(() => ({ data: [] })),
  api.get('/proveedores').catch(() => ({ data: [] })),
  api.get('/unidades-medida').catch(() => ({ data: [] }))
])

export const guardarProductoAPI = (data, id = null) => {
  return id 
    ? api.put(`/productos/${id}`, data)
    : api.post('/productos', data)
}

export const subirImagenAPI = (formData) => {
  return api.post('/imagenes', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

export const eliminarProductoAPI = (id) => api.delete(`/productos/${id}`)