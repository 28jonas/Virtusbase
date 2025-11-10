<!-- views/Todos.vue -->
<template>
  <div class="p-6 space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Todo's</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Beheer je taken en verantwoordelijkheden</p>
      </div>
      <button @click="showCreateModal = true"
        class="bg-purple-500 text-white px-6 py-3 rounded-xl hover:bg-purple-600 transition-colors flex items-center space-x-2">
        <span>✅</span>
        <span>Nieuwe Taak</span>
      </button>
    </div>

    <!-- Todo Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ totalTodos }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Totaal Taken</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ completedTodos }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Voltooid</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ pendingTodos }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Openstaand</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center">
        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ overdueTodos }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Te Laat</div>
      </div>
    </div>

    <!-- Todo List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Today's Todos -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Vandaag</h2>
        <TodoList :todos="todayTodos" @toggle="toggleComplete" @delete="deleteTodo" />
      </div>

      <!-- Upcoming Todos -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Binnenkort</h2>
        <TodoList :todos="upcomingTodos" @toggle="toggleComplete" @delete="deleteTodo" />
      </div>
    </div>

    <!-- Create Todo Modal-->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-96">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Nieuwe Taak</h3>
        <input v-model="newTodo.title" placeholder="Titel"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
        <textarea v-model="newTodo.description" placeholder="Beschrijving"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
        <input type="datetime-local" v-model="newTodo.date"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />

        <!-- Priority Selection -->
        <select v-model="newTodo.priority"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
          <option value="low">Laag</option>
          <option value="medium">Medium</option>
          <option value="high">Hoog</option>
        </select>

        <div class="flex justify-end space-x-2">
          <button @click="showCreateModal = false"
            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">Annuleren</button>
          <button @click="createTodo"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Aanmaken</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TodoList from '../components/todos/TodoList.vue'
import { useTodoStore } from '../stores/todo'

const todoStore = useTodoStore()
const showCreateModal = ref(false)
const newTodo = ref({
  title: '',
  description: '',
  date: '',
  priority: 'medium'
})

// Debug de store om te zien wat erin zit
console.log('Todo Store:', todoStore)

// Veilige computed properties met array fallback
const todos = computed(() => {
  const storeTodos = todoStore.todos
  return Array.isArray(storeTodos) ? storeTodos : []
})

const pendingTodos = computed(() => {
  const storePending = todoStore.pendingTodos
  console.log('pendingTodos from store:', storePending, 'Type:', typeof storePending)

  // Als het een computed/ref is, gebruik .value, anders direct
  if (storePending && typeof storePending === 'object' && 'value' in storePending) {
    return Array.isArray(storePending.value) ? storePending.value : []
  }
  return Array.isArray(storePending) ? storePending : []
})

const completedTodos = computed(() => {
  const storeCompleted = todoStore.completedTodos
  if (storeCompleted && typeof storeCompleted === 'object' && 'value' in storeCompleted) {
    return Array.isArray(storeCompleted.value) ? storeCompleted.value : []
  }
  return Array.isArray(storeCompleted) ? storeCompleted : []
})

// Filter todos voor vandaag
const todayTodos = computed(() => {
  const today = new Date()
  const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const todayEnd = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1)

  console.log('Today range:', todayStart, 'to', todayEnd)
  console.log('Pending todos:', pendingTodos.value)
  console.log('Filtered todos:', pendingTodos.value.filter(todo => {
    if (!todo || !todo.date) return false
  }))
  return todos.value.filter(todo => {
    if (!todo || !todo.date) return false

    // Converteer de database string naar Date object
    const todoDate = new Date(todo.date)
    console.log('Todo date:', todo.date, '->', todoDate)

    // Check of de todo datum binnen vandaag valt
    return todoDate >= todayStart && todoDate < todayEnd
  })
})

// Filter todos voor de komende dagen
const upcomingTodos = computed(() => {
  const today = new Date()
  const nextWeek = new Date(today)
  nextWeek.setDate(today.getDate() + 7)

  return todos.value.filter(todo => {
    if (!todo || !todo.date) return false
    const todoDate = new Date(todo.date)
    return todoDate > today && todoDate <= nextWeek
  })
})

// Stats
const totalTodos = computed(() => todos.value.length)
const completedCount = computed(() => completedTodos.value.length)
const pendingCount = computed(() => pendingTodos.value.length)
const overdueTodos = computed(() => {
  const today = new Date()
  return todos.value.filter(todo => {
    if (!todo || !todo.date) return false
    return new Date(todo.date) < today
  }).length
})

onMounted(async () => {
  try {
    await todoStore.fetchTodos()
    console.log('Todos after fetch:', {
      todos: todos.value,
      pending: pendingTodos.value,
      completed: completedTodos.value
    })
  } catch (error) {
    console.error('Failed to load todos:', error)
  }
})

const createTodo = async () => {
  if (!newTodo.value.title.trim()) {
    alert('Titel is verplicht')
    return
  }

  if (!newTodo.value.date) {
    alert('Datum is verplicht')
    return
  }

  try {
    await todoStore.createTodo({
      title: newTodo.value.title,
      description: newTodo.value.description,
      date: newTodo.value.date,
      priority: newTodo.value.priority,
      completed: false
    })

    showCreateModal.value = false
    newTodo.value = {
      title: '',
      description: '',
      date: '',
      priority: 'medium'
    }

    // Herlaad de todos
    await todoStore.fetchTodos()
  } catch (error) {
    console.error('Failed to create todo:', error)
    alert('Er is een fout opgetreden bij het aanmaken van de taak')
  }
}

const deleteTodo = async (id) => {
  if (confirm('Weet je zeker dat je deze taak wilt verwijderen?')) {
    try {
      await todoStore.deleteTodo(id)
    } catch (error) {
      console.error('Failed to delete todo:', error)
      alert('Er is een fout opgetreden bij het verwijderen van de taak')
    }
  }
}

const toggleComplete = async (todo) => {
  try {
    await todoStore.toggleComplete(todo.id)
  } catch (error) {
    console.error('Failed to toggle todo:', error)
    alert('Er is een fout opgetreden bij het bijwerken van de taak')
  }
}
</script>