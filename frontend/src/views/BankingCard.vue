<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Banking Cards</h1>
          <p class="text-gray-600 mt-2">Manage your bank cards and transfers</p>
        </div>
        <div class="flex space-x-4">
          <button 
            @click="openTransferModal"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
          >
            Transfer Money
          </button>
          <button 
            @click="openNewCardModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
          >
            + New Card
          </button>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <div class="text-2xl font-bold text-gray-900">{{ stats.card_count }}</div>
          <div class="text-gray-600 text-sm">Total Cards</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <div class="text-2xl font-bold text-green-600">€{{ stats.total_balance.toFixed(2) }}</div>
          <div class="text-gray-600 text-sm">Current Balance</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <div class="text-2xl font-bold text-blue-600">€{{ stats.total_transfers.toFixed(2) }}</div>
          <div class="text-gray-600 text-sm">Pending Transfers</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <div class="text-2xl font-bold text-purple-600">€{{ stats.total_balance_with_transfers.toFixed(2) }}</div>
          <div class="text-gray-600 text-sm">Total Available</div>
        </div>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="card in cards" 
          :key="card.id"
          class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-xl p-6 shadow-lg"
        >
          <div class="flex justify-between items-start mb-6">
            <div>
              <div class="text-lg font-semibold">{{ card.bank?.name || 'Bank' }}</div>
              <div class="text-sm opacity-80">**** **** **** {{ card.last_four }}</div>
            </div>
            <div class="text-right">
              <div class="text-sm opacity-80">Expires</div>
              <div class="font-semibold">{{ card.expiry_date }}</div>
            </div>
          </div>
          
          <div class="text-2xl font-bold mb-4">€{{ card.balance.toFixed(2) }}</div>
          
          <div class="flex justify-between items-center">
            <button 
              @click="editCard(card)"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button 
              @click="deleteCard(card.id)"
              class="text-white hover:text-red-200 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div 
          v-if="cards.length === 0"
          class="col-span-full text-center py-12 text-gray-500"
        >
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
          <p class="text-lg mb-2">No cards yet</p>
          <p class="text-sm mb-4">Add your first bank card to get started</p>
          <button 
            @click="openNewCardModal"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
          >
            Add Card
          </button>
        </div>
      </div>
    </div>

    <!-- New Card Modal -->
    <div 
      v-if="showNewCardModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">
          {{ isEditing ? 'Edit Card' : 'Add New Card' }}
        </h3>
        
        <form @submit.prevent="isEditing ? updateCard() : createCard()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bank *</label>
            <select
              v-model="form.bank_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select a bank</option>
              <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                {{ bank.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number *</label>
            <input
              v-model="form.card_number"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="1234 5678 9012 3456"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (MM/YY) *</label>
            <input
              v-model="form.expiry_date"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="12/25"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Balance *</label>
            <input
              v-model="form.balance"
              type="number"
              step="0.01"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0.00"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeNewCardModal"
              class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-semibold"
            >
              {{ isEditing ? 'Update' : 'Add' }} Card
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Transfer Modal -->
    <div 
      v-if="showTransferModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    >
      <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Transfer Money</h3>
        
        <form @submit.prevent="transferMoney" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Card *</label>
            <select
              v-model="transferForm.source_card_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select source card</option>
              <option v-for="card in cards" :key="card.id" :value="card.id">
                {{ card.bank?.name }} (****{{ card.last_four }}) - €{{ card.balance.toFixed(2) }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Card *</label>
            <select
              v-model="transferForm.destination_card_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select destination card</option>
              <option 
                v-for="card in cards" 
                :key="card.id" 
                :value="card.id"
                :disabled="card.id === transferForm.source_card_id"
              >
                {{ card.bank?.name }} (****{{ card.last_four }}) - €{{ card.balance.toFixed(2) }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
            <input
              v-model="transferForm.amount"
              type="number"
              step="0.01"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0.00"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeTransferModal"
              class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-semibold"
            >
              Transfer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { useCardStore } from '../stores/bankingCard'

export default {
  name: 'Banking',
  setup() {
    const cardStore = useCardStore()
    
    const showNewCardModal = ref(false)
    const showTransferModal = ref(false)
    const isEditing = ref(false)
    const editingCardId = ref(null)

    const form = reactive({
      bank_id: '',
      card_number: '',
      expiry_date: '',
      balance: 0
    })

    const transferForm = reactive({
      source_card_id: '',
      destination_card_id: '',
      amount: 0
    })

    // Gebruik de formatted getters
    const cards = computed(() => cardStore.formattedCards)
    const banks = computed(() => cardStore.banks)
    const stats = computed(() => cardStore.formattedStats)

    onMounted(async () => {
      await cardStore.fetchCards()
      await cardStore.fetchBanks()
      await cardStore.fetchStats()
    })

    const openNewCardModal = (card = null) => {
      console.log('openNewCardModal called', card);
      if (card) {
        // Edit mode
        isEditing.value = true
        editingCardId.value = card.id
        form.bank_id = card.bank_id
        form.card_number = '**** **** **** ' + card.last_four
        form.expiry_date = card.expiry_date
        form.balance = card.balance // Dit is nu al een number door formattedCards
      } else {
        // Create mode
        isEditing.value = false
        editingCardId.value = null
        resetForm()
      }
      showNewCardModal.value = true
    }

    const closeNewCardModal = () => {
      showNewCardModal.value = false
      resetForm()
    }

    const resetForm = () => {
      form.bank_id = ''
      form.card_number = ''
      form.expiry_date = ''
      form.balance = 0
    }

    const openTransferModal = () => {
      if (cards.value.length < 2) {
        alert('You need at least two cards to make a transfer.')
        return
      }
      showTransferModal.value = true
      transferForm.source_card_id = cards.value[0]?.id || ''
      transferForm.destination_card_id = ''
      transferForm.amount = 0
    }

    const closeTransferModal = () => {
      showTransferModal.value = false
      transferForm.source_card_id = ''
      transferForm.destination_card_id = ''
      transferForm.amount = 0
    }

    const createCard = async () => {
      try {
        await cardStore.createCard(form)
        closeNewCardModal()
      } catch (error) {
        alert('Failed to create card: ' + error.response?.data?.message || error.message)
      }
    }

    const updateCard = async () => {
      try {
        const updateData = {
          balance: form.balance,
          expiry_date: form.expiry_date
        }
        await cardStore.updateCard(editingCardId.value, updateData)
        closeNewCardModal()
      } catch (error) {
        alert('Failed to update card: ' + error.response?.data?.message || error.message)
      }
    }

    const deleteCard = async (cardId) => {
      if (confirm('Are you sure you want to delete this card?')) {
        try {
          await cardStore.deleteCard(cardId)
        } catch (error) {
          alert('Failed to delete card: ' + error.response?.data?.message || error.message)
        }
      }
    }

    const transferMoney = async () => {
      try {
        await cardStore.transfer(transferForm)
        closeTransferModal()
        alert('Transfer completed successfully!')
      } catch (error) {
        alert('Failed to transfer: ' + error.response?.data?.message || error.message)
      }
    }

    return {
      cards,
      banks,
      stats,
      showNewCardModal,
      showTransferModal,
      isEditing,
      form,
      transferForm,
      openNewCardModal,
      closeNewCardModal,
      openTransferModal,
      closeTransferModal,
      createCard,
      updateCard,
      deleteCard,
      transferMoney,
      formatCurrency: cardStore.formatCurrency // Gebruik de helper uit de store
    }
  }
}

</script>