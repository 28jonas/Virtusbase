// stores/note.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useNoteStore = defineStore('note', () => {
  const notes = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchNotes = async (params = {}) => {
    try {
      loading.value = true
      const response = await axios.get(`${API_BASE}/api/notes`, {
        params,
        withCredentials: true
      })
      notes.value = response.data.data || []
      return response.data.data
    } catch (err) {
      console.error('Error fetching notes:', err)
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  const createNote = async (noteData) => {
    try {
      const response = await axios.post(`${API_BASE}/api/notes`, noteData, {
        withCredentials: true
      })
      notes.value.push(response.data.data)
      return response.data.data
    } catch (err) {
      console.error('Error creating note:', err)
      error.value = err
      throw err
    }
  }

  const updateNote = async (id, noteData) => {
    try {
      const response = await axios.put(`${API_BASE}/api/notes/${id}`, noteData, {
        withCredentials: true
      })
      const index = notes.value.findIndex(note => note.id === id)
      if (index !== -1) {
        notes.value[index] = response.data.data
      }
      return response.data.data
    } catch (err) {
      console.error('Error updating note:', err)
      error.value = err
      throw err
    }
  }

  const deleteNote = async (id) => {
    try {
      await axios.delete(`${API_BASE}/api/notes/${id}`, {
        withCredentials: true
      })
      notes.value = notes.value.filter(note => note.id !== id)
    } catch (err) {
      console.error('Error deleting note:', err)
      error.value = err
      throw err
    }
  }

  return {
    notes,
    loading,
    error,
    fetchNotes,
    createNote,
    updateNote,
    deleteNote
  }
})