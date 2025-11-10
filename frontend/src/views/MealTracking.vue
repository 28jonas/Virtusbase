<template>
  <div class="space-y-6">
    <!-- Header section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
      <div>
        <h1 class="text-3xl font-semibold text-indigo-900 dark:text-indigo-200">Food Diary</h1>
        <p class="text-indigo-500 dark:text-indigo-400 mt-1">Your daily nutrition insights</p>
      </div>
      <div class="flex flex-wrap gap-3">
        <button @click="openMealModal" class="flex items-center px-5 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl font-medium text-white hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-sm hover:shadow-md">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Add meal
        </button>
        <button @click="openFoodItemModal" class="flex items-center px-5 py-3 bg-white dark:bg-gray-700 border border-indigo-200 dark:border-gray-600 rounded-xl font-medium text-indigo-600 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-gray-600 transition-all shadow-sm hover:shadow-md">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Add food item
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="lg:col-span-8 space-y-6">
        <!-- Date navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-gray-700">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <button @click="previousDay" :disabled="isLoading" class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-colors disabled:opacity-50">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Previous day
            </button>

            <div class="flex items-center gap-3">
              <span class="text-lg font-medium text-indigo-900 dark:text-indigo-200">{{ formatDate(date) }}</span>
              <button @click="setToday" :disabled="isLoading" class="px-4 py-1.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm font-medium hover:bg-indigo-200 dark:hover:bg-indigo-800 transition-colors disabled:opacity-50">
                Today
              </button>
            </div>

            <button @click="nextDay" :disabled="isLoading" class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-colors disabled:opacity-50">
              Next day
              <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Meals grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div
            v-for="mealType in mealTypes"
            :key="mealType.id"
            class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-gray-700"
          >
            <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-gray-700">
              <h3 class="font-semibold text-lg text-indigo-900 dark:text-indigo-200">{{ mealType.name }}</h3>
            </div>

            <div class="space-y-5">
              <div
                v-for="meal in getMealsByType(mealType.id)"
                :key="meal.id"
                class="pb-5 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0"
              >
                <div class="flex justify-between items-center mb-3">
                  <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ meal.name }}</h4>
                  <span class="text-sm bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-full">{{ formatTime(meal.time) }}</span>
                </div>

                <div
                  v-for="foodItem in meal.food_items"
                  :key="foodItem.id"
                  class="flex items-start gap-3 mb-3 p-2 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                >
                  <div class="flex-1 min-w-0">
                    <h5 class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ foodItem.name }}</h5>
                    <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400 mt-1">
                      <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ foodItem.pivot.quantity }} {{ foodItem.serving_unit }}</span>
                      <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-indigo-400 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{ formatNumber(foodItem.protein * foodItem.pivot.quantity, 1) }}g
                      </span>
                      <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-amber-400 dark:text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 7 7 8c0 .657-.343 1.314-.686 1.686M17.657 18.657c.343-.372.686-.743.686-1.157s-.343-.785-.686-1.157m0 0c-.372-.343-1.014-.686-1.686-.686s-1.314.343-1.686.686"/>
                        </svg>
                        {{ formatNumber(foodItem.fat * foodItem.pivot.quantity, 1) }}g
                      </span>
                      <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-green-400 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ formatNumber(foodItem.carbs * foodItem.pivot.quantity, 1) }}g
                      </span>
                    </div>
                  </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                  <div class="flex justify-between text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Total:</span>
                    <div class="flex flex-wrap gap-3 text-gray-600 dark:text-gray-400">
                      <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ formatNumber(meal.total_nutrition.calories, 0) }} kcal</span>
                      <span>P: {{ formatNumber(meal.total_nutrition.protein, 1) }}g</span>
                      <span>F: {{ formatNumber(meal.total_nutrition.fat, 1) }}g</span>
                      <span>C: {{ formatNumber(meal.total_nutrition.carbs, 1) }}g</span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="getMealsByType(mealType.id).length === 0" class="text-center py-6">
                <div class="mx-auto w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-3">
                  <svg class="w-6 h-6 text-indigo-300 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">No meals for {{ mealType.name }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary sidebar -->
      <div class="lg:col-span-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-gray-100 dark:border-gray-700">
          <h3 class="font-semibold text-lg text-indigo-900 dark:text-indigo-200 mb-2">Nutrition Summary</h3>
          <p class="text-indigo-500 dark:text-indigo-400 text-sm mb-6">Overview of your daily intake</p>

          <!-- Macro ring visualization -->
          <div class="relative h-40 w-40 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full border-8 border-indigo-100 dark:border-indigo-900/30"></div>
            <div class="absolute inset-0 rounded-full border-8 border-indigo-200 dark:border-indigo-700" style="clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);"></div>
            <div class="absolute inset-0 rounded-full border-8 border-amber-200 dark:border-amber-700" style="clip-path: polygon(50% 0, 100% 0, 100% 100%, 50% 100%);"></div>
            <div class="absolute inset-0 rounded-full border-8 border-green-200 dark:border-green-700" style="clip-path: polygon(0 0, 100% 0, 100% 50%, 0 50%);"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-2xl font-bold text-indigo-900 dark:text-indigo-200">{{ formatNumber(dailyTotals.calories, 0) }}</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">kcal</span>
            </div>
          </div>

          <!-- Nutrition values grid -->
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
              <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-xs font-medium">Calories</span>
              </div>
              <div class="text-xl font-bold text-indigo-900 dark:text-indigo-200">{{ formatNumber(dailyTotals.calories, 0) }} <span class="text-sm font-normal text-indigo-600 dark:text-indigo-400">kcal</span></div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-3 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
              <div class="flex items-center gap-2 text-green-600 dark:text-green-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-xs font-medium">Protein</span>
              </div>
              <div class="text-xl font-bold text-green-900 dark:text-green-200">{{ formatNumber(dailyTotals.protein, 1) }} <span class="text-sm font-normal text-green-600 dark:text-green-400">g</span></div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
              <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 7 7 8c0 .657-.343 1.314-.686 1.686M17.657 18.657c.343-.372.686-.743.686-1.157s-.343-.785-.686-1.157m0 0c-.372-.343-1.014-.686-1.686-.686s-1.314.343-1.686.686"/>
                </svg>
                <span class="text-xs font-medium">Fats</span>
              </div>
              <div class="text-xl font-bold text-amber-900 dark:text-amber-200">{{ formatNumber(dailyTotals.fat, 1) }} <span class="text-sm font-normal text-amber-600 dark:text-amber-400">g</span></div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
              <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-xs font-medium">Carbs</span>
              </div>
              <div class="text-xl font-bold text-blue-900 dark:text-blue-200">{{ formatNumber(dailyTotals.carbs, 1) }} <span class="text-sm font-normal text-blue-600 dark:text-blue-400">g</span></div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-3 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
              <div class="flex items-center gap-2 text-purple-600 dark:text-purple-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="text-xs font-medium">Fiber</span>
              </div>
              <div class="text-xl font-bold text-purple-900 dark:text-purple-200">{{ formatNumber(dailyTotals.fiber, 1) }} <span class="text-sm font-normal text-purple-600 dark:text-purple-400">g</span></div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
              <div class="flex items-center gap-2 text-red-600 dark:text-red-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-medium">Sugar</span>
              </div>
              <div class="text-xl font-bold text-red-900 dark:text-red-200">{{ formatNumber(dailyTotals.sugar, 1) }} <span class="text-sm font-normal text-red-600 dark:text-red-400">g</span></div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-xs font-medium">Sodium</span>
              </div>
              <div class="text-xl font-bold text-gray-900 dark:text-gray-200">{{ formatNumber(dailyTotals.sodium, 0) }} <span class="text-sm font-normal text-gray-600 dark:text-gray-400">mg</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddMealModal v-if="showMealModal" @close="closeMealModal" />
    <AddFoodItemModal v-if="showFoodItemModal" @close="closeFoodItemModal" />
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useMealTrackingStore } from '../stores/mealTrackingStore'
import AddMealModal from '../components/food/AddMealModel.vue'
import AddFoodItemModal from '../components/food/AddFoodItemModal.vue'

const store = useMealTrackingStore()

const {
  date,
  meals,
  mealTypes,
  showMealModal,
  showFoodItemModal,
  isLoading,
  dailyTotals
} = storeToRefs(store)

const {
  loadMeals,
  loadMealTypes,
  previousDay,
  nextDay,
  setToday,
  openMealModal,
  closeMealModal,
  openFoodItemModal,
  closeFoodItemModal
} = store

const getMealsByType = (mealTypeId) => {
  return meals.value.filter(meal => meal.meal_type_id === mealTypeId)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const formatTime = (timeString) => {
  return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  })
}

const formatNumber = (number, decimals = 0) => {
  return number.toLocaleString('en', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  })
}

onMounted(() => {
  loadMeals()
  loadMealTypes()
})
</script>