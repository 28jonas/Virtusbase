<template>
  <div class="flex w-full flex-auto">
    <div class="grid flex-auto grid-cols-1 grid-rows-1 relative">
      <div class="flex flex-auto flex-col overflow-auto">
        <!--        <div
            class="sticky top-0 z-10 grid flex-none grid-cols-7 bg-white text-xs text-gray-500 shadow ring-1 ring-black/5 md:hidden">
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>W</span>
            &lt;!&ndash; Default: "text-gray-900", Selected: "bg-gray-900 text-white", Today (Not Selected): "text-indigo-600", Today (Selected): "bg-indigo-600 text-white" &ndash;&gt;
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-gray-900">{{ store.dagNummer -3 }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>T</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-indigo-600">{{ store.dagNummer -2 }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>F</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-gray-900">{{ store.dagNummer -1 }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>S</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full bg-gray-900 text-base font-semibold text-white">{{ store.dagNummer }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>S</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-gray-900">{{ Number(store.dagNummer) +1 }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>M</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-gray-900">{{ Number(store.dagNummer) +2 }}</span>
          </button>
          <button type="button" class="flex flex-col items-center pb-1.5 pt-3">
            <span>T</span>
            <span
                class="mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold text-gray-900">{{ Number(store.dagNummer) +3 }}</span>
          </button>
        </div>-->
        <div
          class="sticky top-0 z-10 grid flex-none grid-cols-7 bg-white text-xs text-gray-500 shadow ring-1 ring-black/5 md:hidden">
          <button v-for="(day, index) in calendarstore.getWeekDays()" :key="index" type="button"
            class="flex flex-col items-center pb-1.5 pt-3">
            <!-- Dagafkorting (W, T, F, etc.) -->
            <span>{{ ['Z', 'M', 'D', 'W', 'D', 'V', 'Z'][new Date(day.year, day.month, day.dayNumber).getDay()]
            }}</span>

            <!-- Dagnummer met dezelfde styling als voorheen -->
            <span :class="[
              'mt-3 flex size-8 items-center justify-center rounded-full text-base font-semibold',
              day.isToday ? 'text-indigo-600' : 'text-gray-900',
              index === 3 ? 'bg-gray-900 text-white' : ''  // 4e knop (index 3) is geselecteerd
            ]">
              {{ day.dayNumber }}
            </span>
          </button>
        </div>
        <div class="flex w-full flex-auto">
          <div class="w-14 flex-none bg-white ring-1 ring-gray-100"></div>
          <div class="grid flex-auto grid-cols-1 grid-rows-1">
            <!-- Horizontal lines -->
            <div class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
              style="grid-template-rows: repeat(48, minmax(3.5rem, 1fr))">
              <div class="row-end-1 h-7"></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">12AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">1AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">2AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">3AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">4AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">5AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">6AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">7AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">8AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">9AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">10AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">11AM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">12PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">1PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">2PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">3PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">4PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">5PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">6PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">7PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">8PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">9PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">10PM</div>
              </div>
              <div></div>
              <div>
                <div class="-ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">11PM</div>
              </div>
              <div></div>
            </div>

            <!-- Events -->
            <ul class="col-start-1 col-end-2 row-start-1 grid grid-cols-${event.lane} gap-1"
              style="grid-template-rows: 1.75rem repeat(288, minmax(0, 1fr)) auto">
              <li v-for="event in positionedEvents" :key="event.id" class="relative mt-px flex"
                :style="`grid-row: ${event.startRow} / span ${event.duration}; grid-column: ${event.lane + 1} / span 1`">
                <a href="#" class="group absolute inset-1 flex flex-col overflow-y-auto rounded-lg p-2 text-xs m-1"
                  :style="{ backgroundColor: event.bgColor }" :class="[event.hoverColor, event.textColor]">
                  <div v-if="event.isMultiDay" class="flex items-center mb-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-yellow-400 mr-1"></span>
                    <span class="text-xs opacity-75">
                      <span v-if="event.dayIndicator === 'started-earlier'">→ Loopt door</span>
                      <span v-else-if="event.dayIndicator === 'continues-tomorrow'">→ Verder morgen</span>
                      <span v-else-if="event.dayIndicator === 'ends-today'">Einde vandaag</span>
                      <span v-else>Meerdere dagen</span>
                    </span>
                  </div>
                  <p class="order-1 font-semibold">{{ event.title }}</p>
                  <p v-if="event.location" class="order-1">{{ event.location }}</p>
                  <p class="mt-1">
                    <time :datetime="event.start">{{ formatTime(event.start) }}</time> -
                    <time :datetime="event.end">{{ formatTime(event.end) }}</time>
                  </p>
                </a>
              </li>
            </ul>

            <!-- Overlay voor huidige tijd -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
              <div v-if="currentTimePosition" class="absolute left-0 right-0 h-0.5 bg-red-500 z-50"
                :style="`top: calc(1.75rem + (${currentTimePosition.row} - 2) * (100% / 288))`">
                <div class="absolute -left-2 -top-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow"></div>
                <div class="absolute left-4 -top-2 bg-red-500 text-white text-xs px-2 py-1 rounded-md shadow-lg">
                  Nu: {{ currentTimePosition.time }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <CalendarModal :isOpen="isOpen" :isEdit="isEdit" :event="selectedEvent" :categories="categories"
      :calendar-id="currentCalendarId" @closeModal="closeModal" @onEventCreated="handleEventCreated">
    </CalendarModal>
  </div>
</template>
<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { useCalendarStore } from "../../stores/calendar";
import { useEventStore } from "../../stores/event";
import CalendarModal from "./CalendarModal.vue";
/*import EventModal from "@/components/Blocks/EventModal.vue";
import EventList from "@/components/Blocks/EventList.vue";*/

// Refs
const isDropdownOpen = ref(false);
const isOpen = ref(false);
const isEdit = ref(false);
const selectedTask = ref(null);
const currentTime = ref(new Date());
let timeInterval = null;

// Store
const calendarstore = useCalendarStore();
const eventstore = useEventStore();

//OnMounted
onMounted(async () => {
  await eventstore.fetchEvents();

  currentTime.value = new Date();
  timeInterval = setInterval(() => {
    currentTime.value = new Date();
  }, 60000); // Update elke minuut
});

//OnUnmounted
onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

// Computed
const filteredEvents = computed(() => {
  console.log('Alle events:', eventstore.events);

  if (!eventstore.events || !Array.isArray(eventstore.events)) {
    console.log('Geen events gevonden of geen array');
    return [];
  }

  const selectedDate = new Date(calendarstore.huidigJaar, calendarstore.huidigeMaand, calendarstore.dagNummer);
  const nextDate = new Date(selectedDate);
  nextDate.setDate(nextDate.getDate() + 1);

  const filtered = eventstore.events.filter((event) => {
    const eventStart = new Date(event.start);
    const eventEnd = new Date(event.end);

    // Controleer of de datum geldig is
    if (isNaN(eventStart.getTime()) || isNaN(eventEnd.getTime())) {
      console.warn('Ongeldige datum voor event:', event);
      return false;
    }

    // Event overlapt met geselecteerde dag als:
    // - Event start op geselecteerde dag OF
    // - Event eindigt op geselecteerde dag OF  
    // - Event loopt over geselecteerde dag heen (start ervoor, eindigt erna)
    const overlaps = (
      (eventStart >= selectedDate && eventStart < nextDate) || // Start op deze dag
      (eventEnd > selectedDate && eventEnd <= nextDate) ||    // Eindigt op deze dag
      (eventStart < selectedDate && eventEnd > nextDate)      // Loopt over deze dag heen
    );

    return overlaps;
  });

  console.log('Gefilterde events voor dag:', filtered);
  return filtered;
});

// Methods
const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const navigateTo = (view) => {
  console.log(`Navigating to ${view}`);
  isDropdownOpen.value = false; // Sluit de dropdown na selectie
};

// Functie om een datum om te zetten naar "22 oktober 2025"
const formatEventDate = (datetime) => {
  const date = new Date(datetime);
  const options = { day: "numeric", month: "long", year: "numeric" };
  return date.toLocaleDateString("nl-NL", options);
};

function openModal(task, edit) {
  console.log("is edit");
  isOpen.value = true;
  isEdit.value = edit;
  selectedTask.value = task;
}

function closeModal() {
  console.log("close modal");
  isOpen.value = false;
  isEdit.value = false;
  selectedTask.value = null;
}

// Helper functie om tijd te formatteren
const formatTime = (datetime) => {
  const date = new Date(datetime);
  if (isNaN(date.getTime())) {
    return 'Ongeldige tijd';
  }
  return date.toLocaleTimeString('nl-NL', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Helper functie voor row calculation (als je die nog niet hebt)
const calculateEventPosition = (event) => {
  // Gebruik displayStart en displayEnd voor multi-day events
  const startDate = new Date(event.displayStart || event.start);
  const endDate = new Date(event.displayEnd || event.end);

  if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
    console.warn('Ongeldige datum voor event:', event);
    return { startRow: 1, duration: 1 };
  }

  const startHours = startDate.getHours();
  const startMinutes = startDate.getMinutes();
  const endHours = endDate.getHours();
  const endMinutes = endDate.getMinutes();

  // Elke rij = 5 minuten
  const startRow = 2 + (startHours * 12) + Math.floor(startMinutes / 5);
  const endRow = 2 + (endHours * 12) + Math.floor(endMinutes / 5);
  const duration = Math.max(1, endRow - startRow);

  console.log(`Event "${event.title}": ${startHours}:${startMinutes} - ${endHours}:${endMinutes} => row ${startRow} duration ${duration} | Multi-day: ${event.isMultiDay}`);

  return {
    startRow,
    duration
  };
};


const calculateEventLayout = (events) => {
  if (!events.length) return [];

  // Sorteer events op starttijd
  const sortedEvents = [...events].sort((a, b) => new Date(a.start) - new Date(b.start));

  const lanes = [];
  const result = [];

  sortedEvents.forEach(event => {
    const position = calculateEventPosition(event);
    const eventStart = new Date(event.start);
    const eventEnd = new Date(event.end);

    // Zoek een lane waar het event past
    let laneIndex = 0;
    let foundLane = false;

    while (laneIndex < lanes.length) {
      const lane = lanes[laneIndex];
      const overlaps = lane.some(existingEvent => {
        const existingStart = new Date(existingEvent.start);
        const existingEnd = new Date(existingEvent.end);
        return eventStart < existingEnd && eventEnd > existingStart;
      });

      if (!overlaps) {
        foundLane = true;
        break;
      }
      laneIndex++;
    }

    // Maak nieuwe lane aan indien nodig
    if (!foundLane) {
      lanes[laneIndex] = [];
    }

    // Voeg event toe aan de lane
    lanes[laneIndex].push(event);

    // Voeg lane informatie toe aan event
    const eventWithLayout = {
      ...event,
      lane: laneIndex,
      totalLanes: lanes.length, // ← CORRECTIE: geen +1!
      startRow: position.startRow,
      duration: position.duration
    };

    result.push(eventWithLayout);
  });

  console.log('Lanes layout:', lanes.map((lane, index) => ({
    lane: index,
    events: lane.map(e => e.title)
  })));

  return result;
};

const positionedEvents = computed(() => {
  const preparedEvents = prepareEventsForDay(filteredEvents.value);
  const eventsWithLayout = calculateEventLayout(preparedEvents);

  console.log('Multi-day events:', preparedEvents.filter(e => e.isMultiDay));

  return eventsWithLayout.map(event => {
    return {
      ...event,
      // Speciale styling voor multi-day events
      bgColor: event.isMultiDay ? (event.bgColor || '#8b5cf6') : (event.bgColor || '#3b82f6'),
      textColor: event.textColor || 'text-white',
      hoverColor: event.hoverColor || 'hover:bg-blue-600',
      // Voeg indicator toe voor multi-day
      multiDayClass: event.isMultiDay ? 'border-l-4 border-yellow-400' : ''
    };
  });
});

// Functie om events aan te passen voor multi-day weergave
const prepareEventsForDay = (events) => {
  const selectedDate = new Date(calendarstore.huidigJaar, calendarstore.huidigeMaand, calendarstore.dagNummer);
  const nextDate = new Date(selectedDate);
  nextDate.setDate(nextDate.getDate() + 1);

  return events.map(event => {
    const eventStart = new Date(event.start);
    const eventEnd = new Date(event.end);

    let displayStart = eventStart;
    let displayEnd = eventEnd;
    let isMultiDay = false;
    let dayIndicator = '';

    // Als event start voor deze dag, toon vanaf middernacht
    if (eventStart < selectedDate) {
      displayStart = new Date(selectedDate);
      isMultiDay = true;
      dayIndicator = 'started-earlier';
    }

    // Als event eindigt na deze dag, toon tot middernacht
    if (eventEnd > nextDate) {
      displayEnd.setHours(23, 59, 0, 0);
      isMultiDay = true;
      dayIndicator = dayIndicator ? 'multi-day' : 'continues-tomorrow';
    }

    // Als event alleen op deze dag eindigt (maar niet start)
    if (eventStart < selectedDate && eventEnd <= nextDate && eventEnd > selectedDate) {
      dayIndicator = 'ends-today';
    }

    return {
      ...event,
      // Voor positionering gebruiken we de display times
      displayStart: displayStart.toISOString(),
      displayEnd: displayEnd.toISOString(),
      isMultiDay,
      dayIndicator,
      // Behoud originele times voor info
      originalStart: event.start,
      originalEnd: event.end
    };
  });
};

// Computed property voor de huidige tijd indicator
const currentTimePosition = computed(() => {
  const now = currentTime.value;
  const today = new Date();

  // Alleen tonen als het vandaag is
  const isToday = (
    now.getDate() === Number(calendarstore.dagNummer) &&
    now.getMonth() === calendarstore.huidigeMaand &&
    now.getFullYear() === calendarstore.huidigJaar
  );

  if (!isToday) {
    return null;
  }

  const hours = now.getHours();
  const minutes = now.getMinutes();

  // Bereken de grid row (zelfde logica als events)
  const startRow = 2 + (hours * 12) + Math.floor(minutes / 5);

  console.log(`Huidige tijd: ${hours}:${minutes} => row ${startRow}`);

  return {
    row: startRow,
    time: now.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })
  };
});

/* methods: {
   navigateTo(view) {
     switch (view) {
       case 'day-view':
         this.$router.push({name: 'DayView'});
         break;
       case 'week-view':
         this.$router.push({name: 'WeekView'});
         break;
       case 'month-view':
         this.$router.push({name: 'MonthView'});
         break;
       case 'year-view':
         this.$router.push({name: 'YearView'});
         break;
       default:
         break;
     }
   },*/
</script>