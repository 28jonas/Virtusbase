<!-- components/families/FamilyCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl"
          :class="colorClasses[family.color]">
          👨‍👩‍👧‍👦
        </div>
        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ family.name }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ family.memberCount }} leden</p>
        </div>
      </div>
      <div class="flex space-x-2">
        <button @click="$emit('edit', family)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
          ✏️
        </button>
        <button @click="$emit('delete', family)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
          🗑️
        </button>
      </div>
    </div>

    <!-- Progress -->
    <div class="mb-4">
      <div class="flex justify-between text-sm mb-2">
        <span class="text-gray-500 dark:text-gray-400">Taken voltooid</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ family.completedTasks }}/{{ family.tasks }}</span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="h-2 rounded-full transition-all duration-500"
          :class="colorProgress[family.color]"
          :style="{ width: `${(family.completedTasks / family.tasks) * 100}%` }">
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex space-x-2">
      <button class="flex-1 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
        Taken
      </button>
      <button class="flex-1 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
        Leden
      </button>
      <button class="flex-1 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
        Instellingen
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  family: {
    type: Object,
    required: true
  }
})

defineEmits(['edit', 'delete'])

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
</script>