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
      <FamilyCard 
        v-for="family in families" 
        :key="family.id" 
        :family="family" 
        :is-selected="selectedFamilyId === family.id"
        @click="selectFamily(family)"
        @edit="openAddMemberModal(family)" 
        @delete="deleteFamily" 
      />
    </div>

    <!-- Family Members Section -->
    <div v-if="selectedFamily" class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            Leden van {{ selectedFamily.name }}
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ currentFamilyMembers.length }} leden
          </p>
        </div>
        <button 
          @click="openAddMemberModal(selectedFamily)" 
          class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
        >
          + Lid toevoegen
        </button>
      </div>
      
      <!-- Loading State -->
      <div v-if="loadingMembers" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Laden...</p>
      </div>
      
      <!-- Members Grid -->
      <FamilyMembers 
        v-else 
        :members="currentFamilyMembers" 
        @remove-member="handleRemoveMember"
      />
      
      <!-- Empty State -->
      <div v-if="!loadingMembers && currentFamilyMembers.length === 0" class="text-center py-8">
        <div class="text-4xl mb-2">👨‍👩‍👧‍👦</div>
        <p class="text-gray-500 dark:text-gray-400">Nog geen leden in deze familie</p>
        <button 
          @click="openAddMemberModal(selectedFamily)" 
          class="mt-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
        >
          Voeg het eerste lid toe
        </button>
      </div>
    </div>

    <!-- No Family Selected State -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center py-12">
      <div class="text-4xl mb-4">👆</div>
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Selecteer een familie</h3>
      <p class="text-gray-500 dark:text-gray-400">Klik op een familiekaart hierboven om de leden te bekijken</p>
    </div>

    <!-- Create Family Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-96 max-w-full">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Nieuwe Familie Aanmaken</h3>
        <input 
          v-model="newFamilyName" 
          placeholder="Familie naam" 
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-4 dark:bg-gray-700 dark:text-white"
        >
        <div class="flex justify-end space-x-2">
          <button @click="showCreateModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-400">Annuleren</button>
          <button @click="createFamily" class="px-4 py-2 bg-blue-600 text-white rounded">Aanmaken</button>
        </div>
      </div>
    </div>

    <!-- Add Member Modal -->
    <div v-if="showAddMemberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-96 max-w-full">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
          {{ selectedFamily ? `Lid toevoegen aan ${selectedFamily.name}` : 'Lid toevoegen' }}
        </h3>

        <!-- Family selection if no family is selected -->
        <div v-if="!selectedFamily" class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Selecteer Familie
          </label>
          <select 
            v-model="selectedFamilyId"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
          >
            <option value="" disabled>Kies een familie</option>
            <option 
              v-for="family in families" 
              :key="family.id" 
              :value="family.id"
            >
              {{ family.name }}
            </option>
          </select>
        </div>

        <!-- Error in modal -->
        <div v-if="modalError" class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
          {{ modalError }}
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Rol
          </label>
          <select 
            v-model="newMemberRole" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
          >
            <option value="child">Kind</option>
            <option value="parent">Ouder</option>
          </select>
        </div>

        <input 
          v-model="addNewMember" 
          placeholder="Email van het lid dat je wilt toevoegen"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md mb-4 dark:bg-gray-700 dark:text-white"
          @keyup.enter="addMember"
        >
        
        <div class="flex justify-end space-x-2">
          <button @click="closeAddMemberModal" class="px-4 py-2 text-gray-600 dark:text-gray-400">Annuleren</button>
          <button 
            @click="addMember" 
            :disabled="addingMember || !canAddMember"
            class="px-4 py-2 bg-blue-600 text-white rounded disabled:bg-blue-400"
          >
            <span v-if="addingMember">Toevoegen...</span>
            <span v-else>Toevoegen</span>
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
import { useFamilyStore } from '../stores/family'

const familyStore = useFamilyStore()
const showCreateModal = ref(false)
const newFamilyName = ref('')
const families = computed(() => familyStore.families)
const showAddMemberModal = ref(false)
const addNewMember = ref('')
const modalError = ref('')
const addingMember = ref(false)
const newMemberRole = ref('parent')
const selectedFamily = ref(null) // Houdt de geselecteerde familie bij
const selectedFamilyId = ref(null) // Houdt het ID van de geselecteerde familie bij
const loadingMembers = ref(false)

// Computed property voor leden van de geselecteerde familie
const currentFamilyMembers = computed(() => {
  if (!selectedFamily.value) return []
  
  const family = families.value.find(f => f.id === selectedFamily.value.id)
  if (!family || !family.members || !Array.isArray(family.members)) return []
  
  return family.members.map(member => {
    // Bouw de volledige naam op
    const fullName = member.first_name && member.last_name 
      ? `${member.first_name} ${member.last_name}`
      : member.email || 'Onbekende gebruiker'
    
    return {
      id: member.id,
      name: fullName,
      email: member.email,
      firstName: member.first_name,
      lastName: member.last_name,
      profilePicture: member.profile_picture,
      familyName: family.name,
      familyId: family.id,
      avatar: getAvatarForMember(member),
      role: getRoleDisplayName(member.pivot?.role),
      tasks: member.tasks_count || 0,
      rawMember: member
    }
  })
})

// Helper functions
const getAvatarForMember = (member) => {
  if (member.profile_picture) {
    return member.profile_picture
  }
  
  if (member.first_name) {
    return member.first_name.charAt(0).toUpperCase()
  }
  
  if (member.email) {
    return member.email.charAt(0).toUpperCase()
  }
  
  const role = member.pivot?.role
  if (role === 'parent') {
    return '👨‍👩‍👧‍👦'
  } else {
    return '👤'
  }
}

const getRoleDisplayName = (role) => {
  const roleMap = {
    '1': 'Ouder',
    '2': 'Kind', 
    'admin': 'Beheerder',
    'owner': 'Eigenaar'
  }
  return roleMap[role] || role || 'Lid'
}

onMounted(async () => {
  await loadFamiliesWithMembers()
})

const loadFamiliesWithMembers = async () => {
  loadingMembers.value = true
  try {
    await familyStore.fetchFamilies()
  } catch (error) {
    console.error('Error loading families:', error)
  } finally {
    loadingMembers.value = false
  }
}

// Selecteer een familie
const selectFamily = (family) => {
  selectedFamily.value = family
  selectedFamilyId.value = family.id
}

const createFamily = async () => {
  if (newFamilyName.value.trim()) {
    try {
      await familyStore.createFamily({ name: newFamilyName.value })
      showCreateModal.value = false
      newFamilyName.value = ''
      await loadFamiliesWithMembers()
    } catch (error) {
      console.error('Error creating family:', error)
    }
  }
}

const openAddMemberModal = (family = null) => {
  const targetFamily = family || selectedFamily.value
  selectedFamily.value = targetFamily
  selectedFamilyId.value = targetFamily ? targetFamily.id : ''
  showAddMemberModal.value = true
  modalError.value = ''
  addNewMember.value = ''
  newMemberRole.value = 'parent'
}

const closeAddMemberModal = () => {
  showAddMemberModal.value = false
  // Behoud de geselecteerde familie
}

const canAddMember = computed(() => {
  const hasFamily = selectedFamily.value || selectedFamilyId.value
  const hasEmail = addNewMember.value.trim()
  return hasFamily && hasEmail
})

const addMember = async () => {
  if (!canAddMember.value) {
    modalError.value = 'Selecteer een familie en voer een email in'
    return
  }

  const familyId = selectedFamily.value ? selectedFamily.value.id : selectedFamilyId.value
  
  if (!familyId) {
    modalError.value = 'Selecteer een familie'
    return
  }

  addingMember.value = true
  modalError.value = ''

  try {
    console.log('Adding member:', addNewMember.value, 'to family ID:', familyId, 'with role:', newMemberRole.value)
    await familyStore.addMember(addNewMember.value, familyId, newMemberRole.value)

    // Success - refresh family data
    await loadFamiliesWithMembers()
    
    // Close modal
    closeAddMemberModal()
    
    console.log('Member added successfully!')

  } catch (error) {
    console.error('Error adding member:', error)

    if (error.response?.data?.message) {
      modalError.value = error.response.data.message
    } else if (error.response?.data?.error) {
      modalError.value = error.response.data.error
    } else if (error.message) {
      modalError.value = error.message
    } else {
      modalError.value = 'Failed to add member. Please try again.'
    }
  } finally {
    addingMember.value = false
  }
}

// Nieuwe functie om lid te verwijderen
const handleRemoveMember = async (memberId) => {
  if (!selectedFamily.value) return
  
  const memberToRemove = currentFamilyMembers.value.find(member => member.id === memberId)
  
  if (!memberToRemove) {
    console.error('Lid niet gevonden')
    return
  }

  if (confirm(`Weet je zeker dat je ${memberToRemove.name} wilt verwijderen uit de familie ${selectedFamily.value.name}?`)) {
    try {
      await familyStore.removeMember(memberId, selectedFamily.value.id)
      await loadFamiliesWithMembers()
      console.log('Lid succesvol verwijderd')
    } catch (error) {
      console.error('Error removing member:', error)
      alert('Er ging iets mis bij het verwijderen van het lid')
    }
  }
}

const deleteFamily = async (family) => {
  if (confirm(`Weet je zeker dat je de familie "${family.name}" wilt verwijderen?`)) {
    try {
      await familyStore.deleteFamily(family.id)
      // Als de geselecteerde familie wordt verwijderd, deselecteer deze
      if (selectedFamily.value && selectedFamily.value.id === family.id) {
        selectedFamily.value = null
        selectedFamilyId.value = null
      }
      await loadFamiliesWithMembers()
    } catch (error) {
      console.error('Error deleting family:', error)
    }
  }
}
</script>