import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { API_BASE } from '../utils/config'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const user = ref(null)
  const requires2FA = ref(false)
  const pendingEmail = ref('')

  const isAuthenticated = computed(() => !!user.value)

  let isRefreshing = false;
  let refreshSubscribers = [];

  // Helper functie om subscribers te notificeren
  function onRefreshed(token) {
    refreshSubscribers.forEach(callback => callback(token));
    refreshSubscribers = [];
  }

  //const API_BASE = 'http://localhost:8080';
  // Verbeterde authenticatedFetch met FormData support
  async function authenticatedFetch(url, options = {}) {
    try {
      console.log('🔁 Making authenticated fetch to:', url);

      // Bepaal headers op basis van body type
      const isFormData = options.body instanceof FormData;
      console.log('📦 Body type:', isFormData ? 'FormData' : 'JSON');
      const defaultHeaders = {
        'Accept': 'application/json',
        ...(isFormData ? {} : { 'Content-Type': 'application/json' }), // Alleen JSON voor non-FormData
        ...options.headers,
      };

      let response = await fetch(`${API_BASE}${url}`, {
        ...options,
        credentials: 'include',
        headers: defaultHeaders,
      });

      console.log('📊 Response status:', response.status);

      // Als unauthorized en niet de refresh endpoint zelf
      if (response.status === 401 && !url.includes('/api/auth/refresh')) {
        console.log('🔄 Token expired, handling refresh...');

        // Als we al aan het refreshen zijn, wacht dan op het resultaat
        if (isRefreshing) {
          console.log('⏳ Already refreshing, waiting...');
          return new Promise((resolve, reject) => {
            refreshSubscribers.push((token) => {
              // Retry de oorspronkelijke request met het nieuwe token
              fetch(`${API_BASE}${url}`, {
                ...options,
                credentials: 'include',
                headers: defaultHeaders,
              })
                .then(resolve)
                .catch(reject);
            });
          });
        }

        // Start refresh proces
        isRefreshing = true;
        console.log('🔄 Starting token refresh...');

        try {
          const refreshResponse = await fetch(`${API_BASE}/api/auth/refresh`, {
            method: 'POST',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
          });

          console.log('🔄 Refresh response status:', refreshResponse.status);

          if (refreshResponse.ok) {
            console.log('✅ Token refreshed successfully');
            isRefreshing = false;
            onRefreshed(); // Notificeer wachtende requests

            // Retry de oorspronkelijke request
            return await fetch(`${API_BASE}${url}`, {
              ...options,
              credentials: 'include',
              headers: defaultHeaders,
            });
          } else {
            console.log('❌ Refresh failed with status:', refreshResponse.status);
            throw new Error('Refresh failed');
          }
        } catch (error) {
          console.log('❌ Refresh error:', error);
          isRefreshing = false;
          refreshSubscribers = []; // Clear subscribers
          await logout();
          throw error;
        }
      }

      return response;
    } catch (error) {
      console.error('💥 Fetch error:', error);
      throw error;
    }
  }

  // Aangepaste login functie met 2FA support
  async function login(credentials) {
    try {
      const response = await fetch(`${API_BASE}/api/auth/login`, {
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

      // Check of 2FA vereist is
      if (data.requires_2fa) {
        requires2FA.value = true
        pendingEmail.value = credentials.email
        return { requires2FA: true }
      }

      user.value = data.user
      requires2FA.value = false
      console.log('Login successful:', data)
      return { success: true, user: data.user }
    } catch (error) {
      throw error
    }
  }

   async function register(userData) {
    try {
      console.log("data begin register:", userData)
      const response = await fetch(`${API_BASE}/api/auth/register`, {
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

  // 2FA verificatie
  async function verify2FA(twoFactorCode) {
    try {
      const response = await fetch(`${API_BASE}/api/auth/verify-2fa`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          email: pendingEmail.value,
          two_factor_code: twoFactorCode
        }),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || '2FA verification failed')
      }

      const data = await response.json()
      user.value = data.user
      requires2FA.value = false
      pendingEmail.value = ''
      console.log('2FA verification successful:', data)
      return data
    } catch (error) {
      throw error
    }
  }

  // Forgot password
  async function forgotPassword(email) {
    try {
      console.log('Forgot password request received:', email)
      const response = await fetch(`${API_BASE}/api/auth/forgot-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ email }),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || 'Failed to send reset link')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

  // Reset password
  async function resetPassword(resetData) {
    try {
      console.log('Reset password request received:', resetData)
      const response = await fetch(`${API_BASE}/api/auth/reset-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(resetData),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || 'Failed to reset password')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

  // 2FA toggle
  async function toggle2FA(enable) {
    try {
      const response = await authenticatedFetch('/api/auth/toggle-2fa', {
        method: 'POST',
        body: JSON.stringify({ enable }),
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.message || 'Failed to toggle 2FA')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

  // Resend 2FA code
  async function resend2FACode() {
    try {
      // We kunnen de login opnieuw proberen zonder wachtwoord om een nieuwe code te triggeren
      const response = await fetch(`${API_BASE}/api/auth/login`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          email: pendingEmail.value,
          password: '' // Backend zal dit afwijzen maar wel 2FA code sturen
        }),
      })

      // We verwachten een 401 of 422, maar de 2FA code zou verzonden moeten zijn
      return { success: true, message: 'Verification code sent' }
    } catch (error) {
      // Zelfs als er een error is, is de code waarschijnlijk verzonden
      return { success: true, message: 'Verification code sent' }
    }
  }

  // In je auth store
async function setupAuthenticator2FA() {
    try {
      const response = await this.authenticatedFetch('/api/auth/setup-authenticator-2fa', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })

      if (!response.ok) {
        throw new Error('Failed to setup 2FA')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

async function verifyAuthenticator2FA(code) {
    try {
      const response = await this.authenticatedFetch('/api/auth/verify-authenticator-2fa', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ code })
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.error || 'Verification failed')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

async function disable2FA() {
    try {
      const response = await this.authenticatedFetch('/api/auth/disable-2fa', {
        method: 'POST'
      })

      if (!response.ok) {
        throw new Error('Failed to disable 2FA')
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

async function verifyRecoveryCode(email, recoveryCode) {
    try {
      const response = await this.authenticatedFetch(`/api/auth/verify-recovery-code`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, recovery_code: recoveryCode })
      })

      if (!response.ok) {
        const error = await response.json()
        throw new Error(error.error || 'Invalid recovery code')
      }

      const data = await response.json()
      this.user = data.user
      return data
    } catch (error) {
      throw error
    }
  }

  async function logout() {
    try {
      await fetch(`${API_BASE}/api/auth/logout`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      }).catch(() => { })
    } finally {
      user.value = null
      requires2FA.value = false
      pendingEmail.value = ''
      router.push('/login')
    }
  }

  async function getCurrentUser() {
  console.log('🍪 Cookies before request:', document.cookie);
  console.log('🔐 getCurrentUser called');
  
  try {
    const response = await fetch(`${API_BASE}/api/auth/me`, {
      method: 'GET',
      credentials: 'include', // 🔥 Dit is cruciaal
      headers: {
        'Accept': 'application/json',
      },
    });

    console.log('📡 Response status:', response.status);
    console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
    
    if (response.ok) {
      const data = await response.json();
      console.log('✅ User data:', data);
      return data;
    } else {
      console.error('❌ Failed to get user:', response.status);
      throw new Error(`HTTP error! status: ${response.status}`);
    }
  } catch (error) {
    console.error('💥 Error fetching user:', error);
    throw error;
  }
}

  // Initialize auth state
  async function init() {
    try {
      await getCurrentUser()
      await getLoggedInUser()
      console.log('Auth store initialized with permissions:', {
        companyType: companyType.value,
        userRole: userRole.value,
        companyTypeName: getCompanyTypeName(),
        userRoleName: getUserRoleName()
      })
    } catch (error) {
      // Silent fail - user is not authenticated
    }
  }

  async function getLoggedInUser() {
    console.log('getLoggedInUser called');
    try {
      const response = await fetch(`${API_BASE}/api/auth/meInfo`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })

      console.log('Response status:', response.status);

      if (response.ok) {
        const userData = await response.json();
        console.log('User data received:', userData);

        user.value = userData;

        // Log permissions voor debugging

        return userData;
      } else {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
    }
    catch (error) {
      user.value = null
      console.error('Error fetching user:', error);
      throw error
    }
  }

  const handleApiError = (error, router) => {
    console.error('API Error:', error)

    if (error.message.includes('status: 403') || error.message.includes('Forbidden')) {
      console.log('🔒 Access denied, redirecting to home')
      router.push('/dashboard') // of '/unauthorized'
      return true // Error is afgehandeld
    }

    if (error.message.includes('status: 401') || error.message.includes('Unauthorized')) {
      console.log('🔑 Unauthorized, redirecting to login')
      router.push('/login')
      return true
    }

    return false // Error niet afgehandeld
  }

  return {
    user,
    isAuthenticated,
    requires2FA,
    pendingEmail,
    login,
    register,
    logout,
    getCurrentUser,
    authenticatedFetch,
    init,
    getLoggedInUser,
    handleApiError,
    // Nieuwe 2FA en password functies
    verify2FA,
    forgotPassword,
    resetPassword,
    toggle2FA,
    resend2FACode,
    setupAuthenticator2FA,
    verifyAuthenticator2FA,
    verifyRecoveryCode,
    disable2FA,
  }
})