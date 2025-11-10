<template>
  <div class="col-span-4 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden h-full">
    <div v-if="nextMeal">
      <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-xl text-indigo-900 dark:text-indigo-200">{{ mealType }}</h3>
        <div class="text-indigo-500 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-full text-sm font-medium">{{ mealTime }}</div>
      </div>
      <div class="space-y-1">
        <div
          v-for="foodItem in nextMeal.food_items"
          :key="foodItem.id"
          class="flex items-center space-x-2 p-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors duration-200"
        >
          <div class="flex-1 min-w-0">
            <h4 class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ foodItem.name }}</h4>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1 text-indigo-400 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Protein {{ foodItem.protein }}g
              </span>
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1 text-amber-400 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 7 7 8c0 .657-.343 1.314-.686 1.686M17.657 18.657c.343-.372.686-.743.686-1.157s-.343-.785-.686-1.157m0 0c-.372-.343-1.014-.686-1.686-.686s-1.314.343-1.686.686"/>
                </svg>
                Fat {{ foodItem.fat }}g
              </span>
            </div>
          </div>
          <div class="text-indigo-500 dark:text-indigo-400 font-medium whitespace-nowrap">{{ foodItem.pivot.quantity || '1 portion' }}</div>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8">
      <div class="mx-auto w-16 h-16 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-indigo-300 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <p class="text-gray-500 dark:text-gray-400">No meals scheduled today</p>
      <button @click="$router.push('/meals')" class="mt-10 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-medium hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors duration-200">
        Create meal
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useMealViewerStore } from '../stores/mealViewerStore'

const store = useMealViewerStore()

const {
  nextMeal,
  mealType,
  mealTime,
  isLoading
} = storeToRefs(store)

const {
  loadNextMeal
} = store

onMounted(() => {
  loadNextMeal()
})
</script>