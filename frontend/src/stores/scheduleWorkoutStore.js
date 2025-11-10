import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useScheduleWorkoutStore = defineStore('scheduleWorkout', () => {
  // State
  const exercises = ref([])
  const scheduleItems = ref([])
  const isLoading = ref(false)

  // Actions
  const loadExercises = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/schedule-workout/exercises', {
        withCredentials: true
      })
      exercises.value = response.data
    } catch (error) {
      console.error('Failed to load exercises:', error)
      throw error
    }
  }

  const loadScheduleItems = async () => {
    isLoading.value = true
    try {
      const response = await axios.get('http://localhost:8080/api/schedule-workout', {
        withCredentials: true
      })
      scheduleItems.value = response.data
    } catch (error) {
      console.error('Failed to load schedule items:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const createWorkout = async (workoutData) => {
    isLoading.value = true
    try {
      const response = await axios.post('http://localhost:8080/api/schedule-workout', workoutData, {
        withCredentials: true
      })
      
      scheduleItems.value.push(response.data)
      
      // Emit event for other components
      window.dispatchEvent(new CustomEvent('workout-added'))
      
      return response.data
    } catch (error) {
      if (error.response?.status === 422) {
        throw error.response.data.errors
      }
      console.error('Failed to add workout:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  return {
    // State
    exercises,
    scheduleItems,
    isLoading,
    
    // Actions
    loadExercises,
    loadScheduleItems,
    createWorkout
  }
})