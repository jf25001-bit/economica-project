import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/subcategorias'

const getHeaders = () => {
  const token = localStorage.getItem('token')

  return {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
}


export const createSubcategoria = async (data) => {
  const res = await axios.post(API_URL, data, getHeaders())
  return res.data
}


export const deleteSubcategoria = async (id) => {
  const res = await axios.delete(
    `${API_URL}/${id}`,
    getHeaders()
  )
  return res.data
}


export const updateSubcategoria = async (id, data) => {
  const res = await axios.put(
    `${API_URL}/${id}`,
    data,
    getHeaders()
  )
  return res.data
}


export const getSubcategorias = async () => {
  const res = await axios.get(API_URL, getHeaders())
  return res.data
}