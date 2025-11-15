import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'
import axios from 'axios'
import { API_BASE } from '../utils/config'

export const useCalendarStore = defineStore('calendar', () => {
  //FUNCTIES VOOR DATBASE
  // State
  const calendars = ref([])
  const events = ref([])
  const currentCalendar = ref(null)
  const weekStartsOnMonday = ref(true)

  // Actions
  const fetchCalendars = async () => {
    try {
      const response = await axios.get(`${API_BASE}/api/calendars`, {
        withCredentials: true // Zorg dat cookies worden meegestuurd
      })
      console.log(response.data.data)  
      calendars.value = response.data.data
      console.log(calendars.value)
    } catch (error) {
      console.error('Error fetching calendars:', error)
      throw error
    }
  }

  const createCalendar = async (calendarData) => {
    try {
      const response = await api.post(`${API_BASE}/api/calendars`, calendarData ,{
        withCredentials: true
      })
      calendars.value.push(response.data.data)
      return response.data
    } catch (error) {
      throw error.response.data
    }
  }

  //FUNCTIES VOOR WERKING VUE COMPONENTS
  const isModalOpen = ref(false);
  const maanden = ref(["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"]);
  const dagen = ref(["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"]);
  const dag = ref(["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"]);
  
  const huidigeMaand = ref(new Date().getMonth());
  const huidigeDag = ref(new Date().getDate());
  const huidigJaar = ref(new Date().getFullYear());
  const huidigeDatum = ref(new Date());
  const geselecteerdeDag = ref(null);
  const geselecteerdeMaand = ref(null);
  const evenementen = ref([]);

  // Getters (computed)
  const maandNaam = computed(() => maanden.value[huidigeMaand.value]);
  
  const dagNaam = computed(() => {
    return dagen.value[new Date(huidigJaar.value, huidigeMaand.value, huidigeDag.value).getDay()];
  });
  
  const dagNummer = computed(() => huidigeDag.value);
  
  const dagenInMaand = computed(() => {
    return new Date(huidigJaar.value, huidigeMaand.value + 1, 0).getDate();
  });
  
  const startDagVanDeWeek = computed(() => {
    return new Date(huidigJaar.value, huidigeMaand.value, 1).getDay();
  });
  
  const genummerdeDagen = computed(() => {
    const totaalDagen = dagenInMaand.value;
    const startDag = startDagVanDeWeek.value;
    const vorigeMaandDagen = new Date(huidigJaar.value, huidigeMaand.value, 0).getDate();

    const days = [];

    // Voeg dagen van de vorige maand toe
    for (let i = startDag - 1; i >= 0; i--) {
      days.push({
        date: vorigeMaandDagen - i,
        isPrevMonth: true,
        isToday: false,
      });
    }

    // Voeg de huidige maand toe
    for (let i = 1; i <= totaalDagen; i++) {
      const isToday =
        i === huidigeDatum.value.getDate() &&
        huidigeMaand.value === huidigeDatum.value.getMonth() &&
        huidigJaar.value === huidigeDatum.value.getFullYear();
      days.push({
        date: i,
        isPrevMonth: false,
        isNextMonth: false,
        isToday,
      });
    }

    // Vul aan met dagen van de volgende maand
    const nextMonthDays = 42 - days.length;
    for (let i = 1; i <= nextMonthDays; i++) {
      days.push({
        date: i,
        isPrevMonth: false,
        isNextMonth: true,
        isToday: false,
      });
    }

    return days;
  });

  // Actions
  function changeMonth(offset) {
    huidigeMaand.value += offset;
    if (huidigeMaand.value > 11) {
      huidigeMaand.value = 0;
      huidigJaar.value += 1;
    } else if (huidigeMaand.value < 0) {
      huidigeMaand.value = 11;
      huidigJaar.value -= 1;
    }
  }

  function changeDay(offset) {
    const newDate = new Date(huidigJaar.value, huidigeMaand.value, huidigeDag.value + offset);
    updateDate(newDate);
  }

  function changeWeek(offset) {
    const newDate = new Date(huidigJaar.value, huidigeMaand.value, huidigeDag.value + (offset * 7));
    updateDate(newDate);
  }

  function updateDate(newDate) {
    huidigJaar.value = newDate.getFullYear();
    huidigeMaand.value = newDate.getMonth();
    huidigeDag.value = newDate.getDate();
    huidigeDatum.value = newDate;
  }

  function today() {
    updateDate(new Date());
  }

  function selecteerDag(day) {
    geselecteerdeDag.value = day;
  }

  function getWeekDays() {
    const days = [];
    const currentDate = new Date(huidigJaar.value, huidigeMaand.value, huidigeDag.value);

    for (let i = -3; i <= 3; i++) {
      const date = new Date(currentDate);
      date.setDate(date.getDate() + i);

      days.push({
        dayNumber: date.getDate(),
        month: date.getMonth(),
        year: date.getFullYear(),
        isCurrentMonth: date.getMonth() === huidigeMaand.value,
        isToday: date.toDateString() === new Date().toDateString()
      });
    }

    return days;
  }

  function makeEvent(title, description, location, startDateTime, endDateTime, allDay, categoryId, reminder, calendarId, id = null, startRow, duration, bgColor, textColor, hoverColor, datetime, time) {
    id = id || uuidv7();
    console.log("makeEvent", id);
    return {
      id,
      title,
      description,
      location,
      startDateTime,
      endDateTime,
      allDay,
      categoryId,
      reminder,
      startRow: calculateEventPosition(startDateTime, endDateTime, allDay).startRow,
      duration: calculateEventPosition(startDateTime, endDateTime, allDay).durationRows,
      bgColor,
      textColor,
      hoverColor,
      datetime,
      time,
      calendarId: calendarId || 'default',
      createdAt: new Date().toISOString()
    };
  }

  function addEvent({ calendarId, eventId, title, description, location, startDateTime, endDateTime, allDay, categoryId, reminder, startRow, duration, bgColor, textColor, hoverColor, datetime, time }) {
    const newEvent = makeEvent(
      title,
      description,
      location,
      startDateTime,
      endDateTime,
      allDay,
      categoryId,
      reminder,
      calendarId || 'default',
      eventId,
      startRow,
      duration,
      bgColor || '#0ea5e9',
      textColor || '#ffffff',
      hoverColor || 'hover:bg-blue-200',
      datetime || startDateTime,
      time || startDateTime.split("T")[1]
    );
    console.log("newEvent", newEvent);

    evenementen.value.push(newEvent);
    saveEvents();
  }

  function saveEvents() {
    try {
      localStorage.setItem('calendarEvents', JSON.stringify(evenementen.value));
    } catch (error) {
      console.error('Fout bij het opslaan van events naar localStorage:', error);
    }
  }

  function closeModal() {
    isModalOpen.value = false;
  }

  function openModal() {
    isModalOpen.value = true;
  }

  function calculateEventPosition(startDateTime, endDateTime, allDay) {
    if (allDay) {
      return { startRow: 1, durationRows: 10, allDay: true };
    }
    const [startHour, startMinute] = startDateTime.split("T")[1].split(":").map(Number);
    const [endHour, endMinute] = endDateTime.split("T")[1].split(":").map(Number);

    const startRow = (startHour * 12) + Math.floor(startMinute / 5) + 2;
    const endRow = (endHour * 12) + Math.floor(endMinute / 5) + 3;

    const durationRows = endRow - startRow;
    console.log(startRow, durationRows);
    return { startRow, durationRows, allDay };
  }

  // Return state and actions
  return {
    //return van de functies voor de database
    calendars,
    currentCalendar,
    fetchCalendars,
    createCalendar,
    //return van de functies voor de vue components
    //variables
    maandNaam,
    dagNummer,
    dagNaam,
    dagenInMaand,
    huidigJaar,
    huidigeMaand,
    huidigeDag,
    huidigeDatum,
    geselecteerdeDag,
    geselecteerdeMaand,
    evenementen,
    isModalOpen,
    maanden,
    dagen,
    startDagVanDeWeek,
    genummerdeDagen,
    //functies
    changeMonth,
    changeWeek,
    changeDay,
    today,
    selecteerDag,
    addEvent,
    closeModal,
    openModal,
    makeEvent,
    getWeekDays,
  }
})