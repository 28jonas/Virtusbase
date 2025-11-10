<template>
  <div class="grid grid-cols-1 gap-4 justify-items-stretch">
    <!-- Income Card -->
    <div class="bg-gradient-to-r from-teal-600 to-teal-700 dark:from-teal-700 dark:to-teal-800 rounded-2xl p-6 text-white shadow-lg dark:shadow-teal-900/50">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-teal-500/30 dark:bg-teal-600/30 rounded-xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-sm text-teal-200 dark:text-teal-300">Total Income (This Month)</p>
          <p class="text-2xl font-bold">€{{ formatCurrency(statsStore.totalIncome) }}</p>
        </div>
        <div class="ml-auto text-lg font-medium" :class="statsStore.incomePercentage >= 0 ? 'text-teal-300 dark:text-teal-200' : 'text-red-300 dark:text-red-200'">
          {{ statsStore.incomePercentage >= 0 ? '+' : '' }}{{ statsStore.incomePercentage }}%
        </div>
      </div>
    </div>

    <!-- Expense Card -->
    <div class="bg-gradient-to-r from-rose-600 to-rose-700 dark:from-rose-700 dark:to-rose-800 rounded-2xl p-6 text-white shadow-lg dark:shadow-rose-900/50">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-rose-500/30 dark:bg-rose-600/30 rounded-xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-sm text-rose-200 dark:text-rose-300">Total Expenses (This Month)</p>
          <p class="text-2xl font-bold">€{{ formatCurrency(statsStore.totalExpense) }}</p>
        </div>
        <div class="ml-auto text-lg font-medium" :class="statsStore.expensePercentage <= 0 ? 'text-teal-300 dark:text-teal-200' : 'text-red-300 dark:text-red-200'">
          {{ statsStore.expensePercentage >= 0 ? '+' : '' }}{{ statsStore.expensePercentage }}%
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useStatsStore } from '../../stores/stats'

const statsStore = useStatsStore()

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

onMounted(async () => {
  await statsStore.loadStats()
})
</script>