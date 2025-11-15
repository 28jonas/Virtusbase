import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useChartStore = defineStore('charts', () => {
  // State
  const cards = ref([])
  const selectedCardId = ref(null)
  const timeRange = ref('30d')
  const chartData = ref([])
  const loading = ref(false)
  const rangeOptions = ref({
    '7d': 'Last 7 days',
    '14d': 'Last 14 days',
    '30d': 'Last 30 days',
    '60d': 'Last 60 days',
    '90d': 'Last 90 days',
    '1y': 'Last year',
    'all': 'All dates',
  })

  // Getters
  const chartOptions = computed(() => {
    if (chartData.value.length === 0) {
      return getEmptyChartOptions()
    }

    return {
      chart: {
        type: 'line',
        height: '100%',
        animations: { enabled: false },
        toolbar: { show: false }
      },
      colors: ['#4F46E5'],
      stroke: {
        curve: 'smooth',
        width: 3
      },
      dataLabels: { enabled: false },
      markers: {
        size: 4,
        strokeWidth: 0
      },
      series: [
        {
          name: 'Balance',
          data: chartData.value.map(item => ({
            x: new Date(item.snapshot_date).getTime(),
            y: parseFloat(item.balance)
          }))
        }
      ],
      tooltip: {
        shared: true,
        intersect: false,
        x: {
          format: 'dd MMM yyyy'
        },
        y: {
          formatter: (value) => `€${value?.toFixed(2) || '0.00'}`
        }
      },
      grid: {
        show: true,
        borderColor: '#f1f1f1'
      },
      xaxis: {
        type: 'datetime',
        labels: {
          datetimeFormatter: {
            year: 'yyyy',
            month: 'MMM yyyy',
            day: 'dd MMM',
            hour: 'HH:mm'
          }
        }
      },
      yaxis: {
        title: { text: 'Balance (€)' },
        decimalsInFloat: 2,
        labels: {
          formatter: (value) => `€${value?.toFixed(2) || '0.00'}`
        }
      }
    }
  })

  // Actions
  const loadCards = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/cards`,{
        withCredentials:true
      })
      cards.value = response.data.cards
      if (cards.value.length > 0 && !selectedCardId.value) {
        selectedCardId.value = cards.value[0].id
      }
    } catch (error) {
      console.error('Failed to load cards:', error)
    }
  }

  const loadChartData = async () => {
    if (!selectedCardId.value) return

    loading.value = true
    try {
      const response = await axios.get(`${API_BASE}/api/chart/balance`, {
        withCredentials: true,
        params: {
          card_id: selectedCardId.value,
          range: timeRange.value
        }
      })
      chartData.value = response.data
    } catch (error) {
      console.error('Failed to load chart data:', error)
      chartData.value = []
    } finally {
      loading.value = false
    }
  }

  const updateChartData = async () => {
    await loadChartData()
  }

  const getEmptyChartOptions = () => {
    return {
      chart: {
        type: 'line',
        height: '100%',
        animations: { enabled: false },
        toolbar: { show: false }
      },
      series: [{
        name: 'Balance',
        data: []
      }],
      xaxis: {
        type: 'datetime'
      },
      yaxis: {
        title: { text: 'Balance (€)' }
      }
    }
  }

  return {
    // State
    cards,
    selectedCardId,
    timeRange,
    chartData,
    loading,
    rangeOptions,
    
    // Getters
    chartOptions,
    
    // Actions
    loadCards,
    loadChartData,
    updateChartData
  }
})