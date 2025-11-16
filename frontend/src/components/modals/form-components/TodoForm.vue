<!-- components/modals/form-components/TodoForm.vue -->
<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Titel *
      </label>
      <input 
        v-model="modelValue.title"
        type="text" 
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Wat moet er gedaan worden?"
        @input="validateForm"
      >
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Beschrijving
      </label>
      <textarea 
        v-model="modelValue.description"
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Optionele beschrijving..."
      ></textarea>
    </div>
    
    <div class="grid grid-cols-1 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Datum & Tijd
        </label>
        <input 
          v-model="modelValue.date"
          type="datetime-local"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        >
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Herinnering (optioneel)
        </label>
        <input 
          v-model="modelValue.notify_at"
          type="datetime-local"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue', 'validation'])

const validateForm = () => {
  const isValid = !!props.modelValue.title?.trim() && !!props.modelValue.date
  emit('validation', isValid)
}

watch(() => props.modelValue, validateForm, { immediate: true, deep: true })
</script>