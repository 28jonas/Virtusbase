// stores/foodItemsStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useFoodItemsStore = defineStore('foodItems', () => {
  const foodItems = ref([])
  const search = ref('')
  const showPublic = ref(true)
  const isLoading = ref(false)
  const currentPage = ref(1)
  const lastPage = ref(1)

  const loadFoodItems = async (page = 1) => {
    isLoading.value = true
    try {
      const response = await axios.get('http://localhost:8080/api/food-items', {
        params: {
          search: search.value,
          showPublic: showPublic.value,
          page: page
        },
        withCredentials: true
      })
      foodItems.value = response.data.data
      currentPage.value = response.data.current_page
      lastPage.value = response.data.last_page
    } catch (error) {
      console.error('Failed to load food items:', error)
    } finally {
      isLoading.value = false
    }
  }

  const deleteFoodItem = async (id) => {
    try {
      await axios.delete(`http://localhost:8080/api/food-items/${id}`, {
        withCredentials: true
      })
      await loadFoodItems(currentPage.value)
    } catch (error) {
      console.error('Failed to delete food item:', error)
    }
  }

  return {
    foodItems,
    search,
    showPublic,
    isLoading,
    currentPage,
    lastPage,
    loadFoodItems,
    deleteFoodItem
  }
})