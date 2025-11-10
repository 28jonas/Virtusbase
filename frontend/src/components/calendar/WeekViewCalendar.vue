<template>
  <div class="flex h-full flex-col">
         <!-- Week start toggle -->
    <div class="flex items-center justify-between p-4 border-b">
      <div class="flex items-center space-x-4">
        <button @click="previousWeek" class="p-2 rounded-lg border hover:bg-gray-50">
          ← Vorige week
        </button>
        <button @click="nextWeek" class="p-2 rounded-lg border hover:bg-gray-50">
          Volgende week →
        </button>
      </div>
      
      <!-- Week start toggle -->
      <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-600">Week start op:</span>
        <button 
          @click="toggleWeekStart"
          class="px-3 py-1 text-sm rounded-lg border hover:bg-gray-50 transition-colors"
          :class="store.weekStartsOnMonday ? 'bg-blue-100 border-blue-300' : 'bg-gray-100 border-gray-300'"
        >
          {{ store.weekStartsOnMonday ? 'Maandag' : 'Zondag' }}
        </button>
      </div>
    </div>
    <<div class="isolate flex flex-auto flex-col overflow-auto bg-white">
      <div style="width: 165%" class="flex max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
        <div class="sticky top-0 z-30 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8">
          <!-- Mobile header -->
          <div class="grid grid-cols-7 text-sm/6 text-gray-500 sm:hidden">
            <button
              v-for="(day, index) in weekDays"
              :key="index"
              type="button"
              class="flex flex-col items-center pb-3 pt-2"
            >
              <span>{{ dayNamesShort[index] }}</span>
              <span
                :class="['mt-1 flex size-8 items-center justify-center font-semibold', day.isToday ? 'rounded-full bg-indigo-600 text-white' : 'text-gray-900']"
              >
                {{ day.dayNumber }}
              </span>
            </button>
          </div>

          <!-- Desktop header -->
          <div class="-mr-px hidden grid-cols-7 divide-x divide-gray-100 border-r border-gray-100 text-sm/6 text-gray-500 sm:grid">
            <div class="col-end-1 w-14"></div>
            <div
              v-for="(day, index) in weekDays"
              :key="index"
              class="flex items-center justify-center py-3"
            >
              <span :class="{'flex items-baseline': day.isToday}">
                {{ dayNames[index] }}
                <span
                  :class="[
                    day.isToday ? 'ml-1.5 flex size-8 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white' :
                    'items-center justify-center font-semibold text-gray-900'
                  ]"
                >
                  {{ day.dayNumber }}
                </span>
              </span>
            </div>
          </div>
        </div>

        <div class="flex flex-auto">
          <div class="sticky left-0 z-10 w-14 flex-none bg-white ring-1 ring-gray-100"></div>
          <div class="grid flex-auto grid-cols-1 grid-rows-1">
            <!-- Horizontal lines -->
            <div class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                 style="grid-template-rows: repeat(48, minmax(3.5rem, 1fr))">
              <div class="row-end-1 h-7"></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">12AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">1AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">2AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">3AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">4AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">5AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">6AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">7AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">8AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">9AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">10AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">11AM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">12PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">1PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">2PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">3PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">4PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">5PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">6PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">7PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">8PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">9PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">10PM</div>
              </div>
              <div></div>
              <div>
                <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">11PM</div>
              </div>
              <div></div>
            </div>

            <!-- Vertical lines -->
            <div class="col-start-1 col-end-2 row-start-1 hidden grid-cols-7 grid-rows-1 divide-x divide-gray-100 sm:grid sm:grid-cols-7">
              <div class="col-start-1 row-span-full"></div>
              <div class="col-start-2 row-span-full"></div>
              <div class="col-start-3 row-span-full"></div>
              <div class="col-start-4 row-span-full"></div>
              <div class="col-start-5 row-span-full"></div>
              <div class="col-start-6 row-span-full"></div>
              <div class="col-start-7 row-span-full"></div>
              <div class="col-start-8 row-span-full w-8"></div>
            </div>

            <!-- Events -->
            <ol class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 sm:pr-8"
                style="grid-template-rows: 1.75rem repeat(288, minmax(0, 1fr)) auto">
              
              <!-- Events per dag -->
              <li
                v-for="event in positionedEvents"
                :key="`${event.id}-${event.dayColumn}-${event.lane}`"
                class="relative mt-px flex"
                :style="`grid-row: ${event.startRow} / span ${event.duration}; grid-column: ${event.dayColumn} / span 1`"
              >
                <a href="#"
                   class="group absolute flex flex-col overflow-y-auto rounded-lg p-2 text-xs border-2 border-black"
                   :style="{
                     backgroundColor: event.bgColor,
                     left: `${event.laneOffset}%`,
                     width: `${event.laneWidth}%`,
                     height: 'calc(100% - 2px)',
                     top: '1px'
                   }"
                   :class="[event.hoverColor, event.textColor]">
                  
                  <!-- Multi-day indicator -->
                  <div v-if="event.isMultiDay" class="flex items-center mb-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-yellow-400 mr-1"></span>
                    <span class="text-xs opacity-75">
                      <span v-if="event.dayIndicator === 'started-earlier'">→ Loopt door</span>
                      <span v-else-if="event.dayIndicator === 'continues-tomorrow'">→ Verder morgen</span>
                      <span v-else-if="event.dayIndicator === 'ends-today'">Einde vandaag</span>
                      <span v-else>Meerdere dagen</span>
                    </span>
                  </div>
                  
                  <p class="order-1 font-semibold truncate">{{ event.title }}</p>
                  <p v-if="event.location" class="order-1 text-xs/3 truncate">{{ event.location }}</p>
                  <p class="mt-1 text-xs/3">
                    <template v-if="!event.isMultiDay || event.dayIndicator === 'ends-today'">
                      <time :datetime="event.start">{{ formatTime(event.start) }}</time> -
                      <time :datetime="event.end">{{ formatTime(event.end) }}</time>
                    </template>
                    <template v-else-if="event.dayIndicator === 'started-earlier'">
                      Tot <time :datetime="event.end">{{ formatTime(event.end) }}</time>
                    </template>
                    <template v-else-if="event.dayIndicator === 'continues-tomorrow'">
                      Vanaf <time :datetime="event.start">{{ formatTime(event.start) }}</time>
                    </template>
                  </p>
                </a>
              </li>
            </ol>

            <!-- Overlay voor huidige tijd (optioneel) -->
            <!-- <div class="absolute inset-0 pointer-events-none overflow-hidden">
              <div v-if="currentTimePosition" 
                   class="absolute left-0 right-0 h-0.5 bg-red-500 z-50"
                   :style="`top: calc(1.75rem + (${currentTimePosition.row} - 2) * (100% / 288)); left: calc(${currentTimePosition.dayColumn} * (100% / 7))`"
                   style="width: calc(100% / 7)">
                <div class="absolute -left-2 -top-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow"></div>
                <div class="absolute left-4 -top-2 bg-red-500 text-white text-xs px-2 py-1 rounded-md shadow-lg">
                  Nu: {{ currentTimePosition.time }}
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { useCalendarStore } from "../../stores/calendar";
import { useEventStore } from "../../stores/event";

// Stores
const store = useCalendarStore();
const eventstore = useEventStore();

// Refs
const currentTime = ref(new Date());
let timeInterval = null;

// OnMounted
onMounted(async () => {
  await eventstore.fetchEvents();
  
  currentTime.value = new Date();
  timeInterval = setInterval(() => {
    currentTime.value = new Date();
  }, 60000);
});

// OnUnmounted
onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

// Helper functies
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

const dayNames = computed(() => {
  if (store.weekStartsOnMonday) {
    return ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
  } else {
    return ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
  }
});

const dayNamesShort = computed(() => {
  if (store.weekStartsOnMonday) {
    return ['M', 'D', 'W', 'D', 'V', 'Z', 'Z'];
  } else {
    return ['Z', 'M', 'D', 'W', 'D', 'V', 'Z'];
  }
});

// Bepaal de dag kolom voor een event
const getDayColumn = (eventDate) => {
  const weekDays = store.getWeekDays();
  const date = new Date(eventDate);
  
  const dayIndex = weekDays.findIndex(day => {
    const dayDate = new Date(day.year, day.month, day.dayNumber);
    return dayDate.getDate() === date.getDate() && 
           dayDate.getMonth() === date.getMonth() && 
           dayDate.getFullYear() === date.getFullYear();
  });
  
  return dayIndex >= 0 ? dayIndex + 1 : 1;
};

// Row calculation
const calculateEventPosition = (event) => {
  const startDate = new Date(event.displayStart || event.start);
  const endDate = new Date(event.displayEnd || event.end);

  if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
    return { startRow: 1, duration: 1 };
  }

  const startHours = startDate.getHours();
  const startMinutes = startDate.getMinutes();
  const endHours = endDate.getHours();
  const endMinutes = endDate.getMinutes();

  const startRow = 2 + (startHours * 12) + Math.floor(startMinutes / 5);
  const endRow = 2 + (endHours * 12) + Math.floor(endMinutes / 5);
  const duration = Math.max(1, endRow - startRow);

  return { startRow, duration };
};

// Prepare events for multi-day display
const prepareEventsForWeek = (events) => {
  const weekDays = store.getWeekDays();
  const result = [];

  events.forEach(event => {
    const eventStart = new Date(event.start);
    const eventEnd = new Date(event.end);
    
    weekDays.forEach((day, dayIndex) => {
      const dayDate = new Date(day.year, day.month, day.dayNumber);
      const nextDayDate = new Date(dayDate);
      nextDayDate.setDate(nextDayDate.getDate() + 1);

      const overlaps = (
        (eventStart >= dayDate && eventStart < nextDayDate) ||
        (eventEnd > dayDate && eventEnd <= nextDayDate) ||
        (eventStart < dayDate && eventEnd > nextDayDate)
      );

      if (overlaps) {
        let displayStart = eventStart;
        let displayEnd = eventEnd;
        let isMultiDay = false;
        let dayIndicator = '';

        if (eventStart < dayDate) {
          displayStart = new Date(dayDate);
          isMultiDay = true;
          dayIndicator = 'started-earlier';
        }

        if (eventEnd > nextDayDate) {
          displayEnd = new Date(nextDayDate);
          displayEnd.setHours(23, 59, 0, 0);
          isMultiDay = true;
          dayIndicator = dayIndicator ? 'multi-day' : 'continues-tomorrow';
        }

        if (eventStart < dayDate && eventEnd <= nextDayDate && eventEnd > dayDate) {
          dayIndicator = 'ends-today';
        }

        if (displayStart < displayEnd) {
          result.push({
            ...event,
            displayStart: displayStart.toISOString(),
            displayEnd: displayEnd.toISOString(),
            isMultiDay,
            dayIndicator,
            dayColumn: dayIndex + 1,
            originalStart: event.start,
            originalEnd: event.end
          });
        }
      }
    });
  });

  return result;
};

// Verbeterde lane calculation met maximale breedte
const calculateEventLayout = (events) => {
  if (!events.length) return [];

  // Groepeer events per dag
  const eventsByDay = {};
  events.forEach(event => {
    if (!eventsByDay[event.dayColumn]) {
      eventsByDay[event.dayColumn] = [];
    }
    eventsByDay[event.dayColumn].push(event);
  });

  const result = [];

  // Bereken lanes voor elke dag apart
  Object.keys(eventsByDay).forEach(dayColumn => {
    const dayEvents = eventsByDay[dayColumn];
    const sortedEvents = [...dayEvents].sort((a, b) => new Date(a.displayStart || a.start) - new Date(b.displayStart || b.start));
    
    const lanes = [];
    const MAX_LANES = 3; // Maximale aantal lanes om te voorkomen dat events te klein worden

    sortedEvents.forEach(event => {
      const position = calculateEventPosition(event);
      const eventStart = new Date(event.displayStart || event.start);
      const eventEnd = new Date(event.displayEnd || event.end);

      // Zoek een lane waar het event past
      let laneIndex = 0;
      let foundLane = false;

      while (laneIndex < Math.min(lanes.length, MAX_LANES)) {
        const lane = lanes[laneIndex];
        const overlaps = lane.some(existingEvent => {
          const existingStart = new Date(existingEvent.displayStart || existingEvent.start);
          const existingEnd = new Date(existingEvent.displayEnd || existingEvent.end);
          return eventStart < existingEnd && eventEnd > existingStart;
        });

        if (!overlaps) {
          foundLane = true;
          break;
        }
        laneIndex++;
      }

      // Als geen lane gevonden en we hebben nog ruimte, maak nieuwe lane
      if (!foundLane && lanes.length < MAX_LANES) {
        lanes[laneIndex] = [];
        foundLane = true;
      }

      // Als nog steeds geen lane, gebruik de eerste lane (overlap forceren)
      if (!foundLane) {
        laneIndex = 0;
      }

      if (!lanes[laneIndex]) {
        lanes[laneIndex] = [];
      }

      lanes[laneIndex].push(event);

      // Bereken positie en breedte
      const totalLanes = Math.min(lanes.length, MAX_LANES);
      const laneWidth = 100 / totalLanes;
      const laneOffset = laneIndex * laneWidth;

      result.push({
        ...event,
        lane: laneIndex,
        totalLanes: totalLanes,
        startRow: position.startRow,
        duration: position.duration,
        laneOffset,
        laneWidth,
        // Voeg een border toe voor overlappende events voor betere zichtbaarheid
        hasOverlap: lanes[laneIndex].length > 1 && !foundLane
      });
    });
  });

  console.log('Events layout:', result.map(e => ({
    title: e.title,
    day: e.dayColumn,
    lane: e.lane,
    overlap: e.hasOverlap
  })));

  return result;
};

// Computed properties
const filteredEvents = computed(() => {
  if (!eventstore.events || !Array.isArray(eventstore.events)) {
    return [];
  }

  const weekDays = store.getWeekDays();
  if (!weekDays.length) return [];

  const weekStart = new Date(weekDays[0].year, weekDays[0].month, weekDays[0].dayNumber);
  const weekEnd = new Date(weekDays[6].year, weekDays[6].month, weekDays[6].dayNumber + 1);

  return eventstore.events.filter((event) => {
    const eventStart = new Date(event.start);
    const eventEnd = new Date(event.end);

    if (isNaN(eventStart.getTime()) || isNaN(eventEnd.getTime())) {
      return false;
    }

    return eventStart < weekEnd && eventEnd > weekStart;
  });
});

const positionedEvents = computed(() => {
  const preparedEvents = prepareEventsForWeek(filteredEvents.value);
  const eventsWithLayout = calculateEventLayout(preparedEvents);

  return eventsWithLayout.map(event => {
    // Speciale styling voor overlappende events
    const baseColor = event.isMultiDay ? (event.bgColor || '#8b5cf6') : (event.bgColor || '#3b82f6');
    
    return {
      ...event,
      bgColor: event.hasOverlap ? adjustColorBrightness(baseColor, -20) : baseColor, // Donkerder voor overlaps
      textColor: event.textColor || 'text-white',
      hoverColor: event.hoverColor || 'hover:bg-blue-600',
      multiDayClass: event.isMultiDay ? 'border-l-4 border-yellow-400' : '',
      // Voeg een border toe voor overlappende events
      overlapBorder: event.hasOverlap ? 'border-2 border-white border-opacity-50' : ''
    };
  });
});

// Helper functie om kleuren aan te passen voor betere zichtbaarheid
const adjustColorBrightness = (color, percent) => {
  // Vereenvoudigde kleuraanpassing
  if (color.startsWith('#')) {
    // Hex kleur aanpassen
    const num = parseInt(color.slice(1), 16);
    const amt = Math.round(2.55 * percent);
    const R = Math.max(0, Math.min(255, (num >> 16) + amt));
    const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
    const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
    return `#${(1 << 24 | R << 16 | G << 8 | B).toString(16).slice(1)}`;
  }
  return color;
};

// Huidige tijd indicator
const currentTimePosition = computed(() => {
  const now = currentTime.value;
  const weekDays = store.getWeekDays();
  
  const todayIndex = weekDays.findIndex(day => 
    now.getDate() === day.dayNumber &&
    now.getMonth() === day.month &&
    now.getFullYear() === day.year
  );

  if (todayIndex === -1) return null;

  const hours = now.getHours();
  const minutes = now.getMinutes();
  const startRow = 2 + (hours * 12) + Math.floor(minutes / 5);

  return {
    row: startRow,
    time: now.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' }),
    dayColumn: todayIndex + 1
  };
});
</script>