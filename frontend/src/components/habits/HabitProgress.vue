<!-- components/habits/HabitProgress.vue -->
<template>
  <div class="space-y-6">
    <!-- Weekly Progress -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Deze Week</h3>
      <div class="space-y-4">
        <div v-for="habit in habits" :key="habit.id" class="flex items-center justify-between">
          <div class="flex items-center space-x-3 flex-1">
            <span class="text-lg">{{ getHabitEmoji(habit.category) }}</span>
            <div class="flex-1">
              <div class="font-medium text-gray-900 dark:text-white">{{ habit.name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ habit.current_streak || 0 }} dagen streak
              </div>
            </div>
          </div>
          <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 bg-green-500"
                 :style="{ width: `${getWeeklyProgress(habit)}%` }">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Completion Heatmap -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activiteit Heatmap</h3>
      <div class="flex space-x-1">
        <div v-for="day in last30Days" :key="day.date" 
             class="flex-1 aspect-square rounded-sm transition-all hover:scale-110"
             :class="getHeatmapColor(day.completionCount)"
             :title="`${day.date}: ${day.completionCount} gewoontes voltooid`">
        </div>
      </div>
      <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
        <span>30 dagen terug</span>
        <span>Vandaag</span>
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

const getHabitEmoji = (category) => {
  const emojis = {
    'health': '💪',
    'work': '💼', 
    'personal': '🌟',
    'fitness': '🏃',
    'learning': '📚'
  }
  return emojis[category] || '🌱'
}

const getWeeklyProgress = (habit) => {
  // Simpele progress berekening - aantal voltooide dagen deze week
  const today = new Date()
  const weekStart = new Date(today.setDate(today.getDate() - today.getDay()))
  
  const completionsThisWeek = habit.completions?.filter(c => {
    const completionDate = new Date(c.completion_date)
    return completionDate >= weekStart
  }).length || 0
  
  return Math.min(100, (completionsThisWeek / 7) * 100)
}

const last30Days = computed(() => {
  const days = []
  const today = new Date()
  
  for (let i = 29; i >= 0; i--) {
    const date = new Date()
    date.setDate(today.getDate() - i)
    const dateString = date.toISOString().split('T')[0]
    
    const completionCount = props.habits.reduce((count, habit) => {
      const isCompleted = habit.completions?.some(c => c.completion_date === dateString)
      return count + (isCompleted ? 1 : 0)
    }, 0)
    
    days.push({
      date: date.toLocaleDateString('nl-NL', { day: 'numeric', month: 'short' }),
      dateString,
      completionCount
    })
  }
  
  return days
})

const getHeatmapColor = (completionCount) => {
  if (completionCount === 0) return 'bg-gray-100 dark:bg-gray-700'
  if (completionCount === 1) return 'bg-green-200 dark:bg-green-800'
  if (completionCount === 2) return 'bg-green-400 dark:bg-green-600'
  return 'bg-green-600 dark:bg-green-400'
}
</script>