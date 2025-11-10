// stores/mealTrackingStore.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useMealTrackingStore = defineStore('mealTracking', () => {
  const date = ref(new Date().toISOString().split('T')[0])
  const meals = ref([])
  const mealTypes = ref([])
  const showMealModal = ref(false)
  const showFoodItemModal = ref(false)
  const isLoading = ref(false)

  const dailyTotals = computed(() => {
    const totals = {
      calories: 0,
      protein: 0,
      fat: 0,
      carbs: 0,
      fiber: 0,
      sugar: 0,
      sodium: 0
    }

    meals.value.forEach(meal => {
      totals.calories += meal.total_nutrition.calories
      totals.protein += meal.total_nutrition.protein
      totals.fat += meal.total_nutrition.fat
      totals.carbs += meal.total_nutrition.carbs
      totals.fiber += meal.total_nutrition.fiber
      totals.sugar += meal.total_nutrition.sugar
      totals.sodium += meal.total_nutrition.sodium
    })

    return totals
  })

  const loadMeals = async () => {
    isLoading.value = true
    try {
      const response = await axios.get('http://localhost:8080/api/meals', {
        params: { date: date.value },
        withCredentials: true
      })
      meals.value = response.data.meals
      mealTypes.value = response.data.mealTypes
    } catch (error) {
      console.error('Failed to load meals:', error)
    } finally {
      isLoading.value = false
    }
  }

  const loadMealTypes = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/meal-types', {
        withCredentials: true
      })
      mealTypes.value = response.data
    } catch (error) {
      console.error('Failed to load meal types:', error)
    }
  }

  const previousDay = () => {
    const currentDate = new Date(date.value)
    currentDate.setDate(currentDate.getDate() - 1)
    date.value = currentDate.toISOString().split('T')[0]
    loadMeals()
  }

  const nextDay = () => {
    const currentDate = new Date(date.value)
    currentDate.setDate(currentDate.getDate() + 1)
    date.value = currentDate.toISOString().split('T')[0]
    loadMeals()
  }

  const setToday = () => {
    date.value = new Date().toISOString().split('T')[0]
    loadMeals()
  }

  const openMealModal = () => {
    showMealModal.value = true
  }

  const closeMealModal = () => {
    showMealModal.value = false
  }

  const openFoodItemModal = () => {
    showFoodItemModal.value = true
  }

  const closeFoodItemModal = () => {
    showFoodItemModal.value = false
  }

  // In stores/mealTrackingStore.js
  const loadMealsForDate = async (specificDate = null) => {
    isLoading.value = true
    try {
      const targetDate = specificDate || date.value
      const response = await axios.get('http://localhost:8080/api/meals', {
        params: { date: targetDate },
        withCredentials: true
      })
      meals.value = response.data.meals
      mealTypes.value = response.data.mealTypes
    } catch (error) {
      console.error('Failed to load meals:', error)
    } finally {
      isLoading.value = false
    }
  }


  return {
    date,
    meals,
    mealTypes,
    showMealModal,
    showFoodItemModal,
    isLoading,
    dailyTotals,
    loadMeals,
    loadMealTypes,
    previousDay,
    nextDay,
    setToday,
    openMealModal,
    closeMealModal,
    openFoodItemModal,
    closeFoodItemModal,
    loadMealsForDate
  }
})