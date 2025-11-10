<!-- components/dashboard/FocusTimer.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Focus Timer</h3>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ timerMode }}</span>
    </div>
    
    <div class="text-center mb-6">
      <div class="relative w-48 h-48 mx-auto mb-4">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8" />
          <circle cx="50" cy="50" r="45" fill="none" stroke="#3b82f6" stroke-width="8" 
            :stroke-dasharray="283" 
            :stroke-dashoffset="283 - (283 * progress)" />
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="text-center">
            <div class="text-3xl font-mono font-light text-gray-900 dark:text-white">{{ formattedTime }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ timerStatus }}</div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="flex justify-center space-x-4">
      <button @click="startTimer" 
        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
        v-if="!isRunning">
        Start
      </button>
      <button @click="pauseTimer" 
        class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors"
        v-else>
        Pause
      </button>
      <button @click="resetTimer" 
        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
        Reset
      </button>
    </div>
    
    <div class="mt-6 grid grid-cols-3 gap-2">
      <button v-for="duration in durations" :key="duration" 
        @click="setDuration(duration)"
        class="py-2 text-sm rounded-lg transition-colors"
        :class="selectedDuration === duration ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
        {{ duration }}min
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'

const durations = [25, 15, 5]
const selectedDuration = ref(25)
const timeLeft = ref(selectedDuration.value * 60)
const isRunning = ref(false)
const timerInterval = ref(null)

const progress = computed(() => {
  const total = selectedDuration.value * 60
  return (total - timeLeft.value) / total
})

const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60)
  const seconds = timeLeft.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

const timerStatus = computed(() => {
  if (!isRunning.value) return 'Klaar om te focussen'
  return 'Aan het focussen...'
})

const timerMode = computed(() => {
  return selectedDuration.value === 25 ? 'Focus' : selectedDuration.value === 15 ? 'Kort' : 'Break'
})

const startTimer = () => {
  isRunning.value = true
  timerInterval.value = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timerInterval.value)
      isRunning.value = false
      // Timer completed logic here
    }
  }, 1000)
}

const pauseTimer = () => {
  isRunning.value = false
  clearInterval(timerInterval.value)
}

const resetTimer = () => {
  pauseTimer()
  timeLeft.value = selectedDuration.value * 60
}

const setDuration = (duration) => {
  selectedDuration.value = duration
  resetTimer()
}

onUnmounted(() => {
  clearInterval(timerInterval.value)
})
</script>