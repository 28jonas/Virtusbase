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
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Add Food Item</h1>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form @submit.prevent="handleSaveFoodItem">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Name -->
                <div class="md:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                  <input type="text" id="name" v-model="form.name" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="E.g. Whole milk">
                  <div v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name[0] }}</div>
                </div>

                <!-- Serving Size -->
                <div>
                  <label for="servingSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serving Size</label>
                  <input type="number" step="0.01" id="servingSize" v-model="form.servingSize" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="100">
                  <div v-if="errors.servingSize" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.servingSize[0] }}</div>
                </div>

                <!-- Serving Unit -->
                <div>
                  <label for="servingUnit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Serving Unit</label>
                  <select id="servingUnit" v-model="form.servingUnit" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                    <option value="g">gram (g)</option>
                    <option value="ml">milliliter (ml)</option>
                    <option value="stuk">piece</option>
                    <option value="portie">portion</option>
                  </select>
                  <div v-if="errors.servingUnit" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.servingUnit[0] }}</div>
                </div>

                <!-- Image -->
                <div class="md:col-span-2">
                  <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image (optional)</label>
                  <input type="file" id="image" @change="handleImageUpload" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                  <div v-if="errors.image" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.image[0] }}</div>
                </div>

                <!-- Nutrition Values -->
                <div>
                  <label for="calories" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Calories (kcal)</label>
                  <input type="number" step="0.1" id="calories" v-model="form.calories" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.calories" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.calories[0] }}</div>
                </div>

                <div>
                  <label for="protein" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Protein (g)</label>
                  <input type="number" step="0.1" id="protein" v-model="form.protein" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.protein" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.protein[0] }}</div>
                </div>

                <div>
                  <label for="fat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fat (g)</label>
                  <input type="number" step="0.1" id="fat" v-model="form.fat" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.fat" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.fat[0] }}</div>
                </div>

                <div>
                  <label for="carbs" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carbs (g)</label>
                  <input type="number" step="0.1" id="carbs" v-model="form.carbs" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.carbs" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.carbs[0] }}</div>
                </div>

                <div>
                  <label for="fiber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fiber (g)</label>
                  <input type="number" step="0.1" id="fiber" v-model="form.fiber" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.fiber" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.fiber[0] }}</div>
                </div>

                <div>
                  <label for="sugar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sugar (g)</label>
                  <input type="number" step="0.1" id="sugar" v-model="form.sugar" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.sugar" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.sugar[0] }}</div>
                </div>

                <div>
                  <label for="sodium" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sodium (mg)</label>
                  <input type="number" step="1" id="sodium" v-model="form.sodium" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200" placeholder="0">
                  <div v-if="errors.sodium" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.sodium[0] }}</div>
                </div>

                <!-- Public Access -->
                <div class="md:col-span-2">
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.isPublic" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make this food item public</span>
                  </label>
                  <div v-if="errors.isPublic" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.isPublic[0] }}</div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" :disabled="isLoading" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                  {{ isLoading ? 'Saving...' : 'Save' }}
                </button>
                <button type="button" @click="$emit('close')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                  Cancel
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
import { storeToRefs } from 'pinia'
import { useAddFoodItemStore } from '../../stores/addFoodItemStore'

const emit = defineEmits(['close', 'saved'])

const store = useAddFoodItemStore()

const {
  isLoading,
  errors,
  form
} = storeToRefs(store)

const {
  saveFoodItem,
  resetForm
} = store

const handleImageUpload = (event) => {
  form.value.image = event.target.files[0]
}

const handleSaveFoodItem = async () => {
  try {
    await saveFoodItem()
    emit('saved')
    emit('close')
  } catch (error) {
    console.error('Failed to save food item:', error)
  }
}

// Reset form when component is unmounted
import { onUnmounted } from 'vue'
onUnmounted(() => {
  resetForm()
})
</script>