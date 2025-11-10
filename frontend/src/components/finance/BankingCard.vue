<template>
  <div class="bg-gradient-to-r from-indigo-700 to-indigo-800 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 text-white shadow-lg overflow-hidden h-full flex flex-col">
    <!-- Flash Messages -->
    <div v-if="error" class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-200 p-4 mb-4 rounded-lg">
      <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ error }}</span>
      </div>
    </div>

    <!-- Main Card Component -->
    <div>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
          </svg>
          My Cards
        </h2>
        <div class="text-sm bg-indigo-600 dark:bg-gray-700 px-3 py-1 rounded-full">
          {{ bankingStore.cards.length }} cards
        </div>
      </div>

      <div class="mb-4">
        <p class="text-sm text-indigo-200 dark:text-gray-300 mb-1">Total Balance</p>
        <div class="flex items-baseline">
          <p class="text-3xl font-bold">€{{ formatCurrency(bankingStore.totalBalance) }}</p>
          <p class="text-sm text-indigo-300 dark:text-gray-400 ml-2">(€{{ formatCurrency(bankingStore.totalTransfers) }} reserved)</p>
        </div>
      </div>

      <CardsCarousel v-if="bankingStore.cards.length > 0" :cards="bankingStore.cards" />
      
      <EmptyCardsState v-else />

      <div class="flex space-x-3">
        <button @click="bankingStore.showNewCardModal = true"
                class="flex-1 bg-white/10 hover:bg-white/20 dark:bg-gray-700/50 dark:hover:bg-gray-700/70 text-white py-3 rounded-lg transition-colors duration-200 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Card
        </button>
        <button v-if="bankingStore.hasMultipleCards" @click="openTransferModal"
                class="flex-1 border border-indigo-400 dark:border-gray-600 text-white py-3 rounded-lg hover:bg-indigo-700 dark:hover:bg-gray-700 transition-colors duration-200">
          Transfer Money
        </button>
      </div>
    </div>

    <!-- New Card Modal -->
    <Modal :show="bankingStore.showNewCardModal" @close="bankingStore.showNewCardModal = false">
      <template #header>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Card</h3>
      </template>
      
      <form @submit.prevent="handleCreateCard">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bank</label>
            <select v-model="bankingStore.form.selectedBankId"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
              <option value="">Select a bank</option>
              <option v-for="bank in bankingStore.banks" :key="bank.id" :value="bank.id">
                {{ bank.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Card Number</label>
            <input type="text"
                   v-model="bankingStore.form.cardNumber"
                   placeholder="Enter card number"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiry Date</label>
              <input type="text" v-model="bankingStore.form.expiryDate" placeholder="MM/YY"
                     class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Initial Balance</label>
              <input type="number" step="0.01" v-model="bankingStore.form.balance"
                     class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" @click="bankingStore.showNewCardModal = false"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800">
            Add Card
          </button>
        </div>
      </form>
    </Modal>

    <!-- Transfer Modal -->
    <Modal :show="bankingStore.showTransferModal" @close="bankingStore.showTransferModal = false">
      <template #header>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Transfer Money</h3>
      </template>

      <form @submit.prevent="handleTransfer">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Card</label>
            <select v-model="bankingStore.transferForm.sourceCardId"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
              <option value="">Select source card</option>
              <option v-for="card in bankingStore.cards" :key="card.id" :value="card.id">
                {{ card.bank.name }} - €{{ formatCurrency(card.balance) }} (•••• {{ card.last_four }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Card</label>
            <select v-model="bankingStore.transferForm.destinationCardId"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
              <option value="">Select destination card</option>
              <option v-for="card in bankingStore.cards" :key="card.id" :value="card.id">
                {{ card.bank.name }} - €{{ formatCurrency(card.balance) }} (•••• {{ card.last_four }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
            <input type="number" step="0.01" v-model="bankingStore.transferForm.transferAmount"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 dark:text-white dark:bg-gray-700">
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" @click="bankingStore.showTransferModal = false"
                  class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800">
            Transfer
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBankingStore } from "../../stores/banking";
import Modal from './Modal.vue'
import CardsCarousel from './CardsCarousel.vue'
import EmptyCardsState from './EmptyCardsState.vue'

const bankingStore = useBankingStore()
const error = ref('')

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const openTransferModal = () => {
  if (bankingStore.cards.length < 2) {
    error.value = 'Je hebt minimaal twee kaarten nodig om geld over te maken.'
    return
  }
  bankingStore.showTransferModal = true
}

const handleCreateCard = async () => {
  try {
    error.value = ''
    await bankingStore.createCard()
  } catch (err) {
    error.value = typeof err === 'string' ? err : 'Fout bij aanmaken kaart. Probeer opnieuw.'
  }
}

const handleTransfer = async () => {
  try {
    error.value = ''
    await bankingStore.transferMoney()
  } catch (err) {
    error.value = typeof err === 'string' ? err : 'Er is een fout opgetreden bij de overdracht. Probeer opnieuw.'
  }
}

onMounted(async () => {
  await bankingStore.loadCards()
  await bankingStore.loadBanks()
})
</script>