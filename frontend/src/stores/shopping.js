import { defineStore } from 'pinia'
import { api } from '../services/api'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useShoppingStore = defineStore('shopping', {
  state: () => ({
    shoppingLists: [],
    currentList: null,
    currentItems: []
  }),

  actions: {
    async fetchShoppingLists() {
      try {
        const response = await axios.get(`${API_BASE}/api/shopping-lists`, {
          withCredentials: true // Zorg dat cookies worden meegestuurd
        })
        this.shoppingLists = response.data.data
      } catch (error) {
        console.error('Error fetching shopping lists:', error)
        throw error
      }
    },

    async createShoppingList(listData) {
      try {
        //const response = await api.post('/api/shopping-lists', listData)
        const response = await axios.post(`${API_BASE}/api/shopping-lists`, listData, {
          withCredentials: true // Dit is de correcte configuratie
        })
        this.shoppingLists.push(response.data.data)
        return response.data
      } catch (error) {
        throw error.response.data
      }
    },

    async updateShoppingList(listId, payload) {
      try {
        const response = await axios.put(`${API_BASE}/api/shopping-lists/${listId}`, payload, {
          withCredentials: true
        })
        // Update de lijst in de store
        const index = this.shoppingLists.findIndex(list => list.id === listId)
        if (index !== -1) {
          this.shoppingLists[index] = response.data.data
        }
        return response.data
      } catch (error) {
        throw error.response?.data || error
      }
    },

    async deleteShoppingList(listId) {
      try {
        //const response = await api.delete(`/api/shopping-lists/${listId}`)
        const response = await axios.delete(`${API_BASE}/api/shopping-lists/${listId}`, {
          withCredentials: true // Dit is de correcte configuratie
        })
        this.shoppingLists = this.shoppingLists.filter(list => list.id !== listId)
        return response.data
      } catch (error) {
        throw error.response.data
      }
    },

    async fetchListItems(listId) {
      try {
        //const response = await api.get(`/api/shopping-lists/${listId}/items`)
        const response = await axios.get(`${API_BASE}/api/shopping-lists/${listId}/items`, {
          withCredentials: true // Zorg dat cookies worden meegestuurd
        })
        this.currentItems = response.data.data
      } catch (error) {
        console.error('Error fetching list items:', error)
        throw error
      }
    },

    async addListItem(listId, itemData) {
      try {
        //const response = await api.post(`/api/shopping-lists/${listId}/items`, itemData)
        const response = await axios.post(`${API_BASE}/api/shopping-lists/${listId}/items`, itemData, {
          withCredentials: true // Dit is de correcte configuratie
        })
        this.currentItems.push(response.data.data)
        return response.data
      } catch (error) {
        throw error.response.data
      }
    },

    async toggleItemComplete(itemId) {
      try {
        //const response = await api.patch(`/api/shopping-items/${itemId}/toggle`)
        const response = await axios.patch(`${API_BASE}/api/shopping-items/${itemId}/toggle`, null, {
          withCredentials: true // Dit is de correcte configuratie
        })
        // Update the item in currentItems
        const item = this.currentItems.find(i => i.id === itemId)
        if (item) {
          Object.assign(item, response.data.data)
        }
        return response.data
      } catch (error) {
        throw error.response.data
      }
    },

    async deleteListItem(itemId) {
      try {
        //const response = await api.delete(`/api/shopping-items/${itemId}`)
        const response = await axios.delete(`${API_BASE}/api/shopping-items/${itemId}`, {
          withCredentials: true // Dit is de correcte configuratie
        })
        this.currentItems = this.currentItems.filter(item => item.id !== itemId)
        return response.data
      } catch (error) {
        throw error.response.data
      }
    }
  }
})