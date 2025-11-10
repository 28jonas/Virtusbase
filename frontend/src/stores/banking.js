import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useBankingStore = defineStore('banking', () => {
  // State
  const cards = ref([])
  const banks = ref([])
  const totalBalance = ref(0)
  const totalTransfers = ref(0)
  
  // Form states
  const showNewCardModal = ref(false)
  const showTransferModal = ref(false)
  const form = ref({
    cardNumber: '',
    expiryDate: '',
    balance: 0,
    selectedBankId: null
  })
  const transferForm = ref({
    sourceCardId: null,
    destinationCardId: null,
    transferAmount: null
  })

  // Getters
  const hasMultipleCards = computed(() => cards.value.length >= 2)

  // Actions
  const loadCards = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/cards',{
        withCredentials: true
      })
      cards.value = response.data.cards
      totalBalance.value = response.data.totalBalance
      totalTransfers.value = response.data.totalTransfers
    } catch (error) {
      console.error('Failed to load cards:', error)
    }
  }

  const loadBanks = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/banks', {
        withCredentials: true
      })
      banks.value = response.data
    } catch (error) {
      console.error('Failed to load banks:', error)
    }
  }

  const validateCardNumber = (cardNumber, bankId) => {
    const bank = banks.value.find(b => b.id === bankId)
    if (!bank) throw new Error('Selecteer eerst een bank')

    const cleanNumber = cardNumber.replace(/\s+/g, '')

    // KBC validation
    if (bank.name === 'KBC') {
      if (!/^\d{17}$/.test(cleanNumber)) {
        throw new Error('Voer een geldig 17-cijferig kaartnummer in')
      }
    }

    // Other banks validation
    if (['Mastercard', 'Visa', 'ING', 'Bunq', 'Axa', 'BeoBank', 'BNP Paribas Fortis'].includes(bank.name)) {
      if (!/^\d{16}$/.test(cleanNumber)) {
        throw new Error('Voer een geldig 16-cijferig kaartnummer in')
      }

      // Bank-specific prefix checks
      if (bank.name === 'Mastercard' && !/^5[1-5]/.test(cleanNumber)) {
        throw new Error('Mastercard nummers beginnen met 51-55')
      }

      if (bank.name === 'Visa' && !/^4/.test(cleanNumber)) {
        throw new Error('Visa nummers beginnen met 4')
      }
    }

    // Luhn check
    if (['Mastercard', 'Visa', 'Belfius', 'KBC', 'BNP Paribas Fortis'].includes(bank.name)) {
      if (!passesLuhnCheck(cleanNumber)) {
        throw new Error('Ongeldig kaartnummer (Luhn check gefaald)')
      }
    }

    return true
  }

  const passesLuhnCheck = (number) => {
    const cleanNumber = number.replace(/\s+/g, '')
    let sum = 0
    const length = cleanNumber.length

    for (let i = 0; i < length; i++) {
      let digit = parseInt(cleanNumber[length - i - 1])
      if (i % 2 === 1) {
        digit *= 2
        if (digit > 9) {
          digit -= 9
        }
      }
      sum += digit
    }

    return sum % 10 === 0
  }

  const createCard = async () => {
    try {
      // Manual validation
      validateCardNumber(form.value.cardNumber, form.value.selectedBankId)

      const response = await axios.post('http://localhost:8080/api/cards', {
      card_number: form.value.cardNumber.replace(/\s+/g, ''),
      expiry_date: form.value.expiryDate,
      balance: form.value.balance,
      bank_id: form.value.selectedBankId
    }, {
      withCredentials: true  // Dit hoort in de config parameter
    })

      await loadCards()
      showNewCardModal.value = false
      resetForm()
      
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  }

  const transferMoney = async () => {
    try {
      const response = await axios.post('http://localhost:8080/api/cards/transfer', transferForm.value, {
        withCredentials: true
      })
      await loadCards()
      showTransferModal.value = false
      resetTransferForm()
      
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  }

  const resetForm = () => {
    form.value = {
      cardNumber: '',
      expiryDate: '',
      balance: 0,
      selectedBankId: null
    }
  }

  const resetTransferForm = () => {
    transferForm.value = {
      sourceCardId: null,
      destinationCardId: null,
      transferAmount: null
    }
  }

  return {
    // State
    cards,
    banks,
    totalBalance,
    totalTransfers,
    showNewCardModal,
    showTransferModal,
    form,
    transferForm,
    
    // Getters
    hasMultipleCards,
    
    // Actions
    loadCards,
    loadBanks,
    createCard,
    transferMoney,
    resetForm,
    resetTransferForm
  }
})