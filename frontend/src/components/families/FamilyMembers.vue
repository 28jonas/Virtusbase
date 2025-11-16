<!-- components/families/FamilyMembers.vue -->
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div v-for="member in members" :key="member.id" 
      class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-600 group">
      
      <!-- Avatar -->
      <div v-if="member.profilePicture" class="text-3xl mb-2">
        <img :src="member.profilePicture" :alt="member.name" class="w-12 h-12 rounded-full mx-auto">
      </div>
      <div v-else class="text-3xl mb-2">
        {{ member.avatar }}
      </div>
      
      <!-- Name -->
      <h3 class="font-medium text-gray-900 dark:text-white">{{ member.name }}</h3>
      
      <!-- Email (als naam niet beschikbaar is) -->
      <p v-if="!member.firstName && member.email" class="text-sm text-gray-500 dark:text-gray-400">
        {{ member.email }}
      </p>
      
      <!-- Role -->
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ member.role }}</p>
      
      <!-- Family Name -->
      <p class="text-xs text-gray-400 mb-2" v-if="member.familyName">
        {{ member.familyName }}
      </p>
      
      <!-- Tasks -->
      <div class="text-xs text-gray-400">{{ member.tasks }} taken</div>
      
      <!-- Hover actions -->
      <div class="mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <!-- <button class="text-blue-500 text-xs hover:text-blue-600 mr-2">Bekijk</button> -->
        <button @click="$emit('removeMember', member.id)" class="text-red-500 text-xs hover:text-red-600">Verwijder</button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  members: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['removeMember']);
</script>