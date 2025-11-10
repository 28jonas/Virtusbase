<template>
  <div class="px-8 py-4 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path v-if="type === 'income'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
    <p class="mt-2 text-gray-400 dark:text-gray-500">
      {{ type === 'income' ? 'No income data available yet.' : 'No data available yet.' }}
    </p>
    <button
      @click="$emit('add-item')"
      class="mt-4 px-4 py-2 text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
      :class="type === 'income' 
        ? 'bg-green-500 dark:bg-green-600 hover:bg-green-600 dark:hover:bg-green-700 focus:ring-green-400 dark:focus:ring-green-500' 
        : 'bg-red-500 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-700 focus:ring-red-400 dark:focus:ring-red-500'">
      {{ type === 'income' ? 'Add your first income' : 'Add your first item' }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'expense'
  }
})

defineEmits(['add-item'])
</script>