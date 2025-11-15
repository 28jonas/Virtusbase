import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useExpensesStore = defineStore('expenses', () => {
  // State
  const expenses = ref([])
  const expensesByCategory = ref([])
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
  const expenseCategories = computed(() => {
    const currentMonth = new Date().toISOString().slice(0, 7)
    const totalExpense = expenses.value
      .filter(expense => expense.date.startsWith(currentMonth))
      .reduce((sum, expense) => sum + parseFloat(expense.amount), 0)

    const categoryMap = new Map()

    expenses.value
      .filter(expense => expense.date.startsWith(currentMonth))
      .forEach(expense => {
        expense.categories?.forEach(category => {
          const current = categoryMap.get(category.id) || {
            id: category.id,
            name: category.name,
            color: category.color,
            amount: 0
          }
          current.amount += parseFloat(expense.amount)
          categoryMap.set(category.id, current)
        })
      })

    return Array.from(categoryMap.values()).map(category => ({
      ...category,
      percentage: totalExpense > 0 ? Math.round((category.amount / totalExpense) * 100) : 0
    })).sort((a, b) => b.amount - a.amount)
  })

  // Actions
  const loadExpenses = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/expenses`, {
        withCredentials: true
      })
      expenses.value = response.data
    } catch (error) {
      console.error('Failed to load expenses:', error)
    }
  }

  const loadExpensesByCategory = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/expenses`, {
        withCredentials: true
      })

      const categoryTotals = {}
      let totalAmount = 0

      response.data.forEach(expense => {
        expense.categories?.forEach(category => {
          if (!categoryTotals[category.id]) {
            categoryTotals[category.id] = {
              id: category.id,
              name: category.name,
              color: category.color,
              amount: 0
            }
          }
          const expenseAmount = parseFloat(expense.amount)
          categoryTotals[category.id].amount += expenseAmount
          totalAmount += expenseAmount
        })
      })

      // Convert to array, add percentage and sort by amount (hoogste eerst)
      const categorizedExpenses = Object.values(categoryTotals)
        .map(category => {
          const percentage = totalAmount > 0 ? (category.amount / totalAmount) * 100 : 0
          return {
            ...category,
            percentage: Math.round(percentage * 100) / 100,
            formattedPercentage: `${Math.round(percentage)}%`
          }
        })
        .sort((a, b) => b.amount - a.amount) // Sorteer van hoog naar laag

      console.log('Expenses by category:', categorizedExpenses)
      console.log('Total amount:', totalAmount)
      expensesByCategory.value = categorizedExpenses
      return categorizedExpenses

    } catch (error) {
      console.error('Failed to load expenses:', error)
    }
  }

  const loadCategories = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/categories?type=expense_category`, {
        withCredentials: true
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

  const addExpense = async () => {
    try {
      const response = await axios.post(`${API_BASE}/api/expenses`, form.value, {
        withCredentials: true
      })
      await loadExpenses()
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
    expenses,
    expensesByCategory,
    categories,
    cards,
    showModal,
    form,

    // Getters
    expenseCategories,

    // Actions
    loadExpenses,
    loadExpensesByCategory,
    loadCategories,
    loadCards,
    addExpense,
    resetForm
  }
})