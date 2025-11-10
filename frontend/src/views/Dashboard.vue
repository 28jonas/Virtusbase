<!-- views/Dashboard.vue -->
<template>
  <div class="p-6 space-y-8 animate-fade-in">
    
    <!-- Welkom Header -->
    <div class="glass-effect rounded-3xl p-8 border border-gray-100 dark:border-gray-800 transition-all duration-300 hover:shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-light text-gray-900 dark:text-white mb-3">
            Goedemorgen, <span class="font-medium">{{ user?.name || 'Gebruiker' }}</span>
          </h1>
          <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            "Een moment van rust is een moment van helderheid. Wat wil je vandaag bereiken?"
          </p>
        </div>
        <div class="text-6xl">🌅</div>
      </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <KPICard 
        title="Familie Overzicht"
        :value="stats.families"
        subtitle="Actieve families"
        icon="👨‍👩‍👧‍👦"
        trend="+2 deze maand"
        color="blue"
      />
      <KPICard 
        title="Habit Streak"
        :value="stats.habitStreak"
        subtitle="dagen consistent"
        icon="🔥"
        trend="+5 dagen"
        color="green"
      />
      <KPICard 
        title="Open Taken"
        :value="stats.openTodos"
        subtitle="voor vandaag"
        icon="📝"
        trend="-3 sinds gisteren"
        color="orange"
      />
      <KPICard 
        title="Productiviteit"
        :value="`${stats.productivity}%`"
        subtitle="van doelen bereikt"
        icon="📈"
        trend="+12%"
        color="purple"
      />
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
        <WeeklyInsights :data="weeklyData" />
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

const authStore = useAuthStore()
const familyStore = useFamilyStore()
const habitStore = useHabitStore()
const todoStore = useTodoStore()
const calendarStore = useCalendarStore()
const eventStore = useEventStore()


const user = ref(null)
const loading = ref(true)
const todayTasks = ref([])
//const todayHabits = ref([])
//const todayEvents = ref([])
//const weeklyData = ref([])

const familiesCount = computed(() => familyStore.families.length)
const habitsCount = computed(() => habitStore.habits.length)
const todosStats = computed(() => todoStore.fetchStats()?.value ?? {}); 
console.log("Todos stats:", todosStats.value)


onMounted(async () => {
  try {
    user.value = await authStore.meInfo()

    await Promise.all([
      familyStore.fetchFamilies(),
      todoStore.fetchStats(),
      todoStore.fetchTodos(), // Verwijder de toewijzing hier
      habitStore.fetchHabits(),
      todoStore.fetchEvents(),
      calendarStore.fetchEvents(),
    ])
    console.log('todostore', todoStore.todos)
    // CORRECT: Gebruik .value om de ref aan te passen
    todayTasks.value = todoStore.pendingTodos
    console.log('Events:', calendarStore.events)
    console.log('Open todos:', todoStore.pendingTodos)
    console.log('Completed todos:', todoStore.completedTodos)
    console.log('User data:', user.value)
  } catch (error) {
    console.error('Failed to fetch user data:', error)
  } finally {
    loading.value = false
  }
})

// Voor sortering op priority (voeg deze computed property toe):
const sortedTodayTasks = computed(() => {
  // Maak een kopie van de array om te sorteren
  console.log('Today tasks:', todoStore.todos)
  const tasks = [...todoStore.todos]
  console.log('Tasks:', tasks)
  // Sorteer op priority: high > medium > low
  const priorityOrder = { high: 3, medium: 2, low: 1 }
  
  return tasks.sort((a, b) => {
    return priorityOrder[b.priority] - priorityOrder[a.priority]
  })
})

// const todayTasks = ref([
//   { id: 1, title: 'Boodschappen doen', completed: false, priority: 'medium' },
//   { id: 2, title: 'Sporten', completed: true, priority: 'high' },
//   { id: 3, title: 'Boek lezen', completed: false, priority: 'low' }
// ])

const stats = ref({
  families: familiesCount.value,
  habitStreak: habitsCount.value,
  openTodos: todoStore.pendingTodos,
  productivity: 78
})

const todayHabits = computed(() => {
  const habits = [...habitStore.habits]
  return habits.sort((a, b) => b.streak - a.streak)
})

// const todayHabits = ref([
//   { id: 1, name: 'Meditatie', completed: true, streak: 5 },
//   { id: 2, name: 'Water drinken', completed: false, streak: 12 },
//   { id: 3, name: 'Early workout', completed: true, streak: 8 }
// ])

const todayEvents = computed(() => {
  const events = [...eventStore.events]
  return events.sort((a, b) => new Date(a.time) - new Date(b.time))
})

// const todayEvents = ref([
//   { id: 1, title: 'Team meeting', time: '10:00', type: 'work' },
//   { id: 2, title: 'Lunch met familie', time: '13:00', type: 'personal' }
// ])

const weeklyData = ref({
  productivity: [65, 78, 82, 75, 88, 90, 78],
  habits: [80, 75, 90, 85, 95, 88, 92]
})
</script>