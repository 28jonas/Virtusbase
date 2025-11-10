import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useTransactionsStore = defineStore('transactions', () => {
  // State
  const transactions = ref([])

  // Actions
  const loadTransactions = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/transactions', {
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