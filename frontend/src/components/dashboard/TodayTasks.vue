<!-- components/dashboard/TodayTasks.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vandaag's Taken</h3>
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ completedTasks }}/{{ todayTasks.length }}
        voltooid</span>
    </div>

    <div class="space-y-3">
      <div v-for="task in sortedTasks" :key="task.id"
        class="flex items-center space-x-3 p-3 rounded-lg border transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
        <!-- :class="getTaskBorderClass(task)" -->

        <!-- Status Pill -->
        <div class="w-3 h-3 rounded-full flex-shrink-0" :class="getStatusColor(task)"></div>

        <!-- Complete Button -->
        <button @click="toggleTask(task.id)"
          class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
          :class="task.completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-600'">
          <span v-if="task.completed" class="text-xs">✓</span>
        </button>

        <!-- Task Content -->
        <div class="flex-1 min-w-0">
          <div class="font-medium text-gray-900 dark:text-white"
            :class="{ 'line-through text-gray-400': task.completed }">
            {{ task.title }}
          </div>
          <div v-if="task.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ task.description }}
          </div>
          <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ formatTaskTime(task.date) }}
          </div>
        </div>

        <!-- Priority Badge -->
        <span class="text-xs px-2 py-1 rounded-full capitalize flex-shrink-0" :class="priorityClasses[task.priority]">
          {{ task.priority || 'medium' }}
        </span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="todayTasks.length === 0" class="text-center py-8">
      <div class="text-4xl mb-2">🎉</div>
      <p class="text-gray-500 dark:text-gray-400">Geen taken voor vandaag!</p>
    </div>

    <a href="/todos">
      <button
        class="w-full mt-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
        + Nieuwe taak toevoegen
      </button>
    </a>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUtilsStore } from '../../stores/utils'

const utils = useUtilsStore()

const props = defineProps({
  tasks: {
    type: Array,
    default: () => []
  }
})

const todayTasks = ref([])

const completedTasks = computed(() => {
  return todayTasks.value.filter(task => task.completed).length
})

const sortedTasks = computed(() => {
  return [...todayTasks.value].sort((a, b) => {
    // Eerst op completed status
    if (a.completed && !b.completed) return 1
    if (!a.completed && b.completed) return -1

    // Dan op overdue status
    const aStatus = getTaskStatus(a)
    const bStatus = getTaskStatus(b)
    const statusPriority = { overdue: 0, today: 1, upcoming: 2, completed: 3 }

    return statusPriority[aStatus] - statusPriority[bStatus]
  })
})

const priorityClasses = {
  high: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
  medium: 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
  low: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
}

// Task status bepalen
const getTaskStatus = (task) => {
  if (task.completed) return 'completed'

  const now = new Date()
  const taskDate = new Date(task.date)
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const taskDay = new Date(taskDate)
  taskDay.setHours(0, 0, 0, 0)

  if (taskDate < now) return 'overdue'
  if (taskDay.getTime() === today.getTime()) return 'today'
  return 'upcoming'
}

// Status kleur voor de pill
const getStatusColor = (task) => {
  const status = getTaskStatus(task)

  const colors = {
    completed: 'bg-gray-300 dark:bg-gray-600',
    overdue: 'bg-red-500',
    today: 'bg-orange-500',
    upcoming: 'bg-green-500'
  }

  return colors[status]
}

// Border kleur voor de task card
// const getTaskBorderClass = (task) => {
//   const status = getTaskStatus(task)

//   const borders = {
//     completed: 'border-gray-100 dark:border-gray-700',
//     overdue: 'border-red-200 dark:border-red-800',
//     today: 'border-orange-200 dark:border-orange-800',
//     upcoming: 'border-green-200 dark:border-green-800'
//   }

//   return borders[status] || 'border-gray-100 dark:border-gray-700'
// }

// Format de tijd voor display
const formatTaskTime = (dateString) => {
  if (!dateString) return 'Geen tijd'

  const taskDate = new Date(dateString)
  const now = new Date()

  // Als de taak vandaag is
  if (taskDate.toDateString() === now.toDateString()) {
    return `Vandaag ${taskDate.toLocaleTimeString('nl-NL', {
      hour: '2-digit',
      minute: '2-digit'
    })}`
  }

  // Als de taak gisteren was (overdue)
  const yesterday = new Date(now)
  yesterday.setDate(yesterday.getDate() - 1)
  if (taskDate.toDateString() === yesterday.toDateString()) {
    return `Gisteren ${taskDate.toLocaleTimeString('nl-NL', {
      hour: '2-digit',
      minute: '2-digit'
    })}`
  }

  // Anders toon volledige datum
  return taskDate.toLocaleDateString('nl-NL', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const toggleTask = (taskId) => {
  const task = todayTasks.value.find(t => t.id === taskId)
  if (task) {
    task.completed = !task.completed
    task.completed_at = task.completed ? new Date().toISOString() : null
  }
}

// Initialize tasks
onMounted(() => {
  todayTasks.value = props.tasks.map(task => ({
    ...task,
    completed: Boolean(task.completed),
    priority: task.priority || 'medium'
  }))

  console.log('TodayTasks loaded:', todayTasks.value)
})
</script>