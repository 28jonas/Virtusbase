<!-- components/habits/HabitCalendar.vue -->
<template>
  <div class="space-y-6">
    <!-- Monthly Overview -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maandoverzicht</h3>
      
      <!-- Month Navigation -->
      <div class="flex items-center justify-between mb-4">
        <button @click="previousMonth" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
          ←
        </button>
        <h4 class="font-medium text-gray-900 dark:text-white">{{ currentMonthYear }}</h4>
        <button @click="nextMonth" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
          →
        </button>
      </div>

      <!-- Calendar Grid -->
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div v-for="day in ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za']" 
             :key="day" class="text-center text-sm text-gray-500 dark:text-gray-400 py-1">
          {{ day }}
        </div>
      </div>

      <div class="grid grid-cols-7 gap-1">
        <div v-for="day in calendarDays" :key="day.date" 
             class="aspect-square flex items-center justify-center text-sm relative">
          <!-- Empty cells for days before month start -->
          <div v-if="!day.inMonth" class="w-full h-full"></div>
          
          <!-- Actual day cells -->
          <div v-else 
               class="w-full h-full flex items-center justify-center rounded-lg transition-colors"
               :class="getDayClasses(day)">
            {{ day.day }}
            
            <!-- Completion dots for habits -->
            <div v-if="day.completionCount > 0" 
                 class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-green-500"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Streak Stats -->
    <div class="grid grid-cols-2 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center border border-gray-100 dark:border-gray-700">
        <div class="text-2xl font-bold text-green-600 mb-1">{{ currentStreak }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Huidige Streak</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl p-4 text-center border border-gray-100 dark:border-gray-700">
        <div class="text-2xl font-bold text-blue-600 mb-1">{{ longestStreak }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Langste Streak</div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recente Activiteit</h3>
      <div class="space-y-3">
        <div v-for="activity in recentActivity" :key="activity.id" 
             class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
              <span class="text-green-600 dark:text-green-400 text-sm">✓</span>
            </div>
            <div>
              <div class="font-medium text-gray-900 dark:text-white">{{ activity.habitName }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ activity.date }}</div>
            </div>
          </div>
          <div class="text-sm text-gray-500">{{ activity.time }}</div>
        </div>
        
        <div v-if="recentActivity.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
          Nog geen recente activiteit
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  habits: {
    type: Array,
    default: () => []
  }
})

const currentDate = ref(new Date())
const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())

const currentMonthYear = computed(() => {
  return new Date(currentYear.value, currentMonth.value).toLocaleDateString('nl-NL', {
    month: 'long',
    year: 'numeric'
  })
})

const calendarDays = computed(() => {
  const days = []
  const firstDay = new Date(currentYear.value, currentMonth.value, 1)
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
  
  // Add empty days for previous month
  const startingDayOfWeek = firstDay.getDay()
  for (let i = 0; i < startingDayOfWeek; i++) {
    days.push({ inMonth: false })
  }
  
  // Add days of current month
  for (let day = 1; day <= lastDay.getDate(); day++) {
    const date = new Date(currentYear.value, currentMonth.value, day)
    const dateString = date.toISOString().split('T')[0]
    
    // Calculate completions for this day
    const completionCount = props.habits.reduce((count, habit) => {
      const isCompleted = habit.completions?.some(c => c.completion_date === dateString)
      return count + (isCompleted ? 1 : 0)
    }, 0)
    
    days.push({
      day,
      date: dateString,
      inMonth: true,
      completionCount,
      isToday: date.toDateString() === new Date().toDateString()
    })
  }
  
  return days
})

const getDayClasses = (day) => {
  const classes = []
  
  if (day.isToday) {
    classes.push('bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300')
  } else if (day.completionCount > 0) {
    classes.push('bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300')
  } else {
    classes.push('bg-white dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700')
  }
  
  return classes.join(' ')
}

const currentStreak = computed(() => {
  // Simpele streak berekening - in praktijk zou dit van je backend komen
  return Math.max(...props.habits.map(h => h.current_streak || 0), 0)
})

const longestStreak = computed(() => {
  return Math.max(...props.habits.map(h => h.longest_streak || 0), 0)
})

const recentActivity = computed(() => {
  const activities = []
  
  props.habits.forEach(habit => {
    habit.completions?.forEach(completion => {
      activities.push({
        id: `${habit.id}-${completion.completion_date}`,
        habitName: habit.name,
        date: new Date(completion.completion_date).toLocaleDateString('nl-NL'),
        time: 'Voltooid'
      })
    })
  })
  
  // Sorteer op datum (nieuwste eerst)
  return activities
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 5)
})

const previousMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}

onMounted(() => {
  // Initialisatie indien nodig
})
</script>