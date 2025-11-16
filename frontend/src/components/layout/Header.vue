<!-- components/layout/Header.vue -->
<template>
  <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <!-- Mobile Menu Button -->
        <button 
          @click="$emit('toggle-sidebar')"
          class="lg:hidden w-10 h-10 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          <span class="text-xl">☰</span>
        </button>
        
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ currentRouteTitle }}
        </h1>
        <div class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
          {{ currentDate }}
        </div>
      </div>
      
      <div class="flex items-center space-x-4">
        <!-- Notificaties Dropdown -->
        <div class="relative">
          <button 
            @click="toggleNotifications"
            class="w-10 h-10 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors relative"
          >
            <span class="text-xl">🔔</span>
            <span 
              v-if="unreadNotificationsCount > 0"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
            >
              {{ unreadNotificationsCount }}
            </span>
          </button>

          <!-- Notificaties Dropdown Menu -->
          <div 
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
          >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="font-semibold text-gray-900 dark:text-white">Notificaties</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ notifications.length }} ongelezen</p>
            </div>
            
            <div class="max-h-96 overflow-y-auto">
              <div 
                v-for="notification in notifications"
                :key="notification.id"
                class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors"
                :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read }"
              >
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                    <span class="text-sm">{{ notification.icon }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ notification.title }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                      {{ notification.message }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                      {{ notification.time }}
                    </p>
                  </div>
                  <button 
                    @click="markAsRead(notification.id)"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                  >
                    <span class="text-lg">⋅⋅⋅</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
              <button 
                @click="markAllAsRead"
                class="w-full text-center py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-md transition-colors"
              >
                Alles als gelezen markeren
              </button>
            </div>
          </div>
        </div>

        <!-- User Dropdown -->
        <div class="relative">
          <button 
            @click="toggleUserMenu"
            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          >
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
              {{ userInitials }}
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden md:block">
              {{ userName }}
            </span>
            <span class="text-gray-400 transform transition-transform" :class="{ 'rotate-180': showUserMenu }">
              ▼
            </span>
          </button>

          <!-- User Dropdown Menu -->
          <div 
            v-if="showUserMenu"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
          >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ userName }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ userEmail }}</p>
            </div>
            
            <div class="p-2">
              <a 
                href="#" 
                class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <span>👤</span>
                <span>Profiel</span>
              </a>
              <a 
                href="#" 
                class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <span>⚙️</span>
                <span>Instellingen</span>
              </a>
              <a 
                href="#" 
                class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <span>🌙</span>
                <span>Weergave</span>
              </a>
            </div>
            
            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
              <button 
                @click="logout"
                class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <span>🚪</span>
                <span>Uitloggen</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import {useAuthStore} from '../../stores/auth'

// Define emits voor de toggle functionaliteit
defineEmits(['toggle-sidebar'])

const route = useRoute()
const authStore = useAuthStore()

// State voor dropdowns
const showNotifications = ref(false)
const showUserMenu = ref(false)
const notifications = ref([])

// Standaard notificaties
const defaultNotifications = [
  {
    id: 1,
    title: 'Nieuwe taak toegewezen',
    message: 'Je hebt een nieuwe taak gekregen in "Boodschappen"',
    time: '5 minuten geleden',
    icon: '📝',
    read: false
  },
  {
    id: 2,
    title: 'Herinnering',
    message: 'Vergeet niet de familie-afspraak van morgen',
    time: '1 uur geleden',
    icon: '⏰',
    read: false
  },
  {
    id: 3,
    title: 'Systeem update',
    message: 'Nieuwe features beschikbaar in de app',
    time: '2 uur geleden',
    icon: '🔄',
    read: true
  },
  {
    id: 4,
    title: 'Welkom!',
    message: 'Succes met je nieuwe Life Manager',
    time: '1 dag geleden',
    icon: '👋',
    read: true
  }
]

// User gegevens
const authUser = computed(() => authStore.user || {})
const userInitials = computed(() => {
  const names = authUser.value.name ? authUser.value.name.split(' ') : []
  const initials = names.map(n => n.charAt(0).toUpperCase()).join('')
  return initials.slice(0, 2)
})
const userName = authUser.value.name
const userEmail = authUser.value.email

// Computed properties
const currentRouteTitle = computed(() => {
  console.log('Route name:', route.name)
  const routeName = route.name
  const titles = {
    'Dashboard': 'Dashboard',
    'Families': 'Familie Beheer',
    'Habits': 'Gewoontes',
    'Shopping': 'Boodschappen',
    'Todo': 'Todo\'s',
    'Calendar': 'Kalender',
  }
  return titles[routeName] || 'Virtusbase'
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('nl-NL', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
})

const unreadNotificationsCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

// Methods
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

const markAsRead = (id) => {
  const notification = notifications.value.find(n => n.id === id)
  if (notification) {
    notification.read = true
  }
}

const markAllAsRead = () => {
  notifications.value.forEach(notification => {
    notification.read = true
  })
}

const logout = () => {
  authStore.logout()
  console.log('Uitloggen...')
  showUserMenu.value = false
}

// Close dropdowns when clicking outside
const closeDropdowns = (event) => {
  if (!event.target.closest('.relative')) {
    showNotifications.value = false
    showUserMenu.value = false
  }
}

// Initialize notifications
onMounted(() => {
  notifications.value = [...defaultNotifications]
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})
</script>

<style scoped>
.dark .hover\:bg-gray-750:hover {
  background-color: #374151;
}
</style>