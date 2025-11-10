<!-- views/Habits.vue -->
<template>
    <div class="p-6 space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gewoontes</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Bouw consistente gewoontes op</p>
            </div>
            <button @click="openModal()"
                class="bg-green-500 text-white px-6 py-3 rounded-xl hover:bg-green-600 transition-colors flex items-center space-x-2">
                <span>🌱</span>
                <span>Nieuwe Gewoonte</span>
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ totalHabits }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Totaal Gewoontes</div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ completedToday }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Vandaag Voltooid</div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ currentStreak }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Longeste Streak</div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
                <!-- TODO: Calculate success rate over period -->
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ successRate }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Succes Ratio</div>
            </div>
        </div>

        <!-- Habits Grid -->
        <div v-if="habits.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <HabitCard v-for="habit in habits" :key="habit.id" :habit="habit" @toggle="toggleHabitCompletion"
                @edit="openModal(habit)" />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <div class="text-6xl mb-4">🌱</div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nog geen gewoontes</h3>
            <p class="text-gray-600 dark:text-gray-400">Begin met het aanmaken van je eerste gewoonte!</p>
        </div>

        <!-- Habit History -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Gewoonte Geschiedenis</h2>
            <HabitCalendar :habits="habits" />
        </div>

        <!-- Create/Edit Habit Modal-->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                    {{ isEditing ? 'Gewoonte Bewerken' : 'Nieuwe Gewoonte' }}
                </h3>

                <form @submit.prevent="isEditing ? updateHabit() : createHabit()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Naam *</label>
                        <input v-model="form.name" type="text" required maxlength="25"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Bijv. Ochtendgymnastiek" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschrijving</label>
                        <textarea v-model="form.description" rows="2" maxlength="25"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Korte beschrijving..." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequentie
                            *</label>
                        <select v-model="form.frequency" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="daily">Dagelijks</option>
                            <option value="weekly">Wekelijks</option>
                            <option value="monthly">Maandelijks</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categorie</label>
                        <input v-model="form.category" type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Bijv. Gezondheid, Werk, Persoonlijk" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                            Annuleren
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-semibold">
                            {{ isEditing ? 'Bijwerken' : 'Aanmaken' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useHabitStore } from '../stores/habit'
import HabitCard from '../components/habits/HabitCard.vue'
import HabitCalendar from '../components/habits/HabitCalendar.vue'

const habitStore = useHabitStore()

const showModal = ref(false)
const isEditing = ref(false)
const editingHabitId = ref(null)

const form = reactive({
    name: '',
    description: '',
    frequency: 'daily',
    category: ''
})

// Computed properties
const habits = computed(() => {
    console.log('Habits in computed:', habitStore.habits)
    return habitStore.habits
})

const weekDays = computed(() => habitStore.weekDays)
const stats = computed(() => habitStore.stats)

// Stats computed properties
const totalHabits = computed(() => stats.value?.total_habits || 0)
const completedToday = computed(() => stats.value?.completed_today || 0)
const currentStreak = computed(() => stats.value?.longest_streak || 0)


//const successRate = computed(() => stats.value?.successRate || 0)
//berekenen van de successrate
const successRate = computed(() => {
  if (!stats.value || !habits.value.length) return 0
  
  const totalHabits = habits.value.length
  const totalCompletions = stats.value.total_completions || 0
  
  // Bereken gemiddelde leeftijd van habits in dagen
  const now = new Date()
  const totalDays = habits.value.reduce((sum, habit) => {
    const created = new Date(habit.created_at)
    const days = Math.ceil((now - created) / (1000 * 60 * 60 * 24))
    return sum + Math.max(1, days)
  }, 0)
  
  const averageDays = totalDays / totalHabits
  const totalPossibleCompletions = totalHabits * averageDays
  
  return totalPossibleCompletions > 0 
    ? Math.round((totalCompletions / totalPossibleCompletions) * 100)
    : 0
})


// Debugging
watch(habits, (newHabits) => {
    console.log('Habits updated:', newHabits)
}, { immediate: true })

// Lifecycle
onMounted(async () => {
    console.log('Fetching habits...')
    await habitStore.fetchHabits()
    console.log('Habits after fetch:', habitStore.habits)
    console.log('Habits computed value:', habits.value)
    await habitStore.fetchStats()
    console.log('Stats:', stats.value)
})

// Modal functions
const openModal = (habit = null) => {
    if (habit) {
        isEditing.value = true
        editingHabitId.value = habit.id
        form.name = habit.name
        form.description = habit.description || ''
        form.frequency = habit.frequency
        form.category = habit.category || ''
    } else {
        isEditing.value = false
        editingHabitId.value = null
        resetForm()
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    resetForm()
}

const resetForm = () => {
    form.name = ''
    form.description = ''
    form.frequency = 'daily'
    form.category = ''
}

// CRUD operations
const createHabit = async () => {
    try {
        await habitStore.createHabit(form)
        closeModal()
        await habitStore.fetchStats()
    } catch (error) {
        alert('Failed to create habit: ' + error.message)
    }
}

const updateHabit = async () => {
    try {
        await habitStore.updateHabit(editingHabitId.value, form)
        closeModal()
        await habitStore.fetchStats()
    } catch (error) {
        alert('Failed to update habit: ' + error.message)
    }
}

const toggleHabit = async (habitId) => {
    try {
        await habitStore.toggleHabitCompletion(habitId, new Date().toISOString().split('T')[0])
        await habitStore.fetchStats()
    } catch (error) {
        alert('Failed to toggle habit: ' + error.message)
    }
}

const toggleHabitCompletion = async (habitId, date) => {
  try {
    await habitStore.toggleHabitCompletion(habitId, date)
    // Refresh stats na toggle
    await habitStore.fetchStats()
  } catch (error) {
    console.error('Failed to toggle habit completion:', error)
    alert('Failed to toggle habit: ' + error.message)
  }
}

// Helper functions (voor HabitCalendar indien nodig)
const isHabitCompleted = (habit, date) => {
    return habitStore.isHabitCompletedOnDate(habit, date)
}

const getCompletionClasses = (habit, day) => {
    const isCompleted = isHabitCompleted(habit, day.date)
    return {
        'bg-green-500 border-green-500 text-white': isCompleted,
        'border-gray-300 hover:border-green-400': !isCompleted,
        'border-blue-300': day.isToday && !isCompleted
    }
}
</script>
<!-- <template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header 
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Habits</h1>
                    <p class="text-gray-600 mt-2">Build better habits, one day at a time</p>
                </div>
                <button @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    + New Habit
                </button>
            </div>

            <!-- Stats 
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_habits }}</div>
                    <div class="text-gray-600 text-sm">Total Habits</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <div class="text-2xl font-bold text-green-600">{{ stats.completed_today }}</div>
                    <div class="text-gray-600 text-sm">Completed Today</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <div class="text-2xl font-bold text-blue-600">{{ stats.total_completions }}</div>
                    <div class="text-gray-600 text-sm">Total Completions</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <div class="text-2xl font-bold text-purple-600">{{ stats.longest_streak }}</div>
                    <div class="text-gray-600 text-sm">Longest Streak</div>
                </div>
            </div>

            <!-- Week Navigation
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <button @click="previousWeek"
                        class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous Week
                    </button>

                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ formatWeekRange() }}
                    </h2>

                    <button @click="nextWeek"
                        class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        Next Week
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Week Days Header 
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <div class="p-3 text-center font-semibold text-gray-500">Habit</div>
                    <div v-for="day in weekDays" :key="day.date" class="p-3 text-center" :class="{
                        'bg-blue-50 text-blue-700 font-semibold': day.isToday,
                        'text-gray-700': !day.isToday
                    }">
                        <div class="text-sm">{{ day.day }}</div>
                        <div class="text-lg font-semibold">{{ day.dayNumber }}</div>
                    </div>
                </div>

                <!-- Habits List 
                <div class="space-y-2">
                    <div v-for="habit in habits" :key="habit.id"
                        class="grid grid-cols-8 gap-2 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <!-- Habit Name 
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ habit.name }}</div>
                                <div class="text-sm text-gray-600">{{ habit.description }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Streak: {{ habit.current_streak }} days • Best: {{ habit.best_streak }}
                                </div>
                            </div>
                        </div>

                        <!-- Completion Checkboxes 
                        <div v-for="day in weekDays" :key="day.date" class="flex justify-center">
                            <button @click="toggleCompletion(habit.id, day.date)"
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all"
                                :class="{
                                    'bg-green-500 border-green-500 text-white': habit.completionStatus && habit.completionStatus[day.date],
                                    'border-gray-300 hover:border-green-400': !(habit.completionStatus && habit.completionStatus[day.date]),
                                    'border-blue-300': day.isToday && !(habit.completionStatus && habit.completionStatus[day.date])
                                }">
                                <svg v-if="habit.completionStatus && habit.completionStatus[day.date]" class="w-4 h-4"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Empty State 
                    <div v-if="habits.length === 0" class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg mb-2">No habits yet</p>
                        <p class="text-sm mb-4">Create your first habit to start tracking your progress</p>
                        <button @click="openModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            Create Habit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>



<script setup>
import { ref, computed } from 'vue'
import HabitCard from '../components/habits/HabitCard.vue'
import HabitCalendar from '../components/habits/HabitCalendar.vue'

const habits = ref([
  {
    id: 1,
    name: 'Ochtend Meditatie',
    description: '10 minuten mediteren',
    streak: 12,
    frequency: 'daily',
    completed: true,
    color: 'blue'
  },
  {
    id: 2,
    name: 'Water Drinken',
    description: '2 liter water per dag',
    streak: 8,
    frequency: 'daily',
    completed: false,
    color: 'green'
  },
  {
    id: 3,
    name: 'Sporten',
    description: '30 minuten beweging',
    streak: 15,
    frequency: 'weekly',
    completed: true,
    color: 'purple'
  },
  {
    id: 4,
    name: 'Lezen',
    description: '20 pagina\'s lezen',
    streak: 5,
    frequency: 'daily',
    completed: false,
    color: 'orange'
  }
])

const currentStreak = computed(() => {
  return Math.max(...habits.value.map(h => h.streak))
})

const completedToday = computed(() => {
  return habits.value.filter(h => h.completed).length
})

const totalHabits = computed(() => habits.value.length)

const successRate = computed(() => {
  const completed = habits.value.filter(h => h.completed).length
  return Math.round((completed / habits.value.length) * 100)
})

const toggleHabit = (habitId) => {
  const habit = habits.value.find(h => h.id === habitId)
  if (habit) {
    habit.completed = !habit.completed
    habit.streak = habit.completed ? habit.streak + 1 : Math.max(0, habit.streak - 1)
  }
}

const editHabit = (habit) => {
  console.log('Edit habit:', habit)
}
</script>-->