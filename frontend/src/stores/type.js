import { defineStore } from 'pinia'
import axios from 'axios'
import { ref } from 'vue'

export const useTypeStore = defineStore('type', () => {
    const types = ref([])
    const loadingTypes = ref(false)

    const fetchTypes = async () => {
        loadingTypes.value = true
        try {
            const response = await axios.get('http://localhost:8080/api/auth/types')
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