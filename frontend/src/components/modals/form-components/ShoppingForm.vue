<!-- components/modals/form-components/ShoppingForm.vue -->
<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Boodschappenlijst *
      </label>
      <select 
        v-model="modelValue.shopping_list_id"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        @change="validateForm"
      >
        <option value="" disabled>Selecteer een boodschappenlijst</option>
        <option 
          v-for="list in shoppingLists" 
          :key="list.id" 
          :value="list.id"
        >
          {{ list.name }}
        </option>
      </select>
      <p v-if="shoppingLists.length === 0" class="text-sm text-gray-500 mt-1">
        Geen boodschappenlijsten beschikbaar
      </p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Productnaam *
      </label>
      <input 
        v-model="modelValue.name"
        type="text" 
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Wat wil je kopen?"
        @input="validateForm"
      >
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Aantal
      </label>
      <input 
        v-model="modelValue.quantity"
        type="number" 
        min="1"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
      >
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useShoppingStore } from '../../../stores/shopping'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue', 'validation'])

const shoppingStore = useShoppingStore()
const shoppingLists = ref([])
const loading = ref(false)

// Fetch shopping lists when component mounts
onMounted(async () => {
  try {
    loading.value = true
    await shoppingStore.fetchShoppingLists()
    shoppingLists.value = shoppingStore.shoppingLists
    
    // Auto-select first list if none selected and lists available
    if (shoppingLists.value.length > 0 && !props.modelValue.shopping_list_id) {
      emit('update:modelValue', {
        ...props.modelValue,
        shopping_list_id: shoppingLists.value[0].id
      })
    }
  } catch (error) {
    console.error('Error fetching shopping lists:', error)
  } finally {
    loading.value = false
  }
})

const validateForm = () => {
  const isValid = !!props.modelValue.name?.trim() && 
                  !!props.modelValue.shopping_list_id
  emit('validation', isValid)
}

// Validate when model changes
watch(() => props.modelValue, validateForm, { immediate: true, deep: true })
</script>