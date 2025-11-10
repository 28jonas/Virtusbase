<template>
  <div v-if="loading" class="flex justify-center items-center h-64">
    <p>Loading...</p>
  </div>

  <div v-else-if="familyData" class="max-w-4xl mx-auto">
    <!-- Error Alert -->
    <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <div class="flex justify-between items-center">
        <span>{{ errorMessage }}</span>
        <button @click="errorMessage = ''" class="text-red-800 hover:text-red-900">
          &times;
        </button>
      </div>
    </div>

    <!-- Success Alert -->
    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      <div class="flex justify-between items-center">
        <span>{{ successMessage }}</span>
        <button @click="successMessage = ''" class="text-green-800 hover:text-green-900">
          &times;
        </button>
      </div>
    </div>

    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold">{{ familyData.name }}</h1>
          <p class="text-gray-600 mt-2">{{ familyData.members?.count || 0 }} members</p>
          <div class="flex justify-between items-center mb-6">
            <button @click="showAddMemberModal = true" class="bg-blue-600 text-white px-4 py-2 rounded">
              Add Member
            </button>
          </div>
        </div>
        <button @click="$router.back()" class="text-gray-600 hover:text-gray-800">
          ← Back
        </button>
      </div>
    </div>

    <!-- Family Members -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-semibold mb-4">Family Members</h2>
      
      <div v-if="familyData.members && familyData.members.length">
        <div v-for="member in familyData.members" :key="member.id" class="border-b py-3 last:border-b-0">
          <p class="font-medium">{{ member.first_name }} {{ member.last_name }}</p>
          <p class="text-gray-600 text-sm">{{ member.email || 'No email' }}</p>
        </div>
      </div>
      
      <div v-else class="text-gray-500 text-center py-8">
        <p>No members in this family yet</p>
      </div>
    </div>
  </div>

  <div v-else class="text-center py-16">
    <p class="text-red-600">Family not found</p>
    <button @click="$router.push('/families')" class="text-blue-600 mt-4">
      Back to Families
    </button>
  </div>

  <!-- Add Member Modal -->
  <div v-if="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
      <h3 class="text-xl font-semibold mb-4">Add New Member</h3>
      
      <!-- Error in modal -->
      <div v-if="modalError" class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
        {{ modalError }}
      </div>

      <select name="newMemberRole" id="newMemberRole" class="w-full px-3 py-2 border rounded-md mb-4" v-model="newMemberRole">
        <option value="child">Child</option>
        <option value="parent">Parent</option>
      </select>
      
      <input 
        v-model="addNewMember" 
        placeholder="Email of the member you want to add" 
        class="w-full px-3 py-2 border rounded-md mb-4"
        @keyup.enter="addMember"
      >
      <div class="flex justify-end space-x-2">
        <button @click="closeModal" class="px-4 py-2 text-gray-600">Cancel</button>
        <button 
          @click="addMember" 
          :disabled="addingMember" 
          class="px-4 py-2 bg-blue-600 text-white rounded disabled:bg-blue-400"
        >
          <span v-if="addingMember">Adding...</span>
          <span v-else>Add</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useFamilyStore } from '../stores/family'

export default {
  name: 'FamilyDetail',
  props: ['id'],
  setup() {
    const route = useRoute()
    const familyStore = useFamilyStore()
    const family = ref(null)
    const loading = ref(true)
    const showAddMemberModal = ref(false)
    const addNewMember = ref('')
    const errorMessage = ref('')
    const successMessage = ref('')
    const modalError = ref('')
    const addingMember = ref(false)
    const newMemberRole = ref('')

    const familyData = computed(() => {
      return family.value?.data || null
    })

    onMounted(async () => {
      const familyId = route.params.id
      try {
        family.value = await familyStore.fetchFamilyById(familyId)
      } catch (error) {
        console.error('Error loading family:', error)
        errorMessage.value = 'Failed to load family details.'
      } finally {
        loading.value = false
      }
    })

    const closeModal = () => {
      showAddMemberModal.value = false
      addNewMember.value = ''
      modalError.value = ''
      newMemberRole.value = ''
    }

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

    return { 
      familyData,
      loading,
      showAddMemberModal,
      addMember, 
      addNewMember,
      newMemberRole,
      errorMessage,
      successMessage,
      modalError,
      addingMember,
      closeModal
    }
  }
}
</script>