import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/roles'

const getHeaders = () => ({
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  }
})

export const getRoles = async () => {
  const response = await axios.get(
    API_URL,
    getHeaders()
  )

  return response.data
}