<!-- components/dashboard/MiniCalendar.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vandaag</h3>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ currentDate }}</span>
    </div>
    
    <div class="space-y-3">
      <div v-for="event in events" :key="event.id" 
        class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
        <div class="w-2 h-8 rounded-full" :class="eventTypeClasses[event.type]"></div>
        <div class="flex-1">
          <div class="font-medium text-gray-900 dark:text-white">{{ event.title }}</div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ event.time }}</div>
        </div>
      </div>
    </div>
    
    <button class="w-full mt-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
      Bekijk volledige kalender
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  events: {
    type: Array,
    default: () => []
  }
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('nl-NL', { 
    day: 'numeric', 
    month: 'long' 
  })
})

const eventTypeClasses = {
  work: 'bg-blue-500',
  personal: 'bg-green-500',
  family: 'bg-purple-500'
}
</script>