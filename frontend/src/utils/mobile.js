import { Capacitor } from '@capacitor/core'

export const isMobile = () => {
  return Capacitor.isNativePlatform()
}

export const getPlatform = () => {
  return Capacitor.getPlatform()
}

// Voor API calls - gebruik het juiste URL
export const getApiBaseUrl = () => {
  if (isMobile()) {
    // Gebruik je externe API URL voor mobile
    return 'http://localhost:8080/api'
  }
  return import.meta.env.VITE_API_BASE_URL
}