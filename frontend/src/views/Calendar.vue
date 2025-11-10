<!-- views/Calendar.vue -->

<template>
  <!-- <NavBar></NavBar>
  <SideBar></SideBar> -->
<!--  <CalendarBigView ></CalendarBigView>-->
<!--  <header class="flex flex-none items-center justify-between border-b border-gray-200 px-6 py-4">
    <div>
      <h1 class="text-base font-semibold text-gray-900">
        <time datetime="{{ store.maandNaam }} {{ store.dagNummer }}, {{ store.huidigJaar }}" class="sm:hidden">
          {{ store.maandNaam }} {{ store.dagNummer }}, {{ store.huidigJaar }}
        </time>
        <time datetime="{{ store.maandNaam }} {{ store.dagNummer }}, {{ store.huidigJaar }}" class="hidden sm:inline">
          {{ store.maandNaam }} {{ store.dagNummer }}, {{ store.huidigJaar }}
        </time>
      </h1>
      <p class="mt-1 text-sm text-gray-500">{{ store.dagNaam }}</p>
    </div>
    <div class="flex items-center">
      <div class="relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
        <button @click="store.changeDay(-1)" type="button"
                class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50">
          <span class="sr-only">Previous day</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd"
                  d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
        <button @click="store.today()" type="button"
                class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block">
          Today
        </button>
        <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden"></span>
        <button @click="store.changeDay(1)" type="button"
                class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50">
          <span class="sr-only">Next day</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd"
                  d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
      <div class="hidden md:ml-4 md:flex md:items-center">
        <div class="relative">
          <button type="button"
                  class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                  id="menu-button" aria-expanded="false" aria-haspopup="true" @click="toggleDropdown">
            Day view
            <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                 data-slot="icon">
              <path fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                    clip-rule="evenodd"/>
            </svg>
          </button>
          &lt;!&ndash;
            Dropdown menu, show/hide based on menu state.

            Entering: "transition ease-out duration-100"
              From: "transform opacity-0 scale-95"
              To: "transform opacity-100 scale-100"
            Leaving: "transition ease-in duration-75"
              From: "transform opacity-100 scale-100"
              To: "transform opacity-0 scale-95"
          &ndash;&gt;
          <div
              class="absolute right-0 z-10 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
              role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
              v-if="isDropdownOpen">
            <div class="py-1" role="none">
              <a @click="changeView('DayViewCalendar')" class="block px-4 py-2 text-sm text-gray-700 cursor-pointer"
                 role="menuitem" tabindex="-1" id="menu-item-0">Day view</a>
              <a @click="changeView('WeekViewCalendar')" class="block px-4 py-2 text-sm text-gray-700 cursor-pointer"
                 role="menuitem" tabindex="-1" id="menu-item-1">Week view</a>
              <a @click="changeView('MonthViewCalendar')" class="block px-4 py-2 text-sm text-gray-700 cursor-pointer"
                 role="menuitem" tabindex="-1" id="menu-item-2">Month view</a>
&lt;!&ndash;              <a @click="navigateTo('year-view')" class="block px-4 py-2 text-sm text-gray-700 cursor-pointer"
                 role="menuitem" tabindex="-1" id="menu-item-3">Year view</a>&ndash;&gt;
            </div>
          </div>
        </div>
        <div class="ml-6 h-6 w-px bg-gray-300"></div>
        <button type="button"
                class="ml-6 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                @click="openModal(null, false)">Add event
        </button>
      </div>
      <div class="relative ml-6 md:hidden">
        <button type="button"
                class="-mx-2 flex items-center rounded-full border border-transparent p-2 text-gray-400 hover:text-gray-500"
                id="menu-0-button" aria-expanded="false" aria-haspopup="true">
          <span class="sr-only">Open menu</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path
                d="M3 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM8.5 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM15.5 8.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
          </svg>
        </button>
        &lt;!&ndash;
          Dropdown menu, show/hide based on menu state.

          Entering: "transition ease-out duration-100"
            From: "transform opacity-0 scale-95"
            To: "transform opacity-100 scale-100"
          Leaving: "transition ease-in duration-75"
            From: "transform opacity-100 scale-100"
            To: "transform opacity-0 scale-95"
        &ndash;&gt;
        <div
            class="absolute right-0 z-10 mt-3 w-36 origin-top-right divide-y divide-gray-100 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none hidden md:block"
            role="menu" aria-orientation="vertical" aria-labelledby="menu-0-button" tabindex="-1">
          <div class="py-1" role="none">
            &lt;!&ndash; Active: "bg-gray-100 text-gray-900 outline-none", Not Active: "text-gray-700" &ndash;&gt;
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-0">Create event</a>
          </div>
          <div class="py-1" role="none">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-1">Go to today</a>
          </div>
          <div class="py-1" role="none">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-2">Day view</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-3">Week view</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-4">Month view</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
               id="menu-0-item-5">Year view</a>
          </div>
        </div>
      </div>
    </div>
  </header>-->
  <DateNavigator mode="day" @viewChanged="changeView"></DateNavigator>
  <!-- Toon alleen de actieve view -->
  <component :is="currentView"></component>
<!--  <CalendarModal
      :isOpen="isOpen"
      :isEdit="isEdit"
      :event="selectedEvent"
      :categories="categories"
      :calendar-id="currentCalendarId"
      @closeModal="closeModal"
      @onEventCreated="handleEventCreated">
  </CalendarModal>-->
</template>

<script>
// import SideBar from "../components/calendar/SideBar.vue";
// import NavBar from "@/components/calendar/NavBar.vue";
/*import CalendarBigView from "@/components/Blocks/CalendarBigView.vue";*/
import DayViewCalendar from "../components/calendar/DayViewCalendar.vue";
import WeekViewCalendar from "../components/calendar/WeekViewCalendar.vue";
import MonthViewCalendar from "../components/calendar/MonthViewCalendar.vue";
import {useCalendarStore} from "../stores/calendar";
import {ref} from "vue";
import CalendarModal from "../components/calendar/CalendarModal.vue";
import DateNavigator from "../components/calendar/DateNavigator.vue";

export default{
  name: 'CalendarPage',
  components: {
    DateNavigator,
    CalendarModal, MonthViewCalendar, WeekViewCalendar, DayViewCalendar /*CalendarBigView*/},
  setup () {
    const store = useCalendarStore();
    const isDropdownOpen = ref(false);
    const currentView = ref("DayViewCalendar");

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value;
    };

    const changeView = (view) => {
      currentView.value = view;
    };


    return { store, toggleDropdown, isDropdownOpen, changeView, currentView, DayViewCalendar, WeekViewCalendar, MonthViewCalendar };
  }
};
</script>

<!-- <template>
  <div class="p-6 space-y-8 animate-fade-in">
    <!-- Header 
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kalender</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Plan je activiteiten en events</p>
      </div>
      <button class="bg-blue-500 text-white px-6 py-3 rounded-xl hover:bg-blue-600 transition-colors flex items-center space-x-2">
        <span>📅</span>
        <span>Nieuw Event</span>
      </button>
    </div>

    <!-- Calendar Navigation 
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
          <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
            ◀️
          </button>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ currentMonth }}</h2>
          <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
            ▶️
          </button>
        </div>
        <div class="flex space-x-2">
          <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Maand
          </button>
          <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            Week
          </button>
          <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Dag
          </button>
        </div>
      </div>

      <!-- Calendar Grid 
      <div class="grid grid-cols-7 gap-1">
        <!-- Week days 
        <div v-for="day in weekDays" :key="day" 
          class="p-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
          {{ day }}
        </div>
        
        <!-- Calendar days 
        <div v-for="day in calendarDays" :key="day.date" 
          class="min-h-24 p-2 border border-gray-100 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          :class="{ 'bg-blue-50 dark:bg-blue-900/20': day.isToday }">
          <div class="flex justify-between items-start mb-1">
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ day.date }}</span>
            <span v-if="day.isToday" class="w-2 h-2 bg-blue-500 rounded-full"></span>
          </div>
          <div class="space-y-1">
            <div v-for="event in day.events" :key="event.id" 
              class="text-xs p-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 truncate">
              {{ event.time }} {{ event.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upcoming Events 
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Komende Events</h2>
        <EventList :events="upcomingEvents" />
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Nieuwe Event</h2>
        <EventForm />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import EventList from '../components/calendar/EventList.vue'
import EventForm from '../components/calendar/EventForm.vue'

const weekDays = ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za']

const currentMonth = computed(() => {
  return new Date().toLocaleDateString('nl-NL', { month: 'long', year: 'numeric' })
})

const calendarDays = computed(() => {
  const days = []
  const today = new Date()
  const firstDay = new Date(today.getFullYear(), today.getMonth(), 1)
  const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0)
  
  // Add empty days for start of month
  for (let i = 0; i < firstDay.getDay(); i++) {
    days.push({ date: '', events: [] })
  }
  
  // Add actual days
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(today.getFullYear(), today.getMonth(), i)
    const isToday = date.toDateString() === today.toDateString()
    
    days.push({
      date: i,
      isToday,
      events: i % 3 === 0 ? [
        { id: 1, title: 'Meeting', time: '10:00' },
        { id: 2, title: 'Lunch', time: '13:00' }
      ] : []
    })
  }
  
  return days
})

const upcomingEvents = ref([
  { id: 1, title: 'Team Meeting', date: '2024-01-15', time: '10:00', type: 'work' },
  { id: 2, title: 'Lunch met familie', date: '2024-01-16', time: '13:00', type: 'personal' },
  { id: 3, title: 'Dokters afspraak', date: '2024-01-17', time: '15:30', type: 'health' }
])
</script> -->
<!-- <template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Calendar</h1>
      <div class="flex space-x-4">
        <select v-model="selectedCalendar" class="px-3 py-2 border rounded-md">
          <option value="">All Calendars</option>
          <option v-for="calendar in calendars" :key="calendar.id" :value="calendar.id">
            {{ calendar.name }}
          </option>
        </select>
        <button @click="showCreateCalendarModal = true" class="bg-blue-600 text-white px-4 py-2 rounded">
          New Calendar
        </button>
        <button @click="showCreateEventModal = true" class="bg-blue-600 text-white px-4 py-2 rounded">
          New Event
        </button>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="grid grid-cols-7 gap-2 mb-4">
        <div v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="day" 
             class="text-center font-semibold py-2">
          {{ day }}
        </div>
      </div>
      <div class="grid grid-cols-7 gap-2">
        <div v-for="day in 31" :key="day" class="border p-2 min-h-24">
          <div class="font-semibold">{{ day }}</div>
          <div v-for="event in getEventsForDay(day)" :key="event.id" 
               class="text-xs bg-blue-100 text-blue-800 p-1 rounded mb-1">
            {{ event.title }}
          </div>
        </div>
      </div>
    </div>

    <!-- Create Event Modal 
    <div v-if="showCreateEventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Create New Event</h3>
        <div class="space-y-3">
          <input v-model="newEvent.title" placeholder="Event title" class="w-full px-3 py-2 border rounded-md">
          <select v-model="newEvent.calendar_id" class="w-full px-3 py-2 border rounded-md">
            <option v-for="calendar in calendars" :key="calendar.id" :value="calendar.id">
              {{ calendar.name }}
            </option>
          </select>
          <input v-model="newEvent.start" type="datetime-local" class="w-full px-3 py-2 border rounded-md">
          <input v-model="newEvent.end" type="datetime-local" class="w-full px-3 py-2 border rounded-md">
          <select v-model="newEvent.owner_type" class="w-full px-3 py-2 border rounded-md">
            <option value="user">Personal</option>
            <option value="family">Family</option>
          </select>
          <select v-if="newEvent.owner_type === 'family'" v-model="newEvent.family_id" class="w-full px-3 py-2 border rounded-md">
            <option v-for="family in families" :key="family.id" :value="family.id">
              {{ family.name }}
            </option>
          </select>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="showCreateEventModal = false" class="px-4 py-2 text-gray-600">Cancel</button>
          <button @click="createEvent" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
        </div>
      </div>
    </div>

    <!-- Create Calendar Modal 
    <div v-if="showCreateCalendarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Create New Calendar</h3>
        <div class="space-y-3">
          <input v-model="newCalendar.name" placeholder="Calendar title" class="w-full px-3 py-2 border rounded-md">
          <select v-model="newCalendar.owner_type" class="w-full px-3 py-2 border rounded-md">
            <option value="user">Personal</option>
            <option value="family">Family</option>
          </select>
          <select v-if="newCalendar.owner_type === 'family'" v-model="newCalendar.family_id" class="w-full px-3 py-2 border rounded-md">
            <option v-for="family in families" :key="family.id" :value="family.id">
              {{ family.name }}
            </option>
          </select>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="showCreateCalendarModal = false" class="px-4 py-2 text-gray-600">Cancel</button>
          <button @click="createCalendar" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted, computed, reactive } from 'vue'
import { useCalendarStore } from '../stores/calendar'
import { useFamilyStore } from '../stores/family'

export default {
  name: 'Calendar',
  setup() {
    const calendarStore = useCalendarStore()
    const familyStore = useFamilyStore()
    const showCreateEventModal = ref(false)
    const showCreateCalendarModal = ref(false)
    const selectedCalendar = ref('')
    
    const newEvent = reactive({
      title: '',
      calendar_id: '',
      start: '',
      end: '',
      owner_type: 'user',
      family_id: ''
    })

    const calendars = computed(() => calendarStore.calendars)
    const allEvents = computed(() => calendarStore.events)
    const families = computed(() => familyStore.families)

    // Filter events based on selected calendar
    const filteredEvents = computed(() => {
      if (!selectedCalendar.value) {
        // If no calendar selected, show all events
        return allEvents.value
      }
      // Filter events by selected calendar
      return allEvents.value.filter(event => event.calendar_id === parseInt(selectedCalendar.value))
    })

    onMounted(async () => {
      await calendarStore.fetchCalendars()
      await calendarStore.fetchEvents()
      await familyStore.fetchFamilies()
    })

    const getEventsForDay = (day) => {
      return filteredEvents.value.filter(event => {
        const eventDate = new Date(event.start)
        return eventDate.getDate() === day
      })
    }

    const createEvent = async () => {
      try {
        await calendarStore.createEvent(newEvent)
        showCreateEventModal.value = false
        Object.assign(newEvent, {
          title: '',
          calendar_id: '',
          start: '',
          end: '',
          owner_type: 'user',
          family_id: ''
        })
      } catch (error) {
        alert(error.message || 'Failed to create event')
      }
    }

    const newCalendar = reactive({
      name: '',
      owner_type: 'user',
      family_id: '',
      color: '#3b82f6',
      is_public: false
    }) 

    const createCalendar = async () => {
      try {
        await calendarStore.createCalendar(newCalendar)
        showCreateCalendarModal.value = false
        Object.assign(newCalendar, {
          name: '',
          owner_type: 'user',
          family_id: ''
        })
      } catch (error) {
        alert(error.message || 'Failed to create calendar')
      }
    }

    return {
      calendars,
      events: filteredEvents, // Use filtered events instead of all events
      families,
      selectedCalendar,
      showCreateEventModal,
      showCreateCalendarModal,
      newEvent,
      newCalendar,
      getEventsForDay,
      createEvent,
      createCalendar
    }
  }
}
</script> -->