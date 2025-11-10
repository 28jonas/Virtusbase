<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
      <div>
        <h1 class="text-3xl font-semibold text-indigo-900 dark:text-indigo-200">Food Items</h1>
        <p class="text-indigo-500 dark:text-indigo-400 mt-1">Manage your food library</p>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <input
            type="text"
            v-model="search"
            @input="handleSearch"
            placeholder="Search food items..."
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 dark:bg-gray-700 dark:text-gray-200 px-4 py-2"
          >
        </div>
        <div class="flex items-center">
          <label class="inline-flex items-center">
            <input type="checkbox" v-model="showPublic" @change="loadFoodItems" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 dark:bg-gray-700">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show public items</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Food Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="foodItem in foodItems"
        :key="foodItem.id"
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-gray-700"
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ foodItem.name }}</h3>
          <button
            @click="deleteFoodItem(foodItem.id)"
            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>

        <div class="space-y-3">
          <div class="flex justify-between text-sm">
            <span class="text-gray-500 dark:text-gray-400">Serving:</span>
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ foodItem.serving_size }}{{ foodItem.serving_unit }}</span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="text-center bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2">
              <div class="font-semibold text-indigo-700 dark:text-indigo-300">{{ foodItem.calories }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Calories</div>
            </div>
            <div class="text-center bg-green-50 dark:bg-green-900/20 rounded-lg p-2">
              <div class="font-semibold text-green-700 dark:text-green-300">{{ foodItem.protein }}g</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Protein</div>
            </div>
            <div class="text-center bg-amber-50 dark:bg-amber-900/20 rounded-lg p-2">
              <div class="font-semibold text-amber-700 dark:text-amber-300">{{ foodItem.fat }}g</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Fat</div>
            </div>
            <div class="text-center bg-blue-50 dark:bg-blue-900/20 rounded-lg p-2">
              <div class="font-semibold text-blue-700 dark:text-blue-300">{{ foodItem.carbs }}g</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">Carbs</div>
            </div>
          </div>

          <div v-if="foodItem.is_public" class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-xs">
            Public
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="foodItems.length === 0 && !isLoading" class="text-center py-12">
      <div class="mx-auto w-16 h-16 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-indigo-300 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <p class="text-gray-500 dark:text-gray-400">No food items found.</p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-8">
      <div class="inline-flex items-center">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mr-2"></div>
        <span class="text-gray-500 dark:text-gray-400">Loading food items...</span>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" class="flex justify-center">
      <nav class="flex items-center space-x-2">
        <button
          v-for="page in lastPage"
          :key="page"
          @click="loadFoodItems(page)"
          :class="[
            'px-3 py-1 rounded-lg text-sm font-medium transition-colors',
            currentPage === page
              ? 'bg-indigo-600 text-white'
              : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'
          ]"
        >
          {{ page }}
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useFoodItemsStore } from '../stores/foodItemsStore'

const store = useFoodItemsStore()

const {
  foodItems,
  search,
  showPublic,
  isLoading,
  currentPage,
  lastPage
} = storeToRefs(store)

const {
  loadFoodItems,
  deleteFoodItem
} = store

const handleSearch = () => {
  loadFoodItems(1)
}

onMounted(() => {
  loadFoodItems()
})

watch(showPublic, () => {
  loadFoodItems(1)
})
</script>