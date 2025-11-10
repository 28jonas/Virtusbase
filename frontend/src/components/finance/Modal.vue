<template>
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
              leave-active-class="transition-opacity duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <div v-if="show" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
      <Transition enter-active-class="transition-transform duration-300" enter-from-class="scale-95 opacity-0" enter-to-class="scale-100 opacity-100"
                  leave-active-class="transition-transform duration-300" leave-from-class="scale-100 opacity-100" leave-to-class="scale-95 opacity-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md transform transition-all">
          <div class="p-6">
            <div class="flex items-center justify-between mb-5">
              <slot name="header"></slot>
              <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <slot></slot>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    required: true
  }
})

defineEmits(['close'])
</script>