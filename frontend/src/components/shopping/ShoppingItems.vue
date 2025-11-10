<!-- components/shopping/ShoppingItems.vue -->
<template>
  <div class="space-y-3">
    <div v-if="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
      <div class="text-4xl mb-2">🛒</div>
      <p>Nog geen items in deze lijst</p>
    </div>
    
    <div v-else class="space-y-3">
      <div v-for="item in items" :key="item.id" 
        class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
        <button @click="toggleItem(item.id)" 
          class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
          :class="item.is_completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-600'">
          <span v-if="item.completed" class="text-xs">✓</span>
        </button>
        
        <div class="flex-1 min-w-0">
          <div class="font-medium text-gray-900 dark:text-white" :class="{ 'line-through text-gray-400': item.completed }">
            {{ item.name }}
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">Aantal: {{ item.quantity }}</div>
        </div>

        <!-- <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        {{ item.category }}
      </span> -->
        
        <!-- <button @click="editItem(item)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
          ✏️
        </button> -->
        
        <button @click="deleteItem(item.id)" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
          🗑️
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  items: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['toggle', 'edit', 'delete'])

const toggleItem = (itemId) => {
  emit('toggle', itemId)
}

const editItem = (item) => {
  emit('edit', item)
}

const deleteItem = (itemId) => {
  if (confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
    emit('delete', itemId)
  }
}
</script>