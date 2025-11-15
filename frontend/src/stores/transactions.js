import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useTransactionsStore = defineStore('transactions', () => {
  // State
  const transactions = ref([])

  // Actions
  const loadTransactions = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/transactions`, {
        withCredentials:true
      })
      transactions.value = response.data
    } catch (error) {
      console.error('Failed to load transactions:', error)
    }
  }

  return {
    // State
    transactions,
    
    // Actions
    loadTransactions
  }
})