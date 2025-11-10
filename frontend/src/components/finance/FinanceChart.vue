<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm overflow-hidden h-full flex flex-col">
    <!-- Header with dropdowns -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-0 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Balance History
      </h2>

      <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
        <!-- Card selector -->
        <select v-if="chartStore.cards.length > 0" v-model="chartStore.selectedCardId"
                @change="chartStore.updateChartData"
                class="bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-w-48">
          <option v-for="card in chartStore.cards" :key="card.id" :value="card.id">
            {{ card.bank.name }} (••••{{ card.last_four }})
          </option>
        </select>

        <!-- Time range selector -->
        <select v-model="chartStore.timeRange"
                @change="chartStore.updateChartData"
                class="bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option v-for="(label, key) in chartStore.rangeOptions" :key="key" :value="key">
            {{ label }}
          </option>
        </select>
      </div>
    </div>

    <!-- Chart Container -->
    <div id="balanceChart" class="flex-1"></div>

    <!-- Loading state -->
    <div v-if="chartStore.loading" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 flex items-center justify-center rounded-2xl">
      <div class="flex items-center space-x-2">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
        <span class="text-gray-600 dark:text-gray-300">Chart wordt bijgewerkt...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { useChartStore } from '../../stores/chart'
import ApexCharts from 'apexcharts'

const chartStore = useChartStore()
let chart = null

const initChart = () => {
  const el = document.getElementById('balanceChart')
  if (!el || !chartStore.chartOptions) return

  if (chart) {
    chart.destroy()
  }

  chart = new ApexCharts(el, chartStore.chartOptions)
  chart.render()
}

const updateChart = () => {
  if (chart && chartStore.chartOptions) {
    chart.updateOptions(chartStore.chartOptions)
  }
}

onMounted(async () => {
  await chartStore.loadCards()
  await chartStore.loadChartData()
  
  // Initialize chart after data is loaded
  setTimeout(initChart, 100)
})

onUnmounted(() => {
  if (chart) {
    chart.destroy()
  }
})

// Watch for chart data changes
import { watch } from 'vue'
watch(() => chartStore.chartOptions, () => {
  updateChart()
})
</script>