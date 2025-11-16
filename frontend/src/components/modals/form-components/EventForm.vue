<!-- components/modals/form-components/EventForm.vue -->
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
        placeholder="Event titel"
        @input="validateForm"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Kalender *
      </label>
      <select 
        v-model="modelValue.calendar_id"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        @change="validateForm"
      >
        <option value="" disabled>Selecteer een kalender</option>
        <option 
          v-for="calendar in calendars" 
          :key="calendar.id" 
          :value="calendar.id"
        >
          {{ calendar.name }}
        </option>
      </select>
      <p v-if="calendars.length === 0" class="text-sm text-gray-500 mt-1">
        Geen kalenders beschikbaar
      </p>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Beschrijving
      </label>
      <textarea 
        v-model="modelValue.description"
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Event beschrijving..."
      ></textarea>
    </div>
    
    <div class="grid grid-cols-1 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Starttijd *
        </label>
        <input 
          v-model="modelValue.start"
          type="datetime-local"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          @change="validateForm"
        >
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Eindtijd
        </label>
        <input 
          v-model="modelValue.end"
          type="datetime-local"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        >
      </div>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Locatie
      </label>
      <input 
        v-model="modelValue.location"
        type="text" 
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="Waar vindt het plaats?"
      >
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useCalendarStore } from '../../../stores/calendar'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue', 'validation'])

const calendarStore = useCalendarStore()
const calendars = ref([])
const loading = ref(false)

// Fetch calendars when component mounts
onMounted(async () => {
  try {
    loading.value = true
    await calendarStore.fetchCalendars()
    console.log('Fetched calendars:', calendarStore.calendars)
    calendars.value = calendarStore.calendars
    
    // Auto-select first calendar if none selected
    if (calendars.value.length > 0 && !props.modelValue.calendar_id) {
      emit('update:modelValue', {
        ...props.modelValue,
        calendar_id: calendars.value[0].id
      })
    }
  } catch (error) {
    console.error('Error fetching calendars:', error)
  } finally {
    loading.value = false
  }
})

const validateForm = () => {
  const isValid = !!props.modelValue.title?.trim() && 
                  !!props.modelValue.start && 
                  !!props.modelValue.calendar_id
  emit('validation', isValid)
}

// Validate when model changes
watch(() => props.modelValue, validateForm, { immediate: true, deep: true })
</script>