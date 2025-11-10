<!-- components/calendar/EventForm.vue -->
<template>
  <form class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Titel</label>
      <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
    </div>
    
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Datum</label>
        <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tijd</label>
        <input type="time" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
      </div>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
      <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        <option value="work">Werk</option>
        <option value="personal">Persoonlijk</option>
        <option value="health">Gezondheid</option>
        <option value="family">Familie</option>
      </select>
    </div>
    
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Beschrijving</label>
      <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
    </div>
    
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors">
      Event Toevoegen
    </button>
  </form>
</template>