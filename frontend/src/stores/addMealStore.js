// stores/addMealStore.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useAddMealStore = defineStore('addMeal', () => {
  const mealTypes = ref([])
  const availableFoodItems = ref([])
  const selectedFoodItems = ref([])
  const search = ref('')
  const isLoading = ref(false)
  const errors = ref({})

  const meal = ref({
    meal_type_id: '',
    name: '',
    notes: '',
    time: new Date().toTimeString().slice(0, 5),
    date: new Date().toISOString().split('T')[0]
  })

  const newFoodItem = ref({
    id: '',
    quantity: 1
  })

  const loadMealTypes = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/meal-types`, {
        withCredentials: true
      })
      mealTypes.value = response.data
    } catch (error) {
      console.error('Failed to load meal types:', error)
    }
  }

  const loadAvailableFoodItems = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/food-items/search`, {
        params: { search: search.value },
        withCredentials: true
      })
      availableFoodItems.value = response.data
    } catch (error) {
      console.error('Failed to load food items:', error)
    }
  }

  const addFoodItem = () => {
    if (!newFoodItem.value.id) {
      errors.value.foodItem = ['Select a food item']
      return
    }

    const foodItem = availableFoodItems.value.find(item => item.id === newFoodItem.value.id)
    if (!foodItem) {
      errors.value.foodItem = ['Food item not found']
      return
    }

    const existingIndex = selectedFoodItems.value.findIndex(item => item.id === foodItem.id)
    if (existingIndex !== -1) {
      selectedFoodItems.value[existingIndex].quantity += newFoodItem.value.quantity
    } else {
      selectedFoodItems.value.push({
        id: foodItem.id,
        name: foodItem.name,
        image_path: foodItem.image_path,
        serving_size: foodItem.serving_size,
        serving_unit: foodItem.serving_unit,
        protein: foodItem.protein,
        fat: foodItem.fat,
        carbs: foodItem.carbs,
        calories: foodItem.calories,
        quantity: newFoodItem.value.quantity
      })
    }

    newFoodItem.value = { id: '', quantity: 1 }
    errors.value.foodItem = null
  }

  const removeFoodItem = (index) => {
    selectedFoodItems.value.splice(index, 1)
  }

  const saveMeal = async () => {
    isLoading.value = true
    errors.value = {}

    try {
      const response = await axios.post(`${API_BASE}/api/meals`, {
        meal: meal.value,
        food_items: selectedFoodItems.value
      }, {
        withCredentials: true
      })

      // Reset form
      meal.value = {
        meal_type_id: '',
        name: '',
        notes: '',
        time: new Date().toTimeString().slice(0, 5),
        date: new Date().toISOString().split('T')[0]
      }
      selectedFoodItems.value = []
      
      return response.data
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
      console.error('Failed to save meal:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }
  

  return {
    mealTypes,
    availableFoodItems,
    selectedFoodItems,
    search,
    isLoading,
    errors,
    meal,
    newFoodItem,
    loadMealTypes,
    loadAvailableFoodItems,
    addFoodItem,
    removeFoodItem,
    saveMeal
  }
})