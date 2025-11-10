import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useHabitStore = defineStore('habit', () => {
  // State
  const habits = ref([])
  const weekDays = ref([])
  const currentDate = ref(new Date().toISOString().split('T')[0])
  const stats = ref({
    total_habits: 0,
    completed_today: 0,
    total_completions: 0,
    longest_streak: 0
  })

  // Getters
  const completedHabitsToday = computed(() => {
    return habits.value.filter(habit => {
      const today = new Date().toISOString().split('T')[0]
      return habit.completions && habit.completions.some(c => c.completion_date === today)
    }).length
  })

  // Actions
  const fetchHabits = async (date = null) => {
    try {
      const params = date ? { week_start: date } : {}
      const res = await axios.get('http://localhost:8080/api/habits', {
        params,
        withCredentials: true
      })
      
      habits.value = res.data.data.habits || []
      weekDays.value = res.data.data.week_days || []
      currentDate.value = res.data.data.current_date || new Date().toISOString().split('T')[0]
    } catch (err) {
      console.error('Failed to fetch habits:', err)
      throw err
    }
  }

  const createHabit = async (payload) => {
    try {
      const res = await axios.post('http://localhost:8080/api/habits', payload, {
        withCredentials: true
      })
      habits.value.push(res.data.data)
      return res.data.data
    } catch (err) {
      console.error('Failed to create habit:', err)
      throw err
    }
  }

  const updateHabit = async (id, payload) => {
    try {
      const res = await axios.put(`http://localhost:8080/api/habits/${id}`, payload, {
        withCredentials: true
      })
      const idx = habits.value.findIndex(h => h.id === id)
      if (idx !== -1) habits.value[idx] = res.data.data
      return res.data.data
    } catch (err) {
      console.error('Failed to update habit:', err)
      throw err
    }
  }

  const deleteHabit = async (id) => {
    try {
      await axios.delete(`http://localhost:8080/api/habits/${id}`, {
        withCredentials: true
      })
      habits.value = habits.value.filter(h => h.id !== id)
    } catch (err) {
      console.error('Failed to delete habit:', err)
      throw err
    }
  }

  const toggleHabitCompletion = async (habitId, date) => {
  try {
    const res = await axios.patch(`http://localhost:8080/api/habits/${habitId}/toggle-completion`, 
      { date },
      { withCredentials: true }
    )
    
    // Refresh de habits om de laatste completionStatus op te halen
    await fetchHabits()
    
    return res.data.data
  } catch (err) {
    console.error('Failed to toggle habit completion:', err)
    throw err
  }
}

  const previousWeek = async () => {
    try {
      const current = new Date(currentDate.value)
      const previousWeekDate = new Date(current.setDate(current.getDate() - 7))
      await fetchHabits(previousWeekDate.toISOString().split('T')[0])
    } catch (err) {
      console.error('Failed to navigate to previous week:', err)
      throw err
    }
  }

  const nextWeek = async () => {
    try {
      const current = new Date(currentDate.value)
      const nextWeekDate = new Date(current.setDate(current.getDate() + 7))
      await fetchHabits(nextWeekDate.toISOString().split('T')[0])
    } catch (err) {
      console.error('Failed to navigate to next week:', err)
      throw err
    }
  }

  const fetchStats = async () => {
    try {
      const res = await axios.get('http://localhost:8080/api/habits/stats', {
        withCredentials: true
      })
      stats.value = res.data.data
    } catch (err) {
      console.error('Failed to fetch habit stats:', err)
      throw err
    }
  }

  // Helper functie om completion status te checken
  // Tijdelijke helper functie
const isHabitCompletedOnDate = (habit, date) => {
  if (habit.completionStatus && habit.completionStatus[date] !== undefined) {
    return habit.completionStatus[date]
  }
  return false
}


  return {
    // State
    habits,
    weekDays,
    currentDate,
    stats,
    
    // Getters
    completedHabitsToday,
    
    // Actions
    fetchHabits,
    createHabit,
    updateHabit,
    deleteHabit,
    toggleHabitCompletion,
    previousWeek,
    nextWeek,
    fetchStats,
    isHabitCompletedOnDate
  }
})