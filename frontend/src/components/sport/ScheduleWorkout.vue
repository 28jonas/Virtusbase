<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border shadow-sm border-gray-100 dark:border-gray-700 overflow-hidden h-full flex flex-col">
    <!-- Schedule Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-teal-50 dark:from-teal-900/20 to-white dark:to-gray-800 flex justify-between border-b border-gray-100 dark:border-gray-700 flex-col">
      <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
             fill="currentColor" viewBox="0 0 24 24" class="m-2 text-gray-800 dark:text-gray-200">
          <path
              d="m22,8c0-.55-.45-1-1-1h-2v-1c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1v5h-4v-5c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1v1h-2c-.55,0-1,.45-1,1v3h-1v2h1v3c0,.55.45,1,1,1h2v1c0,.55.45,1,1,1h3c.55,0,1-.45,1-1v-5h4v5c0,.55.45,1,1,1h3c.55,0,1-.45,1-1v-1h2c.55,0,1-.45,1-1v-3h1v-2h-1v-3ZM4,15v-6h1v6h-1Zm4,2h-1V7h1v10Zm9,0h-1V7h1v10Zm3-2h-1v-6h1v6Z"></path>
        </svg>
        My workout schema
      </h1>
      <button @click="openAddWorkoutModal"
              class="w-full px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-emerald-500/30 transition-all duration-300 transform">
        Add workout +
      </button>
    </div>

    <!-- Schedule List -->
    <div class="h-54 overflow-y-auto">
      <ul>
        <li
          v-for="item in store.scheduleItems"
          :key="item.id"
          class="group m-2 p-4 bg-gradient-to-r from-white/60 dark:from-gray-700/60 to-white/40 dark:to-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-300 dark:border-gray-600 hover:shadow-lg hover:scale-[1.02] transition-all duration-300"
        >
          <div class="flex items-start gap-4">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-800 dark:text-gray-200">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
              </svg>
            </div>
            <div class="flex-1 space-y-2">
              <h3 class="font-semibold text-slate-800 dark:text-gray-200">
                {{ item.name }}
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ item.duration }}</p>
              <p class="text-sm text-warm-gray-500 dark:text-gray-400">{{ formatDate(item.scheduled_at) }}</p>
            </div>
          </div>
        </li>
      </ul>
      
      <div v-if="store.scheduleItems.length === 0" class="p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
             fill="currentColor" viewBox="0 0 24 24" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600">
          <path
              d="m22,8c0-.55-.45-1-1-1h-2v-1c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1v5h-4v-5c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1v1h-2c-.55,0-1,.45-1,1v3h-1v2h1v3c0,.55.45,1,1,1h2v1c0,.55.45,1,1,1h3c.55,0,1-.45,1-1v-5h4v5c0,.55.45,1,1,1h3c.55,0,1-.45,1-1v-1h2c.55,0,1-.45,1-1v-3h1v-2h-1v-3ZM4,15v-6h1v6h-1Zm4,2h-1V7h1v10Zm9,0h-1V7h1v10Zm3-2h-1v-6h1v6Z"></path>
        </svg>
        <p class="mt-2 text-gray-400 dark:text-gray-500">It appears you didn't add any workouts yet.</p>
      </div>
    </div>

    <!-- Add Workout Modal -->
    <div
      v-if="showAddWorkoutModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg dark:text-gray-200">Nieuwe Workout Toevoegen</h3>
          <button @click="closeAddWorkoutModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleAddWorkout">
          <!-- Basic Workout Info -->
          <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 mb-2" for="name">Title</label>
            <input v-model="newWorkout.name" type="text" id="name"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                   placeholder="E.g. Run with friends, Tour of the Alps">
            <div v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</div>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 mb-2" for="type">Type Workout</label>
            <input v-model="newWorkout.type" type="text" id="type"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"
                   placeholder="bijv. Yoga, Stretch, Cardio">
            <div v-if="errors.type" class="text-red-500 text-sm mt-1">{{ errors.type[0] }}</div>
          </div>

          <!-- List of Added Exercises -->
          <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
              <h4 class="font-medium dark:text-gray-200">Added exercises</h4>
              <button 
                type="button" 
                @click="openAddExerciseModal"
                class="bg-purple-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-purple-600 transition-colors"
              >
                Add Exercise +
              </button>
            </div>
            
            <div v-if="workoutExercises.length > 0" class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md max-h-40 overflow-y-auto">
              <div
                v-for="(exercise, index) in workoutExercises"
                :key="index"
                class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600 last:border-b-0"
              >
                <div>
                  <div class="font-medium dark:text-gray-200">{{ exercise.name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ exercise.sets }} sets
                    × {{ exercise.reps }} reps × {{ exercise.weight }}kg
                  </div>
                </div>
                <button type="button" @click="removeExercise(index)"
                        class="text-red-500 hover:text-red-700">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                       xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
            
            <div v-else class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
              <p class="text-gray-500 dark:text-gray-400 text-sm">No exercises added yet</p>
            </div>
          </div>

          <div class="mb-3">
            <label class="block text-gray-700 dark:text-gray-300 mb-2" for="duration">Duration</label>
            <input v-model="newWorkout.duration" type="time" id="duration"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200" placeholder="1">
            <div v-if="errors.duration" class="text-red-500 text-sm mt-1">{{ errors.duration[0] }}</div>
          </div>

          <div class="mb-4">
            <label for="schedule_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Schedule at</label>
            <input
              v-model="newWorkout.schedule_at"
              id="schedule_at"
              type="datetime-local"
              name="schedule_at"
              placeholder="E.g. 2025-03-01"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-200"
            />
            <div v-if="errors.schedule_at" class="text-red-500 text-sm mt-1">{{ errors.schedule_at[0] }}</div>
          </div>

          <div class="mb-3">
            <label class="block text-gray-700 dark:text-gray-300 mb-2" for="notes">Notes</label>
            <textarea v-model="newWorkout.notes" id="notes"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"></textarea>
            <div v-if="errors.notes" class="text-red-500 text-sm mt-1">{{ errors.notes[0] }}</div>
          </div>

          <div class="flex justify-end">
            <button
              type="button"
              @click="closeAddWorkoutModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 mr-2"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="store.isLoading"
              class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50"
            >
              {{ store.isLoading ? 'Saving...' : 'Save workout' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add Exercise Modal -->
    <div
      v-if="showAddExerciseModal"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg dark:text-gray-200">Add Exercise</h3>
          <button @click="closeAddExerciseModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleAddExercise">
          <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 mb-2" for="exercise">Exercise</label>
            <select v-model="currentExercise.exercise_id" id="exercise-select"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200">
              <option value="">-- Select an exercise --</option>
              <option
                v-for="exercise in store.exercises"
                :key="exercise.id"
                :value="exercise.id"
              >
                {{ exercise.name }} ({{ exercise.muscle_group }})
              </option>
            </select>
            <div v-if="exerciseErrors.exercise_id" class="text-red-500 text-sm mt-1">{{ exerciseErrors.exercise_id }}</div>
          </div>

          <div class="grid grid-cols-3 gap-3 mb-4">
            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2" for="sets">Sets</label>
              <input v-model="currentExercise.sets" type="number" id="sets"
                     class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200" placeholder="3" min="1">
              <div v-if="exerciseErrors.sets" class="text-red-500 text-sm mt-1">{{ exerciseErrors.sets }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2" for="reps">Reps</label>
              <input v-model="currentExercise.reps" type="number" id="reps"
                     class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200" placeholder="10" min="1">
              <div v-if="exerciseErrors.reps" class="text-red-500 text-sm mt-1">{{ exerciseErrors.reps }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 mb-2" for="weight">Gewicht (kg)</label>
              <input v-model="currentExercise.weight" type="number" id="weight"
                     class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200" placeholder="10" min="0" step="0.5">
              <div v-if="exerciseErrors.weight" class="text-red-500 text-sm mt-1">{{ exerciseErrors.weight }}</div>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="button"
              @click="closeAddExerciseModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 mr-2"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors"
            >
              Add Exercise
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useScheduleWorkoutStore } from '../../stores/scheduleWorkoutStore'

const store = useScheduleWorkoutStore()

// Modal states
const showAddWorkoutModal = ref(false)
const showAddExerciseModal = ref(false)

// Form data
const newWorkout = ref({
  name: '',
  type: '',
  duration: '',
  schedule_at: new Date().toISOString().slice(0, 16),
  notes: ''
})

const workoutExercises = ref([])

const currentExercise = ref({
  exercise_id: null,
  sets: null,
  reps: null,
  weight: null
})

// Errors
const errors = ref({})
const exerciseErrors = reactive({
  exercise_id: '',
  sets: '',
  reps: '',
  weight: ''
})

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('nl', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Modal functions
const openAddWorkoutModal = async () => {
  resetForms()
  showAddWorkoutModal.value = true
  
  try {
    await store.loadExercises()
  } catch (error) {
    console.error('Failed to load exercises:', error)
  }
}

const closeAddWorkoutModal = () => {
  showAddWorkoutModal.value = false
  showAddExerciseModal.value = false
  resetForms()
}

const openAddExerciseModal = () => {
  showAddExerciseModal.value = true
  resetExerciseErrors()
}

const closeAddExerciseModal = () => {
  showAddExerciseModal.value = false
}

// Form reset functions
const resetForms = () => {
  newWorkout.value = {
    name: '',
    type: '',
    duration: '',
    schedule_at: new Date().toISOString().slice(0, 16),
    notes: ''
  }
  workoutExercises.value = []
  currentExercise.value = {
    exercise_id: null,
    sets: null,
    reps: null,
    weight: null
  }
  errors.value = {}
  resetExerciseErrors()
}

const resetExerciseErrors = () => {
  exerciseErrors.exercise_id = ''
  exerciseErrors.sets = ''
  exerciseErrors.reps = ''
  exerciseErrors.weight = ''
}

// Validation functions
const validateExercise = () => {
  resetExerciseErrors()
  let isValid = true

  if (!currentExercise.value.exercise_id) {
    exerciseErrors.exercise_id = 'Please select an exercise'
    isValid = false
  }

  if (!currentExercise.value.sets || currentExercise.value.sets < 1) {
    exerciseErrors.sets = 'Please enter a valid number of sets'
    isValid = false
  }

  if (!currentExercise.value.reps || currentExercise.value.reps < 1) {
    exerciseErrors.reps = 'Please enter a valid number of reps'
    isValid = false
  }

  if (!currentExercise.value.weight || currentExercise.value.weight < 0) {
    exerciseErrors.weight = 'Please enter a valid weight'
    isValid = false
  }

  return isValid
}

// Exercise functions
const handleAddExercise = () => {
  if (!validateExercise()) return

  const exercise = store.exercises.find(e => e.id === currentExercise.value.exercise_id)

  if (!exercise) {
    exerciseErrors.exercise_id = 'Exercise not found'
    return
  }

  workoutExercises.value.push({
    exercise_id: currentExercise.value.exercise_id,
    name: exercise.name,
    sets: currentExercise.value.sets,
    reps: currentExercise.value.reps,
    weight: currentExercise.value.weight
  })

  // Reset current exercise
  currentExercise.value = {
    exercise_id: null,
    sets: null,
    reps: null,
    weight: null
  }

  closeAddExerciseModal()
}

const removeExercise = (index) => {
  workoutExercises.value.splice(index, 1)
}

// Workout functions
const handleAddWorkout = async () => {
  try {
    errors.value = {}
    
    const workoutData = {
      ...newWorkout.value,
      exercises: workoutExercises.value
    }

    await store.createWorkout(workoutData)
    closeAddWorkoutModal()
  } catch (error) {
    if (Array.isArray(error)) {
      errors.value = error
    } else {
      console.error('Failed to add workout:', error)
    }
  }
}

// Initialize
onMounted(() => {
  store.loadScheduleItems()
})
</script>