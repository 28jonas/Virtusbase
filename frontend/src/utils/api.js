import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const user = ref(null)
  const isAuthenticated = computed(() => !!user.value)

  // API base URL
  const API_BASE = 'http://localhost:8080/api'

  // Fetch met automatische token refresh
  async function authenticatedFetch(url, options = {}) {
    const response = await fetch(`${API_BASE}${url}`, {
      ...options,
      credentials: 'include', // Cookies meesturen
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers,
      },
    })

    if (response.status === 401) {
      // Probeer token te refreshen
      const refreshResponse = await fetch(`${API_BASE}/auth/refresh`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })

      if (refreshResponse.ok) {
        // Probeer opnieuw na refresh
        return await fetch(`${API_BASE}${url}`, {
          ...options,
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
          },
        })
      } else {
        // Refresh failed, logout
        await logout()
        throw new Error('Authentication failed')
      }
    }

    return response
  }

  async function login(credentials) {
    try {
      const response = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(credentials),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || 'Login failed')
      }

      const data = await response.json()
      user.value = data.user
      
      return data
    } catch (error) {
      throw error
    }
  }

  async function register(userData) {
    try {
      const response = await fetch(`${API_BASE}/auth/register`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(userData),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || 'Registration failed')
      }

      const data = await response.json()
      user.value = data.user
      
      return data
    } catch (error) {
      throw error
    }
  }

  async function logout() {
    try {
      await fetch(`${API_BASE}/auth/logout`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      router.push('/login')
    }
  }

  async function getCurrentUser() {
    try {
      const response = await authenticatedFetch('/auth/me')
      
      if (response.ok) {
        const data = await response.json()
        user.value = data
        return data
      }
    } catch (error) {
      user.value = null
      throw error
    }
  }

  // Initialize auth state
  async function init() {
    try {
      await getCurrentUser()
    } catch (error) {
      // Silent fail - user is not authenticated
    }
  }

  return {
    user,
    isAuthenticated,
    login,
    register,
    logout,
    getCurrentUser,
    authenticatedFetch,
    init,
  }
})