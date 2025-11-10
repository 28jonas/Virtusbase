<!-- components/dashboard/QuickActions.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Snelle Acties</h3>
    
    <div class="grid grid-cols-2 gap-4">
      <button v-for="action in quickActions" :key="action.name" 
        @click="action.handler"
        class="p-4 rounded-xl border border-gray-100 dark:border-gray-700 transition-all duration-200 hover:shadow-md hover:scale-105 group">
        <div class="text-2xl mb-2 transition-transform group-hover:scale-110">{{ action.icon }}</div>
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ action.name }}</div>
      </button>
    </div>
  </div>
</template>

<script setup>
const quickActions = [
  { name: 'Nieuwe Taak', icon: '📝', handler: () => console.log('Nieuwe taak') },
  { name: 'Gewoonte', icon: '🌱', handler: () => console.log('Nieuwe gewoonte') },
  { name: 'Boodschap', icon: '🛒', handler: () => console.log('Nieuwe boodschap') },
  { name: 'Familie', icon: '👨‍👩‍👧‍👦', handler: () => console.log('Nieuwe familie') },
  { name: 'Event', icon: '📅', handler: () => console.log('Nieuw event') },
  { name: 'Notitie', icon: '📋', handler: () => console.log('Nieuwe notitie') }
]
</script>