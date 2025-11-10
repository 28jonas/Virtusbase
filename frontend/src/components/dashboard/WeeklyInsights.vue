<!-- components/dashboard/WeeklyInsights.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Week Overzicht</h3>
      <span class="text-sm text-green-600 dark:text-green-400">+12% ↗</span>
    </div>
    
    <div class="space-y-6">
      <!-- Productivity Chart -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm text-gray-600 dark:text-gray-400">Productiviteit</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">78%</span>
        </div>
        <div class="flex items-end justify-between h-8">
          <div v-for="(value, index) in data.productivity" :key="index" 
            class="flex-1 mx-1 bg-blue-500 rounded-t transition-all duration-500 ease-out hover:bg-blue-600"
            :style="{ height: `${value}%` }"
            :title="`${value}%`">
          </div>
        </div>
      </div>
      
      <!-- Habits Chart -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm text-gray-600 dark:text-gray-400">Gewoontes</span>
          <span class="text-sm font-medium text-gray-900 dark:text-white">85%</span>
        </div>
        <div class="flex items-end justify-between h-8">
          <div v-for="(value, index) in data.habits" :key="index" 
            class="flex-1 mx-1 bg-green-500 rounded-t transition-all duration-500 ease-out hover:bg-green-600"
            :style="{ height: `${value}%` }"
            :title="`${value}%`">
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
      <div class="text-center text-sm text-gray-500 dark:text-gray-400">
        Gemiddelde deze week: <span class="font-medium text-gray-900 dark:text-white">82%</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  data: {
    type: Object,
    default: () => ({})
  }
})
</script>