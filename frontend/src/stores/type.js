import { defineStore } from 'pinia'
import axios from 'axios'
import { ref } from 'vue'
import { API_BASE } from '../utils/config'

export const useTypeStore = defineStore('type', () => {
    const types = ref([])
    const loadingTypes = ref(false)

    const fetchTypes = async () => {
        loadingTypes.value = true
        try {
            const response = await axios.get(`${API_BASE}/api/auth/types`, {
                withCredentials: true
            })
            console.log('Types fetched:', response.data);
            
            types.value = response.data
        } catch (error) {
            console.error(error)
        } finally {
            loadingTypes.value = false
        }
    }

    return { types, loadingTypes, fetchTypes }
})