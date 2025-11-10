<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Health Goals</h3>
      <button
        @click="openModal"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Add Goal +
      </button>
    </div>

    <div class="space-y-4">
      <div
        v-for="goal in goals"
        :key="goal.id"
        class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600"
      >
        <div class="flex justify-between items-start mb-2">
          <div class="flex items-center">
            <i :class="['fas', goal.icon_class, 'mr-2']" :style="{ color: goal.color }"></i>
            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ goal.title }}</h4>
          </div>
          <button
            @click="deleteGoal(goal.id)"
            class="text-red-500 hover:text-red-700"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
        
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
          <span>{{ goal.current }} / {{ goal.target }}</span>
          <span>{{ goal.progress }}%</span>
        </div>
        
        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
          <div
            class="h-2 rounded-full transition-all duration-500"
            :style="{
              width: goal.progress + '%',
              backgroundColor: goal.color
            }"
          ></div>
        </div>
        
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
          Target: {{ new Date(goal.date).toLocaleDateString() }}
        </div>
      </div>
    </div>

    <!-- Add Goal Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg dark:text-gray-200">Add Health Goal</h3>
          <button @click="closeModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="addGoal">
          <div class="space-y-4">
            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2">Title</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                required
              >
              <div v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Current</label>
                <input
                  v-model="form.current"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                  required
                >
                <div v-if="errors.current" class="text-red-500 text-sm mt-1">{{ errors.current[0] }}</div>
              </div>
              <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-2">Target</label>
                <input
                  v-model="form.target"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                  required
                >
                <div v-if="errors.target" class="text-red-500 text-sm mt-1">{{ errors.target[0] }}</div>
              </div>
            </div>

            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2">Goal Date</label>
              <input
                v-model="form.goalDate"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                required
              >
              <div v-if="errors.goalDate" class="text-red-500 text-sm mt-1">{{ errors.goalDate[0] }}</div>
            </div>

            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2">Type</label>
              <select
                v-model="form.type"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                required
              >
                <option value="">Select Type</option>
                <option value="Weight">Weight</option>
                <option value="Calories">Calories</option>
                <option value="Steps">Steps</option>
                <option value="Workout">Workout</option>
              </select>
              <div v-if="errors.type" class="text-red-500 text-sm mt-1">{{ errors.type[0] }}</div>
            </div>

            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2">Color</label>
              <input
                v-model="form.color"
                type="color"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
              >
              <div v-if="errors.color" class="text-red-500 text-sm mt-1">{{ errors.color[0] }}</div>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 mr-2"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
            >
              {{ isLoading ? 'Adding...' : 'Add Goal' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useHealthGoalsStore } from '../../stores/healthGoalsStore'

const store = useHealthGoalsStore()

// Behoud reactiviteit voor state properties
const {
  goals,
  isModalOpen,
  isLoading,
  form,
  errors
} = storeToRefs(store)

// Methods kunnen gewoon gedestructureerd worden
const {
  openModal,
  closeModal,
  loadGoals,
  addGoal,
  deleteGoal
} = store

onMounted(() => {
  loadGoals()
})
</script>