<!-- views/Shopping.vue -->
<template>
  <div class="p-6 space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Boodschappen</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Beheer je boodschappenlijstjes</p>
      </div>
      <button @click="openCreateModal()"
        class="bg-orange-500 text-white px-6 py-3 rounded-xl hover:bg-orange-600 transition-colors flex items-center space-x-2">
        <span>🛒</span>
        <span>Nieuwe Lijst</span>
      </button>
    </div>

    <!-- Shopping Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ShoppingListCard v-for="list in shoppingLists" :key="list.id" :list="list" @click="selectList(list)"
        @edit="openEditModal" @delete="deleteList" />
    </div>

    <!-- Current List -->
    <div v-if="currentList"
      class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ currentList.name }}</h2>
          <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
            Aangemaakt op {{ formatDate(currentList.created_at) }}
          </p>
        </div>
        <div class="flex space-x-2">
          <!-- <button
            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Sorteer
          </button> -->
          <button @click="showAddItemModal = true"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            + Item
          </button>
        </div>
      </div>

      <ShoppingItems :items="currentItems" @toggle="toggleItemComplete" @edit="editListItem" @delete="deleteListItem" />
    </div>

    <!-- Empty State -->
    <div v-else
      class="text-center py-12 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
      <div class="text-6xl mb-4">🛒</div>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Geen lijst geselecteerd</h3>
      <p class="text-gray-600 dark:text-gray-400">Selecteer een lijst om de items te bekijken of maak een nieuwe lijst
        aan.</p>
    </div>

    <!-- Create/Edit List Modal -->
    <div v-if="showListModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
          {{ isEditingList ? 'Lijst Bewerken' : 'Nieuwe Boodschappenlijst' }}
        </h3>

        <form @submit.prevent="isEditingList ? updateList() : createList()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Naam *</label>
            <input v-model="listForm.name" type="text" required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              placeholder="Bijv. Weekboodschappen" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
            <select v-model="listForm.owner_type"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
              <option value="user">Persoonlijk</option>
              <option value="family">Gezamenlijk</option>
            </select>
          </div>

          <div v-if="listForm.owner_type === 'family'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gezin</label>
            <select v-model="listForm.family_id"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
              <option value="">Selecteer gezin</option>
              <option v-for="family in families" :key="family.id" :value="family.id">
                {{ family.name }}
              </option>
            </select>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeListModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
              Annuleren
            </button>
            <button type="submit"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-semibold">
              {{ isEditingList ? 'Bijwerken' : 'Aanmaken' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add Item Modal -->
    <div v-if="showAddItemModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Item Toevoegen</h3>

        <form @submit.prevent="addItem" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Naam *</label>
            <input v-model="newItem.name" type="text" required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              placeholder="Bijv. Brood, Melk, Eieren" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hoeveelheid</label>
            <input v-model="newItem.quantity" type="number" min="1"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" />
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="showAddItemModal = false"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
              Annuleren
            </button>
            <button type="submit"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-semibold">
              Toevoegen
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useShoppingStore } from '../stores/shopping'
import { useFamilyStore } from '../stores/family'
import ShoppingListCard from '../components/shopping/ShoppingListCard.vue'
import ShoppingItems from '../components/shopping/ShoppingItems.vue'

const shoppingStore = useShoppingStore()
const familyStore = useFamilyStore()

// Refs
const showListModal = ref(false)
const showAddItemModal = ref(false)
const currentList = ref(null)
const isEditingList = ref(false)
const editingListId = ref(null)

// Reactive objects
const listForm = reactive({
  name: '',
  owner_type: 'user',
  family_id: ''
})

const newItem = reactive({
  name: '',
  quantity: 1
})

// Computed properties
const shoppingLists = computed(() => shoppingStore.shoppingLists)
const currentItems = computed(() => shoppingStore.currentItems)
const families = computed(() => familyStore.families)

// Lifecycle
onMounted(async () => {
  await shoppingStore.fetchShoppingLists()
  await familyStore.fetchFamilies()
})

// List Modal functions
const openCreateModal = () => {
  isEditingList.value = false
  editingListId.value = null
  resetListForm()
  showListModal.value = true
}

const openEditModal = (list) => {
  isEditingList.value = true
  editingListId.value = list.id
  listForm.name = list.name
  listForm.owner_type = list.owner_type
  listForm.family_id = list.family_id || ''
  showListModal.value = true
}

const closeListModal = () => {
  showListModal.value = false
  resetListForm()
}

const resetListForm = () => {
  Object.assign(listForm, {
    name: '',
    owner_type: 'user',
    family_id: ''
  })
}

// List CRUD operations
const createList = async () => {
  try {
    await shoppingStore.createShoppingList(listForm)
    closeListModal()
  } catch (error) {
    alert(error.message || 'Failed to create list')
  }
}

const updateList = async () => {
  try {
    await shoppingStore.updateShoppingList(editingListId.value, listForm)
    closeListModal()
  } catch (error) {
    alert(error.message || 'Failed to update list')
  }
}

const deleteList = async (list) => {
  if (confirm(`Weet je zeker dat je "${list.name}" wilt verwijderen?`)) {
    try {
      await shoppingStore.deleteShoppingList(list.id)
      if (currentList.value?.id === list.id) {
        currentList.value = null
      }
    } catch (error) {
      alert(error.message || 'Failed to delete list')
    }
  }
}

// Item operations
const selectList = async (list) => {
  currentList.value = list
  await shoppingStore.fetchListItems(list.id)
}

const addItem = async () => {
  if (!currentList.value) return

  try {
    await shoppingStore.addListItem(currentList.value.id, newItem)
    showAddItemModal.value = false
    resetNewItem()
  } catch (error) {
    alert(error.message || 'Failed to add item')
  }
}

const toggleItemComplete = async (itemId) => {
  console.log('Toggle item complete:', itemId)
  try {
    await shoppingStore.toggleItemComplete(itemId)
  } catch (error) {
    alert(error.message || 'Failed to update item')
  }
}

const deleteListItem = async (itemId) => {
  try {
    await shoppingStore.deleteListItem(itemId)
  } catch (error) {
    alert(error.message || 'Failed to delete item')
  }
}

const editListItem = (item) => {
  console.log('Edit item:', item)
  // Hier kun je later een modal voor item editing toevoegen
}

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('nl-NL', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const resetNewItem = () => {
  Object.assign(newItem, {
    name: '',
    quantity: 1
  })
}
</script>