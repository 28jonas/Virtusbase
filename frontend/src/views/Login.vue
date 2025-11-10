<template>
  <div class="max-w-md mx-auto mt-20 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input v-model="form.email" type="email" required class="w-full px-3 py-2 border rounded-md">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input v-model="form.password" type="password" required class="w-full px-3 py-2 border rounded-md">
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">
        Login
      </button>
    </form>
    <p class="mt-4 text-center">
      Don't have an account? 
      <router-link to="/register" class="text-blue-600 hover:underline">Register</router-link>
    </p>
  </div>
</template>

<script>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Login',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    const form = reactive({ email: '', password: '' })

    const handleLogin = async () => {
      try {
        await authStore.login(form)
        router.push('/')
      } catch (error) {
        alert(error.message || 'Login failed')
      }
    }

    return { form, handleLogin }
  }
}
</script>