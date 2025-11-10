import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../services/api'
import axios from 'axios'

export const useFamilyStore = defineStore('family', () => {
  // State
  const families = ref([])
  const currentFamily = ref(null)
  const loading = ref(false)
  const error = ref(null)


  // Actions
  const fetchFamilies = async () => {
    try {
      const response = await axios.get('http://localhost:8080/api/families', {
        withCredentials: true // Zorg dat cookies worden meegestuurd
      })  
      families.value = response.data.data
    } catch (error) {
      console.error('Error fetching families:', error)
      throw error
    }
  }

  const createFamily = async (familyData) => {
    try {
      //const response = await api.post('/families', familyData)
      const response = await axios.post('http://localhost:8080/api/families', familyData, {
      withCredentials: true // Dit is de correcte configuratie
    }) 
      families.value.push(response.data.data)
      return response.data
    } catch (error) {
      console.error('Error creating family:', error)
      throw error.response.data
    }
  }

  const addMember = async (email, familyId, memberRole) => {
    try {
      console.log('familyId:', familyId, 'memberData:', email, 'role:', memberRole)
      const memberData = {
          email: email,    // of gewoon `email`
          family_id: familyId,
          role: memberRole
      };
      const response = await axios.post(`http://localhost:8080/api/families/${familyId}/members`, memberData, {
      withCredentials: true // Zorg dat cookies worden meegestuurd
    });
      await fetchFamilies()
      return response.data
    } catch (error) {
      console.error('Error adding member:', error)
      throw error.response.data
    }
  }

  const fetchFamilyById = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(`http://localhost:8080/api/families/${id}`, {
        withCredentials: true // Zorg dat cookies worden meegestuurd
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

  // Return state and actions
  return {
    families,
    currentFamily,
    fetchFamilies,
    createFamily,
    addMember,
    fetchFamilyById,
    loading,
    error,
  }
})