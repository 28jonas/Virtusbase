import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useHealthGoalsStore = defineStore('healthGoals', () => {
  const goals = ref([])
  const isModalOpen = ref(false)
  const isLoading = ref(false)

  const form = ref({
    title: '',
    target: '',
    current: '',
    goalDate: '',
    type: '',
    color: ''
  })

  const errors = ref({})

  const openModal = () => {
    isModalOpen.value = true
  }

  const closeModal = () => {
    isModalOpen.value = false
    resetForm()
    errors.value = {}
  }

  const resetForm = () => {
    form.value = {
      title: '',
      target: '',
      current: '',
      goalDate: '',
      type: '',
      color: ''
    }
  }

  const loadGoals = async () => {
    isLoading.value = true
    try {
      const response = await axios.get(`${API_BASE}/api/health-goals`,{
        withCredentials: true
      })
      goals.value = response.data
    } catch (error) {
      console.error('Failed to load health goals:', error)
    } finally {
      isLoading.value = false
    }
  }

  const addGoal = async () => {
    isLoading.value = true
    try {
      const response = await axios.post(`${API_BASE}/api/health-goals`, form.value, {
        withCredentials: true
      })
      goals.value.push(response.data)
      closeModal()
      
      // Show success message
      // You can use a notification store here
      console.log('Fitness goal added successfully.')
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
      console.error('Failed to add goal:', error)
    } finally {
      isLoading.value = false
    }
  }

  const deleteGoal = async (id) => {
    try {
      await axios.delete(`${API_BASE}/api/health-goals/${id}`, {
        withCredentials: true
      })
      goals.value = goals.value.filter(goal => goal.id !== id)
      console.log('Goal deleted successfully.')
    } catch (error) {
      console.error('Failed to delete goal:', error)
    }
  }

  return {
    goals,
    isModalOpen,
    isLoading,
    form,
    errors,
    openModal,
    closeModal,
    loadGoals,
    addGoal,
    deleteGoal
  }
})