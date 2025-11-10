<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl"
          :class="colorClasses[list.color]">
          🛒
        </div>
        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ list.name }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(list.date) }}</p>
        </div>
      </div>
      <div class="flex space-x-2">
        <button @click="$emit('edit', list)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
          ✏️
        </button>
        <button @click="$emit('delete', list)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
          🗑️
        </button>
      </div>
    </div>

    <!-- Progress -->
    <div class="mb-4">
      <div class="flex justify-between text-sm mb-2">
        <span class="text-gray-500 dark:text-gray-400">Items voltooid</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ completedItems }}/{{ totalItems }}</span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="h-2 rounded-full transition-all duration-500"
          :class="colorProgress[list.color]"
          :style="{ width: progressWidth }">
        </div>
      </div>
    </div>

    <button class="w-full py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
      Lijst openen
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  list: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete'])

// Bereken de progress statistics
const completedItems = computed(() => {
  if (!props.list.items || !Array.isArray(props.list.items)) return 0
  return props.list.items.filter(item => item.is_completed).length
})

const totalItems = computed(() => {
  if (!props.list.items || !Array.isArray(props.list.items)) return 0
  return props.list.items.length
})

const progressWidth = computed(() => {
  if (totalItems.value === 0) return '0%'
  const percentage = (completedItems.value / totalItems.value) * 100
  return `${Math.min(100, percentage)}%`
})

const colorClasses = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  purple: 'bg-purple-500',
  orange: 'bg-orange-500'
}

const colorProgress = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  purple: 'bg-purple-500',
  orange: 'bg-orange-500'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('nl-NL')
}
</script>