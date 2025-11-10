<!-- components/dashboard/TodayTasks.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vandaag's Taken</h3>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ completedTasks }}/{{ todayTasks.length }} voltooid</span>
    </div>
    
    <div class="space-y-3">
      <div v-for="task in todayTasks" :key="task.id" 
        class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
        <button @click="toggleTask(task.id)" 
          class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
          :class="task.completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-600'">
          <span v-if="task.completed" class="text-xs">✓</span>
        </button>
        
        <div class="flex-1 min-w-0">
          <div class="font-medium text-gray-900 dark:text-white" :class="{ 'line-through text-gray-400': task.completed }">
            {{ task.title }}
          </div>
        </div>
        
        <span class="text-xs px-2 py-1 rounded-full capitalize"
          :class="priorityClasses[task.priority]">
          {{ task.priority }}
        </span>
      </div>
    </div>
    <a href="/login#/todos">
      <button class="w-full mt-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
      + Nieuwe taak toevoegen
    </button>
    </a>
    
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => []
  }
})

const todayTasks = ref([...props.tasks])

const completedTasks = computed(() => {
  return todayTasks.value.filter(task => task.completed).length
})

const priorityClasses = {
  high: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
  medium: 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
  low: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
}

const toggleTask = (taskId) => {
  const task = todayTasks.value.find(t => t.id === taskId)
  if (task) {
    task.completed = !task.completed
  }
}
</script>