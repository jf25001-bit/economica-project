import axios from 'axios'
import api from './api'; // Tu instancia de Axios configurada

const API_URL = 'http://127.0.0.1:8000/api/auth' 

export const login = async (credentials) => {
  // credentials ya lleva { usuario, password } mapeados correctamente
  const response = await api.post('/auth/login', credentials);
  return response.data; 
};