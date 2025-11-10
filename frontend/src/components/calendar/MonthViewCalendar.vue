<template>
  <div class="p-6">
    <!-- Header met maand navigatie -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="flex-auto text-lg font-semibold text-gray-900">{{ store.maandNaam }} {{ store.huidigJaar }}</h2>
      <div class="flex items-center space-x-2">
        <button @click="store.changeMonth(-1)" type="button"
                class="flex items-center justify-center p-2 text-gray-400 hover:text-gray-500 rounded-lg hover:bg-gray-100">
          <span class="sr-only">Vorige maand</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd"
                  d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
        <button @click="store.changeMonth(1)" type="button"
                class="flex items-center justify-center p-2 text-gray-400 hover:text-gray-500 rounded-lg hover:bg-gray-100">
          <span class="sr-only">Volgende maand</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd"
                  d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Dagen van de week headers -->
    <div class="grid grid-cols-7 gap-1 mb-2 text-center text-sm font-medium text-gray-600">
      <div v-for="day in store.dagen" :key="day" class="py-2">{{ day }}</div>
    </div>

    <!-- Kalender dagen -->
    <div class="grid grid-cols-7 gap-1 text-sm">
      <div
        v-for="(day, index) in store.genummerdeDagen"
        :key="`${day.date}-${index}-${store.huidigeMaand}-${store.huidigJaar}`"
        :class="{
          'bg-purple-100': day.isToday,
          'text-gray-400': day.isPrevMonth || day.isNextMonth,
          'bg-blue-100 border-2 border-blue-500': store.geselecteerdeDag && store.geselecteerdeDag.date === day.date && store.geselecteerdeDag.isPrevMonth === day.isPrevMonth && store.geselecteerdeDag.isNextMonth === day.isNextMonth,
          'bg-white hover:bg-gray-50': !day.isToday && !(store.geselecteerdeDag && store.geselecteerdeDag.date === day.date)
        }"
        class="min-h-24 p-2 border rounded-lg cursor-pointer transition-colors"
        @click="selectDay(day)"
      >
        <!-- Datum nummer -->
        <div class="flex justify-between items-start mb-1">
          <span :class="{
            'font-semibold': day.isToday,
            'text-gray-900': !day.isPrevMonth && !day.isNextMonth,
            'text-purple-600': day.isToday
          }">
            {{ day.date }}
          </span>
          <span v-if="day.isToday" class="text-xs bg-purple-600 text-white px-1 rounded">
            Vandaag
          </span>
        </div>

        <!-- Events voor deze dag -->
        <div class="space-y-1 max-h-20 overflow-y-auto">
          <div
            v-for="event in getEventsForDay(day)"
            :key="event.id"
            class="text-xs p-1 rounded truncate"
            :style="{ backgroundColor: event.bgColor || '#3b82f6' }"
            :class="event.textColor || 'text-white'"
            @click.stop="openEvent(event)"
          >
            <div class="font-medium truncate">{{ event.title }}</div>
            <div v-if="event.location" class="truncate opacity-90">{{ event.location }}</div>
            <div class="opacity-80">
              {{ formatTime(event.start) }}
              <span v-if="isMultiDayEvent(event)">→</span>
            </div>
          </div>
          
          <!-- Meer events indicator -->
          <div 
            v-if="getEventsForDay(day).length > 3" 
            class="text-xs text-gray-500 text-center py-1"
          >
            + {{ getEventsForDay(day).length - 3 }} meer
          </div>
        </div>
      </div>
    </div>

    <!-- Geselecteerde dag events sectie -->
    <section v-if="selectedDayEvents.length > 0" class="mt-8">
      <h2 class="text-base font-semibold text-gray-900 mb-4">
        Agenda voor 
        <time :datetime="selectedDayDate">{{ selectedDayDate }}</time>
      </h2>
      <ol class="space-y-3">
        <li
          v-for="event in selectedDayEvents"
          :key="event.id"
          class="group flex items-center gap-x-4 rounded-xl px-4 py-3 bg-white border border-gray-200 hover:border-blue-300 transition-colors"
        >
          <!-- Event kleur indicator -->
          <div 
            class="size-3 rounded-full flex-none" 
            :style="{ backgroundColor: event.bgColor || '#3b82f6' }"
          ></div>
          
          <div class="flex-auto min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ event.title }}</p>
            <p v-if="event.location" class="text-sm text-gray-600 mt-0.5 truncate">
              📍 {{ event.location }}
            </p>
            <p class="text-sm text-gray-500 mt-0.5">
              <time :datetime="event.start">{{ formatEventDateTime(event.start) }}</time>
              <span v-if="event.end"> - <time :datetime="event.end">{{ formatEventDateTime(event.end) }}</time></span>
            </p>
            <p v-if="event.description" class="text-sm text-gray-600 mt-1 line-clamp-2">
              {{ event.description }}
            </p>
          </div>
          
          <!-- Event actions -->
          <div class="relative opacity-0 group-hover:opacity-100 transition-opacity">
            <button 
              type="button" 
              class="flex items-center rounded-lg p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100"
              @click.stop="openEventModal(event)"
            >
              <span class="sr-only">Opties</span>
              <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>
              </svg>
            </button>
          </div>
        </li>
      </ol>
    </section>

    <!-- Geen events placeholder -->
    <section v-else-if="store.geselecteerdeDag" class="mt-8 text-center py-8">
      <div class="text-gray-400">
        <svg class="mx-auto size-12 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
        <p class="text-sm font-medium text-gray-900">Geen events gepland</p>
        <p class="text-sm text-gray-500 mt-1">Voeg een event toe voor deze dag</p>
        <button 
          @click="openEventModal()"
          class="mt-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Event toevoegen
        </button>
      </div>
    </section>

    <!-- Event Modal -->
    <CalendarModal
      v-if="isModalOpen"
      :isOpen="isModalOpen"
      :isEdit="isEditModal"
      :event="selectedEvent"
      :categories="categories"
      :calendar-id="currentCalendarId"
      @closeModal="closeModal"
      @onEventCreated="handleEventCreated"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { useCalendarStore } from "../../stores/calendar";
import { useEventStore } from "../../stores/event";
import CalendarModal from "./CalendarModal.vue";

// Stores
const store = useCalendarStore();
const eventstore = useEventStore();

// Refs
const isModalOpen = ref(false);
const isEditModal = ref(false);
const selectedEvent = ref(null);
const categories = ref([]);
const currentCalendarId = ref(null);

// OnMounted
onMounted(async () => {
  await eventstore.fetchEvents();
});

// Computed properties
const selectedDayEvents = computed(() => {
  if (!store.geselecteerdeDag) return [];
  
  const selectedDate = new Date(
    store.geselecteerdeDag.isPrevMonth ? store.huidigJaar - (store.huidigeMaand === 0 ? 1 : 0) : 
    store.geselecteerdeDag.isNextMonth ? store.huidigJaar + (store.huidigeMaand === 11 ? 1 : 0) : 
    store.huidigJaar,
    store.geselecteerdeDag.isPrevMonth ? store.huidigeMaand - 1 : 
    store.geselecteerdeDag.isNextMonth ? store.huidigeMaand + 1 : 
    store.huidigeMaand,
    store.geselecteerdeDag.date
  );

  return eventstore.events.filter(event => {
    const eventDate = new Date(event.start);
    return eventDate.getDate() === selectedDate.getDate() &&
           eventDate.getMonth() === selectedDate.getMonth() &&
           eventDate.getFullYear() === selectedDate.getFullYear();
  });
});

const selectedDayDate = computed(() => {
  if (!store.geselecteerdeDag) return '';
  
  const date = new Date(
    store.geselecteerdeDag.isPrevMonth ? store.huidigJaar - (store.huidigeMaand === 0 ? 1 : 0) : 
    store.geselecteerdeDag.isNextMonth ? store.huidigJaar + (store.huidigeMaand === 11 ? 1 : 0) : 
    store.huidigJaar,
    store.geselecteerdeDag.isPrevMonth ? store.huidigeMaand - 1 : 
    store.geselecteerdeDag.isNextMonth ? store.huidigeMaand + 1 : 
    store.huidigeMaand,
    store.geselecteerdeDag.date
  );
  
  return date.toLocaleDateString('nl-NL', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  });
});

// Methods
const getEventsForDay = (day) => {
  const dayDate = new Date(
    day.isPrevMonth ? store.huidigJaar - (store.huidigeMaand === 0 ? 1 : 0) : 
    day.isNextMonth ? store.huidigJaar + (store.huidigeMaand === 11 ? 1 : 0) : 
    store.huidigJaar,
    day.isPrevMonth ? store.huidigeMaand - 1 : 
    day.isNextMonth ? store.huidigeMaand + 1 : 
    store.huidigeMaand,
    day.date
  );

  const events = eventstore.events.filter(event => {
    const eventDate = new Date(event.start);
    return eventDate.getDate() === dayDate.getDate() &&
           eventDate.getMonth() === dayDate.getMonth() &&
           eventDate.getFullYear() === dayDate.getFullYear();
  });

  // Toon maximaal 3 events in de kalender cel
  return events.slice(0, 3);
};

const selectDay = (day) => {
  store.selecteerDag(day);
};

const formatTime = (datetime) => {
  const date = new Date(datetime);
  if (isNaN(date.getTime())) return '--:--';
  return date.toLocaleTimeString('nl-NL', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });
};

const formatEventDateTime = (datetime) => {
  const date = new Date(datetime);
  if (isNaN(date.getTime())) return 'Ongeldige tijd';
  return date.toLocaleString('nl-NL', { 
    day: 'numeric',
    month: 'numeric',
    hour: '2-digit', 
    minute: '2-digit' 
  });
};

const isMultiDayEvent = (event) => {
  const start = new Date(event.start);
  const end = new Date(event.end);
  return end.getDate() !== start.getDate() || 
         end.getMonth() !== start.getMonth() || 
         end.getFullYear() !== start.getFullYear();
};

const openEvent = (event) => {
  selectedEvent.value = event;
  isEditModal.value = true;
  isModalOpen.value = true;
};

const openEventModal = (event = null) => {
  selectedEvent.value = event;
  isEditModal.value = !!event;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  isEditModal.value = false;
  selectedEvent.value = null;
};

const handleEventCreated = () => {
  closeModal();
  // Optioneel: events opnieuw ophalen
  eventstore.fetchEvents();
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.min-h-24 {
  min-height: 6rem;
}

.max-h-20 {
  max-height: 5rem;
}
</style>