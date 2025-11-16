<!-- components/modals/DashboardModal.vue -->
<!-- components/modals/DashboardModal.vue -->
<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header -->
            <div
                class="flex-shrink-0 flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ modalConfig.title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ modalConfig.description }}</p>
                </div>
                <button @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    ✕
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-6">
                    <component :is="modalComponent" v-model="formData" @validation="updateValidation" />
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex-shrink-0 flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <button @click="$emit('close')"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    Annuleren
                </button>
                <button @click="handleSubmit" :disabled="!isFormValid || loading"
                    class="px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center space-x-2">
                    <span v-if="loading"
                        class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span>{{ loading ? 'Bezig...' : modalConfig.submitText || 'Opslaan' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Import form components
import TodoForm from '../modals/form-components/TodoForm.vue'
import ShoppingForm from '../modals/form-components/ShoppingForm.vue'
import EventForm from '../modals/form-components/EventForm.vue'
import NoteForm from '../modals/form-components/NoteForm.vue'

// Import stores
import { useTodoStore } from '../../stores/todo'
import { useShoppingStore } from '../../stores/shopping'
import { useEventStore } from '../../stores/event'
import { useNoteStore } from '../../stores/note'

const props = defineProps({
    modalType: {
        type: String,
        required: true,
        validator: (value) => ['todo', 'shopping', 'event', 'note'].includes(value)
    }
})

const emit = defineEmits(['close', 'saved'])

// Stores
const todoStore = useTodoStore()
const shoppingStore = useShoppingStore()
const eventStore = useEventStore()
const noteStore = useNoteStore()

const loading = ref(false)
const isFormValid = ref(false)

// Modal configurations
const modalConfigs = {
    todo: {
        title: 'Nieuwe Taak',
        description: 'Voeg een nieuwe taak toe aan je todo lijst',
        submitText: 'Taak Opslaan',
        component: TodoForm,
        defaultData: {
            title: '',
            description: '',
            date: new Date().toISOString().slice(0, 16),
            notify_at: null
        }
    },
    shopping: {
        title: 'Nieuwe Boodschap',
        description: 'Voeg iets toe aan je boodschappenlijst',
        submitText: 'Toevoegen',
        component: ShoppingForm,
        defaultData: {
            name: '',
            quantity: 1,
            shopping_list_id: null // ← Nu required
        }
    },
    event: {
        title: 'Nieuw Event',
        description: 'Plan een nieuw event in je agenda',
        submitText: 'Event Plannen',
        component: EventForm,
        defaultData: {
            title: '',
            description: '',
            start: new Date().toISOString().slice(0, 16),
            end: new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16),
            location: '',
            calendar_id: null
        }
    },
    note: {
        title: 'Nieuwe Notitie',
        description: 'Maak een snelle notitie',
        submitText: 'Notitie Opslaan',
        component: NoteForm,
        defaultData: {
            title: '',
            content: '',
            category: 'general'
        }
    }
}

const formData = ref({})

const modalConfig = computed(() => modalConfigs[props.modalType])
const modalComponent = computed(() => modalConfig.value.component)

// Initialize form data when modal type changes
watch(() => props.modalType, (newType) => {
    formData.value = { ...modalConfigs[newType].defaultData }
    isFormValid.value = false
}, { immediate: true })

const updateValidation = (isValid) => {
    isFormValid.value = isValid
}

const handleSubmit = async () => {
    if (!isFormValid.value) return

    loading.value = true

    try {
        let result

        switch (props.modalType) {
            case 'todo':
                result = await todoStore.createTodo(formData.value)
                break

            case 'shopping':
                // Voor shopping moeten we eerst de default shopping list vinden/gebruiken
                if (!formData.value.shopping_list_id) {
                    // Gebruik de eerste lijst of maak een default aan
                    if (shoppingStore.shoppingLists.length > 0) {
                        formData.value.shopping_list_id = shoppingStore.shoppingLists[0].id
                    } else {
                        throw new Error('Geen shopping lijst beschikbaar')
                    }
                }
                result = await shoppingStore.addListItem(formData.value.shopping_list_id, formData.value)
                break

            case 'event':
                // Voor events moeten we een calendar_id hebben
                if (!formData.value.calendar_id) {
                    // Hier zou je een default calendar kunnen gebruiken
                    formData.value.calendar_id = 1 // Default calendar ID - aanpassen naar jouw logica
                }
                result = await eventStore.createEvent(formData.value)
                break

            case 'note':
                result = await noteStore.createNote(formData.value)
                break

            default:
                throw new Error(`Onbekend modal type: ${props.modalType}`)
        }

        emit('saved', {
            type: props.modalType,
            data: result
        })

    } catch (error) {
        console.error(`Fout bij opslaan van ${props.modalType}:`, error)
        // Hier kun je een error message tonen aan de gebruiker
    } finally {
        loading.value = false
    }
}
</script>