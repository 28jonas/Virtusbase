<!-- components/modals/form-components/NoteForm.vue -->
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
        placeholder="Notitie titel"
        @input="validateForm"
      >
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Inhoud
      </label>
      <textarea 
        v-model="modelValue.content"
        rows="5"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Schrijf je notitie hier..."
      ></textarea>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Categorie
      </label>
      <select 
        v-model="modelValue.category"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
      >
        <option value="general">Algemeen</option>
        <option value="personal">Persoonlijk</option>
        <option value="work">Werk</option>
        <option value="ideas">Ideeën</option>
        <option value="reminders">Herinneringen</option>
      </select>
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
  const isValid = !!props.modelValue.title?.trim()
  emit('validation', isValid)
}

watch(() => props.modelValue, validateForm, { immediate: true, deep: true })
</script>