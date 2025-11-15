import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useStatsStore = defineStore('stats', () => {
  // State
  const totalIncome = ref(0)
  const totalExpense = ref(0)
  const incomePercentage = ref(0)
  const expensePercentage = ref(0)

  // Actions
  const loadStats = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/stats/income-expense`, {
        withCredentials: true
      })
      const data = response.data
      
      totalIncome.value = data.totalIncome
      totalExpense.value = data.totalExpense
      incomePercentage.value = data.incomePercentage
      expensePercentage.value = data.expensePercentage
    } catch (error) {
      console.error('Failed to load stats:', error)
    }
  }

  return {
    // State
    totalIncome,
    totalExpense,
    incomePercentage,
    expensePercentage,
    
    // Actions
    loadStats
  }
})