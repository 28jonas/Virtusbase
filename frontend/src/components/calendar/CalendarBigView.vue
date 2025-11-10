<template>
  <div class="container mx-auto bg-gray-800 text-white rounded-lg shadow-md flex flex-col md:flex-row">
    <div class="left w-full md:w-3/5 p-5">
      <div class="calendar bg-white text-gray-800 rounded-lg p-5">
        <div class="month flex justify-between items-center">
          <button @click="store.changeMonth(-1)" class="text-xl hover:text-purple-600">vorige</button>
          <div class="date text-xl font-semibold">{{store.maandNaam }} {{store.huidigJaar }}</div>
          <button @click="store.changeMonth(1)" class="text-xl hover:text-purple-600">volgende</button>
        </div>
        <div class="weekdays grid grid-cols-7 text-center font-medium mt-4">
          <div v-for="day in store.dagen" :key="day">{{ day }}</div>
        </div>
        <div class="days grid grid-cols-7 gap-2 mt-4">
          <div
              v-for="(day, index) in store.genummerdeDagen"
              :key="`${day.date}-${index}-${store.huidigeMaand}-${store.huidigJaar}`"
              :class="{'bg-purple-600 text-white rounded-lg': day.isToday,
                        'text-gray-500': day.isPrevMonth || day.isNextMonth,
                        'bg-blue-600 text-white rounded-full': store.geselecteerdeDag && store.geselecteerdeDag.date === day.date,
                        }"
              class="p-2 text-center cursor-pointer hover:bg-purple-500 hover:text-white"
              @click="store.selecteerDag(day);">
            {{ day.date }}
          </div>
        </div>
      </div>
    </div>
    <div class="right w-full md:w-2/5 p-5">
      <div class="today-date text-center">
        <div class="event-day text-xl font-medium">
          {{ store.geselecteerdeDag ? store.geselecteerdeDag.date : "Select a day" }}
          {{store.maandNaam}}
          <button @click="store.openModal" class="bg-purple-600 text-white py-2 px-4 rounded-lg">
            Add Event
          </button>
          <EventModal v-if="store.openModal"/>
        </div>
        <div>
          <EventList :filter-by-selected-day="true" />
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { defineComponent } from "vue";
import { useCalendarStore } from "../stores/calendar";
import EventModal from "../components/calendar/EventModal.vue";
import EventList from "../components/calendar/EventList.vue";

export default defineComponent({
  components:{
    EventModal,
    EventList,
  },
  setup() {
    const store = useCalendarStore();
    return {
      store,
    };
  },
});
</script>