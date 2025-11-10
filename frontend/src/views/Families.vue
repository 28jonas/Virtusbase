<!-- views/Families.vue -->
<template>
  <div class="p-6 space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Familie Beheer</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Beheer je familie en huishouden</p>
      </div>
      <button @click="showCreateModal = true"
        class="bg-blue-500 text-white px-6 py-3 rounded-xl hover:bg-blue-600 transition-colors flex items-center space-x-2">
        <span>👨‍👩‍👧‍👦</span>
        <span>Nieuwe Familie</span>
      </button>
    </div>

    <!-- Families Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <FamilyCard v-for="family in families" :key="family.id" :family="family" @edit="showAddMemberModal = true"
        @delete="deleteFamily" />
    </div>

    <!-- Family Members Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Familie Leden</h2>
        <button class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
          + Lid toevoegen
        </button>
      </div>
      <FamilyMembers :members="familyMembers" />
    </div>

    <!-- Create Family Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Create New Family</h3>
        <input v-model="newFamilyName" placeholder="Family name" class="w-full px-3 py-2 border rounded-md mb-4">
        <div class="flex justify-end space-x-2">
          <button @click="showCreateModal = false" class="px-4 py-2 text-gray-600">Cancel</button>
          <button @click="createFamily" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
        </div>
      </div>
    </div>

    <!-- Add Member Modal -->
    <div v-if="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Add New Member</h3>

        <!-- Error in modal -->
        <div v-if="modalError" class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
          {{ modalError }}
        </div>

        <select name="newMemberRole" id="newMemberRole" class="w-full px-3 py-2 border rounded-md mb-4"
          v-model="newMemberRole">
          <option value="child">Child</option>
          <option value="parent">Parent</option>
        </select>

        <input v-model="addNewMember" placeholder="Email of the member you want to add"
          class="w-full px-3 py-2 border rounded-md mb-4" @keyup.enter="addMember">
        <div class="flex justify-end space-x-2">
          <button @click="showAddMemberModal = false" class="px-4 py-2 text-gray-600">Cancel</button>
          <button @click="addMember" :disabled="addingMember"
            class="px-4 py-2 bg-blue-600 text-white rounded disabled:bg-blue-400">
            <span v-if="addingMember">Adding...</span>
            <span v-else>Add</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import FamilyCard from '../components/families/FamilyCard.vue'
import FamilyMembers from '../components/families/FamilyMembers.vue'

import { useRouter } from 'vue-router'
import { useFamilyStore } from '../stores/family'

const familyStore = useFamilyStore()
const router = useRouter()
const showCreateModal = ref(false)
const newFamilyName = ref('')
const families = computed(() => familyStore.families)
const showAddMemberModal = ref(false)
const addNewMember = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const modalError = ref('')
const addingMember = ref(false)
const newMemberRole = ref('')

onMounted(async () => {
  await familyStore.fetchFamilies()
})

const createFamily = async () => {
  if (newFamilyName.value) {
    await familyStore.createFamily({ name: newFamilyName.value })
    showCreateModal.value = false
    newFamilyName.value = ''
  }
}

const viewFamily = (family) => {
  router.push({ name: 'FamilyDetail', params: { id: family.id } })
}

// const families = ref([
//   {
//     id: 1,
//     name: 'Hoofd Familie',
//     memberCount: 4,
//     color: 'blue',
//     tasks: 12,
//     completedTasks: 8
//   },
//   {
//     id: 2,
//     name: 'Weekend Huis',
//     memberCount: 2,
//     color: 'green',
//     tasks: 5,
//     completedTasks: 3
//   }
// ])

const addMember = async () => {
  if (!addNewMember.value.trim()) {
    modalError.value = 'Please enter an email address.'
    return
  }

  addingMember.value = true
  modalError.value = ''

  try {
    await familyStore.addMember(addNewMember.value, route.params.id, newMemberRole.value)

    // Success - refresh family data
    family.value = await familyStore.fetchFamilyById(route.params.id)
    successMessage.value = 'Member added successfully!'
    closeModal()

    // Auto-hide success message after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

  } catch (error) {
    console.error('Error adding member:', error)

    // Handle different error formats
    if (error.response?.data?.message) {
      // Laravel validation error
      modalError.value = error.response.data.message
    } else if (error.response?.data?.error) {
      // Laravel error response
      modalError.value = error.response.data.error
    } else if (error.message) {
      // General error
      modalError.value = error.message
    } else {
      modalError.value = 'Failed to add member. Please try again.'
    }
  } finally {
    addingMember.value = false
  }
}

const familyMembers = ref([
  { id: 1, name: 'Johan', role: 'Ouder', avatar: '👨', tasks: 5 },
  { id: 2, name: 'Marie', role: 'Ouder', avatar: '👩', tasks: 4 },
  { id: 3, name: 'Thomas', role: 'Kind', avatar: '👦', tasks: 2 },
  { id: 4, name: 'Emma', role: 'Kind', avatar: '👧', tasks: 1 }
])

const editFamily = (family) => {
  console.log('Edit family:', family)
}

const deleteFamily = (family) => {
  console.log('Delete family:', family)
}
</script>
<!-- <template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Families</h1>
      <button @click="showCreateModal = true" class="bg-blue-600 text-white px-4 py-2 rounded">
        Create Family
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="family in families" :key="family.id" class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-2">{{ family.name }}</h3>
        <p class="text-gray-600 mb-4">{{ family.members?.length || 0 }} members</p>
        <button @click="viewFamily(family)" class="text-blue-600 hover:underline">
          View Details
        </button>
      </div>
    </div>

    
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useFamilyStore } from '../stores/family'

export default {
  name: 'Families',
  setup() {
    const familyStore = useFamilyStore()
    const router = useRouter()
    const showCreateModal = ref(false)
    const newFamilyName = ref('')
    const families = computed(() => familyStore.families)

    onMounted(async () => {
      await familyStore.fetchFamilies()
    })

    const createFamily = async () => {
      if (newFamilyName.value) {
        await familyStore.createFamily({ name: newFamilyName.value })
        showCreateModal.value = false
        newFamilyName.value = ''
      }
    }

    const viewFamily = (family) => {
  router.push({ name: 'FamilyDetail', params: { id: family.id } })
}

    return { families, showCreateModal, newFamilyName, createFamily, viewFamily }
  }
}
</script> -->