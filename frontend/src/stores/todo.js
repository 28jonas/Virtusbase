import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useTodoStore = defineStore('todo', () => {
  // State
  const todos = ref([])
  const stats = ref({
    total: 0,
    completed: 0,
    pending: 0
  })

  // Computed properties
  const totalTodos = computed(() => stats.value.total)
  const completedTodos = computed(() => stats.value.completed)
  const pendingTodos = computed(() => stats.value.pending)

  // Actions
  const fetchTodos = async (params = {}) => {
    try {
      const res = await axios.get('http://localhost:8080/api/todos', {
        params,
        withCredentials: true
      })
      todos.value = res.data.data.todos
      stats.value = res.data.data.stats
    } catch (err) {
      console.error('Failed to fetch todos:', err)
      throw err
    }
  }

  const fetchStats = async () => {
    try {
      const res = await axios.get('http://localhost:8080/api/todos/stats', {
        withCredentials: true
      })
      stats.value = res.data.data
      return res.data.data
    } catch (err) {
      console.error('Failed to fetch stats:', err)
      throw err
    }
  }

  const createTodo = async (payload) => {
    try {
      const res = await axios.post('http://localhost:8080/api/todos', payload, {
        withCredentials: true
      })
      todos.value.push(res.data.data)
      return res.data.data
    } catch (err) {
      console.error('Failed to create todo:', err)
      throw err
    }
  }

  const updateTodo = async (id, payload) => {
    try {
      const res = await axios.put(`http://localhost:8080/api/todos/${id}`, payload, {
        withCredentials: true
      })
      const idx = todos.value.findIndex(t => t.id === id)
      if (idx !== -1) {
        todos.value[idx] = res.data.data
      }
      return res.data.data
    } catch (err) {
      console.error('Failed to update todo:', err)
      throw err
    }
  }

  const deleteTodo = async (id) => {
    try {
      await axios.delete(`http://localhost:8080/api/todos/${id}`, {
        withCredentials: true
      })
      todos.value = todos.value.filter(t => t.id !== id)
    } catch (err) {
      console.error('Failed to delete todo:', err)
      throw err
    }
  }

  const toggleComplete = async (id) => {
    try {
      const res = await axios.patch(`http://localhost:8080/api/todos/${id}/toggle-complete`, {}, {
        withCredentials: true
      })
      const idx = todos.value.findIndex(t => t.id === id)
      if (idx !== -1) {
        todos.value[idx] = res.data.data
      }
      return res.data.data
    } catch (err) {
      console.error('Failed to toggle todo:', err)
      throw err
    }
  }

  // Return everything
  return {
    // State
    todos,
    stats,
    
    // Computed
    totalTodos,
    completedTodos,
    pendingTodos,
    
    // Actions
    fetchTodos,
    fetchStats,
    createTodo,
    updateTodo,
    deleteTodo,
    toggleComplete
  }
})