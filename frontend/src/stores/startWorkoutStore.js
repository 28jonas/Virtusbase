import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useStartWorkoutStore = defineStore('startWorkout', () => {
  const scheduledWorkout = ref(null)
  const previousWorkout = ref(null)
  const latestActivity = ref(null)
  const activitySource = ref(null)
  const stravaError = ref(null)
  const isLoading = ref(false)

  const formattedDistance = computed(() => {
    if (!latestActivity.value || !latestActivity.value.distance) {
      return '0 km'
    }
    const distanceInKm = latestActivity.value.distance / 1000
    return `${distanceInKm.toFixed(1)} km`
  })

  const formattedDuration = computed(() => {
    if (!latestActivity.value || !latestActivity.value.moving_time) {
      return '0 min'
    }
    const minutes = Math.floor(latestActivity.value.moving_time / 60)
    const seconds = latestActivity.value.moving_time % 60
    if (minutes > 60) {
      const hours = Math.floor(minutes / 60)
      const remainingMinutes = minutes % 60
      return `${hours}h ${remainingMinutes.toString().padStart(2, '0')}m`
    }
    return `${minutes}m ${seconds.toString().padStart(2, '0')}s`
  })

  const formattedSpeed = computed(() => {
    if (!latestActivity.value || !latestActivity.value.average_speed) {
      return '0 km/h'
    }
    const speedKmh = latestActivity.value.average_speed * 3.6
    return `${speedKmh.toFixed(1)} km/h`
  })

  const loadWorkoutData = async () => {
    isLoading.value = true
    try {
      const response = await axios.get('http://localhost:8080/api/start-workout', {
        withCredentials: true
      })
      const data = response.data
      console.log('Workout data loaded:', data)
      scheduledWorkout.value = data.scheduledWorkout
      previousWorkout.value = data.previousWorkout
      latestActivity.value = data.latestActivity
      activitySource.value = data.activitySource
      stravaError.value = data.stravaError
    } catch (error) {
      console.error('Failed to load workout data:', error)
    } finally {
      isLoading.value = false
    }
  }

  const refreshStravaData = async () => {
    isLoading.value = true
    try {
      await axios.post('/api/start-workout/refresh-strava')
      await loadWorkoutData()
      
      if (!stravaError.value) {
        // Show success message
        console.log('Strava data vernieuwd!')
      }
    } catch (error) {
      console.error('Failed to refresh Strava data:', error)
    } finally {
      isLoading.value = false
    }
  }

  const finishWorkout = async () => {
  if (!scheduledWorkout.value) {
    console.error('Geen actieve workout gevonden.')
    return
  }
  try {
    await axios.post(
      `http://localhost:8080/api/start-workout/${scheduledWorkout.value.id}/finish`, 
      {}, // Lege request body
      {
        withCredentials: true  // Correcte positie voor config
      }
    )
    await loadWorkoutData()
    console.log('Workout voltooid!')
  } catch (error) {
    console.error('Failed to finish workout:', error)
  }
}

  const startWorkout = () => {
    if (!scheduledWorkout.value) {
      console.error('Geen geplande workout gevonden.')
      return
    }
    // Redirect to workout start page
    window.location.href = `/workouts/start/${scheduledWorkout.value.id}`
  }

  const viewWorkoutResults = () => {
    if (!previousWorkout.value) {
      console.error('Geen vorige workout gevonden.')
      return
    }
    // Redirect to workout results page
    window.location.href = `/workouts/results/${previousWorkout.value.id}`
  }

  return {
    scheduledWorkout,
    previousWorkout,
    latestActivity,
    activitySource,
    stravaError,
    isLoading,
    formattedDistance,
    formattedDuration,
    formattedSpeed,
    loadWorkoutData,
    refreshStravaData,
    finishWorkout,
    startWorkout,
    viewWorkoutResults
  }
})