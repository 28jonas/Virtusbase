<!-- components/layout/Header.vue -->
<template>
  <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ currentRouteTitle }}
        </h1>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ currentDate }}
        </div>
      </div>
      
      <div class="flex items-center space-x-4">
        <button class="w-10 h-10 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
          <span class="text-xl">🔔</span>
        </button>
        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
          {{ userInitials }}
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const currentRouteTitle = computed(() => {
  const routeName = route.name
  const titles = {
    'dashboard': 'Dashboard',
    'families': 'Familie Beheer',
    'habits': 'Gewoontes',
    'shopping': 'Boodschappen',
    'todos': 'Todo\'s',
    'calendar': 'Kalender'
  }
  return titles[routeName] || 'Life Manager'
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('nl-NL', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
})

const userInitials = 'JA' // Dit zou uit je auth komen
</script>