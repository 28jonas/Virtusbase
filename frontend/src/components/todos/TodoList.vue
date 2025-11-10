<!-- components/todos/TodoList.vue -->
<template>
  <div class="space-y-3">
    <div v-for="todo in todos" :key="todo.id" 
      class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
      <button @click="$emit('toggle', todo)" 
        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
        :class="todo.completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-600'">
        <span v-if="todo.completed" class="text-xs">✓</span>
      </button>
      
      <div class="flex-1 min-w-0">
        <div class="font-medium text-gray-900 dark:text-white" :class="{ 'line-through text-gray-400': todo.completed }">
          {{ todo.title }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ todo.description }}</div>
        <div class="flex items-center space-x-2 mt-1">
          <span class="text-xs px-2 py-1 rounded-full capitalize"
            :class="priorityClasses[todo.priority]">
            {{ todo.priority }}
          </span>
          <span class="text-xs text-gray-400">{{ formatDate(todo.date) }}</span>
        </div>
      </div>
      
      <button @click="$emit('delete', todo.id)" class="text-gray-400 hover:text-red-600 dark:hover:text-red-300">
        🗑️
      </button>
    </div>
    
    <div v-if="todos.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
      Geen taken gevonden
    </div>
  </div>
</template>

<script setup>
defineProps({
  todos: {
    type: Array,
    default: () => []
  }
})

// Zorg dat de event names overeenkomen met wat in Todos.vue wordt gebruikt
defineEmits(['toggle', 'delete'])

const priorityClasses = {
  high: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
  medium: 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
  low: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Geen datum'
  return new Date(dateString).toLocaleDateString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}
</script>