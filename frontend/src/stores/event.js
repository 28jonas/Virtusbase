import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";

export const useEventStore = defineStore("event", () => {
    const events = ref([]);
    const loading = ref(false);
    const error = ref(null);

    const fetchEvents = async (params = {}) => {
        try {
            loading.value = true;
            const response = await axios.get('http://localhost:8080/api/events', {
                params,
                withCredentials: true
            });
            console.log("fetchevents in event.js:", response.data.data);
            events.value = response.data.data || []; // Zorg voor fallback naar lege array
            return response.data.data;
        } catch (err) {
            console.error('Error fetching events:', err);
            error.value = err;
            events.value = []; // Reset naar lege array bij error
            throw err;
        } finally {
            loading.value = false;
        }
    }

    const createEvent = async (eventData) => {
        try {
            //const response = await api.post('/api/events', eventData)
            //console.log('eventData:', eventData)
            const response = await axios.post(`http://localhost:8080/api/calendars/${eventData.calendar_id}/events`, eventData, {
                withCredentials: true
            })
            events.value.push(response.data.data)
            return response.data
        } catch (error) {
            console.error('Error creating event:', error)
            throw error.response.data
        }
    }

    return {
        fetchEvents,
        createEvent,
        events,
        loading,
        error
    };
});