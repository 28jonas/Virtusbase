<!-- components/dashboard/QuickActions.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Snelle Acties</h3>
    
    <div class="grid grid-cols-2 gap-4">
      <button v-for="action in quickActions" :key="action.name" 
        @click="openModal(action.type)"
        class="p-4 rounded-xl border border-gray-100 dark:border-gray-700 transition-all duration-200 hover:shadow-md hover:scale-105 group bg-white dark:bg-gray-800 hover:border-blue-200 dark:hover:border-blue-800">
        <div class="text-2xl mb-2 transition-transform group-hover:scale-110">{{ action.icon }}</div>
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ action.name }}</div>
      </button>
    </div>

    <!-- Unified Modal -->
    <DashboardModal 
      v-if="activeModal"
      :modal-type="activeModal"
      @close="closeModal"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import DashboardModal from './DashboardModal.vue'

const activeModal = ref(null)

const quickActions = [
  { 
    name: 'Nieuwe Taak', 
    icon: '📝', 
    type: 'todo'
  },
  { 
    name: 'Boodschap', 
    icon: '🛒', 
    type: 'shopping'
  },
  { 
    name: 'Event', 
    icon: '📅', 
    type: 'event'
  },
  { 
    name: 'Notitie', 
    icon: '📋', 
    type: 'note'
  }
]

const openModal = (modalType) => {
  activeModal.value = modalType
}

const closeModal = () => {
  activeModal.value = null
}

const handleSaved = (result) => {
  console.log(`${result.type} opgeslagen:`, result.data)
  closeModal()
  
  // Hier kun je eventueel een toast notification tonen
  // of de parent component laten weten dat er nieuwe data is
}
</script>