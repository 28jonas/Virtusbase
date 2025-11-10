<!-- components/dashboard/HabitProgress.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gewoontes Vandaag</h3>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ completedHabits }}/{{ habits.length }} voltooid</span>
    </div>
    
    <div class="space-y-4">
      <div v-for="habit in habits" :key="habit.id" 
        class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <button @click="toggleHabit(habit.id)" 
            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
            :class="habit.completed ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400'">
            <span v-if="habit.completed" class="text-sm">✓</span>
          </button>
          
          <div>
            <div class="font-medium text-gray-900 dark:text-white">{{ habit.name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ habit.streak }} dagen streak</div>
          </div>
        </div>
        
        <div class="text-right">
          <div class="text-sm font-medium text-gray-900 dark:text-white"
            :class="habit.completed ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">
            {{ habit.completed ? 'Voltooid' : 'Nog te doen' }}
          </div>
        </div>
      </div>
    </div>
    
    <button class="w-full mt-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
      + Gewoonte toevoegen
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  habits: {
    type: Array,
    default: () => []
  }
})

const habits = ref([...props.habits])

const completedHabits = computed(() => {
  return habits.value.filter(habit => habit.completed).length
})

const toggleHabit = (habitId) => {
  const habit = habits.value.find(h => h.id === habitId)
  if (habit) {
    habit.completed = !habit.completed
    habit.streak = habit.completed ? habit.streak + 1 : Math.max(0, habit.streak - 1)
  }
}
</script>