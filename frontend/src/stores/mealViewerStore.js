// stores/mealViewerStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useMealViewerStore = defineStore('mealViewer', () => {
  const nextMeal = ref(null)
  const mealType = ref('')
  const mealTime = ref('')
  const isLoading = ref(false)

  const loadNextMeal = async () => {
    isLoading.value = true
    try {
      const response = await axios.get('http://localhost:8080/api/meals/next', {
        withCredentials: true
      })
      nextMeal.value = response.data.meal
      mealType.value = response.data.mealType
      mealTime.value = response.data.mealTime
    } catch (error) {
      console.error('Failed to load next meal:', error)
    } finally {
      isLoading.value = false
    }
  }

  return {
    nextMeal,
    mealType,
    mealTime,
    isLoading,
    loadNextMeal
  }
})