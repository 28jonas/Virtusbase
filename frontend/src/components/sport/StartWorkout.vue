<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
    <!-- Scheduled Workout -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 flex-1 max-w-md border border-gray-100 dark:border-gray-700 overflow-hidden inset-0 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 mx-auto w-full">
      <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Scheduled workout</h2>

      <div v-if="scheduledWorkout" class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-6 shadow-sm border border-gray-100 dark:border-gray-600">
        <div class="flex items-center">
          <div class="flex-1">
            <p class="font-medium text-gray-800 dark:text-gray-200">
              {{ formatDay(scheduledWorkout.scheduled_at) }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ formatTime(scheduledWorkout.scheduled_at) }}
            </p>
          </div>
          <p class="ml-4 px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 rounded-full text-sm font-medium">
            {{ scheduledWorkout.type }}
          </p>
        </div>
      </div>
      <div v-else class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-6 shadow-sm border border-gray-100 dark:border-gray-600">
        <p class="text-gray-500 dark:text-gray-400 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
          </svg>
          No workouts scheduled
        </p>
      </div>
      
      <button
        v-if="scheduledWorkout"
        @click="finishWorkout"
        :disabled="isLoading"
        class="bg-gradient-to-r from-amber-400 to-amber-500 dark:from-amber-500 dark:to-amber-600 text-white px-8 py-3 rounded-lg mx-auto block hover:from-amber-500 hover:to-amber-600 dark:hover:from-amber-600 dark:hover:to-amber-700 transition-all shadow-md hover:shadow-lg font-medium"
      >
        FINISH WORKOUT
      </button>
    </div>

    <!-- Previous Workout Results -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 flex-1 max-w-md border border-gray-100 dark:border-gray-700 overflow-hidden inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 mx-auto w-full">
      <div class="flex justify-between">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Vorige workout</h2>
        <a class="mb-4 bg-gradient-to-r from-blue-400 to-blue-500 dark:from-blue-500 dark:to-blue-600 text-white px-4 py-3 rounded-lg block hover:from-blue-500 hover:to-blue-600 dark:hover:from-blue-600 dark:hover:to-blue-700 transition-all shadow-md hover:shadow-lg font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 64 64"><path d="M41.03 47.852l-5.572-10.976h-8.172L41.03 64l13.736-27.124h-8.18" fill="#f9b797"/><path d="M27.898 21.944l7.564 14.928h11.124L27.898 0 9.234 36.876H20.35" fill="#f05222"/></svg>
        </a>
      </div>

      <div>
        <div v-if="latestActivity" class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg shadow border border-gray-100 dark:border-gray-600">
          <h3 class="font-bold text-lg mb-2 dark:text-gray-200">
            Latest activity
            <span class="text-sm text-gray-500 dark:text-gray-400">
              (van {{ activitySource === 'strava' ? 'Strava' : 'eigen database' }})
            </span>
          </h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-gray-600 dark:text-gray-400">Name:</p>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ latestActivity.name }}</p>
            </div>
            <div>
              <p class="text-gray-600 dark:text-gray-400">Type:</p>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ latestActivity.type }}</p>
            </div>
            <div>
              <p class="text-gray-600 dark:text-gray-400">Distance:</p>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ formattedDistance }}</p>
            </div>
            <div>
              <p class="text-gray-600 dark:text-gray-400">Time:</p>
              <p class="font-medium text-gray-800 dark:text-gray-200">{{ formattedDuration }}</p>
            </div>
          </div>
          
          <div class="mt-4">
            <button
              @click="refreshStravaData"
              :disabled="isLoading"
              class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm"
            >
              {{ isLoading ? 'Refreshing...' : 'Refresh Strava Data' }}
            </button>
          </div>
        </div>
        <p v-else class="text-gray-500 dark:text-gray-400">No recent activities found</p>
        
        <div v-if="stravaError" class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
          <p class="text-red-700 dark:text-red-300 text-sm">{{ stravaError }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useStartWorkoutStore } from '../../stores/startWorkoutStore'

const store = useStartWorkoutStore()

// Computed properties voor reactiviteit
const scheduledWorkout = computed(() => store.scheduledWorkout)
const latestActivity = computed(() => store.latestActivity)
const activitySource = computed(() => store.activitySource)
const stravaError = computed(() => store.stravaError)
const isLoading = computed(() => store.isLoading)
const formattedDistance = computed(() => store.formattedDistance)
const formattedDuration = computed(() => store.formattedDuration)
const formattedSpeed = computed(() => store.formattedSpeed)

const {
  loadWorkoutData,
  refreshStravaData,
  finishWorkout,
  startWorkout,
  viewWorkoutResults
} = store

const formatDay = (dateString) => {
  return new Date(dateString).toLocaleDateString('nl', { weekday: 'long' })
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('nl', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

onMounted(() => {
  loadWorkoutData()
})
</script>