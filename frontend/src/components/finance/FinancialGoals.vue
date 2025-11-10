<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm dark:shadow-none dark:border dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Financial Goals
      </h2>

      <button @click="openModal(null, false)"
              class="text-sm px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white flex items-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="goalsStore.goals.length >= 4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add Goal
      </button>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4 auto-rows-[160px] max-h-[160px] overflow-y-auto">
      <div v-if="goalsStore.goals && goalsStore.goals.length > 0">
        <div v-for="goal in goalsStore.goals" :key="goal.id"
             class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md dark:hover:shadow-none transition-shadow relative bg-white dark:bg-gray-700 cursor-pointer"
             @click="openModal(goal.id, true)">
          <div class="flex justify-between items-start mb-2">
            <div class="text-lg font-semibold dark:text-gray-100">€{{ formatCurrency(goal.amount) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded-full">
              {{ formatDate(goal.date) }}
            </div>
          </div>

          <div class="flex items-center space-x-2 text-sm mb-3">
            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
              <i class="fas fa-bullseye text-indigo-600 dark:text-indigo-400 text-xs"></i>
            </div>
            <span class="text-gray-700 dark:text-gray-300">
              {{ goal.categories[0]?.name || 'Uncategorized' }}
            </span>
          </div>

          <div class="mb-2">
            <div class="h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-500"
                   :style="{ width: Math.min(100, (goal.transfer / goal.amount) * 100) + '%' }"></div>
            </div>
          </div>

          <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>{{ Math.round((goal.transfer / goal.amount) * 100) }}% completed</span>
            <span>€{{ formatCurrency(goal.transfer) }}/€{{ formatCurrency(goal.amount) }}</span>
          </div>

          <div v-if="goal.card" class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
            {{ goal.card.bank?.name || 'Unknown bank' }}
          </div>
        </div>
      </div>
      
      <!-- Empty state -->
      <div v-else class="grid grid-cols-2 gap-4">
        <div v-for="i in 4" :key="i"
             class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 flex flex-col items-center justify-center h-full min-h-[120px] bg-gray-50 dark:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span class="text-sm text-gray-500 dark:text-gray-400">No goal set</span>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Modal :show="goalsStore.showModal" @close="closeModal">
      <template #header>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ goalsStore.isEdit ? "Edit Goal" : "Add New Goal" }}</h3>
      </template>

      <form @submit.prevent="handleSubmitGoal">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Goal Type</label>
            <select v-model="goalsStore.form.type"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200">
              <option value="">Select a type</option>
              <option v-for="category in goalsStore.categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (€)</label>
              <input type="number" step="0.01" v-model="goalsStore.form.amount"
                     class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Date</label>
              <input type="date" v-model="goalsStore.form.goalDate"
                     class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Linked Card</label>
            <select v-model="goalsStore.form.cardId"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200">
              <option value="">No linked card</option>
              <option v-for="card in goalsStore.cards" :key="card.id" :value="card.id">
                {{ card.bank.name }} - €{{ formatCurrency(card.balance) }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Initial Transfer (€)</label>
            <input type="number" step="0.01" v-model="goalsStore.form.transfer"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200">
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" @click="closeModal"
                  class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            {{ goalsStore.isEdit ? "Save" : "Create" }}
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useGoalsStore } from '../../stores/goals'
import Modal from './Modal.vue'

const goalsStore = useGoalsStore()

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getIconClass = (type) => {
  const icons = {
    'Holidays': 'fa-mountain',
    'Renovation': 'fa-landmark',
    'Education': 'fa-graduation-cap',
    'Car': 'fa-car',
    'Emergency': 'fa-shield',
    'Other': 'fa-circle-dollar-to-slot',
  }
  return `fas ${icons[type] || 'fa-circle-dollar-to-slot'}`
}

const openModal = (goalId = null, isEdit = false) => {
  goalsStore.openModal(goalId, isEdit)
}

const closeModal = () => {
  goalsStore.closeModal()
}

const handleSubmitGoal = async () => {
  try {
    if (goalsStore.isEdit) {
      await goalsStore.updateGoal()
      await goalsStore.loadGoals()
    } else {
      await goalsStore.addGoal()
      await goalsStore.loadGoals()
    }
  } catch (error) {
    console.error('Failed to save goal:', error)
  }
}

onMounted(async () => {
  await goalsStore.loadGoals()
  //console.log(goalsStore.goals)
  await goalsStore.loadCategories()
  await goalsStore.loadCards()
})
</script>