import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useCardStore = defineStore('card', () => {
  // State
  const cards = ref([])
  const banks = ref([])
  const stats = ref({
    total_balance: 0,
    total_transfers: 0,
    total_balance_with_transfers: 0,
    card_count: 0,
    banks_count: 0
  })

  // Getters (computed)
  const formattedCards = computed(() => {
    return cards.value.map(card => ({
      ...card,
      balance: ensureNumber(card.balance)
    }))
  })

  const formattedStats = computed(() => ({
    total_balance: ensureNumber(stats.value.total_balance),
    total_transfers: ensureNumber(stats.value.total_transfers),
    total_balance_with_transfers: ensureNumber(stats.value.total_balance_with_transfers),
    card_count: stats.value.card_count,
    banks_count: stats.value.banks_count
  }))

  // Helper function to ensure values are numbers
  const ensureNumber = (value) => {
    if (value === null || value === undefined) return 0
    return typeof value === 'number' ? value : Number(value)
  }

  // Helper function for currency formatting
  const formatCurrency = (amount) => {
    return ensureNumber(amount).toFixed(2)
  }

  // Actions
  const fetchCards = async () => {
    try {
      const res = await axios.get(`${API_BASE}/api/cards`, {
        withCredentials: true
      })
      cards.value = res.data.data.cards
      stats.value = res.data.data.stats
    } catch (err) {
      console.error('Failed to fetch cards:', err)
      throw err
    }
  }

  const fetchBanks = async () => {
    try {
      const res = await axios.get(`${API_BASE}/api/cards/banks`, {
        withCredentials: true
      })
      banks.value = res.data.data
    } catch (err) {
      console.error('Failed to fetch banks:', err)
      throw err
    }
  }

  const createCard = async (payload) => {
    try {
      // Ensure balance is sent as number
      const formattedPayload = {
        ...payload,
        balance: ensureNumber(payload.balance)
      }

      const res = await axios.post(`${API_BASE}/api/cards`, formattedPayload, {
        withCredentials: true
      })
      cards.value.push(res.data.data)
      await fetchStats() // Refresh stats
      return res.data.data
    } catch (err) {
      console.error('Failed to create card:', err)
      throw err
    }
  }

  const updateCard = async (id, payload) => {
    try {
      // Ensure balance is sent as number
      const formattedPayload = {
        ...payload,
        balance: ensureNumber(payload.balance)
      }

      const res = await axios.put(`${API_BASE}/api/cards/${id}`, formattedPayload, {
        withCredentials: true
      })
      const idx = cards.value.findIndex(c => c.id === id)
      if (idx !== -1) cards.value[idx] = res.data.data
      await fetchStats() // Refresh stats
      return res.data.data
    } catch (err) {
      console.error('Failed to update card:', err)
      throw err
    }
  }

  const deleteCard = async (id) => {
    try {
      await axios.delete(`${API_BASE}/api/cards/${id}`, {
        withCredentials: true
      })
      cards.value = cards.value.filter(c => c.id !== id)
      await fetchStats() // Refresh stats
    } catch (err) {
      console.error('Failed to delete card:', err)
      throw err
    }
  }

  const transfer = async (payload) => {
    try {
      // Ensure amount is sent as number
      const formattedPayload = {
        ...payload,
        amount: ensureNumber(payload.amount)
      }

      const res = await axios.post(`${API_BASE}/api/cards/transfer`, formattedPayload, {
        withCredentials: true
      })
      // Refresh cards after transfer
      await fetchCards()
      return res.data.data
    } catch (err) {
      console.error('Failed to transfer:', err)
      throw err
    }
  }

  const fetchStats = async () => {
    try {
      const res = await axios.get(`${API_BASE}/api/cards/stats`, {
        withCredentials: true
      })
      stats.value = res.data.data
    } catch (err) {
      console.error('Failed to fetch card stats:', err)
      throw err
    }
  }

  return {
    // State
    cards,
    banks,
    stats,
    
    // Getters
    formattedCards,
    formattedStats,
    
    // Helper functions
    formatCurrency,
    
    // Actions
    fetchCards,
    fetchBanks,
    createCard,
    updateCard,
    deleteCard,
    transfer,
    fetchStats
  }
})