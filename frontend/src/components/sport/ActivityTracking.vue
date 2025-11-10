<template>
  <div class="col-span-4 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-2">
      <h3 class="text-gray-800 dark:text-gray-200 text-xl font-semibold">Activity overview</h3>
      <div class="text-gray-500 dark:text-gray-400">{{ currentDate }}</div>
    </div>

    <!-- Week Navigation -->
    <div class="flex justify-between items-center mb-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
      <button
        @click="previousWeek"
        :disabled="isLoading"
        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <div class="text-gray-700 dark:text-gray-300 font-medium">
        {{ isLoading ? 'Loading...' : weekLabel }}
      </div>
      
      <button
        @click="nextWeek"
        :disabled="isLoading"
        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Calendar -->
    <div class="flex justify-between mb-8">
      <div
        v-for="(date, index) in dates"
        :key="index"
        @click="selectDate(date)"
        :class="{
          'cursor-pointer': !isLoading,
          'cursor-not-allowed opacity-50': isLoading
        }"
        class="flex flex-col items-center w-10"
      >
        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ getDayName(index) }}</div>
        <div
          class="w-10 h-10 rounded-full flex items-center justify-center transition-all"
          :class="selectedDate === date
            ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
        >
          {{ date }}
        </div>
      </div>
    </div>

    <!-- Graph -->
    <div class="h-40 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 flex items-end justify-between space-x-1.5">
      <template v-if="isLoading">
        <div
          v-for="index in 7"
          :key="index"
          class="flex-1 bg-gray-300 dark:bg-gray-600 rounded-t-lg animate-pulse"
          :style="{ height: '30%' }"
        ></div>
      </template>
      <template v-else>
        <div
          v-for="(value, index) in activityData"
          :key="index"
          class="flex-1 bg-gradient-to-t from-blue-400 to-blue-500 dark:from-blue-500 dark:to-blue-600 rounded-t-lg transition-all duration-500 ease-out hover:from-blue-500 hover:to-blue-600 dark:hover:from-blue-600 dark:hover:to-blue-700"
          :style="{ height: value + '%' }"
          :title="value + '%'"
        ></div>
      </template>
    </div>

    <!-- Legend -->
    <div class="mt-4 flex justify-center space-x-4">
      <div class="flex items-center">
        <div class="w-3 h-3 bg-blue-400 dark:bg-blue-500 rounded-full mr-2"></div>
        <span class="text-sm text-gray-600 dark:text-gray-300">Activiteit</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useActivityTrackingStore } from '../../stores/activityTrackingStore'

const store = useActivityTrackingStore()

// Behoud reactiviteit voor state properties
const {
  selectedDate,
  dates,
  activityData,
  weekLabel,
  isLoading
} = storeToRefs(store)

// Methods kunnen gewoon gedestructureerd worden
const {
  getDayName,
  loadActivityData,
  selectDate,
  previousWeek,
  nextWeek
} = store

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'long' 
  })
})

onMounted(() => {
  loadActivityData()
})
</script>