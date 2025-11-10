<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75 dark:opacity-80"></div>
      </div>

      <!-- Modal Container -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal Content -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Add Meal</h1>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <form @submit.prevent="handleSaveMeal">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label for="meal-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meal type</label>
                  <select id="meal-type" v-model="meal.meal_type_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Select type</option>
                    <option v-for="type in mealTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                  </select>
                  <div v-if="errors.meal_type_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.meal_type_id[0] }}</div>
                </div>

                <div>
                  <label for="meal-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                  <input type="text" id="meal-name" v-model="meal.name" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="E.g. Breakfast with oatmeal">
                  <div v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name[0] }}</div>
                </div>

                <div>
                  <label for="meal-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                  <input type="date" id="meal-date" v-model="meal.date" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                  <div v-if="errors.date" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.date[0] }}</div>
                </div>

                <div>
                  <label for="meal-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                  <input type="time" id="meal-time" v-model="meal.time" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                  <div v-if="errors.time" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.time[0] }}</div>
                </div>

                <div class="md:col-span-2">
                  <label for="meal-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                  <textarea id="meal-notes" v-model="meal.notes" rows="2" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="Optional notes about this meal"></textarea>
                  <div v-if="errors.notes" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.notes[0] }}</div>
                </div>
              </div>

              <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add meal items</h3>

                <div class="flex flex-col md:flex-row md:space-x-4 mb-6">
                  <div class="flex-1 mb-4 md:mb-0">
                    <label for="food-item-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search meal items</label>
                    <input type="text" id="food-item-search" v-model="search" @input="handleSearch" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="Search by name...">
                  </div>

                  <div class="w-full md:w-1/4">
                    <label for="food-item" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select item</label>
                    <select id="food-item" v-model="newFoodItem.id" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                      <option value="">Select item</option>
                      <option v-for="item in availableFoodItems" :key="item.id" :value="item.id">
                        {{ item.name }} ({{ item.serving_size }}{{ item.serving_unit }})
                      </option>
                    </select>
                    <div v-if="errors.foodItem" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.foodItem[0] }}</div>
                  </div>

                  <div class="w-full md:w-1/4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                    <div class="flex">
                      <input type="number" step="0.01" id="quantity" v-model="newFoodItem.quantity" class="w-full rounded-l-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="1">
                      <button type="button" @click="addFoodItem" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Add
                      </button>
                    </div>
                    <div v-if="errors.quantity" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.quantity[0] }}</div>
                  </div>
                </div>

                <div v-if="errors.foodItems" class="mb-4 text-sm text-red-600 dark:text-red-400">{{ errors.foodItems[0] }}</div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">Selected food items</h4>

                  <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    <div v-for="(item, index) in selectedFoodItems" :key="index" class="py-3 flex items-center space-x-4">
                      <div class="flex-1">
                        <h5 class="font-medium dark:text-gray-200">{{ item.name }}</h5>
                        <div class="flex flex-wrap text-sm text-gray-500 dark:text-gray-400 gap-x-4 mt-1">
                          <span>{{ item.quantity }} {{ item.serving_unit }}</span>
                          <span>{{ formatNumber(item.calories * item.quantity, 0) }} kcal</span>
                          <span>P: {{ formatNumber(item.protein * item.quantity, 1) }}g</span>
                          <span>F: {{ formatNumber(item.fat * item.quantity, 1) }}g</span>
                          <span>C: {{ formatNumber(item.carbs * item.quantity, 1) }}g</span>
                        </div>
                      </div>
                      <div class="flex items-center">
                        <input type="number" step="0.01" v-model="item.quantity" @change="updateFoodItemQuantity(index, $event.target.value)" class="w-20 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 mr-2 dark:bg-gray-700 dark:text-gray-200">
                        <button type="button" @click="removeFoodItem(index)" class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                          </svg>
                        </button>
                      </div>
                    </div>

                    <div v-if="selectedFoodItems.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">
                      <p>No food items added.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit" :disabled="isLoading" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                  {{ isLoading ? 'Saving...' : 'Save meal' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useAddMealStore } from '../../stores/addMealStore'
import { useMealTrackingStore } from '../../stores/mealTrackingStore'

const emit = defineEmits(['close', 'saved'])

const store = useAddMealStore()
const mealTrackingStore = useMealTrackingStore()

const {
  mealTypes,
  availableFoodItems,
  selectedFoodItems,
  search,
  isLoading,
  errors,
  meal,
  newFoodItem
} = storeToRefs(store)

const {
  loadMealTypes,
  loadAvailableFoodItems,
  addFoodItem,
  removeFoodItem,
  saveMeal
} = store

const handleSearch = () => {
  loadAvailableFoodItems()
}

const updateFoodItemQuantity = (index, quantity) => {
  selectedFoodItems.value[index].quantity = parseFloat(quantity) || 1
}

const handleSaveMeal = async () => {
  try {
    await saveMeal()
    
    // Herlaad meals voor de datum die in de modal is geselecteerd
    await mealTrackingStore.loadMealsForDate(meal.value.date)
    
    emit('saved')
    emit('close')
  } catch (error) {
    console.error('Failed to save meal:', error)
  }
}

const formatNumber = (number, decimals = 0) => {
  return number.toLocaleString('en', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  })
}

onMounted(() => {
  loadMealTypes()
  loadAvailableFoodItems()
})

watch(search, () => {
  loadAvailableFoodItems()
})
</script>