<template>
  <header class="flex flex-none items-center justify-between border-b border-gray-200 px-6 py-4">
    <div>
      <h1 class="text-base font-semibold text-gray-900">
        <time :datetime="dateFormat" class="sm:hidden">
          {{ displayDate }}
        </time>
        <time :datetime="dateFormat" class="hidden sm:inline">
          {{ displayDate }}
        </time>
      </h1>
      <p class="mt-1 text-sm text-gray-500">{{ calendarStore.dagNaam }}</p>
    </div>

    <div class="flex items-center">
      <!-- Navigatieknoppen -->
      <div class="relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
        <button
            @click="navigate(-1)"
            type="button"
            class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50"
        >
          <span class="sr-only">Previous {{ navigationUnit }}</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
          </svg>
        </button>

        <button
            @click="calendarStore.today()"
            type="button"
            class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block"
        >
          Today
        </button>

        <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden"></span>

        <button
            @click="navigate(1)"
            type="button"
            class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50"
        >
          <span class="sr-only">Next {{ navigationUnit }}</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>

      <!-- View selector dropdown -->
      <div class="hidden md:ml-4 md:flex md:items-center">
        <div class="relative">
          <button
              type="button"
              class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
              id="menu-button"
              aria-expanded="false"
              aria-haspopup="true"
              @click="toggleDropdown"
          >
            {{ currentView }} view
            <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
              <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
            </svg>
          </button>

          <div
              v-if="isDropdownOpen"
              class="absolute right-0 z-10 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
              role="menu"
              aria-orientation="vertical"
              aria-labelledby="menu-button"
              tabindex="-1"
          >
            <div class="py-1" role="none">
              <a
                  v-for="view in views"
                  :key="view"
                  @click="changeView(view)"
                  class="block px-4 py-2 text-sm text-gray-700 cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
              >
                {{ view }} view
              </a>
            </div>
          </div>
        </div>

        <div class="ml-6 h-6 w-px bg-gray-300"></div>
        <button
            type="button"
            class="ml-6 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
            @click="openModal(null, false)"
        >
          Add event
        </button>
      </div>

      <!-- Mobile menu -->
      <div class="relative ml-6 md:hidden">
        <button
            type="button"
            class="-mx-2 flex items-center rounded-full border border-transparent p-2 text-gray-400 hover:text-gray-500"
            id="menu-0-button"
            aria-expanded="false"
            aria-haspopup="true"
            @click="toggleMobileDropdown"
        >
          <span class="sr-only">Open menu</span>
          <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
            <path d="M3 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM8.5 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM15.5 8.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
          </svg>
        </button>

        <div
            v-if="isMobileDropdownOpen"
            class="absolute right-0 z-10 mt-3 w-36 origin-top-right divide-y divide-gray-100 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="menu-0-button"
            tabindex="-1"
        >
          <div class="py-1" role="none">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-0-item-0" @click="openModal(null, false)">Create event</a>
          </div>
          <div class="py-1" role="none">
            <a href="#" @click="calendarStore.today()" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="menu-0-item-1">Go to today</a>
          </div>
          <div class="py-1" role="none">
            <a
                v-for="view in views"
                :key="view"
                @click="changeView(view)"
                class="block px-4 py-2 text-sm text-gray-700"
                role="menuitem"
                tabindex="-1"
            >
              {{ view }} view
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
  <CalendarModal
      :isOpen="isOpen"
      :isEdit="isEdit"
      :event="selectedEvent"
      :categories="categories"
      :calendar-id="currentCalendarId"
      @closeModal="closeModal"
      @onEventCreated="handleEventCreated">
  </CalendarModal>
</template>

<script setup>
import {useCalendarStore} from '../../stores/calendar';
import {computed, ref, defineEmits} from 'vue';
import CalendarModal from "./CalendarModal.vue";

const calendarStore = useCalendarStore();
const isDropdownOpen = ref(false);
const isMobileDropdownOpen = ref(false);
const currentView = ref('Day');
const views = ['Day', 'Week', 'Month'];

const navigationUnit = computed(() => currentView.value.toLowerCase());
const increment = computed(() => {
  switch (currentView.value) {
    case 'Week':
      return 7;
    case 'Month':
      return 30;
    default:
      return 1;
  }
});

const dateFormat = computed(() => {
  return `${calendarStore.maandNaam} ${calendarStore.dagNummer}, ${calendarStore.huidigJaar}`;
});

const displayDate = computed(() => {
  if (currentView.value === 'Month') {
    return `${calendarStore.maandNaam} ${calendarStore.huidigJaar}`;
  }
  return `${calendarStore.maandNaam} ${calendarStore.dagNummer}, ${calendarStore.huidigJaar}`;
});

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
  isMobileDropdownOpen.value = false;
};

const toggleMobileDropdown = () => {
  isMobileDropdownOpen.value = !isMobileDropdownOpen.value;
  isDropdownOpen.value = false;
};

const emit = defineEmits(['viewChanged']);
const changeView = (view) => {
  currentView.value = view;
  isDropdownOpen.value = false;
  isMobileDropdownOpen.value = false;
  console.log(view);
  emit('viewChanged', view + 'ViewCalendar');
  // Emit event or update calendarStore as needed
};

const navigate = (direction) => {
  const offset = direction * increment.value;
  if (currentView.value === 'Day') {
    calendarStore.changeDay(offset);
  } else if (currentView.value === 'Week') {
    calendarStore.changeWeek(direction > 0 ? 1 : -1);
  } else if (currentView.value === 'Month') {
    calendarStore.changeMonth(direction > 0 ? 1 : -1);
  }
};

/*Modal integration with change edit or add*/
const isOpen = ref(false)
const isEdit = ref(false);
const selectedTask = ref(null)

function openModal(task, edit) {
  console.log("is edit")
  isOpen.value = true;
  isEdit.value = edit;
  selectedTask.value = task;
}

function closeModal() {
  console.log("close modal")
  isOpen.value = false
  isEdit.value = false;
  selectedTask.value = null
}

</script>