<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Health Metrics</h3>
    
    <div class="space-y-6">
      <!-- Steps -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-walking text-blue-600 dark:text-blue-400"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Steps</p>
            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ steps }} / {{ stepsGoal }}</p>
          </div>
        </div>
        <div class="w-16">
          <div class="text-sm font-medium text-gray-800 dark:text-gray-200 text-right">{{ Math.round(stepsPercentage) }}%</div>
          <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-1">
            <div
              class="h-2 rounded-full bg-blue-500 transition-all duration-500"
              :style="{ width: stepsPercentage + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Water Intake -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-tint text-cyan-600 dark:text-cyan-400"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Water</p>
            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ waterIntake }}ml / {{ waterGoal }}ml</p>
          </div>
        </div>
        <div class="flex items-center">
          <div class="text-sm font-medium text-gray-800 dark:text-gray-200 mr-3">{{ Math.round(waterPercentage) }}%</div>
          <button
            @click="addWaterGlass"
            :disabled="isLoading"
            class="w-8 h-8 bg-cyan-500 text-white rounded-full flex items-center justify-center hover:bg-cyan-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i class="fas fa-plus text-xs"></i>
          </button>
        </div>
      </div>

      <!-- Calories -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-fire text-orange-600 dark:text-orange-400"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Calories</p>
            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ caloriesConsumed }} / {{ caloriesGoal }}</p>
          </div>
        </div>
        <div class="w-16">
          <div class="text-sm font-medium text-gray-800 dark:text-gray-200 text-right">{{ Math.round(caloriesPercentage) }}%</div>
          <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-1">
            <div
              class="h-2 rounded-full bg-orange-500 transition-all duration-500"
              :style="{ width: caloriesPercentage + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Heart Rate -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
            <i class="fas fa-heart text-red-600 dark:text-red-400"></i>
          </div>
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Heart Rate</p>
            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ heartRate }} BPM</p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-4">
        <div class="inline-flex items-center">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
          <span class="text-sm text-gray-500 dark:text-gray-400">Loading health data...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useHealthMetricsStore } from '../../stores/healthMetricsStore'

const store = useHealthMetricsStore()

// Behoud reactiviteit voor state properties
const {
  steps,
  stepsGoal,
  waterIntake,
  waterGoal,
  caloriesConsumed,
  caloriesGoal,
  heartRate,
  isLoading,
  stepsPercentage,
  waterPercentage,
  caloriesPercentage
} = storeToRefs(store)

// Methods kunnen gewoon gedestructureerd worden
const {
  loadData,
  addWaterGlass
} = store

onMounted(() => {
  loadData()
})
</script>