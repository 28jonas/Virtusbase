import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useHealthMetricsStore = defineStore('healthMetrics', () => {
  const steps = ref(0)
  const stepsGoal = ref(5000)
  const waterIntake = ref(0)
  const waterGoal = ref(2000)
  const caloriesConsumed = ref(0)
  const caloriesGoal = ref(2000)
  const heartRate = ref(0)
  const isLoading = ref(false)

  const stepsPercentage = computed(() => {
    return Math.min(100, (steps.value / stepsGoal.value) * 100)
  })

  const waterPercentage = computed(() => {
    return Math.min(100, (waterIntake.value / waterGoal.value) * 100)
  })

  const caloriesPercentage = computed(() => {
    return Math.min(100, (caloriesConsumed.value / caloriesGoal.value) * 100)
  })

  const loadData = async () => {
    isLoading.value = true
    try {
      const response = await axios.get(`${API_BASE}/api/health-metrics`, {
        withCredentials: true
      })
      const data = response.data
      
      steps.value = data.steps
      stepsGoal.value = data.stepsGoal
      waterIntake.value = data.waterIntake
      waterGoal.value = data.waterGoal
      caloriesConsumed.value = data.caloriesConsumed
      caloriesGoal.value = data.caloriesGoal
      heartRate.value = data.heartRate

    } catch (error) {
      console.error('Failed to load health metrics:', error)
    } finally {
      isLoading.value = false
    }
  }

  const addWaterGlass = async () => {
    if (isLoading.value) return
    
    try {
      const response = await axios.post(`${API_BASE}/api/health-metrics/water`, {}, {
        withCredentials: true
      })
      const data = response.data
      
      waterIntake.value = data.waterIntake
      
      // Emit event for other components
      window.dispatchEvent(new CustomEvent('water-added'))
    } catch (error) {
      console.error('Failed to add water:', error)
    }
  }

  const updateCalories = (calories) => {
    caloriesConsumed.value = calories
  }

  return {
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
    caloriesPercentage,
    loadData,
    addWaterGlass,
    updateCalories
  }
})