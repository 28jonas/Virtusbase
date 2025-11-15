import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useGoalsStore = defineStore('goals', () => {
  // State
  const goals = ref([])
  const categories = ref([])
  const cards = ref([])
  const showModal = ref(false)
  const isEdit = ref(false)
  const editingGoalId = ref(null)
  const form = ref({
    type: '',
    amount: '',
    goalDate: '',
    cardId: null,
    transfer: 0
  })

  // Actions
  const loadGoals = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/goals`,{
        withCredentials: true
      })
      goals.value = response.data
    } catch (error) {
      console.error('Failed to load goals:', error)
    }
  }

  const loadCategories = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/categories?type=financial_goal`, {
        withCredentials: true
      })
      categories.value = response.data
    } catch (error) {
      console.error('Failed to load categories:', error)
    }
  }

  const loadCards = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/cards`,{
        withCredentials: true
      })
      cards.value = response.data.cards
    } catch (error) {
      console.error('Failed to load cards:', error)
    }
  }

  const openModal = (goalId = null, edit = false) => {
    isEdit.value = edit
    editingGoalId.value = goalId
    showModal.value = true

    if (goalId && edit) {
      const goal = goals.value.find(g => g.id === goalId)
      //console.log('Editing goal:', goal)
      if (goal) {
        form.value = {
          type: goal.categories[0]?.id || '',
          amount: goal.amount,
          goalDate: goal.date,
          cardId: goal.card_id,
          transfer: goal.transfer
        }
      }
    } else {
      resetForm()
    }
  }

  const closeModal = () => {
    showModal.value = false
    isEdit.value = false
    editingGoalId.value = null
    resetForm()
  }

  const addGoal = async () => {
    try {
      const response = await axios.post(`${API_BASE}/api/goals`, form.value, {
        withCredentials: true
      })
      await loadGoals()
      closeModal()
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  }

  const updateGoal = async () => {
    try {
      const response = await axios.put(`${API_BASE}/api/goals/${editingGoalId.value}`, form.value, {
        withCredentials: true
      })
      await loadGoals()
      closeModal()
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  }

  const resetForm = () => {
    form.value = {
      type: '',
      amount: '',
      goalDate: '',
      cardId: null,
      transfer: 0
    }
  }

  return {
    // State
    goals,
    categories,
    cards,
    showModal,
    isEdit,
    form,
    
    // Actions
    loadGoals,
    loadCategories,
    loadCards,
    openModal,
    closeModal,
    addGoal,
    updateGoal,
    resetForm
  }
})