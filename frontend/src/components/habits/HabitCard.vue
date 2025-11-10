<!-- components/habits/HabitCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-lg">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <button @click="toggleCompletion" 
          class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-lg transition-colors"
          :class="isCompletedToday ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600'">
          <span v-if="isCompletedToday">✓</span>
        </button>
        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ habit.name }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ habit.description }}</p>
        </div>
      </div>
      <button @click="$emit('edit', habit)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
        ✏️
      </button>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4 text-sm">
        <div class="flex items-center space-x-1">
          <span>🔥</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ habit.current_streak || 0 }}</span>
          <span class="text-gray-500 dark:text-gray-400">dagen</span>
        </div>
        <div class="flex items-center space-x-1">
          <span>🔄</span>
          <span class="text-gray-500 dark:text-gray-400 capitalize">{{ habit.frequency }}</span>
        </div>
      </div>
      
      <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="h-2 rounded-full transition-all duration-500"
          :class="colorProgress[habit.color]"
          :style="{ width: `${Math.min(100, ((habit.currentStreak || 0) / 21) * 100)}%` }">
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useHabitStore } from '../../stores/habit'

const props = defineProps({
  habit: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['toggle', 'edit'])
const habitStore = useHabitStore()

// Bereken of de habit vandaag is voltooid
const isCompletedToday = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return habitStore.isHabitCompletedOnDate(props.habit, today)
})

const toggleCompletion = () => {
  const today = new Date().toISOString().split('T')[0]
  emit('toggle', props.habit.id, today)
}

const colorProgress = {
  blue: 'bg-blue-500',
  green: 'bg-green-500', 
  purple: 'bg-purple-500',
  orange: 'bg-orange-500'
}
</script>