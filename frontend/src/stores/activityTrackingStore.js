import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useActivityTrackingStore = defineStore('activityTracking', () => {
  const selectedDate = ref(new Date().getDate())
  const dates = ref([])
  const activityData = ref([])
  const currentWeekStart = ref(null)
  const weekLabel = ref('')
  const isLoading = ref(false)

  const days = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo']

  const getDayName = (index) => {
    return days[index]
  }

  const loadActivityData = async () => {
    isLoading.value = true
    try {
      const response = await axios.get(`${API_BASE}/api/activity-tracking`, {
        withCredentials: true
      })
      const data = response.data
      
      selectedDate.value = data.selectedDate
      dates.value = data.dates
      activityData.value = data.activityData
      currentWeekStart.value = data.currentWeekStart
      weekLabel.value = data.weekLabel
    } catch (error) {
      console.error('Failed to load activity data:', error)
    } finally {
      isLoading.value = false
    }
  }

  const selectDate = async (date) => {
    selectedDate.value = date
    isLoading.value = true
    
    try {
      const response = await axios.post(`${API_BASE}/api/activity-tracking/select-date`, {
        date: date,
        currentWeekStart: currentWeekStart.value
      }, {
        withCredentials: true
      })
      
      const data = response.data
      activityData.value = data.activityData
      selectedDate.value = data.selectedDate
      
      // Emit event for other components
      window.dispatchEvent(new CustomEvent('date-changed', { 
        detail: { date: data.selectedFullDate }
      }))
    } catch (error) {
      console.error('Failed to select date:', error)
    } finally {
      isLoading.value = false
    }
  }

  const previousWeek = async () => {
    isLoading.value = true
    try {
      const response = await axios.post(`${API_BASE}/api/activity-tracking/previous-week`, {
        currentWeekStart: currentWeekStart.value,
        selectedDate: selectedDate.value
      }, {
        withCredentials: true
      })
      
      const data = response.data
      dates.value = data.dates
      weekLabel.value = data.weekLabel
      currentWeekStart.value = data.currentWeekStart
      selectedDate.value = data.selectedDate
    } catch (error) {
      console.error('Failed to load previous week:', error)
    } finally {
      isLoading.value = false
    }
  }

  const nextWeek = async () => {
    isLoading.value = true
    try {
      const response = await axios.post(`${API_BASE}/api/activity-tracking/next-week`, {
        currentWeekStart: currentWeekStart.value,
        selectedDate: selectedDate.value
      }, {
        withCredentials: true
      })
      
      const data = response.data
      dates.value = data.dates
      weekLabel.value = data.weekLabel
      currentWeekStart.value = data.currentWeekStart
      selectedDate.value = data.selectedDate
    } catch (error) {
      console.error('Failed to load next week:', error)
    } finally {
      isLoading.value = false
    }
  }

  return {
    selectedDate,
    dates,
    activityData,
    currentWeekStart,
    weekLabel,
    isLoading,
    getDayName,
    loadActivityData,
    selectDate,
    previousWeek,
    nextWeek
  }
})