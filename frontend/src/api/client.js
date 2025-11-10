// frontend/src/api/client.js
import axios from 'axios'
import { getApiBaseUrl } from '@/utils/mobile'

const api = axios.create({
  baseURL: getApiBaseUrl(),
  timeout: 10000,
})

// JWT interceptor blijft hetzelfde
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api