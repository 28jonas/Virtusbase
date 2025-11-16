<!-- components/dashboard/KPICard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-md hover:scale-[1.02] group">
    <div class="flex items-center justify-between mb-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl"
        :class="colorClasses[color]">
        {{ icon }}
      </div>
      <div class="text-sm font-medium px-2 py-1 rounded-full"
        :class="trendColor">
        {{ trend }}
      </div>
    </div>
    
    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-2">{{ title }}</h3>
    <div class="text-3xl font-light text-gray-900 dark:text-white mb-1">{{ value }}</div>
    <p class="text-gray-400 dark:text-gray-500 text-sm">{{ subtitle }}</p>
    
    <!-- Progress bar -->
    <!-- <div class="mt-4 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1">
      <div class="h-1 rounded-full transition-all duration-1000 ease-out"
        :class="colorProgress[color]"
        :style="{ width: '70%' }"></div>
    </div> -->
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  subtitle: String,
  icon: String,
  trend: String,
  color: {
    type: String,
    default: 'blue'
  }
})

const colorClasses = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  orange: 'bg-orange-500',
  purple: 'bg-purple-500'
}

const colorProgress = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  orange: 'bg-orange-500',
  purple: 'bg-purple-500'
}

const trendColor = 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20'
</script>