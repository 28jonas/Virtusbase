// stores/addFoodItemStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useAddFoodItemStore = defineStore('addFoodItem', () => {
  const isLoading = ref(false)
  const errors = ref({})

  const form = ref({
    name: '',
    image: null,
    servingSize: '',
    servingUnit: 'g',
    calories: 0,
    protein: 0,
    fat: 0,
    carbs: 0,
    fiber: 0,
    sugar: 0,
    sodium: 0,
    is_public: false
  })

  // stores/addFoodItemStore.js
  const saveFoodItem = async () => {
    isLoading.value = true
    errors.value = {}

    const formData = new FormData()

    // Alleen niet-lege en niet-null waarden toevoegen
    Object.keys(form.value).forEach(key => {
      const value = form.value[key]

      // Skip image als het null is
      if (key === 'image') {
        if (value && value instanceof File) {
          formData.append(key, value)
        }
        // Als image null is, voeg het niet toe aan formData
      } else {
        // Voor andere velden, voeg altijd toe (zelfs als ze leeg zijn)
        formData.append(key, value)
      }
    })

    // Log de FormData voor debugging (optioneel)
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1])
    }

    try {
      const response = await axios.post(`${API_BASE}/api/food-items`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        withCredentials: true
      })

      // Reset form
      resetForm()
      return response.data
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
      console.error('Failed to save food item:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const resetForm = () => {
    form.value = {
      name: '',
      image: null,
      servingSize: '',
      servingUnit: 'g',
      calories: 0,
      protein: 0,
      fat: 0,
      carbs: 0,
      fiber: 0,
      sugar: 0,
      sodium: 0,
      isPublic: false
    }
    errors.value = {}
  }

  return {
    isLoading,
    errors,
    form,
    saveFoodItem,
    resetForm
  }
})