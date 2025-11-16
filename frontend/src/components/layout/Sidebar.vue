<template>
  <aside class="h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col items-center py-6 space-y-8 transition-colors duration-200 lg:w-20 w-64">
    
    <!-- Logo & Close Button -->
    <div class="flex items-center justify-between w-full px-4 lg:px-0 lg:justify-center">
      <router-link to="/" class="group" @click="emit('close')">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center cursor-pointer transition-all duration-300 hover:rounded-xl hover:shadow-lg">
          <span class="text-white text-xl transition-transform group-hover:scale-110">🧘</span>
        </div>
      </router-link>
      
      <!-- Close Button - Alleen op mobile -->
      <button 
        @click="emit('close')"
        class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
      >
        <span class="text-xl">✕</span>
      </button>
    </div>
    
    <!-- Main Navigation -->
    <nav class="flex flex-col space-y-6 flex-1 w-full px-4 lg:px-0">
      <router-link 
        v-for="item in navigation" 
        :key="item.name"
        :to="item.path"
        class="relative group flex items-center lg:justify-center"
        @click="emit('close')"
      >
        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200 flex-shrink-0"
          :class="[
            $route.path === item.path 
              ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 shadow-sm' 
              : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300'
          ]"
        >
          <span class="text-2xl">{{ item.icon }}</span>
        </div>
        
        <!-- Text label - Alleen op mobile -->
        <span class="ml-4 lg:hidden text-gray-700 dark:text-gray-300 font-medium">
          {{ item.name }}
        </span>
        
        <!-- Tooltip - Alleen op desktop -->
        <div class="hidden lg:block absolute left-14 top-1/2 transform -translate-y-1/2 bg-gray-900 dark:bg-gray-700 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 whitespace-nowrap">
          {{ item.name }}
          <div class="absolute right-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-r-gray-900 dark:border-r-gray-700"></div>
        </div>
      </router-link>
    </nav>

    <!-- Bottom Actions -->
    <div class="flex flex-col space-y-4 w-full px-4 lg:px-0">
      <button @click="toggleTheme" class="w-12 h-12 lg:w-10 lg:h-10 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors mx-auto">
        <span class="text-xl">{{ themeIcon }}</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])

const navigation = [
  { name: 'Dashboard', path: '/', icon: '📊' },
  { name: 'Families', path: '/families', icon: '👨‍👩‍👧‍👦' },
  { name: 'Habits', path: '/habits', icon: '🌱' },
  { name: 'Boodschappen', path: '/shopping', icon: '🛍️' },
  { name: 'Todo\'s', path: '/todos', icon: '✅' },
  { name: 'Calendar', path: '/calendar', icon: '📅' },
]

const isDark = ref(false)

const themeIcon = computed(() => isDark.value ? '🌙' : '☀️')

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme') || 'light'
  isDark.value = savedTheme === 'dark'
  document.documentElement.classList.toggle('dark', isDark.value)
})
</script>