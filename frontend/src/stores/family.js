// stores/family.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useFamilyStore = defineStore('family', () => {
  // State
  const families = ref([])
  const currentFamily = ref(null)
  const loading = ref(false)
  const error = ref(null)

  // Actions
  const fetchFamilies = async () => {
    loading.value = true
    error.value = null
    try {
      console.log('Fetching families...')
      const response = await axios.get(`${API_BASE}/api/families`, {
        withCredentials: true
      })

      console.log('Families response:', response.data)
      families.value = response.data.data || []
      console.log('Families stored:', families.value.length)

      return families.value
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch families'
      console.error('Error fetching families:', err)
      families.value = [] // Reset naar lege array bij error
      throw err
    } finally {
      loading.value = false
    }
  }

  const createFamily = async (familyData) => {
    loading.value = true
    try {
      const response = await axios.post(`${API_BASE}/api/families`, familyData, {
        withCredentials: true
      })
      families.value.push(response.data.data)
      return response.data
    } catch (err) {
      console.error('Error creating family:', err)
      throw err.response?.data || err
    } finally {
      loading.value = false
    }
  }

  const addMember = async (email, familyId, memberRole) => {
    try {
      console.log('Adding member:', { email, familyId, memberRole })
      const memberData = {
        email: email,
        family_id: familyId,
        role: memberRole
      }

      const response = await axios.post(`${API_BASE}/api/families/${familyId}/members`, memberData, {
        withCredentials: true
      })

      await fetchFamilies() // Refresh de families
      return response.data
    } catch (err) {
      console.error('Error adding member:', err)
      throw err.response?.data || err
    }
  }

  // In je family store
  const removeMember = async (memberId, familyId) => {
    try {
      const response = await axios.delete(`${API_BASE}/api/families/${familyId}/members/${memberId}`, {
        withCredentials: true
      })
      return response.data
    } catch (error) {
      console.error('Error removing member:', error)
      throw error
    }
  }

  const fetchFamilyById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`${API_BASE}/api/families/${id}`, {
        withCredentials: true
      })

      currentFamily.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch family'
      console.error('Error fetching family:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Getters als computed properties
  const familiesCount = () => families.value.length

  return {
    // State
    families,
    currentFamily,
    loading,
    error,

    // Actions
    fetchFamilies,
    createFamily,
    addMember,
    removeMember,
    fetchFamilyById,

    // Getters
    familiesCount
  }
})