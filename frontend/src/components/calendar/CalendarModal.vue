<template>
  <!-- Create Event Modal -->
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-96 max-h-[90vh] overflow-y-auto">
      <h3 class="text-xl font-semibold mb-4">{{ isEdit ? "Event Bewerken" : "Nieuw Event Aanmaken" }}</h3>
      
      <!-- Error message -->
      <div v-if="error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
        {{ error }}
      </div>
      
      <div class="space-y-3">
        <input v-model="newEvent.title" placeholder="Event titel" class="w-full px-3 py-2 border rounded-md">
        <select v-model="newEvent.calendar_id" class="w-full px-3 py-2 border rounded-md">
          <option value="">Selecteer een calendar</option>
          <option v-for="calendar in userCalendars" :key="calendar.id" :value="calendar.id">
            {{ calendar.name }}
          </option>
        </select>
        <input v-model="newEvent.start" type="datetime-local" class="w-full px-3 py-2 border rounded-md">
        <input v-model="newEvent.end" type="datetime-local" class="w-full px-3 py-2 border rounded-md">
        <select v-model="newEvent.owner_type" class="w-full px-3 py-2 border rounded-md">
          <option value="user">Persoonlijk</option>
          <option value="family">Familie</option>
        </select>
        <select v-if="newEvent.owner_type === 'family'" v-model="newEvent.family_id" class="w-full px-3 py-2 border rounded-md">
          <option value="">Selecteer een familie</option>
          <option v-for="family in families" :key="family.id" :value="family.id">
            {{ family.name }}
          </option>
        </select>
        <!-- Extra velden voor de volledigheid -->
        <textarea v-model="newEvent.description" placeholder="Beschrijving" class="w-full px-3 py-2 border rounded-md" rows="3"></textarea>
        <input v-model="newEvent.location" placeholder="Locatie" class="w-full px-3 py-2 border rounded-md">
        <div class="flex items-center">
          <input v-model="newEvent.allDay" type="checkbox" id="allDay" class="mr-2">
          <label for="allDay" class="text-sm">Hele dag</label>
        </div>
      </div>
      <div class="flex justify-end space-x-2 mt-4">
        <button @click="closeModal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Annuleren</button>
        <button @click="isEdit ? updateEvent() : addNewEvent()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          {{ isEdit ? "Opslaan" : "Aanmaken" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch, onMounted } from "vue";
import { useCalendarStore } from "../../stores/calendar";
import { useEventStore } from "../../stores/event";

// Props
const props = defineProps({
  event: Object,
  isOpen: {
    type: Boolean,
    default: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  calendarId: {
    type: String,
    default: "default",
  },
  calendars: {
    type: Array,
    default: () => [],
  },
  families: {
    type: Array,
    default: () => [],
  }
});

// Emits
const emit = defineEmits(["onEventCreated", "closeModal"]);

// Stores
const calendarStore = useCalendarStore();
const eventStore = useEventStore();

// Reactive data voor de nieuwe modal structuur
const newEvent = reactive({
  title: "",
  calendar_id: "",
  start: "",
  end: "",
  owner_type: "user",
  family_id: "",
  description: "",
  location: "",
  allDay: false
});

const error = ref("");
const loading = ref(false);
const userCalendars = ref([])


// Watchers
watch(() => props.event, (newEventData) => {
  if (newEventData) {
    updateNewEvent(newEventData);
  }
}, { immediate: true });

watch(() => props.isEdit, () => {
  if (props.event) {
    updateNewEvent(props.event);
  }
}, { immediate: true });

//Haal alle calendars op van deze gebruiker
onMounted(async () => {
  try {
    await calendarStore.fetchCalendars()
    userCalendars.value = calendarStore.calendars
    console.log("hu?", calendarStore.calendars)
  } catch (error) {
    console.error('Failed to load calendars:', error)
  }
})


// Functions
function updateNewEvent(event) {
  newEvent.title = event.title || "";
  newEvent.calendar_id = event.calendar_id || props.calendars[0]?.id || "";
  newEvent.start = event.startDateTime || event.start || "";
  newEvent.end = event.endDateTime || event.end || "";
  newEvent.owner_type = event.owner_type || "user";
  newEvent.family_id = event.family_id || "";
  newEvent.description = event.description || "";
  newEvent.location = event.location || "";
  newEvent.allDay = event.allDay || false;
}

async function addNewEvent() {
  if (!validateForm()) return;
  
  loading.value = true;
  error.value = "";

  try {
    // Gebruik de eventStore om het event aan te maken
    const eventData = {
      title: newEvent.title,
      description: newEvent.description,
      location: newEvent.location,
      start: newEvent.start,
      end: newEvent.end,
      //allDay: newEvent.allDay,
      calendar_id: newEvent.calendar_id || props.calendarId,
      owner_type: newEvent.owner_type,
      family_id: newEvent.family_id
    };

    await eventStore.createEvent(eventData);
    
    resetForm();
    emit("onEventCreated");
    closeModal();
  } catch (err) {
    error.value = "Er is een fout opgetreden bij het aanmaken van het event";
    console.error("Error creating event:", err);
  } finally {
    loading.value = false;
  }
}

async function updateEvent() {
  if (!validateForm()) return;

  loading.value = true;
  error.value = "";

  try {
    const updatePayload = {
      eventId: props.event.id,
      title: newEvent.title,
      description: newEvent.description,
      location: newEvent.location,
      startDateTime: newEvent.start,
      endDateTime: newEvent.end,
      allDay: newEvent.allDay,
      calendarId: newEvent.calendar_id || props.calendarId,
      owner_type: newEvent.owner_type,
      family_id: newEvent.family_id
    };

    await eventStore.updateEvent(updatePayload);
    emit("onEventCreated");
    closeModal();
  } catch (err) {
    error.value = "Er is een fout opgetreden bij het bijwerken van het event";
    console.error("Error updating event:", err);
  } finally {
    loading.value = false;
  }
}

function validateForm() {
  if (!newEvent.title) {
    error.value = "Titel is verplicht";
    return false;
  }

  if (!newEvent.start) {
    error.value = "Startdatum is verplicht";
    return false;
  }

  if (!newEvent.end && !newEvent.allDay) {
    error.value = "Einddatum is verplicht als het geen hele dag event is";
    return false;
  }

  // Controleer of einddatum na startdatum ligt
  if (newEvent.end && new Date(newEvent.end) <= new Date(newEvent.start)) {
    error.value = "Einddatum moet na startdatum liggen";
    return false;
  }

  if (newEvent.owner_type === 'family' && !newEvent.family_id) {
    error.value = "Selecteer een familie voor familie events";
    return false;
  }

  error.value = "";
  return true;
}

function resetForm() {
  Object.assign(newEvent, {
    title: "",
    calendar_id: "",
    start: "",
    end: "",
    owner_type: "user",
    family_id: "",
    description: "",
    location: "",
    allDay: false
  });
  error.value = "";
}

function closeModal() {
  resetForm();
  emit("closeModal");
}
</script>