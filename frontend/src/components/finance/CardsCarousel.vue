<template>
  <div class="relative mb-6">
    <!-- Cards Container -->
    <div class="overflow-hidden">
      <div class="flex transition-transform duration-300 ease-in-out"
           :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
        <div v-for="(card, index) in cards" :key="card.id" class="w-full flex-shrink-0 px-1">
          <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 shadow-lg">
            <div class="flex justify-between items-start mb-6">
              <div>
                <p class="text-sm text-indigo-200 dark:text-gray-300 mb-1">Current Balance</p>
                <p class="text-2xl font-bold">
                  €{{ formatCurrency(card.balance) }}
                </p>
              </div>
              <div class="text-right">
                <p class="text-xs text-indigo-200 dark:text-gray-400 mb-2">Expires {{ card.expiry_date }}</p>
                <div v-if="card.bank.name === 'Mastercard' || card.bank.name === 'Visa'" class="h-6">
                  <img v-if="card.bank.name === 'Mastercard'" src="/images/mastercard-logo.svg" alt="Mastercard" class="h-6"/>
                  <img v-else-if="card.bank.name === 'Visa'" src="/images/visa-logo.svg" alt="Visa" class="h-6"/>
                </div>
                <div v-else class="text-sm font-bold text-white">{{ card.bank.name }}</div>
              </div>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-indigo-500 dark:border-gray-600">
              <p class="text-sm tracking-wider">
                •••• •••• •••• {{ card.last_four }}
              </p>
              <p class="text-xs text-indigo-200 dark:text-gray-400">{{ card.bank.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination Dots -->
    <div v-if="cards.length > 1" class="flex justify-center mt-4 space-x-2">
      <button v-for="index in cards.length" :key="index" 
              @click="activeSlide = index - 1"
              class="w-2 h-2 rounded-full transition-colors"
              :class="activeSlide === index - 1 ? 'bg-white' : 'bg-indigo-400 dark:bg-gray-600'">
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  cards: {
    type: Array,
    required: true
  }
})

const activeSlide = ref(0)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('nl-BE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>