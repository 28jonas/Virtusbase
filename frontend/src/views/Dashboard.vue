<!-- views/Dashboard.vue -->
<template>
  <div class="p-6 space-y-8 animate-fade-in">

    <!-- Welkom Header -->
    <div
      class="glass-effect rounded-3xl p-8 border border-gray-100 dark:border-gray-800 transition-all duration-300 hover:shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-light text-gray-900 dark:text-white mb-3">
            Goedemorgen, <span class="font-medium">{{ utilsStore.capitalizeFirst(user?.name || 'gebruiker') }}</span>
          </h1>
          <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            "Een moment van rust is een moment van helderheid. Wat wil je vandaag bereiken?"
          </p>
        </div>
        <div class="text-6xl">🌅</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="text-gray-600 dark:text-gray-400 mt-4">Data laden...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="hasError" class="text-center py-12">
      <div class="text-red-500 text-6xl mb-4">❌</div>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
        Oeps! Er ging iets mis
      </h3>
      <p class="text-gray-600 dark:text-gray-400 mb-4">
        We konden de data niet laden. Probeer de pagina te vernieuwen.
      </p>
      <button @click="loadData" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
        Opnieuw proberen
      </button>
    </div>

    <!-- Content wanneer geladen -->
    <div v-else>
      <!-- KPI Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-2">
        <KPICard title="Familie Overzicht" :value="stats.families" subtitle="Actieve families" icon="👨‍👩‍👧‍👦"
          color="blue" />
        <!-- trend="+2 deze maand" (maakt een trend aan met de vorige maand in het component kpicard) -->
        <KPICard title="Habit Streak" :value="stats.habitStreak" subtitle="dagen consistent" icon="🔥" color="green" />
        <KPICard title="Open Taken" :value="stats.openTodos" subtitle="voor vandaag" icon="📝" color="orange" />
        <!-- <KPICard title="Productiviteit" :value="`${stats.productivity}%`" subtitle="van doelen bereikt" icon="📈"
          color="purple" /> -->
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        <!-- Linkerkolom -->
        <div class="space-y-8">
          <TodayTasks :tasks="sortedTodayTasks" />
          <HabitProgress :habits="todayHabits" />
        </div>

        <!-- Middelkolom -->
        <div class="space-y-8">
          <FocusTimer />
          <MiniCalendar :events="todayEvents" />
        </div>

        <!-- Rechterkolom -->
        <div class="space-y-8">
          <QuickActions />
          <!-- <WeeklyInsights :data="weeklyData" /> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import KPICard from '../components/dashboard/KPICard.vue'
import TodayTasks from '../components/dashboard/TodayTasks.vue'
import HabitProgress from '../components/dashboard/HabitProgress.vue'
import FocusTimer from '../components/dashboard/FocusTimer.vue'
import MiniCalendar from '../components/dashboard/MiniCalendar.vue'
import QuickActions from '../components/dashboard/QuickActions.vue'
import WeeklyInsights from '../components/dashboard/WeeklyInsights.vue'

import { useAuthStore } from '../stores/auth'
import { useFamilyStore } from '../stores/family'
import { useHabitStore } from '../stores/habit'
import { useTodoStore } from '../stores/todo'
import { useCalendarStore } from '../stores/calendar'
import { useEventStore } from '../stores/event'
import { useUtilsStore } from '../stores/utils'

const authStore = useAuthStore()
const familyStore = useFamilyStore()
const habitStore = useHabitStore()
const todoStore = useTodoStore()
const calendarStore = useCalendarStore()
const eventStore = useEventStore()
const utilsStore = useUtilsStore()

const user = ref(null)
const loading = ref(true)
const hasError = ref(false)

const loadData = async () => {
  try {
    loading.value = true
    hasError.value = false

    console.log('Starting data load...')

    // 1. Laad user info eerst
    user.value = await authStore.getLoggedInUser()
    console.log('User loaded:', user.value)

    // 2. Laad alle data parallel
    await Promise.all([
      familyStore.fetchFamilies(),
      habitStore.fetchHabits(),
      todoStore.fetchTodos(),
      eventStore.fetchEvents(),
    ])

    console.log('All data loaded successfully')
    console.log('- Families:', familyStore.families.length)
    console.log('- Habits:', habitStore.habits.length)
    console.log('- Todos:', todoStore.todos.length)
    console.log('- Events:', eventStore.events.length)

  } catch (error) {
    console.error('Failed to load dashboard data:', error)
    hasError.value = true
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})

// Computed properties
const stats = computed(() => ({
  families: familyStore.families?.length || 0,
  habitStreak: habitStore.habits?.reduce((total, habit) => total + (habit.streak || 0), 0) || 0,
  openTodos: todoStore.todos?.filter(todo => !todo.completed).length || 0,
  productivity: 78
}))

const sortedTodayTasks = computed(() => {
  const todos = todoStore.todos || []
  const todayTasks = todos.filter(todo => !todo.completed)
  const priorityOrder = { high: 3, medium: 2, low: 1 }

  return todayTasks.sort((a, b) => {
    return priorityOrder[b.priority] - priorityOrder[a.priority]
  })
})

const todayHabits = computed(() => {
  const habits = habitStore.habits || []
  return habits.sort((a, b) => (b.streak || 0) - (a.streak || 0))
})

const todayEvents = computed(() => {
  const events = eventStore.events || []
  return events.sort((a, b) => new Date(a.time || 0) - new Date(b.time || 0))
})

const weeklyData = computed(() => ({
  productivity: [65, 78, 82, 75, 88, 90, 78],
  habits: [80, 75, 90, 85, 95, 88, 92]
}))
</script>