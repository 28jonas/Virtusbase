<!-- components/habits/HabitCalendar.vue -->
<template>
  <div class="space-y-4">
    <div class="flex space-x-2 overflow-x-auto pb-2">
      <div v-for="day in last7Days" :key="day.date" 
        class="flex-shrink-0 w-24 bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ day.day }}</div>
        <div class="text-xs text-gray-400 mb-2">{{ day.date }}</div>
        <div class="text-lg">✅</div>
      </div>
    </div>
    
    <div class="grid grid-cols-7 gap-1">
      <div v-for="day in calendarDays" :key="day" 
        class="aspect-square rounded-lg flex items-center justify-center text-sm"
        :class="getDayClass(day)">
        {{ day }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  habits: {
    type: Array,
    default: () => []
  }
})

const last7Days = computed(() => {
  const days = []
  for (let i = 6; i >= 0; i--) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    days.push({
      day: date.toLocaleDateString('nl-NL', { weekday: 'short' }),
      date: date.getDate()
    })
  }
  return days
})

const calendarDays = computed(() => {
  return Array.from({ length: 31 }, (_, i) => i + 1)
})

const getDayClass = (day) => {
  const completed = day % 3 === 0 // Simulatie van voltooide dagen
  return completed 
    ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' 
    : 'bg-gray-100 dark:bg-gray-700 text-gray-400'
}
</script>