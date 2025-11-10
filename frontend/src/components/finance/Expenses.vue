<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border shadow-sm border-gray-100 dark:border-gray-700 overflow-hidden h-full flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-red-50 dark:from-gray-700 to-white dark:to-gray-800 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
      <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        Expenses by category
      </h1>
      <button
        @click="expensesStore.showModal = true"
        class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <!-- Main Content -->
    <div class="flex-1 px-6 py-4 grid auto-rows-[110px] max-h-[170px] overflow-y-auto">
      <div v-for="category in expensesStore.expensesByCategory" :key="category.id"
           class="group mb-4 p-4 bg-gradient-to-r from-white/60 dark:from-gray-700/60 to-white/40 dark:to-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-600 hover:shadow-lg dark:hover:shadow-none hover:scale-[1.02] transition-all duration-300">
        <div class="flex justify-between items-center mb-3">
          <span class="font-medium text-gray-800 dark:text-gray-200">{{ category.name || 'Uncategorized' }}</span>
          <div class="flex items-center space-x-3">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">€{{ formatCurrency(category.amount) }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">{{ category.percentage }}%</span>
          </div>
        </div>
        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
          <div class="h-full rounded-full bg-gradient-to-r from-red-400 to-red-500 dark:from-red-500 dark:to-red-600 transition-all duration-500 ease-out shadow-sm"
               :style="{ width: category.percentage + '%' }">
          </div>
        </div>
      </div>
      
      <EmptyState v-if="expensesStore.expensesByCategory.length === 0" @add-item="expensesStore.showModal = true" />
    </div>

    <!-- Modal -->
    <Modal :show="expensesStore.showModal" @close="expensesStore.showModal = false">
      <template #header>
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-gray-700 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
              <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Add New Expense</h2>
        </div>
      </template>

      <form @submit.prevent="handleAddExpense">
        <div class="space-y-4">
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <select v-model="expensesStore.form.category_id" id="category" 
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:text-white">
              <option value="">Select a category</option>
              <option v-for="category in expensesStore.categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <div class="flex space-x-2">
            <div class="w-3/4">
              <label for="card" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card</label>
              <select v-model="expensesStore.form.card_id" id="card" 
                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:text-white">
                <option value="">Select a card</option>
                <option v-for="card in expensesStore.cards" :key="card.id" :value="card.id">
                  {{ card.bank.name }} ( •••• •••• •••• {{ card.last_four }})
                </option>
              </select>
            </div>

            <div class="w-1/4">
              <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount (€)</label>
              <input type="number" step="0.01" v-model="expensesStore.form.amount" id="amount" placeholder="0.00" 
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:text-white">
            </div>
          </div>
          
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <input type="text" v-model="expensesStore.form.description" id="description" placeholder="What did you spend on?" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:text-white">
          </div>

          <div>
            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
            <input type="date" v-model="expensesStore.form.date" id="date" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-gray-700 dark:text-white">
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-8">
          <button
            type="button"
            @click="expensesStore.showModal = false"
            class="w-full sm:w-auto px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600 transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 text-white rounded-xl hover:from-red-600 hover:to-red-700 dark:hover:from-red-700 dark:hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
          >
            Save Expense
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useExpensesStore } from '../../stores/expenses'
import Modal from './Modal.vue'
import EmptyState from './EmptyState.vue'

const expensesStore = useExpensesStore()

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const handleAddExpense = async () => {
  try {
    await expensesStore.addExpense()
    await expensesStore.loadExpensesByCategory()
  } catch (error) {
    console.error('Failed to add expense:', error)
  }
}

onMounted(async () => {
  await expensesStore.loadExpenses()
  await expensesStore.loadExpensesByCategory()
  //console.log('Loaded expenses:bycategorie', expensesStore.expensesByCategory)
  await expensesStore.loadCategories()
  await expensesStore.loadCards()
})
</script>