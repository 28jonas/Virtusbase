<!-- components/calendar/EventList.vue -->
<template>
  <div class="space-y-4">
    <div v-for="event in events" :key="event.id" 
      class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
      <div class="w-3 h-3 rounded-full" :class="eventTypeClasses[event.type]"></div>
      <div class="flex-1">
        <div class="font-medium text-gray-900 dark:text-white">{{ event.title }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ formatDate(event.date) }} om {{ event.time }}
        </div>
      </div>
      <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
        ✏️
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  events: {
    type: Array,
    default: () => []
  }
})

const eventTypeClasses = {
  work: 'bg-blue-500',
  personal: 'bg-green-500',
  health: 'bg-red-500',
  family: 'bg-purple-500'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('nl-NL')
}
</script>