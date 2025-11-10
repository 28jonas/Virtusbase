<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden h-full flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-teal-50 dark:from-gray-700 to-white dark:to-gray-800 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
      <h1 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-teal-500 dark:text-teal-400" viewBox="0 0 20 20" fill="currentColor">
          <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
          <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
        </svg>
        Transactions
      </h1>
    </div>

    <!-- Main Transactions -->
    <div class="flex-1 overflow-y-auto" style="max-height: 350px;">
      <div v-for="transaction in transactionsStore.transactions" :key="transaction.id"
           class="group m-2 p-4 bg-gradient-to-r from-white/60 dark:from-gray-700/60 to-white/40 dark:to-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-300 dark:border-gray-600 hover:shadow-lg dark:hover:shadow-gray-700/50 hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between">
          <!-- Left side: Icon and details -->
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl" :class="transaction.type === 'expense' ? 'bg-gradient-to-br from-red-100 dark:from-red-900/30 to-red-50 dark:to-red-900/10 border border-red-200 dark:border-red-800' : 'bg-gradient-to-br from-emerald-100 dark:from-emerald-900/30 to-emerald-50 dark:to-emerald-900/10 border border-emerald-200 dark:border-emerald-800'">
              <div class="w-full h-full flex items-center justify-center">
                <svg v-if="transaction.type === 'expense'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
              </div>
            </div>
            <div class="flex-1 space-y-1">
              <h3 class="font-semibold text-slate-800 dark:text-white">
                {{ transaction.description }}
              </h3>
              <p class="text-sm text-warm-gray-500 dark:text-gray-400">
                {{ transaction.category }} - ( •••• •••• •••• {{ transaction.card }})
              </p>
            </div>
          </div>

          <!-- Right side: Amount and date -->
          <div class="text-right space-y-1">
            <p class="font-bold text-lg" :class="transaction.type === 'expense' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'">
              {{ transaction.type === 'expense' ? '-' : '+' }}€{{ formatCurrency(transaction.amount) }}
            </p>
            <p class="text-sm text-warm-gray-500 dark:text-gray-400">
              {{ formatDate(transaction.date) }}
            </p>
          </div>
        </div>
      </div>
      
      <div v-if="transactionsStore.transactions.length === 0" class="px-8 py-6 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="mt-2 text-gray-400 dark:text-gray-500">No transactions found yet.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useTransactionsStore } from '../../stores/transactions'

const transactionsStore = useTransactionsStore()

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

onMounted(async () => {
  await transactionsStore.loadTransactions()
})
</script>