<!-- components/families/FamilyMembers.vue -->
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div v-for="member in members" :key="member.id" 
      class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-600">
      <div class="text-3xl mb-2">{{ member.avatar }}</div>
      <h3 class="font-medium text-gray-900 dark:text-white">{{ member.name }}</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ member.role }}</p>
      <div class="text-xs text-gray-400">{{ member.tasks }} taken</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  members: {
    type: Array,
    default: () => []
  }
})
</script>