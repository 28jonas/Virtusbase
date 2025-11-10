import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './styles/tailwind.css'

// Import stores
import { useAuthStore } from './stores/auth'
import { useFamilyStore } from './stores/family'
import { useCalendarStore } from './stores/calendar'
import { useShoppingStore } from './stores/shopping'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

const authStore = useAuthStore()
authStore.init().finally(() => {
  app.mount('#app')
})

// Mobile-specifieke initialisatie
if (import.meta.env.MODE === 'mobile') {
  import('@capacitor/core').then(({ Capacitor }) => {
    console.log('Capacitor geladen:', Capacitor.getPlatform())
  })
}

export { useAuthStore, useFamilyStore, useCalendarStore, useShoppingStore }
