import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useIncomesStore = defineStore('incomes', () => {
  // State
  const incomes = ref([])
  const incomesByCategory = ref([])
  const categories = ref([])
  const cards = ref([])
  const showModal = ref(false)
  const form = ref({
    category_id: null,
    amount: null,
    description: '',
    date: new Date().toISOString().split('T')[0],
    card_id: null
  })

  // Getters
  const incomeCategories = computed(() => {
    const currentMonth = new Date().toISOString().slice(0, 7)
    const totalIncome = incomes.value
      .filter(income => income.date.startsWith(currentMonth))
      .reduce((sum, income) => sum + parseFloat(income.amount), 0)

    const categoryMap = new Map()
    
    incomes.value
      .filter(income => income.date.startsWith(currentMonth))
      .forEach(income => {
        income.categories?.forEach(category => {
          const current = categoryMap.get(category.id) || {
            id: category.id,
            name: category.name,
            color: category.color,
            amount: 0
          }
          current.amount += parseFloat(income.amount)
          categoryMap.set(category.id, current)
        })
      })

    return Array.from(categoryMap.values()).map(category => ({
      ...category,
      percentage: totalIncome > 0 ? Math.round((category.amount / totalIncome) * 100) : 0
    })).sort((a, b) => b.amount - a.amount)
  })

  // Actions
  const loadIncomes = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/incomes`, {
        withCredentials: true
      })
      incomes.value = response.data
    } catch (error) {
      console.error('Failed to load incomes:', error)
    }
  }

  const loadIncomesByCategory = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/incomes`, {
        withCredentials: true
      })

      const categoryTotals = {}
      let totalAmount = 0

      response.data.forEach(income => {
        income.categories?.forEach(category => {
          if (!categoryTotals[category.id]) {
            categoryTotals[category.id] = {
              id: category.id,
              name: category.name,
              color: category.color,
              amount: 0
            }
          }
          const incomeAmount = parseFloat(income.amount)
          categoryTotals[category.id].amount += incomeAmount
          totalAmount += incomeAmount
        })
      })

      // Convert to array, add percentage and sort by amount (hoogste eerst)
      const categorizedIncomes = Object.values(categoryTotals)
        .map(category => {
          const percentage = totalAmount > 0 ? (category.amount / totalAmount) * 100 : 0
          return {
            ...category,
            percentage: Math.round(percentage * 100) / 100,
            formattedPercentage: `${Math.round(percentage)}%`
          }
        })
        .sort((a, b) => b.amount - a.amount) // Sorteer van hoog naar laag

      console.log('Incomes by category:', categorizedIncomes)
      console.log('Total amount:', totalAmount)
      incomesByCategory.value = categorizedIncomes
      return categorizedIncomes

    } catch (error) {
      console.error('Failed to load incomes:', error)
    }
  }

  const loadCategories = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/categories?type=income_category`, {
        withCredentials:true
      })
      categories.value = response.data
    } catch (error) {
      console.error('Failed to load categories:', error)
    }
  }

  const loadCards = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/cards`, {
        withCredentials: true
      })
      cards.value = response.data.cards
    } catch (error) {
      console.error('Failed to load cards:', error)
    }
  }

  const addIncome = async () => {
    try {
      const response = await axios.post(`${API_BASE}/api/incomes`, form.value, {
        withCredentials: true
      })
      await loadIncomes()
      showModal.value = false
      resetForm()
      
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  }

  const resetForm = () => {
    form.value = {
      category_id: null,
      amount: null,
      description: '',
      date: new Date().toISOString().split('T')[0],
      card_id: null
    }
  }

  return {
    // State
    incomes,
    incomesByCategory,
    categories,
    cards,
    showModal,
    form,
    
    // Getters
    incomeCategories,
    
    // Actions
    loadIncomes,
    loadIncomesByCategory,
    loadCategories,
    loadCards,
    addIncome,
    resetForm
  }
})